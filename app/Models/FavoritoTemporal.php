<?php
// app/Models/FavoritoTemporal.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class FavoritoTemporal extends Model
{
    protected $table = 'tbl_favoritos_temporales';
    protected $primaryKey = 'id_favorito_temporal';
    
    public $timestamps = false;

    protected $fillable = [
        'session_id',
        'id_producto',
        'id_variacion',
        'tFecha_agregado'
    ];

    protected $casts = [
        'tFecha_agregado' => 'datetime'
    ];

    /**
     * Obtener favoritos de la sesión actual
     */
    public static function getFavoritosDeSesion()
    {
        $sessionId = Session::getId();
        
        return self::where('session_id', $sessionId)
            ->with(['producto', 'variacion'])
            ->orderBy('tFecha_agregado', 'desc')
            ->get();
    }

    /**
     * Agregar producto a favoritos temporales
     */
    public static function agregarProducto($idProducto, $idVariacion = null)
    {
        try {
            $sessionId = Session::getId();
            
            // Verificar si ya existe
            $existe = self::where('session_id', $sessionId)
                ->where('id_producto', $idProducto)
                ->where('id_variacion', $idVariacion)
                ->exists();
                
            if ($existe) {
                return false;
            }
            
            return self::create([
                'session_id' => $sessionId,
                'id_producto' => $idProducto,
                'id_variacion' => $idVariacion,
                'tFecha_agregado' => now()
            ]);
        } catch (\Exception $e) {
            Log::error('Error en FavoritoTemporal::agregarProducto: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Eliminar producto de favoritos temporales
     */
    public static function eliminarProducto($idProducto, $idVariacion = null)
    {
        try {
            $sessionId = Session::getId();
            
            return self::where('session_id', $sessionId)
                ->where('id_producto', $idProducto)
                ->where('id_variacion', $idVariacion)
                ->delete();
        } catch (\Exception $e) {
            Log::error('Error en FavoritoTemporal::eliminarProducto: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verificar si un producto está en favoritos temporales
     */
    public static function esFavorito($idProducto, $idVariacion = null)
    {
        try {
            $sessionId = Session::getId();
            
            return self::where('session_id', $sessionId)
                ->where('id_producto', $idProducto)
                ->where('id_variacion', $idVariacion)
                ->exists();
        } catch (\Exception $e) {
            Log::error('Error en FavoritoTemporal::esFavorito: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Migrar favoritos temporales a usuario registrado
     */
    public static function migrarAUsuario($userId)
    {
        try {
            $sessionId = Session::getId();
            $favoritosTemporales = self::where('session_id', $sessionId)->get();
            
            $migrados = 0;
            foreach ($favoritosTemporales as $temp) {
                try {
                    // Verificar si ya existe en favoritos permanentes
                    $existe = Favorito::where('id_usuario', $userId)
                        ->where('id_producto', $temp->id_producto)
                        ->where('id_variacion', $temp->id_variacion)
                        ->exists();
                        
                    if (!$existe) {
                        Favorito::create([
                            'id_usuario' => $userId,
                            'id_producto' => $temp->id_producto,
                            'id_variacion' => $temp->id_variacion,
                            'tFecha_agregado' => $temp->tFecha_agregado
                        ]);
                        $migrados++;
                    }
                    
                    $temp->delete();
                } catch (\Exception $e) {
                    Log::error('Error migrando favorito temporal individual: ' . $e->getMessage());
                }
            }
            
            return $migrados;
        } catch (\Exception $e) {
            Log::error('Error en FavoritoTemporal::migrarAUsuario: ' . $e->getMessage());
            throw $e;
        }
    }

    // Relaciones
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }

    public function variacion()
    {
        return $this->belongsTo(ProductoVariacion::class, 'id_variacion', 'id_variacion');
    }
}