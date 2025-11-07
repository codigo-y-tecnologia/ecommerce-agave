<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table = 'tbl_ventas';
    protected $primaryKey = 'id_venta';
    
    public $timestamps = false;
    
    protected $fillable = [
        'id_pedido',
        'id_usuario',
        'tFecha_venta',
        'dTotal',
        'eMetodo_pago',
        'eEstado'
    ];
    
    protected $casts = [
        'tFecha_venta' => 'datetime',
        'dTotal' => 'decimal:2'
    ];
}