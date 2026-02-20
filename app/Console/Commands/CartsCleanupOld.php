<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Carrito;
use App\Support\CartExpiration;
use Carbon\Carbon;

class CartsCleanupOld extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'carts:cleanup-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina carritos de invitado sin actividad por más de 90 días';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $fecha = Carbon::now()->subDays(CartExpiration::ELIMINADO_DIAS);

        $carritos = Carrito::whereNull('id_usuario')
            ->where('eEstado', 'activo')
            ->where('tFecha_actualizacion', '<=', $fecha)
            ->get();

        foreach ($carritos as $carrito) {
            $carrito->detalles()->delete();
            $carrito->delete();
        }

        $this->info("Carritos eliminados definitivamente: {$carritos->count()}");

        return Command::SUCCESS;
    }
}
