<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Events\Verified;
use App\Services\PedidoMergeService;
use Illuminate\Support\Facades\Log;

class MergeGuestOrdersOnEmailVerified
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
    public function handle(Verified $event): void
    {
        $user = $event->user;

        // 🔒 Doble validación defensiva
        if (!$user->email_verified_at) {
            return;
        }

        app(PedidoMergeService::class)
            ->mergeByEmail($user->vEmail, $user->id_usuario);

        Log::info('Pedidos migrados tras verificación de email', [
            'user_id' => $user->id_usuario,
            'email' => $user->vEmail,
        ]);
    }
}
