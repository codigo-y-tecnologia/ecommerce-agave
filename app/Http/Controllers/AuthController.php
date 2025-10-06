<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;

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
        $rememberToken = \Illuminate\Support\Str::random(60);
        $apiToken = \Illuminate\Support\Str::random(60);

        $usuario = Usuario::create([
            'vNombre' => $data['vNombre'],
            'vApaterno' => $data['vApaterno'],
            'vAmaterno' => $data['vAmaterno'] ?? null,
            'vEmail' => $data['vEmail'],
            'vPassword' => Hash::make($data['vPassword']),
            'dFecha_nacimiento' => $data['dFecha_nacimiento'],
            'eRol' => 'cliente',
            'remember_token' => $rememberToken, // Token para "recordar sesión"
            'api_token' => $apiToken, // Token para API
        ]);

        Auth::login($usuario); // inicia sesión automático después de registro
        $request->session()->regenerate();

        return redirect('/')->with('success', 'Registro exitoso, bienvenido.');
    }

     /**
     * Limpia y valida si los campos contienen palabras reservadas peligrosas
     */
    private function verificarYLimpiar(array &$data, array $palabrasReservadas): void
    {
        foreach ($data as $campo => &$valor) {
            // Ignorar campos no textuales
            if (!is_string($valor)) continue;

            // Eliminar etiquetas HTML y espacios extremos
            $valor = trim(strip_tags($valor));

            // Si el campo contiene palabra reservada -> bloquear
            foreach ($palabrasReservadas as $palabra) {
                if (stripos($valor, $palabra) !== false) {
                    abort(400, "El campo '$campo' contiene contenido no permitido.");
                }
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
