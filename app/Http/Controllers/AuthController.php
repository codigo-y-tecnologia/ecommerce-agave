<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Mostrar formulario de login
    public function showLoginForm()
    {
        // Verificar si ya está autenticado
        if (Auth::check()) {
            return redirect()->route('inicio.real');
        }
        
        return view('auth.login'); // Busca en resources/views/auth/login.blade.php
    }

    // Procesar login - SIN encriptación
    public function login(Request $request)
    {
        // Validación
        $request->validate([
            'vEmail' => 'required|email',
            'vPassword' => 'required|string'
        ]);

        // Buscar usuario por email
        $usuario = Usuario::where('vEmail', $request->vEmail)->first();

        // Verificar contraseña SIN encriptación
        if ($usuario && $usuario->vPassword === $request->vPassword) {
            // Iniciar sesión
            Auth::login($usuario, $request->filled('remember'));
            
            // Redirigir según la URL de destino o a inicio
            return redirect()->intended(route('inicio.real'));
        }

        // Si falla la autenticación
        return back()->withErrors([
            'vEmail' => 'Las credenciales no coinciden con nuestros registros.',
        ])->onlyInput('vEmail');
    }

    // Cerrar sesión
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('inicio.real');
    }

    // Mostrar formulario de registro
    public function showRegister()
    {
        return view('auth.registroUsuarios');
    }

    // Procesar registro - SIN encriptación
    public function register(Request $request)
    {
        // Validación
        $validator = Validator::make($request->all(), [
            'vNombre' => 'required|string|max:60',
            'vApaterno' => 'required|string|max:50',
            'vAmaterno' => 'required|string|max:50',
            'vEmail' => 'required|email|unique:tbl_usuarios,vEmail',
            'vPassword' => 'required|min:6|confirmed',
            'dFecha_nacimiento' => 'required|date',
            'eRol' => 'in:cliente,admin'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Crear usuario - SIN encriptación
        $usuario = Usuario::create([
            'vNombre' => $request->vNombre,
            'vApaterno' => $request->vApaterno,
            'vAmaterno' => $request->vAmaterno,
            'vEmail' => $request->vEmail,
            'vPassword' => $request->vPassword, // ✅ SIN encriptación
            'dFecha_nacimiento' => $request->dFecha_nacimiento,
            'eRol' => $request->eRol ?? 'cliente',
        ]);

        // Iniciar sesión automáticamente
        Auth::login($usuario);
        
        // Redirigir al inicio
        return redirect()->route('inicio.real')->with('success', '¡Cuenta creada exitosamente!');
    }
}