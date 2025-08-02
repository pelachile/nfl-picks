<?php

namespace App\Console\Commands;

use App\Http\Integrations\ESPN\ESPNConnector;
use App\Http\Integrations\ESPN\Requests\GetCurrentWeekGames;
use App\Http\Integrations\ESPN\Requests\GetWeekGames;
use Illuminate\Console\Command;

class TestESPNApi extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:espn-api {--week=1 : Week number to fetch}';

    /**
     * The console command description.
     */
    protected $description = 'Test ESPN API integration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing ESPN API integration...');

        $connector = new ESPNConnector;

        // Debug: Show the base URL
        $baseUrl = $connector->resolveBaseUrl();
        $this->info('Base URL: '.$baseUrl);

        if (empty($baseUrl)) {
            $this->error('❌ Base URL is empty!');

            return;
        }

        try {
            $this->info('Fetching current week games...');
            $currentWeekRequest = new GetCurrentWeekGames;

            // Debug: Show the full URL
            $fullUrl = $connector->resolveBaseUrl().$currentWeekRequest->resolveEndpoint();
            $this->info('Full URL: '.$fullUrl);

            // Test current week games
            $this->info('Fetching current week games...');
            $currentWeekRequest = new GetCurrentWeekGames;
            $currentWeekResponse = $connector->send($currentWeekRequest);

            if ($currentWeekResponse->successful()) {
                $data = $currentWeekResponse->json();
                $this->info('✅ Current week API call successful');
                $this->info('Found '.count($data['events'] ?? []).' games');

                // Show first game as example
                if (! empty($data['events'])) {
                    $firstGame = $data['events'][0];
                    $this->info('Example game: '.($firstGame['name'] ?? 'N/A'));
                }
            } else {
                $this->error('❌ Current week API call failed');
                $this->error('Status: '.$currentWeekResponse->status());
            }

            // Test specific week
            $week = $this->option('week');
            $season = now()->year;

            $this->info("Fetching week {$week} games for {$season} season...");
            $weekRequest = new GetWeekGames($week, $season);
            $weekResponse = $connector->send($weekRequest);

            if ($weekResponse->successful()) {
                $data = $weekResponse->json();
                $this->info('✅ Week-specific API call successful');
                $this->info('Found '.count($data['events'] ?? []).' games for week '.$week);
            } else {
                $this->error('❌ Week-specific API call failed');
                $this->error('Status: '.$weekResponse->status());
            }

        } catch (\Exception $e) {
            $this->error('❌ API test failed with exception: '.$e->getMessage());
        }
    }
}
