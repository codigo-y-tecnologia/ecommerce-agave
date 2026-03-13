<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Setting, Configuracion};
use Illuminate\Support\Facades\Cache;
use App\Services\System\SettingsService;

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

    public function updateShipping(Request $request)
    {
        $request->validate([
            'envio_estandar' => 'required|numeric|min:0',
            'envio_gratis' => 'required|numeric|min:0',
        ]);

        Configuracion::updateOrCreate(
            ['clave' => 'costo_de_envio'],
            ['valor' => $request->envio_estandar]
        );

        Configuracion::updateOrCreate(
            ['clave' => 'envio_gratis_desde'],
            ['valor' => $request->envio_gratis]
        );

        Cache::forget('configuraciones_sistema');

        return back()->with('success', 'Configuración de envíos actualizada');
    }
}
