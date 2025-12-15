<?php

namespace App\Http\Controllers\Checkout;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaypalSuccesController extends Controller
{
    public function index(Request $request)
    {
        $pedidoId = session('ultimo_pedido_id');

        Log::info('🅿️ PayPal success callback', [
            'pedido_id' => $pedidoId
        ]);

        if (!$pedidoId) {
            Log::warning('⚠️ PayPal success sin pedido en sesión');

            return redirect()->route('checkout.error', [
                'msg' => 'El pago fue aprobado, pero no pudimos recuperar tu pedido. Contacta a soporte.'
            ]);
        }

        // Limpiar sesión (igual que Stripe)
        session()->forget('carrito');
        session()->forget('carrito_detalles');
        session()->forget('codigo_cupon');
        session()->forget('ultimo_pedido_id');

        return redirect()->route('order.received', $pedidoId);
    }
}
