<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Producto extends Model
{
    use HasFactory;
    
    protected $table = 'tbl_productos';
    protected $primaryKey = 'id_producto';
    
    // Desactivar timestamps automáticos
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

    // Accesor para obtener las imágenes del producto
    public function getImagenesAttribute()
    {
        $carpetaImagenes = 'products/' . $this->id_producto;
        if (Storage::disk('public')->exists($carpetaImagenes)) {
            $archivos = Storage::disk('public')->files($carpetaImagenes);
            $imagenes = [];
            
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
        $carpetaImagenes = 'products/' . $this->id_producto;
        
        // Eliminar imágenes existentes si es necesario
        // Storage::disk('public')->deleteDirectory($carpetaImagenes);
        
        $imagenesGuardadas = [];
        
        foreach ($imagenes as $index => $imagen) {
            if ($index >= 6) break; // Máximo 6 imágenes
            
            $extension = $imagen->getClientOriginalExtension();
            $nombreArchivo = 'imagen_' . ($index + 1) . '.' . $extension;
            $ruta = $imagen->storeAs($carpetaImagenes, $nombreArchivo, 'public');
            $imagenesGuardadas[] = Storage::url($ruta);
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
    
    public function atributos()
    {
        return $this->hasMany(ProductoAtributo::class, 'id_producto');
    }
}