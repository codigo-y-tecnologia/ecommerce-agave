<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoAtributo extends Model
{
    use HasFactory;

    protected $table = 'tbl_producto_atributos';
    protected $primaryKey = 'id_producto_atributo';

    protected $fillable = [
        'id_producto',
        'id_atributo',
        'id_atributo_valor',
        'dPrecio_extra'
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    public function atributo()
    {
        return $this->belongsTo(Atributo::class, 'id_atributo');
    }

    public function valor()
    {
        return $this->belongsTo(AtributoValor::class, 'id_atributo_valor');
    }
}