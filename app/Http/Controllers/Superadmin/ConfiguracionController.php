<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Configuracion;
use Illuminate\Support\Facades\Cache;

class ConfiguracionController extends Controller
{
    public function index()
    {
        $configs = Configuracion::pluck('valor', 'clave')->toArray();

        return view('superadmin.configuracion.index', compact('configs'));
    }

    public function update(Request $request)
    {

        $request->validate([
            'nombre_tienda' => 'required|string|max:255',
            'email_soporte' => 'required|email|max:255',
            'telefono' => 'required|string|max:20',
            'moneda' => 'required|string|max:10',
            'envio_estandar' => 'required|numeric|min:0',
            'envio_gratis' => 'required|numeric|min:0',
            'iva' => 'required|numeric|min:0|max:100',
            'modo_mantenimiento' => 'required'
        ], [
            'required' => 'Este campo es obligatorio',
            'email' => 'Debe ser un email válido',
            'numeric' => 'Debe ser un número'
        ]);

        foreach ($request->except('_token') as $clave => $valor) {

            Configuracion::updateOrCreate(
                ['clave' => $clave],
                ['valor' => $valor]
            );
        }

        Cache::forget('configuraciones_sistema');

        return back()->with('success', 'Configuración actualizada correctamente');
    }
}
