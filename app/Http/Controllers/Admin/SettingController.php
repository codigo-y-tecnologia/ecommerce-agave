<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Configuracion;
use Illuminate\Support\Facades\Cache;
use App\Services\System\SettingsService;
use App\Services\System\SecurityLoggerService;

class SettingController extends Controller
{
    public function index()
    {
        return view('admin.settings.index');
    }

    public function updateAutoRegister(Request $request)
    {

        app(SettingsService::class)->set(
            'auto_register_guest_after_purchase',
            $request->boolean('value')
        );

        return back()->with('success', 'Configuración actualizada');
    }

    public function updateAllowReturns(Request $request)
    {

        app(SettingsService::class)->set(
            'allow_order_returns',
            $request->boolean('value')
        );

        return back()->with(
            'success',
            'Configuración de postventa actualizada'
        );
    }

    public function updateStore(Request $request)
    {
        $request->validate([
            'nombre_tienda' => 'required|string|max:255',
            'email_soporte' => 'required|email|max:255',
            'telefono' => 'required|string|max:20',
            'moneda' => 'required|string|max:10',
            'costo_de_envio' => 'required|numeric|min:0',
            'envio_gratis_desde' => 'required|numeric|min:0',
        ], [
            'email' => 'Debe ser un email válido',
            'numeric' => 'Debe ser un número'
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

        return back()->with('success', 'Configuración actualizada');
    }
}
