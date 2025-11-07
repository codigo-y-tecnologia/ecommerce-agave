<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use Illuminate\Http\Request;

class VentaController extends Controller
{
    public function index()
    {
        $ventas = Venta::all();
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
            'id_pedido' => 'integer',
            'id_usuario' => 'integer',
            'dTotal' => 'numeric',
            'eMetodo_pago' => 'in:stripe,tarjeta,transferencia',
            'eEstado' => 'in:completada,devuelta,reembolsada,cancelada'
        ]);

        $venta->update($request->all());
        
        return redirect()->route('ventas.index')->with('success', 'Venta actualizada correctamente.');
    }
}