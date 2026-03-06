<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class cupones_usados extends Model
{
    protected $table = 'tbl_cupon_usos';
    protected $primaryKey = 'id_cupon';
    public $timestamps = false;

    protected $fillable = [
        'id_venta',
        'id_usuario',
        'guest_token',
        'tFecha_uso',
    ];

    protected $casts = [
        'tFecha_uso' => 'datetime',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'id_venta', 'id_venta');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
}