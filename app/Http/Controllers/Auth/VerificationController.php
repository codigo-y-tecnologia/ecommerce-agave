<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Services\System\SecurityLoggerService;

class VerificationController extends Controller
{
    public function verify($token)
    {
        $usuario = Usuario::where('verification_token', $token)->first();

        if (!$usuario) {

            // Registrar intento inválido
            SecurityLoggerService::emailVerificationFailed($token);

            return redirect()->route('login')->with('error', 'Enlace inválido o expirado.');
        }

        if ($usuario->email_verified_at) {
            return redirect()->route('login')
                ->with('info', 'Este correo ya fue verificado anteriormente.');
        }

        $usuario->email_verified_at = now();
        $usuario->verification_token = null;
        $usuario->save();

        // Registrar verificación exitosa
        SecurityLoggerService::emailVerified(
            $usuario->id_usuario,
            $usuario->vEmail
        );

        return redirect()->route('login')->with('success', 'Correo verificado correctamente. Ahora puedes iniciar sesión.');
    }
}
