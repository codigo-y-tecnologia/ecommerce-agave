<?php

namespace App\Services\Cupones;

use Illuminate\Support\Facades\DB;
use App\Models\{CuponReserva, CuponUso};

class ConsumirCuponService
{
    public function ejecutar(string $sessionId, int $ventaId, ?int $userId, ?string $guestToken): void
    {
        DB::transaction(function () use ($sessionId, $ventaId, $userId, $guestToken) {

            // Obtener reserva activa con lock
            $reserva = CuponReserva::where('session_id', $sessionId)
                ->lockForUpdate()
                ->first();

            if (!$reserva || !$reserva->estaActiva()) {
                return;
            }

            // Registrar uso definitivo (idempotente)
            CuponUso::firstOrCreate(
                [
                    'id_cupon' => $reserva->id_cupon,
                    'id_venta' => $ventaId,
                ],
                [
                    'id_usuario' => $userId,
                    'guest_token' => $guestToken,
                ]
            );

            // Eliminar reserva temporal para liberar el cupón
            $reserva->delete();
        });
    }
}
