<?php

namespace App\Http\Controllers\Checkout;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\Direccion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderReceivedController extends Controller
{
    public function show($id)
    {

        Log::info('📦 Mostrando order-received', ['id_pedido' => $id]);

        $pedido = Pedido::with(['detalles.producto', 'usuario', 'venta'])
            ->where('id_usuario', Auth::user()->id_usuario)
            ->findOrFail($id);

        $direccion = Direccion::find($pedido->id_direccion);

        // Obtener método de pago desde la venta
        $payment_method = $pedido->venta->eMetodo_pago ?? 'No disponible';

        // -------------------------
    // CÁLCULOS: subtotales + impuestos por tipo
    // -------------------------
    $subtotal = 0; // subtotal **CON impuestos incluidos**
    $totalImpuestos = 0;
    $impuestosPorTipo = []; // ['IVA' => 123.45, 'IEPS' => 50.00, ...]

    foreach ($pedido->detalles as $det) {
        $producto = $det->producto;
        $cantidad = (int) $det->iCantidad;

        // Precio unitario guardado en pedido_detalle (asumimos SIN impuestos)
        $precioUnitarioSinImp = $det->dPrecio_unitario;

        // Calcular porcentaje total y monto de impuestos por impuesto
        $porcentajeTotal = $producto->impuestos->where('bActivo', 1)->sum('dPorcentaje');

        // precio unitario CON impuestos:
        $precioUnitarioConImp = $precioUnitarioSinImp * (1 + ($porcentajeTotal / 100));

        // subtotal de la línea con impuestos
        $lineSubtotalConImp = $precioUnitarioConImp * $cantidad;

        // acumular subtotal (con impuestos)
        $subtotal += $lineSubtotalConImp;

        // Desglosar impuestos por cada impuesto aplicado al producto
        foreach ($producto->impuestos->where('bActivo', 1) as $imp) {
            $nombreImp = $imp->vNombre ?? ('Impuesto ' . $imp->id_impuesto);
            // monto del impuesto para la línea: base * porcentaje
            $montoImpLinea = $precioUnitarioSinImp * ($imp->dPorcentaje / 100) * $cantidad;

            if (!isset($impuestosPorTipo[$nombreImp])) {
                $impuestosPorTipo[$nombreImp] = 0;
            }
            $impuestosPorTipo[$nombreImp] += $montoImpLinea;
            $totalImpuestos += $montoImpLinea;
        }
    }

    // -------------------------
    // DESCUENTO (si existe cupón vinculado a la venta, lo aplicamos sobre subtotal CON impuestos)
    // -------------------------
    $descuento = 0;
    $cupon = null;

    // Intentamos obtener Cupon vía CuponUso (se creó en finalizeOrderFromCart)
    try {
        $cuponUso = \App\Models\CuponUso::where('id_venta', $pedido->venta->id_venta ?? 0)->first();
        if ($cuponUso) {
            $cupon = $cuponUso->cupon ?? \App\Models\Cupon::find($cuponUso->id_cupon);
        }
    } catch (\Throwable $e) {
        // No crítico, solo continuar con descuento 0
        Log::info('No se encontró CuponUso o relación: ' . $e->getMessage());
    }

    if ($cupon) {
        if ($cupon->vCodigo_cupon === 'ENVIOGRATIS') {
            // lo manejaremos en la sección de envío
            $descuento = 0;
        } else {
            if ($cupon->eTipo === 'porcentaje') {
                $descuento = $subtotal * ($cupon->dDescuento / 100);
            } else { // 'monto'
                $descuento = $cupon->dDescuento;
            }
        }
    }

    // -------------------------
    // ENVÍO
    // -------------------------
    $montoEnvioGratis = 1500;
    $costoEnvioFijo = 150;
    $envio = ($subtotal >= $montoEnvioGratis) ? 0 : $costoEnvioFijo;

    // Si el cupón es ENVIOGRATIS, forzamos envío = 0
    if ($cupon && $cupon->vCodigo_cupon === 'ENVIOGRATIS') {
        $envio = 0;
    }

    // -------------------------
    // TOTAL FINAL
    // -------------------------
    $totalFinal = max(0, $subtotal - $descuento + $envio);


        return view('checkout.order-received', [
            'pedido' => $pedido,
            'direccion' => $direccion,
            'payment_method' => $payment_method,
            'nota_pedido' => $pedido->nota ?? null,
            'subtotal' => $subtotal,
            'totalImpuestos' => $totalImpuestos,
            'impuestosPorTipo' => $impuestosPorTipo,
            'envio' => $envio,
            'descuento' => $descuento,
            'totalFinal' => $totalFinal,
            'cupon' => $cupon,
        ]);
    }
}
