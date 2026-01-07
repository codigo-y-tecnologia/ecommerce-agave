<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Traits\InputSanitizer;
use App\Models\Usuario;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Notifications\VerifyEmailNotification;

class AuthController extends Controller
{

    use InputSanitizer;

    public function showLogin()
    {

        if (Auth::check()) {
            return $this->redirectToDashboard(Auth::user());
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'vEmail' => ['required', 'email', 'max:100', 'exists:tbl_usuarios,vEmail'],
            'vPassword' => ['required', 'string', 'min:8', 'max:150'],
        ], [
            // Mensajes personalizados claros
            'vEmail.exists' => 'No se encontró una cuenta con ese correo electrónico.',
            'vEmail.email' => 'El correo electrónico debe tener un formato válido.',
        ]);

        $this->verificarYLimpiar($credentials, config('security.sql_keywords'));

        $credentials = [
            'vEmail' => $request->vEmail,
            'password' => $request->vPassword,
        ];

        $remember = $request->boolean('remember'); // checkbox remember

        if (Auth::attempt($credentials, $remember)) {

            $user = Auth::user();

            // Verificar si el email está verificado
            if (!$user->hasVerifiedEmail()) {
                Auth::logout();
                return back()->withErrors([
                    'vEmail' => 'Por favor, verifica tu dirección de correo electrónico antes de iniciar sesión. Revisa tu bandeja de entrada.',
                ])->withInput();
            }

            $request->session()->regenerate();
            //return redirect()->intended('/');

            // Redirigir al dashboard según rol
            return $this->redirectToDashboard($user);
        }

        return back()->withErrors([
            'vEmail' => 'Credenciales incorrectas.',
        ])->onlyInput('vEmail');
    }

    private function redirectToDashboard($user)
    {
        return match (true) {
            $user->hasRole('superadmin') => redirect()->route('dashboard.superadmin'),
            $user->hasRole('admin')      => redirect()->route('dashboard.admin'),
            $user->hasRole('cliente')    => redirect()->route('dashboard.cliente'),
            default                      => abort(403, 'Rol no autorizado'),
        };
    }

    public function showRegister()
    {
        return view('auth.registroUsuarios');
    }

    /**
     * Guardar nuevo usuario
     */
    public function register(Request $request)
    {
        // Validación
        $data = $request->validate([
            'vNombre' => ['required', 'string', 'max:60', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/u'],
            'vApaterno' => ['required', 'string', 'max:50', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/u'],
            'vAmaterno' => ['required', 'string', 'max:50', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/u'],
            'vEmail' => ['required', 'email', 'max:100', 'unique:tbl_usuarios,vEmail'],
            'vPassword' => ['required', 'string', 'min:8', 'max:150', 'confirmed'],
            'dFecha_nacimiento' => ['required', 'date', 'before_or_equal:today'],
            'terminos' => ['accepted'],
        ], [
            // Mensajes personalizados claros
            'regex' => 'El campo :attribute solo puede contener letras y espacios.',
            'accepted' => 'Debes aceptar los términos y condiciones.',
            'vEmail.email' => 'El correo electrónico debe tener un formato válido.',
            'vEmail.unique' => 'El correo electrónico ya está registrado.',
            'confirmed' => 'Las contraseñas no coinciden.',
            'vPassword.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'dFecha_nacimiento.before_or_equal' => 'La fecha de nacimiento no puede ser mayor a la fecha actual.',
        ]);

        // Validación: mayor de edad
        $fechaNac = new \DateTime($data['dFecha_nacimiento']);
        $hoy = new \DateTime();
        $edad = $hoy->diff($fechaNac)->y;

        if ($edad < 18) {
            return back()->withErrors(['dFecha_nacimiento' => 'Debes ser mayor de edad para registrarte.'])->withInput();
        }

        /**
         * Limpia y valida si los campos contienen contenido potencialmente peligroso
         * (anti inyección SQL básica sin falsos positivos)
         */
        // Funciones de limpieza y detección
        $this->verificarYLimpiar($data, config('security.sql_keywords'));

        // Generar tokens
        $rememberToken = Str::random(60);
        $apiToken = Str::random(60);
        $verificationToken = Str::random(60);

        $usuario = Usuario::create([
            'vNombre' => $data['vNombre'],
            'vApaterno' => $data['vApaterno'],
            'vAmaterno' => $data['vAmaterno'] ?? null,
            'vEmail' => $data['vEmail'],
            'verification_token' => $verificationToken,
            'is_verified' => false,
            'vPassword' => Hash::make($data['vPassword']),
            'dFecha_nacimiento' => $data['dFecha_nacimiento'],
            // 'eRol' => 'cliente',
            'remember_token' => $rememberToken, // Token para "recordar sesión"
            'api_token' => $apiToken, // Token para API
        ]);

        $usuario->assignRole('cliente');

        // Enviar email de verificación
        try {
            $usuario->notify(new VerifyEmailNotification($usuario, $verificationToken));
        } catch (\Exception $e) {
            dd($e->getMessage());
            // Si falla el envío del email, eliminar el usuario creado
            $usuario->delete();
            return back()->withErrors([
                'vEmail' => 'Error al enviar el email de verificación. Por favor, intenta nuevamente.'
            ])->withInput();
        }

        // Auth::login($usuario); // inicia sesión automático después de registro
        // $request->session()->regenerate();

        return redirect('/login')->with([
            'success' => '¡Registro exitoso!',
            'verification_message' => 'Te hemos enviado un enlace de verificación a tu correo electrónico. Por favor, revisa tu bandeja de entrada y haz clic en el enlace para activar tu cuenta.'
        ]);
    }

    // Método para verificar el email
    public function verifyEmail($token)
    {
        $user = Usuario::where('verification_token', $token)->first();

        if (!$user) {
            return redirect('/login')->with('error', 'El enlace de verificación es inválido o ha expirado.');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect('/login')->with('info', 'Tu dirección de correo electrónico ya ha sido verificada. Puedes iniciar sesión.');
        }

        $user->markEmailAsVerified();

        return redirect('/login')->with([
            'success' => '¡Cuenta activada!',
            'verification_success' => 'Tu dirección de correo electrónico ha sido verificada exitosamente. Ahora puedes iniciar sesión con tus credenciales.'
        ]);
    }

    // Método para reenviar email de verificación
    public function resendVerificationEmail(Request $request)
    {
        $request->validate([
            'vEmail' => 'required|email|max:100|exists:tbl_usuarios,vEmail'
        ], [
            // Mensajes personalizados claros
            'vEmail.exists' => 'No se encontró una cuenta con ese correo electrónico.',
            'vEmail.email' => 'El correo electrónico debe tener un formato válido.',
        ]);

        $emailData = $request->only('vEmail');
        $this->verificarYLimpiar($emailData, config('security.sql_keywords'));


        $user = Usuario::where('vEmail', $request->vEmail)->first();

        if ($user->hasVerifiedEmail()) {
            return back()->with('info', 'Tu dirección de correo electrónico ya ha sido verificada.');
        }

        // Generar nuevo token si no existe
        if (!$user->verification_token) {
            $user->verification_token = Str::random(60);
            $user->save();
        }

        try {
            $user->notify(new VerifyEmailNotification($user, $user->verification_token));
            return back()->with('success', 'Se ha enviado un nuevo enlace de verificación a tu correo electrónico.');
        } catch (\Exception $e) {
            dd($e->getMessage());
            return back()->withErrors([
                'vEmail' => 'Error al enviar el email de verificación. Por favor, intenta nuevamente.'
            ]);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
