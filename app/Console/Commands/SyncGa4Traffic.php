<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Models\TrafficReport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Sync traffic data from Google Analytics 4 Data API into traffic_reports.
 *
 * Setup instructions:
 *  1. Go to Google Analytics → Admin → Property → Service Accounts
 *  2. Create a Service Account in Google Cloud Console with "Viewer" role on the GA4 property
 *  3. Download the JSON key file
 *  4. In Admin → Settings → Integrations, paste the JSON key contents into "GA4 Service Account JSON"
 *  5. Also add your GA4 Property ID (e.g. "123456789") — found in GA4 → Admin → Property Settings
 *  6. Run: php artisan ga4:sync  OR let the daily scheduler run it automatically
 */
class SyncGa4Traffic extends Command
{
    protected $signature = 'ga4:sync {--days=7 : Number of past days to sync}';
    protected $description = 'Pull traffic data from Google Analytics 4 into traffic_reports table';

    public function handle(): int
    {
        $propertyId  = Setting::get('ga4_property_id', '');
        $serviceJson = Setting::get('ga4_service_account_json', '');

        if (empty($propertyId) || empty($serviceJson)) {
            $this->warn('GA4 not configured. Add ga4_property_id and ga4_service_account_json in Settings → Integrations.');
            return self::FAILURE;
        }

        $credentials = json_decode($serviceJson, true);
        if (!$credentials) {
            $this->error('ga4_service_account_json is not valid JSON.');
            return self::FAILURE;
        }

        $this->info("Fetching GA4 token...");
        $token = $this->getAccessToken($credentials);

        if (!$token) {
            $this->error('Failed to obtain Google access token. Check your service account credentials.');
            return self::FAILURE;
        }

        $days      = (int) $this->option('days');
        $startDate = now()->subDays($days)->format('Y-m-d');
        $endDate   = now()->subDay()->format('Y-m-d'); // GA4 data has ~24h delay

        $this->info("Fetching GA4 data: {$startDate} → {$endDate}");

        $body = [
            'dateRanges' => [
                ['startDate' => $startDate, 'endDate' => $endDate],
            ],
            'dimensions' => [
                ['name' => 'date'],
            ],
            'metrics' => [
                ['name' => 'screenPageViews'],
                ['name' => 'activeUsers'],
                ['name' => 'totalUsers'],
            ],
            'orderBys' => [
                ['dimension' => ['dimensionName' => 'date'], 'desc' => false],
            ],
        ];

        $response = Http::withToken($token)
            ->timeout(30)
            ->post("https://analyticsdata.googleapis.com/v1beta/properties/{$propertyId}:runReport", $body);

        if (!$response->successful()) {
            $this->error('GA4 API error: ' . $response->body());
            return self::FAILURE;
        }

        $rows = $response->json('rows', []);
        $synced = 0;

        foreach ($rows as $row) {
            $dateRaw      = $row['dimensionValues'][0]['value'] ?? null; // YYYYMMDD
            $pageViews    = (int) ($row['metricValues'][0]['value'] ?? 0);
            $activeUsers  = (int) ($row['metricValues'][1]['value'] ?? 0);
            $totalUsers   = (int) ($row['metricValues'][2]['value'] ?? 0);
            $uniqueVisitors = max($activeUsers, $totalUsers);

            if (!$dateRaw) continue;

            // GA4 returns dates as "20260517" — convert to "2026-05-17"
            $reportDate = \Carbon\Carbon::createFromFormat('Ymd', $dateRaw)->toDateString();

            DB::table('traffic_reports')->upsert(
                [
                    'report_date'      => $reportDate,
                    'page_views'       => $pageViews,
                    'unique_visitors'  => $uniqueVisitors,
                    'data_consumed_mb' => 0,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ],
                ['report_date'],
                [
                    // GA4 data is authoritative — overwrite local middleware counts
                    'page_views'      => $pageViews,
                    'unique_visitors' => $uniqueVisitors,
                    'updated_at'      => now(),
                ]
            );

            $synced++;
        }

        $this->info("Synced {$synced} days of GA4 data.");
        return self::SUCCESS;
    }

    /**
     * Exchange a service account JSON key for a short-lived OAuth2 access token
     * using a JWT assertion (no external library needed).
     */
    private function getAccessToken(array $credentials): ?string
    {
        try {
            $now = time();
            $header  = base64url_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
            $payload = base64url_encode(json_encode([
                'iss'   => $credentials['client_email'],
                'scope' => 'https://www.googleapis.com/auth/analytics.readonly',
                'aud'   => 'https://oauth2.googleapis.com/token',
                'exp'   => $now + 3600,
                'iat'   => $now,
            ]));

            $signingInput = "{$header}.{$payload}";
            $privateKey   = $credentials['private_key'];

            openssl_sign($signingInput, $signature, $privateKey, 'SHA256');
            $jwt = "{$signingInput}." . base64url_encode($signature);

            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt,
            ]);

            return $response->json('access_token');
        } catch (\Throwable $e) {
            Log::error('GA4 JWT signing failed: ' . $e->getMessage());
            return null;
        }
    }
}

if (!function_exists('base64url_encode')) {
    function base64url_encode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
