<?php

namespace App\Http\Controllers\Checkout;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

use App\Models\{
    Carrito,
    Pedido,
    PedidoDetalle,
    Direccion,
    Cupon,
    CuponUso,
    Pago,
    Venta,
    DetalleVenta
};

use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Webhook as StripeWebhook;
use Stripe\PaymentIntent;

// PayPal (we will use Guzzle)
use GuzzleHttp\Client;

class PaymentController extends Controller 

{

    /**
     * Crea una Stripe Checkout Session y devuelve la URL (cliente redirige).
     */
    public function createStripeSession(Request $request)
{
    try {

        $user = Auth::user();

        // VALIDAR DIRECCION
        if (!$request->id_direccion) {
            return response()->json([
                'success' => false,
                'message' => 'Debes seleccionar una dirección.'
            ]);
        }

        // Guardar en sesión
        session([
            'id_direccion' => $request->id_direccion,
            'id_direccion_facturacion' => $request->id_direccion_facturacion ?? $request->id_direccion,
            'nota_pedido' => $request->nota ?? null
        ]);

        // Cargar carrito

        $carrito = Carrito::where('id_usuario', $user->id_usuario)
            ->where('eEstado', 'activo')
            ->with(['detalles.producto.impuestos'])
            ->first();

        if (!$carrito || $carrito->detalles->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Carrito vacío.']);
        }

        // Calcular totales
        [$subtotal, $totalImpuestos, $total] =
            (new \App\Http\Controllers\Checkout\CheckoutController)->calcularTotales($carrito);

        // Cupón
        $codigoCupon = session('codigo_cupon');
        $descuento = 0;
        $cupon = null;

        if ($codigoCupon) {
            $cupon = Cupon::whereRaw('BINARY vCodigo_cupon = ?', [$codigoCupon])
                ->where('bActivo', 1)
                ->first();

            if ($cupon) {
                if ($cupon->eTipo === 'porcentaje') {
                    $descuento = $total * ($cupon->dDescuento / 100);
                } else {
                    $descuento = $cupon->dDescuento;
                }
            }
        }

        // Envío
        $montoEnvioGratis = 1500;
        $costoEnvioFijo = 150;
        $envio = ($total >= $montoEnvioGratis) ? 0 : $costoEnvioFijo;

        if ($cupon && $cupon->vCodigo_cupon === 'ENVIOGRATIS') {
            $envio = 0;
        }

        $totalFinal = max(0, $total - $descuento + $envio);

        // Stripe
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $amountCents = (int) round($totalFinal * 100);

        // Metadata COMPLETA
        $metadata = [
            'user_id'        => $user->id_usuario,
            'carrito_id'     => $carrito->id_carrito,
            'id_direccion'   => $request->id_direccion,
            'id_direccion_facturacion' => $request->id_direccion_facturacion ?? $request->id_direccion,
            'nota_pedido' => $request->nota ?? '',
            'codigo_cupon'   => $codigoCupon ?? ''
        ];

        // Crear sesión Stripe
        $session = StripeSession::create([
    'mode' => 'payment',

    'line_items' => [[
        'price_data' => [
            'currency' => 'mxn',
            'product_data' => [
                'name' => 'Compra en ' . config('app.name', 'Tienda'),
            ],
            'unit_amount' => $amountCents,
        ],
        'quantity' => 1,
    ]],

    'metadata' => $metadata,

    'success_url' => route('checkout.index') . '?paid=1&payment=stripe&session_id={CHECKOUT_SESSION_ID}',
    'cancel_url' => route('checkout.index') . '?paid=0',
]);

        return response()->json([
            'success' => true,
            'url' => $session->url,
            'id' => $session->id
        ]);

    } catch (\Throwable $e) {

        Log::error("🔥 Stripe error: " . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Error al crear la sesión de pago.'
        ]);
    }
}

    /**
     * Endpoint público para recibir webhook de Stripe.
     * Recomiendo registrar este endpoint en el dashboard de Stripe con STRIPE_WEBHOOK_SECRET.
     */
    // public function stripeWebhook(Request $request)
    // {

    //     Log::info('🔔 Webhook de Stripe recibido', [
    //     'type' => $request->getContent() ? 'has content' : 'empty',
    //     'headers' => $request->headers->all()
    // ]);

    //     $payload = $request->getContent();
    //     $sigHeader = $request->header('Stripe-Signature');
    //     $webhookSecret = env('STRIPE_WEBHOOK_SECRET');

    //     try {
    //         $event = StripeWebhook::constructEvent($payload, $sigHeader, $webhookSecret);
    //     } catch (\UnexpectedValueException $e) {
    //         // Invalid payload
    //         return response('Invalid payload', 400);
    //     } catch (\Stripe\Exception\SignatureVerificationException $e) {
    //         // Invalid signature
    //         return response('Invalid signature', 400);
    //     }

    //     // Handle the checkout.session.completed event
    //     if ($event->type === 'checkout.session.completed') {
    //         $session = $event->data->object;

    //         // metadata previously attached
    //         $metadata = $session->metadata ?? null;
    //         $userId = $metadata->user_id ?? null;
    //         $carritoId = $metadata->carrito_id ?? null;
    //         $codigoCupon = $metadata->codigo_cupon ?? null;

    //         // Asegúrate de no duplicar: busca si ya existe un pago/venta con referencia de Stripe
    //         DB::transaction(function () use ($session, $userId, $carritoId, $codigoCupon) {
    //             // Finaliza el pedido / crea ventas aquí
    //             $this->finalizeOrderFromCart($userId, $carritoId, 'stripe', $session->id, $codigoCupon);
    //         });
    //     }

    //     return response('Received', 200);
    // }

    public function stripeWebhook(Request $request)
{
    Log::info('🔔 Webhook de Stripe recibido', [
        'type' => $request->getContent() ? 'has content' : 'empty',
        'headers' => $request->headers->all()
    ]);

    $payload = $request->getContent();
    $sigHeader = $request->header('Stripe-Signature');
    $webhookSecret = env('STRIPE_WEBHOOK_SECRET');

    Log::info('Webhook details', [
        'payload_length' => strlen($payload),
        'sig_header' => $sigHeader ? 'present' : 'missing',
        'webhook_secret' => $webhookSecret ? 'set' : 'not set'
    ]);

    try {
        $event = \Stripe\Webhook::constructEvent(
            $payload, $sigHeader, $webhookSecret
        );
        
        Log::info('✅ Evento de Stripe verificado', ['type' => $event->type]);

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $metadata = $session->metadata ?? null;
            
            Log::info('✅ checkout.session.completed', [
                'session_id' => $session->id,
                'metadata' => $metadata
            ]);

            DB::transaction(function () use ($session, $metadata) {
                $userId = $metadata->user_id ?? null;
                $carritoId = $metadata->carrito_id ?? null;
                $codigoCupon = $metadata->codigo_cupon ?? null;
                $idDireccion = $metadata->id_direccion ?? null;
                $notaPedido = $metadata->nota_pedido ?? null;

                $this->finalizeOrderFromCart($userId, $carritoId, 'stripe', $session->id, $codigoCupon, $idDireccion, $notaPedido);
                
                Log::info('✅ Pedido finalizado exitosamente');
            });
        }

        return response()->json(['received' => true]);

    } catch (\UnexpectedValueException $e) {
        Log::error('❌ Invalid payload en webhook', ['error' => $e->getMessage()]);
        return response('Invalid payload', 400);
    } catch (\Stripe\Exception\SignatureVerificationException $e) {
        Log::error('❌ Invalid signature en webhook', ['error' => $e->getMessage()]);
        return response('Invalid signature', 400);
    } catch (\Exception $e) {
        Log::error('❌ Error general en webhook', ['error' => $e->getMessage()]);
        return response('Error', 500);
    }
}

    /**
     * Crea una orden PayPal (server side) y devuelve id (cliente usa approve).
     */
    public function createPaypalOrder(Request $request)
    {
        $user = Auth::user();

        // VALIDAR DIRECCION
        if (!$request->id_direccion) {
            return response()->json([
                'success' => false,
                'message' => 'Debes seleccionar una dirección.'
            ]);
        }

        $carrito = Carrito::where('id_usuario', $user->id_usuario)
            ->where('eEstado', 'activo')
            ->with(['detalles.producto.impuestos'])
            ->first();


        if (!$carrito || $carrito->detalles->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Carrito vacío.'], 400);
        }

        // Guardar en sesión
        session([
            'id_direccion' => $request->id_direccion,
            'id_direccion_facturacion' => $request->id_direccion_facturacion ?? $request->id_direccion,
            'nota_pedido' => $request->nota ?? null
        ]);

        // Recalcular totales (misma lógica)
        [$subtotal, $totalImpuestos, $total] = (new \App\Http\Controllers\Checkout\CheckoutController)->calcularTotales($carrito);

        $codigoCupon = session('codigo_cupon');
        $descuento = 0;
        $cupon = null;
        if ($codigoCupon) {
            $cupon = Cupon::whereRaw('BINARY vCodigo_cupon = ?', [$codigoCupon])->where('bActivo',1)
                ->whereDate('dValido_desde','<=', now())
                ->whereDate('dValido_hasta','>=', now())
                ->first();
            if ($cupon && $cupon->vCodigo_cupon !== 'ENVIOGRATIS') {
                $descuento = ($cupon->eTipo === 'porcentaje') ? $total * ($cupon->dDescuento / 100) : $cupon->dDescuento;
            }
        }

        $montoEnvioGratis = 1500;
        $costoEnvioFijo = 150;
        $envio = ($total >= $montoEnvioGratis) ? 0 : $costoEnvioFijo;
        if ($cupon && $cupon->vCodigo_cupon === 'ENVIOGRATIS') $envio = 0;

        $totalFinal = max(0, $total - $descuento + $envio);

        // Crear orden en PayPal
        $client = new Client();
        $base = env('PAYPAL_MODE', 'sandbox') === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';

        try {
            // Get access token
            $resp = $client->post($base . '/v1/oauth2/token', [
                'auth' => [env('PAYPAL_CLIENT_ID'), env('PAYPAL_CLIENT_SECRET')],
                'form_params' => ['grant_type' => 'client_credentials']
            ]);
            $tokenData = json_decode((string) $resp->getBody(), true);
            $accessToken = $tokenData['access_token'];

            // Create order
            $orderResp = $client->post($base . '/v2/checkout/orders', [
                'headers' => [
                    'Authorization' => "Bearer {$accessToken}",
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'intent' => 'CAPTURE',
                    'purchase_units' => [
                        [
                            'amount' => [
                                'currency_code' => 'MXN',
                                'value' => number_format($totalFinal, 2, '.', '')
                            ],
                            'description' => 'Compra en ' . config('app.name', 'Tienda')
                        ]
                    ],
                    'application_context' => [
                        'return_url' => route('checkout.index') . '?paid=1&payment=paypal',
                        'cancel_url' => route('checkout.index') . '?paid=0'
                    ]
                ]
            ]);

            $order = json_decode((string) $orderResp->getBody(), true);

            return response()->json(['success' => true, 'orderID' => $order['id']]);
        } catch (\Throwable $e) {
            Log::error('PayPal create order: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al crear orden PayPal'], 500);
        }
    }

    /**
     * Captura la orden PayPal (llamada por cliente tras approve).
     * Luego finaliza la orden (crea pedido/venta/pago).
     */
    public function capturePaypalOrder(Request $request)
    {
        $orderId = $request->orderID;
        $user = Auth::user();

        if (!$orderId) {
            return response()->json(['success' => false, 'message' => 'orderID requerido'], 400);
        }

        // Obtener id_direccion de la sesión
        $idDireccion = session('id_direccion_paypal');

        $client = new Client();
        $base = env('PAYPAL_MODE', 'sandbox') === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';

        try {
            // token
            $resp = $client->post($base . '/v1/oauth2/token', [
                'auth' => [env('PAYPAL_CLIENT_ID'), env('PAYPAL_CLIENT_SECRET')],
                'form_params' => ['grant_type' => 'client_credentials']
            ]);
            $tokenData = json_decode((string) $resp->getBody(), true);
            $accessToken = $tokenData['access_token'];

            // capture
            $capResp = $client->post($base . "/v2/checkout/orders/{$orderId}/capture", [
                'headers' => [
                    'Authorization' => "Bearer {$accessToken}",
                    'Content-Type' => 'application/json'
                ]
            ]);

            $capData = json_decode((string) $capResp->getBody(), true);

            // Aquí puedes obtener detalles como captura id y status
            $status = $capData['status'] ?? null;
            $captureId = $capData['purchase_units'][0]['payments']['captures'][0]['id'] ?? null;
            $amount = $capData['purchase_units'][0]['payments']['captures'][0]['amount']['value'] ?? null;

            // Finalizar pedido localmente (crear Pedido, Venta, Pago, etc.)
            DB::transaction(function () use ($user, $orderId, $captureId, $idDireccion) {
                // buscamos carrito id por user
                $carrito = Carrito::where('id_usuario', $user->id_usuario)
                    ->where('eEstado', 'activo')
                    ->with(['detalles.producto.impuestos'])
                    ->firstOrFail();

                $this->finalizeOrderFromCart($user->id_usuario, $carrito->id_carrito, 'paypal', $captureId, session('codigo_cupon') ?? null, $idDireccion);
            });

             // Limpiar sesión
            session()->forget('id_direccion_paypal');

            return response()->json(['success' => true, 'capture' => $captureId, 'status' => $status]);
        } catch (\Throwable $e) {
            Log::error('PayPal capture error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error capturando orden PayPal.'], 500);
        }
    }

