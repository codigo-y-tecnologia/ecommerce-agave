<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        return view('admin.settings.index');
    }

    public function updateAutoRegister(Request $request)
    {
        Setting::updateOrCreate(
            ['key' => 'auto_register_guest_after_purchase'],
            ['value' => $request->boolean('value')]
        );

        return back()->with('success', 'Configuración actualizada');
    }

    public function updateAllowReturns(Request $request)
    {
        Setting::updateOrCreate(
            ['key' => 'allow_order_returns'],
            ['value' => $request->boolean('value')]
        );

        return back()->with(
            'success',
            'Configuración de postventa actualizada'
        );
    }
}
