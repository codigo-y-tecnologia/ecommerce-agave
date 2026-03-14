<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Configuracion;
use Illuminate\Support\Facades\Cache;
use App\Services\System\SecurityLoggerService;

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

            $config = Configuracion::where('clave', $clave)->first();

            $oldValue = $config?->valor;

            Configuracion::updateOrCreate(
                ['clave' => $clave],
                ['valor' => $valor]
            );
        }

        Cache::forget('configuraciones_sistema');

        if ($oldValue != $valor) {
            SecurityLoggerService::configChanged(
                $clave,
                $oldValue,
                $valor
            );
        }

        return back()->with('success', 'Configuración actualizada correctamente');
    }
}
