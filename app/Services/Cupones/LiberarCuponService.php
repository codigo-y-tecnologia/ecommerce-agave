<?php

namespace App\Services\Cupones;

use App\Models\Cupon;
use Illuminate\Support\Facades\DB;

class LiberarCuponService
{
    public function ejecutar(int $carritoId): void
    {
        DB::transaction(function () use ($carritoId) {

            $reserva = DB::table('tbl_cupon_reservas')
                ->where('id_carrito', $carritoId)
                ->lockForUpdate()
                ->first();

            if (!$reserva) {
                return;
            }

            Cupon::where('id_cupon', $reserva->id_cupon)
                ->decrement('iUsos_actuales');

            DB::table('tbl_cupon_reservas')
                ->where('id_cupon_reserva', $reserva->id_cupon_reserva)
                ->delete();
        });
    }
}
