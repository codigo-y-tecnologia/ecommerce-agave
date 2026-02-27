<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductoVariacion extends Model
{
    use HasFactory;

    protected $table = 'tbl_producto_variaciones';
    protected $primaryKey = 'id_variacion';
    
    // Desactivar timestamps de Laravel porque usamos campos personalizados
    public $timestamps = false;

    protected $fillable = [
        'id_producto',
        'vSKU',
        'vCodigo_barras',
        'vNombre_variacion',
        'dPrecio',
        'dPrecio_oferta',
        'dFecha_inicio_oferta',
        'dFecha_fin_oferta',
        'vMotivo_oferta',
        'bTiene_oferta',
        'iStock',
        'dPeso',
        'dLargo_cm',
        'dAncho_cm',
        'dAlto_cm',
        'vClase_envio',
        'tDescripcion',
        'vImagen',
        'bActivo',
        'id_impuesto', // NUEVO CAMPO
        'tFecha_registro',
        'tFecha_actualizacion'
    ];

    protected $casts = [
        'dPrecio' => 'decimal:2',
        'dPrecio_oferta' => 'decimal:2',
        'iStock' => 'integer',
        'bTiene_oferta' => 'boolean',
        'bActivo' => 'boolean',
        'dPeso' => 'decimal:3',
        'dLargo_cm' => 'decimal:2',
        'dAncho_cm' => 'decimal:2',
        'dAlto_cm' => 'decimal:2',
        'tFecha_registro' => 'datetime',
        'tFecha_actualizacion' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($variacion) {
            if (empty($variacion->tFecha_registro)) {
                $variacion->tFecha_registro = now();
            }
            
            // Si no tiene SKU, generar uno basado en el producto y atributos
            if (empty($variacion->vSKU)) {
                $producto = $variacion->producto;
                if ($producto) {
                    $variacion->vSKU = $producto->vCodigo_barras . '-VAR-' . uniqid();
                }
            }
        });

        static::updating(function ($variacion) {
            $variacion->tFecha_actualizacion = now();
        });
        
        static::deleting(function ($variacion) {
            $variacion->eliminarTodasLasImagenes();
            $variacion->atributos()->delete();
        });
    }

    // ============ RELACIONES ============

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    public function atributos()
    {
        return $this->hasMany(VariacionAtributo::class, 'id_variacion');
    }

    /**
     * Relación con impuesto (una variación puede tener un impuesto)
     */
    public function impuesto()
    {
        return $this->belongsTo(Impuesto::class, 'id_impuesto');
    }

    // ============ MÉTODOS DE OFERTA/DESCUENTO ============

    /**
     * Verificar si la oferta está vigente
     */
    public function ofertaVigente()
    {
        if (!$this->bTiene_oferta || !$this->dPrecio_oferta) {
            return false;
        }
        
        $fechaActual = now()->toDateString();
        
        if ($this->dFecha_inicio_oferta && $this->dFecha_fin_oferta) {
            return $fechaActual >= $this->dFecha_inicio_oferta && 
                   $fechaActual <= $this->dFecha_fin_oferta;
        }
        
        return $this->bTiene_oferta;
    }

    /**
     * Obtener el precio actual (normal o de oferta si está vigente)
     */
    public function getPrecioActualAttribute()
    {
        if ($this->ofertaVigente()) {
            return $this->dPrecio_oferta;
        }
        return $this->dPrecio;
    }

    /**
     * Obtener el porcentaje de descuento
     */
    public function getPorcentajeDescuentoAttribute()
    {
        if ($this->ofertaVigente() && $this->dPrecio_oferta < $this->dPrecio) {
            $descuento = (($this->dPrecio - $this->dPrecio_oferta) / $this->dPrecio) * 100;
            return round($descuento);
        }
        return 0;
    }

    /**
     * Verificar si tiene descuento activo
     */
    public function tieneDescuentoActivo()
    {
        return $this->ofertaVigente();
    }

    // ============ MÉTODOS DE IMPUESTOS ============

    /**
     * Calcular el precio final con impuesto incluido
     */
    public function getPrecioFinalAttribute()
    {
        $precioBase = $this->precio_actual;
        $totalImpuestos = 0;
        
        if ($this->impuesto && $this->impuesto->bActivo) {
            $totalImpuestos = $precioBase * ($this->impuesto->dPorcentaje / 100);
        }
        
        return $precioBase + $totalImpuestos;
    }

    /**
     * Obtener el total de impuesto aplicado
     */
    public function getTotalImpuestoAttribute()
    {
        if ($this->impuesto && $this->impuesto->bActivo) {
            return $this->precio_actual * ($this->impuesto->dPorcentaje / 100);
        }
        return 0;
    }

    /**
     * Obtener el porcentaje de impuesto
     */
    public function getPorcentajeImpuestoAttribute()
    {
        return $this->impuesto ? $this->impuesto->dPorcentaje : 0;
    }

    /**
     * Obtener el nombre del impuesto
     */
    public function getNombreImpuestoAttribute()
    {
        return $this->impuesto ? $this->impuesto->vNombre : 'Sin impuesto';
    }

    // ============ ACCESORES PARA IMÁGENES ============

    /**
     * Obtener la URL de la imagen principal de la variación
     */
    public function getImagenPrincipalAttribute()
    {
        if ($this->vImagen) {
            // Si ya tiene una imagen guardada en el campo vImagen
            return Storage::url($this->vImagen);
        }
        
        // Buscar en la carpeta específica de la variación
        $carpeta = 'variaciones/' . $this->id_variacion;
        
        if (Storage::disk('public')->exists($carpeta)) {
            $archivos = Storage::disk('public')->files($carpeta);
            
            // Buscar archivo que comience con 'principal_'
            foreach ($archivos as $archivo) {
                if (strpos($archivo, 'principal_') !== false) {
                    return Storage::url($archivo);
                }
            }
        }
        
        return null;
    }

    /**
     * Obtener la URL del GIF de la variación
     */
    public function getGifUrlAttribute()
    {
        $carpeta = 'variaciones/' . $this->id_variacion;
        
        if (Storage::disk('public')->exists($carpeta)) {
            $archivos = Storage::disk('public')->files($carpeta);
            
            foreach ($archivos as $archivo) {
                if (strpos($archivo, 'gif_') !== false) {
                    return Storage::url($archivo);
                }
            }
        }
        
        return null;
    }

    /**
     * Obtener todas las imágenes adicionales de la variación
     */
    public function getImagenesAdicionalesAttribute()
    {
        $carpetaAdicionales = 'variaciones/' . $this->id_variacion . '/adicionales';
        $imagenes = [];
        
        if (Storage::disk('public')->exists($carpetaAdicionales)) {
            $archivos = Storage::disk('public')->files($carpetaAdicionales);
            
            // Ordenar por nombre
            sort($archivos);
            
            foreach ($archivos as $archivo) {
                if (preg_match('/\.(jpg|jpeg|png|webp)$/i', $archivo)) {
                    $imagenes[] = Storage::url($archivo);
                }
            }
        }
        
        return $imagenes;
    }

    /**
     * Obtener TODAS las imágenes de la variación en un array simple (para la vista show-public)
     */
    public function getImagenesAttribute()
    {
        $imagenes = [];
        
        // 1. Imagen principal
        $imagenPrincipal = $this->imagen_principal;
        if ($imagenPrincipal) {
            $imagenes[] = $imagenPrincipal;
        }
        
        // 2. GIF (si existe)
        $gifUrl = $this->gif_url;
        if ($gifUrl) {
            $imagenes[] = $gifUrl;
        }
        
        // 3. Imágenes adicionales
        $adicionales = $this->imagenes_adicionales;
        foreach ($adicionales as $url) {
            $imagenes[] = $url;
        }
        
        return $imagenes; // Retorna: ["/storage/variaciones/1/principal.jpg", "/storage/variaciones/1/adicionales/imagen_1.jpg"]
    }

    // ============ MÉTODOS PARA GUARDAR Y ELIMINAR IMÁGENES ============

    /**
     * Guardar imagen principal de la variación
     */
    public function guardarImagenPrincipal($imagen)
    {
        $carpeta = 'variaciones/' . $this->id_variacion;
        
        if (!Storage::disk('public')->exists($carpeta)) {
            Storage::disk('public')->makeDirectory($carpeta);
        }
        
        // Eliminar imagen principal anterior si existe
        $this->eliminarImagenPrincipal();
        
        $extension = $imagen->getClientOriginalExtension();
        $nombreArchivo = 'principal_' . time() . '.' . $extension;
        $ruta = $imagen->storeAs($carpeta, $nombreArchivo, 'public');
        
        // Actualizar campo vImagen con la ruta relativa
        $this->vImagen = $ruta;
        $this->saveQuietly();
        
        return Storage::url($ruta);
    }

    /**
     * Guardar GIF de la variación
     */
    public function guardarGif($gif)
    {
        $carpeta = 'variaciones/' . $this->id_variacion;
        
        if (!Storage::disk('public')->exists($carpeta)) {
            Storage::disk('public')->makeDirectory($carpeta);
        }
        
        // Eliminar GIF anterior si existe
        $gifAnterior = $this->gif_url;
        if ($gifAnterior) {
            $rutaGifAnterior = str_replace('/storage/', '', $gifAnterior);
            if (Storage::disk('public')->exists($rutaGifAnterior)) {
                Storage::disk('public')->delete($rutaGifAnterior);
            }
        }
        
        $extension = $gif->getClientOriginalExtension();
        $nombreArchivo = 'gif_' . time() . '.' . $extension;
        $ruta = $gif->storeAs($carpeta, $nombreArchivo, 'public');
        
        return Storage::url($ruta);
    }

    /**
     * Guardar imágenes adicionales de la variación
     */
    public function guardarImagenesAdicionales($imagenes)
    {
        $carpeta = 'variaciones/' . $this->id_variacion . '/adicionales';
        
        if (!Storage::disk('public')->exists($carpeta)) {
            Storage::disk('public')->makeDirectory($carpeta);
        }
        
        $imagenesExistentes = Storage::disk('public')->files($carpeta);
        $numeroInicio = count($imagenesExistentes);
        
        $imagenesGuardadas = [];
        $contador = 0;
        $maxImagenes = 7;
        
        foreach ($imagenes as $imagen) {
            // Verificar límite máximo
            if (($numeroInicio + $contador) >= $maxImagenes) {
                break;
            }
            
            $extension = $imagen->getClientOriginalExtension();
            $nombreArchivo = 'imagen_' . ($numeroInicio + $contador + 1) . '_' . time() . '.' . $extension;
            $ruta = $imagen->storeAs($carpeta, $nombreArchivo, 'public');
            $imagenesGuardadas[] = Storage::url($ruta);
            $contador++;
        }
        
        return $imagenesGuardadas;
    }

    /**
     * Eliminar imagen principal
     */
    public function eliminarImagenPrincipal()
    {
        if ($this->vImagen) {
            if (Storage::disk('public')->exists($this->vImagen)) {
                Storage::disk('public')->delete($this->vImagen);
            }
            $this->vImagen = null;
            $this->saveQuietly();
        }
        
        // También buscar en la carpeta
        $carpeta = 'variaciones/' . $this->id_variacion;
        if (Storage::disk('public')->exists($carpeta)) {
            $archivos = Storage::disk('public')->files($carpeta);
            foreach ($archivos as $archivo) {
                if (strpos($archivo, 'principal_') !== false) {
                    Storage::disk('public')->delete($archivo);
                }
            }
        }
    }

    /**
     * Eliminar imágenes adicionales específicas
     */
    public function eliminarImagenesAdicionalesEspecificas($nombresArchivos)
    {
        $carpeta = 'variaciones/' . $this->id_variacion . '/adicionales';
        
        foreach ($nombresArchivos as $nombreArchivo) {
            $rutaCompleta = $carpeta . '/' . $nombreArchivo;
            if (Storage::disk('public')->exists($rutaCompleta)) {
                Storage::disk('public')->delete($rutaCompleta);
            }
        }
    }

    /**
     * Eliminar todas las imágenes de la variación
     */
    public function eliminarTodasLasImagenes()
    {
        $carpeta = 'variaciones/' . $this->id_variacion;
        
        if (Storage::disk('public')->exists($carpeta)) {
            Storage::disk('public')->deleteDirectory($carpeta);
        }
        
        $this->vImagen = null;
        $this->saveQuietly();
    }

    /**
     * Obtener nombres de archivos de imágenes adicionales
     */
    public function getNombresArchivosImagenesAdicionales()
    {
        $carpeta = 'variaciones/' . $this->id_variacion . '/adicionales';
        $imagenes = [];
        
        if (Storage::disk('public')->exists($carpeta)) {
            $archivos = Storage::disk('public')->files($carpeta);
            
            foreach ($archivos as $archivo) {
                if (preg_match('/\.(jpg|jpeg|png|webp|gif)$/i', $archivo)) {
                    $imagenes[] = basename($archivo);
                }
            }
        }
        
        return $imagenes;
    }

    /**
     * Número total de imágenes
     */
    public function getNumeroImagenesAttribute()
    {
        $total = 0;
        
        if ($this->imagen_principal) $total++;
        if ($this->gif_url) $total++;
        $total += count($this->imagenes_adicionales);
        
        return $total;
    }

    /**
     * Verificar si tiene espacio para más imágenes
     */
    public function puedeAgregarMasImagenes()
    {
        return $this->numero_imagenes < 9; // 1 principal + 1 gif + 7 adicionales
    }

    // ============ MÉTODOS DE UTILIDAD ============

    /**
     * Accesor para dimensiones formateadas
     */
    public function getDimensionesFormateadasAttribute()
    {
        if ($this->dLargo_cm && $this->dAncho_cm && $this->dAlto_cm) {
            return number_format($this->dLargo_cm, 2) . ' × ' . 
                   number_format($this->dAncho_cm, 2) . ' × ' . 
                   number_format($this->dAlto_cm, 2) . ' cm';
        }
        return 'No especificado';
    }

    /**
     * Accesor para peso formateado
     */
    public function getPesoFormateadoAttribute()
    {
        if ($this->dPeso) {
            return number_format($this->dPeso, 3) . ' kg';
        }
        return 'No especificado';
    }

    /**
     * Accesor para volumen
     */
    public function getVolumenAttribute()
    {
        if ($this->dLargo_cm && $this->dAncho_cm && $this->dAlto_cm) {
            return $this->dLargo_cm * $this->dAncho_cm * $this->dAlto_cm;
        }
        return null;
    }

    /**
     * Accesor para clase de envío formateada
     */
    public function getClaseEnvioFormateadaAttribute()
    {
        switch ($this->vClase_envio) {
            case 'estandar':
                return 'Estándar';
            case 'express':
                return 'Express';
            case 'fragil':
                return 'Frágil';
            case 'grandes_dimensiones':
                return 'Grandes dimensiones';
            default:
                return $this->vClase_envio ?: 'No especificada';
        }
    }

    /**
     * Calcular peso volumétrico
     */
    public function getPesoVolumetricoAttribute()
    {
        if ($this->volumen) {
            return $this->volumen / 5000;
        }
        return null;
    }

    /**
     * Obtener el precio con formato
     */
    public function getPrecioFormateadoAttribute()
    {
        return '$' . number_format($this->precio_actual, 2);
    }

    /**
     * Obtener el precio final con formato
     */
    public function getPrecioFinalFormateadoAttribute()
    {
        return '$' . number_format($this->precio_final, 2);
    }

    /**
     * Obtener el stock con formato
     */
    public function getStockFormateadoAttribute()
    {
        if ($this->iStock > 10) {
            return '<span class="text-success">' . $this->iStock . ' unidades</span>';
        } elseif ($this->iStock > 0) {
            return '<span class="text-warning">' . $this->iStock . ' unidades (bajo stock)</span>';
        } else {
            return '<span class="text-danger">Sin stock</span>';
        }
    }
}