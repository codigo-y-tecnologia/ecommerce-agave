<?php

namespace App\Services\Stock;

use App\Models\{Carrito, Producto, StockReserva, ProductoVariacion};
use Illuminate\Support\Facades\DB;
use Exception;

class ConsumirReservaService
{
    public function ejecutar(string $sessionId): void
    {
        DB::transaction(function () use ($sessionId) {

            // 🔒 Bloqueamos reservas (idempotencia)
            $reservas = StockReserva::where('session_id', $sessionId)
                ->lockForUpdate()
                ->get();

            if ($reservas->isEmpty()) {
                // Puede ser webhook o Paypal duplicado
                return;
            }

            foreach ($reservas as $reserva) {

                if ($reserva->id_variacion) {
                    // ── VARIACIÓN ──
                    $variacion = ProductoVariacion::lockForUpdate()
                        ->find($reserva->id_variacion);

                    if (!$variacion) {
                        throw new Exception("Variación no encontrada al consumir reserva (id: {$reserva->id_variacion})");
                    }

                    // El stock ya fue decrementado al reservar,
                    // aquí solo nos aseguramos de que no quede negativo
                    if ($variacion->iStock < 0) {
                        $variacion->iStock = 0;
                        $variacion->save();
                    }

                    // No hay iStock_reservado en variaciones, nada más que limpiar

                } else {

                    $producto = Producto::lockForUpdate()
                        ->find($reserva->id_producto);

                    if (! $producto) {
                        throw new Exception('Producto no encontrado');
                    }

                    // 🔽 Consumir definitivamente
                    $producto->iStock_reservado -= $reserva->cantidad;
                    $producto->iStock     -= $reserva->cantidad;
                    $producto->save();
                }
            }

            // 📦 Marcar carrito como convertido
            $carrito = Carrito::find($reservas->first()->id_carrito);
            if ($carrito) {
                $carrito->marcarComoConvertido();
            }

            // 🧹 Limpiar reservas
            StockReserva::where('session_id', $sessionId)->delete();
        });
    }
}
