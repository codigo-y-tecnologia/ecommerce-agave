<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoVariacion extends Model
{
    use HasFactory;

    protected $table = 'tbl_producto_variaciones';
    protected $primaryKey = 'id_variacion';

    // Desactivar timestamps
    public $timestamps = false;

    protected $fillable = [
        'id_producto',
        'vSKU',
        'dPrecio',
        'dPrecio_oferta',
        'iStock',
        'dPeso',
        'vClase_envio',
        'tDescripcion',
        'vImagen',
        'bActivo'
    ];

    protected $casts = [
        'bActivo' => 'boolean',
        'dPrecio' => 'decimal:2',
        'dPrecio_oferta' => 'decimal:2',
        'iStock' => 'integer',
        'dPeso' => 'decimal:2'
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    public function atributos()
    {
        return $this->hasMany(VariacionAtributo::class, 'id_variacion');
    }

    // Accesor para verificar si tiene oferta
    public function getTieneOfertaAttribute()
    {
        return $this->dPrecio_oferta && $this->dPrecio_oferta > 0 && $this->dPrecio_oferta < $this->dPrecio;
    }

    // Accesor para nombre de la combinación
    public function getNombreCombinacionAttribute()
    {
        $nombres = [];
        foreach ($this->atributos as $atributo) {
            if ($atributo->valor) {
                $nombres[] = $atributo->valor->vValor;
            }
        }
        return !empty($nombres) ? implode(' / ', $nombres) : 'Sin atributos';
    }
}