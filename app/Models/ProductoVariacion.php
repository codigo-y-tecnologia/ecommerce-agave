<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoVariacion extends Model
{
    use HasFactory;

    protected $table = 'tbl_producto_variaciones';
    protected $primaryKey = 'id_variacion';

    protected $fillable = [
        'id_producto',
        'vSKU',
        'vCodigo_barras',
        'dPrecio',
        'dPrecio_oferta',
        'iStock',
        'dPeso',
        'dAncho',
        'dAlto',
        'dProfundidad',
        'bActivo'
    ];

    protected $casts = [
        'dPrecio' => 'decimal:2',
        'dPrecio_oferta' => 'decimal:2',
        'iStock' => 'integer',
        'dPeso' => 'decimal:2',
        'dAncho' => 'decimal:2',
        'dAlto' => 'decimal:2',
        'dProfundidad' => 'decimal:2',
        'bActivo' => 'boolean'
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    public function atributos()
    {
        return $this->hasMany(VariacionAtributo::class, 'id_variacion')
            ->with(['atributo', 'valor']);
    }

    public function getNombreCombinacionAttribute()
    {
        $nombres = [];
        foreach ($this->atributos as $atributo) {
            $nombres[] = $atributo->atributo->vNombre . ': ' . $atributo->valor->vValor;
        }
        return implode(' / ', $nombres);
    }

    public function getPrecioFinalAttribute()
    {
        return $this->dPrecio_oferta ?: $this->dPrecio;
    }

    public function tieneOferta()
    {
        return !empty($this->dPrecio_oferta) && $this->dPrecio_oferta < $this->dPrecio;
    }

    public function porcentajeDescuento()
    {
        if (!$this->tieneOferta()) {
            return 0;
        }
        
        $descuento = (($this->dPrecio - $this->dPrecio_oferta) / $this->dPrecio) * 100;
        return round($descuento);
    }

    public function estaBajoStock()
    {
        return $this->iStock > 0 && $this->iStock <= 5;
    }

    public function sinStock()
    {
        return $this->iStock <= 0;
    }
}