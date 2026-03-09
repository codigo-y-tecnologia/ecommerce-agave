<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pedido;
use Carbon\Carbon;

class PedidoController extends Controller
{
    public function index(Request $request)
    {
        $pedidos = Pedido::with(['venta', 'envio']);

        // ============================
        // FILTROS RÁPIDOS
        // ============================
        if ($request->quick === 'today') {
            $pedidos->whereDate('tFecha_pedido', Carbon::today());
        }

        if ($request->quick === 'week') {
            $pedidos->whereBetween('tFecha_pedido', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek(),
            ]);
        }

        if ($request->quick === 'month') {
            $pedidos->whereMonth('tFecha_pedido', Carbon::now()->month)
                ->whereYear('tFecha_pedido', Carbon::now()->year);
        }

        // ============================
        // FILTROS AVANZADOS
        // ============================
        $pedidos->when(
            $request->pedido_id,
            fn($q) =>
            $q->where('id_pedido', $request->pedido_id)
        );

        $pedidos->when($request->cliente, function ($q) use ($request) {
            $q->where(function ($sub) use ($request) {
                $sub->where('vNombre', 'like', "%{$request->cliente}%")
                    ->orWhere('vApaterno', 'like', "%{$request->cliente}%")
                    ->orWhere('vAmaterno', 'like', "%{$request->cliente}%")
                    ->orWhere('vEmail', 'like', "%{$request->cliente}%");
            });
        });

        $pedidos->when(
            $request->estado_pedido,
            fn($q) =>
            $q->where('eEstado', $request->estado_pedido)
        );

        $pedidos->when($request->metodo_pago, function ($q) use ($request) {
            $q->whereHas(
                'venta',
                fn($v) =>
                $v->where('eMetodo_pago', $request->metodo_pago)
            );
        });

        $pedidos->when($request->estado_envio, function ($q) use ($request) {
            $q->whereHas(
                'envio',
                fn($e) =>
                $e->where('eEstado', $request->estado_envio)
            );
        });

        $pedidos->when(
            $request->total_min,
            fn($q) =>
            $q->where('dTotal', '>=', $request->total_min)
        );

        $pedidos->when(
            $request->total_max,
            fn($q) =>
            $q->where('dTotal', '<=', $request->total_max)
        );

        $pedidos = $pedidos
            ->orderByDesc('tFecha_pedido')
            ->paginate(15)
            ->withQueryString();

        return view('admin.pedidos.index', compact('pedidos'));
    }

    public function show($id)
    {
        $pedido = Pedido::with([
            'detalles.producto',
            'direccion',
            'venta',
            'envio'
        ])
            ->findOrFail($id);

        $subtotal = $pedido->detalles->sum(function ($det) {
            return $det->iCantidad * $det->dPrecio_unitario;
        });

        return view('admin.pedidos.show', compact('pedido', 'subtotal'));
    }
}
