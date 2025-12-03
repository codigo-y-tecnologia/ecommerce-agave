<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorito;
use App\Models\Producto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FavoritoController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('info', 'Por favor inicia sesión para ver tus favoritos.');
        }

        try {
            $favoritos = Favorito::with(['producto.categoria', 'producto.marca', 'producto.etiquetas'])
                ->where('id_usuario', Auth::id())
                ->orderBy('tFecha_agregado', 'desc')
                ->get();

            return view('favoritos.index', compact('favoritos'));
        } catch (\Exception $e) {
            \Log::error('Error cargando favoritos: ' . $e->getMessage());
            
            $favoritos = collect();
            return view('favoritos.index', compact('favoritos'))
                ->with('error', 'Error al cargar tus favoritos. Por favor intenta más tarde.');
        }
    }

    public function toggle($idProducto)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Debes iniciar sesión para gestionar favoritos',
                'redirect' => true
            ], 401);
        }

        try {
            DB::beginTransaction();

            $producto = Producto::where('id_producto', $idProducto)
                ->where('bActivo', true)
                ->first();

            if (!$producto) {
                return response()->json([
                    'success' => false,
                    'message' => 'Producto no encontrado'
                ], 404);
            }

            $favorito = Favorito::where('id_usuario', Auth::id())
                ->where('id_producto', $idProducto)
                ->first();

            if ($favorito) {
                $favorito->delete();
                
                DB::commit();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Producto eliminado de tu lista de deseos',
                    'action' => 'removed',
                    'producto_id' => $idProducto
                ]);
            } else {
                Favorito::create([
                    'id_usuario' => Auth::id(),
                    'id_producto' => $idProducto,
                    'bNotificado_stock' => false,
                    'bNotificado_descuento' => false,
                    'tFecha_agregado' => now()
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Producto agregado a tu lista de deseos',
                    'action' => 'added',
                    'producto_id' => $idProducto
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Error en toggle favorito: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al gestionar favoritos. Por favor intenta nuevamente.'
            ], 500);
        }
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
            DB::beginTransaction();

            $favorito = Favorito::where('id_usuario', Auth::id())
                ->where('id_producto', $idProducto)
                ->first();

            if (!$favorito) {
                return response()->json([
                    'success' => false,
                    'message' => 'El producto no está en tu lista de deseos'
                ], 404);
            }

            $favorito->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado de tu lista de deseos',
                'action' => 'removed',
                'producto_id' => $idProducto
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Error eliminando favorito: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el producto de favoritos'
            ], 500);
        }
    }
}