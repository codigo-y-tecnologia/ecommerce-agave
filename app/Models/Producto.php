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
    
    // DESACTIVAR TIMESTAMPS - IMPORTANTE
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
        // NUEVOS CAMPOS
        'dPeso',
        'dLargo_cm',
        'dAncho_cm',
        'dAlto_cm',
        'vClase_envio',
        // CAMPOS DE OFERTA (NUEVOS)
        'bTiene_oferta',
        'dPrecio_oferta',
        'dFecha_inicio_oferta',
        'dFecha_fin_oferta',
        'vMotivo_oferta',
        // CAMPOS DE FECHA
        'tFecha_registro',
        'tFecha_actualizacion'
    ];

    protected $casts = [
        'bActivo' => 'boolean',
        'dPrecio_compra' => 'decimal:2',
        'dPrecio_venta' => 'decimal:2',
        'dPrecio_final' => 'decimal:2',
        'iStock' => 'integer',
        // NUEVOS CAMPOS
        'dPeso' => 'decimal:3',
        'dLargo_cm' => 'decimal:2',
        'dAncho_cm' => 'decimal:2',
        'dAlto_cm' => 'decimal:2',
        // CAMPOS DE OFERTA (NUEVOS)
        'bTiene_oferta' => 'boolean',
        'dPrecio_oferta' => 'decimal:2',
        // CAMPOS DE FECHA
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

        // Al crear un producto, establecer fecha de registro si no existe
        static::creating(function ($producto) {
            if (empty($producto->tFecha_registro)) {
                $producto->tFecha_registro = now();
            }
            
            // Calcular precio final antes de guardar
            $producto->calcularPrecioFinal();
        });

        // Al actualizar un producto, establecer fecha de actualización y recalcular precio final
        static::updating(function ($producto) {
            $producto->tFecha_actualizacion = now();
            $producto->calcularPrecioFinal();
        });

        static::deleting(function ($producto) {
            $producto->favoritos()->delete();
            $producto->eliminarTodasLasImagenes();
            // Eliminar relaciones con impuestos
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
        
        // Si el producto ya tiene ID, obtenemos los impuestos de la relación
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
        $this->saveQuietly(); // Guardar sin disparar eventos para evitar bucles
    }

    // Accesor para imágenes
    public function getImagenesAttribute()
    {
        $carpetaImagenes = 'products/' . $this->id_producto;
        if (Storage::disk('public')->exists($carpetaImagenes)) {
            $archivos = Storage::disk('public')->files($carpetaImagenes);
            $imagenes = [];
            
            // Ordenar por nombre para mantener el orden
            sort($archivos);
            
            foreach ($archivos as $archivo) {
                if (preg_match('/\.(jpg|jpeg|png|gif|webp|jfif|svg)$/i', $archivo)) {
                    $imagenes[] = Storage::url($archivo);
                }
            }
            
            return $imagenes;
        }
        
        return [];
    }

    // Guardar imágenes
    public function guardarImagenes($imagenes)
    {
        if (!$this->id_producto) {
            throw new \Exception('No se puede guardar imágenes sin un ID de producto');
        }
        
        $carpetaImagenes = 'products/' . $this->id_producto;
        
        // Crear directorio si no existe
        if (!Storage::disk('public')->exists($carpetaImagenes)) {
            Storage::disk('public')->makeDirectory($carpetaImagenes);
        }
        
        // Obtener imágenes existentes para numeración
        $imagenesExistentes = Storage::disk('public')->files($carpetaImagenes);
        
        // Filtrar solo imágenes válidas
        $imagenesExistentes = array_filter($imagenesExistentes, function($archivo) {
            return preg_match('/\.(jpg|jpeg|png|gif|webp|jfif|svg)$/i', $archivo);
        });
        
        // Obtener el siguiente número disponible
        $numeroInicio = count($imagenesExistentes);
        
        $imagenesGuardadas = [];
        $contador = 0;
        $maxImagenes = 8;
        
        foreach ($imagenes as $imagen) {
            // Verificar límite máximo
            if (($numeroInicio + $contador) >= $maxImagenes) {
                break;
            }
            
            // Validar que sea una imagen válida
            if (!$imagen->isValid()) {
                continue;
            }
            
            $extension = $imagen->getClientOriginalExtension();
            $nombreArchivo = 'imagen_' . ($numeroInicio + $contador + 1) . '.' . $extension;
            $ruta = $imagen->storeAs($carpetaImagenes, $nombreArchivo, 'public');
            $imagenesGuardadas[] = Storage::url($ruta);
            $contador++;
        }
        
        return $imagenesGuardadas;
    }

    // Eliminar imágenes específicas
    public function eliminarImagenesEspecificas($nombresArchivos)
    {
        $carpetaImagenes = 'products/' . $this->id_producto;
        
        foreach ($nombresArchivos as $nombreArchivo) {
            $rutaCompleta = $carpetaImagenes . '/' . $nombreArchivo;
            if (Storage::disk('public')->exists($rutaCompleta)) {
                Storage::disk('public')->delete($rutaCompleta);
            }
        }
    }

    // Eliminar todas las imágenes
    public function eliminarTodasLasImagenes()
    {
        $carpetaImagenes = 'products/' . $this->id_producto;
        if (Storage::disk('public')->exists($carpetaImagenes)) {
            Storage::disk('public')->deleteDirectory($carpetaImagenes);
        }
    }

    // Obtener nombres de archivos de imágenes
    public function getNombresArchivosImagenes()
    {
        $carpetaImagenes = 'products/' . $this->id_producto;
        if (Storage::disk('public')->exists($carpetaImagenes)) {
            $archivos = Storage::disk('public')->files($carpetaImagenes);
            
            // Filtrar solo imágenes
            $imagenes = [];
            foreach ($archivos as $archivo) {
                if (preg_match('/\.(jpg|jpeg|png|gif|webp|jfif|svg)$/i', $archivo)) {
                    // Extraer solo el nombre del archivo
                    $nombreArchivo = basename($archivo);
                    $imagenes[] = [
                        'nombre' => $nombreArchivo,
                        'url' => Storage::url($archivo)
                    ];
                }
            }
            
            return $imagenes;
        }
        
        return [];
    }

    // Número de imágenes
    public function getNumeroImagenes()
    {
        return count($this->imagenes);
    }

    // Verificar si tiene espacio para más imágenes
    public function puedeAgregarMasImagenes()
    {
        return $this->getNumeroImagenes() < 8;
    }

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
     * Relación con impuestos (muchos a muchos) - CORREGIDA
     */
    public function impuestos()
    {
        return $this->belongsToMany(Impuesto::class, 'tbl_producto_impuestos', 'id_producto', 'id_impuesto');
        // El método withTimestamps() ha sido eliminado para evitar que Laravel busque las columnas created_at y updated_at
    }

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

    // Método para obtener atributos agrupados
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

    // Método para verificar si tiene atributos
    public function tieneAtributos()
    {
        return $this->valoresAtributos()->count() > 0;
    }

    public function favoritos()
    {
        return $this->hasMany(Favorito::class, 'id_producto');
    }

    public function esFavorito()
    {
        try {
            if (!Auth::check()) {
                return false;
            }

            return $this->favoritos()
                ->where('id_usuario', Auth::id())
                ->exists();
                 
        } catch (\Exception $e) {
            return false;
        }
    }

    public function estaBajoEnStock()
    {
        return $this->iStock > 0 && $this->iStock <= 5;
    }

    // NUEVO: Método para verificar si la oferta está vigente
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
        
        // Si no hay fechas definidas, solo verifica si tiene oferta
        return $this->bTiene_oferta;
    }

    // NUEVO: Método para obtener el precio de oferta si está vigente
    public function getPrecioOfertaVigenteAttribute()
    {
        if ($this->ofertaVigente()) {
            return $this->dPrecio_oferta;
        }
        return $this->dPrecio_venta;
    }

    // NUEVO: Método para obtener porcentaje de descuento
    public function getPorcentajeDescuentoAttribute()
    {
        if ($this->ofertaVigente() && $this->dPrecio_oferta < $this->dPrecio_venta) {
            $descuento = (($this->dPrecio_venta - $this->dPrecio_oferta) / $this->dPrecio_venta) * 100;
            return round($descuento);
        }
        return 0;
    }

    // NUEVO: Badge para oferta
    public function getOfertaBadgeAttribute()
    {
        if ($this->ofertaVigente()) {
            return '<span class="badge bg-danger">-' . $this->porcentajeDescuento . '%</span>';
        }
        return '';
    }

    public function tieneDescuento()
    {
        // Si hay oferta vigente
        if ($this->ofertaVigente() && $this->dPrecio_oferta < $this->dPrecio_venta) {
            return true;
        }
        
        // Descuento normal por precio de compra
        if (!$this->dPrecio_compra || $this->dPrecio_compra <= 0) {
            return false;
        }
        
        return $this->dPrecio_venta < $this->dPrecio_compra;
    }

    public function porcentajeDescuento()
    {
        // Prioridad a la oferta vigente
        if ($this->ofertaVigente() && $this->dPrecio_oferta < $this->dPrecio_venta) {
            $descuento = (($this->dPrecio_venta - $this->dPrecio_oferta) / $this->dPrecio_venta) * 100;
            return max(0, min(100, round($descuento)));
        }
        
        // Descuento normal
        if (!$this->tieneDescuento()) {
            return 0;
        }

        $descuento = (($this->dPrecio_compra - $this->dPrecio_venta) / $this->dPrecio_compra) * 100;
        return max(0, min(100, round($descuento)));
    }

    public function variaciones()
    {
        return $this->hasMany(ProductoVariacion::class, 'id_producto');
    }

    public function variacionesActivas()
    {
        return $this->hasMany(ProductoVariacion::class, 'id_producto')->where('bActivo', true);
    }

    public function tieneVariaciones()
    {
        return $this->variaciones()->count() > 0;
    }

    // Accesor para precio de venta
    public function getDPrecioVentaAttribute()
    {
        return $this->attributes['dPrecio_venta'];
    }

    // Nuevo método para mostrar rango de precios cuando hay variaciones
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

    // Método para obtener el precio numérico (el más bajo si hay variaciones)
    public function getPrecioMostrarAttribute()
    {
        if ($this->tieneVariaciones()) {
            $precioMin = $this->variacionesActivas()->min('dPrecio');
            return $precioMin ?: $this->attributes['dPrecio_venta'];
        }
        
        return $this->attributes['dPrecio_venta'];
    }

    // Sobrescribe el accesor de stock
    public function getIStockAttribute()
    {
        if ($this->tieneVariaciones()) {
            return $this->variacionesActivas()->sum('iStock');
        }
        
        return $this->attributes['iStock'];
    }

    // Método para obtener el precio mínimo
    public function getPrecioMinimoAttribute()
    {
        if ($this->tieneVariaciones()) {
            return $this->variacionesActivas()->min('dPrecio');
        }
        
        return $this->attributes['dPrecio_venta'];
    }

    // Método para obtener el precio máximo
    public function getPrecioMaximoAttribute()
    {
        if ($this->tieneVariaciones()) {
            return $this->variacionesActivas()->max('dPrecio');
        }
        
        return $this->attributes['dPrecio_venta'];
    }

    // Accesor para dimensiones
    public function getDimensionesFormateadasAttribute()
    {
        if ($this->dLargo_cm && $this->dAncho_cm && $this->dAlto_cm) {
            return number_format($this->dLargo_cm, 2) . ' × ' . 
                   number_format($this->dAncho_cm, 2) . ' × ' . 
                   number_format($this->dAlto_cm, 2) . ' cm';
        }
        return 'No especificado';
    }

    // Accesor para peso formateado
    public function getPesoFormateadoAttribute()
    {
        if ($this->dPeso) {
            return number_format($this->dPeso, 3) . ' kg';
        }
        return 'No especificado';
    }

    // Accesor para volumen (largo × ancho × alto)
    public function getVolumenAttribute()
    {
        if ($this->dLargo_cm && $this->dAncho_cm && $this->dAlto_cm) {
            return $this->dLargo_cm * $this->dAncho_cm * $this->dAlto_cm;
        }
        return null;
    }

    // Accesor para volumen formateado
    public function getVolumenFormateadoAttribute()
    {
        if ($this->volumen) {
            return number_format($this->volumen, 2) . ' cm³';
        }
        return 'No calculable';
    }

    // Método para verificar si tiene dimensiones completas
    public function tieneDimensionesCompletas()
    {
        return $this->dLargo_cm && $this->dAncho_cm && $this->dAlto_cm;
    }

    // Accesor para clase de envío formateada
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

    // Accesor para badge de clase de envío
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

    // Método para calcular peso volumétrico
    public function getPesoVolumetricoAttribute()
    {
        if ($this->volumen) {
            // Fórmula estándar: volumen (cm³) / 5000 = peso volumétrico (kg)
            return $this->volumen / 5000;
        }
        return null;
    }

    // Accesor para peso volumétrico formateado
    public function getPesoVolumetricoFormateadoAttribute()
    {
        if ($this->peso_volumetrico) {
            return number_format($this->peso_volumetrico, 3) . ' kg';
        }
        return 'No calculable';
    }

    // Método para determinar el peso facturable (el mayor entre peso real y volumétrico)
    public function getPesoFacturableAttribute()
    {
        $pesoReal = $this->dPeso ?: 0;
        $pesoVolumetrico = $this->peso_volumetrico ?: 0;
        
        return max($pesoReal, $pesoVolumetrico);
    }

    // Método para buscar productos por nombre o SKU
    public static function buscarPorNombreOSKU($busqueda)
    {
        return self::where(function($query) use ($busqueda) {
            $query->where('vNombre', 'LIKE', '%' . $busqueda . '%')
                  ->orWhere('vCodigo_barras', 'LIKE', '%' . $busqueda . '%');
        })->where('bActivo', true);
    }

    // Scope para productos con dimensiones
    public function scopeConDimensiones($query)
    {
        return $query->whereNotNull('dLargo_cm')
                     ->whereNotNull('dAncho_cm')
                     ->whereNotNull('dAlto_cm');
    }

    // Scope para productos con peso
    public function scopeConPeso($query)
    {
        return $query->whereNotNull('dPeso')
                     ->where('dPeso', '>', 0);
    }

    // Scope para productos por clase de envío
    public function scopePorClaseEnvio($query, $clase)
    {
        return $query->where('vClase_envio', $clase);
    }
}