<?php

namespace App\Jobs;

use App\Models\StockReserva;
use App\Services\Stock\LiberarReservaService;

class LimpiarReservasExpiradas
{
    public function handle(LiberarReservaService $liberador)
    {
        StockReserva::expiradas()
            ->pendientes()
            ->each(function ($reserva) use ($liberador) {
                $liberador->ejecutar($reserva);
            });
    }
}
