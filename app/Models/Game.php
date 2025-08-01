<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_id',
        'week',
        'season',
        'home_team',
        'away_team',
        'home_team_abbr',
        'away_team_abbr',
        'game_date',
        'status',
        'home_score',
        'away_score',
        'winning_team',
        'winning_team_abbr',
        'metadata',
    ];

    protected $casts = [
        'game_date' => 'datetime',
        'metadata' => 'array',
    ];

    // Relationships
    public function picks()
    {
        return $this->hasMany(Pick::class);
    }

    // Helper methods
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function hasStarted()
    {
        return now()->greaterThan($this->game_date);
    }

    public function canMakePicks()
    {
        return ! $this->hasStarted() && $this->status === 'scheduled';
    }

    public function getWinner()
    {
        if (! $this->isCompleted() || $this->home_score === $this->away_score) {
            return null;
        }

        return $this->home_score > $this->away_score ? 'home' : 'away';
    }

    public function determineWinner()
    {
        if ($this->isCompleted() && $this->home_score !== $this->away_score) {
            if ($this->home_score > $this->away_score) {
                $this->winning_team = $this->home_team;
                $this->winning_team_abbr = $this->home_team_abbr;
            } else {
                $this->winning_team = $this->away_team;
                $this->winning_team_abbr = $this->away_team_abbr;
            }
        }
    }
}
