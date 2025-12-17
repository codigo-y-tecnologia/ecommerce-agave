<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pago extends Model
{
    use HasFactory;

    protected $table = 'tbl_pagos';
    protected $primaryKey = 'id_pago';
    public $timestamps = false;

    protected $fillable = [
        'id_pedido',
        'eMetodo_pago',
        'dMonto',
        'eEstado',
        'vReferencia',
        'vSessionID',
        'tFecha_pago',
    ];

    protected $casts = [
    'tFecha_pago' => 'datetime',
];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido', 'id_pedido');
    }
}
