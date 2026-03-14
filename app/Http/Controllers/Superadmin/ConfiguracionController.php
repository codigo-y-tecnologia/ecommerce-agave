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
            'modo_mantenimiento' => 'required'
        ], [
            'required' => 'Este campo es obligatorio',
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
