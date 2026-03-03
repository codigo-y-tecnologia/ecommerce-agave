<?php

namespace App\Http\Controllers\Checkout;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\Direccion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\Pdf;

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
        Log::info('📦 Mostrando pedido', ['pedido' => $pedido]);

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
            if ($cupon->eTipo !== 'envio_gratis') {
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

        if ($cupon && $cupon->eTipo === 'envio_gratis') {
            $envio = 0;
        }

        // -------------------------
        // TOTAL FINAL
        // -------------------------

        $totalFinal = max(0, $subtotalConImpuestos - $descuento + $envio);

        if ($pedido->id_usuario && $pedido->vGuest_token) {
            Pedido::where('vGuest_token', $guestToken)
                ->update([
                    'vGuest_token' => null
                ]);
        }

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

    public function pdf($id)
    {
        $pedido = Pedido::with(['detalles.producto.impuestos', 'venta'])
            ->findOrFail($id);

        // 🔐 Seguridad básica:
        // - Si está logueado, validar que el pedido sea suyo
        if (Auth::check() && $pedido->id_usuario !== Auth::id()) {
            abort(403);
        }

        // Si es invitado, el acceso debe venir desde order-received
        // (opcional: validar vGuest_token por query string)

        $payment_method = $pedido->venta->eMetodo_pago ?? 'No disponible';

        /**
         * 🔢 CÁLCULOS 
         */
        $subtotal = 0;
        $totalImpuestos = 0;
        $impuestosPorTipo = [];

        foreach ($pedido->detalles as $det) {

            $producto = $det->producto;
            $cantidad = $det->iCantidad;

            $precio = $producto->dPrecio_venta;

            $ieps = $producto->calcularIEPS();
            $iva  = $producto->calcularIVA($ieps);

            $subtotal += $precio * $cantidad;

            if ($ieps > 0) {
                $impuestosPorTipo['IEPS'] = ($impuestosPorTipo['IEPS'] ?? 0) + ($ieps * $cantidad);
            }
            if ($iva > 0) {
                $impuestosPorTipo['IVA'] = ($impuestosPorTipo['IVA'] ?? 0) + ($iva * $cantidad);
            }

            $totalImpuestos += ($ieps + $iva) * $cantidad;
        }

        $subtotalConImpuestos = $subtotal + $totalImpuestos;

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
            if ($cupon->eTipo !== 'envio_gratis') {
                if ($cupon->eTipo === 'porcentaje') {
                    $descuento = $subtotalConImpuestos * ($cupon->dDescuento / 100);
                } else {
                    $descuento = $cupon->dDescuento;
                }
            }
        }

        $montoEnvioGratis = 1500;
        $costoEnvioFijo = 150;

        $envio = ($subtotalConImpuestos >= $montoEnvioGratis) ? 0 : $costoEnvioFijo;

        if ($cupon && $cupon->eTipo === 'envio_gratis') {
            $envio = 0;
        }

        $totalFinal = max(0, $subtotalConImpuestos - $descuento + $envio);

        $pdf = Pdf::loadView('pdf.recibo', compact(
            'pedido',
            'payment_method',
            'subtotal',
            'totalImpuestos',
            'impuestosPorTipo',
            'subtotalConImpuestos',
            'envio',
            'descuento',
            'totalFinal',
            'cupon'
        ))->setPaper('letter');

        return $pdf->download('recibo-pedido-' . $pedido->id_pedido . '.pdf');
    }
}
