<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule Cal.com sync to run every 15 minutes
Schedule::command('leads:sync-calcom', ['--all' => true])
    ->everyFifteenMinutes()
    ->withoutOverlapping()
    ->runInBackground();

