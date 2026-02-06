<?php

namespace App\Http\Controllers\Checkout;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pago;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

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

        // Inicializar Stripe
        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            // Recuperar la sesión de Stripe
            $session = StripeSession::retrieve($session_id);

            // Stripe SIEMPRE garantiza este campo
            $paymentIntent = $session->payment_intent;

            Log::info('🔍 Checkout success recibido', [
                'session_id' => $session_id,
                'payment_intent' => $paymentIntent
            ]);
        } catch (\Exception $e) {
            Log::error('❌ Error recuperando sesión de Stripe', [
                'error' => $e->getMessage()
            ]);

            return redirect()->route('checkout.error', [
                'msg' => 'No se pudo verificar el pago.'
            ]);
        }

        // Intentos de esperar al webhook (máx 10 segundos)
        $pago = null;

        for ($i = 0; $i < 10; $i++) {

            $pago = Pago::where('vReferencia', $paymentIntent)->first();

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
            Log::error('❌ Pago no encontrado tras esperar webhook', [
                'payment_intent' => $paymentIntent
            ]);

            // Intentar buscar de nuevo después de un momento
            sleep(3);
            $pago = Pago::where('vReferencia', $paymentIntent)->first();

            if (!$pago) {
                return redirect()->route('checkout.error', [
                    'msg' => 'Estamos confirmando tu pago. Si ya se realizó el cargo, contáctanos para asistencia.'
                ]);
            }
        }

        // CASO: PAGO REEMBOLSADO POR FALTA DE STOCK
        if ($pago->eEstado === 'reembolsado') {

            Log::info('🔄 Redirigiendo a checkout.payment-refunded', [
                'payment_intent' => $paymentIntent
            ]);

            return redirect()
                ->route('checkout.payment-refunded')
                ->with([
                    'message' => 'El producto se quedó sin stock. Tu pago fue reembolsado automáticamente.'
                ]);
        }

        // ✅ CASO: PEDIDO GENERADO CORRECTAMENTE
        if ($pago->id_pedido) {

            Log::info('📦 Pedido confirmado', [
                'id_pedido' => $pago->id_pedido
            ]);

            // Limpiar carrito
            session()->forget('carrito');
            session()->forget('carrito_detalles');
            session()->forget('codigo_cupon');

            return redirect()->route('order.received', [
                'id' => $pago->id_pedido
            ]);
        }

        // 🟡 Caso inesperado
        Log::warning('⚠️ Estado de pago no reconocido', [
            'pago_id' => $pago->id,
            'estado' => $pago->eEstado
        ]);

        return redirect()->route('checkout.error', [
            'msg' => 'No se pudo completar el pedido.'
        ]);
    }
}
