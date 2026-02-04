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
        'vNombre_variacion',
        'dPrecio',
        'dPrecio_oferta',
        'iStock',
        'dPeso',
        'dLargo_cm',
        'dAncho_cm',
        'dAlto_cm',
        'vClase_envio',
        'tDescripcion',
        'vImagen',
        'bActivo',
        'bTiene_oferta', // Agregado
        'dFecha_inicio_oferta',
        'dFecha_fin_oferta',
        'vMotivo_oferta'
    ];

    protected $casts = [
       'bActivo' => 'boolean',
        'bTiene_oferta' => 'boolean', // Agregado
        'dPrecio' => 'decimal:2',
        'dPrecio_oferta' => 'decimal:2',
        'iStock' => 'integer',
        'dPeso' => 'decimal:2',
        'dLargo_cm' => 'decimal:2',
        'dAncho_cm' => 'decimal:2',
        'dAlto_cm' => 'decimal:2'
    ];

    // Mutadores para campos de oferta
    public function setBTieneOfertaAttribute($value)
    {
        $this->attributes['bTiene_oferta'] = $value ? 1 : 0;
    }

    public function setDPrecioOfertaAttribute($value)
    {
        if (empty($value) || $value == 0) {
            $this->attributes['dPrecio_oferta'] = null;
        } else {
            $this->attributes['dPrecio_oferta'] = $value;
        }
    }

    public function setDFechaInicioOfertaAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['dFecha_inicio_oferta'] = null;
        } else {
            $this->attributes['dFecha_inicio_oferta'] = $value;
        }
    }

    public function setDFechaFinOfertaAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['dFecha_fin_oferta'] = null;
        } else {
            $this->attributes['dFecha_fin_oferta'] = $value;
        }
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    public function atributos()
    {
        return $this->hasMany(VariacionAtributo::class, 'id_variacion');
    }

    // Calcular volumen cúbico
    public function getVolumenAttribute()
    {
        if ($this->dLargo_cm && $this->dAncho_cm && $this->dAlto_cm) {
            return $this->dLargo_cm * $this->dAncho_cm * $this->dAlto_cm;
        }
        return 0;
    }

    // Calcular peso volumétrico (para envíos)
    public function getPesoVolumetricoAttribute()
    {
        $volumen = $this->getVolumenAttribute();
        if ($volumen > 0) {
            return $volumen / 5000;
        }
        return 0;
    }

    // Método para obtener dimensiones formateadas
    public function getDimensionesFormateadasAttribute()
    {
        if ($this->dLargo_cm && $this->dAncho_cm && $this->dAlto_cm) {
            return number_format($this->dLargo_cm, 1) . ' × ' . 
                   number_format($this->dAncho_cm, 1) . ' × ' . 
                   number_format($this->dAlto_cm, 1) . ' cm';
        }
        return 'No especificado';
    }

    // Accesor para verificar si tiene oferta (usando bTiene_oferta como fuente de verdad)
    public function getTieneOfertaAttribute()
    {
        return $this->bTiene_oferta == 1 && 
               $this->dPrecio_oferta && 
               $this->dPrecio_oferta > 0 && 
               $this->dPrecio_oferta < $this->dPrecio &&
               $this->esOfertaVigente();
    }

    // Verificar si la oferta está vigente (entre fechas)
    public function esOfertaVigente()
    {
        if (!$this->bTiene_oferta || !$this->dFecha_inicio_oferta || !$this->dFecha_fin_oferta) {
            return false;
        }
        
        $hoy = now()->toDateString();
        return $hoy >= $this->dFecha_inicio_oferta && $hoy <= $this->dFecha_fin_oferta;
    }

    // Método para compatibilidad con llamadas antiguas
    public function tieneOferta()
    {
        return $this->tiene_oferta;
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

    // Método para obtener el atributo activo
    public function estaActivo()
    {
        return $this->bActivo;
    }

    // Accesor para obtener el precio final (considerando oferta)
    public function getPrecioFinalAttribute()
    {
        if ($this->tiene_oferta) {
            return $this->dPrecio_oferta;
        }
        return $this->dPrecio;
    }

    // Accesor para verificar si hay stock disponible
    public function getHayStockAttribute()
    {
        return $this->iStock > 0;
    }

    // Accesor para nivel de stock
    public function getNivelStockAttribute()
    {
        if ($this->iStock <= 0) {
            return 'agotado';
        } elseif ($this->iStock <= 5) {
            return 'bajo';
        } elseif ($this->iStock <= 20) {
            return 'medio';
        } else {
            return 'alto';
        }
    }

    // Método para reducir stock
    public function reducirStock($cantidad = 1)
    {
        if ($this->iStock >= $cantidad) {
            $this->iStock -= $cantidad;
            return $this->save();
        }
        return false;
    }

    // Método para aumentar stock
    public function aumentarStock($cantidad = 1)
    {
        $this->iStock += $cantidad;
        return $this->save();
    }

    // Accesor para obtener los atributos como array
    public function getAtributosArrayAttribute()
    {
        $atributosArray = [];
        foreach ($this->atributos as $atributo) {
            if ($atributo->valor) {
                $atributosArray[$atributo->atributo->vNombre] = $atributo->valor->vValor;
            }
        }
        return $atributosArray;
    }

    // Método para obtener el nombre completo de la variación
    public function getNombreCompletoAttribute()
    {
        $nombreProducto = $this->producto ? $this->producto->vNombre : '';
        $combinacion = $this->nombre_combinacion;
        
        if ($combinacion && $combinacion !== 'Sin atributos') {
            return $nombreProducto . ' - ' . $combinacion;
        }
        
        return $nombreProducto . ' - ' . $this->vSKU;
    }

    // Accesor para obtener la URL de la imagen
    public function getImagenUrlAttribute()
    {
        if ($this->vImagen) {
            if (filter_var($this->vImagen, FILTER_VALIDATE_URL)) {
                return $this->vImagen;
            }
            return asset('storage/' . $this->vImagen);
        }
        
        if ($this->producto && count($this->producto->imagenes) > 0) {
            return $this->producto->imagenes[0];
        }
        
        return asset('images/default-product.png');
    }

    // Scope para variaciones activas
    public function scopeActivas($query)
    {
        return $query->where('bActivo', true);
    }

    // Scope para variaciones con stock
    public function scopeConStock($query)
    {
        return $query->where('iStock', '>', 0);
    }

    // Scope para variaciones en oferta
    public function scopeEnOferta($query)
    {
        $hoy = now()->toDateString();
        return $query->where('bTiene_oferta', 1)
                     ->whereNotNull('dPrecio_oferta')
                     ->where('dPrecio_oferta', '>', 0)
                     ->whereRaw('dPrecio_oferta < dPrecio')
                     ->where('dFecha_inicio_oferta', '<=', $hoy)
                     ->where('dFecha_fin_oferta', '>=', $hoy);
    }

    // Método para verificar si es la variación predeterminada
    public function esPredeterminada()
    {
        return $this->atributos()->count() === 0;
    }

    // Método para obtener el porcentaje de descuento
    public function getPorcentajeDescuentoAttribute()
    {
        if (!$this->tiene_oferta || $this->dPrecio <= 0) {
            return 0;
        }
        
        $descuento = (($this->dPrecio - $this->dPrecio_oferta) / $this->dPrecio) * 100;
        return max(0, min(100, round($descuento, 0)));
    }

    // Método para formatear el precio con símbolo de moneda
    public function getPrecioFormateadoAttribute()
    {
        return '$' . number_format($this->dPrecio, 2);
    }

    public function getPrecioOfertaFormateadoAttribute()
    {
        if ($this->tiene_oferta) {
            return '$' . number_format($this->dPrecio_oferta, 2);
        }
        return null;
    }

    // Método para obtener el peso formateado
    public function getPesoFormateadoAttribute()
    {
        if ($this->dPeso) {
            return number_format($this->dPeso, 2) . ' kg';
        }
        return 'No especificado';
    }

    // Eventos del modelo
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($variacion) {
            if (!isset($variacion->bActivo)) {
                $variacion->bActivo = true;
            }
            if (!isset($variacion->bTiene_oferta)) {
                $variacion->bTiene_oferta = false;
            }
        });

        static::deleting(function ($variacion) {
            $variacion->atributos()->delete();
        });
    }
}