<?php

namespace App\Contracts;

use App\Models\Game;
use Illuminate\Support\Collection;

interface NFLDataServiceInterface
{
    /**
     * Get games for the current NFL week
     */
    public function getCurrentWeekGames(): Collection;

    /**
     * Get games for a specific week and season
     */
    public function getWeekGames(int $week, ?int $season = null): Collection;

    /**
     * Get a single game by external ID
     */
    public function getGameById(string $gameId): ?Game;

    /**
     * Update scores for all active games
     */
    public function updateGameScores(): Collection;

    /**
     * Get current NFL week number
     */
    public function getCurrentWeek(): int;

    /**
     * Get current NFL season year
     */
    public function getCurrentSeason(): int;
}
