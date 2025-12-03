<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Donde redirigir después del login
     */
    protected $redirectTo = '/';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Sobrescribir el método de autenticación para NO usar encriptación
     */
    protected function attemptLogin(Request $request)
    {
        // Buscar usuario por email
        $usuario = Usuario::where('vEmail', $request->vEmail)->first();

        // Verificar contraseña SIN encriptación (comparación directa)
        if ($usuario && $usuario->vPassword === $request->vPassword) {
            // Login manual
            Auth::login($usuario, $request->filled('remember'));
            return true;
        }

        return false;
    }

    /**
     * Campo de email personalizado (usamos vEmail en lugar de email)
     */
    public function username()
    {
        return 'vEmail';
    }

    /**
     * Mostrar formulario de login personalizado
     */
    public function showLoginForm()
    {
        return view('login');
    }

    /**
     * Validación personalizada
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            'vEmail' => 'required|string',
            'vPassword' => 'required|string',
        ]);
    }

    /**
     * Credenciales personalizadas
     */
    protected function credentials(Request $request)
    {
        return $request->only('vEmail', 'vPassword');
    }
}