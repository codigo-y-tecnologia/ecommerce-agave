<?php

namespace App\Services;

use App\Models\{Pedido, CuponUso};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PedidoMergeService
{
    /**
     * Migrar pedidos por guest token (login inmediato)
     */
    public function mergeByGuestToken(string $guestToken, int $userId): void
    {
        DB::transaction(function () use ($guestToken, $userId) {

            $pedidos = Pedido::whereNull('id_usuario')
                ->where('vGuest_token', $guestToken)
                ->get();

            if ($pedidos->isEmpty()) {
                return;
            }

            $pedidoIds = $pedidos->pluck('id_pedido')->toArray();

            foreach ($pedidos as $pedido) {
                $pedido->update([
                    'id_usuario'   => $userId,
                    'vGuest_token' => null,
                ]);
            }

            CuponUso::whereIn('id_venta', $pedidoIds)
                ->whereNull('id_usuario')
                ->update([
                    'id_usuario' => $userId,
                    'guest_token' => null,
                ]);

            Log::info('Pedidos migrados por guest_token', [
                'user_id' => $userId,
                'cantidad' => $pedidos->count(),
            ]);
        });
    }

    /**
     * Migrar pedidos históricos por email (registro manual posterior)
     */
    public function mergeByEmail(string $email, int $userId): void
    {
        DB::transaction(function () use ($email, $userId) {

            $pedidos = Pedido::whereNull('id_usuario')
                ->where('vEmail', $email)
                ->lockForUpdate()
                ->get();

            if ($pedidos->isEmpty()) {
                return;
            }

            $pedidoIds = $pedidos->pluck('id_pedido')->toArray();

            foreach ($pedidos as $pedido) {
                $pedido->update([
                    'id_usuario'   => $userId,
                    'vGuest_token' => null,
                ]);
            }

            CuponUso::whereIn('id_venta', $pedidoIds)
                ->whereNull('id_usuario')
                ->update([
                    'id_usuario' => $userId,
                    'guest_token' => null,
                ]);

            Log::info('Pedidos históricos migrados por email', [
                'user_id' => $userId,
                'email' => $email,
                'cantidad_pedidos' => count($pedidoIds),
            ]);
        });
    }
}
