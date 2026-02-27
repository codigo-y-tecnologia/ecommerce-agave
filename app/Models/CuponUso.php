<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CuponUso extends Model
{
    use HasFactory;

    protected $table = 'tbl_cupon_usos';
    protected $primaryKey = null;
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'id_cupon',
        'id_venta',
        'id_usuario',
        'guest_token',
        'tFecha_uso',
    ];

    protected $casts = [
        'id_cupon'   => 'integer',
        'id_venta'   => 'integer',
        'id_usuario' => 'integer',
        'tFecha_uso' => 'datetime',
    ];

    public function cupon()
    {
        return $this->belongsTo(Cupon::class, 'id_cupon', 'id_cupon');
    }
}
