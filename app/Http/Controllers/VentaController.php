<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use Illuminate\Http\Request;

class VentaController extends Controller
{
    public function index(Request $request)
    {
        $query = Venta::query();

        // BÚSQUEDA ÚNICA EN MÚLTIPLES CAMPOS
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('id_venta', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('id_pedido', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('id_usuario', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('dTotal', 'LIKE', "%{$searchTerm}%");

                // BÚSQUEDA POR FECHA 
                if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $searchTerm)) {
                    try {
                        $fecha = \Carbon\Carbon::createFromFormat('d/m/Y', $searchTerm);
                        $q->orWhereDate('tFecha_venta', $fecha->format('Y-m-d'));
                    } catch (\Exception $e) {
                    }
                }
            });
        }

        // Filtros adicionales
        if ($request->filled('search_metodo_pago')) {
            $query->where('eMetodo_pago', $request->search_metodo_pago);
        }

        if ($request->filled('search_estado')) {
            $query->where('eEstado', $request->search_estado);
        }

        $ventas = $query->orderBy('tFecha_venta', 'desc')->paginate(10);

        return view('ventas.index', compact('ventas'));
    }

    public function show(Venta $venta)
    {
        return view('ventas.show', compact('venta'));
    }

    public function edit(Venta $venta)
    {
        return view('ventas.edit', compact('venta'));
    }

    public function update(Request $request, Venta $venta)
    {
        $request->validate([
            'id_pedido' => 'required|integer',
            'id_usuario' => 'required|integer',
            'dTotal' => 'required|numeric|min:0',
            'eMetodo_pago' => 'required|in:stripe,paypal',
            'eEstado' => 'required|in:completada,devuelta,reembolsada,cancelada'
        ]);

        $venta->update($request->all());

        return redirect()->route('ventas.index')->with('success', 'Venta actualizada correctamente.');
    }
}
