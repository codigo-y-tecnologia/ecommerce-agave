<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Traits\InputSanitizer;

class SuperadminController extends Controller
{

    use InputSanitizer;

    // Listado de administradores
    public function index(Request $request)
    {
        $query = $request->input('q');

        $usuarios = Usuario::query()
            ->when($query, function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('vNombre', 'like', "%{$query}%")
                        ->orWhere('vApaterno', 'like', "%{$query}%")
                        ->orWhere('vAmaterno', 'like', "%{$query}%")
                        ->orWhere('vEmail', 'like', "%{$query}%");
                });
            })
            ->whereHas('roles', function ($q) {
                $q->whereIn('name', ['admin', 'superadmin']);
            })
            ->orderBy('vNombre')
            ->paginate(8);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('superadmin.partials.table', compact('usuarios'))->render()
            ]);
        }

        return view('superadmin.admins.index', compact('usuarios'));
    }

    // Promover cliente → admin
    public function promoteToAdmin($id)
    {
        $user = Usuario::findOrFail($id);

        if ($user->hasRole('superadmin')) {
            return back()->with('error', 'No se puede modificar a un superadmin.');
        }

        $user->syncRoles(['admin']);

        return back()->with('success', "{$user->vNombre} ahora es administrador.");
    }

    // Degradar admin → cliente
    public function demoteToClient($id)
    {
        $user = Usuario::findOrFail($id);

        if (! $user->hasRole('admin')) {
            return back()->with('error', 'Solo se pueden degradar administradores.');
        }

        $user->syncRoles(['cliente']);

        return back()->with('success', "{$user->vNombre} ha sido degradado a cliente.");
    }

    // Eliminar usuario
    public function destroy($id)
    {
        $user = Usuario::findOrFail($id);

        if ($user->hasRole('superadmin')) {
            return back()->with('error', 'No se puede eliminar a un superadmin.');
        }

        $nombre = $user->vNombre;
        $user->delete();

        return back()->with('success', "El usuario {$nombre} fue eliminado.");
    }

    public function create()
    {
        return view('superadmin.admins.create');
    }

    public function store(Request $request)
    {

        $data = $request->validate([
            'vNombre' => ['required', 'string', 'max:60', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/u'],
            'vApaterno' => ['required', 'string', 'max:50', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/u'],
            'vAmaterno' => ['required', 'string', 'max:50', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/u'],
            'vEmail' => ['required', 'email', 'max:100', 'unique:tbl_usuarios,vEmail'],
            'dFecha_nacimiento' => ['required', 'date', 'before_or_equal:today'],
        ], [
            // Mensajes personalizados claros
            'regex' => 'El campo :attribute solo puede contener letras y espacios.',
            'vEmail.email' => 'El correo electrónico debe tener un formato válido.',
            'vEmail.unique' => 'El correo electrónico ya está registrado.',
            'dFecha_nacimiento.before_or_equal' => 'La fecha de nacimiento no puede ser mayor a la fecha actual.',
        ]);

        // Validación: mayor de edad
        $fechaNac = new \DateTime($data['dFecha_nacimiento']);
        $hoy = new \DateTime();
        $edad = $hoy->diff($fechaNac)->y;

        if ($edad < 18) {
            return back()->withErrors(['dFecha_nacimiento' => 'El administrador debe ser mayor de edad para registrarse.'])->withInput();
        }

        // Generar contraseña aleatoria segura
        $passwordPlain = Str::random(10);

        $admin = Usuario::create([
            'vNombre' => $data['vNombre'],
            'vApaterno' => $data['vApaterno'],
            'vAmaterno' => $data['vAmaterno'],
            'vEmail' => $data['vEmail'],
            'dFecha_nacimiento' => $data['dFecha_nacimiento'],
            'vPassword' => Hash::make($passwordPlain),
            // 'eRol' => 'admin',
            'is_verified' => true,
        ]);

        // Asignar rol Spatie
        $admin->assignRole('admin');

        // Enviar correo al nuevo admin
        try {
            Mail::raw("Hola {$admin->vNombre}, tu cuenta de administrador ha sido creada.\n\nCorreo: {$admin->vEmail}\nContraseña temporal: {$passwordPlain}\n\nPor favor inicia sesión y cambia tu contraseña.", function ($message) use ($admin) {
                $message->to($admin->vEmail)
                    ->subject('Tu nueva cuenta de administrador - Ecommerce Agave');
            });
        } catch (\Exception $e) {
            // Puedes registrar el error en logs
            Log::error('No se pudo enviar el correo al nuevo admin: ' . $e->getMessage());
        }

        return redirect()->route('superadmin.admins.index')
            ->with('success', "Administrador {$admin->vNombre} creado correctamente. Se le envió su contraseña al correo.");
    }
}
