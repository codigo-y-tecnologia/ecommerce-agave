<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        'vCodigo_barras','vNombre','tDescripcion_corta','tDescripcion_larga',
        'dPrecio_compra','dPrecio_venta','iStock',
        'id_marca','id_categoria','bActivo'
    ];

    protected $casts = [
        'bActivo' => 'boolean',
        'dPrecio_compra' => 'decimal:2',
        'dPrecio_venta' => 'decimal:2',
        'iStock' => 'integer',
    ];

    // Accesor para imágenes
    public function getImagenesAttribute()
    {
        $carpetaImagenes = 'products/' . $this->id_producto;
        if (Storage::disk('public')->exists($carpetaImagenes)) {
            $archivos = Storage::disk('public')->files($carpetaImagenes);
            $imagenes = [];
            
            sort($archivos);
            
            foreach ($archivos as $archivo) {
                if (preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $archivo)) {
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
        
        if (!Storage::disk('public')->exists($carpetaImagenes)) {
            Storage::disk('public')->makeDirectory($carpetaImagenes);
        }
        
        $imagenesExistentes = Storage::disk('public')->files($carpetaImagenes);
        $numeroInicio = count($imagenesExistentes) + 1;
        
        $imagenesGuardadas = [];
        $contador = 0;
        
        foreach ($imagenes as $imagen) {
            if (($numeroInicio + $contador) > 6) break;
            
            $extension = $imagen->getClientOriginalExtension();
            $nombreArchivo = 'imagen_' . ($numeroInicio + $contador) . '.' . $extension;
            $ruta = $imagen->storeAs($carpetaImagenes, $nombreArchivo, 'public');
            $imagenesGuardadas[] = Storage::url($ruta);
            $contador++;
        }
        
        return $imagenesGuardadas;
    }

    // Eliminar imágenes
    public function eliminarImagenes()
    {
        $carpetaImagenes = 'products/' . $this->id_producto;
        if (Storage::disk('public')->exists($carpetaImagenes)) {
            Storage::disk('public')->deleteDirectory($carpetaImagenes);
        }
    }

    // Número de imágenes
    public function getNumeroImagenes()
    {
        $carpetaImagenes = 'products/' . $this->id_producto;
        if (Storage::disk('public')->exists($carpetaImagenes)) {
            $archivos = Storage::disk('public')->files($carpetaImagenes);
            return count($archivos);
        }
        return 0;
    }

    public function setVCodigoBarrasAttribute($value)
    {
        $this->attributes['vCodigo_barras'] = strtoupper($value);
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
    
    // Relaciones para atributos
    public function productoAtributos()
    {
        return $this->hasMany(ProductoAtributo::class, 'id_producto');
    }

    public function atributos()
    {
        return $this->belongsToMany(Atributo::class, 'tbl_producto_atributos', 'id_producto', 'id_atributo')
                    ->withPivot('vValor', 'id_opcion')
                    ->using(ProductoAtributo::class);
    }

    
    // MÉTODOS PARA FAVORITOS

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

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($producto) {
            $producto->favoritos()->delete();
            $producto->eliminarImagenes();
        });
    }

public function impuesto()
{
    return $this->belongsToMany(
        Impuesto::class,
        'tbl_producto_impuestos',
        'id_producto',
        'id_impuesto'
    )->where('bActivo', 1);
}

/**
 * Calcula el precio de venta con impuestos incluidos.
 */
public function getPrecioConImpuestosAttribute()
{
    $precio_base = $this->dPrecio_venta;

    $ieps = 0;
    $iva = 0;

    $impuestos = $this->impuestos->where('bActivo', 1);

    // IEPS primero
    foreach ($impuestos as $imp) {
        if ($imp->eTipo === 'IEPS') {
            $ieps = $precio_base * ($imp->dPorcentaje / 100);
        }
    }

    // IVA después (sobre precio base + IEPS)
    foreach ($impuestos as $imp) {
        if ($imp->eTipo === 'IVA') {
            $iva = ($precio_base + $ieps) * ($imp->dPorcentaje / 100);
        }
    }

    return $precio_base + $ieps + $iva;
}

/**
 * Calcula el monto total de impuestos (IVA + IEPS + otros) aplicados al producto.
 */
public function getMontoImpuestosAttribute()
{
    $porcentajeTotal = $this->impuestos->sum('dPorcentaje');
    return round($this->dPrecio_venta * ($porcentajeTotal / 100), 2);
}

public function calcularIEPS()
{
    $precio_base = $this->dPrecio_venta;
    $ieps = 0;

    foreach ($this->impuestos->where('bActivo', 1) as $imp) {
        if ($imp->eTipo === 'IEPS') {
            $ieps = $precio_base * ($imp->dPorcentaje / 100);
        }
    }

    return $ieps;
}

public function calcularIVA($ieps)
{
    $precio_base = $this->dPrecio_venta;
    $iva = 0;

    foreach ($this->impuestos->where('bActivo', 1) as $imp) {
        if ($imp->eTipo === 'IVA') {
            $iva = ($precio_base + $ieps) * ($imp->dPorcentaje / 100);
        }
    }

    return $iva;
}

public function getPrecioConImpuestosCorrectoAttribute()
{
    $precio_base = $this->dPrecio_venta;

    $ieps = $this->calcularIEPS();
    $iva = $this->calcularIVA($ieps);

    return $precio_base + $ieps + $iva;
}

    //Relación con CarritoDetalle: Un producto puede estar en muchos carritos
    public function detalles()
    {
        return $this->hasMany(CarritoDetalle::class, 'id_producto', 'id_producto');
    }

    public function impuestos()
{
    return $this->belongsToMany(Impuesto::class, 'tbl_producto_impuestos', 'id_producto', 'id_impuesto');
}
}
