<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;

class VerificationController extends Controller
{
    public function verify($token)
    {
        $usuario = Usuario::where('verification_token', $token)->first();

        if (!$usuario) {
            return redirect()->route('login')->with('error', 'Enlace inválido o expirado.');
        }

        $usuario->email_verified_at = now();
        $usuario->verification_token = null;
        $usuario->save();

        return redirect()->route('login')->with('success', 'Correo verificado correctamente. Ahora puedes iniciar sesión.');
    }
}
