<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Envio extends Model
{
    use HasFactory;
    
    protected $table = 'tbl_envios';
    protected $primaryKey = 'id_envio';

    protected $fillable = [
        'id_pedido',
        'vTransportista',
        'vNumero_guia',
        'eEstado',
    ];

    protected $casts = [
    'eEstado' => 'string',
];

    /*
    |--------------------------------------------------------------------------
    | ESTADOS DE ENVÍO
    |--------------------------------------------------------------------------
    */
    const ESTADO_PENDIENTE = 'pendiente';
    const ESTADO_ENVIADO   = 'enviado';
    const ESTADO_ENTREGADO = 'entregado';
    const ESTADO_DEVUELTO  = 'devuelto';

    // Relación con Pedido
    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido', 'id_pedido');
    }
}
