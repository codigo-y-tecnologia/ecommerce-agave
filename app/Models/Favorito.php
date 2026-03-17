<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorito extends Model
{
    protected $table = 'tbl_favoritos';
    protected $primaryKey = 'id_favorito';
    
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'id_producto',
        'id_variacion',
        'bNotificado_stock',
        'bNotificado_descuento',
        'tFecha_agregado'
    ];

    protected $casts = [
        'bNotificado_stock' => 'boolean',
        'bNotificado_descuento' => 'boolean',
        'tFecha_agregado' => 'datetime'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }

    public function variacion()
    {
        return $this->belongsTo(ProductoVariacion::class, 'id_variacion', 'id_variacion');
    }
}