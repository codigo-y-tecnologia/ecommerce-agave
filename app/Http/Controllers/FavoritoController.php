<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorito;
use App\Models\Producto;
use App\Models\ProductoVariacion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FavoritoController extends Controller
{
    /**
     * Mostrar lista de favoritos del usuario
     */
    public function index()
    {
        if (!Auth::check()) {
            return view('favoritos.no-autenticado');
        }

        try {
            // Cargar favoritos con relaciones específicas según el tipo
            $favoritos = Favorito::with([
                    'producto' => function($query) {
                        $query->with(['categoria', 'marca', 'etiquetas']);
                    },
                    'variacion' => function($query) {
                        $query->with(['atributos.valor', 'atributos.atributo', 'producto']);
                    }
                ])
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

    /**
     * Toggle favorito para PRODUCTOS (sin variación)
     */
    public function toggleProducto($idProducto)
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

            // Buscar favorito existente para este producto (SIN variación)
            $favorito = Favorito::where('id_usuario', Auth::id())
                ->where('id_producto', $idProducto)
                ->whereNull('id_variacion')
                ->first();

            if ($favorito) {
                // Eliminar favorito de producto
                $favorito->delete();
                
                DB::commit();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Producto eliminado de tu lista de deseos',
                    'action' => 'removed',
                    'tipo' => 'producto',
                    'producto_id' => $idProducto,
                    'variacion_id' => null
                ]);
            } else {
                // Crear favorito para producto
                Favorito::create([
                    'id_usuario' => Auth::id(),
                    'id_producto' => $idProducto,
                    'id_variacion' => null,
                    'bNotificado_stock' => false,
                    'bNotificado_descuento' => false,
                    'tFecha_agregado' => now()
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Producto agregado a tu lista de deseos',
                    'action' => 'added',
                    'tipo' => 'producto',
                    'producto_id' => $idProducto,
                    'variacion_id' => null
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Error en toggle producto favorito: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al gestionar favoritos. Por favor intenta nuevamente.'
            ], 500);
        }
    }

    /**
     * Toggle favorito para VARIACIONES
     */
    public function toggleVariacion($idVariacion)
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

            // Buscar favorito existente para esta variación (CON id_variacion)
            $favorito = Favorito::where('id_usuario', Auth::id())
                ->where('id_variacion', $idVariacion)
                ->first();

            if ($favorito) {
                // Eliminar favorito de variación
                $favorito->delete();
                
                DB::commit();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Variación eliminada de tu lista de deseos',
                    'action' => 'removed',
                    'tipo' => 'variacion',
                    'producto_id' => $variacion->id_producto,
                    'variacion_id' => $idVariacion
                ]);
            } else {
                // Crear favorito para variación
                Favorito::create([
                    'id_usuario' => Auth::id(),
                    'id_producto' => $variacion->id_producto,
                    'id_variacion' => $idVariacion,
                    'bNotificado_stock' => false,
                    'bNotificado_descuento' => false,
                    'tFecha_agregado' => now()
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Variación agregada a tu lista de deseos',
                    'action' => 'added',
                    'tipo' => 'variacion',
                    'producto_id' => $variacion->id_producto,
                    'variacion_id' => $idVariacion
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Error en toggle variación favorito: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al gestionar favoritos. Por favor intenta nuevamente.'
            ], 500);
        }
    }

    /**
     * Eliminar favorito (puede ser producto o variación)
     */
    public function destroy(Request $request, $id)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Debes iniciar sesión para gestionar favoritos'
            ], 401);
        }

        try {
            DB::beginTransaction();

            $idVariacion = $request->input('id_variacion');

            if ($idVariacion) {
                // Eliminar favorito de variación específica
                $favorito = Favorito::where('id_usuario', Auth::id())
                    ->where('id_variacion', $idVariacion)
                    ->first();

                if (!$favorito) {
                    return response()->json([
                        'success' => false,
                        'message' => 'La variación no está en tu lista de deseos'
                    ], 404);
                }

                $productoId = $favorito->id_producto;
                $favorito->delete();

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Variación eliminada de tu lista de deseos',
                    'action' => 'removed',
                    'tipo' => 'variacion',
                    'producto_id' => $productoId,
                    'variacion_id' => $idVariacion
                ]);
            } else {
                // Eliminar favorito de producto (sin variación)
                $favorito = Favorito::where('id_usuario', Auth::id())
                    ->where('id_producto', $id)
                    ->whereNull('id_variacion')
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
                    'tipo' => 'producto',
                    'producto_id' => $id,
                    'variacion_id' => null
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Error eliminando favorito: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el producto de favoritos'
            ], 500);
        }
    }

    /**
     * Verificar si un producto está en favoritos
     */
    public function checkProducto($idProducto)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'is_favorite' => false
            ]);
        }

        $isFavorite = Favorito::where('id_usuario', Auth::id())
            ->where('id_producto', $idProducto)
            ->whereNull('id_variacion')
            ->exists();

        return response()->json([
            'success' => true,
            'is_favorite' => $isFavorite
        ]);
    }

    /**
     * Verificar si una variación está en favoritos
     */
    public function checkVariacion($idVariacion)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'is_favorite' => false
            ]);
        }

        $isFavorite = Favorito::where('id_usuario', Auth::id())
            ->where('id_variacion', $idVariacion)
            ->exists();

        return response()->json([
            'success' => true,
            'is_favorite' => $isFavorite
        ]);
    }
}