<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reembolsos extends Model
{
    use HasFactory;

    protected $table = 'tbl_reembolsos';     
    protected $primaryKey = 'id_reembolso';  // Cambié a mayúscula

    public $timestamps = false;           

    protected $fillable = [
        'Id_venta',
        'tFecha_reembolso',
        'dMonto',
        'vMotivo',
        'eMetodo_pago',
        'eEstado', // Cambié de Estado a eEstado
    ];

    protected $casts = [
        'dMonto' => 'decimal:2',
        'tFecha_reembolso' => 'datetime:Y-m-d H:i:s',
    ];
}
