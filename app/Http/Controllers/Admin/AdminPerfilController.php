<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminPerfilController extends Controller
{
    /**
     * Vista principal del perfil
     */
    public function index()
    {
        return view('admin.perfil.index');
    }

    /**
     * Actualizar datos personales
     */
    public function updateDatos(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'vNombre'    => 'required|string|max:60',
            'vApaterno'  => 'required|string|max:50',
            'vAmaterno'  => 'required|string|max:50',
            'vEmail'     => 'required|email|max:100|unique:tbl_usuarios,vEmail,' . $user->id_usuario . ',id_usuario',
        ]);

        $user->update($data);

        return back()->with('success', 'Datos actualizados correctamente.');
    }

    /**
     * Cambiar contraseña
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:10'],
        ]);

        $user = Auth::user();

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Cierra otras sesiones
        Auth::logoutOtherDevices($request->password);

        return back()->with('success', 'Contraseña actualizada correctamente.');
    }

    /**
     * Cerrar sesiones activas
     */
    public function logoutOtherDevices(Request $request)
    {
        $request->validate([
            'password' => ['required'],
        ]);

        Auth::logoutOtherDevices($request->password);

        return back()->with('success', 'Sesiones cerradas.');
    }
}
