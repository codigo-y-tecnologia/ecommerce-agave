<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DireccionGuest;
use Carbon\Carbon;

class CleanOldGuestAddresses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'guest:clean-addresses {--days=15}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina direcciones guest antiguas que ya no son válidas';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');

        $cutoffDate = Carbon::now()->subDays($days);

        $deleted = DireccionGuest::where('tFecha_registro', '<', $cutoffDate)
            ->delete();

        $this->info("Direcciones guest eliminadas: {$deleted}");

        return Command::SUCCESS;
    }
}
