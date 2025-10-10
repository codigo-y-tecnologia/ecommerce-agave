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
        $request->validate([
            'token' => 'required',
            'vEmail' => 'required|email',
            'password' => 'required|min:8|max:150|confirmed',
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
