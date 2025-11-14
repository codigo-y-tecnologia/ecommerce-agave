<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductoAtributo extends Pivot
{
    use HasFactory;

    protected $table = 'tbl_producto_atributos';
    
    // Indicar que no hay clave primaria autoincremental
    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = [
        'id_producto',
        'id_atributo',
        'vValor',
        'id_opcion'
    ];

    public $timestamps = false;

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    public function atributo()
    {
        return $this->belongsTo(Atributo::class, 'id_atributo');
    }

    public function opcion()
    {
        return $this->belongsTo(AtributoOpcion::class, 'id_opcion');
    }
}