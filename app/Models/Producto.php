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

    public function impuestos()
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
    $precio_base = $this->dPrecio_venta;

    $ieps = 0;
    $iva = 0;

    $impuestos = $this->impuestos->where('bActivo', 1);

    // IEPS primero
    foreach ($impuestos as $imp) {
        if ($imp->eTipo === 'IEPS') {
            $ieps = $precio_base * ($imp->dPorcentaje / 100);
        }
    }

    // IVA después (sobre precio base + IEPS)
    foreach ($impuestos as $imp) {
        if ($imp->eTipo === 'IVA') {
            $iva = ($precio_base + $ieps) * ($imp->dPorcentaje / 100);
        }
    }

    return $precio_base + $ieps + $iva;
}

    //Relación con CarritoDetalle: Un producto puede estar en muchos carritos
    public function detalles()
    {
        return $this->hasMany(CarritoDetalle::class, 'id_producto', 'id_producto');
    }

}
