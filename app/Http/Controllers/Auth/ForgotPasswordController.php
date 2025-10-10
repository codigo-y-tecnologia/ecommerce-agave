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

    $request->validate(['vEmail' => ['required', 'email', 'max:80', 'exists:tbl_usuarios,vEmail']]);

     $emailData = $request->only('vEmail');
        $this->verificarYLimpiar($emailData, config('security.sql_keywords'));

    // Configurar el campo personalizado para el broker
        $response = Password::broker('users')->sendResetLink(
            $request->only('vEmail')
        );

        return $response == Password::RESET_LINK_SENT
            ? back()->with(['status' => __($response)])
            : back()->withErrors(['vEmail' => __($response)]);

    }
}
