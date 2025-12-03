<?php

namespace App\Http\Controllers\Checkout;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\Direccion;
use Illuminate\Support\Facades\Auth;

class OrderReceivedController extends Controller
{
    public function show($id)
    {
        $pedido = Pedido::with(['detalles.producto', 'usuario'])
            ->where('id_usuario', Auth::user()->id_usuario)
            ->findOrFail($id);

        $direccion = Direccion::find($pedido->id_direccion);

        return view('checkout.order-received', [
            'pedido' => $pedido,
            'direccion' => $direccion,
            'payment_method' => $pedido->venta->eMetodo_pago,
            'nota_pedido' => $pedido->nota ?? null,
        ]);
    }
}
