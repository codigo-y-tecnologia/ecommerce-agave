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
        'iStock',
        'id_marca',
        'id_categoria',
        'bActivo'
    ];

    protected $casts = [
        'bActivo' => 'boolean',
        'dPrecio_compra' => 'decimal:2',
        'dPrecio_venta' => 'decimal:2',
        'iStock' => 'integer'
    ];

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

    // Guardar imágenes NUEVO - CORREGIDO
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
        $maxImagenes = 8; // Cambiado a 8 según tu requerimiento
        
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

    public function tieneDescuento()
    {
        if (!$this->dPrecio_compra || $this->dPrecio_compra <= 0) {
            return false;
        }
        
        return $this->dPrecio_venta < $this->dPrecio_compra;
    }

    public function porcentajeDescuento()
    {
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

    // Accesor CORREGIDO para precio de venta (siempre devuelve el valor numérico de la BD)
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

    // NUEVO: Método para obtener dimensiones promedio de las variaciones
    public function getDimensionesPromedio()
    {
        if ($this->variaciones->count() > 0) {
            $variacionesConDimensiones = $this->variaciones->filter(function($variacion) {
                return $variacion->dLargo_cm && $variacion->dAncho_cm && $variacion->dAlto_cm;
            });
            
            if ($variacionesConDimensiones->count() > 0) {
                $largo = $variacionesConDimensiones->avg('dLargo_cm');
                $ancho = $variacionesConDimensiones->avg('dAncho_cm');
                $alto = $variacionesConDimensiones->avg('dAlto_cm');
                
                return [
                    'largo' => $largo ? floatval($largo) : null,
                    'ancho' => $ancho ? floatval($ancho) : null,
                    'alto' => $alto ? floatval($alto) : null,
                    'volumen' => $largo && $ancho && $alto ? $largo * $ancho * $alto : null
                ];
            }
        }
        
        return ['largo' => null, 'ancho' => null, 'alto' => null, 'volumen' => null];
    }

    // NUEVO: Accesor para dimensiones promedio formateadas
    public function getDimensionesPromedioFormateadasAttribute()
    {
        $dimensiones = $this->getDimensionesPromedio();
        
        if ($dimensiones['largo'] && $dimensiones['ancho'] && $dimensiones['alto']) {
            return number_format($dimensiones['largo'], 1) . ' × ' . 
                   number_format($dimensiones['ancho'], 1) . ' × ' . 
                   number_format($dimensiones['alto'], 1) . ' cm';
        }
        
        return 'No especificado';
    }

    // NUEVO: Método para verificar si alguna variación tiene dimensiones
    public function tieneDimensiones()
    {
        return $this->variaciones()
            ->whereNotNull('dLargo_cm')
            ->whereNotNull('dAncho_cm')
            ->whereNotNull('dAlto_cm')
            ->exists();
    }

    // NUEVO: Método para obtener las dimensiones más comunes
    public function getDimensionesMasComunes()
    {
        $dimensionesFrecuentes = [];
        
        foreach ($this->variaciones as $variacion) {
            if ($variacion->dLargo_cm && $variacion->dAncho_cm && $variacion->dAlto_cm) {
                $key = $variacion->dLargo_cm . '|' . $variacion->dAncho_cm . '|' . $variacion->dAlto_cm;
                
                if (!isset($dimensionesFrecuentes[$key])) {
                    $dimensionesFrecuentes[$key] = [
                        'largo' => $variacion->dLargo_cm,
                        'ancho' => $variacion->dAncho_cm,
                        'alto' => $variacion->dAlto_cm,
                        'count' => 0,
                        'variaciones' => []
                    ];
                }
                
                $dimensionesFrecuentes[$key]['count']++;
                $dimensionesFrecuentes[$key]['variaciones'][] = $variacion->vSKU;
            }
        }
        
        // Ordenar por frecuencia descendente
        usort($dimensionesFrecuentes, function($a, $b) {
            return $b['count'] - $a['count'];
        });
        
        return $dimensionesFrecuentes;
    }

    // NUEVO: Accesor para el volumen total estimado de inventario
    public function getVolumenTotalInventarioAttribute()
    {
        $volumenTotal = 0;
        
        foreach ($this->variaciones as $variacion) {
            if ($variacion->dLargo_cm && $variacion->dAncho_cm && $variacion->dAlto_cm) {
                $volumenUnidad = $variacion->dLargo_cm * $variacion->dAncho_cm * $variacion->dAlto_cm;
                $volumenTotal += $volumenUnidad * $variacion->iStock;
            }
        }
        
        return $volumenTotal;
    }

    // NUEVO: Accesor para el peso total estimado de inventario
    public function getPesoTotalInventarioAttribute()
    {
        $pesoTotal = 0;
        
        foreach ($this->variaciones as $variacion) {
            if ($variacion->dPeso) {
                $pesoTotal += $variacion->dPeso * $variacion->iStock;
            }
        }
        
        return $pesoTotal;
    }

    // NUEVO: Método para obtener estadísticas de dimensiones
    public function getEstadisticasDimensiones()
    {
        $variacionesConDimensiones = $this->variaciones->filter(function($variacion) {
            return $variacion->dLargo_cm && $variacion->dAncho_cm && $variacion->dAlto_cm;
        });
        
        $totalVariaciones = $variacionesConDimensiones->count();
        
        if ($totalVariaciones === 0) {
            return [
                'total_variaciones' => 0,
                'con_dimensiones' => 0,
                'porcentaje_con_dimensiones' => 0,
                'largo_min' => null,
                'largo_max' => null,
                'ancho_min' => null,
                'ancho_max' => null,
                'alto_min' => null,
                'alto_max' => null,
                'volumen_promedio' => null
            ];
        }
        
        $largoMin = $variacionesConDimensiones->min('dLargo_cm');
        $largoMax = $variacionesConDimensiones->max('dLargo_cm');
        $anchoMin = $variacionesConDimensiones->min('dAncho_cm');
        $anchoMax = $variacionesConDimensiones->max('dAncho_cm');
        $altoMin = $variacionesConDimensiones->min('dAlto_cm');
        $altoMax = $variacionesConDimensiones->max('dAlto_cm');
        
        $volumenPromedio = $variacionesConDimensiones->avg(function($variacion) {
            return $variacion->dLargo_cm * $variacion->dAncho_cm * $variacion->dAlto_cm;
        });
        
        return [
            'total_variaciones' => $this->variaciones->count(),
            'con_dimensiones' => $totalVariaciones,
            'porcentaje_con_dimensiones' => round(($totalVariaciones / $this->variaciones->count()) * 100, 1),
            'largo_min' => floatval($largoMin),
            'largo_max' => floatval($largoMax),
            'ancho_min' => floatval($anchoMin),
            'ancho_max' => floatval($anchoMax),
            'alto_min' => floatval($altoMin),
            'alto_max' => floatval($altoMax),
            'volumen_promedio' => $volumenPromedio ? floatval($volumenPromedio) : null
        ];
    }

    // NUEVO: Método para buscar productos por nombre o SKU
    public static function buscarPorNombreOSKU($busqueda)
    {
        return self::where(function($query) use ($busqueda) {
            $query->where('vNombre', 'LIKE', '%' . $busqueda . '%')
                  ->orWhere('vCodigo_barras', 'LIKE', '%' . $busqueda . '%');
        })->where('bActivo', true);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($producto) {
            $producto->favoritos()->delete();
            $producto->eliminarTodasLasImagenes();
        });
    }
}