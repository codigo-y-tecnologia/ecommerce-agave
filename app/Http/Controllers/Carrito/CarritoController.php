<?php

namespace App\Http\Controllers\Carrito;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Carrito;
use App\Models\CarritoDetalle;
use App\Models\Producto;

class CarritoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $usuario = Auth::user();

        // Buscar carrito activo del usuario
        $carrito = Carrito::where('id_usuario', $usuario->id_usuario)
            ->where('eEstado', 'activo')
            ->with('detalles.producto') // eager load para no hacer muchas queries
            ->first();

            if (!$carrito || $carrito->detalles->isEmpty()) {
            // Si no tiene carrito o no hay productos
            return view('carrito.index', [
                'detalles' => [],
                'total' => 0
            ])->with('carrito_vacio', 'Tu carrito está vacío.');
        }

        // Calcular total
        $total = $carrito->detalles->sum('dSubtotal');

        return view('carrito.index', [
            'detalles' => $carrito->detalles,
            'total' => $total
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
         $usuario = Auth::user();

        // Validar que el producto exista
        $producto = Producto::findOrFail($idProducto);

        // Buscar o crear carrito activo del usuario
        $carrito = Carrito::firstOrCreate(
            [
                'id_usuario' => $usuario->id_usuario,
                'eEstado' => 'activo'
            ],
            [
                'id_usuario' => $usuario->id_usuario,
                'eEstado' => 'activo'
            ]
        );

        // Revisar si el producto ya está en el carrito
        $detalle = CarritoDetalle::where('id_carrito', $carrito->id_carrito)
            ->where('id_producto', $producto->id_producto)
            ->first();

        if ($detalle) {
            // Si ya existe, actualizar cantidad
            $detalle->iCantidad += $request->input('cantidad', 1);
            //$detalle->subtotal = $detalle->cantidad * $detalle->precio_unitario;
            $detalle->save();
        } else {
            // Si no existe, crear nuevo detalle
            CarritoDetalle::create([
                'id_carrito' => $carrito->id_carrito,
                'id_producto' => $producto->id_producto,
                'iCantidad' => $request->input('cantidad', 1),
                'dPrecio_unitario' => $producto->precio_con_impuestos,
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

        $detalle = CarritoDetalle::findOrFail($idDetalle);
        $detalle->iCantidad = $request->input('cantidad');
        $detalle->save();

        return redirect()->route('carrito.index')->with('success', 'Cantidad actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($idDetalle)
    {
        $detalle = CarritoDetalle::findOrFail($idDetalle);
        $detalle->delete();

        return redirect()->route('carrito.index')->with('success', 'Producto eliminado del carrito.');
    }
}
