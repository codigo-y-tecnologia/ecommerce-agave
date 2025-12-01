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
            
            // Ordenar archivos para mostrar en orden correcto
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
        // Asegurarnos de que tenemos un ID de producto
        if (!$this->id_producto) {
            throw new \Exception('No se puede guardar imágenes sin un ID de producto');
        }
        
        $carpetaImagenes = 'products/' . $this->id_producto;
        
        // Crear directorio si no existe
        if (!Storage::disk('public')->exists($carpetaImagenes)) {
            Storage::disk('public')->makeDirectory($carpetaImagenes);
        }
        
        // Obtener imágenes existentes para determinar el siguiente número
        $imagenesExistentes = Storage::disk('public')->files($carpetaImagenes);
        $numeroInicio = count($imagenesExistentes) + 1;
        
        $imagenesGuardadas = [];
        $contador = 0;
        
        foreach ($imagenes as $imagen) {
            if (($numeroInicio + $contador) > 6) break; // Máximo 6 imágenes
            
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

    // Método para eliminar una imagen específica
    public function eliminarImagen($nombreArchivo)
    {
        $rutaCompleta = 'products/' . $this->id_producto . '/' . $nombreArchivo;
        if (Storage::disk('public')->exists($rutaCompleta)) {
            Storage::disk('public')->delete($rutaCompleta);
            return true;
        }
        return false;
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

    // Método para debug - verificar si la carpeta se crea correctamente
    public function debugImagenes()
    {
        $carpetaImagenes = 'products/' . $this->id_producto;
        
        return [
            'id_producto' => $this->id_producto,
            'carpeta' => $carpetaImagenes,
            'existe_carpeta' => Storage::disk('public')->exists($carpetaImagenes),
            'archivos_en_carpeta' => Storage::disk('public')->exists($carpetaImagenes) 
                ? Storage::disk('public')->files($carpetaImagenes) 
                : []
        ];
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