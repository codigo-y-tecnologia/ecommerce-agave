<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use App\Traits\InputSanitizer;

class ResetPasswordController extends Controller
{

    use InputSanitizer;
    
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.reset-password', [
            'token' => $token, 
            'email' => $request->email]);
    }

    public function reset(Request $request)
    {
        $data = $request->validate([
            'token' => 'required',
            'vEmail' => 'required|email|max:80|exists:tbl_usuarios,vEmail',
            'password' => 'required|min:8|max:150|confirmed',
        ], [
            // Mensajes personalizados claros
            'vEmail.exists' => 'No se encontró una cuenta con ese correo electrónico.',
            'vEmail.email' => 'El correo electrónico debe tener un formato válido.',
            'vEmail.required' => 'El campo de correo electrónico es obligatorio.',
            'vEmail.max' => 'El correo electrónico no debe exceder los 80 caracteres.',
            'password.required' => 'El campo de contraseña es obligatorio.',
            'confirmed' => 'Las contraseñas no coinciden.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.max' => 'La contraseña no debe exceder los 150 caracteres.',
        ]);

        $this->verificarYLimpiar($data, config('security.sql_keywords'));
        
        $status = Password::broker('users')->reset(
            $request->only('vEmail', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'vPassword' => Hash::make($password)
                ])->save();
            }
        );

        return $status == Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', __($status))
            : back()->withErrors(['vEmail' => [__($status)]]);
    }
}
