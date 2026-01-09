<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Models\Usuario;
use App\Mail\VerifyNewEmailSuperAdmin;

class SuperadminPerfilController extends Controller
{
    public function index()
    {
        return view('superadmin.perfil.index');
    }

    public function updateDatos(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'vNombre'   => 'required|string|max:60',
            'vApaterno' => 'required|string|max:50',
            'vAmaterno' => 'required|string|max:50',
            'vEmail'    => 'required|email|max:100',
        ]);

        // Datos básicos
        $user->update([
            'vNombre'   => $data['vNombre'],
            'vApaterno' => $data['vApaterno'],
            'vAmaterno' => $data['vAmaterno'],
        ]);

        // Cambio de correo
        if ($data['vEmail'] !== $user->vEmail) {

            if (Usuario::where('vEmail', $data['vEmail'])->exists()) {
                return back()->withErrors([
                    'vEmail' => 'El correo ya está en uso.'
                ]);
            }

            $token = Str::random(60);

            $user->update([
                'email_pending' => $data['vEmail'],
                'email_verification_token' => $token,
            ]);

            Mail::to($data['vEmail'])->send(
                new VerifyNewEmailSuperAdmin($user, $token)
            );

            return back()->with(
                'warning',
                'Debes confirmar el nuevo correo. El cambio no se aplicará hasta verificarlo.'
            );
        }

        return back()->with('success', 'Perfil actualizado correctamente.');
    }

    public function verifyNewEmail($token)
    {
        $user = Usuario::where('email_verification_token', $token)->firstOrFail();

        $user->update([
            'vEmail' => $user->email_pending,
            'email_pending' => null,
            'email_verification_token' => null,
        ]);

        return redirect()
            ->route('superadmin.perfil.index')
            ->with('success', 'Correo electrónico actualizado correctamente.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|confirmed|min:8',
        ], [
            'current_password.required' => 'Debes ingresar tu contraseña actual.',
            'password.required' => 'La nueva contraseña es obligatoria.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->vPassword)) {
            return back()->withErrors([
                'current_password' => 'La contraseña actual no es correcta.'
            ]);
        }

        $user->update([
            'vPassword' => Hash::make($request->password),
            'remember_token' => Str::random(60),
        ]);

        Auth::logout();

        return redirect()->route('login')
            ->with('success', 'Contraseña actualizada. Vuelve a iniciar sesión.');
    }

    public function logoutOtherDevices(Request $request)
    {
        $request->validate([
            'password' => ['required'],
        ], [
            'password.required' => 'Debes ingresar tu contraseña actual.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->password, $user->vPassword)) {
            return back()->withErrors([
                'password' => 'La contraseña ingresada no es correcta.'
            ]);
        }

        $user->update([
            'remember_token' => Str::random(60),
        ]);

        $request->session()->regenerate();

        return back()->with('success', 'Todas las demás sesiones han sido cerradas.');
    }
}
