<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Clientes
            'comprar_productos',
            'ver_pedidos_propios',

            // Admin - operación
            'ver_pedidos',
            'gestionar_productos',
            'ver_clientes',
            'procesar_reembolsos',
            'ver_reportes',

            // Superadmin - sistema
            'configurar_sistema',
            'gestionar_administradores',
            'ver_logs_seguridad',
            'gestionar_permisos',
            'ver_monitoreo',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $cliente = Role::firstOrCreate(['name' => 'cliente']);
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $superadmin = Role::firstOrCreate(['name' => 'superadmin']);

        $cliente->syncPermissions([
            'comprar_productos',
            'ver_pedidos_propios',
        ]);

        $admin->syncPermissions([
            'ver_pedidos',
            'gestionar_productos',
            'ver_clientes',
            'procesar_reembolsos',
            'ver_reportes',
        ]);

        $superadmin->syncPermissions(Permission::all());
    }
}
