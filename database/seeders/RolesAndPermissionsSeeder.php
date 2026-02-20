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
            'ver_carrito',
            'comprar_productos',
            'ver_pedidos_propios',
            'ver_perfil',
            'gestionar_direcciones',
            'gestionar_perfil',
            'ver_favoritos',

            // Admin - operación
            'ver_pedidos',
            'gestionar_productos',
            'ver_clientes',
            'procesar_reembolsos',
            'ver_reportes',
            'gestionar_tienda',
            'gestionar_clientes',
            'mi_perfil_admin',
            'gestionar_cupones',
            'gestionar_impuestos',

            // Superadmin - sistema
            'configurar_sistema',
            'gestionar_administradores',
            'ver_logs_seguridad',
            'gestionar_permisos',
            'ver_monitoreo',
            'gestionar_sistema',
            'mi_perfil_superadmin',
            'gestionar_roles',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $cliente = Role::firstOrCreate(['name' => 'cliente']);
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $superadmin = Role::firstOrCreate(['name' => 'superadmin']);

        $cliente->syncPermissions([
            'ver_carrito',
            'comprar_productos',
            'ver_pedidos_propios',
            'ver_perfil',
            'gestionar_direcciones',
            'gestionar_perfil',
            'ver_favoritos',
        ]);

        $admin->syncPermissions([
            'ver_pedidos',
            'gestionar_productos',
            'ver_clientes',
            'procesar_reembolsos',
            'ver_reportes',
            'gestionar_tienda',
            'gestionar_clientes',
            'mi_perfil_admin',
            'gestionar_cupones',
            'gestionar_impuestos',
        ]);

        $superadmin->syncPermissions(Permission::all());
    }
}
