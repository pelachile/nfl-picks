<?php

namespace App\Services\ESPN;

use App\Contracts\NFLDataServiceInterface;
use App\DataObjects\GameData;
use App\Http\Integrations\ESPN\ESPNConnector;
use App\Http\Integrations\ESPN\Requests\GetCurrentWeekGames;
use App\Http\Integrations\ESPN\Requests\GetGameDetails;
use App\Http\Integrations\ESPN\Requests\GetWeekGames;
use App\Models\Game;
use Illuminate\Support\Collection;

class ESPNNFLDataService implements NFLDataServiceInterface
{
    protected ESPNConnector $connector;

    public function __construct()
    {
        $this->connector = new ESPNConnector;
    }

    public function getCurrentWeekGames(): Collection
    {
        try {
            $request = new GetCurrentWeekGames;
            $response = $this->connector->send($request);

            if (! $response->successful()) {
                throw new \Exception('ESPN API request failed: '.$response->status());
            }

            // Use Saloon's DTO method
            $gameDTOs = $request->createDtoFromResponse($response);

            // Convert DTOs to our Game models and save to database
            return collect($gameDTOs)->map(function (GameData $gameDTO) {
                return Game::updateOrCreate(
                    ['external_id' => $gameDTO->externalId],
                    $gameDTO->toArray()
                );
            });

        } catch (\Exception $e) {
            \Log::error('ESPN getCurrentWeekGames failed: '.$e->getMessage());

            return collect();
        }
    }

    public function getWeekGames(int $week, ?int $season = null): Collection
    {
        $season = $season ?? $this->getCurrentSeason();

        try {
            $request = new GetWeekGames($week, $season);
            $response = $this->connector->send($request);

            if (! $response->successful()) {
                throw new \Exception('ESPN API request failed: '.$response->status());
            }

            // Get game references from the week endpoint
            $gameRefs = $request->createDtoFromResponse($response);
            $games = collect();

            // Fetch actual game details for each reference
            foreach ($gameRefs as $gameRef) {
                if (isset($gameRef['id'])) {
                    $gameDetail = $this->getGameById($gameRef['id']);
                    if ($gameDetail) {
                        $games->push($gameDetail);
                    }
                }
            }

            return $games;

        } catch (\Exception $e) {
            \Log::error('ESPN getWeekGames failed: '.$e->getMessage());

            return collect();
        }
    }

    public function getGameById(string $gameId): ?Game
    {
        try {
            $request = new GetGameDetails($gameId);
            $response = $this->connector->send($request);

            if (! $response->successful()) {
                return null;
            }

            // Use Saloon's DTO method
            $gameDTO = $request->createDtoFromResponse($response);

            if (! $gameDTO) {
                return null;
            }

            return Game::updateOrCreate(
                ['external_id' => $gameDTO->externalId],
                $gameDTO->toArray()
            );

        } catch (\Exception $e) {
            \Log::error('ESPN getGameById failed: '.$e->getMessage());

            return null;
        }
    }

    public function updateGameScores(): Collection
    {
        // Get current week games and update their scores
        $currentGames = $this->getCurrentWeekGames();

        // Update any games that are in progress or completed
        foreach ($currentGames as $game) {
            if ($game->status === 'completed' && $game->winning_team === null) {
                $game->determineWinner();
                $game->save();
            }
        }

        return $currentGames;
    }

    public function getCurrentWeek(): int
    {
        // Simple calculation for current NFL week
        // You might want to make this more sophisticated based on actual NFL schedule
        $now = now();

        // NFL season typically runs from September to February
        if ($now->month >= 9 || $now->month <= 2) {
            $startOfSeason = $now->month >= 9 ?
                $now->copy()->month(9)->day(1) :
                $now->copy()->subYear()->month(9)->day(1);

            $weeksSinceStart = $now->diffInWeeks($startOfSeason);

            return min(18, max(1, $weeksSinceStart + 1));
        }

        return 1; // Off-season default
    }

    public function getCurrentSeason(): int
    {
        $now = now();

        // NFL season spans two calendar years
        // Season starts in September of year X and ends in February of year X+1

        if ($now->month >= 9) {
            // September through December - current year season
            return $now->year;
        } elseif ($now->month <= 2) {
            // January through February - previous year's season
            return $now->year - 1;
        } else {
            // March through August - upcoming season (current year)
            return $now->year;
        }
    }
}
