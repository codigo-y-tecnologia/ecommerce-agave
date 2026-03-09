<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\InputSanitizer;

class RegistrarAdminController extends Controller
{
    use InputSanitizer;

    public function showRegistrarAdmin()
    {
        return view('superadmin.registrar-admin');
    }

    public function registrarAdmin(Request $request)
    {
        // Validación
        $data = $request->validate([
            'vNombre' => ['required', 'string', 'max:60', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/u'],
            'vApaterno' => ['required', 'string', 'max:50', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/u'],
            'vAmaterno' => ['required', 'string', 'max:50', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/u'],
            'vEmail' => ['required', 'email', 'max:100', 'unique:tbl_usuarios,vEmail'],
            'vPassword' => ['required', 'string', 'min:8', 'max:150', 'confirmed'],
            'dFecha_nacimiento' => ['required', 'date'],
        ], [
            // Mensajes personalizados claros
            'regex' => 'El campo :attribute solo puede contener letras y espacios.',
            'vEmail.email' => 'El correo electrónico debe tener un formato válido.',
            'unique' => 'El correo electrónico ya está en uso por otro usuario.',
            'confirmed' => 'Las contraseñas no coinciden.',
            'vPassword.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        // Lógica para crear el usuario admin
        // ...

        return redirect()->route('dashboard.superadmin')->with('success', 'Administrador registrado exitosamente.');
    }
}
