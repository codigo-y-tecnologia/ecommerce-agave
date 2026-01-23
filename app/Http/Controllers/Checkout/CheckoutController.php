<?php

namespace App\Http\Controllers\Checkout;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{Carrito, Pedido, PedidoDetalle, Direccion, Cupon, CuponUso, Pago, Venta, DetalleVenta};
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
        
        $direcciones = $usuario
            ? Direccion::where('id_usuario', $usuario->id_usuario)->get()
            : collect();

        // seleccionar la dirección principal
        $direccionPrincipal = $direcciones->firstWhere('bDireccion_principal', 1);

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

        $carrito = CarritoHelper::carritoCheckout();

        if (!$carrito || $carrito->detalles->isEmpty()) {
            throw new Exception('El carrito está vacío.');
        }

        $usuario = Auth::user();

        $request->validate([
            'id_direccion' => 'required|exists:tbl_direcciones,id_direccion'
        ]);

        $idDireccion = $request->id_direccion;

        DB::transaction(function () use ($usuario, $idDireccion) {
            $carrito = Carrito::where('id_usuario', $usuario->id_usuario)
                ->where('eEstado', 'activo')
                ->with(['detalles.producto.impuestos'])
                ->firstOrFail();

            [$subtotal, $totalImpuestos, $total] = $this->calcularTotales($carrito);

            $descuento = 0;
            $codigoCupon = session('codigo_cupon');
            $cupon = null;

            if ($codigoCupon) {
                $cupon = Cupon::whereRaw('BINARY vCodigo_cupon = ?', [$codigoCupon])
                    ->where('bActivo', 1)
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

            if ($totalFinal <= 0) {
                throw new Exception('El total del pedido no puede ser cero o negativo.');
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
                'vEmail_invitado' => $usuario ? null : $carrito->vEmail_invitado,
                'id_direccion' => $idDireccion,
                'eEstado' => 'pendiente',
                'dTotal' => $totalFinal,
                'tFecha_pedido' => now(),
            ]);

            foreach ($carrito->detalles as $detalle) {
                PedidoDetalle::create([
                    'id_pedido' => $pedido->id_pedido,
                    'id_producto' => $detalle->id_producto,
                    'iCantidad' => $detalle->cantidad,
                    'dPrecio_unitario' => $detalle->precio_unitario,
                ]);
            }
        });

        session()->forget('codigo_cupon');

        return redirect()->route('home')->with('success', '✅ Pedido confirmado correctamente.');
    }

    /**
     * Crear dirección (AJAX)
     */
    public function crearDireccion(Request $request)
    {
        try {

            $data = $request->validate([
                'vTelefono_contacto' => 'required|string|max:20',
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
            ]);

            $this->verificarYLimpiar($data, config('security.sql_keywords'));

            $data['bDireccion_principal'] = $request->has('bDireccion_principal') ? 1 : 0;
            $data['id_usuario'] = Auth::user()->id_usuario;

            // Si marca esta nueva como principal, desmarcamos las demás
            if (!empty($data['bDireccion_principal']) && $data['bDireccion_principal'] == 1) {
                Direccion::where('id_usuario', Auth::user()->id_usuario)
                    ->update(['bDireccion_principal' => 0]);
            }

            $direccion = Direccion::create($data);

            return response()->json(['success' => true, 'direccion' => $direccion]);
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
            $direccion = Direccion::where('id_direccion', $id)
                ->where('id_usuario', Auth::user()->id_usuario)
                ->firstOrFail();

            $data = $request->validate([
                'vTelefono_contacto' => 'required|string|max:20',
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
            ]);

            $this->verificarYLimpiar($data, config('security.sql_keywords'));

            $data['bDireccion_principal'] = $request->has('bDireccion_principal') ? 1 : 0;

            // Si el usuario marca esta como principal, desmarcamos todas las demás
            if (!empty($data['bDireccion_principal']) && $data['bDireccion_principal'] == 1) {
                Direccion::where('id_usuario', Auth::user()->id_usuario)
                    ->where('id_direccion', '!=', $id)
                    ->update(['bDireccion_principal' => 0]);
            }

            $direccion->update($data);

            return response()->json(['success' => true, 'direccion' => $direccion]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno: ' . $e->getMessage(),
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
        $codigo = $request->codigo;
        $usuario = Auth::user();

        $carrito = Carrito::where('id_usuario', $usuario->id_usuario)
            ->where('eEstado', 'activo')
            ->with(['detalles.producto.impuestos'])
            ->first();

        if (!$carrito) {
            return response()->json(['success' => false, 'message' => 'Carrito vacío.']);
        }

        [$subtotal, $totalImpuestos, $total] = $this->calcularTotales($carrito);

        $cupon = Cupon::whereRaw('BINARY vCodigo_cupon = ?', [$codigo])
            ->where('bActivo', 1)
            ->whereDate('dValido_desde', '<=', now())
            ->whereDate('dValido_hasta', '>=', now())
            ->first();

        if (!$cupon) {
            return response()->json(['success' => false, 'message' => 'Cupón inválido o expirado.']);
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

        // 🔹 Guardar el cupón en la sesión
        session(['codigo_cupon' => $codigo]);

        return response()->json([
            'success' => true,
            'codigo' => $cupon->vCodigo_cupon,
            'descuento' => $descuento,
            'envio' => $envio,
            'totalFinal' => $totalFinal,
            'message' => $mensaje
        ]);
    }
}
