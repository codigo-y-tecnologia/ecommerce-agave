<?php

namespace App\Services\Stock;

use App\Models\{Producto, StockReserva};
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

            $reserva->delete();
        });
    }
}
