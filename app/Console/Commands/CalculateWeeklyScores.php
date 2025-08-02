<?php

namespace App\Console\Commands;

use App\Contracts\NFLDataServiceInterface;
use App\Models\Game;
use App\Models\Group;
use App\Models\Pick;
use Illuminate\Console\Command;

class CalculateWeeklyScores extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'nfl:calculate-scores {--week= : Specific week to calculate} {--season= : Specific season}';

    /**
     * The console command description.
     */
    protected $description = 'Calculate weekly scores and leaderboards for all groups';

    /**
     * Execute the console command.
     */
    public function handle(NFLDataServiceInterface $nflService)
    {
        $this->info('🏆 Calculating weekly scores...');

        $week = $this->option('week') ?? $nflService->getCurrentWeek();
        $season = $this->option('season') ?? $nflService->getCurrentSeason();

        $this->info("Calculating scores for Week {$week}, Season {$season}");

        try {
            // Get all completed games for the week
            $completedGames = Game::where('week', $week)
                ->where('season', $season)
                ->where('status', 'completed')
                ->get();

            if ($completedGames->isEmpty()) {
                $this->warn("⚠️ No completed games found for week {$week}");

                return Command::SUCCESS;
            }

            $this->info("Found {$completedGames->count()} completed games");

            // Get all groups that have picks for this week
            $groups = Group::whereHas('picks', function ($query) use ($week, $season) {
                $query->whereHas('game', function ($gameQuery) use ($week, $season) {
                    $gameQuery->where('week', $week)->where('season', $season);
                });
            })->get();

            if ($groups->isEmpty()) {
                $this->warn("⚠️ No groups found with picks for week {$week}");

                return Command::SUCCESS;
            }

            $totalScoresCalculated = 0;

            foreach ($groups as $group) {
                $this->info("Calculating scores for group: {$group->name}");

                $scores = $this->calculateGroupScores($group, $week, $season);
                $totalScoresCalculated += count($scores);

                // Display group leaderboard
                $this->displayGroupLeaderboard($group, $scores);
            }

            $this->newLine();
            $this->info('✅ Score calculation complete!');
            $this->info("• {$groups->count()} groups processed");
            $this->info("• {$totalScoresCalculated} user scores calculated");

        } catch (\Exception $e) {
            $this->error('❌ Failed to calculate scores: '.$e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    protected function calculateGroupScores(Group $group, int $week, int $season): array
    {
        $userScores = [];

        // Get all users in this group
        $users = $group->activeMembers;

        foreach ($users as $user) {
            $correctPicks = Pick::where('user_id', $user->id)
                ->where('group_id', $group->id)
                ->where('is_correct', true)
                ->whereHas('game', function ($query) use ($week, $season) {
                    $query->where('week', $week)
                        ->where('season', $season)
                        ->where('status', 'completed');
                })
                ->count();

            $totalPicks = Pick::where('user_id', $user->id)
                ->where('group_id', $group->id)
                ->whereHas('game', function ($query) use ($week, $season) {
                    $query->where('week', $week)
                        ->where('season', $season)
                        ->where('status', 'completed');
                })
                ->count();

            $userScores[] = [
                'user' => $user,
                'correct' => $correctPicks,
                'total' => $totalPicks,
                'percentage' => $totalPicks > 0 ? round(($correctPicks / $totalPicks) * 100, 1) : 0,
            ];
        }

        // Sort by correct picks (descending), then by percentage
        usort($userScores, function ($a, $b) {
            if ($a['correct'] === $b['correct']) {
                return $b['percentage'] <=> $a['percentage'];
            }

            return $b['correct'] <=> $a['correct'];
        });

        return $userScores;
    }

    protected function displayGroupLeaderboard(Group $group, array $scores)
    {
        if (empty($scores)) {
            $this->info("  No scores to display for {$group->name}");

            return;
        }

        $this->newLine();
        $this->info("📊 Leaderboard for: {$group->name}");
        $this->table(
            ['Rank', 'User', 'Correct', 'Total', 'Percentage'],
            collect($scores)->map(function ($score, $index) {
                return [
                    $index + 1,
                    $score['user']->name,
                    $score['correct'],
                    $score['total'],
                    $score['percentage'].'%',
                ];
            })->toArray()
        );
    }
}
