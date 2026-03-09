<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Usuario;
use Spatie\Permission\Models\Role;

class MigrateUsersToSpatieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = Usuario::all();

        foreach ($users as $user) {

            // Si ya tiene rol Spatie, no hacer nada
            if ($user->roles()->exists()) {
                continue;
            }

            // Si tiene eRol válido, migrarlo
            if ($user->eRol) {
                $user->assignRole($user->eRol);
            }
        }
    }
}
