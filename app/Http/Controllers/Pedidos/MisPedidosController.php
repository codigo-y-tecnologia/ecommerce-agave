<?php

namespace App\Http\Controllers\Pedidos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pedido;
use Illuminate\Support\Facades\Auth;

class MisPedidosController extends Controller
{
    public function index()
    {
        $pedidos = Pedido::where('id_usuario', Auth::id())
            ->with(['pago'])
            ->orderByDesc('id_pedido')
            ->paginate(10);

        return view('pedidos.index', compact('pedidos'));
    }

    public function show($id)
    {
        $pedido = Pedido::with([
                'detalles.producto',
                'direccion',
                'direccionFacturacion',
                'pago',
                'venta'
            ])
            ->where('id_usuario', Auth::id())
            ->findOrFail($id);

        /**
         * =====================================
         * TOTALES HISTÓRICOS (SEGUROS)
         * =====================================
         */

        // Subtotal real del pedido
        $subtotal = $pedido->detalles->sum(function ($detalle) {
            return $detalle->iCantidad * $detalle->dPrecio_unitario;
        });

        // Descuento aplicado (si existe)
        $descuento = $pedido->venta->dDescuento ?? 0;

        // Costo de envío real (si existe)
        $envio = $pedido->venta->dCosto_envio ?? 0;

        return view('pedidos.show', [
            'pedido'    => $pedido,
            'subtotal'  => $subtotal,
            'envio'     => $envio,
            'descuento' => $descuento,
        ]);
    }
}
