<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\CarritoMergeService;
use App\Services\DireccionMergeService;

class MergeGuestCartOnLogin
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        if (!session()->has('guest_token')) {
            return;
        }

        $guestToken = session('guest_token');

        app(CarritoMergeService::class)
            ->merge($guestToken, $event->user->id_usuario);

        app(DireccionMergeService::class)
            ->merge($guestToken, $event->user->id_usuario);

        // Limpiar sesión
        session()->forget('guest_token');
    }
}
