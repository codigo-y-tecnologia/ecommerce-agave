<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\Usuario;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Services\System\SecurityLoggerService;

class UsuarioRolController extends Controller
{

    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('asignar_roles');

        $usuarios = Usuario::orderBy('vNombre')->paginate(10);

        return view(
            'superadmin.usuarios.index',
            compact('usuarios')
        );
    }

    public function edit(Usuario $usuario)
    {
        $this->authorize('asignar_roles');

        $roles = Role::orderBy('name')->get();

        return view(
            'superadmin.usuarios.roles',
            compact('usuario', 'roles')
        );
    }

    public function update(Request $request, Usuario $usuario)
    {
        $this->authorize('asignar_roles');

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

        // 📌 Obtener rol anterior
        $oldRole = $usuario->roles->pluck('name')->first();

        // 🧹 Quitar roles anteriores y asignar el nuevo
        $usuario->syncRoles([$request->role]);

        // 🛡 Registrar cambio de rol
        SecurityLoggerService::roleChanged(
            $usuario->vNombre,
            $oldRole ?? 'none',
            $request->role
        );

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Rol asignado correctamente.');
    }
}
