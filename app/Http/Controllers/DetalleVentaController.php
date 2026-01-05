<?php

namespace App\Http\Controllers;

use App\Models\DetalleVenta;
use App\Models\Venta;
use App\Models\Producto;
use Illuminate\Http\Request;
use PDF;

class DetalleVentaController extends Controller
{
    public function index(Request $request)
    {
        $query = DetalleVenta::with(['venta', 'producto'])
            ->orderBy('id_detalle_venta', 'desc');

        // Agregar búsqueda por ID
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            
            // Búsqueda SEGURA - solo en campos numéricos
            $query->where(function($q) use ($search) {
                // Buscar solo por IDs y números
                if (is_numeric($search)) {
                    $q->where('id_detalle_venta', $search)
                      ->orWhere('id_venta', $search)
                      ->orWhere('id_producto', $search)
                      ->orWhere('iCantidad', $search)
                      ->orWhere('dprecio_unitario', $search)
                      ->orWhere('dSubtotal', $search);
                      
                      ;
                } else {
                    // Si no es numérico, solo buscar en campos de texto si existen
                    $q->where('id_detalle_venta', 'like', "%{$search}%")
                      ->orWhere('id_venta', 'like', "%{$search}%")
                      ->orWhere('id_producto', 'like', "%{$search}%");
                }
            });
        }

        $detallesVenta = $query->paginate(10);
        
        return view('detalle_venta.index', compact('detallesVenta'));
    }

    public function create()
    {
        $ventas = Venta::orderBy('id_venta', 'desc')->get();
        $productos = Producto::orderBy('id_producto', 'desc')->get();
        
        return view('detalle_venta.create', compact('ventas', 'productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_venta' => 'required|exists:ventas,id_venta',
            'id_producto' => 'required|exists:productos,id_producto',
            'iCantidad' => 'required|integer|min:1',
            'dPrecio_unitario' => 'required|numeric|min:0',
        ]);

        $subtotal = $request->iCantidad * $request->dPrecio_unitario;

        DetalleVenta::create([
            'id_venta' => $request->id_venta,
            'id_producto' => $request->id_producto,
            'iCantidad' => $request->iCantidad,
            'dPrecio_unitario' => $request->dPrecio_unitario,
            'dSubtotal' => $subtotal,
        ]);

        return redirect()->route('detalle_venta.index')
            ->with('success', 'Detalle de venta creado correctamente.');
    }

    public function show($id)
    {
        $detalleVenta = DetalleVenta::with(['venta', 'producto'])->findOrFail($id);
        return view('detalle_venta.show', compact('detalleVenta'));
    }

    public function edit($id)
    {
        $detalleVenta = DetalleVenta::findOrFail($id);
        $ventas = Venta::orderBy('id_venta', 'desc')->get();
        $productos = Producto::orderBy('id_producto', 'desc')->get();
        return view('detalle_venta.edit', compact('detalleVenta', 'ventas', 'productos'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_venta' => 'required|exists:ventas,id_venta',
            'id_producto' => 'required|exists:productos,id_producto',
            'iCantidad' => 'required|integer|min:1',
            'dPrecio_unitario' => 'required|numeric|min:0',
        ]);

        $detalleVenta = DetalleVenta::findOrFail($id);
        
        $subtotal = $request->iCantidad * $request->dPrecio_unitario;

        $detalleVenta->update([
            'id_venta' => $request->id_venta,
            'id_producto' => $request->id_producto,
            'iCantidad' => $request->iCantidad,
            'dPrecio_unitario' => $request->dPrecio_unitario,
            'dSubtotal' => $subtotal,
        ]);

        return redirect()->route('detalle_venta.index')
            ->with('success', 'Detalle de venta actualizado correctamente.');
    }

    public function destroy($id)
    {
        $detalleVenta = DetalleVenta::findOrFail($id);
        $detalleVenta->delete();

        return redirect()->route('detalle_venta.index')
            ->with('success', 'Detalle de venta eliminado correctamente.');
    }
    public function generarPDF($id)
{
    $detalle = DetalleVenta::findOrFail($id); // ← Cambia aquí
    $pdf = PDF::loadView('detalles.pdf', compact('detalle'));
    return $pdf->download("detalle-{$id}.pdf");
}
}