<?php

namespace App\Services;

use App\Models\Carrito;
use App\Models\CarritoDetalle;
use Illuminate\Support\Facades\DB;

class CarritoMergeService
{
    public function merge(string $guestToken, int $userId): void
    {
        DB::transaction(function () use ($guestToken, $userId) {

            // 1. Carrito invitado
            $guestCart = Carrito::where('vGuest_token', $guestToken)
                ->whereNull('id_usuario')
                ->where('eEstado', 'activo')
                ->with('detalles')
                ->first();

            if (!$guestCart || $guestCart->detalles->isEmpty()) {
                return;
            }

            // 2. Carrito activo del usuario (trigger maneja uActivo)
            $userCart = Carrito::firstOrCreate(
                [
                    'id_usuario' => $userId,
                    'eEstado'    => 'activo',
                ],
                [
                    'vGuest_token' => null,
                ]
            );

            // 3. Merge de productos
            foreach ($guestCart->detalles as $detalleGuest) {

                $detalleUser = CarritoDetalle::where('id_carrito', $userCart->id_carrito)
                    ->where('id_producto', $detalleGuest->id_producto)
                    ->first();

                if ($detalleUser) {
                    $detalleUser->iCantidad += $detalleGuest->iCantidad;
                    $detalleUser->save();
                } else {
                    CarritoDetalle::create([
                        'id_carrito'       => $userCart->id_carrito,
                        'id_producto'      => $detalleGuest->id_producto,
                        'iCantidad'        => $detalleGuest->iCantidad,
                        'dPrecio_unitario' => $detalleGuest->dPrecio_unitario,
                    ]);
                }
            }

            // 4. Marcar carrito invitado como abandonado
            $guestCart->update([
                'eEstado'       => 'abandonado',
                'vGuest_token'  => null,
            ]);

            // 5. Eliminar detalles invitados (opcional pero recomendado)
            CarritoDetalle::where('id_carrito', $guestCart->id_carrito)->delete();
        });
    }
}
