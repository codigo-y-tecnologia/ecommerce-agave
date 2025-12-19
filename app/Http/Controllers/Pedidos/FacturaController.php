<?php

namespace App\Http\Controllers\Pedidos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pedido;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class FacturaController extends Controller
{
    public function download($id)
    {
        $pedido = Pedido::with([
                'detalles.producto',
                'direccion',
                'venta',
                'usuario'
            ])
            ->where('id_usuario', Auth::id())
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
