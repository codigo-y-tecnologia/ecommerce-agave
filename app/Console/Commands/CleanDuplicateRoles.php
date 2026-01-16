<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class CleanDuplicateRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-duplicate-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $roles = Role::all()
            ->groupBy(fn($r) => strtolower(trim($r->name)));

        foreach ($roles as $group) {
            if ($group->count() > 1) {
                $keep = $group->shift();

                foreach ($group as $duplicate) {
                    DB::table('model_has_roles')
                        ->where('role_id', $duplicate->id)
                        ->update(['role_id' => $keep->id]);

                    $duplicate->delete();
                }
            }
        }

        $this->info('Roles duplicados eliminados correctamente');
    }
}
