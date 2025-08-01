<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->datetime('joined_at');
            $table->boolean('is_active')->default(true);
            $table->enum('role', ['member', 'admin'])->default('member');
            $table->timestamps();

            // Ensure a user can only be in a group once
            $table->unique(['group_id', 'user_id']);

            // Indexes for performance
            $table->index(['group_id', 'is_active']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_members');
    }
};
