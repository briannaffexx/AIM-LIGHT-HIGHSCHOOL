<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Automated Daily Database Backup at Midnight with 60-day rolling retention
Schedule::command('school:backup --prune-days=60')
    ->dailyAt('00:00')
    ->runInBackground();

