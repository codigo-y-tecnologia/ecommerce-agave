<?php

namespace App\Services\Cupones;

use App\Models\{Carrito, Cupon};
use Illuminate\Support\Facades\DB;
use App\Exceptions\StockException;

class ReservarCuponService
{
    public function ejecutar(Carrito $carrito, ?string $codigoCupon): ?Cupon
    {
        if (!$codigoCupon) {
            return null;
        }

        return DB::transaction(function () use ($carrito, $codigoCupon) {

            // Limpiar reservas expiradas
            DB::table('tbl_cupon_reservas')
                ->where('expires_at', '<', now())
                ->delete();

            $cupon = Cupon::whereRaw('BINARY vCodigo_cupon = ?', [$codigoCupon])
                ->where('bActivo', 1)
                ->whereDate('dValido_desde', '<=', now())
                ->whereDate('dValido_hasta', '>=', now())
                ->lockForUpdate()
                ->first();

            if (!$cupon) {
                throw new StockException('Cupón inválido o expirado');
            }

            // Intento atómico de uso
            $updated = Cupon::where('id_cupon', $cupon->id_cupon)
                ->whereColumn('iUsos_actuales', '<', 'iUso_maximo')
                ->increment('iUsos_actuales');

            if ($updated === 0) {
                throw new StockException('El cupón ya no tiene usos disponibles');
            }

            DB::table('tbl_cupon_reservas')->insert([
                'id_cupon' => $cupon->id_cupon,
                'id_carrito' => $carrito->id_carrito,
                'session_id' => null,
                'expires_at' => now()->addMinutes(config('stock.reserva_minutos', 30)),
            ]);

            return $cupon;
        });
    }
}
