<?php

namespace App\Http\Controllers\Checkout;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\Direccion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class OrderReceivedController extends Controller
{
    public function show($id)
    {
        Log::info('📦 Mostrando order-received', ['id_pedido' => $id]);

        $query = Pedido::with(['detalles.producto.impuestos', 'venta'])
            ->where('id_pedido', $id);

        // 🔐 Usuario logueado
        if (Auth::check()) {
            $query->where('id_usuario', Auth::user()->id_usuario);
        }
        // 🔐 Invitado
        else {
            $guestToken = Session::get('guest_token');

            if (!$guestToken) {
                abort(403);
            }

            $query->where('vGuest_token', $guestToken);
        }

        $pedido = $query->firstOrFail();

        // $pedido = Pedido::with(['detalles.producto.impuestos', 'usuario', 'venta'])
        //     ->where('id_usuario', Auth::user()->id_usuario)
        //     ->findOrFail($id);

        // $direccion = Direccion::find($pedido->id_direccion);

        // Método de pago
        $payment_method = $pedido->venta->eMetodo_pago ?? 'No disponible';

        // -------------------------
        // CÁLCULOS DE IMPUESTOS
        // -------------------------

        $subtotalSinImpuestos = 0;
        $totalImpuestos = 0;
        $impuestosPorTipo = [];

        foreach ($pedido->detalles as $det) {

            $producto = $det->producto;
            $cantidad = $det->iCantidad;

            $precio_base = $producto->dPrecio_venta;

            // mismos métodos usados en checkout
            $ieps = $producto->calcularIEPS();
            $iva  = $producto->calcularIVA($ieps);

            $precio_unitario_con_imp = $precio_base + $ieps + $iva;

            // Acumular subtotal SIN impuestos
            $subtotalSinImpuestos += ($precio_base * $cantidad);

            // Impuestos por tipo
            if ($ieps > 0) {
                $impuestosPorTipo['IEPS'] = ($impuestosPorTipo['IEPS'] ?? 0) + ($ieps * $cantidad);
            }
            if ($iva > 0) {
                $impuestosPorTipo['IVA'] = ($impuestosPorTipo['IVA'] ?? 0) + ($iva * $cantidad);
            }

            // Acumular total impuestos
            $totalImpuestos += ($ieps + $iva) * $cantidad;
        }

        // Subtotal CON impuestos (como en checkout)
        $subtotalConImpuestos = $subtotalSinImpuestos + $totalImpuestos;

        // -------------------------
        // DESCUENTO
        // -------------------------

        $descuento = 0;
        $cupon = null;

        try {
            $cuponUso = \App\Models\CuponUso::where('id_venta', $pedido->venta->id_venta ?? 0)->first();
            if ($cuponUso) {
                $cupon = $cuponUso->cupon ?? \App\Models\Cupon::find($cuponUso->id_cupon);
            }
        } catch (\Throwable $e) {
            Log::info('No se encontró CuponUso: ' . $e->getMessage());
        }

        if ($cupon) {
            if ($cupon->vCodigo_cupon !== 'ENVIOGRATIS') {
                if ($cupon->eTipo === 'porcentaje') {
                    $descuento = $subtotalConImpuestos * ($cupon->dDescuento / 100);
                } else {
                    $descuento = $cupon->dDescuento;
                }
            }
        }

        // -------------------------
        // ENVÍO
        // -------------------------

        $montoEnvioGratis = 1500;
        $costoEnvioFijo = 150;

        $envio = ($subtotalConImpuestos >= $montoEnvioGratis) ? 0 : $costoEnvioFijo;

        if ($cupon && $cupon->vCodigo_cupon === 'ENVIOGRATIS') {
            $envio = 0;
        }

        // -------------------------
        // TOTAL FINAL
        // -------------------------

        $totalFinal = max(0, $subtotalConImpuestos - $descuento + $envio);

        return view('checkout.order-received', [
            'pedido' => $pedido,
            //'direccion' => $direccion,
            'payment_method' => $payment_method,
            'nota_pedido' => $pedido->nota ?? null,

            // Para mostrar igual que en checkout:
            'subtotal' => $subtotalSinImpuestos,
            'totalImpuestos' => $totalImpuestos,
            'impuestosPorTipo' => $impuestosPorTipo,
            'subtotalConImpuestos' => $subtotalConImpuestos,

            'envio' => $envio,
            'descuento' => $descuento,
            'totalFinal' => $totalFinal,
            'cupon' => $cupon,
        ]);
    }
}
