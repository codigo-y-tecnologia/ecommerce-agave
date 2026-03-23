<?php

namespace App\Services\Stock;

use App\Models\{Carrito, Producto, StockReserva, ProductoVariacion};
use Illuminate\Support\Facades\DB;

class LiberarReservaService
{
    public function ejecutar(StockReserva $reserva): void
    {
        DB::transaction(function () use ($reserva) {

            if ($reserva->id_variacion) {
                // ── VARIACIÓN ──
                // Al reservar se hizo iStock -= cantidad directamente,
                // entonces al liberar hay que devolver ese stock
                $variacion = ProductoVariacion::lockForUpdate()
                    ->find($reserva->id_variacion);

                if ($variacion) {
                    $variacion->iStock += $reserva->cantidad;
                    $variacion->save();
                }
            } else {

                $producto = Producto::lockForUpdate()
                    ->find($reserva->id_producto);

                if ($producto) {
                    $producto->iStock_reservado -= $reserva->cantidad;
                    $producto->save();
                }
            }

            $carrito = Carrito::find($reserva->id_carrito);
            if ($carrito) {
                $carrito->marcarComoActivo();
            }

            $reserva->delete();
        });
    }
}
