<?php

namespace App\Console\Commands;

use App\Contracts\NFLDataServiceInterface;
use App\Models\Game;
use Illuminate\Console\Command;

class FetchCurrentWeekGames extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'nfl:fetch-current-week {--force : Force fetch even if games already exist} {--season= : Specific season to fetch}';

    /**
     * The console command description.
     */
    protected $description = 'Fetch current week NFL games from ESPN API';

    /**
     * Execute the console command.
     */
    public function handle(NFLDataServiceInterface $nflService)
    {
        $this->info('🏈 Fetching current week NFL games...');

        // Use season option or default to current
        $currentSeason = $this->option('season') ? (int) $this->option('season') : $nflService->getCurrentSeason();
        $currentWeek = $nflService->getCurrentWeek();

        $this->info("Fetching Week: {$currentWeek}, Season: {$currentSeason}");

        // Check if we already have games for current week (unless forced)
        if (! $this->option('force')) {
            $existingGames = Game::where('week', $currentWeek)
                ->where('season', $currentSeason)
                ->count();

            if ($existingGames > 0) {
                $this->info("✅ Already have {$existingGames} games for week {$currentWeek}");
                $this->info('Use --force to fetch anyway');

                return Command::SUCCESS;
            }
        }

        try {
            // Fetch games for specific season/week instead of current
            $games = $nflService->getWeekGames($currentWeek, $currentSeason);

            if ($games->isEmpty()) {
                $this->warn("⚠️ No games found for week {$currentWeek}, season {$currentSeason}");
                $this->info('💡 Try with 2024 season: --season=2024');

                return Command::SUCCESS;
            }

            // Rest of your existing code...
            $this->info("📥 Fetched {$games->count()} games");

            $this->newLine();
            $this->info('Games for this week:');
            $this->table(
                ['Away Team', 'Home Team', 'Date', 'Status'],
                $games->map(function ($game) {
                    return [
                        $game->away_team_abbr,
                        $game->home_team_abbr,
                        $game->game_date->format('M j, Y g:i A'),
                        ucfirst($game->status),
                    ];
                })->toArray()
            );

            $this->newLine();
            $this->info("✅ Successfully fetched and stored {$games->count()} games");

        } catch (\Exception $e) {
            $this->error('❌ Failed to fetch games: '.$e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
