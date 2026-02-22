<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atributo extends Model
{
    use HasFactory;

    protected $table = 'tbl_atributos';
    protected $primaryKey = 'id_atributo';
    
    // DESACTIVA LOS TIMESTAMPS AUTOMÁTICOS
    public $timestamps = false;
    
    protected $fillable = [
        'vNombre',
        'vSlug',
        'tDescripcion',
        'iOrden',
        'bActivo'
    ];

    protected $casts = [
        'bActivo' => 'boolean'
    ];

    public function valores()
    {
        return $this->hasMany(AtributoValor::class, 'id_atributo');
    }

    public function valoresActivos()
    {
        return $this->hasMany(AtributoValor::class, 'id_atributo')
                    ->where('bActivo', true)
                    ->orderBy('iOrden');
    }

    /**
     * Relación con productos a través de la tabla pivote
     */
    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'tbl_producto_atributos', 'id_atributo', 'id_producto')
                    ->withPivot('id_atributo_valor', 'dPrecio_extra');
    }
}