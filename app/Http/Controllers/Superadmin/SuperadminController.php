<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SuperadminController extends Controller
{
    // Listado de administradores
    public function index(Request $request)
    {
        $query = $request->input('q');
        $usuarios = Usuario::query()
            ->when($query, fn($q) =>
                $q->where('vNombre', 'like', "%{$query}%")
                  ->orWhere('vApaterno', 'like', "%{$query}%")
                  ->orWhere('vAmaterno', 'like', "%{$query}%")
                  ->orWhere('vEmail', 'like', "%{$query}%")
            )
            ->whereIn('eRol', ['admin', 'superadmin']) // ver admins y superadmins
            ->orderBy('vNombre')
            ->paginate(8);

        if ($request->ajax()) {
            return response()->json(['html' => view('superadmin.partials.table', compact('usuarios'))->render()]);
        }

        return view('superadmin.admins.index', compact('usuarios'));
    }

    // Promover cliente → admin
    public function promoteToAdmin($id)
    {
        $user = Usuario::findOrFail($id);
        if ($user->eRol !== 'superadmin') {
            $user->update(['eRol' => 'admin']);
            return back()->with('success', "{$user->vNombre} ahora es administrador.");
        }
        return back()->with('error', 'No se puede modificar a un superadmin.');
    }

    // Degradar admin → cliente
    public function demoteToClient($id)
    {
        $user = Usuario::findOrFail($id);
        if ($user->eRol === 'admin') {
            $user->update(['eRol' => 'cliente']);
            return back()->with('success', "{$user->vNombre} ha sido degradado a cliente.");
        }
        return back()->with('error', 'Solo se pueden degradar administradores.');
    }

    // Eliminar usuario
    public function destroy($id)
    {
        $user = Usuario::findOrFail($id);
        if ($user->eRol === 'superadmin') {
            return back()->with('error', 'No se puede eliminar a un superadmin.');
        }

        $nombre = $user->vNombre;
        $user->delete();
        return back()->with('success', "El administrador {$nombre} fue eliminado.");
    }

    public function create()
{
    return view('superadmin.admins.create');
}

public function store(Request $request)
{
    $request->validate([
        'vNombre' => 'required|string|max:100',
        'vApaterno' => 'nullable|string|max:100',
        'vAmaterno' => 'nullable|string|max:100',
        'vEmail' => 'required|email|unique:tbl_usuarios,vEmail',
    ]);

    // Generar contraseña aleatoria segura
    $passwordPlain = Str::random(10);

    $admin = \App\Models\Usuario::create([
        'vNombre' => $request->vNombre,
        'vApaterno' => $request->vApaterno,
        'vAmaterno' => $request->vAmaterno,
        'vEmail' => $request->vEmail,
        'vPassword' => Hash::make($passwordPlain),
        'eRol' => 'admin',
        'bActivo' => 1,
    ]);

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
