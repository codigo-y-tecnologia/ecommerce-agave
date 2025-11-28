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

class CheckoutController extends Controller
{

    use InputSanitizer;

    public function index()
{

    // Si el usuario viene de Stripe o PayPal y el pago fue exitoso
    if (request()->paid == 1) {
        session()->forget('codigo_cupon');
    }

    $usuario = Auth::user();

    $carrito = Carrito::where('id_usuario', $usuario->id_usuario)
        ->where('eEstado', 'activo')
        ->orderByDesc('id_carrito')
        ->with(['detalles.producto.impuestos'])
        ->first();

    if (!$carrito || $carrito->detalles->isEmpty()) {
        return redirect()->route('carrito.index')->with('warning', 'Tu carrito está vacío.');
    }

    [$subtotal, $totalImpuestos, $total] = $this->calcularTotales($carrito);

    $direcciones = Direccion::where('id_usuario', $usuario->id_usuario)->get();

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
    $request->validate([
        'id_direccion' => 'required|exists:tbl_direcciones,id_direccion'
    ]);

    $usuario = Auth::user();
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

        // Crear el pedido
        $pedido = Pedido::create([
            'id_usuario' => $usuario->id_usuario,
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

        // if ($cupon) {
        //     CuponUso::create([
        //         'id_cupon' => $cupon->id_cupon,
        //         'id_venta' => $pedido->id_pedido,
        //         'tFecha_uso' => now(),
        //     ]);
        // }

        // $carrito->detalles()->delete();
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
        'vNumero_exterior' => 'nullable|string|max:20',
        'vNumero_interior' => 'nullable|string|max:20',
        'vColonia' => 'nullable|string|max:150',
        'vCodigo_postal' => 'nullable|string|max:10',
        'vCiudad' => 'nullable|string|max:80',
        'vEstado' => 'nullable|string|max:80',
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
            'vNumero_exterior' => 'nullable|string|max:20',
            'vNumero_interior' => 'nullable|string|max:20',
            'vColonia' => 'nullable|string|max:150',
            'vCodigo_postal' => 'nullable|string|max:10',
            'vCiudad' => 'nullable|string|max:80',
            'vEstado' => 'nullable|string|max:80',
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
        $precioConImpuestos = $detalle->precio_unitario;
        $cantidad = $detalle->cantidad;

        // Obtener porcentaje total de impuestos activos del producto
        $porcentajeTotal = $producto->impuestos
            ->where('bActivo', 1)
            ->sum('dPorcentaje');

        // Calcular precio sin impuestos
        $precioSinImpuestos = $precioConImpuestos / (1 + ($porcentajeTotal / 100));

        // Calcular totales
        $subtotalProducto = $precioSinImpuestos * $cantidad;
        $impuestosProducto = ($precioConImpuestos - $precioSinImpuestos) * $cantidad;

        // Acumular
        $subtotal += $subtotalProducto;
        $totalImpuestos += $impuestosProducto;
    }

    // El total es la suma del subtotal sin impuestos + los impuestos
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

    // if ($cupon->eTipo === 'porcentaje') {
    //     $descuento = $total * ($cupon->dDescuento / 100);
    // } elseif ($cupon->eTipo === 'monto' && $cupon->dDescuento > 0) {
    //     $descuento = $cupon->dDescuento;
    // }

    // 🔹 Guardar el cupón en la sesión
    session(['codigo_cupon' => $codigo]);

    // // 🔹 Mensaje personalizado según el tipo de cupón
    // $mensaje = "Cupón aplicado correctamente: {$cupon->vCodigo_cupon}";

    // if ($cupon->vCodigo_cupon === 'ENVIOGRATIS') {
    //     $mensaje .= " — Envío gratis activado 🚚";
    // } elseif ($descuento > 0) {
    //     $mensaje .= " — Descuento: $" . number_format($descuento, 2);
    // }

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
