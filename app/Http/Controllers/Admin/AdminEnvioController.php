<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pedido;

class AdminEnvioController extends Controller
{
    public function store(Request $request, Pedido $pedido)
{
    abort_if($pedido->envio, 403);

    $request->validate([
        'vTransportista' => 'required|string|max:80',
        'vNumero_guia'   => 'required|string|max:80',
    ]);

    $pedido->envio()->create([
        'vTransportista' => $request->vTransportista,
        'vNumero_guia'   => $request->vNumero_guia,
        'eEstado'        => 'pendiente',
    ]);

    return back()->with('success', 'Envío creado correctamente');
    }

}
