<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /**
     * Donde redirigir después del registro
     */
    protected $redirectTo = '/';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Mostrar formulario de registro
     */
    public function showRegistrationForm()
    {
        return view('registroUsuarios');
    }

    /**
     * Procesar registro - SIN ENCRIPTACIÓN
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $usuario = $this->create($request->all());

        Auth::login($usuario);

        return redirect($this->redirectTo);
    }

    /**
     * Validación personalizada
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'vNombre' => 'required|string|max:60',
            'vApaterno' => 'required|string|max:50',
            'vAmaterno' => 'required|string|max:50',
            'vEmail' => 'required|email|unique:tbl_usuarios,vEmail',
            'vPassword' => 'required|min:6|confirmed',
            'dFecha_nacimiento' => 'required|date',
            'eRol' => 'in:cliente,admin'
        ]);
    }

    /**
     * Crear usuario - SIN ENCRIPTACIÓN
     */
    protected function create(array $data)
    {
        return Usuario::create([
            'vNombre' => $data['vNombre'],
            'vApaterno' => $data['vApaterno'],
            'vAmaterno' => $data['vAmaterno'],
            'vEmail' => $data['vEmail'],
            'vPassword' => $data['vPassword'], 
            'dFecha_nacimiento' => $data['dFecha_nacimiento'],
            'eRol' => $data['eRol'] ?? 'cliente',
        ]);
    }
}