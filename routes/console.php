<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Keep portfolio quotes fresh for near real-time dashboard updates.
Schedule::command('app:fetch-quotes')
    ->everyMinute()
    ->withoutOverlapping();
