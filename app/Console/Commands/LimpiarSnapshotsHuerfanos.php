<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LimpiarSnapshotsHuerfanos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'snapshots:cleanup-orphaned';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina snapshots huérfanos antiguos';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🧹 Limpiando snapshots huérfanos...');

        $fechaLimite = now()->subDays(7);

        DB::beginTransaction();

        try {

            $snapshots = DB::table('tbl_checkout_snapshots as s')
                ->where('s.created_at', '<', $fechaLimite)
                ->whereNotExists(function ($q) {
                    $q->select(DB::raw(1))
                        ->from('tbl_pedidos as p')
                        ->whereColumn('p.id_checkout_snapshot', 's.id');
                })
                ->lockForUpdate()
                ->get();

            if ($snapshots->isEmpty()) {
                $this->info('✅ No hay snapshots huérfanos.');
                DB::commit();
                return Command::SUCCESS;
            }

            foreach ($snapshots as $snapshot) {

                DB::table('tbl_checkout_snapshots')
                    ->where('id', $snapshot->id)
                    ->delete();

                $this->line("🗑 Snapshot {$snapshot->id} eliminado.");
            }

            DB::commit();
            $this->info('🎯 Limpieza completada.');

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('❌ Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
