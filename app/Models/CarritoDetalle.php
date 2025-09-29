<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarritoDetalle extends Model
{
    protected $table = 'tbl_carrito_detalles';
    protected $primaryKey = 'id_detalle_carrito';
    public $timestamps = false;

    protected $fillable = ['id_carrito', 'id_producto', 'iCantidad', 'dPrecio_unitario', 'dSubtotal'];

    // Relación con producto
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }
}
