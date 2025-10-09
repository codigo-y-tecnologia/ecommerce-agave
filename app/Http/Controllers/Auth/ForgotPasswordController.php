<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
{
return view('auth.forgot-password');
}

public function sendResetLinkEmail(Request $request)
{

    $request->validate(['vEmail' => 'required|email']);

    // Configura el campo personalizado para el broker
        $response = Password::broker('users')->sendResetLink(
            $request->only('vEmail')
        );

        return $response == Password::RESET_LINK_SENT
            ? back()->with(['status' => __($response)])
            : back()->withErrors(['vEmail' => __($response)]);

    }
}
