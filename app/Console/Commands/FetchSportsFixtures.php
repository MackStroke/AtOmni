<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\SportsFixture;
use Carbon\Carbon;

class FetchSportsFixtures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:sports-fixtures';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch the latest sports fixtures from public ESPN API and update the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fetching latest sports fixtures...');

        // Using ESPN's public scoreboard API for FIFA World Cup (or any active league)
        $url = 'https://site.api.espn.com/apis/site/v2/sports/soccer/fifa.world/scoreboard';

        try {
            $response = Http::get($url);

            if ($response->successful()) {
                $data = $response->json();
                $events = $data['events'] ?? [];

                if (empty($events)) {
                    $this->warn('No events found at the moment. Trying English Premier League as fallback...');
                    $fallbackUrl = 'https://site.api.espn.com/apis/site/v2/sports/soccer/eng.1/scoreboard';
                    $response = Http::get($fallbackUrl);
                    if ($response->successful()) {
                        $data = $response->json();
                        $events = $data['events'] ?? [];
                    }
                }

                $count = 0;
                foreach ($events as $event) {
                    $competition = $event['competitions'][0] ?? null;
                    if (!$competition) continue;

                    $competitors = $competition['competitors'] ?? [];
                    if (count($competitors) < 2) continue;

                    // ESPN puts home team at index 0 and away at index 1 usually
                    $teamA = $competitors[0]['team'];
                    $teamB = $competitors[1]['team'];

                    $scoreA = $competitors[0]['score'] ?? '-';
                    $scoreB = $competitors[1]['score'] ?? '-';

                    $status = $event['status']['type']['detail'] ?? 'Upcoming';
                    $date = Carbon::parse($event['date']);

                    $link = $event['links'][0]['href'] ?? null;

                    SportsFixture::updateOrCreate(
                        ['event_id' => $event['id']],
                        [
                            'team_a_name' => $teamA['displayName'],
                            'team_a_logo' => $teamA['logo'] ?? null,
                            'team_a_score' => $scoreA,
                            'team_a_abbrev' => $teamA['abbreviation'] ?? substr($teamA['displayName'], 0, 3),
                            'team_b_name' => $teamB['displayName'],
                            'team_b_logo' => $teamB['logo'] ?? null,
                            'team_b_score' => $scoreB,
                            'team_b_abbrev' => $teamB['abbreviation'] ?? substr($teamB['displayName'], 0, 3),
                            'match_status' => $status,
                            'match_time' => $date,
                            'link' => $link,
                        ]
                    );
                    $count++;
                }

                $this->info("Successfully fetched and updated {$count} fixtures.");
            } else {
                $this->error('Failed to fetch data from API. Status: ' . $response->status());
            }
        } catch (\Exception $e) {
            $this->error('Error fetching fixtures: ' . $e->getMessage());
        }
    }
}
