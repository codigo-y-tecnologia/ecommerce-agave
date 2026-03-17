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
            'modo_mantenimiento' => 'required',
            'email_soporte_superadmin' => 'required|email'
        ], [
            'modo_mantenimiento.required' => 'El campo de modo mantenimiento es obligatorio',
            'email_soporte_superadmin.required' => 'El email es obligatorio',
            'email_soporte_superadmin.email' => 'El correo electrónico debe tener un formato válido.',
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
