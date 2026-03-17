<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;

    protected $table = 'tbl_marcas';
    protected $primaryKey = 'id_marca';
    
    protected $fillable = [
        'vNombre',
        'tDescripcion'
    ];

    public $timestamps = false; 

    public function productos()
    {
        return $this->hasMany(Producto::class, 'id_marca');
    }

    /**
     * ============================================
     * NUEVOS MÉTODOS PARA PANEL DE GESTIÓN
     * ============================================
     */

    /**
     * Obtener marcas activas
     */
    public static function getActivas()
    {
        return self::orderBy('vNombre')->get();
    }

    /**
     * Buscar marcas por término
     */
    public static function buscar($termino)
    {
        return self::where('vNombre', 'LIKE', "%{$termino}%")
            ->orWhere('tDescripcion', 'LIKE', "%{$termino}%")
            ->orderBy('vNombre')
            ->get();
    }

    /**
     * Crear marca rápidamente
     */
    public static function crearRapida($nombre, $descripcion = null)
    {
        try {
            return self::create([
                'vNombre' => $nombre,
                'tDescripcion' => $descripcion
            ]);
        } catch (\Exception $e) {
            throw new \Exception('Error al crear marca: ' . $e->getMessage());
        }
    }
}