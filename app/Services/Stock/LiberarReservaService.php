<?php

namespace App\Services\Stock;

use App\Models\{Carrito, Producto, StockReserva};
use Illuminate\Support\Facades\DB;

class LiberarReservaService
{
    public function ejecutar(StockReserva $reserva): void
    {
        DB::transaction(function () use ($reserva) {

            $producto = Producto::lockForUpdate()
                ->find($reserva->id_producto);

            if ($producto) {
                $producto->iStock_reservado -= $reserva->cantidad;
                $producto->save();
            }

            $carrito = Carrito::find($reserva->first()->id_carrito);
            if ($carrito) {
                $carrito->marcarComoActivo();
            }

            $reserva->delete();
        });
    }
}
