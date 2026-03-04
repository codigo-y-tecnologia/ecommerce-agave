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
use App\Helpers\CarritoHelper;
use App\Services\Stock\{ReservarStockService, ConsumirReservaService, LiberarReservaPorCarritoService, LiberarReservaService};
use App\Services\Checkout\CrearPagoDesdeCarritoService;
use App\Exceptions\StockException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Models\{
    Usuario,
    Carrito,
    Pedido,
    PedidoDetalle,
    Direccion,
    DireccionGuest,
    Cupon,
    CuponUso,
    Pago,
    Venta,
    DetalleVenta,
    Envio,
    Producto,
    StockReserva,
    Setting,
    CheckoutSnapshot,
    CuponReserva
};
use App\Services\Cupones\ConsumirCuponService;
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
                    'type' => 'validation',
                    'message' => 'Debes seleccionar una dirección de envío.'
                ], 400);
            }

            // VALIDAR EMAIL (INVITADO)
            if (!$user) {
                if (!$request->email_invitado) {
                    return response()->json([
                        'success' => false,
                        'type' => 'validation',
                        'message' => 'Debes ingresar un correo para continuar.'
                    ], 400);
                }
            }

            // VALIDAR DIRECCIÓN DE FACTURACIÓN según checkbox
            $usarMisma = $request->has('misma_direccion_facturacion') && $request->misma_direccion_facturacion == 'on';

            if (!$usarMisma) {

                if (!$request->id_direccion_facturacion) {
                    return response()->json([
                        'success' => false,
                        'type' => 'validation',
                        'message' => 'Debes seleccionar una dirección de facturación.'
                    ], 400);
                }
            }

            $this->releaseReservation();

            // Cargar carrito (usuario invitado y logueado)
            $carrito = CarritoHelper::carritoCheckout();

            if (!$carrito || $carrito->detalles->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'type' => 'validation',
                    'message' => 'Carrito vacío.'
                ], 400);
            }

            if ($carrito->eEstado === 'reservado') {
                return response()->json([
                    'success' => false,
                    'type' => 'business',
                    'message' => 'Ya hay un pago en proceso para este carrito.'
                ], 409);
            }

            // Guardar en sesión
            session([
                'id_direccion' => $request->id_direccion,
                'email_invitado' => $request->email_invitado ?? null,
                'id_direccion_facturacion' => $usarMisma
                    ? $request->id_direccion
                    : $request->id_direccion_facturacion,
                'nota_pedido' => $request->nota ?? null,
                'stripe_checkout_in_progress' => true,
                'stripe_carrito_id' => $carrito->id_carrito,
            ]);

            $result = app(CrearPagoDesdeCarritoService::class)
                ->ejecutar($carrito, session('codigo_cupon'));

            $totalFinal = $result['totalFinal'];

            // Stripe
            Stripe::setApiKey(env('STRIPE_SECRET'));

            $amountCents = (int) round($totalFinal * 100);

            // Metadata COMPLETA
            $metadata = [
                'user_id' => Auth::check() ? Auth::id() : null,
                'guest_token'  => session('guest_token'),
                'carrito_id' => $carrito->id_carrito,
                'id_direccion' => $request->id_direccion,
                'email_invitado' => session('email_invitado') ?? null,
                'id_direccion_facturacion' => session('id_direccion_facturacion'),
                'nota_pedido' => session('nota_pedido') ?? '',
                'codigo_cupon' => session('codigo_cupon') ?? '',
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
                'cancel_url' => route('checkout.cancel') . '?paid=0',
            ]);

            DB::transaction(function () use ($carrito, $session) {

                // Asociar session_id a TODAS las reservas del carrito
                StockReserva::where('id_carrito', $carrito->id_carrito)
                    ->whereNull('session_id')
                    ->update([
                        'session_id' => $session->id
                    ]);

                // Asociar session_id a snapshot para validación futura (webhook)
                CheckoutSnapshot::where('id_carrito', $carrito->id_carrito)
                    ->update([
                        'payment_session' => $session->id
                    ]);

                // Asociar session_id a TODAS las reservas de cupón del carrito
                CuponReserva::where('id_carrito', $carrito->id_carrito)
                    ->update([
                        'session_id' => $session->id
                    ]);
            });

            return response()->json([
                'success' => true,
                'url' => $session->url,
                'id' => $session->id
            ]);
        } catch (StockException $e) {

            if (isset($carrito)) {
                $carrito->update([
                    'eEstado' => 'activo'
                ]);
            }

            Log::error("🔥 Stripe error", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'type' => 'stock',
                'message' => $e->getMessage()
            ], 409);
        } catch (\Throwable $e) {

            if (isset($carrito)) {
                $carrito->update([
                    'eEstado' => 'activo'
                ]);
            }

            Log::error("🔥 Stripe error", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'type' => 'system',
                'message' => 'Ocurrió un error al procesar el pago. Intenta nuevamente.'
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
                        $guestToken = $metadata->guest_token ?? null;
                        $emailGuest = $metadata->email_invitado ?? null;
                        $carritoId = $metadata->carrito_id ?? null;
                        $codigoCupon = $metadata->codigo_cupon ?? null;
                        $idDireccion = $metadata->id_direccion ?? null;
                        $idDireccionFact = $metadata->id_direccion_facturacion ?? $idDireccion;
                        $notaPedido = $metadata->nota_pedido ?? null;

                        Log::info('Referencia a guardar: ' . $reference);

                        // Obtener snapshot con lock
                        $snapshot = CheckoutSnapshot::where('payment_session', $session->id)
                            ->lockForUpdate()
                            ->first();

                        if (!$snapshot) {
                            throw new Exception('Snapshot no encontrado.');
                        }

                        // buscamos carrito
                        if ($carritoId) {

                            $carrito = Carrito::where('id_carrito', $carritoId)
                                ->lockForUpdate()
                                ->first();

                            if (!$carrito) {
                                throw new Exception("Carrito no encontrado para finalizar pedido (Stripe webhook).");
                            }
                        } else {
                            Log::warning('No se encontró carrito_id en metadata de Stripe.');

                            throw new Exception('Carrito no encontrado');
                        }

                        $this->finalizeOrderFromCart(
                            $userId,
                            $guestToken,
                            $emailGuest,
                            $carritoId,
                            'stripe',
                            $reference,
                            $codigoCupon,
                            $idDireccion,
                            $idDireccionFact,
                            $notaPedido,
                            $session->id,
                            $snapshot
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
            Log::error('Error en webhook Stripe', [
                'error' => $e->getMessage(),
                'payment_intent' => $reference ?? null,
            ]);

            return response()->json(['error' => 'internal_error'], 500);
        }
    }

    /** Valida los datos del formulario de PayPal antes de crear la orden */
    public function validatePaypal(Request $request)
    {
        $user = Auth::user();

        // VALIDAR DIRECCION
        if (!$request->id_direccion) {
            return response()->json([
                'success' => false,
                'type' => 'validation',
                'message' => 'Debes seleccionar una dirección de envío.'
            ], 400);
        }

        // VALIDAR EMAIL (INVITADO)
        if (!$user) {
            if (!$request->email_invitado) {
                return response()->json([
                    'success' => false,
                    'type' => 'validation',
                    'message' => 'Debes ingresar un correo para continuar.'
                ], 400);
            }
        }

        // VALIDAR DIRECCIÓN DE FACTURACIÓN
        if (
            !$request->usar_misma_direccion &&
            !$request->id_direccion_facturacion
        ) {
            return response()->json([
                'success' => false,
                'type' => 'validation',
                'message' => 'Debes seleccionar una dirección de facturación.'
            ], 400);
        }

        $this->releaseReservation();

        // Cargar carrito (usuario invitado y logueado)
        $carrito = CarritoHelper::carritoCheckout();

        if (!$carrito || $carrito->detalles->isEmpty()) {

            return response()->json([
                'success' => false,
                'type' => 'validation',
                'message' => 'Carrito vacío.'
            ], 400);
        }

        if ($carrito->eEstado === 'reservado') {
            return response()->json([
                'success' => false,
                'type' => 'business',
                'message' => 'Ya hay un pago en proceso para este carrito.'
            ], 409);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Crea una orden PayPal (server side) y devuelve id (cliente usa approve).
     */
    public function createPaypalOrder(Request $request)
    {

        // DIRECCIÓN DE FACTURACIÓN
        $usarMisma = $request->has('misma_direccion_facturacion') && $request->misma_direccion_facturacion == 'on';

        // Cargar carrito (usuario invitado y logueado)
        $carrito = CarritoHelper::carritoCheckout();

        // Guardar en sesión
        session([
            'paypal_context' => [
                'id_direccion' => $request->id_direccion,
                'email_invitado' => $request->email_invitado ?? null,
                'id_direccion_facturacion' => $usarMisma
                    ? $request->id_direccion
                    : $request->id_direccion_facturacion,
                'nota_pedido' => $request->nota ?? null,
                'guest_token' => session('guest_token'),
                'user_id' => Auth::id(),
                'paypal_carrito_id' => $carrito->id_carrito,
            ]
        ]);

        // Recalcular totales (misma lógica)
        [$subtotal, $totalImpuestos, $total] = (new \App\Http\Controllers\Checkout\CheckoutController)->calcularTotales($carrito);

        // Cupón
        $codigoCupon = session('codigo_cupon');
        $descuento = 0;
        $cupon = null;

        if ($codigoCupon) {
            $cupon = Cupon::whereRaw('BINARY vCodigo_cupon = ?', [$codigoCupon])->where('bActivo', 1)
                ->whereDate('dValido_desde', '<=', now())
                ->whereDate('dValido_hasta', '>=', now())
                ->first();

            if ($cupon && $cupon->vCodigo_cupon !== 'ENVIOGRATIS') {
                $descuento = ($cupon->eTipo === 'porcentaje') ? $total * ($cupon->dDescuento / 100) : $cupon->dDescuento;
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

            $orderId = $order['id'];

            DB::transaction(function () use ($carrito, $orderId) {

                // Reservar stock (lock + validación)
                app(ReservarStockService::class)->ejecutar($carrito);

                // Asociar session_id a TODAS las reservas del carrito
                StockReserva::where('id_carrito', $carrito->id_carrito)
                    ->whereNull('session_id')
                    ->update([
                        'session_id' => $orderId
                    ]);
            });

            return response()->json([
                'success' => true,
                'orderID' => $orderId
            ]);
        } catch (StockException $e) {

            if (isset($carrito)) {
                $carrito->update([
                    'eEstado' => 'activo'
                ]);
            }

            Log::error("🔥 PayPal error", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'type' => 'stock',
                'message' => $e->getMessage()
            ], 409);
        } catch (\Throwable $e) {

            if (isset($carrito)) {
                $carrito->update([
                    'eEstado' => 'activo'
                ]);
            }

            Log::error("🔥 Paypal error", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'type' => 'system',
                'message' => 'Ocurrió un error al procesar el pago. Intenta nuevamente.'
            ], 500);
        }
    }

    /**
     * Captura la orden PayPal (llamada por cliente tras approve).
     * Luego finaliza la orden (crea pedido/venta/pago).
     */
    public function capturePaypalOrder(Request $request)
    {
        $orderId = $request->orderID;

        Log::info('Capturando orden PayPal', [
            'order_id' => $orderId,
        ]);

        $context = session('paypal_context');

        $idDireccion = $context['id_direccion'] ?? null;
        $idDireccionFact = $context['id_direccion_facturacion'] ?? null;
        $userId = $context['user_id'] ?? null;
        $guestToken = $context['guest_token'] ?? null;
        $emailGuest = $context['email_invitado'] ?? null;
        $carritoId = $context['paypal_carrito_id'] ?? null;
        $notaPedido = $context['nota_pedido'] ?? null;

        if (!$orderId) {
            return response()->json([
                'success' => false,
                'message' => 'orderID requerido'
            ], 400);
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

            // Aquí se pueden obtener detalles como captura id y status
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
            DB::transaction(function () use ($userId, $guestToken, $emailGuest, $carritoId, $idDireccion, $idDireccionFact, $notaPedido, $orderId, $captureId) {

                Log::info('Finalizando pedido desde carrito PayPal', [
                    'order_id' => $orderId,
                ]);

                // buscamos carrito 
                if ($carritoId) {

                    $carrito = Carrito::where('id_carrito', $carritoId)->first();

                    if (!$carrito) {
                        throw new Exception("Carrito no encontrado para finalizar pedido (PayPal capturePaypalOrder).");
                    }
                } else {
                    Log::warning('No se encontró carrito_id en sesión para captura PayPal.');

                    throw new \Exception('Carrito no encontrado');
                }

                // Consumir reserva (validación + stock real)
                app(ConsumirReservaService::class)
                    ->ejecutar($orderId);

                $this->finalizeOrderFromCart(
                    $userId,
                    $guestToken,
                    $emailGuest,
                    $carritoId,
                    'paypal',
                    $captureId,
                    session('codigo_cupon') ?? null,
                    $idDireccion,
                    $idDireccionFact,
                    $notaPedido,
                    null
                );
            });

            // OBTENER EL ID DEL PEDIDO RECIÉN CREADO
            $pago = Pago::where('vReferencia', $captureId)->firstOrFail();
            $pedido = Pedido::findOrFail($pago->id_pedido);

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

    private function finalizeOrderFromCart($userId, $guestToken, $emailGuest, $carritoId, $method, $reference, $codigoCupon = null, $idDireccion = null, $idDireccionFact = null, $notaPedido = null, $sessionId = null, $snapshot = null)
    {
        Log::info('Iniciando finalizeOrderFromCart', [
            'userId' => $userId,
            'guestToken' => $guestToken,
            'emailGuest' => $emailGuest,
            'carritoId' => $carritoId,
            'method' => $method,
            'reference' => $reference,
            'codigoCupon' => $codigoCupon,
            'idDireccion' => $idDireccion,
            'idDireccionFacturacion' => $idDireccionFact,
            'notaPedido' => $notaPedido,
            'sessionId' => $sessionId,
            'snapshot' => $snapshot
        ]);

        // Buscar carrito
        $carrito = Carrito::with(['detalles.producto.impuestos'])
            ->where('id_carrito', $carritoId)
            ->firstOrFail();

        if ($userId) {
            $usuario = Usuario::findOrFail($userId);

            $nombre    = $usuario->vNombre;
            $apaterno  = $usuario->vApaterno;
            $amaterno  = $usuario->vAmaterno;
            $email     = $usuario->vEmail;

            $direccionEnvio = Direccion::where('id_direccion', $idDireccion)
                ->where('id_usuario', $userId)
                ->firstOrFail();

            $telefonoEnvio = $direccionEnvio->vTelefono_contacto;
            $rfc = $direccionEnvio->vRFC;
            $calleEnvio = $direccionEnvio->vCalle;
            $numeroExteriorEnvio = $direccionEnvio->vNumero_exterior;
            $numeroInteriorEnvio = $direccionEnvio->vNumero_interior;
            $coloniaEnvio = $direccionEnvio->vColonia;
            $codigoPostalEnvio = $direccionEnvio->vCodigo_postal;
            $ciudadEnvio = $direccionEnvio->vCiudad;
            $estadoEnvio = $direccionEnvio->vEstado;
            $entreCalleUnoEnvio = $direccionEnvio->vEntre_calle_1;
            $entreCalleDosEnvio = $direccionEnvio->vEntre_calle_2;
            $referenciasEnvio = $direccionEnvio->tReferencias;
        } else {
            $email = $emailGuest;

            $direccionEnvio = DireccionGuest::where('id_direccion_guest', $idDireccion)
                ->where('vGuest_token', $guestToken)
                ->firstOrFail();

            $nombre    = $direccionEnvio->vNombre;
            $apaterno  = $direccionEnvio->vApaterno;
            $amaterno  = $direccionEnvio->vAmaterno;
            $telefonoEnvio = $direccionEnvio->vTelefono_contacto;
            $rfc = $direccionEnvio->vRFC;
            $calleEnvio = $direccionEnvio->vCalle;
            $numeroExteriorEnvio = $direccionEnvio->vNumero_exterior;
            $numeroInteriorEnvio = $direccionEnvio->vNumero_interior;
            $coloniaEnvio = $direccionEnvio->vColonia;
            $codigoPostalEnvio = $direccionEnvio->vCodigo_postal;
            $ciudadEnvio = $direccionEnvio->vCiudad;
            $estadoEnvio = $direccionEnvio->vEstado;
            $entreCalleUnoEnvio = $direccionEnvio->vEntre_calle_1;
            $entreCalleDosEnvio = $direccionEnvio->vEntre_calle_2;
            $referenciasEnvio = $direccionEnvio->tReferencias;
        }

        if ($idDireccionFact) {
            if ($userId) {
                $direccionFact = Direccion::where('id_direccion', $idDireccionFact)
                    ->where('id_usuario', $userId)
                    ->first();

                $telefonoFacturacion = $direccionFact->vTelefono_contacto;
                $rfc = $direccionFact->vRFC;
                $calleFacturacion = $direccionFact->vCalle;
                $numeroExteriorFacturacion = $direccionFact->vNumero_exterior;
                $numeroInteriorFacturacion = $direccionFact->vNumero_interior;
                $coloniaFacturacion = $direccionFact->vColonia;
                $codigoPostalFacturacion = $direccionFact->vCodigo_postal;
                $ciudadFacturacion = $direccionFact->vCiudad;
                $estadoFacturacion = $direccionFact->vEstado;
                $entreCalleUnoFacturacion = $direccionFact->vEntre_calle_1;
                $entreCalleDosFacturacion = $direccionFact->vEntre_calle_2;
                $referenciasFacturacion = $direccionFact->tReferencias;
            } else {
                $direccionFact = DireccionGuest::where('id_direccion_guest', $idDireccionFact)
                    ->where('vGuest_token', $guestToken)
                    ->first();

                $telefonoFacturacion = $direccionFact->vTelefono_contacto;
                $rfc = $direccionFact->vRFC;
                $calleFacturacion = $direccionFact->vCalle;
                $numeroExteriorFacturacion = $direccionFact->vNumero_exterior;
                $numeroInteriorFacturacion = $direccionFact->vNumero_interior;
                $coloniaFacturacion = $direccionFact->vColonia;
                $codigoPostalFacturacion = $direccionFact->vCodigo_postal;
                $ciudadFacturacion = $direccionFact->vCiudad;
                $estadoFacturacion = $direccionFact->vEstado;
                $entreCalleUnoFacturacion = $direccionFact->vEntre_calle_1;
                $entreCalleDosFacturacion = $direccionFact->vEntre_calle_2;
                $referenciasFacturacion = $direccionFact->tReferencias;
            }
        }

        if (!$userId && $idDireccion) {
            Log::info('Pedido invitado: id_direccion ignorado para evitar FK inválida', [
                'id_direccion' => $idDireccion
            ]);
        }

        // 🔹 REGISTRO AUTOMÁTICO (SI ES INVITADO Y ESTÁ HABILITADO)
        $nuevoUserId = $this->autoRegisterGuestIfEnabled(
            $userId,
            $email,
            $nombre,
            $apaterno,
            $amaterno
        );

        // Si se creó cuenta, usar ese usuario para el pedido y la venta
        if ($nuevoUserId) {
            $userId = $nuevoUserId;
        }

        // Consumir reserva (validación + stock real)
        app(ConsumirReservaService::class)
            ->ejecutar($sessionId);

        // Crear pedido
        $pedido = Pedido::create([
            'id_usuario' => $userId,
            'id_direccion' => null,
            'id_direccion_facturacion' => null,
            'vNombre' => $nombre,
            'eEstado' => 'pagado',
            'dTotal' => $snapshot->total_final,
            'tNota' => $notaPedido,
            'vApaterno' => $apaterno,
            'vAmaterno' => $amaterno,
            'vEmail' => $email,
            'env_telefono_contacto' => $telefonoEnvio,
            'env_calle' => $calleEnvio,
            'env_numero_exterior' => $numeroExteriorEnvio,
            'env_numero_interior' => $numeroInteriorEnvio,
            'env_colonia' => $coloniaEnvio,
            'env_codigo_postal' => $codigoPostalEnvio,
            'env_ciudad' => $ciudadEnvio,
            'env_estado' => $estadoEnvio,
            'env_entre_calle_1' => $entreCalleUnoEnvio,
            'env_entre_calle_2' => $entreCalleDosEnvio,
            'env_referencias' => $referenciasEnvio,
            'fac_telefono_contacto' => $telefonoFacturacion,
            'fac_calle' => $calleFacturacion,
            'fac_numero_exterior' => $numeroExteriorFacturacion,
            'fac_numero_interior' => $numeroInteriorFacturacion,
            'fac_colonia' => $coloniaFacturacion,
            'fac_codigo_postal' => $codigoPostalFacturacion,
            'fac_ciudad' => $ciudadFacturacion,
            'fac_estado' => $estadoFacturacion,
            'fac_entre_calle_1' => $entreCalleUnoFacturacion,
            'fac_entre_calle_2' => $entreCalleDosFacturacion,
            'fac_referencias' => $referenciasFacturacion,
            'vRFC' => $rfc,
            'vGuest_token' => $guestToken
        ]);

        foreach ($carrito->detalles as $detalle) {
            PedidoDetalle::create([
                'id_pedido' => $pedido->id_pedido,
                'id_producto' => $detalle->id_producto,
                'iCantidad' => $detalle->cantidad,
                'dPrecio_unitario' => $detalle->precio_unitario,
            ]);
        }

        // Crear venta
        $venta = Venta::create([
            'id_pedido' => $pedido->id_pedido,
            'id_usuario' => $userId,
            'dTotal' => $snapshot->total_final,
            'dDescuento' => $snapshot->descuento,
            'dCosto_envio' => $snapshot->envio,
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
            'monto' => $snapshot->total_final,
            'referencia' => $reference,
            'session_id' => $sessionId,
            'estado' => 'exitoso',
        ]);

        // Registrar pago
        $pago = Pago::create([
            'id_pedido' => $pedido->id_pedido,
            'eMetodo_pago' => $method,
            'dMonto' => $snapshot->total_final,
            'eEstado' => 'exitoso',
            'vReferencia' => $reference,
            'vSessionID' => $sessionId,
        ]);

        Log::info('Pago creado exitosamente', [
            'id_pago' => $pago->id_pago,
            'referencia' => $pago->vReferencia
        ]);

        app(ConsumirCuponService::class)->ejecutar(
            $sessionId,
            $venta->id_venta,
            $carrito->id_usuario ?? null,
            $carrito->vGuest_token ?? null
        );

        // Limpiar carrito
        $carrito->detalles()->delete();
        $carrito->save();

        session()->forget([
            'paypal_context',
            'codigo_cupon',
            'id_direccion',
            'id_direccion_facturacion',
            'nota_pedido',
            'email_invitado',
            'stripe_checkout_in_progress',
            'stripe_carrito_id'
        ]);

        // Email cliente
        Mail::to($email)->send(
            new \App\Mail\PedidoRealizadoCliente($pedido, $snapshot)
        );

        // Email admin
        $adminEmails = Usuario::role('admin')->pluck('vEmail');

        if ($adminEmails->isNotEmpty()) {
            Mail::to($adminEmails)->send(
                new \App\Mail\PedidoNuevoAdmin($pedido, $snapshot)
            );
        }

        return true;
    }

    /**
     * Libera la reserva de stock si el usuario abandona el proceso de pago.
     * Se llama desde el frontend cuando detecta que el usuario cierra la ventana de pago o navega fuera.
     */

    public function releaseReservation()
    {
        if (session('stripe_checkout_in_progress')) {

            $carritoId = session('stripe_carrito_id');

            $carrito = Carrito::find($carritoId);

            if ($carrito && $carrito->eEstado === 'reservado') {
                app(LiberarReservaPorCarritoService::class)->ejecutar($carrito);
            }

            session()->forget([
                'stripe_checkout_in_progress',
                'stripe_carrito_id',
            ]);

            return response()->json(['status' => 'released']);
        }

        if (session('paypal_context')) {

            $context = session('paypal_context');

            $carritoId = $context['paypal_carrito_id'] ?? null;

            $carrito = Carrito::find($carritoId);

            if ($carrito && $carrito->eEstado === 'reservado') {
                app(LiberarReservaPorCarritoService::class)->ejecutar($carrito);
            }

            session()->forget([
                'paypal_context',
            ]);

            return response()->json(['status' => 'released']);
        }

        return response()->json(['status' => 'no_reservation']);
    }

    private function autoRegisterGuestIfEnabled(
        $userId,
        $email,
        $nombre,
        $apaterno,
        $amaterno
    ) {
        // Ya es usuario → no hacer nada
        if ($userId) {
            return $userId;
        }

        // Switch apagado → no registrar
        if (!Setting::getValue('auto_register_guest_after_purchase')) {
            return null;
        }

        // Si el email ya existe → usar ese usuario
        $existingUser = Usuario::where('vEmail', $email)->first();
        if ($existingUser) {
            return $existingUser->id_usuario;
        }

        // Crear usuario
        $token = Str::uuid()->toString();

        $usuario = Usuario::create([
            'vNombre' => $nombre,
            'vApaterno' => $apaterno,
            'vAmaterno' => $amaterno,
            'vEmail' => $email,
            'vPassword' => null,
            'email_verification_token' => $token,
            'is_verified' => 0,
        ]);

        // Rol cliente (Spatie)
        $usuario->assignRole('cliente');

        // Aquí se manda email de bienvenida / set password
        Mail::to($email)->send(
            new \App\Mail\CuentaCreadaAutomaticamente($usuario, $token)
        );

        return $usuario->id_usuario;
    }
}
