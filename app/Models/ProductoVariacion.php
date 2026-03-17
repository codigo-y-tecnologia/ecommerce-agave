<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProductoVariacion extends Model
{
    use HasFactory;

    protected $table = 'tbl_producto_variaciones';
    protected $primaryKey = 'id_variacion';
    
    public $timestamps = false;

    protected $fillable = [
        'id_producto',
        'vSKU',
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
        'imagen_principal_url',
        'gif_url',
        'imagenes_adicionales_urls',
        'imagenes',
        'primera_imagen',
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
        });

        static::updating(function ($variacion) {
            $variacion->tFecha_actualizacion = now();
        });
        
        static::deleting(function ($variacion) {
            $variacion->eliminarTodasLasImagenes();
            $variacion->atributos()->delete();
            $variacion->favoritos()->delete();
        });
    }

    // ============ MÉTODOS DE DESCUENTO ============

    public function tieneDescuentoActivo()
    {
        if (!$this->bTiene_oferta || $this->dPrecio_oferta === null || $this->dPrecio_oferta <= 0) {
            return false;
        }

        $fechaActual = now()->toDateString();

        if ($this->dFecha_inicio_oferta && $this->dFecha_fin_oferta) {
            return $fechaActual >= $this->dFecha_inicio_oferta && 
                   $fechaActual <= $this->dFecha_fin_oferta;
        }

        if ($this->dFecha_inicio_oferta && !$this->dFecha_fin_oferta) {
            return $fechaActual >= $this->dFecha_inicio_oferta;
        }

        if (!$this->dFecha_inicio_oferta && $this->dFecha_fin_oferta) {
            return $fechaActual <= $this->dFecha_fin_oferta;
        }

        return true;
    }

    public function ofertaVigente()
    {
        return $this->tieneDescuentoActivo();
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
        if ($this->tieneDescuentoActivo() && $this->dPrecio_oferta < $this->dPrecio && $this->dPrecio > 0) {
            $descuento = (($this->dPrecio - $this->dPrecio_oferta) / $this->dPrecio) * 100;
            return round($descuento);
        }
        return 0;
    }

    public function tieneDescuento()
    {
        return $this->tieneDescuentoActivo();
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

    public function imagenesRegistradas()
    {
        return $this->hasMany(VariacionImagen::class, 'id_variacion')->orderBy('iOrden');
    }

    public function favoritos()
    {
        return $this->hasMany(Favorito::class, 'id_variacion');
    }

    // ============ MÉTODOS DE ATRIBUTOS ============

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

    public function getAtributosCompletosTexto()
    {
        $atributos = [];
        foreach ($this->atributos as $atributoRel) {
            if ($atributoRel->atributo && $atributoRel->valor) {
                $atributos[] = $atributoRel->atributo->vNombre . ': ' . $atributoRel->valor->vValor;
            }
        }
        return implode(' | ', $atributos);
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

    // ============ MÉTODOS PARA GUARDAR Y ELIMINAR IMÁGENES ============

    /**
     * Guardar imagen principal de la variación
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
        
        $this->eliminarImagenPrincipal();
        
        $extension = $imagen->getClientOriginalExtension();
        $nombreArchivo = 'principal_' . time() . '_' . uniqid() . '.' . $extension;
        $ruta = $imagen->storeAs($carpeta, $nombreArchivo, 'public');
        
        $imagenRegistrada = VariacionImagen::create([
            'id_variacion' => $this->id_variacion,
            'vRuta' => $ruta,
            'eTipo' => 'principal',
            'iOrden' => 0,
            'bActivo' => true
        ]);
        
        $this->vImagen = $ruta;
        $this->saveQuietly();
        
        Log::info('Imagen principal de variación guardada: ' . $ruta . ' para variación ID: ' . $this->id_variacion);
        
        return $imagenRegistrada->url;
    }

    /**
     * Guardar GIF de la variación
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
        
        $this->eliminarGif();
        
        $extension = $gif->getClientOriginalExtension();
        $nombreArchivo = 'gif_' . time() . '_' . uniqid() . '.' . $extension;
        $ruta = $gif->storeAs($carpeta, $nombreArchivo, 'public');
        
        $imagenRegistrada = VariacionImagen::create([
            'id_variacion' => $this->id_variacion,
            'vRuta' => $ruta,
            'eTipo' => 'gif',
            'iOrden' => 1,
            'bActivo' => true
        ]);
        
        Log::info('GIF de variación guardado: ' . $ruta . ' para variación ID: ' . $this->id_variacion);
        
        return $imagenRegistrada->url;
    }

    /**
     * Guardar múltiples imágenes adicionales para la variación
     *
     * @param array $imagenes Array de objetos UploadedFile
     * @return array URLs de las imágenes guardadas
     */
    public function guardarImagenesAdicionales($imagenes)
    {
    if (!$this->id_variacion) {
        throw new \Exception('No se puede guardar imágenes sin ID de variación');
    }
    
    // Validar que sea un array, si no lo es, convertirlo
    if (!is_array($imagenes)) {
        $imagenes = [$imagenes];
    }
    
    // Si no hay imágenes, retornar vacío
    if (empty($imagenes)) {
        \Log::warning('No hay imágenes para guardar en variación ID: ' . $this->id_variacion);
        return [];
    }

    $carpetaBase = 'variaciones/' . $this->id_variacion;
    $carpetaAdicionales = $carpetaBase . '/adicionales';
    
    // Crear la carpeta si no existe
    if (!Storage::disk('public')->exists($carpetaAdicionales)) {
        Storage::disk('public')->makeDirectory($carpetaAdicionales);
        \Log::info('Carpeta creada: ' . $carpetaAdicionales);
    }
    
    // Obtener el último orden de las imágenes adicionales existentes
    $ultimoOrden = VariacionImagen::where('id_variacion', $this->id_variacion)
        ->where('eTipo', 'adicional')
        ->max('iOrden') ?? 0;
    
    $orden = $ultimoOrden + 1;
    $maxImagenes = 7;
    
    // Contar cuántas imágenes adicionales ya tiene la variación
    $totalActual = VariacionImagen::where('id_variacion', $this->id_variacion)
        ->where('eTipo', 'adicional')
        ->count();
    
    \Log::info('Estado actual - Total imágenes: ' . $totalActual . ', Último orden: ' . $ultimoOrden . ', Nuevas imágenes: ' . count($imagenes));
    
    $imagenesGuardadas = [];
    
    foreach ($imagenes as $index => $imagen) {
        // Verificar que el objeto sea válido
        if (!$imagen || !$imagen->isValid()) {
            \Log::warning('Archivo de imagen inválido en posición ' . $index . ' para variación ID: ' . $this->id_variacion);
            continue;
        }
        
        // Verificar límite de imágenes
        if ($totalActual >= $maxImagenes) {
            \Log::warning('Se alcanzó el límite máximo de ' . $maxImagenes . ' imágenes adicionales para la variación ID: ' . $this->id_variacion);
            break;
        }
        
        try {
            $extension = $imagen->getClientOriginalExtension();
            $nombreArchivo = 'imagen_' . $orden . '_' . time() . '_' . uniqid() . '.' . $extension;
            $ruta = $imagen->storeAs($carpetaAdicionales, $nombreArchivo, 'public');
            
            \Log::info('Archivo guardado en: ' . $ruta);
            
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
            
            \Log::info('Imagen adicional guardada: ' . $ruta . ' para variación ID: ' . $this->id_variacion);
        } catch (\Exception $e) {
            \Log::error('Error al guardar imagen adicional individual: ' . $e->getMessage() . ' para variación ID: ' . $this->id_variacion);
            // Continuar con la siguiente imagen
        }
    }
    
    \Log::info('Proceso completado. Se guardaron ' . count($imagenesGuardadas) . ' de ' . count($imagenes) . ' imágenes para variación ID: ' . $this->id_variacion);
    
    return $imagenesGuardadas;
}

    /**
     * Eliminar imagen principal de la variación
     */
    public function eliminarImagenPrincipal()
    {
        $imagenes = VariacionImagen::where('id_variacion', $this->id_variacion)
            ->where('eTipo', 'principal')
            ->get();
            
        foreach ($imagenes as $img) {
            $img->delete();
        }
        
        if ($this->vImagen) {
            $this->vImagen = null;
            $this->saveQuietly();
        }
    }

    /**
     * Eliminar GIF de la variación
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
    public function eliminarImagenesAdicionalesEspecificas($imagenesAEliminar)
    {
        if (empty($imagenesAEliminar)) {
            return;
        }

        // Si es un string JSON, decodificarlo
        if (is_string($imagenesAEliminar)) {
            $imagenesAEliminar = json_decode($imagenesAEliminar, true);
        }

        foreach ($imagenesAEliminar as $item) {
            // Si tenemos ID de imagen, eliminar por ID
            if (isset($item['id'])) {
                $imagen = VariacionImagen::find($item['id']);
                if ($imagen) {
                    $imagen->delete();
                }
            } 
            // Si solo tenemos nombre de archivo, buscar por ruta
            elseif (isset($item['filename'])) {
                $imagen = VariacionImagen::where('id_variacion', $this->id_variacion)
                    ->where('vRuta', 'like', '%' . $item['filename'])
                    ->first();
                if ($imagen) {
                    $imagen->delete();
                }
            }
        }
    }

    /**
     * Eliminar todas las imágenes de la variación
     */
    public function eliminarTodasLasImagenes()
    {
        VariacionImagen::where('id_variacion', $this->id_variacion)->delete();
        
        $carpeta = 'variaciones/' . $this->id_variacion;
        if (Storage::disk('public')->exists($carpeta)) {
            Storage::disk('public')->deleteDirectory($carpeta);
        }
        
        $this->vImagen = null;
        $this->saveQuietly();
    }

    // ============ ACCESORES PARA IMÁGENES ============

    public function getImagenPrincipalUrlAttribute()
    {
        // Buscar en imágenes registradas
        $imagenPrincipal = $this->imagenesRegistradas()
            ->where('eTipo', 'principal')
            ->where('bActivo', true)
            ->first();
            
        if ($imagenPrincipal) {
            return $imagenPrincipal->url;
        }
        
        // Buscar en campo vImagen
        if ($this->vImagen && Storage::disk('public')->exists($this->vImagen)) {
            return Storage::url($this->vImagen);
        }
        
        // Buscar archivos en la carpeta
        $carpeta = 'variaciones/' . $this->id_variacion;
        if (Storage::disk('public')->exists($carpeta)) {
            $archivos = Storage::disk('public')->files($carpeta);
            foreach ($archivos as $archivo) {
                if (strpos($archivo, 'principal_') !== false) {
                    return Storage::url($archivo);
                }
            }
        }
        
        // Si no hay imagen de variación, usar la del producto padre
        if ($this->productoPadre) {
            return $this->productoPadre->imagen_principal_url;
        }
        
        return null;
    }

    public function getGifUrlAttribute()
    {
        $gif = $this->imagenesRegistradas()
            ->where('eTipo', 'gif')
            ->where('bActivo', true)
            ->first();
            
        if ($gif) {
            return $gif->url;
        }
        
        $carpeta = 'variaciones/' . $this->id_variacion;
        if (Storage::disk('public')->exists($carpeta)) {
            $archivos = Storage::disk('public')->files($carpeta);
            foreach ($archivos as $archivo) {
                if (strpos($archivo, 'gif_') !== false) {
                    return Storage::url($archivo);
                }
            }
        }
        
        if ($this->productoPadre) {
            return $this->productoPadre->gif_url;
        }
        
        return null;
    }

    public function getImagenesAdicionalesUrlsAttribute()
    {
        $imagenes = [];
        
        // Obtener de la relación
        $imagenesAdicionales = $this->imagenesRegistradas()
            ->where('eTipo', 'adicional')
            ->where('bActivo', true)
            ->orderBy('iOrden')
            ->get();
            
        foreach ($imagenesAdicionales as $img) {
            $imagenes[] = $img->url;
        }
        
        // Si no hay en la relación, buscar en la carpeta
        if (empty($imagenes)) {
            $carpetaAdicionales = 'variaciones/' . $this->id_variacion . '/adicionales';
            if (Storage::disk('public')->exists($carpetaAdicionales)) {
                $archivos = Storage::disk('public')->files($carpetaAdicionales);
                sort($archivos);
                
                foreach ($archivos as $archivo) {
                    if (preg_match('/\.(jpg|jpeg|png|webp)$/i', $archivo)) {
                        $imagenes[] = Storage::url($archivo);
                    }
                }
            }
        }
        
        return $imagenes;
    }

    public function getImagenesAttribute()
    {
        $imagenes = [];
        
        $imgPrincipal = $this->imagen_principal_url;
        if ($imgPrincipal) {
            $imagenes[] = $imgPrincipal;
        }
        
        $gif = $this->gif_url;
        if ($gif) {
            $imagenes[] = $gif;
        }
        
        $adicionales = $this->imagenes_adicionales_urls;
        foreach ($adicionales as $url) {
            if (!in_array($url, $imagenes)) {
                $imagenes[] = $url;
            }
        }
        
        return array_values($imagenes);
    }

    public function getPrimeraImagenAttribute()
    {
        $imagenPrincipal = $this->imagen_principal_url;
        if ($imagenPrincipal) {
            return $imagenPrincipal;
        }
        
        $adicionales = $this->imagenes_adicionales_urls;
        if (!empty($adicionales)) {
            return $adicionales[0];
        }
        
        if ($this->productoPadre) {
            return $this->productoPadre->primera_imagen;
        }
        
        return null;
    }

    public function getNumeroImagenesAttribute()
    {
        return VariacionImagen::where('id_variacion', $this->id_variacion)
            ->where('bActivo', true)
            ->count();
    }

    public function puedeAgregarMasImagenes()
    {
        return $this->numero_imagenes < 9; // 1 principal + 1 gif + 7 adicionales = 9 máximo
    }

    // ============ MÉTODOS DE FAVORITOS ============

    public function esFavorito()
    {
        try {
            if (Auth::check()) {
                return $this->favoritos()
                    ->where('id_usuario', Auth::id())
                    ->exists();
            } else {
                return \App\Models\FavoritoTemporal::esFavorito($this->id_producto, $this->id_variacion);
            }
        } catch (\Exception $e) {
            return false;
        }
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