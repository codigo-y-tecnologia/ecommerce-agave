<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Producto extends Model
{
    use HasFactory;
    
    protected $table = 'tbl_productos';
    protected $primaryKey = 'id_producto';
    
    public $timestamps = false;
    
    protected $fillable = [
        'vCodigo_barras',
        'vNombre',
        'tDescripcion_corta',
        'tDescripcion_larga',
        'dPrecio_compra',
        'dPrecio_venta',
        'dPrecio_final',
        'iStock',
        'id_marca',
        'id_categoria',
        'bActivo',
        'dPeso',
        'dLargo_cm',
        'dAncho_cm',
        'dAlto_cm',
        'vClase_envio',
        'vImagen_principal',
        'vGif',
        'vImagenes_adicionales',
        'bTiene_descuento',
        'dPrecio_descuento',
        'dFecha_inicio_descuento',
        'dFecha_fin_descuento',
        'vMotivo_descuento',
        'tFecha_registro',
        'tFecha_actualizacion'
    ];

    protected $casts = [
        'bActivo' => 'boolean',
        'dPrecio_compra' => 'decimal:2',
        'dPrecio_venta' => 'decimal:2',
        'dPrecio_final' => 'decimal:2',
        'iStock' => 'integer',
        'dPeso' => 'decimal:3',
        'dLargo_cm' => 'decimal:2',
        'dAncho_cm' => 'decimal:2',
        'dAlto_cm' => 'decimal:2',
        'vImagenes_adicionales' => 'array',
        'bTiene_descuento' => 'boolean',
        'dPrecio_descuento' => 'decimal:2',
        'tFecha_registro' => 'datetime',
        'tFecha_actualizacion' => 'datetime',
        'dFecha_inicio_descuento' => 'datetime',
        'dFecha_fin_descuento' => 'datetime'
    ];

    protected $appends = [
        'imagen_principal_url',
        'gif_url',
        'imagenes_adicionales_urls',
        'imagenes',
        'primera_imagen',
        'precio_actual',
        'porcentaje_descuento',
        'precio_final',
        'total_impuestos',
        'porcentaje_impuestos',
        'dimensiones_formateadas',
        'peso_formateado',
        'volumen',
        'clase_envio_formateada',
        'peso_volumetrico',
        'precio_formateado',
        'precio_final_formateado',
        'stock_formateado'
    ];

    public function setBTieneDescuentoAttribute($value)
    {
        if ($value === '1' || $value === 1 || $value === true || $value === 'on') {
            $this->attributes['bTiene_descuento'] = true;
        } else {
            $this->attributes['bTiene_descuento'] = false;
        }
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($producto) {
            if (empty($producto->tFecha_registro)) {
                $producto->tFecha_registro = now();
            }
            
            if (is_null($producto->vImagenes_adicionales)) {
                $producto->vImagenes_adicionales = [];
            }
            
            $producto->calcularPrecioFinal();
        });

        static::updating(function ($producto) {
            $producto->tFecha_actualizacion = now();
            $producto->calcularPrecioFinal();
        });

        static::deleting(function ($producto) {
            $producto->favoritos()->delete();
            $producto->eliminarTodasLasImagenes();
            $producto->impuestos()->detach();
        });
    }

    // ============ MÉTODOS DE DESCUENTO ============

    /**
     * Verifica si el descuento está activo en la fecha actual
     */
    public function descuentoVigente(): bool
    {
        if (!$this->bTiene_descuento || $this->dPrecio_descuento === null || $this->dPrecio_descuento <= 0) {
            return false;
        }

        $fechaActual = new \DateTime();
        $fechaActual->setTime(0, 0, 0);
        
        // Caso 1: Tiene ambas fechas
        if ($this->dFecha_inicio_descuento && $this->dFecha_fin_descuento) {
            $fechaInicio = new \DateTime($this->dFecha_inicio_descuento);
            $fechaInicio->setTime(0, 0, 0);
            
            $fechaFin = new \DateTime($this->dFecha_fin_descuento);
            $fechaFin->setTime(23, 59, 59);
            
            $vigente = $fechaActual >= $fechaInicio && $fechaActual <= $fechaFin;
            
            Log::info('Verificando descuento producto', [
                'producto_id' => $this->id_producto,
                'fecha_actual' => $fechaActual->format('Y-m-d H:i:s'),
                'fecha_inicio' => $fechaInicio->format('Y-m-d H:i:s'),
                'fecha_fin' => $fechaFin->format('Y-m-d H:i:s'),
                'resultado' => $vigente
            ]);
            
            return $vigente;
        }

        // Caso 2: Solo tiene fecha de inicio
        if ($this->dFecha_inicio_descuento && !$this->dFecha_fin_descuento) {
            $fechaInicio = new \DateTime($this->dFecha_inicio_descuento);
            $fechaInicio->setTime(0, 0, 0);
            return $fechaActual >= $fechaInicio;
        }

        // Caso 3: Solo tiene fecha de fin
        if (!$this->dFecha_inicio_descuento && $this->dFecha_fin_descuento) {
            $fechaFin = new \DateTime($this->dFecha_fin_descuento);
            $fechaFin->setTime(23, 59, 59);
            return $fechaActual <= $fechaFin;
        }

        // Caso 4: No tiene fechas, el descuento está siempre activo
        return true;
    }

    public function tieneDescuentoActivo()
    {
        return $this->descuentoVigente();
    }

    /**
     * Obtiene el precio base (con descuento si está vigente, sino el normal)
     */
    public function getPrecioBaseAttribute()
    {
        if ($this->descuentoVigente()) {
            return (float)$this->dPrecio_descuento;
        }
        return (float)$this->dPrecio_venta;
    }

    public function getPrecioActualAttribute()
    {
        return $this->getPrecioBaseAttribute();
    }

    public function getPorcentajeDescuentoAttribute()
    {
        if ($this->descuentoVigente() && $this->dPrecio_descuento < $this->dPrecio_venta && $this->dPrecio_venta > 0) {
            $descuento = (($this->dPrecio_venta - $this->dPrecio_descuento) / $this->dPrecio_venta) * 100;
            return round($descuento);
        }
        return 0;
    }

    public function tieneDescuento()
    {
        return $this->descuentoVigente();
    }

    public function porcentajeDescuento()
    {
        return $this->porcentajeDescuento;
    }

    // ============ MÉTODO DE CÁLCULO DE PRECIO FINAL ============

    /**
     * Calcula el precio final EN TIEMPO REAL basado en la fecha actual
     */
    public function getPrecioFinalAttribute()
    {
        $precioBase = $this->getPrecioBaseAttribute();
        
        $totalImpuestos = 0;
        
        foreach ($this->impuestosActivos as $impuesto) {
            $totalImpuestos += $precioBase * ($impuesto->dPorcentaje / 100);
        }
        
        return $precioBase + $totalImpuestos;
    }
    
    /**
     * Obtiene el total de impuestos sobre el precio base actual
     */
    public function getTotalImpuestosAttribute()
    {
        $precioBase = $this->getPrecioBaseAttribute();
        $total = 0;
        
        foreach ($this->impuestosActivos as $impuesto) {
            $total += $precioBase * ($impuesto->dPorcentaje / 100);
        }
        
        return $total;
    }
    
    public function getPorcentajeImpuestosAttribute()
    {
        $total = 0;
        
        foreach ($this->impuestosActivos as $impuesto) {
            $total += $impuesto->dPorcentaje;
        }
        
        return $total;
    }

    /**
     * Método para recalcular y guardar dPrecio_final (solo para respaldo)
     */
    public function calcularPrecioFinal()
    {
        $precioBase = $this->getPrecioBaseAttribute();
        
        Log::info('Calculando precio final para guardar', [
            'producto_id' => $this->id_producto,
            'bTiene_descuento' => $this->bTiene_descuento,
            'dPrecio_descuento' => $this->dPrecio_descuento,
            'dPrecio_venta' => $this->dPrecio_venta,
            'descuento_vigente' => $this->descuentoVigente()
        ]);
        
        $totalImpuestos = 0;
        
        if ($this->exists && $this->id_producto) {
            $impuestos = $this->impuestos()->where('bActivo', true)->get();
            
            foreach ($impuestos as $impuesto) {
                $totalImpuestos += $precioBase * ($impuesto->dPorcentaje / 100);
            }
        }
        
        $this->attributes['dPrecio_final'] = $precioBase + $totalImpuestos;
        
        return $this->attributes['dPrecio_final'];
    }

    public function recalcularPrecioFinal()
    {
        $this->calcularPrecioFinal();
        $this->save();
    }

    public function getPrecioFinalFormateadoAttribute()
    {
        if ($this->precio_final) {
            return '$' . number_format($this->precio_final, 2);
        }
        return '$0.00';
    }

    public function getDetalleImpuestosAttribute()
    {
        $detalle = [];
        $precioBase = $this->getPrecioBaseAttribute();
        
        foreach ($this->impuestosActivos as $impuesto) {
            $monto = $precioBase * ($impuesto->dPorcentaje / 100);
            $detalle[] = [
                'nombre' => $impuesto->vNombre,
                'tipo' => $impuesto->eTipo,
                'porcentaje' => $impuesto->dPorcentaje,
                'monto' => $monto,
                'monto_formateado' => '$' . number_format($monto, 2)
            ];
        }
        
        return $detalle;
    }

    // ============ ACCESORES PARA IMÁGENES ============
    
    public function getImagenPrincipalUrlAttribute()
    {
        if ($this->vImagen_principal) {
            if (Storage::disk('public')->exists($this->vImagen_principal)) {
                return Storage::url($this->vImagen_principal);
            }
        }
        
        $carpeta = 'products/' . $this->id_producto;
        if (Storage::disk('public')->exists($carpeta)) {
            $archivos = Storage::disk('public')->files($carpeta);
            foreach ($archivos as $archivo) {
                if (strpos($archivo, 'principal_') !== false) {
                    $this->vImagen_principal = $archivo;
                    $this->save();
                    return Storage::url($archivo);
                }
            }
        }
        
        return null;
    }

    public function getGifUrlAttribute()
    {
        if ($this->vGif) {
            if (Storage::disk('public')->exists($this->vGif)) {
                return Storage::url($this->vGif);
            }
        }
        
        $carpeta = 'products/' . $this->id_producto;
        if (Storage::disk('public')->exists($carpeta)) {
            $archivos = Storage::disk('public')->files($carpeta);
            foreach ($archivos as $archivo) {
                if (strpos($archivo, 'gif_') !== false) {
                    $this->vGif = $archivo;
                    $this->save();
                    return Storage::url($archivo);
                }
            }
        }
        
        return null;
    }

    public function getImagenesAdicionalesUrlsAttribute()
    {
        $imagenes = [];
        
        if ($this->vImagenes_adicionales && is_array($this->vImagenes_adicionales)) {
            foreach ($this->vImagenes_adicionales as $ruta) {
                if (Storage::disk('public')->exists($ruta)) {
                    $imagenes[] = Storage::url($ruta);
                }
            }
            return $imagenes;
        }
        
        $carpetaAdicionales = 'products/' . $this->id_producto . '/adicionales';
        if (Storage::disk('public')->exists($carpetaAdicionales)) {
            $archivos = Storage::disk('public')->files($carpetaAdicionales);
            sort($archivos);
            
            $rutasGuardar = [];
            foreach ($archivos as $archivo) {
                if (preg_match('/\.(jpg|jpeg|png|webp)$/i', $archivo)) {
                    $rutasGuardar[] = $archivo;
                    $imagenes[] = Storage::url($archivo);
                }
            }
            
            if (!empty($rutasGuardar)) {
                $this->vImagenes_adicionales = $rutasGuardar;
                $this->save();
            }
        }
        
        return $imagenes;
    }

    public function getImagenesAttribute()
    {
        $imagenes = [];
        
        $imagenPrincipal = $this->imagen_principal_url;
        if ($imagenPrincipal) {
            $imagenes[] = $imagenPrincipal;
        }
        
        $gifUrl = $this->gif_url;
        if ($gifUrl) {
            $imagenes[] = $gifUrl;
        }
        
        $adicionales = $this->imagenes_adicionales_urls;
        foreach ($adicionales as $url) {
            $imagenes[] = $url;
        }
        
        return $imagenes;
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
        
        return null;
    }

    // ============ MÉTODOS PARA GUARDAR Y ELIMINAR IMÁGENES ============
    
    public function guardarImagenPrincipal($imagen)
    {
        if (!$this->id_producto) {
            throw new \Exception('No se puede guardar imagen sin ID de producto');
        }
        
        $carpeta = 'products/' . $this->id_producto;
        
        if (!Storage::disk('public')->exists($carpeta)) {
            Storage::disk('public')->makeDirectory($carpeta);
        }
        
        $this->eliminarImagenPrincipal();
        
        $extension = $imagen->getClientOriginalExtension();
        $nombreArchivo = 'principal_' . time() . '_' . uniqid() . '.' . $extension;
        $ruta = $imagen->storeAs($carpeta, $nombreArchivo, 'public');
        
        $this->vImagen_principal = $ruta;
        $this->save();
        
        return Storage::url($ruta);
    }

    public function guardarGif($gif)
    {
        if (!$this->id_producto) {
            throw new \Exception('No se puede guardar GIF sin ID de producto');
        }
        
        $carpeta = 'products/' . $this->id_producto;
        
        if (!Storage::disk('public')->exists($carpeta)) {
            Storage::disk('public')->makeDirectory($carpeta);
        }
        
        $this->eliminarGif();
        
        $extension = $gif->getClientOriginalExtension();
        $nombreArchivo = 'gif_' . time() . '_' . uniqid() . '.' . $extension;
        $ruta = $gif->storeAs($carpeta, $nombreArchivo, 'public');
        
        $this->vGif = $ruta;
        $this->save();
        
        return Storage::url($ruta);
    }

    public function guardarImagenesAdicionales($imagenes)
    {
        if (!$this->id_producto) {
            throw new \Exception('No se puede guardar imágenes sin ID de producto');
        }
        
        if (!is_array($imagenes)) {
            $imagenes = [$imagenes];
        }
        
        if (empty($imagenes)) {
            return [];
        }

        $carpetaBase = 'products/' . $this->id_producto;
        $carpetaAdicionales = $carpetaBase . '/adicionales';
        
        if (!Storage::disk('public')->exists($carpetaAdicionales)) {
            Storage::disk('public')->makeDirectory($carpetaAdicionales);
        }
        
        $imagenesExistentes = $this->vImagenes_adicionales ?? [];
        if (!is_array($imagenesExistentes)) {
            $imagenesExistentes = [];
        }
        
        $maxImagenes = 7;
        $contador = count($imagenesExistentes);
        $nuevasRutas = [];
        
        foreach ($imagenes as $index => $imagen) {
            if ($contador >= $maxImagenes) {
                break;
            }
            
            if (!$imagen || !$imagen->isValid()) {
                continue;
            }
            
            try {
                $extension = $imagen->getClientOriginalExtension();
                $nombreArchivo = 'imagen_' . ($contador + 1) . '_' . time() . '_' . uniqid() . '.' . $extension;
                $ruta = $imagen->storeAs($carpetaAdicionales, $nombreArchivo, 'public');
                
                $nuevasRutas[] = $ruta;
                $contador++;
                
            } catch (\Exception $e) {
                Log::error('Error al guardar imagen adicional: ' . $e->getMessage());
            }
        }
        
        $todasLasImagenes = array_merge($imagenesExistentes, $nuevasRutas);
        
        $this->vImagenes_adicionales = $todasLasImagenes;
        $this->save();
        
        return array_map(function($ruta) {
            return Storage::url($ruta);
        }, $nuevasRutas);
    }

    public function eliminarImagenesAdicionalesEspecificas($imagenesAEliminar)
    {
        Log::info('=== INICIO eliminarImagenesAdicionalesEspecificas ===');
        Log::info('Producto ID: ' . $this->id_producto);
        
        if (is_string($imagenesAEliminar)) {
            $imagenesAEliminar = json_decode($imagenesAEliminar, true);
        }
        
        if (!is_array($imagenesAEliminar) || empty($imagenesAEliminar)) {
            return true;
        }
        
        $imagenesActuales = $this->vImagenes_adicionales;
        
        if (!is_array($imagenesActuales)) {
            $imagenesActuales = [];
        }
        
        if (empty($imagenesActuales)) {
            return true;
        }
        
        $nuevasImagenes = [];
        
        foreach ($imagenesActuales as $index => $ruta) {
            $debeEliminar = false;
            $nombreArchivo = basename($ruta);
            
            foreach ($imagenesAEliminar as $item) {
                if (is_numeric($item) && (int)$item === $index) {
                    $debeEliminar = true;
                    break;
                }
                
                if (is_string($item) && ($item === $nombreArchivo || strpos($ruta, $item) !== false)) {
                    $debeEliminar = true;
                    break;
                }
                
                if (is_array($item)) {
                    if (isset($item['filename']) && $item['filename'] === $nombreArchivo) {
                        $debeEliminar = true;
                        break;
                    }
                    if (isset($item['id']) && is_numeric($item['id']) && (int)$item['id'] === $index) {
                        $debeEliminar = true;
                        break;
                    }
                }
            }
            
            if ($debeEliminar) {
                if (Storage::disk('public')->exists($ruta)) {
                    Storage::disk('public')->delete($ruta);
                }
            } else {
                $nuevasImagenes[] = $ruta;
            }
        }
        
        $this->vImagenes_adicionales = $nuevasImagenes;
        $this->save();
        
        return true;
    }

    public function eliminarImagenPrincipal()
    {
        if ($this->vImagen_principal) {
            if (Storage::disk('public')->exists($this->vImagen_principal)) {
                Storage::disk('public')->delete($this->vImagen_principal);
            }
            $this->vImagen_principal = null;
            $this->save();
        }
        
        $carpeta = 'products/' . $this->id_producto;
        if (Storage::disk('public')->exists($carpeta)) {
            $archivos = Storage::disk('public')->files($carpeta);
            foreach ($archivos as $archivo) {
                if (strpos($archivo, 'principal_') !== false) {
                    Storage::disk('public')->delete($archivo);
                }
            }
        }
    }

    public function eliminarGif()
    {
        if ($this->vGif) {
            if (Storage::disk('public')->exists($this->vGif)) {
                Storage::disk('public')->delete($this->vGif);
            }
            $this->vGif = null;
            $this->save();
        }
        
        $carpeta = 'products/' . $this->id_producto;
        if (Storage::disk('public')->exists($carpeta)) {
            $archivos = Storage::disk('public')->files($carpeta);
            foreach ($archivos as $archivo) {
                if (strpos($archivo, 'gif_') !== false) {
                    Storage::disk('public')->delete($archivo);
                }
            }
        }
    }

    public function eliminarTodasLasImagenes()
    {
        $carpeta = 'products/' . $this->id_producto;
        if (Storage::disk('public')->exists($carpeta)) {
            Storage::disk('public')->deleteDirectory($carpeta);
        }
        
        $this->vImagen_principal = null;
        $this->vGif = null;
        $this->vImagenes_adicionales = [];
        $this->save();
    }

    public function getNombresArchivosImagenesAdicionales()
    {
        $nombres = [];
        $imagenes = $this->vImagenes_adicionales ?? [];
        
        foreach ($imagenes as $ruta) {
            $nombres[] = basename($ruta);
        }
        
        return $nombres;
    }

    public function getNumeroImagenes()
    {
        $total = 0;
        if ($this->vImagen_principal) $total++;
        if ($this->vGif) $total++;
        
        $adicionales = $this->vImagenes_adicionales;
        if (is_string($adicionales)) {
            $adicionales = json_decode($adicionales, true);
        }
        if (is_array($adicionales)) {
            $total += count($adicionales);
        }
        return $total;
    }

    public function puedeAgregarMasImagenes()
    {
        return $this->getNumeroImagenes() < 8;
    }

    // ============ RELACIONES ============

    public function marca()
    {
        return $this->belongsTo(Marca::class, 'id_marca');
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria');
    }

    public function etiquetas()
    {
        return $this->belongsToMany(Etiqueta::class, 'tbl_producto_etiquetas', 'id_producto', 'id_etiqueta');
    }
    
    public function atributos()
    {
        return $this->belongsToMany(Atributo::class, 'tbl_producto_atributos', 'id_producto', 'id_atributo')
                    ->withPivot(['id_atributo_valor', 'dPrecio_extra']);
    }

    public function valoresAtributos()
    {
        return $this->belongsToMany(AtributoValor::class, 'tbl_producto_atributos', 'id_producto', 'id_atributo_valor')
                    ->withPivot('dPrecio_extra')
                    ->with('atributo');
    }

    public function impuestos()
    {
        return $this->belongsToMany(Impuesto::class, 'tbl_producto_impuestos', 'id_producto', 'id_impuesto');
    }
    
    public function getImpuestosActivosAttribute()
    {
        return $this->impuestos()->where('bActivo', true)->get();
    }

    public function favoritos()
    {
        return $this->hasMany(Favorito::class, 'id_producto')->whereNull('id_variacion');
    }

    public function variaciones()
    {
        return $this->hasMany(ProductoVariacion::class, 'id_producto');
    }

    public function variacionesActivas()
    {
        return $this->hasMany(ProductoVariacion::class, 'id_producto')->where('bActivo', true);
    }

    // ============ MÉTODOS DE UTILIDAD ============

    public function getAtributosAgrupadosAttribute()
    {
        $atributos = [];
        
        foreach ($this->valoresAtributos as $valor) {
            $atributo = $valor->atributo;
            if (!isset($atributos[$atributo->id_atributo])) {
                $atributos[$atributo->id_atributo] = [
                    'id_atributo' => $atributo->id_atributo,
                    'nombre' => $atributo->vNombre,
                    'valores' => []
                ];
            }
            
            $atributos[$atributo->id_atributo]['valores'][] = [
                'id_valor' => $valor->id_atributo_valor,
                'valor' => $valor->vValor,
                'precio_extra' => $valor->pivot->dPrecio_extra
            ];
        }
        
        return $atributos;
    }

    public function tieneAtributos()
    {
        return $this->valoresAtributos()->count() > 0;
    }

    public function esFavorito()
    {
        try {
            if (Auth::check()) {
                return $this->favoritos()
                    ->where('id_usuario', Auth::id())
                    ->whereNull('id_variacion')
                    ->exists();
            } else {
                return \App\Models\FavoritoTemporal::esFavorito($this->id_producto, null);
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    public function estaBajoEnStock()
    {
        return $this->iStock > 0 && $this->iStock <= 5;
    }

    public function tieneVariaciones()
    {
        return $this->variaciones()->count() > 0;
    }

    // ============ MÉTODOS DE PRECIOS Y STOCK ============

    public function getRangoPreciosAttribute()
    {
        if ($this->tieneVariaciones()) {
            $precioMin = $this->variacionesActivas()->min('dPrecio');
            $precioMax = $this->variacionesActivas()->max('dPrecio');
            
            if ($precioMin == $precioMax) {
                return '$' . number_format($precioMin, 2);
            }
            return '$' . number_format($precioMin, 2) . ' - $' . number_format($precioMax, 2);
        }
        
        return '$' . number_format($this->dPrecio_venta, 2);
    }

    public function getPrecioMostrarAttribute()
    {
        if ($this->tieneVariaciones()) {
            $precioMin = $this->variacionesActivas()->min('dPrecio');
            return $precioMin ?: $this->attributes['dPrecio_venta'];
        }
        
        return $this->attributes['dPrecio_venta'];
    }

    public function getIStockAttribute()
    {
        return $this->attributes['iStock'];
    }

    public function getStockTotalAttribute()
    {
        if ($this->tieneVariaciones()) {
            return $this->variacionesActivas()->sum('iStock');
        }
        
        return $this->attributes['iStock'];
    }

    public function getPrecioMinimoAttribute()
    {
        if ($this->tieneVariaciones()) {
            return $this->variacionesActivas()->min('dPrecio');
        }
        
        return $this->attributes['dPrecio_venta'];
    }

    public function getPrecioMaximoAttribute()
    {
        if ($this->tieneVariaciones()) {
            return $this->variacionesActivas()->max('dPrecio');
        }
        
        return $this->attributes['dPrecio_venta'];
    }

    public function getPrecioFormateadoAttribute()
    {
        return '$' . number_format($this->precio_actual, 2);
    }

    public function getStockFormateadoAttribute()
    {
        if ($this->stock_total > 10) {
            return '<span class="text-success">' . $this->stock_total . ' unidades</span>';
        } elseif ($this->stock_total > 0) {
            return '<span class="text-warning">' . $this->stock_total . ' unidades (bajo stock)</span>';
        } else {
            return '<span class="text-danger">Sin stock</span>';
        }
    }

    // ============ MÉTODOS DE DIMENSIONES Y ENVÍO ============

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

    public function tieneDimensionesCompletas()
    {
        return $this->dLargo_cm && $this->dAncho_cm && $this->dAlto_cm;
    }

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

    public function getPesoVolumetricoAttribute()
    {
        if ($this->volumen) {
            return $this->volumen / 5000;
        }
        return null;
    }

    public function getPesoFacturableAttribute()
    {
        $pesoReal = $this->dPeso ?: 0;
        $pesoVolumetrico = $this->peso_volumetrico ?: 0;
        
        return max($pesoReal, $pesoVolumetrico);
    }

    // ============ SCOPES ============

    public static function buscarPorNombreOSKU($busqueda)
    {
        return self::where(function($query) use ($busqueda) {
            $query->where('vNombre', 'LIKE', '%' . $busqueda . '%')
                  ->orWhere('vCodigo_barras', 'LIKE', '%' . $busqueda . '%');
        })->where('bActivo', true);
    }

    public function scopeConDimensiones($query)
    {
        return $query->whereNotNull('dLargo_cm')
                     ->whereNotNull('dAncho_cm')
                     ->whereNotNull('dAlto_cm');
    }

    public function scopeConPeso($query)
    {
        return $query->whereNotNull('dPeso')
                     ->where('dPeso', '>', 0);
    }

    public function scopePorClaseEnvio($query, $clase)
    {
        return $query->where('vClase_envio', $clase);
    }
}