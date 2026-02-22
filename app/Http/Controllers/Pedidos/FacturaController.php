<?php

namespace App\Http\Controllers\Pedidos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pedido;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class FacturaController extends Controller
{

    private function queryPedidoBase()
    {
        if (Auth::check()) {
            return Pedido::where('id_usuario', Auth::id());
        }

        $guestToken = session('guest_token');

        if (!$guestToken) {
            abort(403, 'No autorizado');
        }

        return Pedido::where('vGuest_token', $guestToken);
    }

    public function download($id)
    {
        $pedido = $this->queryPedidoBase()
            ->with([
                'detalles.producto',
                'venta',
            ])
            ->findOrFail($id);

        /**
         * 🔒 SEGURIDAD:
         * Bloquear facturas si no hay venta o no está completada
         */
        abort_if(
            !$pedido->venta || $pedido->venta->eEstado !== 'completada',
            403,
            'La factura aún no está disponible.'
        );

        $pdf = Pdf::loadView('facturas.pdf', [
            'pedido' => $pedido,
            'venta'  => $pedido->venta,
        ]);

        return $pdf->download(
            'factura-pedido-' . $pedido->id_pedido . '.pdf'
        );
    }
}
