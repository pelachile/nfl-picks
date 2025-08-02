<?php

namespace App\Console\Commands;

use App\Contracts\NFLDataServiceInterface;
use App\Models\Game;
use App\Models\Pick;
use Illuminate\Console\Command;

class UpdateGameScores extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'nfl:update-scores {--week= : Specific week to update} {--all : Update all active games}';

    /**
     * The console command description.
     */
    protected $description = 'Update NFL game scores and evaluate user picks';

    /**
     * Execute the console command.
     */
    public function handle(NFLDataServiceInterface $nflService)
    {
        $this->info('🏈 Updating NFL game scores...');

        try {
            if ($this->option('all')) {
                // Update all games that are not completed
                $games = Game::whereIn('status', ['scheduled', 'in_progress'])->get();
                $this->info("Updating all active games ({$games->count()} games)");
            } elseif ($week = $this->option('week')) {
                // Update specific week
                $season = $nflService->getCurrentSeason();
                $games = $nflService->getWeekGames($week, $season);
                $this->info("Updating week {$week} games ({$games->count()} games)");
            } else {
                // Update current week by default
                $games = $nflService->updateGameScores();
                $currentWeek = $nflService->getCurrentWeek();
                $this->info("Updating current week {$currentWeek} games ({$games->count()} games)");
            }

            if ($games->isEmpty()) {
                $this->warn('⚠️ No games to update');

                return Command::SUCCESS;
            }

            $updatedCount = 0;
            $completedCount = 0;

            foreach ($games as $game) {
                $oldStatus = $game->status;

                // Refresh game data from API
                $updatedGame = $nflService->getGameById($game->external_id);

                if ($updatedGame && $updatedGame->status !== $oldStatus) {
                    $updatedCount++;

                    if ($updatedGame->status === 'completed') {
                        $completedCount++;
                        $this->info("✅ Game completed: {$updatedGame->away_team_abbr} @ {$updatedGame->home_team_abbr}");

                        // Evaluate picks for completed game
                        $this->evaluatePicksForGame($updatedGame);
                    } else {
                        $this->info("📊 Status updated: {$updatedGame->away_team_abbr} @ {$updatedGame->home_team_abbr} -> {$updatedGame->status}");
                    }
                }
            }

            $this->newLine();
            $this->info('✅ Update complete:');
            $this->info("• {$updatedCount} games updated");
            $this->info("• {$completedCount} games completed");

        } catch (\Exception $e) {
            $this->error('❌ Failed to update scores: '.$e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    protected function evaluatePicksForGame(Game $game)
    {
        $picks = Pick::where('game_id', $game->id)
            ->whereNull('is_correct')
            ->get();

        if ($picks->isEmpty()) {
            return;
        }

        $correctPicks = 0;

        foreach ($picks as $pick) {
            $pick->evaluateCorrectness();
            if ($pick->is_correct) {
                $correctPicks++;
            }
        }

        $this->info("  📝 Evaluated {$picks->count()} picks ({$correctPicks} correct)");
    }
}
