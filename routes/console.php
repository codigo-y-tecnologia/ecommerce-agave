<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('carritos:limpiar-abandonados')
    ->dailyAt('03:00');

Schedule::command('carts:notify-active')
    ->hourly();

Schedule::command('carts:cleanup-old')
    ->dailyAt('04:00');
