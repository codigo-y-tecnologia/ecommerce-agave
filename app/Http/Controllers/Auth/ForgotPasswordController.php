<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Traits\InputSanitizer;

class ForgotPasswordController extends Controller
{

    use InputSanitizer;

    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {

        $request->validate(['vEmail' => ['required', 'email', 'max:80', 'exists:tbl_usuarios,vEmail']], [
            // Mensajes personalizados claros
            'vEmail.exists' => 'No se encontró una cuenta con ese correo electrónico.',
            'vEmail.email' => 'El correo electrónico debe tener un formato válido.',
            'vEmail.required' => 'El campo de correo electrónico es obligatorio.',
        ]);

        // Configurar el campo personalizado para el broker
        $response = Password::broker('users')->sendResetLink(
            $request->only('vEmail')
        );

        return $response == Password::RESET_LINK_SENT
            ? back()->with(['status' => __($response)])
            : back()->withErrors(['vEmail' => __($response)]);
    }
}
