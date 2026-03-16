<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cupones extends Model  
{
    use HasFactory;

    protected $table = 'tbl_cupones';   
    protected $primaryKey = 'id_cupon'; 

    public $timestamps = false; 

    protected $fillable = [
        'vCodigo_cupon',
        'dDescuento',
        'dMonto_minimo',
        'eTipo',
        'dValido_desde',
        'dValido_hasta',
        'iUso_maximo',
        'iUsos_por_usuario',
        'bActivo',
    ];
}