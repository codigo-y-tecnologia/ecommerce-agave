<?php

namespace App\Services\Stock;

use App\Models\{Carrito, Producto, StockReserva, ProductoVariacion};
use Illuminate\Support\Facades\DB;
use Exception;
use App\Exceptions\StockException;

class ReservarStockService
{
    public function ejecutar(Carrito $carrito): void
    {

        // 🚫 Evitar doble reserva del mismo carrito
        if ($carrito->eEstado !== 'activo') {
            throw new StockException('El carrito ya fue procesado');
        }

        foreach ($carrito->detalles as $detalle) {

            if ($detalle->id_variacion) {
                // ── VARIACIÓN ──
                $variacion = ProductoVariacion::where('id_variacion', $detalle->id_variacion)
                    ->lockForUpdate()
                    ->first();

                if (!$variacion) {
                    throw new StockException('El producto ya no está disponible.');
                }

                if ($variacion->iStock < $detalle->iCantidad) {
                    throw new StockException(
                        "Stock insuficiente para el producto solicitado."
                    );
                }

                $variacion->iStock -= $detalle->iCantidad;
                $variacion->save();

                StockReserva::create([
                    'id_producto'  => $detalle->id_producto,
                    'id_variacion' => $variacion->id_variacion,
                    'id_carrito'   => $carrito->id_carrito,
                    'cantidad'     => $detalle->iCantidad,
                    'expires_at'   => now()->addMinutes(config('stock.reserva_minutos', 30)),
                ]);
            } else {

                // 🔒 BLOQUEO DE FILA (ESTO ES LA CLAVE)
                $producto = Producto::where('id_producto', $detalle->id_producto)
                    ->lockForUpdate()
                    ->first();

                if (! $producto) {
                    throw new StockException('El producto ya no está disponible');
                }

                // 🧮 Stock real disponible
                if (! $producto->tieneStock($detalle->iCantidad)) {
                    throw new StockException(
                        "Stock insuficiente para {$producto->vNombre}"
                    );
                }

                // 🔐 RESERVAR STOCK
                $producto->iStock_reservado += $detalle->iCantidad;
                $producto->save();

                // 🧾 REGISTRAR LA RESERVA
                StockReserva::create([
                    'id_producto' => $producto->id_producto,
                    'id_variacion' => null,
                    'id_carrito'  => $carrito->id_carrito,
                    'cantidad'    => $detalle->iCantidad,
                    'expires_at'  => now()->addMinutes(
                        config('stock.reserva_minutos', 30)
                    ),
                ]);
            }
        }

        // 📌 Marcar carrito como reservado
        $carrito->marcarComoReservado();
    }
}
