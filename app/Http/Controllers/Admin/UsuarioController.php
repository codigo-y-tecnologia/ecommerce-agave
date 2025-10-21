<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Traits\InputSanitizer;

class UsuarioController extends Controller
{

    use InputSanitizer;

    /**
    * Mostrar lista de usuarios
    */
    public function index()
    {
        $usuarios = Usuario::orderBy('id_usuario', 'desc')->get();
        return view('admin.usuarios.index', compact('usuarios'));
    }

    //Editar usuario (mostrar formulario)
    public function edit($id)
    {
        $usuario = Usuario::findOrFail($id);
        return view('admin.usuarios.edit', compact('usuario'));
    }

    // Actualizar usuario (guardar cambios)
    public function update(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);

        // Validación
        $data = $request->validate([
            'vNombre' => ['required', 'string', 'max:60', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/u'],
            'vEmail' => ['required', 'email', 'max:100', 'unique:tbl_usuarios,vEmail'],
            'eRol' => ['required', 'in:cliente,admin,superadmin'],
        ], [
            // Mensajes personalizados claros
            'regex' => 'El campo :attribute solo puede contener letras y espacios.',
            'vEmail.email' => 'El correo electrónico debe tener un formato válido.',
            'unique' => 'El correo electrónico ya está en uso por otro usuario.',
        ]);

        // Funciones de limpieza y detección
        $this->verificarYLimpiar($data, config('security.sql_keywords'));

        $usuario->update($request->only(['vNombre', 'vEmail', 'eRol']));

        return redirect()->route('admin.usuarios')->with('success', 'Usuario actualizado correctamente.');
    }

    //Eliminar usuario
    public function destroy($id)
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->delete();

        return redirect()->route('admin.usuarios')->with('success', 'Usuario eliminado correctamente.');
    }
}
