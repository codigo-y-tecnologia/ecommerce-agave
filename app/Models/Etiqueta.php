<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Etiqueta extends Model
{
    use HasFactory;

    protected $table = 'tbl_etiquetas';
    protected $primaryKey = 'id_etiqueta';
    
    protected $fillable = [
        'vNombre',
        'tDescripcion',
        'color'
    ];

    public $timestamps = false; 

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'tbl_producto_etiquetas', 'id_etiqueta', 'id_producto');
    }

    /**
     * ============================================
     * NUEVOS MÉTODOS PARA PANEL DE GESTIÓN
     * ============================================
     */

    /**
     * Obtener etiquetas activas
     */
    public static function getActivas()
    {
        return self::orderBy('vNombre')->get();
    }

    /**
     * Buscar etiquetas por término
     */
    public static function buscar($termino)
    {
        return self::where('vNombre', 'LIKE', "%{$termino}%")
            ->orWhere('tDescripcion', 'LIKE', "%{$termino}%")
            ->orderBy('vNombre')
            ->get();
    }

    /**
     * Crear etiqueta rápidamente
     */
    public static function crearRapida($nombre, $color = '#007bff', $descripcion = null)
    {
        try {
            return self::create([
                'vNombre' => $nombre,
                'color' => $color,
                'tDescripcion' => $descripcion
            ]);
        } catch (\Exception $e) {
            throw new \Exception('Error al crear etiqueta: ' . $e->getMessage());
        }
    }

    /**
     * Accesor para color por defecto
     */
    public function getColorAttribute($value)
    {
        return $value ?: '#007bff';
    }
}