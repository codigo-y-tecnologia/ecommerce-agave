<?php

namespace App\Http\Controllers\Pedidos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pedido;
use Illuminate\Support\Facades\Auth;

class MisPedidosController extends Controller
{
    public function index(Request $request)
{
    $fechaFiltro = $request->get('fecha', '30d');

    $query = Pedido::where('id_usuario', Auth::id());

    switch ($fechaFiltro) {

        case '3m':
            $query->where('tFecha_pedido', '>=', now()->subMonths(3));
            break;

        case '30d':
            $query->where('tFecha_pedido', '>=', now()->subDays(30));
            break;

        default:
            // Año (ej: 2025, 2024, etc.)
            if (is_numeric($fechaFiltro)) {
                $query->whereYear('tFecha_pedido', $fechaFiltro);
            }
            break;
    }

    $pedidos = $query
        ->with('ultimaSolicitudPostventa')
        ->orderByDesc('tFecha_pedido')
        ->paginate(10)
        ->withQueryString();

    // Años disponibles (como Amazon)
    $years = Pedido::where('id_usuario', Auth::id())
        ->selectRaw('YEAR(tFecha_pedido) as year')
        ->distinct()
        ->orderByDesc('year')
        ->pluck('year');

    return view('pedidos.index', compact('pedidos', 'fechaFiltro', 'years'));
}

    public function show($id)
    {
        $pedido = Pedido::with([
                'detalles.producto',
                'direccion',
                'direccionFacturacion',
                'pago',
                'venta',
                'envio',
                'ultimaSolicitudPostventa',
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
