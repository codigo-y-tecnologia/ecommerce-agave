<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Impuesto extends Model
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

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'tbl_producto_impuestos', 'id_impuesto', 'id_producto');
    }
}
