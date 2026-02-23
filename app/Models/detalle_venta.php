<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class detalle_venta extends Model
{
    use HasFactory;

    protected $table = 'tbl_detalle_ventas';  // ✅ CON "S" al final
    protected $primaryKey = 'id_detalle_venta';
    public $timestamps = true;
    
    protected $fillable = [
        'id_venta',
        'id_producto',
        'iCantidad',
        'dPrecio_unitario',
        'dSubtotal',
    ];
    
    protected $casts = [
        'iCantidad' => 'integer',
        'dPrecio_unitario' => 'decimal:2',
        'dSubtotal' => 'decimal:2',
    ];

    /**
     * Relación con la venta
     */
    public function venta()
    {
        return $this->belongsTo(Venta::class, 'id_venta', 'id_venta');
    }

    /**
     * Relación con el producto
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }
}































