<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\LimpiarReservasExpiradas;
use App\Services\Cupones\LiberarCuponService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('carritos:limpiar-abandonados')
    ->dailyAt('03:00');

Schedule::command('guest:clean-addresses', ['--days' => 15])
    ->dailyAt('03:30')
    ->withoutOverlapping();

Schedule::command('carts:notify-active')
    ->hourly();

Schedule::command('carts:cleanup-old')
    ->dailyAt('04:00');

Schedule::command('snapshots:cleanup-orphaned')
    ->dailyAt('04:30')
    ->withoutOverlapping();

Schedule::job(new LimpiarReservasExpiradas)->everyMinute()->withoutOverlapping();

Schedule::call(function () {
    app(LiberarCuponService::class)->limpiarExpiradas();
})
    ->name('cupones_liberar_expiradas')
    ->everyMinute()
    ->withoutOverlapping();
