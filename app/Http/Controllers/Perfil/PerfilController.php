<?php

namespace App\Http\Controllers\Perfil;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Models\Usuario;
use App\Mail\VerifyNewEmail;

class PerfilController extends Controller
{
    public function configuracion()
    {
        $usuario = Auth::user();
        return view('perfil.configuracion', compact('usuario'));
    }

    /**
     * Actualizar datos personales del usuario
     */
    public function actualizar(Request $request)
    {
        $usuario = Auth::user();

        $request->validate([
            'vNombre'   => 'required|string|max:60',
            'vApaterno' => 'required|string|max:50',
            'vAmaterno' => 'nullable|string|max:50',
            'vEmail'    => 'required|email|max:100',
        ], [
            'vNombre.required'   => 'El nombre es obligatorio.',
            'vApaterno.required' => 'El apellido paterno es obligatorio.',
            'vEmail.required'    => 'El correo es obligatorio.',
            'vEmail.email'       => 'Debe ingresar un correo válido.',
            'vEmail.max'         => 'El correo no debe exceder los 100 caracteres.',
        ]);

        // Actualizar los datos
        $usuario->update([
            'vNombre'   => $request->vNombre,
            'vApaterno' => $request->vApaterno,
            'vAmaterno' => $request->vAmaterno,
        ]);

        // Cambio de correo
        if ($data['vEmail'] !== $user->vEmail) {

            if (Usuario::where('vEmail', $data['vEmail'])->exists()) {
                return back()->withErrors([
                    'vEmail' => 'Este correo ya está registrado.'
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
                'Te enviamos un correo para confirmar tu nuevo email.'
            );
        }

        return back()->with('success', 'Datos actualizados correctamente.');
    }

    /**
     * Cambiar contraseña
     */
    public function cambiarPassword(Request $request)
    {
        $usuario = Auth::user();

        $request->validate([
            'password_actual'       => 'required',
            'password_nueva'        => 'required|min:8|max:150|confirmed',
        ], [
            'password_actual.required' => 'Debes ingresar tu contraseña actual.',
            'password_nueva.required'  => 'Debes ingresar una nueva contraseña.',
            'password_nueva.min'       => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'password_nueva.max'       => 'La nueva contraseña no debe exceder los 150 caracteres.',
            'password_nueva.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        // Verificar la contraseña actual
        if (!Hash::check($request->password_actual, $usuario->vPassword)) {
            return back()->withErrors(['password_actual' => 'La contraseña actual es incorrecta.']);
        }

        // Actualizar la contraseña
        $usuario->vPassword = Hash::make($request->password_nueva);
        $usuario->save();

        return back()->with('success', '🔒 Tu contraseña se ha actualizado correctamente.');
    }

    /**
     * Eliminar cuenta del usuario
     */
    public function eliminar(Request $request)
    {
        $usuario = Auth::user();
        Auth::logout();

        $usuario->delete();

        return redirect()->route('home')->with('success', '🗑️ Tu cuenta ha sido eliminada correctamente.');
    }
}
