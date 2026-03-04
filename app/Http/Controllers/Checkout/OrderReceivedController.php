<?php

namespace App\Http\Controllers\Checkout;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Pedido, CheckoutSnapshot};
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

        $sessionID = $pedido->pago->vSessionID ?? null;

        $snapshot = CheckoutSnapshot::where('payment_session', $sessionID)
            ->lockForUpdate()
            ->first();

        if (!$snapshot) {
            abort(500, 'Snapshot no encontrado.');
        }

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
            'subtotal' => $snapshot->subtotal,
            'totalImpuestos' => $snapshot->impuestos,
            'impuestosPorTipo' => $snapshot->impuestos_por_tipo ?? [],
            'subtotalConImpuestos' => $snapshot->subtotal_con_impuestos,

            'envio' => $snapshot->envio,
            'descuento' => $snapshot->descuento,
            'totalFinal' => $snapshot->total_final,
            'cuponCodigo' => $snapshot->cupon_codigo,
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

        $sessionID = $pedido->pago->vSessionID ?? null;

        $snapshot = CheckoutSnapshot::where('payment_session', $sessionID)
            ->lockForUpdate()
            ->first();

        if (!$snapshot) {
            abort(500, 'Snapshot no encontrado.');
        }

        $pdf = Pdf::loadView('pdf.recibo', [
            'pedido' => $pedido,
            'payment_method' => $payment_method,
            'subtotal' => $snapshot->subtotal,
            'totalImpuestos' => $snapshot->impuestos,
            'impuestosPorTipo' => $snapshot->impuestos_por_tipo ?? [],
            'subtotalConImpuestos' => $snapshot->subtotal_con_impuestos,
            'envio' => $snapshot->envio,
            'descuento' => $snapshot->descuento,
            'totalFinal' => $snapshot->total_final,
            'cuponCodigo' => $snapshot->cupon_codigo,
        ])->setPaper('letter');

        return $pdf->download('recibo-pedido-' . $pedido->id_pedido . '.pdf');
    }
}
