<?php

namespace App\Services\Stock;

use App\Models\{Carrito, Producto, StockReserva};
use Illuminate\Support\Facades\DB;
use Exception;

class ReservarStockService
{
    public function ejecutar(Carrito $carrito): void
    {
        DB::transaction(function () use ($carrito) {

            // 🚫 Evitar doble reserva del mismo carrito
            if ($carrito->eEstado !== 'activo') {
                throw new Exception('El carrito ya fue procesado');
            }

            foreach ($carrito->detalles as $detalle) {

                // 🔒 BLOQUEO DE FILA (ESTO ES LA CLAVE)
                $producto = Producto::where('id_producto', $detalle->id_producto)
                    ->lockForUpdate()
                    ->first();

                if (! $producto) {
                    throw new Exception('Producto no encontrado');
                }

                // 🧮 Stock real disponible
                if (! $producto->tieneStock($detalle->iCantidad)) {
                    throw new Exception(
                        "Stock insuficiente para {$producto->vNombre}"
                    );
                }

                // 🔐 RESERVAR STOCK
                $producto->iStock_reservado += $detalle->iCantidad;
                $producto->save();

                // 🧾 REGISTRAR LA RESERVA
                StockReserva::create([
                    'id_producto' => $producto->id_producto,
                    'id_carrito'  => $carrito->id_carrito,
                    'cantidad'    => $detalle->iCantidad,
                    'expires_at'  => now()->addMinutes(
                        config('stock.reserva_minutos', 15)
                    ),
                ]);
            }

            // 📌 Marcar carrito como reservado
            $carrito->marcarComoReservado();
        });
    }
}
