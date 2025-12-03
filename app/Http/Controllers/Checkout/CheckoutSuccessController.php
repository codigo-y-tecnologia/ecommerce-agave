<?php

namespace App\Http\Controllers\Checkout;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pago;

class CheckoutSuccessController extends Controller
{
    public function index(Request $request)
    {
        $session_id = $request->session_id;

        if (!$session_id) {
            return redirect()->route('carrito.index')
                ->with('warning', 'No se pudo validar la sesión de pago.');
        }

        // Buscar pago por session_id
        $pago = Pago::where('vSessionID', $session_id)->first();

        if (!$pago) {
            return redirect()->route('carrito.index')
                ->with('warning', 'No se encontró ningún pedido relacionado.');
        }

        return redirect()->route('order.received', $pago->id_pedido);
    }
}
