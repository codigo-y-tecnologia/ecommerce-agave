<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Impuestos extends Model
{
    use HasFactory;

    protected $table = 'tbl_impuestos';     
    protected $primaryKey = 'id_impuesto';  

    public $timestamps = false;           

    protected $fillable = [
        'vNombre',
        'eTipo',        
        'dPorcentaje',
        'bActivo',
        'dFecha_creacion',
    ];

    protected $casts = [
        'dPorcentaje'     => 'decimal:2',
        'bActivo'         => 'boolean',
        'dFecha_creacion' => 'datetime',
    ];
}
