<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pedido extends Model
{

    use HasFactory;

    protected $table = 'tbl_pedidos';
    protected $primaryKey = 'id_pedido';
    public $timestamps = false; 

    protected $fillable = [
        'id_usuario',
        'id_direccion',
        'id_direccion_facturacion',
        'eEstado',
        'dTotal',
        'tNota',
        'tFecha_pedido',
    ];

    protected $casts = [
    'tFecha_pedido' => 'datetime',
];

    // Relación con Usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    // Relación con DetallePedido
    public function detalles()
    {
        return $this->hasMany(PedidoDetalle::class, 'id_pedido', 'id_pedido');
    }

    public function venta()
{
    return $this->hasOne(Venta::class, 'id_pedido', 'id_pedido');
}

// Relación con Dirección
    public function direccion()
    {
        return $this->belongsTo(Direccion::class, 'id_direccion', 'id_direccion');
    }

// Relación con Dirección de Facturación
    public function direccionFacturacion()
    {
        return $this->belongsTo(Direccion::class, 'id_direccion_facturacion', 'id_direccion');
    }

    // Relación con Pago
    public function pago()
    {
        return $this->hasOne(Pago::class, 'id_pedido', 'id_pedido');
    }
}
