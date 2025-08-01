<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('picks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('game_id')->constrained()->onDelete('cascade');
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->string('picked_team', 50);
            $table->string('picked_team_abbr', 5);
            $table->boolean('is_correct')->nullable(); // null until game completes
            $table->integer('points_earned')->default(0);
            $table->datetime('picked_at');
            $table->timestamps();

            // Ensure one pick per user per game per group
            $table->unique(['user_id', 'game_id', 'group_id']);

            // Indexes for performance
            $table->index(['user_id', 'group_id']);
            $table->index(['game_id', 'group_id']);
            $table->index('is_correct');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('picks');
    }
};
