<?php

namespace App\Services\Cupones;

use App\Models\{Carrito, Cupon, CuponReserva};
use Illuminate\Support\Facades\DB;

class LiberarCuponService
{
    public function ejecutar(int $carritoId): void
    {
        DB::transaction(function () use ($carritoId) {

            // Buscar reserva activa del carrito con lock para evitar condiciones de carrera
            $reserva = CuponReserva::where('id_carrito', $carritoId)
                ->lockForUpdate()
                ->first();

            if (!$reserva) {
                return;
            }

            // Decrementar contador global del cupón (solo si aún existe el cupón)
            Cupon::where('id_cupon', $reserva->id_cupon)
                ->where('iUsos_actuales', '>', 0)
                ->decrement('iUsos_actuales');

            // Eliminar reserva
            $reserva->delete();
        });
    }

    /**
     * Limpia TODAS las reservas expiradas del sistema.
     * Ideal para scheduler/cron.
     */
    public function limpiarExpiradas(): int
    {
        return DB::transaction(function () {

            $reservas = CuponReserva::expiradas()->lockForUpdate()->get();

            $contador = 0;

            foreach ($reservas as $reserva) {

                Cupon::where('id_cupon', $reserva->id_cupon)
                    ->where('iUsos_actuales', '>', 0)
                    ->decrement('iUsos_actuales');

                $reserva->delete();

                $contador++;
            }

            return $contador;
        });
    }
}
