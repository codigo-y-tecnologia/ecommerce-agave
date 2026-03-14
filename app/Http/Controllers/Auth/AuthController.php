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
use App\Mail\CuentaCreadaAutomaticamente;
use Illuminate\Auth\Events\Verified;
use App\Services\System\SecurityLoggerService;

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

        $user = Usuario::where('vEmail', $request->vEmail)->first();

        if (
            $user && empty($user->vPassword)
        ) {
            return back()->withErrors([
                'vEmail' => 'Tu cuenta fue creada automáticamente después de una compra. Revisa tu correo y establece tu contraseña para poder iniciar sesión.',
            ])
                ->with('show_set_password', true)
                ->withInput();
        }

        $credentials = [
            'vEmail' => $request->vEmail,
            'password' => $request->vPassword,
        ];

        $remember = $request->boolean('remember'); // checkbox remember

        if (Auth::attempt($credentials, $remember)) {

            $user = Auth::user();

            SecurityLoggerService::loginSuccess($user->id_usuario, $user->vEmail);

            // Verificar si el email está verificado
            if (!$user->hasVerifiedEmail()) {
                Auth::logout();
                return back()->withErrors([
                    'vEmail' => 'Por favor, verifica tu dirección de correo electrónico antes de iniciar sesión. Si no recibiste el correo de verificación, puedes solicitar uno nuevo.',
                ])->withInput();
            }

            $request->session()->regenerate();
            return redirect()->intended('/');

            // Redirigir al dashboard según rol
            //return $this->redirectToDashboard($user);
        }

        SecurityLoggerService::loginFailed($request->vEmail);

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

        event(new Verified($user));

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

        if (Auth::check()) {
            SecurityLoggerService::logout(Auth::user()->vEmail);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function activarCuenta($token)
    {
        $usuario = Usuario::where('email_verification_token', $token)->firstOrFail();

        return view('auth.activar_cuenta', compact('usuario', 'token'));
    }

    public function guardarPassword(Request $request, $token)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ], [
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $usuario = Usuario::where('email_verification_token', $token)->firstOrFail();

        $usuario->update([
            'vPassword' => Hash::make($request->password),
            'email_verified_at' => now(),
            'is_verified' => 1,
            'email_verification_token' => null,
        ]);

        // Registrar cambio de contraseña
        SecurityLoggerService::passwordChanged($usuario->id_usuario, $usuario->vEmail);

        Auth::login($usuario);

        return redirect()->route('dashboard.cliente')
            ->with('success', 'Cuenta activada correctamente');
    }

    public function reenviarCorreo(Request $request)
    {
        $request->validate([
            'vEmail' => 'required|email|max:100|exists:tbl_usuarios,vEmail',
        ], [
            'vEmail.exists' => 'No se encontró una cuenta con ese correo electrónico.',
            'vEmail.email' => 'El correo electrónico debe tener un formato válido.',
        ]);

        $usuario = Usuario::where('vEmail', $request->vEmail)->first();

        // Seguridad: solo si NO tiene contraseña
        if ($usuario->vPassword !== null) {
            return back()->withErrors([
                'vEmail' => 'Esta cuenta ya tiene una contraseña configurada.'
            ]);
        }

        // Generar nuevo token
        $token = Str::uuid()->toString();

        $usuario->update([
            'email_verification_token' => $token,
        ]);

        Mail::to($usuario->vEmail)->send(
            new CuentaCreadaAutomaticamente($usuario, $token)
        );

        // Registrar solicitud de configuración de contraseña
        SecurityLoggerService::passwordResetRequested($usuario->vEmail);

        SecurityLoggerService::passwordSetupEmailSent(
            $usuario->id_usuario,
            $usuario->vEmail
        );

        return back()->with('success', 'Te enviamos nuevamente el correo para establecer tu contraseña.');
    }
}
