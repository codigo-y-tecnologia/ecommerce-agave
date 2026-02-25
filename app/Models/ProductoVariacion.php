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

    // Desactivar timestamps
    public $timestamps = false;

    protected $fillable = [
        'id_producto',
        'vSKU',
        'vCodigo_barras',
        'vNombre_variacion',
        'dPrecio',
        'dPrecio_adicional',
        'iStock_variacion',
        'dPeso',
        'vClase_envio',
        'tDescripcion',
        'vImagen',
        'bActivo',
        'tFecha_registro',
        'tFecha_actualizacion'
    ];

    protected $casts = [
        'bActivo' => 'boolean',
        'dPrecio' => 'decimal:2',
        'dPrecio_adicional' => 'decimal:2',
        'iStock_variacion' => 'integer',
        'dPeso' => 'decimal:2',
        'tFecha_registro' => 'datetime',
        'tFecha_actualizacion' => 'datetime'
    ];

    // ============ MÉTODOS PARA IMÁGENES MÚLTIPLES ============

    /**
     * Guardar imagen principal (reemplaza la imagen existente)
     */
    public function guardarImagenPrincipal($imagen)
    {
        $carpeta = 'variaciones/' . $this->id_variacion;
        
        if (!Storage::disk('public')->exists($carpeta)) {
            Storage::disk('public')->makeDirectory($carpeta);
        }
        
        // Eliminar imagen anterior si existe
        if ($this->vImagen) {
            $rutaAnterior = str_replace('/storage/', '', $this->vImagen);
            if (Storage::disk('public')->exists($rutaAnterior)) {
                Storage::disk('public')->delete($rutaAnterior);
            }
        }
        
        $extension = $imagen->getClientOriginalExtension();
        $nombreArchivo = 'principal_' . time() . '.' . $extension;
        $ruta = $imagen->storeAs($carpeta, $nombreArchivo, 'public');
        
        $this->vImagen = '/storage/' . $ruta;
        $this->saveQuietly();
        
        return $this->vImagen;
    }

    /**
     * Guardar GIF (se guarda en la misma carpeta pero con nombre gif_)
     */
    public function guardarGif($gif)
    {
        $carpeta = 'variaciones/' . $this->id_variacion;
        
        if (!Storage::disk('public')->exists($carpeta)) {
            Storage::disk('public')->makeDirectory($carpeta);
        }
        
        $extension = $gif->getClientOriginalExtension();
        $nombreArchivo = 'gif_' . time() . '.' . $extension;
        $ruta = $gif->storeAs($carpeta, $nombreArchivo, 'public');
        
        // Guardamos la ruta del GIF (no hay campo en BD, se buscará por patrón)
        return '/storage/' . $ruta;
    }

    /**
     * Guardar imágenes adicionales
     */
    public function guardarImagenesAdicionales($imagenes)
    {
        $carpeta = 'variaciones/' . $this->id_variacion . '/adicionales';
        
        if (!Storage::disk('public')->exists($carpeta)) {
            Storage::disk('public')->makeDirectory($carpeta);
        }
        
        $imagenesGuardadas = [];
        $imagenesExistentes = Storage::disk('public')->files($carpeta);
        $numeroInicio = count($imagenesExistentes);
        
        foreach ($imagenes as $index => $imagen) {
            if ($numeroInicio + $index >= 7) {
                break; // Máximo 7 imágenes adicionales
            }
            
            $extension = $imagen->getClientOriginalExtension();
            $nombreArchivo = 'imagen_' . ($numeroInicio + $index + 1) . '_' . time() . '.' . $extension;
            $ruta = $imagen->storeAs($carpeta, $nombreArchivo, 'public');
            $imagenesGuardadas[] = '/storage/' . $ruta;
        }
        
        return $imagenesGuardadas;
    }

    /**
     * Obtener URL del GIF si existe
     */
    public function getGifUrlAttribute()
    {
        $carpeta = 'variaciones/' . $this->id_variacion;
        
        if (Storage::disk('public')->exists($carpeta)) {
            $archivos = Storage::disk('public')->files($carpeta);
            
            foreach ($archivos as $archivo) {
                if (strpos($archivo, 'gif_') !== false) {
                    return '/storage/' . $archivo;
                }
            }
        }
        
        return null;
    }

    /**
     * Obtener imágenes adicionales
     */
    public function getImagenesAdicionalesAttribute()
    {
        $carpeta = 'variaciones/' . $this->id_variacion . '/adicionales';
        $imagenes = [];
        
        if (Storage::disk('public')->exists($carpeta)) {
            $archivos = Storage::disk('public')->files($carpeta);
            sort($archivos);
            
            foreach ($archivos as $archivo) {
                if (preg_match('/\.(jpg|jpeg|png|webp)$/i', $archivo)) {
                    $imagenes[] = '/storage/' . $archivo;
                }
            }
        }
        
        return $imagenes;
    }

    /**
     * Obtener todas las imágenes en orden: Principal -> GIF -> Adicionales
     */
    public function getTodasLasImagenesAttribute()
    {
        $imagenes = [];
        
        // 1. Imagen principal (campo vImagen)
        if ($this->vImagen) {
            $imagenes[] = [
                'url' => $this->vImagen,
                'tipo' => 'principal',
                'nombre' => 'Principal'
            ];
        }
        
        // 2. GIF
        $gifUrl = $this->gif_url;
        if ($gifUrl) {
            $imagenes[] = [
                'url' => $gifUrl,
                'tipo' => 'gif',
                'nombre' => 'GIF'
            ];
        }
        
        // 3. Imágenes adicionales
        foreach ($this->imagenes_adicionales as $index => $url) {
            $imagenes[] = [
                'url' => $url,
                'tipo' => 'adicional',
                'nombre' => 'Adicional ' . ($index + 1)
            ];
        }
        
        return $imagenes;
    }

    /**
     * Contar número total de archivos multimedia
     */
    public function getNumeroImagenesAttribute()
    {
        $count = 0;
        if ($this->vImagen) $count++;
        if ($this->gif_url) $count++;
        $count += count($this->imagenes_adicionales);
        return $count;
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
        $archivos = [];
        
        if (Storage::disk('public')->exists($carpeta)) {
            $archivosStorage = Storage::disk('public')->files($carpeta);
            
            foreach ($archivosStorage as $archivo) {
                if (preg_match('/\.(jpg|jpeg|png|webp)$/i', $archivo)) {
                    $archivos[] = basename($archivo);
                }
            }
        }
        
        return $archivos;
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

    // ============ MÉTODOS DE UTILIDAD ============

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

    // Accesor para URL de imagen (compatibilidad)
    public function getImagenUrlAttribute()
    {
        if ($this->vImagen) {
            return $this->vImagen;
        }
        
        if ($this->gif_url) {
            return $this->gif_url;
        }
        
        if (count($this->imagenes_adicionales) > 0) {
            return $this->imagenes_adicionales[0];
        }
        
        return asset('images/default-product.png');
    }

    // Método para formatear el precio
    public function getPrecioFormateadoAttribute()
    {
        return '$' . number_format($this->dPrecio, 2);
    }

    // Método para obtener el peso formateado
    public function getPesoFormateadoAttribute()
    {
        if ($this->dPeso) {
            return number_format($this->dPeso, 2) . ' kg';
        }
        return 'No especificado';
    }

    // Badge para clase de envío
    public function getClaseEnvioBadgeAttribute()
    {
        switch ($this->vClase_envio) {
            case 'estandar':
                return '<span class="badge bg-primary">Estándar</span>';
            case 'express':
                return '<span class="badge bg-success">Express</span>';
            case 'fragil':
                return '<span class="badge bg-warning">Frágil</span>';
            case 'grandes_dimensiones':
                return '<span class="badge bg-danger">Grandes dimensiones</span>';
            default:
                return '<span class="badge bg-secondary">No especificada</span>';
        }
    }

    // ============ SCOPES ============

    public function scopeActivas($query)
    {
        return $query->where('bActivo', true);
    }

    public function scopeConStock($query)
    {
        return $query->where('iStock_variacion', '>', 0);
    }

    // ============ EVENTOS DEL MODELO ============

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($variacion) {
            if (empty($variacion->tFecha_registro)) {
                $variacion->tFecha_registro = now();
            }
            if (!isset($variacion->bActivo)) {
                $variacion->bActivo = true;
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
}