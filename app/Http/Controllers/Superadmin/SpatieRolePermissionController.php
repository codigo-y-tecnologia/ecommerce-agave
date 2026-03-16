<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Services\System\SecurityLoggerService;

class SpatieRolePermissionController extends Controller
{
    public function edit(Role $role)
    {
        $permissions = Permission::all();
        return view('superadmin.asignar-permisos-a-roles.permissions', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        // Protegemos el superadmin
        if ($role->name === 'superadmin') {
            return back()->with('error', 'El rol superadmin siempre tiene todos los permisos');
        }

        $role->syncPermissions($request->permissions ?? []);

        return back()->with('success', 'Permisos actualizados correctamente');
    }
}
