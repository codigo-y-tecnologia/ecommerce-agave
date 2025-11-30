<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorito;
use App\Models\Producto;
use Illuminate\Support\Facades\Auth;

class FavoritoController extends Controller
{
    public function index()
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return view('favoritos.no-autenticado');
        }

        $favoritos = Favorito::with(['producto.categoria', 'producto.marca', 'producto.etiquetas'])
            ->where('id_usuario', Auth::id())
            ->orderBy('tFecha_agregado', 'desc')
            ->get();

        return view('favoritos.index', compact('favoritos'));
    }

    public function create()
    {
        return redirect()->route('favoritos.index');
    }

    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Debes iniciar sesión para agregar favoritos',
                'redirect' => route('login')
            ], 401);
        }

        $request->validate([
            'id_producto' => 'required|exists:tbl_productos,id_producto'
        ]);

        try {
            $producto = Producto::where('id_producto', $request->id_producto)
                ->where('bActivo', true)
                ->firstOrFail();

            $favoritoExistente = Favorito::where('id_usuario', Auth::id())
                ->where('id_producto', $request->id_producto)
                ->first();

            if ($favoritoExistente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este producto ya está en tu lista de deseos'
                ]);
            }

            Favorito::create([
                'id_usuario' => Auth::id(),
                'id_producto' => $request->id_producto,
                'bNotificado_stock' => false,
                'bNotificado_descuento' => false
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Producto agregado a tu lista de deseos'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al agregar el producto a favoritos'
            ], 500);
        }
    }

    public function show(Favorito $favorito)
    {
        return redirect()->route('favoritos.index');
    }

    public function edit(Favorito $favorito)
    {
        return redirect()->route('favoritos.index');
    }

    public function update(Request $request, Favorito $favorito)
    {
        return redirect()->route('favoritos.index');
    }

    public function destroy($idProducto)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Debes iniciar sesión para gestionar favoritos'
            ], 401);
        }

        try {
            $favorito = Favorito::where('id_usuario', Auth::id())
                ->where('id_producto', $idProducto)
                ->firstOrFail();

            $favorito->delete();

            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado de tu lista de deseos'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el producto de favoritos'
            ], 500);
        }
    }

    // Métodos adicionales para nuestra funcionalidad
    public function toggle($idProducto)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Debes iniciar sesión para gestionar favoritos',
                'redirect' => route('login')
            ], 401);
        }

        try {
            $favorito = Favorito::where('id_usuario', Auth::id())
                ->where('id_producto', $idProducto)
                ->first();

            if ($favorito) {
                $favorito->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Producto eliminado de tu lista de deseos',
                    'action' => 'removed'
                ]);
            } else {
                Favorito::create([
                    'id_usuario' => Auth::id(),
                    'id_producto' => $idProducto,
                    'bNotificado_stock' => false,
                    'bNotificado_descuento' => false
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Producto agregado a tu lista de deseos',
                    'action' => 'added'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al gestionar favoritos: ' . $e->getMessage()
            ], 500);
        }
    }

    public function verificarNotificaciones()
    {
        if (!Auth::check()) {
            return [];
        }

        $favoritos = Favorito::with('producto')
            ->where('id_usuario', Auth::id())
            ->get();

        $notificaciones = [];

        foreach ($favoritos as $favorito) {
            $producto = $favorito->producto;

            // Verificar stock bajo
            if ($producto->estaBajoEnStock() && !$favorito->bNotificado_stock) {
                $notificaciones[] = [
                    'tipo' => 'stock',
                    'mensaje' => "¡Últimas unidades! {$producto->vNombre} se está agotando. ¡Compra ahora antes de que se acabe!",
                    'producto' => $producto,
                    'favorito' => $favorito
                ];

                $favorito->update(['bNotificado_stock' => true]);
            }

            // Verificar descuentos
            if ($producto->tieneDescuento() && !$favorito->bNotificado_descuento) {
                $porcentaje = $producto->porcentajeDescuento();
                $notificaciones[] = [
                    'tipo' => 'descuento',
                    'mensaje' => "¡Oferta especial! {$producto->vNombre} tiene {$porcentaje}% de descuento. ¡Aprovecha ahora!",
                    'producto' => $producto,
                    'favorito' => $favorito
                ];

                $favorito->update(['bNotificado_descuento' => true]);
            }
        }

        return $notificaciones;
    }
}