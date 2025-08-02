<?php

use Illuminate\Support\Facades\Schedule;

// Fetch current week games - run once daily at 6 AM
Schedule::command('nfl:fetch-current-week')
    ->dailyAt('06:00')
    ->withoutOverlapping()
    ->runInBackground();

// Update game scores during NFL season
// Run every 15 minutes on Thursday, Sunday, and Monday (main NFL game days)
Schedule::command('nfl:update-scores')
    ->everyFifteenMinutes()
    ->days([0, 4, 7]) // Sunday=0, Thursday=4, Monday=1 (adjust as needed)
    ->between('12:00', '23:59')
    ->withoutOverlapping()
    ->runInBackground();

// Update scores more frequently during peak game times on Sunday
Schedule::command('nfl:update-scores')
    ->everyTenMinutes()
    ->sundays()
    ->between('13:00', '20:00') // 1 PM to 8 PM on Sundays
    ->withoutOverlapping()
    ->runInBackground();

// Daily score update to catch any missed games
Schedule::command('nfl:update-scores --all')
    ->dailyAt('23:30')
    ->withoutOverlapping()
    ->runInBackground();

// Calculate weekly scores after games are likely complete (Tuesday mornings)
Schedule::command('nfl:calculate-scores')
    ->weeklyOn(2, '08:00') // Tuesdays at 8 AM
    ->withoutOverlapping()
    ->runInBackground();
