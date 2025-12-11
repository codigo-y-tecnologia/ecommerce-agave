<?php

namespace App\Http\Controllers\Checkout;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CheckoutErrorController extends Controller
{
    public function index(Request $request)
    {
        $mensaje = $request->query('msg') ?? 'Ocurrió un problema al procesar tu pago. No se generó ningún pedido.';

        return view('checkout.payment-error', [
            'mensaje' => $mensaje
        ]);
    }
}
