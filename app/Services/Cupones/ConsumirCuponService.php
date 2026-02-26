<?php

namespace App\Services\Cupones;

use Illuminate\Support\Facades\DB;

class ConsumirCuponService
{
    public function ejecutar(string $sessionId, int $ventaId, ?int $userId, ?string $guestToken): void
    {
        DB::transaction(function () use ($sessionId, $ventaId, $userId, $guestToken) {

            $reserva = DB::table('tbl_cupon_reservas')
                ->where('session_id', $sessionId)
                ->lockForUpdate()
                ->first();

            if (!$reserva) {
                return;
            }

            DB::table('tbl_cupon_usos')->insert([
                'id_cupon' => $reserva->id_cupon,
                'id_venta' => $ventaId,
                'id_usuario' => $userId,
                'guest_token' => $guestToken,
            ]);

            DB::table('tbl_cupon_reservas')
                ->where('id_cupon_reserva', $reserva->id_cupon_reserva)
                ->delete();
        });
    }
}
