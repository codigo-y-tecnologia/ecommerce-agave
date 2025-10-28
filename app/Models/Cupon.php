<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cupon extends Model
{
      use HasFactory;

    protected $table = 'tbl_cupones';
    protected $primaryKey = 'id_cupon';
    public $timestamps = false;

    protected $fillable = [
        'vCodigo_cupon',
        'dDescuento',
        'eTipo',
        'dValido_desde',
        'dValido_hasta',
        'iUso_maximo',
        'bActivo',
    ];

    public function usos()
    {
        return $this->hasMany(CuponUso::class, 'id_cupon', 'id_cupon');
    }
}
