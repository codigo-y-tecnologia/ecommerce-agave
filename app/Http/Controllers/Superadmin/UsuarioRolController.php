<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\Usuario;

class UsuarioRolController extends Controller
{
    public function edit(Usuario $usuario)
    {
        $roles = Role::orderBy('name')->get();

        return view(
            'superadmin.usuarios.roles',
            compact('usuario', 'roles')
        );
    }

    public function update(Request $request, Usuario $usuario)
    {
        // 🔒 Proteger superadmin
        if ($usuario->hasRole('superadmin')) {
            return back()->with(
                'error',
                'No se puede modificar el rol de un superadmin.'
            );
        }

        $request->validate([
            'role' => 'required|exists:roles,name',
        ]);

        // 🧹 Quitar roles anteriores y asignar el nuevo
        $usuario->syncRoles([$request->role]);

        // (Opcional) mantener eRol sincronizado
        $usuario->update([
            'eRol' => $request->role
        ]);

        return redirect()
            ->route('roles.permisos')
            ->with('success', 'Rol asignado correctamente.');
    }
}
