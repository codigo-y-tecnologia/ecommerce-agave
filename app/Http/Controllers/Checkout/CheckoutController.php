<?php

namespace App\Http\Controllers\Checkout;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{Carrito, Pedido, PedidoDetalle, Direccion, DireccionGuest, Cupon, CuponUso, Pago, Venta, DetalleVenta};
use App\Traits\InputSanitizer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;
use Carbon\Carbon;
use App\Helpers\CarritoHelper;
use App\Services\Stock\LiberarReservaPorCarritoService;
use Illuminate\Support\Facades\Log;
use App\Services\Checkout\{CalcularDescuentoService, CalcularTotalesService};

class CheckoutController extends Controller
{

    use InputSanitizer;

    public function index()
    {
        // Si el usuario viene de Stripe o PayPal y el pago fue exitoso
        if (request()->paid == 1) {
            session()->forget('codigo_cupon');
        }

        $this->liberarStockReservas();

        $carrito = CarritoHelper::carritoCheckout();

        if (!$carrito || $carrito->detalles->isEmpty()) {
            return redirect()->route('carrito.index')->with('warning', 'Tu carrito está vacío.');
        }

        // VALIDACIÓN DE STOCK EN CHECKOUT 
        foreach ($carrito->detalles as $detalle) {

            if ($detalle->variacion) {
                $stock = $detalle->variacion->iStock;
                $nombre = $detalle->producto->vNombre . ' (' . $detalle->variacion->getAtributosTexto() . ')';
            } else {
                $stock = $detalle->producto->iStock;
                $nombre = $detalle->producto->vNombre;
            }

            if ($stock <= 0) {
                return redirect()
                    ->route('carrito.index')
                    ->with('warning', "El producto {$nombre} está agotado.");
            }

            if ($detalle->iCantidad > $stock) {
                return redirect()
                    ->route('carrito.index')
                    ->with('warning', "La cantidad de {$nombre} excede el stock disponible. Solo quedan {$stock}.");
            }
        }

        $totales = app(CalcularTotalesService::class)
            ->ejecutar($carrito);

        $subtotal = $totales['subtotal'];
        $totalImpuestos = $totales['total_impuestos'];
        $total = $totales['total'];

        $usuario = Auth::user();

        if ($usuario) {
            // Direcciones de usuario logueado
            $direcciones = Direccion::where('id_usuario', $usuario->id_usuario)->get();
        } else {
            // Direcciones de invitado (por guest_token)
            $direcciones = DireccionGuest::byGuestToken(session('guest_token'))->get();
        }

        // seleccionar la dirección principal
        $direccionPrincipal = $direcciones
            ->where('bDireccion_principal', 1)
            ->first() ?? $direcciones->first();

        $codigoCupon = session('codigo_cupon');

        if ($codigoCupon) {

            $cupon = Cupon::disponible()
                ->whereRaw('BINARY vCodigo_cupon = ?', [$codigoCupon])
                ->first();
        }

        $tipoCupon = $cupon?->eTipo ?? null;

        // Definir reglas de envío
        $montoEnvioGratis = config('tienda.envio_gratis_desde'); // Envío gratis si el total >= costo de envío gratis definido
        $costoEnvioFijo = config('tienda.costo_de_envio');   // Costo de envío si no alcanza
        $envio = ($total >= $montoEnvioGratis) ? 0 : $costoEnvioFijo;

        $descuento = 0;

        if (!empty($codigoCupon) && isset($cupon)) {
            // 💸 Cálculo de descuento
            $resultado = app(CalcularDescuentoService::class)
                ->ejecutar($cupon, $total, $envio);

            $descuento = $resultado['descuento'];
            $envio = $resultado['envio'];
        }

        // Recalcular total final
        $totalFinal = max(0, $total - $descuento + $envio);

        return view('checkout.index', compact(
            'carrito',
            'subtotal',
            'totalImpuestos',
            'total',
            'descuento',
            'totalFinal',
            'codigoCupon',
            'direcciones',
            'direccionPrincipal',
            'envio',
            'tipoCupon'
        ));
    }

    public function store(Request $request) {}

