<?php

namespace App\Http\Controllers\Carrito;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Carrito;
use App\Models\CarritoDetalle;
use App\Models\Producto;
use App\Helpers\CarritoHelper;

class CarritoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $carrito = CarritoHelper::carritoActual()
            ->load('detalles.producto');

        $mensajes = [];

        foreach ($carrito->detalles as $detalle) {
            $producto = $detalle->producto;

            // Producto eliminado o desactivado
            if (!$producto || $producto->iStock <= 0) {
                $mensajes[] = "El producto {$detalle->producto->vNombre} ya no tiene stock y fue eliminado del carrito.";
                $detalle->delete();
                continue;
            }

            // Ajustar cantidad si excede stock
            if ($detalle->iCantidad > $producto->iStock) {
                $detalle->iCantidad = $producto->iStock;
                $detalle->save();

                $mensajes[] = "La cantidad de {$producto->vNombre} fue ajustada al stock disponible.";
            }
        }

        $carrito->refresh();

        $detalles = $carrito->detalles;

        // Calcular total actualizado
        $total = $detalles->sum(
            fn($d) =>
            $d->dPrecio_unitario * $d->iCantidad
        );

        return view('carrito.index', [
            'detalles' => $detalles,
            'total' => $total,
            'warning' => $mensajes,
            'carrito_vacio' => $detalles->isEmpty()
                ? 'Tu carrito está vacío.'
                : null,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $idProducto)
    {

        $request->validate([
            'cantidad' => 'required|integer|min:1'
        ]);

        // Validar que el producto exista
        $producto = Producto::findOrFail($idProducto);

        if (is_null($producto->dPrecio_final)) {
            return redirect()->back()->with(
                'error',
                'Este producto no tiene precio configurado.'
            );
        }

        // Validación: producto sin stock
        if ($producto->iStock <= 0) {
            return redirect()->back()->with('warning', 'Este producto está agotado y no puede agregarse al carrito.');
        }

        // Cantidad solicitada 
        $cantidadSolicitada = (int) $request->cantidad;

        if ($cantidadSolicitada > $producto->iStock) {
            return redirect()->back()->with(
                'warning',
                "Solo hay {$producto->iStock} unidades disponibles."
            );
        }

        $carrito = CarritoHelper::carritoActual();

        $detalle = CarritoDetalle::where('id_carrito', $carrito->id_carrito)
            ->where('id_producto', $producto->id_producto)
            ->first();

        if ($detalle) {

            // Validar que no exceda stock disponible
            $nuevaCantidad = $detalle->iCantidad + $cantidadSolicitada;

            if ($nuevaCantidad > $producto->iStock) {
                return redirect()->back()->with(
                    'warning',
                    "Solo hay {$producto->iStock} unidades disponibles."
                );
            }

            // Si ya existe, actualizar cantidad
            $detalle->iCantidad = $nuevaCantidad;
            $detalle->save();
        } else {

            // Si no existe, crear nuevo detalle
            CarritoDetalle::create([
                'id_carrito' => $carrito->id_carrito,
                'id_producto' => $producto->id_producto,
                'iCantidad' => $cantidadSolicitada,
                'dPrecio_unitario' => $producto->dPrecio_final,
            ]);
        }

        return redirect()->route('carrito.index')->with('success', 'Producto agregado al carrito.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $idDetalle)
    {
        $request->validate([
            'cantidad' => 'required|integer|min:1'
        ]);

        $carrito = CarritoHelper::carritoActual();

        $detalle = CarritoDetalle::where('id_detalle_carrito', $idDetalle)
            ->where('id_carrito', $carrito->id_carrito)
            ->firstOrFail();

        $producto = $detalle->producto;
        $cantidadSolicitada = (int) $request->cantidad;

        // Validar stock disponible
        if ($cantidadSolicitada > $producto->iStock) {
            return redirect()->back()->with(
                'warning',
                "No puedes agregar {$cantidadSolicitada} unidades. Solo hay {$producto->iStock} disponibles."
            );
        }

        $detalle->iCantidad = $cantidadSolicitada;
        $detalle->save();

        return redirect()->route('carrito.index')->with('success', 'Cantidad actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($idDetalle)
    {
        $carrito = CarritoHelper::carritoActual();

        $detalle = CarritoDetalle::where('id_detalle_carrito', $idDetalle)
            ->where('id_carrito', $carrito->id_carrito)
            ->firstOrFail();

        $detalle->delete();

        return redirect()->route('carrito.index')->with('success', 'Producto eliminado del carrito.');
    }
}
