<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;

class UsuarioController extends Controller
{
    /**
     * Mostrar formulario de registro
     */
    public function create()
    {
        return view('registroUsuarios');
    }

    /**
     * Guardar nuevo usuario
     */
    public function store(Request $request)
    {
        // Validación
        $data = $request->validate([
            'vNombre' => 'required|string|max:60',
            'vApaterno' => 'required|string|max:50',
            'vAmaterno' => 'required|string|max:50',
            'vEmail' => 'required|email|unique:tbl_usuarios,vEmail',
            'vPassword' => 'required|min:6|confirmed',
            'dFecha_nacimiento' => 'required|date',
            'eRol' => 'in:cliente,admin'
        ]);

        // Insertar en la BD
        $usuario = Usuario::create([
            'vNombre' => $data['vNombre'],
            'vApaterno' => $data['vApaterno'],
            'vAmaterno' => $data['vAmaterno'] ?? null,
            'vEmail' => $data['vEmail'],
            'vPassword' => Hash::make($data['vPassword']),
            'dFecha_nacimiento' => $data['dFecha_nacimiento'],
            'eRol' => $data['eRol'] ?? 'cliente',
        ]);

        Auth::login($usuario); // inicia sesión automático después de registro

        return redirect('/');
    }
}
