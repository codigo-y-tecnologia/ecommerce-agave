<?php

namespace App\Services\Cupones;

use App\Models\{Carrito, Cupon, CuponUso, CuponReserva};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Exceptions\StockException;
use Carbon\Carbon;

class ReservarCuponService
{
    public function ejecutar(Carrito $carrito, ?string $codigoCupon): ?Cupon
    {
        if (!$codigoCupon) {
            return null;
        }

        return DB::transaction(function () use ($carrito, $codigoCupon) {

            // Limpiar reservas expiradas
            CuponReserva::expiradas()->delete();

            $cupon = Cupon::disponible()
                ->whereRaw('BINARY vCodigo_cupon = ?', [$codigoCupon])
                ->lockForUpdate()
                ->first();

            if (!$cupon) {
                throw new StockException('Cupón inválido o expirado');
            }

            /**
             * Verificar si el carrito ya tiene una reserva previa
             *    (evita duplicar usos si el usuario refresca checkout)
             */
            $reservaExistente = CuponReserva::where('id_carrito', $carrito->id_carrito)
                ->lockForUpdate()
                ->first();

            if ($reservaExistente) {
                // Si es el mismo cupón y sigue activa → reutilizar
                if (
                    $reservaExistente->id_cupon === $cupon->id_cupon &&
                    $reservaExistente->estaActiva()
                ) {
                    return $cupon;
                }

                // Si es otro cupón → liberar uso anterior
                Cupon::where('id_cupon', $reservaExistente->id_cupon)
                    ->decrement('iUsos_actuales');

                $reservaExistente->delete();
            }

            // Validar límite global de usos (ATÓMICO)
            if (
                !is_null($cupon->iUso_maximo) &&
                $cupon->iUsos_actuales >= $cupon->iUso_maximo
            ) {
                throw new StockException('Este cupón ya no tiene usos disponibles.');
            }

            // Validar límite por usuario
            if ($cupon->iUsos_por_usuario) {

                $usosUsuario = CuponUso::where('id_cupon', $cupon->id_cupon)
                    ->when(Auth::check(), function ($q) {
                        $q->where('id_usuario', Auth::id());
                    }, function ($q) {
                        $q->where('guest_token', session('guest_token'));
                    })
                    ->count();

                if ($usosUsuario >= $cupon->iUsos_por_usuario) {
                    throw new StockException('Ya alcanzaste el límite de uso de este cupón.');
                }
            }

            // Incremento atómico del contador global

            $updated = Cupon::where('id_cupon', $cupon->id_cupon)
                ->where(function ($q) use ($cupon) {
                    if (!is_null($cupon->iUso_maximo)) {
                        $q->whereColumn('iUsos_actuales', '<', 'iUso_maximo');
                    }
                })
                ->increment('iUsos_actuales');

            if ($updated === 0) {
                throw new StockException('El cupón se agotó mientras intentabas usarlo.');
            }

            CuponReserva::create([
                'id_cupon'   => $cupon->id_cupon,
                'id_carrito' => $carrito->id_carrito,
                'session_id' => null,
                'expires_at' => Carbon::now()->addMinutes(
                    config('stock.reserva_minutos', 30)
                ),
            ]);

            return $cupon;
        });
    }
}
