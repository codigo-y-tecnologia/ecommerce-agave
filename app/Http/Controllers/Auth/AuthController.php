<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Notifications\VerifyEmailNotification;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'vEmail' => 'required|email',
            'vPassword' => 'required|min:6',
        ]);

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
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'vEmail' => 'Credenciales incorrectas.',
        ])->onlyInput('vEmail');
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
            'vEmail' => ['required', 'email', 'max:80', 'unique:tbl_usuarios,vEmail'],
            'vPassword' => ['required', 'string', 'min:8', 'max:150', 'confirmed'],
            'dFecha_nacimiento' => ['required', 'date'],
            'terminos' => ['accepted'],
        ], [
            // Mensajes personalizados claros
            'regex' => 'El campo :attribute solo puede contener letras y espacios.',
            'accepted' => 'Debes aceptar los términos y condiciones.',
            'confirmed' => 'Las contraseñas no coinciden.',
            'vPassword.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        // Validación extra: mayor de edad
    $fechaNac = new \DateTime($data['dFecha_nacimiento']);
    $hoy = new \DateTime();
    $edad = $hoy->diff($fechaNac)->y;

    if ($edad < 18) {
        return back()->withErrors(['dFecha_nacimiento' => 'Debes ser mayor de edad para registrarte.'])->withInput();
    }

    // Lista de palabras reservadas SQL
        $palabrasReservadas = [
            'SELECT', 'INSERT', 'DELETE', 'UPDATE', 'DROP', 'CREATE', 'ALTER', 'TRUNCATE',
            'FROM', 'WHERE', 'AND', 'OR', 'JOIN', 'UNION', 'LIKE', 'HAVING', 'EXEC',
            'GRANT', 'REVOKE', 'ADMIN', 'CAST', 'DECLARE', 'REPLACE', 'RENAME',
            'BENCHMARK', 'LOAD_FILE', 'INTO OUTFILE', 'SHOW', 'DESCRIBE', 'EXPLAIN',
            'MERGE', 'WITH', 'FOREIGN', 'PRIMARY', 'TABLE', 'COLUMN', 'VIEW', 'INDEX',
            'PASSWORD', 'USER', 'SYSTEM', 'DATABASE', 'DROP USER', 'INFILE'
        ];

        // Funciones de limpieza y detección
        $this->verificarYLimpiar($data, $palabrasReservadas);

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
            'eRol' => 'cliente',
            'remember_token' => $rememberToken, // Token para "recordar sesión"
            'api_token' => $apiToken, // Token para API
        ]);

        // Enviar email de verificación
        try {
            $usuario->notify(new VerifyEmailNotification($usuario, $verificationToken));
        } catch (\Exception $e) {
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
            'vEmail' => 'required|email|exists:tbl_usuarios,vEmail'
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
            return back()->withErrors([
                'vEmail' => 'Error al enviar el email de verificación. Por favor, intenta nuevamente.'
            ]);
        }
    }

     /**
 * Limpia y valida si los campos contienen contenido potencialmente peligroso
 * (anti inyección SQL básica sin falsos positivos)
 */
private function verificarYLimpiar(array &$data, array $palabrasReservadas): void
{
    foreach ($data as $campo => &$valor) {
        // Ignorar campos no textuales
        if (!is_string($valor)) continue;

        // Sanitizar entradas: quitar etiquetas HTML y espacios
        $valor = trim(strip_tags($valor));

        // Evitar caracteres de control invisibles o nulos
        $valor = preg_replace('/[\x00-\x1F\x7F]/u', '', $valor);

        // Normalizar espacios múltiples
        $valor = preg_replace('/\s+/', ' ', $valor);

        // Detectar inyecciones SQL reales (palabras reservadas completas)
        foreach ($palabrasReservadas as $palabra) {
            // Buscar palabra reservada completa (no dentro de nombres como "Alejandro")
            if (preg_match('/\b' . preg_quote($palabra, '/') . '\b/i', $valor)) {
                abort(400, "El campo '$campo' contiene contenido no permitido.");
            }
        }

        // Validar longitud general (prevención de payloads largos)
        if (strlen($valor) > 255) {
            abort(400, "El campo '$campo' es demasiado largo.");
        }

        // Revalidar que no contenga caracteres sospechosos tipo SQL o HTML
        if (preg_match('/(--|#|;|\/\*|\*\/|<\?|<script|<\/script>)/i', $valor)) {
            abort(400, "El campo '$campo' contiene caracteres no permitidos.");
        }
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
