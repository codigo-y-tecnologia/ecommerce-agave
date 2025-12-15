<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarritoDetalle extends Model
{
    protected $table = 'tbl_carrito_detalles';
    protected $primaryKey = 'id_detalle_carrito';
    public $timestamps = false;

    protected $fillable = ['id_carrito', 'id_producto', 'iCantidad', 'dPrecio_unitario'];


    /**
     * Relación con el carrito al que pertenece el detalle.
     * Un detalle pertenece a UN carrito.
     */
    public function carrito()
    {
        return $this->belongsTo(Carrito::class, 'id_carrito', 'id_carrito');
    }

    // Relación con producto
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }

    // Accessors para simplificar en vistas y controladores
    public function getCantidadAttribute()
    {
        return $this->iCantidad;
    }

    public function getPrecioUnitarioAttribute()
    {
        return $this->dPrecio_unitario;
    }
}
