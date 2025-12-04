<?php

namespace App\Http\Controllers\Checkout;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\Direccion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderReceivedController extends Controller
{
    public function show($id)
    {

        Log::info('📦 Mostrando order-received', ['id_pedido' => $id]);

        $pedido = Pedido::with(['detalles.producto', 'usuario', 'venta'])
            ->where('id_usuario', Auth::user()->id_usuario)
            ->findOrFail($id);

        $direccion = Direccion::find($pedido->id_direccion);

        // Obtener método de pago desde la venta
        $payment_method = $pedido->venta->eMetodo_pago ?? 'No disponible';

        return view('checkout.order-received', [
            'pedido' => $pedido,
            'direccion' => $direccion,
            'payment_method' => $payment_method,
            'nota_pedido' => $pedido->nota ?? null,
        ]);
    }
}
