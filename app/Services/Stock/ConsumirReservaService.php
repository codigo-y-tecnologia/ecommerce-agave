<?php

namespace App\Services\Stock;

use App\Models\{Carrito, Producto, StockReserva};
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

            // 📦 Marcar carrito como convertido
            $carrito = Carrito::find($reservas->first()->id_carrito);
            if ($carrito) {
                $carrito->marcarComoConvertido();
            }
        });
    }
}
