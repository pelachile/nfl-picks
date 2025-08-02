<?php

namespace App\DataObjects;

class GameData
{
    public function __construct(
        public string $externalId,
        public int $week,
        public int $season,
        public string $homeTeam,
        public string $awayTeam,
        public string $homeTeamAbbr,
        public string $awayTeamAbbr,
        public \DateTime $gameDate,
        public string $status,
        public ?int $homeScore = null,
        public ?int $awayScore = null,
        public ?string $winningTeam = null,
        public ?string $winningTeamAbbr = null,
        public array $metadata = []
    ) {}

    public function toArray(): array
    {
        return [
            'external_id' => $this->externalId,
            'week' => $this->week,
            'season' => $this->season,
            'home_team' => $this->homeTeam,
            'away_team' => $this->awayTeam,
            'home_team_abbr' => $this->homeTeamAbbr,
            'away_team_abbr' => $this->awayTeamAbbr,
            'game_date' => $this->gameDate,
            'status' => $this->status,
            'home_score' => $this->homeScore,
            'away_score' => $this->awayScore,
            'winning_team' => $this->winningTeam,
            'winning_team_abbr' => $this->winningTeamAbbr,
            'metadata' => $this->metadata,
        ];
    }
}
