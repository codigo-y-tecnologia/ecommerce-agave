<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class CleanDuplicatePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:clean-duplicates';


    /**
     * The console command description.
     *
     * @var string
     */

    protected $description = 'Elimina permisos duplicados (case-insensitive)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Buscando permisos duplicados...');

        $permissions = Permission::all();

        $grouped = $permissions->groupBy(function ($permission) {
            return strtolower(trim($permission->name));
        });

        $deleted = 0;

        foreach ($grouped as $normalized => $group) {
            if ($group->count() > 1) {
                $keep = $group->shift(); // nos quedamos con uno

                foreach ($group as $duplicate) {
                    // transferir relaciones
                    DB::table('role_has_permissions')
                        ->where('permission_id', $duplicate->id)
                        ->update(['permission_id' => $keep->id]);

                    $duplicate->delete();
                    $deleted++;
                }
            }
        }

        $this->info("✅ Permisos duplicados eliminados: {$deleted}");

        return Command::SUCCESS;
    }
}
