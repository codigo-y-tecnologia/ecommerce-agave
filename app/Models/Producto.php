<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'tbl_productos';     
    protected $primaryKey = 'id_producto';  
    public $timestamps = false;             
    protected $fillable = [
        'vCodigo_barras','vNombre','tDescripcion_corta','tDescripcion_larga',
        'dPrecio_compra','dPrecio_venta','iStock',
        'id_marca','id_categoria','bActivo'
    ];

    //Relación con CarritoDetalle: Un producto puede estar en muchos carritos
    public function detalles()
    {
        return $this->hasMany(CarritoDetalle::class, 'id_producto', 'id_producto');
    }

}
