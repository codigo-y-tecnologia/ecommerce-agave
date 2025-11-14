<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

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
        'iStock' => 'integer'
    ];

    // Accesor para obtener las imágenes del producto
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

    // Método para guardar imágenes
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

    // Método para eliminar imágenes al borrar el producto
    public function eliminarImagenes()
    {
        $carpetaImagenes = 'products/' . $this->id_producto;
        if (Storage::disk('public')->exists($carpetaImagenes)) {
            Storage::disk('public')->deleteDirectory($carpetaImagenes);
        }
    }

    // Método para obtener el número de imágenes actuales
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
    
    // MÉTODOS PARA ATRIBUTOS
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

    public function impuestos()
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
    $porcentajeTotal = $this->impuestos->sum('dPorcentaje');
    return round($this->dPrecio_venta * (1 + ($porcentajeTotal / 100)), 2);
}

    //Relación con CarritoDetalle: Un producto puede estar en muchos carritos
    public function detalles()
    {
        return $this->hasMany(CarritoDetalle::class, 'id_producto', 'id_producto');
    }

}
