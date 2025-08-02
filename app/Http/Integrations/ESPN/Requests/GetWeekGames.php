<?php

namespace App\Http\Integrations\ESPN\Requests;

use App\DataObjects\GameData;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetWeekGames extends Request
{
    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::GET;

    public function __construct(
        protected int $week,
        protected int $season
    ) {}

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return "/seasons/{$this->season}/types/2/weeks/{$this->week}/events";
    }

    /**
     * Query parameters for specific week
     */
    protected function defaultQuery(): array
    {
        return [
            'lang' => 'en',
            'region' => 'us',
        ];
    }

    /**
     * Create DTO from response
     */
    public function createDtoFromResponse(Response $response): array
    {
        $data = $response->json();
        $games = [];

        // The events are in the items array
        foreach ($data['items'] ?? [] as $gameRef) {
            // Each item is a reference like {"$ref": "http://sports.core.api.espn.com/v2/sports/football/leagues/nfl/events/401671716?lang=en&region=us"}
            // We need to extract the game ID and fetch the actual game data
            if (isset($gameRef['$ref'])) {
                $gameId = $this->extractGameIdFromRef($gameRef['$ref']);
                if ($gameId) {
                    // For now, we'll store the reference and fetch details later
                    // Or we could make additional API calls here
                    $games[] = [
                        'id' => $gameId,
                        'ref' => $gameRef['$ref'],
                    ];
                }
            }
        }

        return $games;
    }

    protected function extractGameIdFromRef(string $ref): ?string
    {
        // Extract game ID from URL like: "http://sports.core.api.espn.com/v2/sports/football/leagues/nfl/events/401671716?lang=en&region=us"
        if (preg_match('/\/events\/(\d+)/', $ref, $matches)) {
            return $matches[1];
        }

        return null;
    }

    protected function transformGameData(array $game): ?GameData
    {
        // This method will be used when we fetch individual game details
        try {
            $competitions = $game['competitions'][0] ?? null;
            if (! $competitions) {
                return null;
            }

            $competitors = $competitions['competitors'] ?? [];
            $homeTeam = collect($competitors)->firstWhere('homeAway', 'home');
            $awayTeam = collect($competitors)->firstWhere('homeAway', 'away');

            if (! $homeTeam || ! $awayTeam) {
                return null;
            }

            return new GameData(
                externalId: $game['id'],
                week: $this->week,
                season: $this->season,
                homeTeam: $homeTeam['team']['displayName'],
                awayTeam: $awayTeam['team']['displayName'],
                homeTeamAbbr: $homeTeam['team']['abbreviation'],
                awayTeamAbbr: $awayTeam['team']['abbreviation'],
                gameDate: new \DateTime($game['date']),
                status: $this->mapESPNStatus($competitions['status']['type']['name'] ?? 'scheduled'),
                homeScore: $homeTeam['score'] ?? null,
                awayScore: $awayTeam['score'] ?? null,
                metadata: $game
            );
        } catch (\Exception $e) {
            \Log::error('Failed to transform game data: '.$e->getMessage());

            return null;
        }
    }

    protected function mapESPNStatus(string $espnStatus): string
    {
        return match (strtolower($espnStatus)) {
            'status_scheduled', 'pre' => 'scheduled',
            'status_in_progress', 'in' => 'in_progress',
            'status_final', 'post' => 'completed',
            'status_postponed' => 'postponed',
            'status_canceled' => 'cancelled',
            default => 'scheduled'
        };
    }
}
