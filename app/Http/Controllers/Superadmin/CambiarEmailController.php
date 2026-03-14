<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Services\System\SecurityLoggerService;

class CambiarEmailController extends Controller
{
    public function verify(string $token)
    {
        $user = Usuario::where('email_verification_token', $token)->first();

        if (!$user || !$user->email_pending) {

            SecurityLoggerService::emailChangeFailed($token);

            return redirect('/login')
                ->with('error', 'El enlace es inválido o ha expirado.');
        }

        // Guardar emails para el log
        $oldEmail = $user->vEmail;
        $newEmail = $user->email_pending;

        // Confirmar cambio
        $user->vEmail = $newEmail;
        $user->email_pending = null;
        $user->email_verification_token = null;
        $user->save();

        SecurityLoggerService::emailChangeCompleted(
            $user->id_usuario,
            $oldEmail,
            $newEmail
        );

        return redirect()->route('superadmin.perfil.index')
            ->with('success', 'Tu correo electrónico fue actualizado correctamente.');
    }
}
