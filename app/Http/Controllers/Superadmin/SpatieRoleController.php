<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use Illuminate\Support\Facades\DB;

class SpatieRoleController extends Controller
{
    public function index(Request $request)
    {
        $roles = Role::withCount('users')
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })
            ->paginate(10);

        return view('superadmin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('superadmin.roles.create', compact('permissions'));
    }

    public function store(StoreRoleRequest $request)
    {
        $role = Role::create(['name' => strtolower(trim($request->name))]);
        $role->syncPermissions($request->permissions ?? []);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Rol creado correctamente.');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();

        return view('superadmin.roles.edit', compact('role', 'permissions'));
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        $role->update([
            'name' => $request->name
        ]);

        $role->syncPermissions($request->permissions ?? []);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Rol actualizado correctamente.');
    }

    public function destroy(Role $role)
    {
        // 1. Proteger superadmin
        if ($role->name === 'superadmin') {
            return back()->with('error', 'No se puede eliminar el rol superadmin.');
        }

        // 2. Verificar si está asignado a usuarios
        $usersCount = DB::table('model_has_roles')
            ->where('role_id', $role->id)
            ->count();

        if ($usersCount > 0) {
            return back()->with(
                'error',
                'No se puede eliminar este rol porque está asignado a usuarios.'
            );
        }

        // 3. Quitar permisos y eliminar
        $role->syncPermissions([]);
        $role->delete();

        return redirect()
            ->route('roles.index')
            ->with('success', 'Rol eliminado correctamente.');
    }
}
