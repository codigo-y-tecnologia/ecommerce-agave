<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;

class CambiarEmailController extends Controller
{
    public function verify(string $token)
    {
        $user = Usuario::where('email_verification_token', $token)->first();

        if (!$user || !$user->email_pending) {
            return redirect('/login')
                ->with('error', 'El enlace es inválido o ha expirado.');
        }

        // Confirmar cambio
        $user->vEmail = $user->email_pending;
        $user->email_pending = null;
        $user->email_verification_token = null;
        $user->save();

        return redirect()->route('superadmin.perfil.index')
            ->with('success', 'Tu correo electrónico fue actualizado correctamente.');
    }
}
