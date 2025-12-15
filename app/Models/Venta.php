<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Venta extends Model
{

    use HasFactory;

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

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'id_venta', 'id_venta');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
}
