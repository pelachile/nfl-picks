<?php

namespace App\Http\Integrations\ESPN\Requests;

use App\DataObjects\GameData;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetCurrentWeekGames extends Request
{
    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::GET;

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/events';
    }

    /**
     * Default query parameters
     */
    protected function defaultQuery(): array
    {
        return [
            'limit' => 100,
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

        foreach ($data['items'] ?? [] as $game) {
            $gameDto = $this->transformGameData($game);
            if ($gameDto) {
                $games[] = $gameDto;
            }
        }

        return $games;
    }

    protected function transformGameData(array $game): ?GameData
    {
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
                week: $game['week']['number'] ?? 1,
                season: $game['season']['year'] ?? now()->year,
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
