<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;

class SpatiePermissionController extends Controller
{
    public function index(Request $request)
    {
        $query = Permission::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $permissions = $query
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('superadmin.permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('superadmin.permissions.create');
    }

    public function store(StorePermissionRequest $request)
    {

        Permission::create([
            'name' => strtolower(trim($request->name)),
            'guard_name' => 'web'
        ]);

        return redirect()
            ->route('permissions.index')
            ->with('success', 'Permiso creado correctamente');
    }

    public function edit(Permission $permission)
    {
        return view('superadmin.permissions.edit', compact('permission'));
    }

    public function update(UpdatePermissionRequest $request, Permission $permission)
    {
        $permission->update([
            'name' => strtolower(trim($request->name))
        ]);

        return redirect()
            ->route('permissions.index')
            ->with('success', 'Permiso actualizado correctamente');
    }

    public function destroy(Permission $permission)
    {
        if (in_array($permission->name, [
            'configurar_sistema',
            'gestionar_permisos',
            'gestionar_sistema'
        ])) {
            return back()->with('error', 'Este permiso es crítico y no puede eliminarse');
        }

        $permission->delete();

        return back()->with('success', 'Permiso eliminado correctamente');
    }
}
