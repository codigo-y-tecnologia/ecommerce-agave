<?php

namespace App\Services\Stock;

use App\Models\StockReserva;

class LiberarReservaPorCarritoService
{
    public function ejecutar($carrito): void
    {
        StockReserva::where('id_carrito', $carrito->id_carrito)
            ->get()
            ->each(
                fn($reserva) =>
                app(LiberarReservaService::class)->ejecutar($reserva)
            );
    }
}
