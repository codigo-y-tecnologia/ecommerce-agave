<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
use App\Models\Producto; 
use Illuminate\Support\Facades\File;

class AuthController extends Controller
{
    public function index()
    {
        $productos = Producto::with(['marca', 'categoria', 'etiquetas'])
                        ->where('bActivo', 1)
                        ->get();
        
        // Obtener imágenes para cada producto - CORREGIDO
        foreach ($productos as $producto) {
            $carpetaProducto = public_path('images/productos/' . $producto->vCodigo_barras);
            $imagenes = [];
            
            if (File::exists($carpetaProducto)) {
                $archivos = File::files($carpetaProducto);
                foreach ($archivos as $archivo) {
                    $extension = strtolower($archivo->getExtension());
                    if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                        $imagenes[] = $archivo->getFilename();
                    }
                }
                // Ordenar imágenes por nombre numéricamente
                natsort($imagenes);
                $imagenes = array_values($imagenes);
            }
            
            // Agregar imágenes al producto
            $producto->imagenes = $imagenes;
        }
    
        return view('inicio', compact('productos'));
    }

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
        return view('registroUsuarios');
    }

    /**
     * Guardar nuevo usuario
     */
    public function register(Request $request)
    {
        // Validación
        $data = $request->validate([
            'vNombre' => 'required|string|max:60',
            'vApaterno' => 'required|string|max:50',
            'vAmaterno' => 'required|string|max:50',
            'vEmail' => 'required|email|unique:tbl_usuarios,vEmail',
            'vPassword' => 'required|min:6|confirmed',
            'dFecha_nacimiento' => 'required|date',
            'eRol' => 'in:cliente,admin'
        ]);

        // Generar tokens
        $rememberToken = \Illuminate\Support\Str::random(60);
        $apiToken = \Illuminate\Support\Str::random(60);

        $usuario = \App\Models\Usuario::create([
            'vNombre' => $data['vNombre'],
            'vApaterno' => $data['vApaterno'],
            'vAmaterno' => $data['vAmaterno'] ?? null,
            'vEmail' => $data['vEmail'],
            'vPassword' => Hash::make($data['vPassword']),
            'dFecha_nacimiento' => $data['dFecha_nacimiento'],
            'eRol' => $data['eRol'] ?? 'cliente',
            'remember_token' => $rememberToken, // Token para "recordar sesión"
            'api_token' => $apiToken, // Token para API
        ]);

        Auth::login($usuario); // inicia sesión automático después de registro
        $request->session()->regenerate();

        return redirect('/');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}