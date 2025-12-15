<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoDetalle extends Model
{
    protected $table = 'tbl_pedido_detalles';
    protected $primaryKey = 'id_item';
    public $timestamps = false; 

    protected $fillable = [
        'id_pedido',
        'id_producto',
        'iCantidad',
        'dPrecio_unitario',
    ];

    // Generado automáticamente por MySQL: dSubtotal (campo generado STORED)

    // Relación con Producto
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }

    // Relación con Pedido
    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido', 'id_pedido');
    }
}