    /**
     * Crear dirección (AJAX)
     */
    public function crearDireccion(Request $request)
    {
        try {

            $isGuest = !Auth::check();

            // Reglas base (para invitados y usuarios logueados)
            $rules = [
                'vTelefono_contacto' => 'required|string|max:20',
                'vRFC' => 'nullable|string|max:13',
                'vCalle' => 'required|string|max:150',
                'vNumero_exterior' => 'required|string|max:20',
                'vNumero_interior' => 'nullable|string|max:20',
                'vColonia' => 'required|string|max:150',
                'vCodigo_postal' => 'required|string|max:10',
                'vCiudad' => 'required|string|max:80',
                'vEstado' => 'required|string|max:80',
                'vEntre_calle_1' => 'nullable|string|max:150',
                'vEntre_calle_2' => 'nullable|string|max:150',
                'tReferencias' => 'nullable|string',
                'bDireccion_principal' => 'nullable|boolean',
            ];

            // Reglas SOLO para invitados
            if ($isGuest) {
                $rules['vNombre']   = 'required|string|max:60';
                $rules['vApaterno'] = 'required|string|max:50';
                $rules['vAmaterno'] = 'nullable|string|max:50';
            }

            // Validar request
            $data = $request->validate($rules);

            // Limpieza de seguridad
            $this->verificarYLimpiar($data, config('security.sql_keywords'));

            $data['bDireccion_principal'] = $request->has('bDireccion_principal') ? 1 : 0;

            /**
             * =====================
             * INVITADO
             * =====================
             */
            if ($isGuest) {

                $data['vGuest_token'] = session('guest_token');

                if ($data['bDireccion_principal']) {
                    DireccionGuest::byGuestToken($data['vGuest_token'])
                        ->update(['bDireccion_principal' => 0]);
                }

                $direccion = DireccionGuest::create($data);

                return response()->json([
                    'success' => true,
                    'direccion' => [
                        // 🔑 NORMALIZAMOS EL ID
                        'id_direccion' => $direccion->id_direccion_guest,

                        // Campos que el frontend usa para mostrar el texto
                        'vCalle' => $direccion->vCalle,
                        'vNumero_exterior' => $direccion->vNumero_exterior,
                        'vColonia' => $direccion->vColonia,
                        'vCiudad' => $direccion->vCiudad,
                    ],
                    'tipo' => 'guest'
                ]);
            }

            /**
             * =====================
             * USUARIO LOGUEADO
             * =====================
             */

            // Asegurar que no se guarden campos de invitado
            unset(
                $data['vNombre'],
                $data['vApaterno'],
                $data['vAmaterno']
            );

            $data['id_usuario'] = Auth::user()->id_usuario;

            // Si marca esta nueva como principal, desmarcamos las demás
            if (!empty($data['bDireccion_principal']) && $data['bDireccion_principal'] == 1) {
                Direccion::where('id_usuario', Auth::user()->id_usuario)
                    ->update(['bDireccion_principal' => 0]);
            }

            $direccion = Direccion::create($data);

            return response()->json(['success' => true, 'direccion' => $direccion, 'tipo' => 'user']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar la dirección',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar dirección existente (AJAX)
     */
    public function actualizarDireccion(Request $request, $id)
    {
        try {

            $isGuest = !Auth::check();

            // Reglas base (guest + user)
            $rules = [
                'vTelefono_contacto' => 'required|string|max:20',
                'vRFC' => 'nullable|string|max:13',
                'vCalle' => 'required|string|max:150',
                'vNumero_exterior' => 'required|string|max:20',
                'vNumero_interior' => 'nullable|string|max:20',
                'vColonia' => 'required|string|max:150',
                'vCodigo_postal' => 'required|string|max:10',
                'vCiudad' => 'required|string|max:80',
                'vEstado' => 'required|string|max:80',
                'vEntre_calle_1' => 'nullable|string|max:150',
                'vEntre_calle_2' => 'nullable|string|max:150',
                'tReferencias' => 'nullable|string',
                'bDireccion_principal' => 'nullable|boolean',
            ];

            // Reglas SOLO para invitado
            if ($isGuest) {
                $rules['vNombre']   = 'required|string|max:60';
                $rules['vApaterno'] = 'required|string|max:50';
                $rules['vAmaterno'] = 'nullable|string|max:50';
            }

            $data = $request->validate($rules);

            $this->verificarYLimpiar($data, config('security.sql_keywords'));

            $data['bDireccion_principal'] = $request->has('bDireccion_principal') ? 1 : 0;

            /**
             * =====================
             * INVITADO
             * =====================
             */
            if ($isGuest) {

                $guestToken = session('guest_token');

                if (!$guestToken) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Sesión de invitado no válida'
                    ], 401);
                }

                /** @var DireccionGuest $direccion */
                $direccion = DireccionGuest::where('id_direccion_guest', $id)
                    ->where('vGuest_token', $guestToken)
                    ->first();

                if (!$direccion) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Dirección no encontrada'
                    ], 404);
                }

                // Si se marca como principal, desmarcar las demás del mismo guest
                if ($data['bDireccion_principal']) {
                    DireccionGuest::where('vGuest_token', $guestToken)
                        ->where('id_direccion_guest', '!=', $id)
                        ->update(['bDireccion_principal' => 0]);
                }

                $direccion->update($data);

