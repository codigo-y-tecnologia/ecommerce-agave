<?php

namespace App\Http\Controllers\Carrito;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Carrito;
use App\Models\{CarritoDetalle, Producto, ProductoVariacion};
use App\Helpers\CarritoHelper;

class CarritoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $carrito = CarritoHelper::carritoActual()
            ->load('detalles.producto', 'detalles.variacion.atributos.valor');

        $mensajes = [];

        foreach ($carrito->detalles as $detalle) {

            $producto = $detalle->producto;

            if ($detalle->id_variacion) {
                // ── VARIACIÓN ──
                $variacion = $detalle->variacion;

                if (!$variacion || $variacion->iStock <= 0) {
                    $nombreProducto = $producto->vNombre ?? 'Producto eliminado';
                    $mensajes[] = "El producto {$nombreProducto} ya no tiene stock y fue eliminado del carrito.";
                    $detalle->delete();
                    continue;
                }

                if ($detalle->iCantidad > $variacion->iStock) {
                    $detalle->iCantidad = $variacion->iStock;
                    $detalle->save();
                    $mensajes[] = "La cantidad de {$producto->vNombre} fue ajustada al stock disponible ({$variacion->iStock} unidades).";
                }
            } else {

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
     * Agregar producto al carrito via AJAX (responde JSON, sin redirección).
     * Soporta producto simple y variaciones.
     */
    public function agregar(Request $request)
    {
        $request->validate([
            'producto_id'  => 'required|integer',
            'variacion_id' => 'nullable|integer',
            'cantidad'     => 'required|integer|min:1',
        ]);

        $productoId  = $request->producto_id;
        $variacionId = $request->variacion_id;
        $cantidad    = (int) $request->cantidad;

        // CASO 1: VARIACIÓN
        if ($variacionId) {
            $variacion = ProductoVariacion::with('productoPadre')
                ->where('id_variacion', $variacionId)
                ->where('bActivo', true)
                ->first();

            if (!$variacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Variación no encontrada.'
                ], 404);
            }

            if ($variacion->iStock <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta variación está agotada.'
                ]);
            }

            if ($cantidad > $variacion->iStock) {
                return response()->json([
                    'success' => false,
                    'message' => "Solo hay {$variacion->iStock} disponibles"
                ]);
            }

            $precio = $variacion->dPrecio_final;

            $carrito = CarritoHelper::carritoActual();

            $detalle = CarritoDetalle::where('id_carrito', $carrito->id_carrito)
                ->where('id_producto', $variacion->id_producto)
                ->where('id_variacion', $variacionId)
                ->first();

            if ($detalle) {
                $nuevaCantidad = $detalle->iCantidad + $cantidad;

                if ($nuevaCantidad > $variacion->iStock) {
                    return response()->json([
                        'success' => false,
                        'message' => "Solo hay {$variacion->iStock} unidades disponibles."
                    ]);
                }

                $detalle->iCantidad = $nuevaCantidad;
                $detalle->save();
            } else {
                if ($cantidad > $variacion->iStock) {
                    return response()->json([
                        'success' => false,
                        'message' => "Solo hay {$variacion->iStock} unidades disponibles."
                    ]);
                }

                CarritoDetalle::create([
                    'id_carrito'       => $carrito->id_carrito,
                    'id_producto'      => $variacion->id_producto,
                    'id_variacion'     => $variacionId,
                    'vNombre_variacion' => $variacion->getAtributosTexto(),
                    'iCantidad'        => $cantidad,
                    'dPrecio_unitario' => $precio,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Producto agregado al carrito.'
            ]);
        }

        //CASO 2: PRODUCTO SIMPLE 
        $producto = Producto::find($productoId);

        if (!$producto || !$producto->bActivo) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado.'
            ], 404);
        }

        if ($producto->iStock <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Este producto está agotado.'
            ]);
        }

        if ($cantidad > $producto->iStock) {
            return response()->json([
                'success' => false,
                'message' => "Solo hay {$producto->iStock} disponibles"
            ]);
        }

        $precioProducto = $producto->dPrecio_final;

        $carrito = CarritoHelper::carritoActual();

        $detalle = CarritoDetalle::where('id_carrito', $carrito->id_carrito)
            ->where('id_producto', $producto->id_producto)
            ->whereNull('id_variacion')
            ->first();

        if ($detalle) {
            $nuevaCantidad = $detalle->iCantidad + $cantidad;

            if ($nuevaCantidad > $producto->iStock) {
                return response()->json([
                    'success' => false,
                    'message' => "Solo hay {$producto->iStock} unidades disponibles."
                ]);
            }

            $detalle->iCantidad = $nuevaCantidad;
            $detalle->save();
        } else {
            if ($cantidad > $producto->iStock) {
                return response()->json([
                    'success' => false,
                    'message' => "Solo hay {$producto->iStock} unidades disponibles."
                ]);
            }

            CarritoDetalle::create([
                'id_carrito'       => $carrito->id_carrito,
                'id_producto'      => $producto->id_producto,
                'id_variacion'     => null,
                'vNombre_variacion' => null,
                'iCantidad'        => $cantidad,
                'dPrecio_unitario' => $precioProducto,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Producto agregado al carrito.'
        ]);
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

        $cantidadSolicitada = (int) $request->cantidad;

        // Determinar stock según si es variación o producto simple
        if ($detalle->id_variacion) {

            $variacion = $detalle->variacion;

            if (!$variacion || !$variacion->bActivo) {
                return redirect()->back()->with('warning', 'La variación ya no está disponible.');
            }

            if ($variacion->iStock <= 0) {
                return redirect()->back()->with('warning', 'Esta variación ya no tiene stock disponible.');
            }

            if ($cantidadSolicitada > $variacion->iStock) {
                return redirect()->back()->with(
                    'warning',
                    "Solo hay {$variacion->iStock} unidades disponibles para esta variación."
                );
            }
        } else {
            $producto = $detalle->producto;

            if (!$producto || $producto->iStock <= 0) {
                return redirect()->back()->with('warning', 'Este producto ya no tiene stock disponible.');
            }

            if ($cantidadSolicitada > $producto->iStock) {
                return redirect()->back()->with(
                    'warning',
                    "No puedes agregar {$cantidadSolicitada} unidades. Solo hay {$producto->iStock} disponibles."
                );
            }
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
