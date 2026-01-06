<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Usuario;

class SyncUserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Usuario::all()->each(function ($user) {
            if ($user->eRol) {
                $user->syncRoles([$user->eRol]);
            }
        });
    }
}
