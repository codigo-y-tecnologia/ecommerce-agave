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
        'id_impuesto',
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
        'tFecha_actualizacion' => 'datetime',
        'dFecha_inicio_oferta' => 'date',
        'dFecha_fin_oferta' => 'date'
    ];

    protected $appends = [
        'imagen_principal',
        'gif_url',
        'imagenes_adicionales',
        'imagenes',
        'numero_imagenes',
        'precio_actual',
        'porcentaje_descuento',
        'precio_final',
        'total_impuesto',
        'porcentaje_impuesto',
        'nombre_impuesto',
        'dimensiones_formateadas',
        'peso_formateado',
        'volumen',
        'clase_envio_formateada',
        'peso_volumetrico',
        'precio_formateado',
        'precio_final_formateado',
        'stock_formateado'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($variacion) {
            if (empty($variacion->tFecha_registro)) {
                $variacion->tFecha_registro = now();
            }
            
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

    public function productoPadre()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    public function atributos()
    {
        return $this->hasMany(VariacionAtributo::class, 'id_variacion');
    }

    public function impuesto()
    {
        return $this->belongsTo(Impuesto::class, 'id_impuesto');
    }

    /**
     * Relación con imágenes de variación
     */
    public function imagenesRegistradas()
    {
        return $this->hasMany(VariacionImagen::class, 'id_variacion')->orderBy('iOrden');
    }

    // ============ MÉTODOS DE DESCUENTO (USANDO TU NOMENCLATURA) ============

    /**
     * Verificar si tiene descuento activo (usando tu nomenclatura)
     */
    public function tieneDescuentoActivo()
    {
        // Si no tiene descuento activado o no tiene precio de descuento
        if (!$this->bTiene_oferta || $this->dPrecio_oferta === null) {
            return false;
        }

        $fechaActual = now()->toDateString();

        // Caso 1: Tiene ambas fechas definidas
        if ($this->dFecha_inicio_oferta && $this->dFecha_fin_oferta) {
            return $fechaActual >= $this->dFecha_inicio_oferta && 
                   $fechaActual <= $this->dFecha_fin_oferta;
        }

        // Caso 2: Solo tiene fecha de inicio
        if ($this->dFecha_inicio_oferta && !$this->dFecha_fin_oferta) {
            return $fechaActual >= $this->dFecha_inicio_oferta;
        }

        // Caso 3: Solo tiene fecha de fin
        if (!$this->dFecha_inicio_oferta && $this->dFecha_fin_oferta) {
            return $fechaActual <= $this->dFecha_fin_oferta;
        }

        // Caso 4: Tiene descuento activado pero sin fechas
        return true;
    }

    public function getPrecioActualAttribute()
    {
        if ($this->tieneDescuentoActivo()) {
            return $this->dPrecio_oferta;
        }
        return $this->dPrecio;
    }

    public function getPorcentajeDescuentoAttribute()
    {
        if ($this->tieneDescuentoActivo() && $this->dPrecio_oferta < $this->dPrecio) {
            $descuento = (($this->dPrecio - $this->dPrecio_oferta) / $this->dPrecio) * 100;
            return round($descuento);
        }
        return 0;
    }

    // ============ MÉTODO PARA OBTENER TEXTO DE ATRIBUTOS ============

    /**
     * Obtener texto de atributos para mostrar
     */
    public function getAtributosTexto()
    {
        $atributos = [];
        foreach ($this->atributos as $atributoRel) {
            if ($atributoRel->atributo && $atributoRel->valor) {
                $atributos[] = $atributoRel->valor->vValor;
            }
        }
        return implode(' - ', $atributos);
    }

    // ============ MÉTODOS DE IMPUESTOS ============

    public function getPrecioFinalAttribute()
    {
        $precioBase = $this->precio_actual;
        $totalImpuestos = 0;
        
        if ($this->impuesto && $this->impuesto->bActivo) {
            $totalImpuestos = $precioBase * ($this->impuesto->dPorcentaje / 100);
        }
        
        return $precioBase + $totalImpuestos;
    }

    public function getTotalImpuestoAttribute()
    {
        if ($this->impuesto && $this->impuesto->bActivo) {
            return $this->precio_actual * ($this->impuesto->dPorcentaje / 100);
        }
        return 0;
    }

    public function getPorcentajeImpuestoAttribute()
    {
        return $this->impuesto ? $this->impuesto->dPorcentaje : 0;
    }

    public function getNombreImpuestoAttribute()
    {
        return $this->impuesto ? $this->impuesto->vNombre : 'Sin impuesto';
    }

    // ============ ACCESORES PARA IMÁGENES ============

    /**
     * Obtener la URL de la imagen principal
     */
    public function getImagenPrincipalAttribute()
    {
        // Buscar en la tabla de imágenes
        $imagenPrincipal = $this->imagenesRegistradas()
            ->where('eTipo', 'principal')
            ->where('bActivo', true)
            ->first();
            
        if ($imagenPrincipal) {
            return $imagenPrincipal->url;
        }
        
        // Legacy: campo vImagen
        if ($this->vImagen && Storage::disk('public')->exists($this->vImagen)) {
            // Migrar a la nueva tabla
            $this->migrarImagenALaTabla($this->vImagen, 'principal');
            return Storage::url($this->vImagen);
        }
        
        // Buscar en carpeta
        $carpeta = 'variaciones/' . $this->id_variacion;
        if (Storage::disk('public')->exists($carpeta)) {
            $archivos = Storage::disk('public')->files($carpeta);
            sort($archivos);
            
            foreach ($archivos as $archivo) {
                if (strpos($archivo, 'principal_') !== false) {
                    // Migrar a la nueva tabla
                    $this->migrarImagenALaTabla($archivo, 'principal');
                    return Storage::url($archivo);
                }
            }
        }
        
        return null;
    }

    /**
     * Obtener la URL del GIF
     */
    public function getGifUrlAttribute()
    {
        // Buscar en la tabla de imágenes
        $gif = $this->imagenesRegistradas()
            ->where('eTipo', 'gif')
            ->where('bActivo', true)
            ->first();
            
        if ($gif) {
            return $gif->url;
        }
        
        // Buscar en carpeta
        $carpeta = 'variaciones/' . $this->id_variacion;
        if (Storage::disk('public')->exists($carpeta)) {
            $archivos = Storage::disk('public')->files($carpeta);
            
            foreach ($archivos as $archivo) {
                if (strpos($archivo, 'gif_') !== false) {
                    // Migrar a la nueva tabla
                    $this->migrarImagenALaTabla($archivo, 'gif');
                    return Storage::url($archivo);
                }
            }
        }
        
        return null;
    }

    /**
     * Obtener todas las imágenes adicionales
     */
    public function getImagenesAdicionalesAttribute()
    {
        $imagenes = [];
        
        // Buscar en la tabla de imágenes
        $imagenesAdicionales = $this->imagenesRegistradas()
            ->where('eTipo', 'adicional')
            ->where('bActivo', true)
            ->orderBy('iOrden')
            ->get();
            
        foreach ($imagenesAdicionales as $img) {
            $imagenes[] = $img->url;
        }
        
        if (!empty($imagenes)) {
            return $imagenes;
        }
        
        // Buscar en carpeta de adicionales (legado)
        $carpetaAdicionales = 'variaciones/' . $this->id_variacion . '/adicionales';
        if (Storage::disk('public')->exists($carpetaAdicionales)) {
            $archivos = Storage::disk('public')->files($carpetaAdicionales);
            sort($archivos);
            
            $orden = 0;
            foreach ($archivos as $archivo) {
                if (preg_match('/\.(jpg|jpeg|png|webp)$/i', $archivo)) {
                    // Migrar a la nueva tabla
                    $this->migrarImagenALaTabla($archivo, 'adicional', $orden);
                    $imagenes[] = Storage::url($archivo);
                    $orden++;
                }
            }
        }
        
        return $imagenes;
    }

    /**
     * Método PRINCIPAL - TODAS LAS IMÁGENES
     */
    public function getImagenesAttribute()
    {
        $imagenes = [];
        
        // 1. Imagen principal
        $imgPrincipal = $this->imagen_principal;
        if ($imgPrincipal) {
            $imagenes[] = $imgPrincipal;
        }
        
        // 2. GIF
        $gif = $this->gif_url;
        if ($gif) {
            $imagenes[] = $gif;
        }
        
        // 3. Imágenes adicionales
        $adicionales = $this->imagenes_adicionales;
        foreach ($adicionales as $url) {
            $imagenes[] = $url;
        }
        
        return array_values(array_unique($imagenes));
    }

    /**
     * Migrar imagen legacy a la nueva tabla
     */
    private function migrarImagenALaTabla($ruta, $tipo, $orden = 0)
    {
        if (!$this->id_variacion) return;
        
        // Verificar si ya existe en la tabla
        $existe = VariacionImagen::where('id_variacion', $this->id_variacion)
            ->where('vRuta', $ruta)
            ->exists();
            
        if (!$existe) {
            VariacionImagen::create([
                'id_variacion' => $this->id_variacion,
                'vRuta' => $ruta,
                'eTipo' => $tipo,
                'iOrden' => $orden,
                'bActivo' => true
            ]);
        }
    }

    // ============ MÉTODOS PARA GUARDAR Y ELIMINAR IMÁGENES ============

    /**
     * Guardar imagen principal
     */
    public function guardarImagenPrincipal($imagen)
    {
        if (!$this->id_variacion) {
            throw new \Exception('No se puede guardar imagen sin ID de variación');
        }
        
        $carpeta = 'variaciones/' . $this->id_variacion;
        
        if (!Storage::disk('public')->exists($carpeta)) {
            Storage::disk('public')->makeDirectory($carpeta);
        }
        
        // Eliminar imagen principal anterior
        $this->eliminarImagenPrincipal();
        
        $extension = $imagen->getClientOriginalExtension();
        $nombreArchivo = 'principal_' . time() . '_' . uniqid() . '.' . $extension;
        $ruta = $imagen->storeAs($carpeta, $nombreArchivo, 'public');
        
        // Guardar en la tabla de imágenes
        $imagenRegistrada = VariacionImagen::create([
            'id_variacion' => $this->id_variacion,
            'vRuta' => $ruta,
            'eTipo' => 'principal',
            'iOrden' => 0,
            'bActivo' => true
        ]);
        
        // Actualizar campo legacy por compatibilidad
        $this->vImagen = $ruta;
        $this->saveQuietly();
        
        \Log::info('Imagen principal de variación guardada en BD: ' . $ruta);
        
        return $imagenRegistrada->url;
    }

    /**
     * Guardar GIF
     */
    public function guardarGif($gif)
    {
        if (!$this->id_variacion) {
            throw new \Exception('No se puede guardar GIF sin ID de variación');
        }
        
        $carpeta = 'variaciones/' . $this->id_variacion;
        
        if (!Storage::disk('public')->exists($carpeta)) {
            Storage::disk('public')->makeDirectory($carpeta);
        }
        
        // Eliminar GIF anterior
        $this->eliminarGif();
        
        $extension = $gif->getClientOriginalExtension();
        $nombreArchivo = 'gif_' . time() . '_' . uniqid() . '.' . $extension;
        $ruta = $gif->storeAs($carpeta, $nombreArchivo, 'public');
        
        // Guardar en la tabla de imágenes
        $imagenRegistrada = VariacionImagen::create([
            'id_variacion' => $this->id_variacion,
            'vRuta' => $ruta,
            'eTipo' => 'gif',
            'iOrden' => 1,
            'bActivo' => true
        ]);
        
        \Log::info('GIF de variación guardado en BD: ' . $ruta);
        
        return $imagenRegistrada->url;
    }

    /**
     * Guardar imágenes adicionales
     */
    public function guardarImagenesAdicionales($imagenes)
    {
        if (!$this->id_variacion) {
            throw new \Exception('No se puede guardar imágenes sin ID de variación');
        }
        
        $carpeta = 'variaciones/' . $this->id_variacion . '/adicionales';
        
        if (!Storage::disk('public')->exists($carpeta)) {
            Storage::disk('public')->makeDirectory($carpeta);
        }
        
        // Obtener el último orden de las imágenes adicionales existentes
        $ultimoOrden = VariacionImagen::where('id_variacion', $this->id_variacion)
            ->where('eTipo', 'adicional')
            ->max('iOrden') ?? 0;
        
        $orden = $ultimoOrden + 1;
        $maxImagenes = 7;
        
        // Obtener total de imágenes adicionales actuales
        $totalActual = VariacionImagen::where('id_variacion', $this->id_variacion)
            ->where('eTipo', 'adicional')
            ->count();
        
        $imagenesGuardadas = [];
        
        // Asegurar que $imagenes sea un array
        if (!is_array($imagenes)) {
            $imagenes = [$imagenes];
        }
        
        foreach ($imagenes as $imagen) {
            // Verificar límite máximo
            if ($totalActual >= $maxImagenes) {
                break;
            }
            
            $extension = $imagen->getClientOriginalExtension();
            $nombreArchivo = 'imagen_' . $orden . '_' . time() . '_' . uniqid() . '.' . $extension;
            $ruta = $imagen->storeAs($carpeta, $nombreArchivo, 'public');
            
            // Guardar en la tabla de imágenes
            $imagenRegistrada = VariacionImagen::create([
                'id_variacion' => $this->id_variacion,
                'vRuta' => $ruta,
                'eTipo' => 'adicional',
                'iOrden' => $orden,
                'bActivo' => true
            ]);
            
            $imagenesGuardadas[] = $imagenRegistrada->url;
            $orden++;
            $totalActual++;
        }
        
        \Log::info('Imágenes adicionales de variación guardadas en BD: ' . count($imagenesGuardadas));
        
        return $imagenesGuardadas;
    }

    /**
     * Eliminar imagen principal
     */
    public function eliminarImagenPrincipal()
    {
        // Eliminar de la tabla
        $imagenes = VariacionImagen::where('id_variacion', $this->id_variacion)
            ->where('eTipo', 'principal')
            ->get();
            
        foreach ($imagenes as $img) {
            $img->delete(); // El modelo se encarga de eliminar el archivo
        }
        
        // Eliminar campo legacy
        if ($this->vImagen) {
            $this->vImagen = null;
            $this->saveQuietly();
        }
    }

    /**
     * Eliminar GIF
     */
    public function eliminarGif()
    {
        VariacionImagen::where('id_variacion', $this->id_variacion)
            ->where('eTipo', 'gif')
            ->delete();
    }

    /**
     * Eliminar imágenes adicionales específicas
     */
    public function eliminarImagenesAdicionalesEspecificas($nombresArchivos)
    {
        foreach ($nombresArchivos as $nombreArchivo) {
            VariacionImagen::where('id_variacion', $this->id_variacion)
                ->where('vRuta', 'like', '%' . $nombreArchivo)
                ->delete();
        }
    }

    /**
     * Eliminar todas las imágenes
     */
    public function eliminarTodasLasImagenes()
    {
        VariacionImagen::where('id_variacion', $this->id_variacion)->delete();
        
        // Eliminar carpeta física
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
        return VariacionImagen::where('id_variacion', $this->id_variacion)
            ->where('eTipo', 'adicional')
            ->get()
            ->map(function($img) {
                return basename($img->vRuta);
            })
            ->toArray();
    }

    /**
     * Número total de imágenes
     */
    public function getNumeroImagenesAttribute()
    {
        return VariacionImagen::where('id_variacion', $this->id_variacion)
            ->where('bActivo', true)
            ->count();
    }

    public function puedeAgregarMasImagenes()
    {
        return $this->numero_imagenes < 9;
    }

    // ============ MÉTODOS DE UTILIDAD ============

    public function getDimensionesFormateadasAttribute()
    {
        if ($this->dLargo_cm && $this->dAncho_cm && $this->dAlto_cm) {
            return number_format($this->dLargo_cm, 2) . ' × ' . 
                   number_format($this->dAncho_cm, 2) . ' × ' . 
                   number_format($this->dAlto_cm, 2) . ' cm';
        }
        return 'No especificado';
    }

    public function getPesoFormateadoAttribute()
    {
        if ($this->dPeso) {
            return number_format($this->dPeso, 3) . ' kg';
        }
        return 'No especificado';
    }

    public function getVolumenAttribute()
    {
        if ($this->dLargo_cm && $this->dAncho_cm && $this->dAlto_cm) {
            return $this->dLargo_cm * $this->dAncho_cm * $this->dAlto_cm;
        }
        return null;
    }

    public function getClaseEnvioFormateadaAttribute()
    {
        switch ($this->vClase_envio) {
            case 'estandar': return 'Estándar';
            case 'express': return 'Express';
            case 'fragil': return 'Frágil';
            case 'grandes_dimensiones': return 'Grandes dimensiones';
            default: return $this->vClase_envio ?: 'No especificada';
        }
    }

    public function getPesoVolumetricoAttribute()
    {
        if ($this->volumen) {
            return $this->volumen / 5000;
        }
        return null;
    }

    public function getPrecioFormateadoAttribute()
    {
        return '$' . number_format($this->precio_actual, 2);
    }

    public function getPrecioFinalFormateadoAttribute()
    {
        return '$' . number_format($this->precio_final, 2);
    }

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