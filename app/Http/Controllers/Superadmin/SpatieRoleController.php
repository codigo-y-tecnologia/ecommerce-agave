<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\StoreRoleRequest;

class SpatieRoleController extends Controller
{
    public function index(Request $request)
    {
        $roles = Role::when($request->search, function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%');
        })->paginate(10);

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

    public function destroy(Role $role)
    {
        if ($role->name === 'superadmin') {
            return back()->with('error', 'Este rol no puede eliminarse.');
        }

        $role->delete();

        return back()->with('success', 'Rol eliminado correctamente.');
    }
}
