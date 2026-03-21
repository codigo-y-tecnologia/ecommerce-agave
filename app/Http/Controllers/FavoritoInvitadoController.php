<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use App\Models\Producto;
use App\Models\ProductoVariacion;
use App\Models\UsuarioTemporal;
use Illuminate\Support\Facades\Auth;

class FavoritoInvitadoController extends Controller
{
    /**
     * Obtener o crear usuario temporal para la sesión actual
     */
    private function obtenerUsuarioTemporal()
    {
        try {
            $sessionId = Session::getId();

            // Verificar si la tabla existe
            $tablaExiste = DB::select("SHOW TABLES LIKE 'tbl_usuarios_temporales'");
            if (empty($tablaExiste)) {
                // Crear la tabla si no existe
                DB::statement("
                    CREATE TABLE IF NOT EXISTS `tbl_usuarios_temporales` (
                        `id_temp_usuario` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
                        `session_id` varchar(255) NOT NULL,
                        `vToken` varchar(100) DEFAULT NULL,
                        `tFecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                        `tFecha_expiracion` timestamp NULL DEFAULT NULL,
                        PRIMARY KEY (`id_temp_usuario`),
                        UNIQUE KEY `vToken` (`vToken`),
                        KEY `idx_session_id` (`session_id`(250)),
                        KEY `idx_expiracion` (`tFecha_expiracion`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
                ");
            }

            $usuarioTemporal = UsuarioTemporal::where('session_id', $sessionId)->first();

            if (!$usuarioTemporal) {
                $usuarioTemporal = UsuarioTemporal::create([
                    'session_id' => $sessionId,
                    'vToken' => UsuarioTemporal::generarToken(),
                    'tFecha_expiracion' => now()->addDays(30)
                ]);
            }

            return $usuarioTemporal;
        } catch (\Exception $e) {
            Log::error('Error en obtenerUsuarioTemporal: ' . $e->getMessage());

            // Si falla, devolver un objeto con session_id
            return (object)[
                'session_id' => Session::getId()
            ];
        }
    }

    /**
     * Mostrar favoritos de invitado
     */
    public function index()
    {
        try {
            $usuarioTemporal = $this->obtenerUsuarioTemporal();

            $favoritos = DB::table('tbl_favoritos_temporales')
                ->where('session_id', $usuarioTemporal->session_id)
                ->orderBy('tFecha_agregado', 'desc')
                ->get();

            // Cargar datos de productos y variaciones
            foreach ($favoritos as $favorito) {
                if ($favorito->id_variacion) {
                    $variacion = ProductoVariacion::with(['productoPadre'])->find($favorito->id_variacion);
                    $favorito->variacion = $variacion;
                } else {
                    $producto = Producto::find($favorito->id_producto);
                    $favorito->producto = $producto;
                }
            }

            return view('favoritos.index-invitado', compact('favoritos'));
        } catch (\Exception $e) {
            Log::error('Error en index invitado: ' . $e->getMessage());
            return view('favoritos.index-invitado', ['favoritos' => collect([])])
                ->with('error', 'Error al cargar tus favoritos temporales');
        }
    }

    /**
     * Toggle favorito para invitado (producto)
     */
    public function toggleProducto($idProducto)
    {
        try {
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

            $sessionId = Session::getId();

            // Verificar si ya existe en favoritos temporales
            $existe = DB::table('tbl_favoritos_temporales')
                ->where('session_id', $sessionId)
                ->where('id_producto', $idProducto)
                ->whereNull('id_variacion')
                ->exists();

            if ($existe) {
                // Eliminar
                DB::table('tbl_favoritos_temporales')
                    ->where('session_id', $sessionId)
                    ->where('id_producto', $idProducto)
                    ->whereNull('id_variacion')
                    ->delete();

                return response()->json([
                    'success' => true,
                    'action' => 'removed',
                    'tipo' => 'producto',
                    'message' => 'Producto eliminado de favoritos'
                ]);
            } else {
                // Agregar usando tFecha_agregado
                DB::table('tbl_favoritos_temporales')->insert([
                    'session_id' => $sessionId,
                    'id_producto' => $idProducto,
                    'id_variacion' => null,
                    'tFecha_agregado' => DB::raw('NOW()')
                ]);

                return response()->json([
                    'success' => true,
                    'action' => 'added',
                    'tipo' => 'producto',
                    'message' => 'Producto agregado a favoritos'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error en toggleProducto invitado: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle favorito para invitado (variación)
     */
    public function toggleVariacion($idVariacion)
    {
        try {
            // Verificar que la variación existe
            $variacion = ProductoVariacion::with('productoPadre')->where('id_variacion', $idVariacion)
                ->first();

            if (!$variacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Variación no encontrada'
                ], 404);
            }

            $sessionId = Session::getId();

            // Verificar si ya existe en favoritos temporales
            $existe = DB::table('tbl_favoritos_temporales')
                ->where('session_id', $sessionId)
                ->where('id_producto', $variacion->id_producto)
                ->where('id_variacion', $idVariacion)
                ->exists();

            if ($existe) {
                // Eliminar
                DB::table('tbl_favoritos_temporales')
                    ->where('session_id', $sessionId)
                    ->where('id_producto', $variacion->id_producto)
                    ->where('id_variacion', $idVariacion)
                    ->delete();

                return response()->json([
                    'success' => true,
                    'action' => 'removed',
                    'tipo' => 'variacion',
                    'message' => 'Variación eliminada de favoritos'
                ]);
            } else {
                // Agregar usando tFecha_agregado
                DB::table('tbl_favoritos_temporales')->insert([
                    'session_id' => $sessionId,
                    'id_producto' => $variacion->id_producto,
                    'id_variacion' => $idVariacion,
                    'tFecha_agregado' => DB::raw('NOW()')
                ]);

                return response()->json([
                    'success' => true,
                    'action' => 'added',
                    'tipo' => 'variacion',
                    'message' => 'Variación agregada a favoritos'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error en toggleVariacion invitado: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar favorito temporal
     */
    public function destroy(Request $request)
    {
        try {
            $idProducto = $request->input('id_producto');
            $idVariacion = $request->input('id_variacion');

            if (!$idProducto) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID de producto requerido'
                ], 400);
            }

            $sessionId = Session::getId();

            $query = DB::table('tbl_favoritos_temporales')
                ->where('session_id', $sessionId)
                ->where('id_producto', $idProducto);

            if ($idVariacion) {
                $query->where('id_variacion', $idVariacion);
            } else {
                $query->whereNull('id_variacion');
            }

            $eliminado = $query->delete();

            if ($eliminado > 0) {
                return response()->json([
                    'success' => true,
                    'message' => $idVariacion ? 'Variación eliminada' : 'Producto eliminado',
                    'action' => 'removed',
                    'tipo' => $idVariacion ? 'variacion' : 'producto'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'El producto no estaba en favoritos'
                ], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error en destroy invitado: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar de favoritos'
            ], 500);
        }
    }

    /**
     * Verificar estado de favorito para invitado
     */
    public function check($idProducto, $idVariacion = null)
    {
        try {
            $sessionId = Session::getId();

            $query = DB::table('tbl_favoritos_temporales')
                ->where('session_id', $sessionId)
                ->where('id_producto', $idProducto);

            if ($idVariacion) {
                $query->where('id_variacion', $idVariacion);
            } else {
                $query->whereNull('id_variacion');
            }

            $esFavorito = $query->exists();

            return response()->json([
                'success' => true,
                'is_favorite' => $esFavorito
            ]);
        } catch (\Exception $e) {
            Log::error('Error en check favorito: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'is_favorite' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Migrar favoritos temporales a usuario autenticado
     */
    public function migrarAUsuario()
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Debes iniciar sesión'
            ], 401);
        }

        try {
            $sessionId = Session::getId();
            $userId = Auth::id();

            $favoritosTemporales = DB::table('tbl_favoritos_temporales')
                ->where('session_id', $sessionId)
                ->get();

            $migrados = 0;

            foreach ($favoritosTemporales as $temp) {
                // Verificar si ya existe en favoritos permanentes
                $existe = DB::table('tbl_favoritos')
                    ->where('id_usuario', $userId)
                    ->where('id_producto', $temp->id_producto)
                    ->where('id_variacion', $temp->id_variacion)
                    ->exists();

                if (!$existe) {
                    DB::table('tbl_favoritos')->insert([
                        'id_usuario' => $userId,
                        'id_producto' => $temp->id_producto,
                        'id_variacion' => $temp->id_variacion,
                        'tFecha_agregado' => $temp->tFecha_agregado,
                        'bNotificado_stock' => 0,
                        'bNotificado_descuento' => 0
                    ]);
                    $migrados++;
                }
            }

            // Limpiar temporales
            DB::table('tbl_favoritos_temporales')
                ->where('session_id', $sessionId)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => "Se migraron {$migrados} favoritos a tu cuenta",
                'migrados' => $migrados
            ]);
        } catch (\Exception $e) {
            Log::error('Error migrando favoritos: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al migrar favoritos: ' . $e->getMessage()
            ], 500);
        }
    }
}
