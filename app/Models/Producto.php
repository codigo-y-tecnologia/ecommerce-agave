<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        'bTiene_oferta',
        'dPrecio_oferta',
        'dFecha_inicio_oferta',
        'dFecha_fin_oferta',
        'vMotivo_oferta',
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
        'bTiene_oferta' => 'boolean',
        'dPrecio_oferta' => 'decimal:2',
        'tFecha_registro' => 'datetime',
        'tFecha_actualizacion' => 'datetime',
        'dFecha_inicio_oferta' => 'date',
        'dFecha_fin_oferta' => 'date'
    ];

    // Mutador para bTiene_oferta
    public function setBTieneOfertaAttribute($value)
    {
        if ($value === '1' || $value === 1 || $value === true || $value === 'on') {
            $this->attributes['bTiene_oferta'] = true;
        } else {
            $this->attributes['bTiene_oferta'] = false;
        }
    }

    // Boot method para manejar eventos
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

    /**
     * Calcular el precio final con impuestos incluidos
     */
    public function calcularPrecioFinal()
    {
        $precioBase = $this->attributes['dPrecio_venta'] ?? 0;
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

    /**
     * Recalcular precio final después de sincronizar impuestos
     */
    public function recalcularPrecioFinal()
    {
        $this->calcularPrecioFinal();
        $this->saveQuietly();
    }

    // ============ ACCESORES PARA IMÁGENES ============

    /**
     * Accesor para imagen principal
     */
    public function getImagenPrincipalAttribute()
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
                    $this->saveQuietly();
                    return Storage::url($archivo);
                }
            }
        }
        
        return null;
    }

    /**
     * Accesor para URL de GIF
     */
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
                    $this->saveQuietly();
                    return Storage::url($archivo);
                }
            }
        }
        
        return null;
    }

    /**
     * Accesor para imágenes adicionales
     */
    public function getImagenesAdicionalesAttribute()
    {
        $imagenes = [];
        
        if ($this->vImagenes_adicionales && is_array($this->vImagenes_adicionales)) {
            foreach ($this->vImagenes_adicionales as $ruta) {
                if (Storage::disk('public')->exists($ruta)) {
                    $imagenes[] = Storage::url($ruta);
                }
            }
            if (!empty($imagenes)) {
                return $imagenes;
            }
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
                $this->saveQuietly();
            }
        }
        
        return $imagenes;
    }

    /**
     * Método PRINCIPAL para obtener TODAS las imágenes
     */
    public function getImagenesAttribute()
    {
        $imagenes = [];
        
        $imagenPrincipal = $this->imagen_principal;
        if ($imagenPrincipal) {
            $imagenes[] = $imagenPrincipal;
        }
        
        $gifUrl = $this->gif_url;
        if ($gifUrl) {
            $imagenes[] = $gifUrl;
        }
        
        $adicionales = $this->imagenes_adicionales;
        foreach ($adicionales as $url) {
            $imagenes[] = $url;
        }
        
        return $imagenes;
    }

    // ============ MÉTODOS PARA GUARDAR Y ELIMINAR IMÁGENES ============

    /**
     * Guardar imagen principal del producto
     */
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
        $this->saveQuietly();
        
        \Log::info('Imagen principal guardada en BD: ' . $ruta);
        
        return Storage::url($ruta);
    }

    /**
     * Guardar GIF del producto
     */
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
        $this->saveQuietly();
        
        \Log::info('GIF guardado en BD: ' . $ruta);
        
        return Storage::url($ruta);
    }

    /**
     * Guardar imágenes adicionales del producto
     */
    public function guardarImagenesAdicionales($imagenes)
    {
        if (!$this->id_producto) {
            throw new \Exception('No se puede guardar imágenes sin ID de producto');
        }
        
        $carpeta = 'products/' . $this->id_producto . '/adicionales';
        
        if (!Storage::disk('public')->exists($carpeta)) {
            Storage::disk('public')->makeDirectory($carpeta);
        }
        
        $imagenesExistentes = $this->vImagenes_adicionales ?? [];
        $numeroInicio = count($imagenesExistentes);
        
        $nuevasRutas = [];
        $contador = 0;
        $maxImagenes = 7;
        
        if (!is_array($imagenes)) {
            $imagenes = [$imagenes];
        }
        
        foreach ($imagenes as $imagen) {
            if (($numeroInicio + $contador) >= $maxImagenes) {
                break;
            }
            
            $extension = $imagen->getClientOriginalExtension();
            $nombreArchivo = 'imagen_' . ($numeroInicio + $contador + 1) . '_' . time() . '_' . uniqid() . '.' . $extension;
            $ruta = $imagen->storeAs($carpeta, $nombreArchivo, 'public');
            $nuevasRutas[] = $ruta;
            $contador++;
        }
        
        $this->vImagenes_adicionales = array_merge($imagenesExistentes, $nuevasRutas);
        $this->saveQuietly();
        
        \Log::info('Imágenes adicionales guardadas en BD: ' . count($nuevasRutas));
        
        return array_map(function($ruta) {
            return Storage::url($ruta);
        }, $nuevasRutas);
    }

    /**
     * Eliminar imagen principal
     */
    public function eliminarImagenPrincipal()
    {
        if ($this->vImagen_principal) {
            if (Storage::disk('public')->exists($this->vImagen_principal)) {
                Storage::disk('public')->delete($this->vImagen_principal);
            }
            $this->vImagen_principal = null;
            $this->saveQuietly();
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

    /**
     * Eliminar GIF
     */
    public function eliminarGif()
    {
        if ($this->vGif) {
            if (Storage::disk('public')->exists($this->vGif)) {
                Storage::disk('public')->delete($this->vGif);
            }
            $this->vGif = null;
            $this->saveQuietly();
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

    /**
     * Eliminar imágenes adicionales específicas
     */
    public function eliminarImagenesAdicionalesEspecificas($nombresArchivos)
    {
        $carpeta = 'products/' . $this->id_producto . '/adicionales';
        $imagenesActuales = $this->vImagenes_adicionales ?? [];
        
        $nuevasImagenes = [];
        foreach ($imagenesActuales as $ruta) {
            $nombreArchivo = basename($ruta);
            if (!in_array($nombreArchivo, $nombresArchivos)) {
                $nuevasImagenes[] = $ruta;
            } else {
                if (Storage::disk('public')->exists($ruta)) {
                    Storage::disk('public')->delete($ruta);
                }
            }
        }
        
        $this->vImagenes_adicionales = $nuevasImagenes;
        $this->saveQuietly();
        
        foreach ($nombresArchivos as $nombreArchivo) {
            $rutaCompleta = $carpeta . '/' . $nombreArchivo;
            if (Storage::disk('public')->exists($rutaCompleta)) {
                Storage::disk('public')->delete($rutaCompleta);
            }
        }
    }

    /**
     * Eliminar todas las imágenes
     */
    public function eliminarTodasLasImagenes()
    {
        $carpeta = 'products/' . $this->id_producto;
        if (Storage::disk('public')->exists($carpeta)) {
            Storage::disk('public')->deleteDirectory($carpeta);
        }
        
        $this->vImagen_principal = null;
        $this->vGif = null;
        $this->vImagenes_adicionales = [];
        $this->saveQuietly();
    }

    /**
     * Obtener nombres de archivos de imágenes adicionales
     */
    public function getNombresArchivosImagenesAdicionales()
    {
        $nombres = [];
        $imagenes = $this->vImagenes_adicionales ?? [];
        
        foreach ($imagenes as $ruta) {
            $nombres[] = basename($ruta);
        }
        
        return $nombres;
    }

    /**
     * Número de imágenes
     */
    public function getNumeroImagenes()
    {
        $total = 0;
        if ($this->vImagen_principal) $total++;
        if ($this->vGif) $total++;
        $total += count($this->vImagenes_adicionales ?? []);
        return $total;
    }

    /**
     * Verificar si tiene espacio para más imágenes
     */
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
                    ->withPivot(['id_atributo', 'dPrecio_extra']);
    }

    /**
     * Relación con impuestos (muchos a muchos)
     */
    public function impuestos()
    {
        return $this->belongsToMany(Impuesto::class, 'tbl_producto_impuestos', 'id_producto', 'id_impuesto');
    }

    /**
     * Relación con favoritos - solo productos sin variación
     */
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

    /**
     * Obtener impuestos activos del producto
     */
    public function getImpuestosActivosAttribute()
    {
        return $this->impuestos()->where('bActivo', true)->get();
    }

    /**
     * Calcular el total de impuestos aplicados al producto
     */
    public function getTotalImpuestosAttribute()
    {
        $precioBase = $this->attributes['dPrecio_venta'] ?? 0;
        $total = 0;
        
        foreach ($this->impuestosActivos as $impuesto) {
            $total += $precioBase * ($impuesto->dPorcentaje / 100);
        }
        
        return $total;
    }

    /**
     * Obtener el porcentaje total de impuestos
     */
    public function getPorcentajeImpuestosAttribute()
    {
        $total = 0;
        
        foreach ($this->impuestosActivos as $impuesto) {
            $total += $impuesto->dPorcentaje;
        }
        
        return $total;
    }

    /**
     * Formatear el precio final para mostrar
     */
    public function getPrecioFinalFormateadoAttribute()
    {
        if ($this->dPrecio_final) {
            return '$' . number_format($this->dPrecio_final, 2);
        }
        return '$0.00';
    }

    /**
     * Formatear el detalle de impuestos
     */
    public function getDetalleImpuestosAttribute()
    {
        $detalle = [];
        
        foreach ($this->impuestosActivos as $impuesto) {
            $monto = $this->dPrecio_venta * ($impuesto->dPorcentaje / 100);
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

    /**
     * Método para obtener atributos agrupados
     */
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
                'precio_extra' => $valor->pivot->dPrecio_extra,
                'stock' => $valor->iStock
            ];
        }
        
        return $atributos;
    }

    /**
     * Método para verificar si tiene atributos
     */
    public function tieneAtributos()
    {
        return $this->valoresAtributos()->count() > 0;
    }

    /**
     * Verificar si el producto es favorito del usuario actual (SIN variación)
     */
    public function esFavorito()
    {
        try {
            if (!Auth::check()) {
                return false;
            }

            return $this->favoritos()
                ->where('id_usuario', Auth::id())
                ->whereNull('id_variacion')
                ->exists();
                 
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Verificar si está bajo en stock
     */
    public function estaBajoEnStock()
    {
        return $this->iStock > 0 && $this->iStock <= 5;
    }

    /**
     * Verificar si tiene variaciones
     */
    public function tieneVariaciones()
    {
        return $this->variaciones()->count() > 0;
    }

    // ============ MÉTODOS DE OFERTA/DESCUENTO ============

    /**
     * Verificar si la oferta está vigente
     */
    public function ofertaVigente(): bool
    {
        // Si no tiene descuento activado o no tiene precio de descuento o el precio es 0 o negativo
        if (!$this->bTiene_oferta || $this->dPrecio_oferta === null || $this->dPrecio_oferta <= 0) {
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

        // Caso 4: Tiene descuento activado pero sin fechas (descuento permanente)
        return true;
    }

    /**
     * Verificar si el producto tiene descuento activo
     */
    public function tieneDescuentoActivo()
    {
        return $this->ofertaVigente();
    }

    /**
     * Obtener el precio de oferta si está vigente
     */
    public function getPrecioOfertaVigenteAttribute()
    {
        return $this->ofertaVigente() ? $this->dPrecio_oferta : $this->dPrecio_venta;
    }

    /**
     * Obtener porcentaje de descuento
     */
    public function getPorcentajeDescuentoAttribute()
    {
        if ($this->ofertaVigente() && $this->dPrecio_oferta < $this->dPrecio_venta && $this->dPrecio_venta > 0) {
            $descuento = (($this->dPrecio_venta - $this->dPrecio_oferta) / $this->dPrecio_venta) * 100;
            return round($descuento);
        }
        return 0;
    }

    /**
     * Badge para oferta
     */
    public function getOfertaBadgeAttribute()
    {
        if ($this->ofertaVigente()) {
            return '<span class="badge bg-danger">-' . $this->porcentajeDescuento . '%</span>';
        }
        return '';
    }

    /**
     * Verificar si tiene descuento (sin alias)
     */
    public function tieneDescuento()
    {
        return $this->ofertaVigente();
    }

    /**
     * Calcular porcentaje de descuento (método adicional)
     */
    public function porcentajeDescuento()
    {
        return $this->porcentajeDescuento;
    }

    // ============ MÉTODOS DE PRECIOS Y STOCK ============

    /**
     * Accesor para precio de venta
     */
    public function getDPrecioVentaAttribute()
    {
        return $this->attributes['dPrecio_venta'];
    }

    /**
     * Mostrar rango de precios cuando hay variaciones
     */
    public function getRangoPreciosAttribute()
    {
        if ($this->tieneVariaciones()) {
            $precioMin = $this->variacionesActivas()->min('dPrecio');
            $precioMax = $this->variacionesActivas()->max('dPrecio');
            
            if ($precioMin == $precioMax) {
                return number_format($precioMin, 2);
            }
            return number_format($precioMin, 2) . ' - ' . number_format($precioMax, 2);
        }
        
        return number_format($this->dPrecio_venta, 2);
    }

    /**
     * Obtener el precio a mostrar
     */
    public function getPrecioMostrarAttribute()
    {
        if ($this->tieneVariaciones()) {
            $precioMin = $this->variacionesActivas()->min('dPrecio');
            return $precioMin ?: $this->attributes['dPrecio_venta'];
        }
        
        return $this->attributes['dPrecio_venta'];
    }

    /**
     * Devuelve el stock real del producto padre
     */
    public function getIStockAttribute()
    {
        return $this->attributes['iStock'];
    }

    /**
     * Obtener el stock total incluyendo variaciones
     */
    public function getStockTotalAttribute()
    {
        if ($this->tieneVariaciones()) {
            return $this->variacionesActivas()->sum('iStock');
        }
        
        return $this->attributes['iStock'];
    }

    /**
     * Obtener el precio mínimo
     */
    public function getPrecioMinimoAttribute()
    {
        if ($this->tieneVariaciones()) {
            return $this->variacionesActivas()->min('dPrecio');
        }
        
        return $this->attributes['dPrecio_venta'];
    }

    /**
     * Obtener el precio máximo
     */
    public function getPrecioMaximoAttribute()
    {
        if ($this->tieneVariaciones()) {
            return $this->variacionesActivas()->max('dPrecio');
        }
        
        return $this->attributes['dPrecio_venta'];
    }

    // ============ MÉTODOS DE DIMENSIONES Y ENVÍO ============

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
     * Accesor para volumen formateado
     */
    public function getVolumenFormateadoAttribute()
    {
        if ($this->volumen) {
            return number_format($this->volumen, 2) . ' cm³';
        }
        return 'No calculable';
    }

    /**
     * Verificar si tiene dimensiones completas
     */
    public function tieneDimensionesCompletas()
    {
        return $this->dLargo_cm && $this->dAncho_cm && $this->dAlto_cm;
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
     * Accesor para badge de clase de envío
     */
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
     * Accesor para peso volumétrico formateado
     */
    public function getPesoVolumetricoFormateadoAttribute()
    {
        if ($this->peso_volumetrico) {
            return number_format($this->peso_volumetrico, 3) . ' kg';
        }
        return 'No calculable';
    }

    /**
     * Determinar el peso facturable
     */
    public function getPesoFacturableAttribute()
    {
        $pesoReal = $this->dPeso ?: 0;
        $pesoVolumetrico = $this->peso_volumetrico ?: 0;
        
        return max($pesoReal, $pesoVolumetrico);
    }

    // ============ SCOPES ============

    /**
     * Buscar productos por nombre o SKU
     */
    public static function buscarPorNombreOSKU($busqueda)
    {
        return self::where(function($query) use ($busqueda) {
            $query->where('vNombre', 'LIKE', '%' . $busqueda . '%')
                  ->orWhere('vCodigo_barras', 'LIKE', '%' . $busqueda . '%');
        })->where('bActivo', true);
    }

    /**
     * Scope para productos con dimensiones
     */
    public function scopeConDimensiones($query)
    {
        return $query->whereNotNull('dLargo_cm')
                     ->whereNotNull('dAncho_cm')
                     ->whereNotNull('dAlto_cm');
    }

    /**
     * Scope para productos con peso
     */
    public function scopeConPeso($query)
    {
        return $query->whereNotNull('dPeso')
                     ->where('dPeso', '>', 0);
    }

    /**
     * Scope para productos por clase de envío
     */
    public function scopePorClaseEnvio($query, $clase)
    {
        return $query->where('vClase_envio', $clase);
    }
}