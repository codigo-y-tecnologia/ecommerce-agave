<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\StorePermissionRequest;

class SpatiePermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::all();
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
}
