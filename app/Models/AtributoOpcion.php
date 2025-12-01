<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AtributoOpcion extends Model
{
    use HasFactory;

    protected $table = 'tbl_atributo_opciones';
    protected $primaryKey = 'id_opcion';

    public $timestamps = false;

    protected $fillable = [
        'id_atributo',
        'vValor',
        'vEtiqueta',
        'bPredeterminado',
        'iOrden'
    ];

    protected $casts = [
        'bPredeterminado' => 'boolean'
    ];

    public function atributo()
    {
        return $this->belongsTo(Atributo::class, 'id_atributo');
    }

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'tbl_producto_atributos', 'id_opcion', 'id_producto');
    }
}