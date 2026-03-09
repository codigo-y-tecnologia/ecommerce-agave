<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Traits\InputSanitizer;
use Illuminate\Support\Facades\View;

class UsuarioController extends Controller
{

    use InputSanitizer;

    /**
     * Mostrar lista de usuarios
     */
    public function index(Request $request)
    {
        // Filtrar solo clientes
        $query = trim($request->get('q'));

        $usuarios = Usuario::role('cliente')
            ->when($query, function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('vNombre', 'like', "%{$query}%")
                        ->orWhere('vApaterno', 'like', "%{$query}%")
                        ->orWhere('vAmaterno', 'like', "%{$query}%")
                        ->orWhere('vEmail', 'like', "%{$query}%");
                });
            })
            ->orderBy('id_usuario', 'desc')
            ->paginate(8);

        if ($request->ajax()) {
            $html = View::make('admin.usuarios.partials.table', compact('usuarios'))->render();
            return response()->json(['html' => $html]);
        }

        return view('admin.usuarios.index', compact('usuarios'));
    }

    //Editar usuario (mostrar formulario)
    public function edit($id)
    {
        $usuario = Usuario::role('cliente')
            ->where('id_usuario', $id)
            ->firstOrFail();

        return view('admin.usuarios.edit', compact('usuario'));
    }

    // Actualizar usuario (guardar cambios)
    public function update(Request $request, $id)
    {
        $usuario = Usuario::role('cliente')
            ->where('id_usuario', $id)
            ->firstOrFail();

        // Validación
        $data = $request->validate([
            'vNombre' => ['required', 'string', 'max:60', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/u'],
            'vApaterno' => ['required', 'string', 'max:50', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/u'],
            'vAmaterno' => ['required', 'string', 'max:50', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/u'],
            'vEmail' => ['required', 'email', 'max:100'],
        ], [
            // Mensajes personalizados claros
            'regex' => 'El campo :attribute solo puede contener letras y espacios.',
            'vEmail.email' => 'El correo electrónico debe tener un formato válido.',
        ]);

        $usuario->update([
            'vNombre' => $data['vNombre'],
            'vApaterno' => $data['vApaterno'],
            'vAmaterno' => $data['vAmaterno'],
            'vEmail' => $data['vEmail'],
        ]);

        return redirect()->route('admin.usuarios')->with('success', 'Cliente actualizado correctamente.');
    }

    //Eliminar usuario
    public function destroy($id)
    {
        $usuario = Usuario::role('cliente')
            ->where('id_usuario', $id)
            ->firstOrFail();

        $usuario->delete();

        return redirect()->route('admin.usuarios')
            ->with('success', 'Cliente eliminado correctamente.');
    }
}
