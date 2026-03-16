<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\Usuario;
use Illuminate\Support\Str;
use App\Services\System\SecurityLoggerService;

class AdminPerfilController extends Controller
{
    /**
     * Vista principal del perfil
     */
    public function index()
    {
        return view('admin.perfil.index');
    }

    /**
     * Actualizar datos personales
     */
    public function updateDatos(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'vNombre'    => 'required|string|max:60',
            'vApaterno'  => 'required|string|max:50',
            'vAmaterno'  => 'required|string|max:50',
            'vEmail'     => 'required|email|max:100',
        ]);

        // Actualizar nombre y apellidos
        $user->update([
            'vNombre'   => $data['vNombre'],
            'vApaterno' => $data['vApaterno'],
            'vAmaterno' => $data['vAmaterno'],
        ]);

        // Si cambia el email
        if ($data['vEmail'] !== $user->vEmail) {

            if (
                Usuario::where('vEmail', $data['vEmail'])->exists()
            ) {
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
                new \App\Mail\VerifyNewEmail($user, $token)
            );

            return back()->with(
                'warning',
                'Se envió un enlace de verificación al nuevo correo. El cambio se aplicará cuando lo confirmes.'
            );
        }

        return back()->with('success', 'Datos actualizados correctamente.');
    }

    /**
     * Verificar nuevo correo electrónico
     */
    public function verifyNewEmail($token)
    {
        $user = Usuario::where('email_verification_token', $token)->firstOrFail();

        $oldEmail = $user->vEmail;

        $user->update([
            'vEmail' => $user->email_pending,
            'email_pending' => null,
            'email_verification_token' => null,
        ]);

        SecurityLoggerService::emailChangeCompleted(
            $user->id_usuario,
            $oldEmail,
            $user->vEmail
        );

        return redirect()->route('admin.perfil.index')
            ->with('success', 'Correo electrónico actualizado correctamente.');
    }

    /**
     * Cambiar contraseña
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', 'min:8'],
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

        SecurityLoggerService::passwordChanged(
            $user->id_usuario,
            $user->vEmail
        );

        Auth::logout(); // cierra sesión actual
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Contraseña actualizada. Vuelve a iniciar sesión.');
    }

    /**
     * Cerrar sesiones activas
     */
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

        // Invalidar todas las demás sesiones
        $user->update([
            'remember_token' => Str::random(60),
        ]);

        // Mantiene la sesión actual
        $request->session()->regenerate();

        return back()->with('success', 'Se cerraron todas las demás sesiones.');
    }
}
