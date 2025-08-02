<?php

namespace App\Console\Commands;

use App\Contracts\NFLDataServiceInterface;
use App\Models\Game;
use Illuminate\Console\Command;

class TestNFLDataService extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:nfl-service {--week=1 : Week number to test}';

    /**
     * The console command description.
     */
    protected $description = 'Test NFL Data Service with clean architecture';

    /**
     * Execute the console command.
     */
    public function handle(NFLDataServiceInterface $nflService)
    {
        $this->info('🏈 Testing Individual Game Fetch...');

        // Test fetching one specific game by ID
        $gameId = '401671789'; // From the ESPN response

        $this->info("Testing getGameById with ID: {$gameId}");

        try {
            $game = $nflService->getGameById($gameId);

            if ($game) {
                $this->info('✅ Game fetched successfully!');
                $this->info("Game: {$game->away_team} @ {$game->home_team}");
                $this->info("Date: {$game->game_date}");
                $this->info("Status: {$game->status}");
            } else {
                $this->error('❌ getGameById returned null');
            }

        } catch (\Exception $e) {
            $this->error('❌ getGameById failed: '.$e->getMessage());
        }

        $this->newLine();

        // Test the full week process
        $this->info('Testing full getWeekGames process...');

        try {
            $games = $nflService->getWeekGames(1, 2024);
            $this->info("getWeekGames returned {$games->count()} games");

            foreach ($games as $game) {
                $this->info("• {$game->away_team_abbr} @ {$game->home_team_abbr}");
            }

        } catch (\Exception $e) {
            $this->error('getWeekGames failed: '.$e->getMessage());
        }
    }
}
