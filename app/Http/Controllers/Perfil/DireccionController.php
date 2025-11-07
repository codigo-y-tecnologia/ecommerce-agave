<?php

namespace App\Http\Controllers\Perfil;
use App\Http\Controllers\Controller;
use App\Models\Direccion;
use Illuminate\Support\Facades\Auth;
use App\Traits\InputSanitizer;

use Illuminate\Http\Request;

class DireccionController extends Controller
{

    use InputSanitizer;

    /**
     * Mostrar lista de direcciones del usuario
     */
    public function index()
    {
        $usuario = Auth::user();
        $direcciones = Direccion::where('id_usuario', $usuario->id_usuario)->get();

        return view('perfil.direcciones', compact('direcciones'));
    }

    /**
     * Crear nueva dirección
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'vTelefono_contacto' => 'required|string|max:20',
            'vCalle' => 'required|string|max:150',
            'vNumero_exterior' => 'nullable|string|max:20',
            'vNumero_interior' => 'nullable|string|max:20',
            'vColonia' => 'nullable|string|max:150',
            'vCodigo_postal' => 'nullable|string|max:10',
            'vCiudad' => 'nullable|string|max:80',
            'vEstado' => 'nullable|string|max:80',
            'vEntre_calle_1' => 'nullable|string|max:150',
            'vEntre_calle_2' => 'nullable|string|max:150',
            'tReferencias' => 'nullable|string',
            'bDireccion_principal' => 'nullable|boolean',
        ]);

        $this->verificarYLimpiar($data, config('security.sql_keywords'));

        $data['id_usuario'] = Auth::user()->id_usuario;
        $data['bDireccion_principal'] = $request->has('bDireccion_principal') ? 1 : 0;

        // 🔹 Si marca esta como principal, desmarcar las otras
        if ($data['bDireccion_principal']) {
            Direccion::where('id_usuario', Auth::user()->id_usuario)
                ->update(['bDireccion_principal' => 0]);
        }

        $direccion = Direccion::create($data);

        return response()->json(['success' => true, 'direccion' => $direccion]);
    }

    /**
     * Actualizar dirección
     */
    public function update(Request $request, $id)
    {
        $direccion = Direccion::where('id_direccion', $id)
            ->where('id_usuario', Auth::user()->id_usuario)
            ->firstOrFail();

        $data = $request->validate([
            'vTelefono_contacto' => 'required|string|max:20',
            'vCalle' => 'required|string|max:150',
            'vNumero_exterior' => 'nullable|string|max:20',
            'vNumero_interior' => 'nullable|string|max:20',
            'vColonia' => 'nullable|string|max:150',
            'vCodigo_postal' => 'nullable|string|max:10',
            'vCiudad' => 'nullable|string|max:80',
            'vEstado' => 'nullable|string|max:80',
            'vEntre_calle_1' => 'nullable|string|max:150',
            'vEntre_calle_2' => 'nullable|string|max:150',
            'tReferencias' => 'nullable|string',
            'bDireccion_principal' => 'nullable|boolean',
        ]);

        $this->verificarYLimpiar($data, config('security.sql_keywords'));

        $data['bDireccion_principal'] = $request->has('bDireccion_principal') ? 1 : 0;

        // 🔹 Si el usuario marca esta como principal, desmarcar las demás
        if ($data['bDireccion_principal']) {
            Direccion::where('id_usuario', Auth::user()->id_usuario)
                ->where('id_direccion', '!=', $id)
                ->update(['bDireccion_principal' => 0]);
        }

        $direccion->update($data);

        return response()->json(['success' => true, 'direccion' => $direccion]);
    }

    /**
     * Eliminar dirección
     */
    public function destroy($id)
    {
        $direccion = Direccion::where('id_direccion', $id)
            ->where('id_usuario', Auth::user()->id_usuario)
            ->firstOrFail();

        $direccion->delete();

        return response()->json(['success' => true]);
    }
}
