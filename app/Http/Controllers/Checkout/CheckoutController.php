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

class CheckoutController extends Controller
{

    use InputSanitizer;

    public function index()
    {
        // Si el usuario viene de Stripe o PayPal y el pago fue exitoso
        if (request()->paid == 1) {
            session()->forget('codigo_cupon');
        }

        $carrito = CarritoHelper::carritoCheckout();

        if (!$carrito || $carrito->detalles->isEmpty()) {
            return redirect()->route('carrito.index')->with('warning', 'Tu carrito está vacío.');
        }

        // VALIDACIÓN DE STOCK EN CHECKOUT 
        foreach ($carrito->detalles as $detalle) {
            $producto = $detalle->producto;

            if ($producto->iStock <= 0) {
                return redirect()
                    ->route('carrito.index')
                    ->with('warning', "El producto {$producto->vNombre} está agotado. Debes retirarlo del carrito.");
            }

            if ($detalle->iCantidad > $producto->iStock) {
                return redirect()
                    ->route('carrito.index')
                    ->with('warning', "La cantidad de {$producto->vNombre} excede las unidades disponibles. Solo quedan {$producto->iStock}.");
            }
        }

        [$subtotal, $totalImpuestos, $total] = $this->calcularTotales($carrito);

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
        $descuento = 0;

        if ($codigoCupon) {
            $cupon = Cupon::whereRaw('BINARY vCodigo_cupon = ?', [$codigoCupon])
                ->where('bActivo', 1)
                ->whereDate('dValido_desde', '<=', now())
                ->whereDate('dValido_hasta', '>=', now())
                ->first();

            if ($cupon) {
                $descuento = $cupon->eTipo === 'porcentaje'
                    ? $total * ($cupon->dDescuento / 100)
                    : $cupon->dDescuento;
            }
        }

        $totalFinal = max(0, $total - $descuento);

        // Definir reglas de envío
        $montoEnvioGratis = 1500; // Envío gratis si el total >= 1500
        $costoEnvioFijo = 150;   // Costo de envío si no alcanza
        $envio = ($total >= $montoEnvioGratis) ? 0 : $costoEnvioFijo;

        // === Aplicar cupón ===
        $descuento = 0;

        if (!empty($codigoCupon) && isset($cupon)) {
            if ($cupon->vCodigo_cupon === 'ENVIOGRATIS') {
                // Cupón especial: solo elimina el costo de envío
                $envio = 0;
            } else {
                // Cupones normales (porcentaje o monto)
                if ($cupon->eTipo === 'porcentaje') {
                    $descuento = $total * ($cupon->dDescuento / 100);
                } elseif ($cupon->eTipo === 'monto') {
                    $descuento = $cupon->dDescuento;
                }
            }
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
            'envio'
        ));
    }

    public function store(Request $request)
    {

        DB::beginTransaction();

        try {

            $usuario = Auth::user();
            $guestToken = session('guest_token');

            // 🛒 Carrito (guest o user)
            $carrito = CarritoHelper::carritoCheckout();

            if (!$carrito || $carrito->detalles->isEmpty()) {
                throw new Exception('El carrito está vacío.');
            }

            // 🧮 Totales
            [$subtotal, $totalImpuestos, $total] = $this->calcularTotales($carrito);

            // Definir reglas de envío
            $montoEnvioGratis = 1500; // Envío gratis si el total >= 1500
            $costoEnvioFijo = 150;   // Costo de envío si no alcanza
            $envio = ($total >= $montoEnvioGratis) ? 0 : $costoEnvioFijo;

            // 🎟️ Cupón
            $codigoCupon = session('codigo_cupon');
            $descuento = 0;
            $cupon = null;

            if ($codigoCupon) {
                $cupon = Cupon::whereRaw('BINARY vCodigo_cupon = ?', [$codigoCupon])
                    ->where('bActivo', 1)
                    ->first();

                if ($cupon) {
                    if ($cupon->vCodigo_cupon === 'ENVIOGRATIS') {
                        $envio = 0;
                    } elseif ($cupon->eTipo === 'porcentaje') {
                        $descuento = $total * ($cupon->dDescuento / 100);
                    } elseif ($cupon->eTipo === 'monto') {
                        $descuento = $cupon->dDescuento;
                    }
                }
            }

            $totalFinal = max(0, $total - $descuento + $envio);

            // 📍 DIRECCIONES
            if ($usuario) {
                $direccionEnvio = Direccion::findOrFail($request->id_direccion_envio);
                $direccionFacturacion = $request->id_direccion_facturacion
                    ? Direccion::findOrFail($request->id_direccion_facturacion)
                    : $direccionEnvio;
            } else {
                $direccionEnvio = DireccionGuest::findOrFail($request->id_direccion_envio);
                $direccionFacturacion = $request->id_direccion_facturacion
                    ? DireccionGuest::findOrFail($request->id_direccion_facturacion)
                    : $direccionEnvio;
            }

            // VALIDACIÓN DE STOCK FINAL ANTES DE CREAR EL PEDIDO
            foreach ($carrito->detalles as $detalle) {
                $producto = $detalle->producto;

                if ($producto->iStock <= 0) {
                    throw new Exception("El producto {$producto->vNombre} se agotó. Actualiza tu carrito.");
                }

                if ($detalle->iCantidad > $producto->iStock) {
                    throw new Exception("Stock insuficiente para {$producto->vNombre}. Solo quedan {$producto->iStock}.");
                }
            }

            // Crear el pedido
            $pedido = Pedido::create([
                'id_usuario' => $usuario?->id_usuario,
                'vGuest_token' => $usuario ? null : $guestToken,

                'vNombre' => $direccionEnvio->vNombre ?? $usuario->vNombre,
                'vApaterno' => $direccionEnvio->vApaterno ?? null,
                'vAmaterno' => $direccionEnvio->vAmaterno ?? null,
                'vEmail' => $usuario?->email ?? $request->email,

                // ENVÍO
                'env_telefono_contacto' => $direccionEnvio->vTelefono_contacto,
                'env_calle' => $direccionEnvio->vCalle,
                'env_numero_exterior' => $direccionEnvio->vNumero_exterior,
                'env_numero_interior' => $direccionEnvio->vNumero_interior,
                'env_colonia' => $direccionEnvio->vColonia,
                'env_codigo_postal' => $direccionEnvio->vCodigo_postal,
                'env_ciudad' => $direccionEnvio->vCiudad,
                'env_estado' => $direccionEnvio->vEstado,
                'env_entre_calle_1' => $direccionEnvio->vEntre_calle_1,
                'env_entre_calle_2' => $direccionEnvio->vEntre_calle_2,
                'env_referencias' => $direccionEnvio->tReferencias,

                // FACTURACIÓN
                'fac_telefono_contacto' => $direccionFacturacion->vTelefono_contacto,
                'fac_calle' => $direccionFacturacion->vCalle,
                'fac_numero_exterior' => $direccionFacturacion->vNumero_exterior,
                'fac_numero_interior' => $direccionFacturacion->vNumero_interior,
                'fac_colonia' => $direccionFacturacion->vColonia,
                'fac_codigo_postal' => $direccionFacturacion->vCodigo_postal,
                'fac_ciudad' => $direccionFacturacion->vCiudad,
                'fac_estado' => $direccionFacturacion->vEstado,
                'fac_entre_calle_1' => $direccionFacturacion->vEntre_calle_1,
                'fac_entre_calle_2' => $direccionFacturacion->vEntre_calle_2,
                'fac_referencias' => $direccionFacturacion->tReferencias,

                'vRFC' => $direccionFacturacion->vRFC ?? null,
                'dTotal' => $totalFinal,
                'eEstado' => 'pendiente',
            ]);

            // 📦 DETALLES
            foreach ($carrito->detalles as $detalle) {
                PedidoDetalle::create([
                    'id_pedido' => $pedido->id_pedido,
                    'id_producto' => $detalle->id_producto,
                    'iCantidad' => $detalle->iCantidad,
                    'dPrecio_unitario' => $detalle->dPrecio_unitario,
                ]);
            }

            // 🎟️ REGISTRAR USO DE CUPÓN
            if ($cupon) {
                CuponUso::create([
                    'id_cupon' => $cupon->id_cupon,
                    'id_venta' => '2',
                ]);
            }

            // 🧹 LIMPIAR CARRITO
            $carrito->detalles()->delete();
            $carrito->delete();

            session()->forget('codigo_cupon');

            DB::commit();

            return redirect()->route('home')->with('success', '✅ Pedido confirmado correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Error al procesar el pedido: ' . $e->getMessage());
        }
    }

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

            $codigo = $request->codigo;
            $usuario = Auth::user();

            $carrito = CarritoHelper::carritoCheckout();

            if (!$carrito || $carrito->detalles->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Carrito vacío.'
                ]);
            }

            [$subtotal, $totalImpuestos, $total] = $this->calcularTotales($carrito);

            $cupon = Cupon::whereRaw('BINARY vCodigo_cupon = ?', [$codigo])
                ->where('bActivo', 1)
                ->whereDate('dValido_desde', '<=', now())
                ->whereDate('dValido_hasta', '>=', now())
                ->first();

            if (!$cupon) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cupón inválido o expirado.'
                ]);
            }

            // Validar uso máximo
            $usosActuales = CuponUso::where('id_cupon', $cupon->id_cupon)->count();

            if ($usosActuales >= $cupon->iUso_maximo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este cupón ya alcanzó su límite de usos.'
                ]);
            }

            // 🚚 Cálculo de envío
            $montoEnvioGratis = 1500;
            $costoEnvioFijo = 150;
            $envio = ($total >= $montoEnvioGratis) ? 0 : $costoEnvioFijo;

            // 💸 Cálculo de descuento
            $descuento = 0;
            $mensaje = "Cupón aplicado correctamente: {$cupon->vCodigo_cupon}";

            if ($cupon->vCodigo_cupon === 'ENVIOGRATIS') {
                // Solo quitar el envío
                $envio = 0;
                $mensaje .= " — Envío gratis activado 🚚";
            } else {
                if ($cupon->eTipo === 'porcentaje') {
                    $descuento = $total * ($cupon->dDescuento / 100);
                } elseif ($cupon->eTipo === 'monto') {
                    $descuento = $cupon->dDescuento;
                }
                $mensaje .= " — Descuento: $" . number_format($descuento, 2, '.', ',');
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

    public function cancel()
    {
        return redirect()->route('carrito.index')
            ->with('info', 'Pago cancelado. Puedes editar tu carrito.');
    }
}
