<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class cupones_usados extends Model
{
    // Nombre real de la tabla
    protected $table = 'tbl_cupon_usos';

    // Primary key personalizada
    protected $primaryKey = 'id_cupon_usado';

    // Si no usas timestamps created_at / updated_at, ponlo en false
    public $timestamps = false;

    protected $fillable = [
        'id_cupon',
        'id_usuario',
        'id_venta',
        'dDescuento_aplicado',
        'tFecha_uso',
    ];

    // Relación con el cupón
    public function cupon()
    {
        return $this->belongsTo(Cupones::class, 'id_cupon', 'id_cupon');
    }

    // Relación con el usuario
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    // Relación con la venta
    public function venta()
    {
        return $this->belongsTo(Venta::class, 'id_venta', 'id_venta');
    }
}