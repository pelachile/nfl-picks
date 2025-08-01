<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'invite_code',
        'owner_id',
        'max_members',
        'is_active',
        'is_public',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_public' => 'boolean',
    ];

    // Relationships
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'group_members')
            ->withPivot('joined_at', 'is_active', 'role')
            ->withTimestamps();
    }

    public function activeMembers()
    {
        return $this->members()->wherePivot('is_active', true);
    }

    public function picks()
    {
        return $this->hasMany(Pick::class);
    }

    // Helper methods
    public function generateInviteCode()
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (self::where('invite_code', $code)->exists());

        return $code;
    }

    public function isFull()
    {
        return $this->activeMembers()->count() >= $this->max_members;
    }
}
