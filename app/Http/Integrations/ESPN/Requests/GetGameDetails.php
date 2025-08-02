<?php

namespace App\Http\Integrations\ESPN\Requests;

use App\DataObjects\GameData;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetGameDetails extends Request
{
    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::GET;

    public function __construct(
        protected string $gameId
    ) {}

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return "/events/{$this->gameId}";
    }

    /**
     * Query parameters for specific game
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
    public function createDtoFromResponse(Response $response): ?GameData
    {
        $data = $response->json();

        return $this->transformGameData($data);
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

            // Extract team names from the game's name field since team data is referenced
            // Game name format: "Baltimore Ravens at Kansas City Chiefs"
            $gameName = $game['name'] ?? '';
            $shortName = $game['shortName'] ?? '';

            // Parse team names from shortName (e.g., "BAL @ KC")
            $teamAbbrs = explode(' @ ', $shortName);
            $awayTeamAbbr = $teamAbbrs[0] ?? 'UNK';
            $homeTeamAbbr = $teamAbbrs[1] ?? 'UNK';

            // Parse full team names from game name
            $teamNames = explode(' at ', $gameName);
            $awayTeamName = trim($teamNames[0] ?? 'Unknown');
            $homeTeamName = trim($teamNames[1] ?? 'Unknown');

            // Get week from the game data - extract from week reference or use fallback
            $weekNum = 1; // Default
            if (isset($game['week']['$ref'])) {
                // Extract week number from URL like: .../weeks/1?lang=en
                if (preg_match('/weeks\/(\d+)/', $game['week']['$ref'], $matches)) {
                    $weekNum = (int) $matches[1];
                }
            }

            // Get season from the game data
            $seasonYear = 2024; // Default
            if (isset($game['season']['$ref'])) {
                // Extract season from URL like: .../seasons/2024?lang=en
                if (preg_match('/seasons\/(\d+)/', $game['season']['$ref'], $matches)) {
                    $seasonYear = (int) $matches[1];
                }
            }

            // Determine scores - we'll need to fetch these separately or set as null for now
            $homeScore = null;
            $awayScore = null;

            // Determine game status from competition status if available
            $status = 'scheduled';
            if (isset($competitions['status']['$ref'])) {
                // For now, use completed since this is a past game
                $status = 'completed';
            }

            return new GameData(
                externalId: $game['id'],
                week: $weekNum,
                season: $seasonYear,
                homeTeam: $homeTeamName,
                awayTeam: $awayTeamName,
                homeTeamAbbr: $homeTeamAbbr,
                awayTeamAbbr: $awayTeamAbbr,
                gameDate: new \DateTime($game['date']),
                status: $status,
                homeScore: $homeScore,
                awayScore: $awayScore,
                metadata: $game
            );

        } catch (\Exception $e) {
            \Log::error('Failed to transform game data: '.$e->getMessage());
            \Log::error('Game data: '.json_encode($game));

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
