<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorito;
use App\Models\Producto;
use App\Models\ProductoVariacion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FavoritoController extends Controller
{
    /**
     * Mostrar favoritos del usuario autenticado
     */
    public function index()
    {
        try {
            $usuarioId = Auth::id();
            
            $favoritos = Favorito::with(['producto', 'variacion'])
                ->where('id_usuario', $usuarioId)
                ->orderBy('tFecha_agregado', 'desc')
                ->get();
            
            return view('favoritos.index', compact('favoritos'));
            
        } catch (\Exception $e) {
            Log::error('Error cargando favoritos: ' . $e->getMessage());
            return view('favoritos.index', ['favoritos' => collect([])])
                ->with('error', 'Error al cargar tus favoritos');
        }
    }

    /**
     * Toggle favorito para producto (sin variación)
     */
    public function toggleProducto($idProducto)
    {
        try {
            DB::beginTransaction();
            
            $usuarioId = Auth::id();
            
            // Verificar que el producto existe
            $producto = Producto::where('id_producto', $idProducto)
                ->where('bActivo', true)
                ->first();

            if (!$producto) {
                return response()->json([
                    'success' => false,
                    'message' => 'Producto no encontrado'
                ], 404);
            }

            // Verificar si ya está en favoritos
            $favorito = Favorito::where('id_usuario', $usuarioId)
                ->where('id_producto', $idProducto)
                ->whereNull('id_variacion')
                ->first();

            if ($favorito) {
                // Eliminar
                $favorito->delete();
                DB::commit();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Producto eliminado de favoritos',
                    'action' => 'removed',
                    'tipo' => 'producto'
                ]);
            } else {
                // Agregar
                $nuevo = Favorito::create([
                    'id_usuario' => $usuarioId,
                    'id_producto' => $idProducto,
                    'id_variacion' => null,
                    'tFecha_agregado' => now()
                ]);
                DB::commit();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Producto agregado a favoritos',
                    'action' => 'added',
                    'tipo' => 'producto'
                ]);
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en toggleProducto: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al gestionar favoritos'
            ], 500);
        }
    }

    /**
     * Toggle favorito para variación
     */
    public function toggleVariacion($idVariacion)
    {
        try {
            DB::beginTransaction();
            
            $usuarioId = Auth::id();

            // Verificar que la variación existe
            $variacion = ProductoVariacion::where('id_variacion', $idVariacion)
                ->with('producto')
                ->whereHas('producto', function($query) {
                    $query->where('bActivo', true);
                })
                ->first();

            if (!$variacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Variación no encontrada'
                ], 404);
            }

            // Verificar si ya está en favoritos
            $favorito = Favorito::where('id_usuario', $usuarioId)
                ->where('id_producto', $variacion->id_producto)
                ->where('id_variacion', $idVariacion)
                ->first();

            if ($favorito) {
                // Eliminar
                $favorito->delete();
                DB::commit();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Variación eliminada de favoritos',
                    'action' => 'removed',
                    'tipo' => 'variacion'
                ]);
            } else {
                // Agregar
                $nuevo = Favorito::create([
                    'id_usuario' => $usuarioId,
                    'id_producto' => $variacion->id_producto,
                    'id_variacion' => $idVariacion,
                    'tFecha_agregado' => now()
                ]);
                DB::commit();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Variación agregada a favoritos',
                    'action' => 'added',
                    'tipo' => 'variacion'
                ]);
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en toggleVariacion: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al gestionar favoritos'
            ], 500);
        }
    }

    /**
     * Eliminar favorito
     */
    public function destroy($id)
    {
        try {
            $usuarioId = Auth::id();
            
            $favorito = Favorito::where('id_favorito', $id)
                ->where('id_usuario', $usuarioId)
                ->first();

            if (!$favorito) {
                return response()->json([
                    'success' => false,
                    'message' => 'Favorito no encontrado'
                ], 404);
            }

            $tipo = $favorito->id_variacion ? 'variacion' : 'producto';
            $favorito->delete();

            return response()->json([
                'success' => true,
                'message' => ucfirst($tipo) . ' eliminado de favoritos',
                'action' => 'removed',
                'tipo' => $tipo
            ]);

        } catch (\Exception $e) {
            Log::error('Error en destroy: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar favorito'
            ], 500);
        }
    }

    /**
     * Verificar si un producto es favorito
     */
    public function checkProducto($idProducto)
    {
        try {
            $usuarioId = Auth::id();
            
            $esFavorito = Favorito::where('id_usuario', $usuarioId)
                ->where('id_producto', $idProducto)
                ->whereNull('id_variacion')
                ->exists();
            
            return response()->json([
                'success' => true,
                'is_favorite' => $esFavorito
            ]);

        } catch (\Exception $e) {
            Log::error('Error en checkProducto: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'is_favorite' => false
            ], 500);
        }
    }

    /**
     * Verificar si una variación es favorita
     */
    public function checkVariacion($idVariacion)
    {
        try {
            $usuarioId = Auth::id();
            
            $variacion = ProductoVariacion::find($idVariacion);
            
            if (!$variacion) {
                return response()->json([
                    'success' => false,
                    'is_favorite' => false
                ], 404);
            }
            
            $esFavorito = Favorito::where('id_usuario', $usuarioId)
                ->where('id_producto', $variacion->id_producto)
                ->where('id_variacion', $idVariacion)
                ->exists();
            
            return response()->json([
                'success' => true,
                'is_favorite' => $esFavorito
            ]);

        } catch (\Exception $e) {
            Log::error('Error en checkVariacion: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'is_favorite' => false
            ], 500);
        }
    }
}