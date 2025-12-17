<?php

namespace App\Http\Controllers\Checkout;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
                    'message' => 'Debes seleccionar una dirección de envío.'
                ], 400);
            }

            // VALIDAR DIRECCIÓN DE FACTURACIÓN según checkbox
            $usarMisma = $request->has('misma_direccion_facturacion') && $request->misma_direccion_facturacion == 'on';
            if (!$usarMisma) {
                if (!$request->id_direccion_facturacion) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Debes seleccionar una dirección de facturación.'
                    ], 400);
                }
            }

        // Guardar en sesión
        session([
            'id_direccion' => $request->id_direccion,
            'id_direccion_facturacion' => $usarMisma
                    ? $request->id_direccion
                    : $request->id_direccion_facturacion,
            'nota_pedido' => $request->nota ?? null
        ]);

        // Cargar carrito
        $carrito = Carrito::where('id_usuario', $user->id_usuario)
            ->where('eEstado', 'activo')
            ->with(['detalles.producto.impuestos'])
            ->first();

        if (!$carrito || $carrito->detalles->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Carrito vacío.'
            ], 400);
        }

        // VALIDAR STOCK ANTES DE CREAR SESIÓN STRIPE
        try {
            $this->validateCartStock($carrito);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
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

            if ($cupon && $cupon->vCodigo_cupon !== 'ENVIOGRATIS') {
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

        if ($cupon && $cupon->vCodigo_cupon === 'enviogratis') {
            $envio = 0;
        }

        $totalFinal = max(0, $total - $descuento + $envio);

        // Stripe
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $amountCents = (int) round($totalFinal * 100);

        // Metadata COMPLETA
        $metadata = [
            'user_id' => $user->id_usuario,
            'carrito_id' => $carrito->id_carrito,
            'id_direccion' => $request->id_direccion,
            'id_direccion_facturacion' => session('id_direccion_facturacion'),
            'nota_pedido' => session('nota_pedido') ?? '',
            'codigo_cupon' => $codigoCupon ?? ''
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
                    'tax_behavior' => 'inclusive',
                ],
                'quantity' => 1,
            ]],
            'metadata' => $metadata,
            'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
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
        ], 500);
    }
}
    /**
     * Maneja el webhook de Stripe para pagos completados.
     */
