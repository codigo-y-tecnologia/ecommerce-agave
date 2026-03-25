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
        'dPrecio',
        'dPrecio_descuento',
        'dPrecio_final',
        'dFecha_inicio_descuento',
        'dFecha_fin_descuento',
        'vMotivo_descuento',
        'iStock',
        'dPeso',
        'dLargo_cm',
        'dAncho_cm',
        'dAlto_cm',
        'bTiene_descuento',
        'id_impuesto',
        'vClase_envio',
        'tDescripcion',
        'vImagen',
        'bActivo',
        'tFecha_registro',
        'tFecha_actualizacion'
    ];

    protected $casts = [
        'dPrecio' => 'decimal:2',
        'dPrecio_descuento' => 'decimal:2',
        'dPrecio_final' => 'decimal:2',
        'iStock' => 'integer',
        'bTiene_descuento' => 'boolean',
        'bActivo' => 'boolean',
        'dPeso' => 'decimal:2',
        'dLargo_cm' => 'decimal:2',
        'dAncho_cm' => 'decimal:2',
        'dAlto_cm' => 'decimal:2',
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
        'numero_imagenes',
        'precio_actual',
        'porcentaje_descuento',
        'porcentaje_descuento_real',
        'ahorro_total',
        'precio_con_descuento_con_impuesto',
        'precio_original_con_impuesto',
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

    // ============ MÉTODOS DE DESCUENTO CORREGIDOS ============

    /**
     * Verifica si el descuento está activo en la fecha actual
     * Usa DateTime nativo de PHP sin Carbon
     */
    public function tieneDescuentoActivo(): bool
    {
        if (!$this->bTiene_descuento || $this->dPrecio_descuento === null || $this->dPrecio_descuento <= 0) {
            return false;
        }

        $fechaActual = new \DateTime();
        $fechaActual->setTime(0, 0, 0);

        // Procesar fecha de inicio
        $fechaInicio = null;
        if ($this->dFecha_inicio_descuento) {
            if ($this->dFecha_inicio_descuento instanceof \DateTime) {
                $fechaInicio = clone $this->dFecha_inicio_descuento;
            } else {
                $fechaInicio = new \DateTime($this->dFecha_inicio_descuento);
            }
            $fechaInicio->setTime(0, 0, 0);
        }

        // Procesar fecha de fin
        $fechaFin = null;
        if ($this->dFecha_fin_descuento) {
            if ($this->dFecha_fin_descuento instanceof \DateTime) {
                $fechaFin = clone $this->dFecha_fin_descuento;
            } else {
                $fechaFin = new \DateTime($this->dFecha_fin_descuento);
            }
            $fechaFin->setTime(23, 59, 59);
        }

        // Caso 1: Tiene ambas fechas
        if ($fechaInicio && $fechaFin) {
            return $fechaActual >= $fechaInicio && $fechaActual <= $fechaFin;
        }

        // Caso 2: Solo tiene fecha de inicio
        if ($fechaInicio && !$fechaFin) {
            return $fechaActual >= $fechaInicio;
        }

        // Caso 3: Solo tiene fecha de fin
        if (!$fechaInicio && $fechaFin) {
            return $fechaActual <= $fechaFin;
        }

        // Caso 4: No tiene fechas, el descuento está siempre activo
        return true;
    }

    /**
     * Alias para tieneDescuentoActivo
     */
    public function descuentoVigente(): bool
    {
        return $this->tieneDescuentoActivo();
    }

    /**
     * Obtiene el precio base (con descuento si está activo, sino el normal)
     */
    public function getPrecioBaseAttribute()
    {
        if ($this->tieneDescuentoActivo()) {
            return $this->dPrecio_descuento;
        }
        return $this->dPrecio;
    }

    /**
     * Alias para getPrecioBaseAttribute
     */
    public function getPrecioActualAttribute()
    {
        return $this->getPrecioBaseAttribute();
    }

    /**
     * Obtener el porcentaje de descuento (sobre precio sin impuesto)
     */
    public function getPorcentajeDescuentoAttribute()
    {
        if ($this->tieneDescuentoActivo() && $this->dPrecio_descuento < $this->dPrecio && $this->dPrecio > 0) {
            $descuento = (($this->dPrecio - $this->dPrecio_descuento) / $this->dPrecio) * 100;
            return round($descuento);
        }
        return 0;
    }

    /**
     * Obtener el porcentaje de descuento real considerando impuestos
     */
    public function getPorcentajeDescuentoRealAttribute()
    {
        if (!$this->tieneDescuentoActivo() || $this->dPrecio_descuento >= $this->dPrecio) {
            return 0;
        }

        $precioOriginalConImpuesto = $this->dPrecio;
        $precioDescuentoConImpuesto = $this->dPrecio_descuento;

        if ($this->impuesto && $this->impuesto->bActivo) {
            $precioOriginalConImpuesto += $this->dPrecio * ($this->impuesto->dPorcentaje / 100);
            $precioDescuentoConImpuesto += $this->dPrecio_descuento * ($this->impuesto->dPorcentaje / 100);
        }

        if ($precioOriginalConImpuesto > 0) {
            $descuentoReal = (($precioOriginalConImpuesto - $precioDescuentoConImpuesto) / $precioOriginalConImpuesto) * 100;
            return round($descuentoReal);
        }

        return 0;
    }

    /**
     * Obtener el ahorro total (precio original con impuesto - precio descuento con impuesto)
     */
    public function getAhorroTotalAttribute()
    {
        if (!$this->tieneDescuentoActivo() || $this->dPrecio_descuento >= $this->dPrecio) {
            return 0;
        }

        $precioOriginalConImpuesto = $this->dPrecio;
        $precioDescuentoConImpuesto = $this->dPrecio_descuento;

        if ($this->impuesto && $this->impuesto->bActivo) {
            $precioOriginalConImpuesto += $this->dPrecio * ($this->impuesto->dPorcentaje / 100);
            $precioDescuentoConImpuesto += $this->dPrecio_descuento * ($this->impuesto->dPorcentaje / 100);
        }

        return round($precioOriginalConImpuesto - $precioDescuentoConImpuesto, 2);
    }

    /**
     * Obtener precio con descuento ya con impuesto incluido
     */
    public function getPrecioConDescuentoConImpuestoAttribute()
    {
        if (!$this->tieneDescuentoActivo() || $this->dPrecio_descuento >= $this->dPrecio) {
            return $this->precio_final;
        }

        $precioDescuentoConImpuesto = $this->dPrecio_descuento;

        if ($this->impuesto && $this->impuesto->bActivo) {
            $precioDescuentoConImpuesto += $this->dPrecio_descuento * ($this->impuesto->dPorcentaje / 100);
        }

        return $precioDescuentoConImpuesto;
    }

    /**
     * Obtener precio original con impuesto
     */
    public function getPrecioOriginalConImpuestoAttribute()
    {
        $precioOriginalConImpuesto = $this->dPrecio;

        if ($this->impuesto && $this->impuesto->bActivo) {
            $precioOriginalConImpuesto += $this->dPrecio * ($this->impuesto->dPorcentaje / 100);
        }

        return $precioOriginalConImpuesto;
    }

    /**
     * Alias para tieneDescuento
     */
    public function tieneDescuento(): bool
    {
        return $this->tieneDescuentoActivo();
    }

    // ============ MÉTODO DE PRECIO FINAL ============

    /**
     * Obtener el precio final EN TIEMPO REAL con impuestos incluidos
     * Este es el método que DEBE usarse para mostrar precios
     */
    public function getPrecioFinalAttribute()
    {
        $precioBase = $this->getPrecioBaseAttribute();
        $totalImpuestos = 0;

        if ($this->impuesto && $this->impuesto->bActivo) {
            $totalImpuestos = $precioBase * ($this->impuesto->dPorcentaje / 100);
        }

        return $precioBase + $totalImpuestos;
    }

    /**
     * Obtener el total de impuestos
     */
    public function getTotalImpuestoAttribute()
    {
        $precioBase = $this->getPrecioBaseAttribute();

        if ($this->impuesto && $this->impuesto->bActivo) {
            return $precioBase * ($this->impuesto->dPorcentaje / 100);
        }
        return 0;
    }

    public function recalcularPrecioFinal()
    {
        $precioBase = $this->getPrecioBaseAttribute();
        $totalImpuestos = 0;

        if ($this->impuesto && $this->impuesto->bActivo) {
            $totalImpuestos = $precioBase * ($this->impuesto->dPorcentaje / 100);
        }

        $this->dPrecio_final = $precioBase + $totalImpuestos;
        $this->save();

        return $this->dPrecio_final;
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

    // ============ RELACIONES ============

    /**
     * Relación con el producto padre
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    /**
     * Alias para producto()
     */
    public function productoPadre()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    /**
     * Relación con los atributos de la variación
     */
    public function atributos()
    {
        return $this->hasMany(VariacionAtributo::class, 'id_variacion');
    }

    /**
     * Relación con el impuesto aplicable
     */
    public function impuesto()
    {
        return $this->belongsTo(Impuesto::class, 'id_impuesto');
    }

    /**
     * Relación con las imágenes registradas
     */
    public function imagenesRegistradas()
    {
        return $this->hasMany(VariacionImagen::class, 'id_variacion')->orderBy('iOrden');
    }

    /**
     * Relación con los favoritos
     */
    public function favoritos()
    {
        return $this->hasMany(Favorito::class, 'id_variacion');
    }

    // ============ MÉTODOS DE ATRIBUTOS ============

    /**
     * Obtener texto de los atributos (ej: "Rojo - XL")
     */
    public function getAtributosTexto(): string
    {
        $atributos = [];
        foreach ($this->atributos as $atributoRel) {
            if ($atributoRel->atributo && $atributoRel->valor) {
                $atributos[] = $atributoRel->valor->vValor;
            }
        }
        return implode(' - ', $atributos);
    }

    /**
     * Obtener texto completo de los atributos (ej: "Color: Rojo | Talla: XL")
     */
    public function getAtributosCompletosTexto(): string
    {
        $atributos = [];
        foreach ($this->atributos as $atributoRel) {
            if ($atributoRel->atributo && $atributoRel->valor) {
                $atributos[] = $atributoRel->atributo->vNombre . ': ' . $atributoRel->valor->vValor;
            }
        }
        return implode(' | ', $atributos);
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

        // Eliminar imagen principal existente
        $this->eliminarImagenPrincipal();

        $carpeta = 'variaciones/' . $this->id_variacion;

        if (!Storage::disk('public')->exists($carpeta)) {
            Storage::disk('public')->makeDirectory($carpeta);
        }

        $extension = $imagen->getClientOriginalExtension();
        $nombreArchivo = 'principal_' . time() . '_' . uniqid() . '.' . $extension;
        $ruta = $imagen->storeAs($carpeta, $nombreArchivo, 'public');

        VariacionImagen::create([
            'id_variacion' => $this->id_variacion,
            'vRuta' => $ruta,
            'eTipo' => 'principal',
            'iOrden' => 0,
            'bActivo' => true
        ]);

        $this->vImagen = $ruta;
        $this->saveQuietly();

        Log::info('Imagen principal guardada para variación ID: ' . $this->id_variacion);

        return Storage::url($ruta);
    }

    /**
     * Guardar GIF de la variación
     */
    public function guardarGif($gif)
    {
        if (!$this->id_variacion) {
            throw new \Exception('No se puede guardar GIF sin ID de variación');
        }

        // Eliminar GIF existente
        $this->eliminarGif();

        $carpeta = 'variaciones/' . $this->id_variacion;

        if (!Storage::disk('public')->exists($carpeta)) {
            Storage::disk('public')->makeDirectory($carpeta);
        }

        $extension = $gif->getClientOriginalExtension();
        $nombreArchivo = 'gif_' . time() . '_' . uniqid() . '.' . $extension;
        $ruta = $gif->storeAs($carpeta, $nombreArchivo, 'public');

        VariacionImagen::create([
            'id_variacion' => $this->id_variacion,
            'vRuta' => $ruta,
            'eTipo' => 'gif',
            'iOrden' => 1,
            'bActivo' => true
        ]);

        Log::info('GIF guardado para variación ID: ' . $this->id_variacion);

        return Storage::url($ruta);
    }

    /**
     * Guardar imágenes adicionales de la variación
     */
    public function guardarImagenesAdicionales($imagenes)
    {
        if (!$this->id_variacion) {
            throw new \Exception('No se puede guardar imágenes sin ID de variación');
        }

        if (!is_array($imagenes)) {
            $imagenes = [$imagenes];
        }

        if (empty($imagenes)) {
            return [];
        }

        $carpetaBase = 'variaciones/' . $this->id_variacion;
        $carpetaAdicionales = $carpetaBase . '/adicionales';

        if (!Storage::disk('public')->exists($carpetaAdicionales)) {
            Storage::disk('public')->makeDirectory($carpetaAdicionales);
        }

        $ultimoOrden = VariacionImagen::where('id_variacion', $this->id_variacion)
            ->where('eTipo', 'adicional')
            ->max('iOrden') ?? 0;

        $orden = $ultimoOrden + 1;
        $maxImagenes = 7;

        $totalActual = VariacionImagen::where('id_variacion', $this->id_variacion)
            ->where('eTipo', 'adicional')
            ->count();

        $imagenesGuardadas = [];

        foreach ($imagenes as $index => $imagen) {
            if (!$imagen || !$imagen->isValid()) {
                continue;
            }

            if ($totalActual >= $maxImagenes) {
                break;
            }

            try {
                $extension = $imagen->getClientOriginalExtension();
                $nombreArchivo = 'imagen_' . $orden . '_' . time() . '_' . uniqid() . '.' . $extension;
                $ruta = $imagen->storeAs($carpetaAdicionales, $nombreArchivo, 'public');

                VariacionImagen::create([
                    'id_variacion' => $this->id_variacion,
                    'vRuta' => $ruta,
                    'eTipo' => 'adicional',
                    'iOrden' => $orden,
                    'bActivo' => true
                ]);

                $imagenesGuardadas[] = Storage::url($ruta);
                $orden++;
                $totalActual++;
            } catch (\Exception $e) {
                Log::error('Error al guardar imagen adicional: ' . $e->getMessage());
            }
        }

        return $imagenesGuardadas;
    }

    /**
     * Eliminar imagen principal de la variación
     */
    public function eliminarImagenPrincipal()
    {
        // Buscar la imagen principal en la tabla de imágenes registradas
        $imagenPrincipal = $this->imagenesRegistradas()
            ->where('eTipo', 'principal')
            ->first();

        if ($imagenPrincipal) {
            // Eliminar el archivo físico
            if ($imagenPrincipal->vRuta && Storage::disk('public')->exists($imagenPrincipal->vRuta)) {
                Storage::disk('public')->delete($imagenPrincipal->vRuta);
                Log::info('Archivo de imagen principal eliminado: ' . $imagenPrincipal->vRuta);
            }
            // Eliminar el registro de la base de datos
            $imagenPrincipal->delete();
            Log::info('Registro de imagen principal eliminado de variación ID: ' . $this->id_variacion);
        }

        // Limpiar el campo vImagen si tiene valor
        if ($this->vImagen) {
            if (Storage::disk('public')->exists($this->vImagen)) {
                Storage::disk('public')->delete($this->vImagen);
            }
            $this->vImagen = null;
            $this->saveQuietly();
        }

        return true;
    }

    /**
     * Eliminar GIF de la variación
     */
    public function eliminarGif()
    {
        // Buscar el GIF en la tabla de imágenes registradas
        $gif = $this->imagenesRegistradas()
            ->where('eTipo', 'gif')
            ->first();

        if ($gif) {
            // Eliminar el archivo físico
            if ($gif->vRuta && Storage::disk('public')->exists($gif->vRuta)) {
                Storage::disk('public')->delete($gif->vRuta);
                Log::info('Archivo de GIF eliminado: ' . $gif->vRuta);
            }
            // Eliminar el registro de la base de datos
            $gif->delete();
            Log::info('Registro de GIF eliminado de variación ID: ' . $this->id_variacion);
        }

        return true;
    }

    /**
     * Eliminar imágenes adicionales específicas por sus índices o IDs
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

        if (!is_array($imagenesAEliminar)) {
            return;
        }

        // Obtener todas las imágenes adicionales ordenadas
        $imagenesAdicionales = $this->imagenesRegistradas()
            ->where('eTipo', 'adicional')
            ->orderBy('iOrden')
            ->get();

        $indicesAEliminar = [];

        // Procesar cada elemento a eliminar
        foreach ($imagenesAEliminar as $item) {
            if (is_array($item) && isset($item['id'])) {
                // Eliminar por ID de imagen
                $imagen = $this->imagenesRegistradas()->find($item['id']);
                if ($imagen) {
                    if ($imagen->vRuta && Storage::disk('public')->exists($imagen->vRuta)) {
                        Storage::disk('public')->delete($imagen->vRuta);
                        Log::info('Archivo de imagen adicional eliminado: ' . $imagen->vRuta);
                    }
                    $imagen->delete();
                    Log::info('Imagen adicional eliminada por ID: ' . $item['id']);
                }
            } elseif (is_numeric($item)) {
                // Eliminar por índice
                $indicesAEliminar[] = (int)$item;
            }
        }

        // Eliminar por índices
        if (!empty($indicesAEliminar)) {
            foreach ($imagenesAdicionales as $index => $imagen) {
                if (in_array($index, $indicesAEliminar)) {
                    if ($imagen->vRuta && Storage::disk('public')->exists($imagen->vRuta)) {
                        Storage::disk('public')->delete($imagen->vRuta);
                        Log::info('Archivo de imagen adicional eliminado por índice: ' . $imagen->vRuta);
                    }
                    $imagen->delete();
                    Log::info('Imagen adicional eliminada por índice: ' . $index);
                }
            }
        }

        // Reordenar las imágenes restantes
        $imagenesRestantes = $this->imagenesRegistradas()
            ->where('eTipo', 'adicional')
            ->orderBy('iOrden')
            ->get();

        $nuevoOrden = 1;
        foreach ($imagenesRestantes as $imagen) {
            $imagen->iOrden = $nuevoOrden;
            $imagen->save();
            $nuevoOrden++;
        }

        return true;
    }

    /**
     * Eliminar todas las imágenes de la variación
     */
    public function eliminarTodasLasImagenes()
    {
        // Eliminar todas las imágenes registradas
        $imagenes = $this->imagenesRegistradas()->get();
        foreach ($imagenes as $imagen) {
            if ($imagen->vRuta && Storage::disk('public')->exists($imagen->vRuta)) {
                Storage::disk('public')->delete($imagen->vRuta);
            }
            $imagen->delete();
        }

        // Eliminar la carpeta de la variación
        $carpeta = 'variaciones/' . $this->id_variacion;
        if (Storage::disk('public')->exists($carpeta)) {
            Storage::disk('public')->deleteDirectory($carpeta);
        }

        // Limpiar el campo vImagen
        if ($this->vImagen) {
            if (Storage::disk('public')->exists($this->vImagen)) {
                Storage::disk('public')->delete($this->vImagen);
            }
            $this->vImagen = null;
            $this->saveQuietly();
        }

        Log::info('Todas las imágenes eliminadas de variación ID: ' . $this->id_variacion);

        return true;
    }

    // ============ ACCESORES PARA IMÁGENES ============

    /**
     * Obtener URL de la imagen principal
     */
    public function getImagenPrincipalUrlAttribute()
    {
        // Buscar en imágenes registradas
        $imagenPrincipal = $this->imagenesRegistradas()
            ->where('eTipo', 'principal')
            ->where('bActivo', true)
            ->first();

        if ($imagenPrincipal && $imagenPrincipal->vRuta) {
            if (Storage::disk('public')->exists($imagenPrincipal->vRuta)) {
                return Storage::url($imagenPrincipal->vRuta);
            }
        }

        // Buscar en campo vImagen
        if ($this->vImagen && Storage::disk('public')->exists($this->vImagen)) {
            return Storage::url($this->vImagen);
        }

        // Si no hay imagen de variación, usar la del producto padre
        if ($this->productoPadre) {
            return $this->productoPadre->imagen_principal_url;
        }

        return null;
    }

    /**
     * Obtener URL del GIF
     */
    public function getGifUrlAttribute()
    {
        $gif = $this->imagenesRegistradas()
            ->where('eTipo', 'gif')
            ->where('bActivo', true)
            ->first();

        if ($gif && $gif->vRuta && Storage::disk('public')->exists($gif->vRuta)) {
            return Storage::url($gif->vRuta);
        }

        if ($this->productoPadre) {
            return $this->productoPadre->gif_url;
        }

        return null;
    }

    /**
     * Obtener URLs de las imágenes adicionales
     */
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
            if ($img->vRuta && Storage::disk('public')->exists($img->vRuta)) {
                $imagenes[] = Storage::url($img->vRuta);
            }
        }

        return $imagenes;
    }

    /**
     * Obtener todas las imágenes (principal, gif, adicionales)
     */
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

    /**
     * Obtener la primera imagen disponible
     */
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

    /**
     * Obtener el número total de imágenes
     */
    public function getNumeroImagenesAttribute()
    {
        return VariacionImagen::where('id_variacion', $this->id_variacion)
            ->where('bActivo', true)
            ->count();
    }

    /**
     * Verificar si se pueden agregar más imágenes
     */
    public function puedeAgregarMasImagenes()
    {
        return $this->numero_imagenes < 9;
    }

    // ============ MÉTODOS DE FAVORITOS ============

    /**
     * Verificar si la variación es favorita del usuario actual
     */
    public function esFavorito(): bool
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

    /**
     * Obtener dimensiones formateadas
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
     * Obtener peso formateado
     */
    public function getPesoFormateadoAttribute()
    {
        if ($this->dPeso) {
            return number_format($this->dPeso, 3) . ' kg';
        }
        return 'No especificado';
    }

    /**
     * Obtener volumen en cm³
     */
    public function getVolumenAttribute()
    {
        if ($this->dLargo_cm && $this->dAncho_cm && $this->dAlto_cm) {
            return $this->dLargo_cm * $this->dAncho_cm * $this->dAlto_cm;
        }
        return null;
    }

    /**
     * Obtener clase de envío formateada
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
     * Obtener peso volumétrico
     */
    public function getPesoVolumetricoAttribute()
    {
        if ($this->volumen) {
            return $this->volumen / 5000;
        }
        return null;
    }

    /**
     * Obtener precio formateado (actual)
     */
    public function getPrecioFormateadoAttribute()
    {
        return '$' . number_format($this->precio_actual, 2);
    }

    /**
     * Obtener precio final formateado
     */
    public function getPrecioFinalFormateadoAttribute()
    {
        return '$' . number_format($this->precio_final, 2);
    }

    /**
     * Obtener stock formateado con colores HTML
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

    /**
     * Verificar si tiene dimensiones completas
     */
    public function tieneDimensionesCompletas(): bool
    {
        return $this->dLargo_cm && $this->dAncho_cm && $this->dAlto_cm;
    }

    /**
     * Verificar si tiene peso
     */
    public function tienePeso(): bool
    {
        return $this->dPeso && $this->dPeso > 0;
    }

    /**
     * Obtener el peso facturable (el mayor entre peso real y peso volumétrico)
     */
    public function getPesoFacturableAttribute()
    {
        $pesoReal = $this->dPeso ?: 0;
        $pesoVolumetrico = $this->peso_volumetrico ?: 0;

        return max($pesoReal, $pesoVolumetrico);
    }
}
