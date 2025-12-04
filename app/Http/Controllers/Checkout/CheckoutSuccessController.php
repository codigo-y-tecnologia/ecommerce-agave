<?php

namespace App\Http\Controllers\Checkout;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pago;
use Illuminate\Support\Facades\Log;

class CheckoutSuccessController extends Controller
{
    public function index(Request $request)
    {
         $session_id = $request->query('session_id');

         Log::info('🎯 Llegó a checkout.success', [
            'session_id' => $session_id,
            'all_params' => $request->all()
        ]);

        if (!$session_id) {
            Log::warning('⚠️ No se recibió session_id');
            return redirect()->route('carrito.index')
                ->with('warning', 'No se pudo validar la sesión de pago.');
        }

        // Esperar un momento para asegurar que el webhook ya procesó
        sleep(2);

        // Buscar pago por session_id
        $pago = Pago::where('vSessionID', $session_id)->first();

        if (!$pago) {
            Log::warning('⚠️ No se encontró pago con session_id: ' . $session_id);

            // Intentar buscar de nuevo después de un momento
            sleep(3);
            $pago = Pago::where('vSessionID', $session_id)->first();

            if (!$pago) {
                return redirect()->route('carrito.index')
                    ->with('warning', 'No se encontró ningún pedido relacionado. Si realizaste un pago, contacta a soporte.');
            }
        }

        Log::info('✅ Pago encontrado, redirigiendo a order.received', [
            'id_pedido' => $pago->id_pedido
        ]);

        session()->forget('carrito');
        session()->forget('carrito_detalles');

        return redirect()->route('order.received', $pago->id_pedido);
    }
}