public function stripeWebhook(Request $request)
{
    Log::info('Webhook de Stripe recibido', [
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
            $payload,
            $sigHeader,
            $webhookSecret
        );

        Log::info('Evento de Stripe verificado', ['type' => $event->type]);

        if ($event->type === 'checkout.session.completed') {

            $session = $event->data->object;
            $metadata = $session->metadata ?? null;

            Log::info('checkout.session.completed', [
                'session_id' => $session->id,
                'metadata' => $metadata
            ]);

            if ($session->payment_status === 'paid') {

                // $reference = $session->id;
                $reference = $session->payment_intent;

                // 🔒 PROTECCIÓN IDEMPOTENTE (EVENTOS DUPLICADOS)
                if (Pago::where('vReferencia', $reference)->exists()) {
                    Log::warning('⚠️ Pago Stripe ya procesado. Evento duplicado ignorado.', [
                        'payment_intent' => $reference,
                        'session_id' => $session->id,
                    ]);

                    // Stripe espera 200 OK
                    return response()->json(['status' => 'already_processed'], 200);
                }

                DB::transaction(function () use ($session, $metadata, $reference) {
                    $userId = $metadata->user_id ?? null;
                    $carritoId = $metadata->carrito_id ?? null;
                    $codigoCupon = $metadata->codigo_cupon ?? null;
                    $idDireccion = $metadata->id_direccion ?? null;
                    $idDireccionFact = $metadata->id_direccion_facturacion ?? $idDireccion;
                    $notaPedido = $metadata->nota_pedido ?? null;

                    Log::info('Referencia a guardar: ' . $reference);

                    // Re-validate stock using carrito id from metadata BEFORE finalizing
                        if ($carritoId) {
                            $carrito = Carrito::where('id_carrito', $carritoId)
                                ->where('eEstado', 'activo')
                                ->with(['detalles.producto.impuestos'])
                                ->first();

                            if (!$carrito) {
                                throw new Exception("Carrito no encontrado para finalizar pedido (Stripe webhook).");
                            }

                            // Validate stock and abort transaction if not valid
                            $this->validateCartStock($carrito);
                        } else {
                            Log::warning('No se encontró carrito_id en metadata de Stripe.');
                        }

                    $this->finalizeOrderFromCart(
                        $userId,
                        $carritoId,
                        'stripe',
                        $reference,
                        $codigoCupon,
                        $idDireccion,
                        $idDireccionFact,
                        $notaPedido,
                        $session->id
                    );

                    Log::info('Pedido finalizado exitosamente');
                });

            } else {
                Log::warning('La sesión no está pagada', [
                    'session_id' => $session->id,
                    'payment_status' => $session->payment_status
                ]);
            }
        }

        return response()->json(['received' => true]);

    } catch (\UnexpectedValueException $e) {
        Log::error('Invalid payload en webhook', ['error' => $e->getMessage()]);
        return response('Invalid payload', 400);

    } catch (\Stripe\Exception\SignatureVerificationException $e) {
        Log::error('Invalid signature en webhook', ['error' => $e->getMessage()]);
        return response('Invalid signature', 400);

    } catch (\Exception $e) {
        Log::error('Error general en webhook', ['error' => $e->getMessage()]);
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
            ], 400);
        }

        // VALIDAR DIRECCIÓN DE FACTURACIÓN
        $usarMisma = $request->has('misma_direccion_facturacion') && $request->misma_direccion_facturacion == 'on';
        if (!$usarMisma) {
            if (!$request->id_direccion_facturacion) {
                return response()->json(['success' => false, 'message' => 'Debes seleccionar una dirección de facturación.'], 400);
            }
        }

        $carrito = Carrito::where('id_usuario', $user->id_usuario)
            ->where('eEstado', 'activo')
            ->with(['detalles.producto.impuestos'])
            ->first();


        if (!$carrito || $carrito->detalles->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Carrito vacío.'], 400);
        }

        // VALIDAR STOCK ANTES DE CREAR ORDEN PAYPAL
        try {
            $this->validateCartStock($carrito);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }

        // Guardar en sesión
        session([
            'id_direccion' => $request->id_direccion,
            'id_direccion_facturacion' => $usarMisma
                ? $request->id_direccion
                : $request->id_direccion_facturacion,
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
                        'return_url' => route('paypal.success') . '?paid=1&payment=paypal',
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

            /**
         * ============================
         * VALIDAR ESTADO COMPLETED
         * ============================
         */
        if ($status !== 'COMPLETED') {

            Log::warning('❌ Pago PayPal NO completado', [
                'order_id' => $orderId,
                'status' => $status,
                'response' => $capData
            ]);

            return response()->json([
                'success' => false,
                'message' => 'El pago con PayPal no se completó correctamente.'
            ], 402);
        }

        /**
         * ============================
         * IDEMPOTENCIA (NO DUPLICAR)
         * ============================
         */
        if (Pago::where('vReferencia', $captureId)->exists()) {

            Log::warning('⚠️ Pago PayPal duplicado ignorado', [
                'capture_id' => $captureId,
                'order_id' => $orderId
            ]);

            return response()->json([
                'success' => true,
                'status' => 'already_processed'
            ], 200);
        }

            // Finalizar pedido localmente (crear Pedido, Venta, Pago, etc.)
            DB::transaction(function () use ($user, $orderId, $captureId) {
               
                // buscamos carrito id por user
                $carrito = Carrito::where('id_usuario', $user->id_usuario)
                    ->where('eEstado', 'activo')
                    ->with(['detalles.producto.impuestos'])
                    ->firstOrFail();

                // VALIDAR STOCK ANTES DE FINALIZAR ORDEN
                $this->validateCartStock($carrito);

                // Obtener id_direccion de la sesión
                $idDireccion = session('id_direccion');
                $idDireccionFact = session('id_direccion_facturacion');
                $notaPedido = session('nota_pedido') ?? null;

                $this->finalizeOrderFromCart($user->id_usuario, $carrito->id_carrito, 'paypal', $captureId, session('codigo_cupon') ?? null, $idDireccion, $idDireccionFact, $notaPedido, null);
            });

            // OBTENER EL ID DEL PEDIDO RECIÉN CREADO
    $pedido = Pedido::where('id_usuario', $user->id_usuario)
        ->latest('id_pedido')
        ->first();

        // Generar la URL de redirección
    $redirectUrl = route('order.received', $pedido->id_pedido);

        return response()->json([
        'success' => true,
        'capture' => $captureId,
        'status' => $status,
        'amount' => $amount,
        'pedido_id' => $pedido->id_pedido,
        'redirect_url' => $redirectUrl
    ]);

        } catch (\Throwable $e) {

            Log::error('🔥 PayPal capture error', [
            'order_id' => $orderId,
            'error' => $e->getMessage()
        ]);

            return response()->json(['success' => false, 'message' => 'Error capturando orden PayPal.'], 500);
        }
    }

    private function finalizeOrderFromCart($userId, $carritoId, $method, $reference, $codigoCupon = null, $idDireccion = null, $idDireccionFact = null, $notaPedido = null, $sessionId = null)
{
    Log::info('Iniciando finalizeOrderFromCart', [
        'userId' => $userId,
        'carritoId' => $carritoId,
        'method' => $method,
        'reference' => $reference,
        'codigoCupon' => $codigoCupon,
        'idDireccion' => $idDireccion,
        'notaPedido' => $notaPedido,
        'sessionId' => $sessionId
    ]);

    // Buscar usuario y carrito
    $carrito = Carrito::where('id_carrito', $carritoId)
        ->with(['detalles.producto.impuestos'])
        ->firstOrFail();

    $userId = $userId ?? $carrito->id_usuario;

    // REVALIDAR STOCK ANTES DE CREAR PEDIDO (defensa en profundidad)
        $this->validateCartStock($carrito);

    // Recalcular totales
    [$subtotal, $totalImpuestos, $total] =
        (new \App\Http\Controllers\Checkout\CheckoutController)->calcularTotales($carrito);

    $montoEnvioGratis = 1500;
    $costoEnvioFijo = 150;
    $envio = ($total >= $montoEnvioGratis) ? 0 : $costoEnvioFijo;

    $descuento = 0;
    $cupon = null;

    if ($codigoCupon) {
        $cupon = Cupon::whereRaw('BINARY vCodigo_cupon = ?', [$codigoCupon])
            ->where('bActivo', 1)
            ->first();

        if ($cupon) {

            // Verificar usos actuales vs máximo
            $usosActuales = CuponUso::where('id_cupon', $cupon->id_cupon)->count();

            if ($usosActuales >= $cupon->iUso_maximo) {
                Log::warning("Cupón {$codigoCupon} excedió el límite de usos ({$usosActuales}/{$cupon->iUso_maximo})");
                $cupon = null;
            } else {
                if ($cupon->vCodigo_cupon === 'ENVIOGRATIS') {
                    $envio = 0;
                } else {
                    $descuento = ($cupon->eTipo === 'porcentaje')
                        ? $total * ($cupon->dDescuento / 100)
                        : $cupon->dDescuento;
                }
            }
        }
    }

    $totalFinal = max(0, $total - $descuento + $envio);

    // Crear pedido
    $pedido = Pedido::create([
        'id_usuario' => $userId,
        'id_direccion' => $idDireccion,
        'id_direccion_facturacion' => $idDireccionFact,  
        'eEstado' => 'pagado',
        'dTotal' => $totalFinal,
        'tNota' => $notaPedido,
    ]);

    foreach ($carrito->detalles as $detalle) {
        PedidoDetalle::create([
            'id_pedido' => $pedido->id_pedido,
            'id_producto' => $detalle->id_producto,
            'iCantidad' => $detalle->cantidad,
            'dPrecio_unitario' => $detalle->precio_unitario,
        ]);
    }

    // Descontar stock
    foreach ($carrito->detalles as $detalle) {
        $producto = \App\Models\Producto::find($detalle->id_producto);

        if ($producto) {
            if ($producto->iStock < $detalle->cantidad) {
                throw new Exception("Inventario insuficiente para el producto: {$producto->vNombre}");
            }

            $producto->iStock -= $detalle->cantidad;
            $producto->save();
        }
    }

    // Crear venta
    $venta = Venta::create([
        'id_pedido' => $pedido->id_pedido,
        'id_usuario' => $userId,
        'dTotal' => $totalFinal,
        'dDescuento' => $descuento,
        'dCosto_envio' => $envio,
        'eMetodo_pago' => $method,
    ]);

    foreach ($carrito->detalles as $detalle) {
        DetalleVenta::create([
            'id_venta' => $venta->id_venta,
            'id_producto' => $detalle->id_producto,
            'iCantidad' => $detalle->cantidad,
            'dPrecio_unitario' => $detalle->precio_unitario,
        ]);
    }

    Log::info('Creando registro de pago', [
        'id_pedido' => $pedido->id_pedido,
        'metodo' => $method,
        'monto' => $totalFinal,
        'referencia' => $reference,
        'session_id' => $sessionId,
        'estado' => 'exitoso',
    ]);

    // Registrar pago
    $pago = Pago::create([
        'id_pedido' => $pedido->id_pedido,
        'eMetodo_pago' => $method,
        'dMonto' => $totalFinal,
        'eEstado' => 'exitoso',
        'vReferencia' => $reference,
        'vSessionID' => $sessionId,
    ]);

    Log::info('Pago creado exitosamente', [
        'id_pago' => $pago->id_pago,
        'referencia' => $pago->vReferencia
    ]);

    // Cupón uso
    if ($cupon) {

        // Verificar nuevamente antes de registrar
        $usosActuales = CuponUso::where('id_cupon', $cupon->id_cupon)->count();

        if ($usosActuales < $cupon->iUso_maximo) {

            CuponUso::create([
                'id_cupon' => $cupon->id_cupon,
                'id_venta' => $venta->id_venta,
            ]);

            $nuevosUsos = $usosActuales + 1;

            Log::info("Cupón {$cupon->vCodigo_cupon} aplicado. Usos: {$nuevosUsos}/{$cupon->iUso_maximo}");

            if ($nuevosUsos >= $cupon->iUso_maximo) {
                $cupon->update(['bActivo' => 0]);
                Log::info("Cupón {$cupon->vCodigo_cupon} desactivado por alcanzar el límite de usos");
            }

        } else {
            Log::warning("Cupón {$cupon->vCodigo_cupon} ya no tiene usos disponibles al momento de guardar");
        }
    }

    // Limpiar carrito
    $carrito->detalles()->delete();
    $carrito->eEstado = 'convertido';
    $carrito->save();

    session()->forget('codigo_cupon');
    session()->forget('nota_pedido');

    // Email cliente
    Mail::to($pedido->usuario->vEmail)->send(
        new \App\Mail\PedidoRealizadoCliente($pedido, $subtotal, $envio, $descuento, $totalFinal, $cupon)
    );

    // Email admin
    $adminEmail = \App\Models\Usuario::whereIn('eRol', ['admin', 'superadmin'])
        ->value('vEmail');

    if ($adminEmail) {
        Mail::to($adminEmail)->send(
            new \App\Mail\PedidoNuevoAdmin($pedido, $subtotal, $envio, $descuento, $totalFinal, $cupon)
        );
    }

    return true;
}

/**
     * Valida stock del carrito antes de permitir cualquier pago.
     * Lanza excepción si algún producto está agotado o la cantidad solicitada supera stock.
     */
    private function validateCartStock($carrito)
    {
        if (!$carrito) {
            throw new Exception("Carrito no encontrado.");
        }

        foreach ($carrito->detalles as $detalle) {

            $producto = $detalle->producto;

            if (!$producto) {
                throw new Exception("Uno de los productos del carrito ya no existe.");
            }

            // Manejar inconsistencia en modelo: iCantidad o cantidad
            $cantidad = $detalle->iCantidad ?? $detalle->cantidad ?? 1;

            if ($producto->iStock <= 0) {
                throw new Exception("El producto '{$producto->vNombre}' está agotado. Debes retirarlo del carrito.");
            }

            if ($cantidad > $producto->iStock) {
                throw new Exception(
                    "La cantidad seleccionada de '{$producto->vNombre}' ({$cantidad}) supera el stock disponible ({$producto->iStock})."
                );
            }
        }

        return true;
    }
}
