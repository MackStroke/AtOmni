<?php

namespace App\Http\Middleware;

use App\Models\TrafficReport;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class RecordPageView
{
    /**
     * Bots/crawlers to exclude from traffic counts.
     * Real human traffic only.
     */
    private const BOT_PATTERNS = [
        'bot', 'crawl', 'spider', 'slurp', 'mediapartners',
        'googlebot', 'bingbot', 'yandex', 'baidu', 'duckduck',
        'facebookexternalhit', 'twitterbot', 'linkedinbot',
        'whatsapp', 'slack', 'telegram', 'curl', 'wget',
        'python-requests', 'axios', 'go-http-client',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only record GET requests (not form submissions, API calls, etc.)
        if (!$request->isMethod('GET')) {
            return $response;
        }

        // Skip admin, login, api, storage, and asset routes
        if ($request->is('admin/*', 'login', 'logout', 'register', 'password/*',
                          'storage/*', '_debugbar/*', 'up')) {
            return $response;
        }

        // Skip non-HTML responses (JSON, XML, images, etc.)
        $contentType = $response->headers->get('Content-Type', '');
        if (!str_contains($contentType, 'text/html')) {
            return $response;
        }

        // Skip bots and crawlers
        $userAgent = strtolower($request->userAgent() ?? '');
        foreach (self::BOT_PATTERNS as $pattern) {
            if (str_contains($userAgent, $pattern)) {
                return $response;
            }
        }

        // Skip if response was an error
        if ($response->getStatusCode() >= 400) {
            return $response;
        }

        $this->record($request, $response);

        // Track Visitor Session for advanced analytics
        $this->recordSession($request);

        return $response;
    }

    private function record(Request $request, Response $response): void
    {
        try {
            $now      = now();
            $today    = $now->toDateString();
            $hour     = $now->hour;
            $isUnique = $this->isUniqueVisitorToday($request, $today);

            // Response size in MB
            $contentLength = strlen($response->getContent() ?? '');
            $dataMb        = round($contentLength / 1048576, 4); // bytes → MB

            // Atomic upsert: increment today's row or create it
            DB::table('traffic_reports')
                ->upsert(
                    [
                        'report_date'     => $today,
                        'page_views'      => 1,
                        'unique_visitors' => $isUnique ? 1 : 0,
                        'data_consumed_mb'=> $dataMb,
                        'created_at'      => $now,
                        'updated_at'      => $now,
                    ],
                    ['report_date'], // unique key
                    // On duplicate: increment
                    [
                        'page_views'       => DB::raw('page_views + 1'),
                        'unique_visitors'  => DB::raw('unique_visitors + ' . ($isUnique ? 1 : 0)),
                        'data_consumed_mb' => DB::raw("data_consumed_mb + {$dataMb}"),
                        'updated_at'       => $now,
                    ]
                );

            // Atomic upsert for hourly report
            DB::table('traffic_report_hourlies')
                ->upsert(
                    [
                        'report_date'     => $today,
                        'hour'            => $hour,
                        'page_views'      => 1,
                        'unique_visitors' => $isUnique ? 1 : 0,
                        'data_consumed_mb'=> $dataMb,
                        'created_at'      => $now,
                        'updated_at'      => $now,
                    ],
                    ['report_date', 'hour'], // unique key
                    [
                        'page_views'       => DB::raw('page_views + 1'),
                        'unique_visitors'  => DB::raw('unique_visitors + ' . ($isUnique ? 1 : 0)),
                        'data_consumed_mb' => DB::raw("data_consumed_mb + {$dataMb}"),
                        'updated_at'       => $now,
                    ]
                );
        } catch (\Throwable $e) {
            // Never let analytics break the page response
            // \Log::error('Analytics failed: ' . $e->getMessage());
        }
    }

    private function recordSession(Request $request): void
    {
        try {
            // Check for persistent tracking cookie (365 days)
            $visitorCookieName = 'atomni_visitor_id';
            $isNewVisitor = false;
            $visitorId = $request->cookie($visitorCookieName);
            
            if (!$visitorId) {
                $isNewVisitor = true;
                $visitorId = \Illuminate\Support\Str::uuid()->toString();
                // We queue the cookie so it attaches to the response
                \Illuminate\Support\Facades\Cookie::queue($visitorCookieName, $visitorId, 60 * 24 * 365);
            }

            // Session ID tied to the user's browser session (cleared on browser close)
            $sessionId = $request->session()->getId();
            $now = now();

            $referrer = $request->headers->get('referer');
            $channel = 'Direct';
            
            if ($referrer) {
                $host = parse_url($referrer, PHP_URL_HOST) ?? '';
                if (str_contains($host, 'google.') || str_contains($host, 'bing.') || str_contains($host, 'yahoo.')) {
                    $channel = 'Organic Search';
                } elseif (str_contains($host, 'facebook.') || str_contains($host, 'twitter.') || str_contains($host, 't.co') || str_contains($host, 'linkedin.') || str_contains($host, 'instagram.')) {
                    $channel = 'Social';
                } elseif (str_contains($host, $request->getHost())) {
                    // Internal link, we shouldn't change the original channel of the session
                    // We'll leave it to be handled below (we don't override the existing channel)
                    $channel = null; 
                } else {
                    $channel = 'Referral';
                }
            }

            $session = \App\Models\VisitorSession::where('session_id', $sessionId)->first();

            if ($session) {
                $session->last_activity_at = $now;
                $session->page_views += 1;
                $session->save();
            } else {
                \App\Models\VisitorSession::create([
                    'session_id' => $sessionId,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'referrer' => $referrer,
                    'channel' => $channel ?? 'Direct',
                    'city' => null, // GeoIP can be implemented later
                    'is_new_visitor' => $isNewVisitor,
                    'started_at' => $now,
                    'last_activity_at' => $now,
                    'page_views' => 1
                ]);
            }
        } catch (\Throwable $e) {
            // Silently fail
        }
    }

    /**
     * A visitor is "unique" for today if we haven't seen their fingerprint
     * in the session today. Uses session-day key so it resets at midnight.
     */
    private function isUniqueVisitorToday(Request $request, string $today): bool
    {
        $sessionKey = 'pv_seen_' . $today;

        if ($request->session()->has($sessionKey)) {
            return false;
        }

        $request->session()->put($sessionKey, 1);
        return true;
    }
}
