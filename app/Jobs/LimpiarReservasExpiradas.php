<?php

namespace App\Jobs;

use App\Models\StockReserva;
use App\Services\Stock\LiberarReservaService;

class LimpiarReservasExpiradas
{
    public function handle(LiberarReservaService $liberador)
    {

        logger()->info('🧹 Limpiando reservas expiradas');

        StockReserva::expiradas()
            ->each(function ($reserva) use ($liberador) {
                $liberador->ejecutar($reserva);
            });
    }
}
