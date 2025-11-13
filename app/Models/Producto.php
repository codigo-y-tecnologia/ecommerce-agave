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

    public function impuesto()
{
    return $this->belongsToMany(
        Impuesto::class,
        'tbl_producto_impuestos',
        'id_producto',
        'id_impuesto'
    )->where('bActivo', 1);
}

/**
 * Calcula el precio de venta con impuestos incluidos.
 */
public function getPrecioConImpuestosAttribute()
{
    $porcentajeTotal = $this->impuestos->sum('dPorcentaje');
    return round($this->dPrecio_venta * (1 + ($porcentajeTotal / 100)), 2);
}


/**
 * Calcula el monto total de impuestos (IVA + IEPS + otros) aplicados al producto.
 */
public function getMontoImpuestosAttribute()
{
    $porcentajeTotal = $this->impuestos->sum('dPorcentaje');
    return round($this->dPrecio_venta * ($porcentajeTotal / 100), 2);
}


    //Relación con CarritoDetalle: Un producto puede estar en muchos carritos
    public function detalles()
    {
        return $this->hasMany(CarritoDetalle::class, 'id_producto', 'id_producto');
    }

    public function impuestos()
{
    return $this->belongsToMany(Impuesto::class, 'tbl_producto_impuestos', 'id_producto', 'id_impuesto');
}


}
