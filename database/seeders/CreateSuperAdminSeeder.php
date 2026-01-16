<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class CreateSuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = 'superadmin@ecommerce-agave.com';

        $user = Usuario::firstOrCreate(
            ['vEmail' => $email],
            [
                'vNombre' => 'Super',
                'vApaterno' => 'Admin',
                'vAmaterno' => 'superadministrador',
                'vPassword' => Hash::make('SuperAdmin@2026'),
                'dFecha_nacimiento' => '1990-01-01',
                'is_verified' => true,
            ]
        );

        if (! $user->hasRole('superadmin')) {
            $user->assignRole('superadmin');
        }
    }
}
