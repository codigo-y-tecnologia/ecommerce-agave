<?php

namespace App\Http\Controllers\Perfil;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Models\Usuario;
use App\Mail\VerifyNewEmailCliente;

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

        $data = $request->validate([
            'vNombre' => ['required', 'string', 'max:60', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/u'],
            'vApaterno' => ['required', 'string', 'max:50', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/u'],
            'vAmaterno' => ['required', 'string', 'max:50', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/u'],
            'vEmail' => ['required', 'email', 'max:100'],
        ], [
            // Mensajes personalizados claros
            'vNombre.required'   => 'El nombre es obligatorio.',
            'vApaterno.required' => 'El apellido paterno es obligatorio.',
            'vAmaterno.required' => 'El apellido materno es obligatorio.',
            'vEmail.required'    => 'El correo es obligatorio.',
            'regex' => 'El campo :attribute solo puede contener letras y espacios.',
            'vEmail.email' => 'El correo electrónico debe tener un formato válido.',
            'vEmail.max'         => 'El correo no debe exceder los 100 caracteres.',
        ]);

        // Actualizar los datos
        $usuario->update([
            'vNombre'   => $data['vNombre'],
            'vApaterno' => $data['vApaterno'],
            'vAmaterno' => $data['vAmaterno'],
        ]);

        // Cambio de correo
        if ($data['vEmail'] !== $usuario->vEmail) {

            if (Usuario::where('vEmail', $data['vEmail'])->exists()) {
                return back()->withErrors([
                    'vEmail' => 'Este correo ya está registrado.'
                ]);
            }

            $token = Str::random(60);

            $usuario->update([
                'email_pending' => $data['vEmail'],
                'email_verification_token' => $token,
            ]);

            Mail::to($data['vEmail'])->send(
                new VerifyNewEmailCliente($usuario, $token)
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
        $usuario->update([
            'vPassword' => Hash::make($request->password_nueva),
            'remember_token' => Str::random(60),
        ]);

        Auth::logout();

        return redirect()->route('login')
            ->with('success', 'Contraseña actualizada. Vuelve a iniciar sesión.');
    }

    /**
     * Cerrar otras sesiones
     */
    public function logoutOtherDevices(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->password, $user->vPassword)) {
            return back()->withErrors([
                'password' => 'La contraseña no es correcta.'
            ]);
        }

        $user->update([
            'remember_token' => Str::random(60),
        ]);

        $request->session()->regenerate();

        return back()->with('success', 'Otras sesiones cerradas correctamente.');
    }

    /**
     * Verificar nuevo correo electrónico
     */
    public function verifyNewEmail(string $token)
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

        return redirect()->route('perfil.configuracion')
            ->with('success', 'Tu correo electrónico fue actualizado correctamente.');
    }

    /**
     * Eliminar cuenta del usuario
     */
    public function eliminar(Request $request)
    {

        $request->validate([
            'password' => 'required',
        ]);

        $usuario = Auth::user();

        if (!Hash::check($request->password, $usuario->vPassword)) {
            return back()->withErrors([
                'password' => 'La contraseña ingresada no es correcta.'
            ]);
        }

        Auth::logout();

        $usuario->delete();

        return redirect()->route('home')->with('success', '🗑️ Tu cuenta ha sido eliminada correctamente.');
    }
}
