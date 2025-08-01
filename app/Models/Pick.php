<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pick extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'game_id',
        'group_id',
        'picked_team',
        'picked_team_abbr',
        'is_correct',
        'points_earned',
        'picked_at',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'picked_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    // Helper methods
    public function evaluateCorrectness()
    {
        if (! $this->game->isCompleted()) {
            return;
        }

        $this->is_correct = $this->picked_team === $this->game->winning_team;
        $this->points_earned = $this->is_correct ? 1 : 0;
        $this->save();
    }
}
