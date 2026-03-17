<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Categoria extends Model
{
    use HasFactory;

    protected $table = 'tbl_categorias';
    protected $primaryKey = 'id_categoria';

    public $timestamps = false;

    protected $fillable = [
        'vNombre',
        'vSlug',
        'tDescripcion',
        'id_categoria_padre',
        'iOrden',
        'bActivo',
        'vImagen'
    ];

    protected $casts = [
        'bActivo' => 'boolean',
        'iOrden' => 'integer'
    ];

    protected $appends = ['imagen_url', 'tiene_imagen'];

    // Accesor para obtener la URL completa de la imagen
    public function getImagenUrlAttribute()
    {
        if (!$this->vImagen) {
            return null;
        }

        // Verificar directamente en public/storage/categorias/
        $publicPath = public_path('storage/categorias/' . $this->vImagen);

        if (file_exists($publicPath)) {
            return asset('storage/categorias/' . $this->vImagen);
        }

        return null;
    }

    // Accesor para verificar si la imagen existe
    public function getTieneImagenAttribute()
    {
        if (!$this->vImagen) {
            return false;
        }

        $path = public_path('storage/categorias/' . $this->vImagen);
        return file_exists($path);
    }

    // Evento para generar slug automáticamente
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($categoria) {
            if (empty($categoria->vSlug)) {
                $categoria->vSlug = Str::slug($categoria->vNombre);
            }

            // Establecer orden si no se especifica
            if (empty($categoria->iOrden)) {
                $ultimoOrden = self::where('id_categoria_padre', $categoria->id_categoria_padre)
                    ->max('iOrden');
                $categoria->iOrden = $ultimoOrden ? $ultimoOrden + 1 : 0;
            }
        });

        static::updating(function ($categoria) {
            if ($categoria->isDirty('vNombre') && empty($categoria->vSlug)) {
                $categoria->vSlug = Str::slug($categoria->vNombre);
            }
        });

        static::deleting(function ($categoria) {
            // Eliminar imagen al eliminar la categoría
            if ($categoria->vImagen) {
                $rutaImagen = public_path('storage/categorias/' . $categoria->vImagen);
                if (file_exists($rutaImagen)) {
                    unlink($rutaImagen);
                }
            }
        });
    }

    // Relación con productos
    public function productos()
    {
        return $this->hasMany(Producto::class, 'id_categoria');
    }

    // Relación con categoría padre
    public function padre()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria_padre');
    }

    // Relación con categorías hijas
    public function hijos()
    {
        return $this->hasMany(Categoria::class, 'id_categoria_padre')
            ->orderBy('iOrden')
            ->orderBy('vNombre');
    }

    // Relación con categorías hijas activas
    public function hijosActivos()
    {
        return $this->hasMany(Categoria::class, 'id_categoria_padre')
            ->where('bActivo', true)
            ->orderBy('iOrden')
            ->orderBy('vNombre');
    }

    // Obtener todas las categorías raíz (sin padre)
    public static function raices()
    {
        return self::whereNull('id_categoria_padre')
            ->where('bActivo', true)
            ->orderBy('iOrden')
            ->orderBy('vNombre')
            ->get();
    }

    // Obtener categorías en formato jerárquico para menús
    public static function obtenerJerarquia()
    {
        $categorias = self::where('bActivo', true)
            ->orderBy('iOrden')
            ->orderBy('vNombre')
            ->get();

        return self::construirArbol($categorias);
    }

    // Construir árbol jerárquico
    private static function construirArbol($categorias, $padreId = null)
    {
        $arbol = [];

        foreach ($categorias as $categoria) {
            if ($categoria->id_categoria_padre == $padreId) {
                $hijos = self::construirArbol($categorias, $categoria->id_categoria);
                if ($hijos) {
                    $categoria->hijos = $hijos;
                }
                $arbol[] = $categoria;
            }
        }

        return $arbol;
    }

    // Obtener ruta de categorías (breadcrumb)
    public function obtenerRuta()
    {
        $ruta = collect([$this]);
        $actual = $this;

        while ($actual->padre) {
            $ruta->prepend($actual->padre);
            $actual = $actual->padre;
        }

        return $ruta;
    }

    // Verificar si es categoría raíz
    public function esRaiz()
    {
        return is_null($this->id_categoria_padre);
    }

    // Verificar si tiene hijos
    public function tieneHijos()
    {
        return $this->hijos()->count() > 0;
    }

    // Obtener todos los productos de esta categoría y sus subcategorías
    public function obtenerTodosProductos()
    {
        $categoriaIds = $this->obtenerIdsCategoriasDescendientes();

        return Producto::whereIn('id_categoria', $categoriaIds)
            ->where('bActivo', true)
            ->get();
    }

    // Obtener IDs de todas las categorías descendientes (incluyéndose a sí misma)
    public function obtenerIdsCategoriasDescendientes()
    {
        $ids = [$this->id_categoria];

        foreach ($this->hijos as $hijo) {
            $ids = array_merge($ids, $hijo->obtenerIdsCategoriasDescendientes());
        }

        return $ids;
    }

    // Obtener categorías para select dropdown
    public static function paraSelect($excluirId = null)
    {
        $categorias = self::where('bActivo', true)
            ->orderBy('iOrden')
            ->orderBy('vNombre')
            ->get();

        return self::construirParaSelect($categorias, $excluirId);
    }

    private static function construirParaSelect($categorias, $excluirId = null, $padreId = null, $nivel = 0)
    {
        $resultado = [];

        foreach ($categorias as $categoria) {
            // Excluir la categoría y sus descendientes si se especifica
            if ($excluirId && $categoria->id_categoria == $excluirId) {
                continue;
            }

            if ($categoria->id_categoria_padre == $padreId) {
                $prefijo = str_repeat('-- ', $nivel);
                $resultado[$categoria->id_categoria] = $prefijo . $categoria->vNombre;

                // Agregar hijos recursivamente
                $hijos = self::construirParaSelect($categorias, $excluirId, $categoria->id_categoria, $nivel + 1);
                foreach ($hijos as $id => $nombre) {
                    $resultado[$id] = $nombre;
                }
            }
        }

        return $resultado;
    }
}
