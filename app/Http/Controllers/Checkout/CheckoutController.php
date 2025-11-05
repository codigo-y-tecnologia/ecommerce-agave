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
    $usuario = Auth::user();

    $carrito = Carrito::where('id_usuario', $usuario->id_usuario)
        ->with(['detalles.producto.impuestos'])
        ->first();

    if (!$carrito || $carrito->detalles->isEmpty()) {
        return redirect()->route('carrito.index')->with('warning', 'Tu carrito está vacío.');
    }

    [$subtotal, $totalImpuestos, $total] = $this->calcularTotales($carrito);

    $direcciones = Direccion::where('id_usuario', $usuario->id_usuario)->get();

    $codigoCupon = session('codigo_cupon');
    $descuento = 0;

    if ($codigoCupon) {
        $cupon = Cupon::where('vCodigo_cupon', $codigoCupon)
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

    return view('checkout.index', compact(
        'carrito',
        'subtotal',
        'totalImpuestos',
        'total',
        'descuento',
        'totalFinal',
        'codigoCupon',
        'direcciones'
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
            ->with(['detalles.producto.impuestos'])
            ->firstOrFail();

        [$subtotal, $totalImpuestos, $total] = $this->calcularTotales($carrito);

        $descuento = 0;
        $codigoCupon = session('codigo_cupon');
        $cupon = null;

        if ($codigoCupon) {
            $cupon = Cupon::where('vCodigo_cupon', $codigoCupon)
                ->where('bActivo', 1)
                ->first();
            if ($cupon) {
                $descuento = $cupon->eTipo === 'porcentaje'
                    ? $total * ($cupon->dDescuento / 100)
                    : $cupon->dDescuento;
            }
        }

        $totalFinal = max(0, $total - $descuento);

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

        if ($cupon) {
            CuponUso::create([
                'id_cupon' => $cupon->id_cupon,
                'id_venta' => $pedido->id_pedido,
                'tFecha_uso' => now(),
            ]);
        }

        $carrito->detalles()->delete();
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
private function calcularTotales($carrito)
{
    $subtotal = 0;
    $totalImpuestos = 0;

    foreach ($carrito->detalles as $detalle) {
        $producto = $detalle->producto;
        $precio = $detalle->precio_unitario;
        $cantidad = $detalle->cantidad;

        $subtotalProducto = $precio * $cantidad;
        $subtotal += $subtotalProducto;

        // Calcular impuestos asociados a ese producto
        if ($producto->impuestos && $producto->impuestos->count() > 0) {
            foreach ($producto->impuestos as $impuesto) {
                if ($impuesto->bActivo) {
                    $totalImpuestos += ($subtotalProducto * ($impuesto->dPorcentaje / 100));
                }
            }
        }
    }

    $total = $subtotal + $totalImpuestos;

    return [$subtotal, $totalImpuestos, $total];
}

public function aplicarCupon(Request $request)
{
    $codigo = $request->codigo;
    $usuario = Auth::user();

    $carrito = Carrito::where('id_usuario', $usuario->id_usuario)
        ->with(['detalles.producto.impuestos'])
        ->first();

    if (!$carrito) {
        return response()->json(['success' => false, 'message' => 'Carrito vacío.']);
    }

    [$subtotal, $totalImpuestos, $total] = $this->calcularTotales($carrito);

    $cupon = Cupon::where('vCodigo_cupon', $codigo)
        ->where('bActivo', 1)
        ->whereDate('dValido_desde', '<=', now())
        ->whereDate('dValido_hasta', '>=', now())
        ->first();

    if (!$cupon) {
        return response()->json(['success' => false, 'message' => 'Cupón inválido o expirado.']);
    }

    // 🔹 Calcular descuento (puede ser 0 si es tipo "envío gratis" u otro)
    $descuento = 0;

    if ($cupon->eTipo === 'porcentaje') {
        $descuento = $total * ($cupon->dDescuento / 100);
    } elseif ($cupon->eTipo === 'monto' && $cupon->dDescuento > 0) {
        $descuento = $cupon->dDescuento;
    }

    // 🔹 Guardar el cupón en la sesión
    session(['codigo_cupon' => $codigo]);

    // 🔹 Mensaje personalizado según el tipo de cupón
    $mensaje = "Cupón aplicado correctamente: {$cupon->vCodigo_cupon}";

    if ($cupon->vCodigo_cupon === 'ENVIOGRATIS') {
        $mensaje .= " — Envío gratis activado 🚚";
    } elseif ($descuento > 0) {
        $mensaje .= " — Descuento: $" . number_format($descuento, 2);
    }

    return response()->json([
        'success' => true,
        'codigo' => $cupon->vCodigo_cupon,
        'descuento' => $descuento,
        'totalFinal' => max(0, $total - $descuento),
        'message' => $mensaje
    ]);
}

}
