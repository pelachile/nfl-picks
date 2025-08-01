<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique(); // ESPN game ID
            $table->integer('week');
            $table->integer('season');
            $table->string('home_team', 50);
            $table->string('away_team', 50);
            $table->string('home_team_abbr', 5);
            $table->string('away_team_abbr', 5);
            $table->datetime('game_date');
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'postponed', 'cancelled'])->default('scheduled');
            $table->integer('home_score')->nullable();
            $table->integer('away_score')->nullable();
            $table->string('winning_team', 50)->nullable();
            $table->string('winning_team_abbr', 5)->nullable();
            $table->json('metadata')->nullable(); // Store additional ESPN data
            $table->timestamps();

            // Indexes for performance
            $table->index(['week', 'season']);
            $table->index('external_id');
            $table->index('game_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
