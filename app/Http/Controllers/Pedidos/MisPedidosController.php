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
         * ===============================
         * CALCULAR TOTALES (HISTÓRICOS)
         * ===============================
         */

        $subtotal = 0;

        foreach ($pedido->detalles as $detalle) {
            $subtotal += $detalle->iCantidad * $detalle->dPrecio_unitario;
        }

        // Envío: total - subtotal - descuento
        // Si no manejas descuento explícito en DB, asumimos 0
        $descuento = 0;

        $envio = max(0, $pedido->dTotal - $subtotal + $descuento);

        return view('pedidos.show', [
            'pedido'    => $pedido,
            'subtotal'  => $subtotal,
            'envio'     => $envio,
            'descuento' => $descuento,
        ]);
    }
}
