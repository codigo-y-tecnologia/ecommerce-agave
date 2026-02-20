<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Carrito;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CleanAbandonedCarts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'carritos:limpiar-abandonados {--hours=48}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina carritos marcados como abandonados después de X horas';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $hours = (int) $this->option('hours');
        $limite = Carbon::now()->subHours($hours);

        DB::transaction(function () use ($limite) {

            $carritos = Carrito::where('eEstado', 'abandonado')
                ->where('tFecha_actualizacion', '<', $limite)
                ->get();

            foreach ($carritos as $carrito) {
                $carrito->detalles()->delete();
                $carrito->delete();
            }
        });

        $this->info('Carritos abandonados eliminados correctamente.');

        return Command::SUCCESS;
    }
}