private function finalizeOrderFromCart($userId, $carritoId, $method, $reference, $codigoCupon = null, $idDireccion = null, $notaPedido = null)
    {
        // Buscar usuario y carrito
        $carrito = Carrito::where('id_carrito', $carritoId)
            ->with(['detalles.producto.impuestos'])
            ->firstOrFail();

        $userId = $userId ?? $carrito->id_usuario;

        // recalcular totales con la misma lógica
        [$subtotal, $totalImpuestos, $total] = (new \App\Http\Controllers\Checkout\CheckoutController)->calcularTotales($carrito);

        $montoEnvioGratis = 1500;
        $costoEnvioFijo = 150;
        $envio = ($total >= $montoEnvioGratis) ? 0 : $costoEnvioFijo;

        $descuento = 0;
        $cupon = null;

        if ($codigoCupon) {
            $cupon = Cupon::whereRaw('BINARY vCodigo_cupon = ?', [$codigoCupon])->where('bActivo',1)->first();

            if ($cupon) {

                // ✅ VERIFICAR USOS ACTUALES vs USOS MÁXIMOS
            $usosActuales = CuponUso::where('id_cupon', $cupon->id_cupon)->count();

             if ($usosActuales >= $cupon->iUso_maximo) {
                Log::warning("Cupón {$codigoCupon} excedió el límite de usos ({$usosActuales}/{$cupon->iUso_maximo})");
                $cupon = null; // No aplicar el cupón
            } else {

                if ($cupon->vCodigo_cupon === 'ENVIOGRATIS') {
                    $envio = 0;
                } else {
                    $descuento = ($cupon->eTipo === 'porcentaje') ? $total * ($cupon->dDescuento / 100) : $cupon->dDescuento;
                }
            }
        }
}

        $totalFinal = max(0, $total - $descuento + $envio);

        // Crear pedido + detalles + venta + pago en una transacción
        $pedido = Pedido::create([
            'id_usuario' => $userId,
            'id_direccion' => $idDireccion, 
            'eEstado' => 'pagado',
            'dTotal' => $totalFinal,
        ]);

        foreach ($carrito->detalles as $detalle) {
            PedidoDetalle::create([
                'id_pedido' => $pedido->id_pedido,
                'id_producto' => $detalle->id_producto,
                'iCantidad' => $detalle->cantidad,
                'dPrecio_unitario' => $detalle->precio_unitario,
            ]);
        }

        // ========================================
        // DESCONTAR STOCK DEL PRODUCTO (iStock)
        // ========================================
foreach ($carrito->detalles as $detalle) {

    $producto = \App\Models\Producto::find($detalle->id_producto);

    if ($producto) {

        // Validación para evitar inventario negativo
        if ($producto->iStock < $detalle->cantidad) {
            throw new Exception("Inventario insuficiente para el producto: {$producto->vNombre}");
        }

        // Descontar inventario
        $producto->iStock -= $detalle->cantidad;
        $producto->save();
    }
}

        // Crear Venta
        $venta = Venta::create([
            'id_pedido' => $pedido->id_pedido,
            'id_usuario' => $userId,
            'dTotal' => $totalFinal,
            'eMetodo_pago' => $method, 
        ]);

        // Detalle de venta (por cada pedido_detalle)
        foreach ($carrito->detalles as $detalle) {
            DetalleVenta::create([
                'id_venta' => $venta->id_venta,
                'id_producto' => $detalle->id_producto,
                'iCantidad' => $detalle->cantidad,
                'dPrecio_unitario' => $detalle->precio_unitario,
            ]);
        }

        // Registrar pago
        $pago = Pago::create([
            'id_pedido' => $pedido->id_pedido,
            'eMetodo_pago' => $method,   
            'dMonto' => $totalFinal,
            'eEstado' => 'exitoso',   
            //'vReferencia' => $reference,
        ]);

        // Cupon uso
        if ($cupon) {

            // ✅ VERIFICAR UNA VEZ MÁS ANTES DE CREAR EL USO (por si hay race conditions)
        $usosActuales = CuponUso::where('id_cupon', $cupon->id_cupon)->count();

        if ($usosActuales < $cupon->iUso_maximo) {
            CuponUso::create([
                'id_cupon' => $cupon->id_cupon,
                'id_venta' => $venta->id_venta,
            ]);

            $nuevosUsos = $usosActuales + 1;
            Log::info("✅ Cupón {$cupon->vCodigo_cupon} aplicado. Usos: {$nuevosUsos}/{$cupon->iUso_maximo}");

            // Desactivar el cupón si alcanzó el límite
            if ($nuevosUsos >= $cupon->iUso_maximo) {
                $cupon->update(['bActivo' => 0]);
                Log::info("🔒 Cupón {$cupon->vCodigo_cupon} desactivado por alcanzar el límite de usos");
            }
        } else {
            Log::warning("Cupón {$cupon->vCodigo_cupon} ya no tiene usos disponibles al momento de guardar");
        }
        }
    
        // Limpiar carrito: borrar detalles y marcar carrito convertido
        $carrito->detalles()->delete();
        $carrito->eEstado = 'convertido';
        $carrito->save();

        // eliminar cupón de session
        session()->forget('codigo_cupon');

        return true;
    }
}
