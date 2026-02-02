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
            return redirect()->route('session.error');
        }

        // Intentos de esperar al webhook (máx 10 segundos)
        $pago = null;

        for ($i = 0; $i < 10; $i++) {

            $pago = Pago::where('vSessionID', $session_id)->first();

            if ($pago) {
                Log::info("✅ Pago encontrado después de $i segundo(s)", [
                    'pago_id' => $pago->id_pago,
                    'pedido_id' => $pago->id_pedido
                ]);
                break;
            }
            sleep(1);
        }

        if (!$pago) {
            Log::warning('⚠️ No se encontró pago con session_id: ' . $session_id);

            // Intentar buscar de nuevo después de un momento
            sleep(3);
            $pago = Pago::where('vSessionID', $session_id)->first();

            if (!$pago) {
                return redirect()->route('checkout.error', [
                    'msg' => 'Tu pago fue procesado, pero no pudimos generar tu pedido. Si realizaste un cargo, contacta a soporte.'
                ]);
            }
        }

        Log::info('✅ Pago encontrado, redirigiendo a order.received', [
            'id_pedido' => $pago->id_pedido
        ]);

        session()->forget('carrito');
        session()->forget('carrito_detalles');
        session()->forget('codigo_cupon');

        return redirect()->route('order.received', $pago->id_pedido);
    }
}