                return response()->json([
                    'success' => true,
                    'direccion' => $direccion,
                    'tipo' => 'guest'
                ]);
            }

            /**
             * =====================
             * USUARIO LOGUEADO
             * =====================
             */

            // Limpiar campos exclusivos de invitado
            unset(
                $data['vNombre'],
                $data['vApaterno'],
                $data['vAmaterno']
            );

            /** @var Direccion $direccion */
            $direccion = Direccion::where('id_direccion', $id)
                ->where('id_usuario', Auth::user()->id_usuario)
                ->first();

            if (!$direccion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dirección no encontrada'
                ], 404);
            }

            // Si el usuario marca esta como principal, desmarcamos todas las demás
            if (!empty($data['bDireccion_principal']) && $data['bDireccion_principal'] == 1) {
                Direccion::where('id_usuario', Auth::user()->id_usuario)
                    ->where('id_direccion', '!=', $id)
                    ->update(['bDireccion_principal' => 0]);
            }

            $direccion->update($data);

            return response()->json([
                'success' => true,
                'direccion' => $direccion,
                'tipo' => 'user'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno al actualizar la dirección',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calcula subtotal, total de impuestos y total general del carrito
     */
    public function calcularTotales($carrito)
    {
        $subtotal = 0;
        $totalImpuestos = 0;

        foreach ($carrito->detalles as $detalle) {

            $producto = $detalle->producto;
            $precio_base = $producto->dPrecio_venta;
            $cantidad = $detalle->cantidad;

            // Obtener impuestos
            $impuestos = $producto->impuestos->where('bActivo', 1);

            $ieps = 0;
            $iva = 0;

            foreach ($impuestos as $imp) {
                if ($imp->eTipo === 'IEPS') {
                    $ieps = $precio_base * ($imp->dPorcentaje / 100);
                }
            }

            foreach ($impuestos as $imp) {
                if ($imp->eTipo === 'IVA') {
                    $iva = ($precio_base + $ieps) * ($imp->dPorcentaje / 100);
                }
            }

            // Precio final unitario
            $precio_final_unitario = $precio_base + $ieps + $iva;

            // Totales por cantidad
            $subtotal_producto = $precio_base * $cantidad;
            $impuestos_producto = ($ieps + $iva) * $cantidad;

            $subtotal += $subtotal_producto;
            $totalImpuestos += $impuestos_producto;
        }

        $total = $subtotal + $totalImpuestos;

        return [$subtotal, $totalImpuestos, $total];
    }

    public function aplicarCupon(Request $request)
    {

        try {

            $request->validate([
                'codigo' => 'required|string|max:50'
            ]);

            $this->liberarStockReservas();

            $codigo = $request->codigo;

            $carrito = CarritoHelper::carritoCheckout();

            if (!$carrito || $carrito->detalles->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Carrito vacío.'
                ]);
            }

            $totales = app(CalcularTotalesService::class)
                ->ejecutar($carrito);

            $total = $totales['total'];

            $cupon = Cupon::disponible()
                ->whereRaw('BINARY vCodigo_cupon = ?', [$codigo])
                ->first();

            if (!$cupon) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cupón inválido o expirado.'
                ]);
            }

            // Validar uso máximo
            if (
                !is_null($cupon->iUso_maximo) &&
                $cupon->iUsos_actuales >= $cupon->iUso_maximo
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este cupón ya alcanzó su límite de usos.'
                ]);
            }

            // Validar límite por usuario
            if ($cupon->iUsos_por_usuario) {

                $usosUsuario = CuponUso::where('id_cupon', $cupon->id_cupon)
                    ->when(Auth::check(), function ($q) {
                        $q->where('id_usuario', Auth::id());
                    }, function ($q) {
                        $q->where('guest_token', session('guest_token'));
                    })
                    ->count();

                if ($usosUsuario >= $cupon->iUsos_por_usuario) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Ya alcanzaste el límite de uso de este cupón.'
                    ]);
                }
            }

            // 🚚 Cálculo de envío
            $montoEnvioGratis = config('tienda.envio_gratis_desde');
            $costoEnvioFijo = config('tienda.costo_de_envio');
            $envio = ($total >= $montoEnvioGratis) ? 0 : $costoEnvioFijo;

            // 💸 Cálculo de descuento
            $resultado = app(CalcularDescuentoService::class)
                ->ejecutar($cupon, $total, $envio);

            $descuento = $resultado['descuento'];
            $envio = $resultado['envio'];
            $mensaje = $resultado['mensaje'];
            $warning = $resultado['warning'];

            if ($warning) {
                return response()->json([
                    'success' => false,
                    'message' => $mensaje
                ]);
            }

            // 🧮 Recalcular total
            $totalFinal = max(0, $total - $descuento + $envio);

            // 🔹 Guardar el cupón en la sesión (guest o user)
            session(['codigo_cupon' => $codigo]);

            return response()->json([
                'success' => true,
                'codigo' => $cupon->vCodigo_cupon,
                'descuento' => $descuento,
                'envio' => $envio,
                'totalFinal' => $totalFinal,
                'message' => $mensaje
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Error al aplicar cupón: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Error al aplicar el cupón',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener dirección guest (AJAX)
     */
    public function getDireccionGuest($id)
    {
        try {
            $guestToken = session('guest_token');

            if (!$guestToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesión de invitado no válida'
                ], 401);
            }

            $direccion = DireccionGuest::where('id_direccion_guest', $id)
                ->where('vGuest_token', $guestToken)
                ->first();

            if (!$direccion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dirección no encontrada'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'direccion' => $direccion
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la dirección',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function liberarStockReservas()
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

    public function cancel()
    {
        return redirect()->route('carrito.index')
            ->with('info', 'Pago cancelado. Puedes editar tu carrito.');
    }
}
