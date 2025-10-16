<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'tbl_pedidos';
    protected $primaryKey = 'id_pedido';
    public $timestamps = false; 

    protected $fillable = [
        'id_usuario',
        'id_direccion',
        'eEstado',
        'dTotal',
        'tFecha_pedido ',
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
}
