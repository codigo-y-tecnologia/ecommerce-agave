<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;

class UsuarioController extends Controller
{
    /**
     * Mostrar formulario de registro
     */
    public function create()
    {
        return view('auth.registroUsuarios');
    }

    /**
     * Guardar nuevo usuario - SIN ENCRIPTAR CONTRASEÑA
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

        // Insertar en la BD - SIN Hash::make()
        $usuario = Usuario::create([
            'vNombre' => $data['vNombre'],
            'vApaterno' => $data['vApaterno'],
            'vAmaterno' => $data['vAmaterno'],
            'vEmail' => $data['vEmail'],
            'vPassword' => $data['vPassword'], // SIN encriptar
            'dFecha_nacimiento' => $data['dFecha_nacimiento'],
            'eRol' => $data['eRol'] ?? 'cliente',
        ]);

        // Iniciar sesión automáticamente
        Auth::login($usuario);
        
        return redirect()->route('inicio.real')->with('success', '¡Cuenta creada exitosamente!');
    }
}