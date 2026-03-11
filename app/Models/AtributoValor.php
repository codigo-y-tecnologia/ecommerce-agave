<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AtributoValor extends Model
{
    use HasFactory;

    protected $table = 'tbl_atributo_valores';
    protected $primaryKey = 'id_atributo_valor';
    
    // DESACTIVA LOS TIMESTAMPS AUTOMÁTICOS
    public $timestamps = false;
    
    protected $fillable = [
        'id_atributo',
        'vValor',
        'vSlug',
        'bActivo'
    ];

    protected $casts = [
        'bActivo' => 'boolean'
    ];

    public function atributo()
    {
        return $this->belongsTo(Atributo::class, 'id_atributo');
    }

    /**
     * Relación con productos a través de la tabla pivote
     */
    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'tbl_producto_atributos', 'id_atributo_valor', 'id_producto')
                    ->withPivot('dPrecio_extra');
    }

    /**
     * Scope para filtrar por atributo
     */
    public function scopePorAtributo($query, $atributoId)
    {
        return $query->where('id_atributo', $atributoId);
    }
}