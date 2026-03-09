<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Carrito;
use App\Jobs\SendCartReminderJob;

class NotifyActiveCarts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'carts:notify-active';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía recordatorios por etapas a carritos activos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $stages = config('cart_reminders.stages');

        foreach ($stages as $stage => $config) {

            $threshold = now()->subHours($config['hours']);

            $carritos = Carrito::where('eEstado', 'activo')
                ->where('tFecha_actualizacion', '<=', $threshold)
                ->whereDoesntHave('reminders', function ($q) use ($stage) {
                    $q->where('etapa', $stage);
                })
                ->get();

            foreach ($carritos as $carrito) {
                SendCartReminderJob::dispatch(
                    $carrito,
                    $stage,
                    $config['type']
                );
            }
        }
    }
}
