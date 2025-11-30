<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    
    use HasFactory;

      protected $table = 'tbl_categorias';
      protected $primaryKey = 'id_categoria';
    
       public $timestamps = false;
    
    protected $fillable = [
        'vNombre',
        'tDescripcion',
        'id_parent',
        'iOrden',
        'bActivo'
    ];

    protected $casts = [
        'bActivo' => 'boolean'
    ];

    // Relación con productos
    public function productos()
    {
        return $this->hasMany(Producto::class, 'id_categoria');
    }

    // Relación padre (categoría padre)
    public function parent()
    {
        return $this->belongsTo(Categoria::class, 'id_parent');
    }

    // Relación hijos (subcategorías)
    public function children()
    {
        return $this->hasMany(Categoria::class, 'id_parent')->orderBy('iOrden')->orderBy('vNombre');
    }

    // Relación hijos recursiva (todas las subcategorías anidadas)
    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    // Obtener todas las categorías padres (jerarquía completa)
    public function getParentsAttribute()
    {
        $parents = collect([]);
        $parent = $this->parent;

        while (!is_null($parent)) {
            $parents->push($parent);
            $parent = $parent->parent;
        }

        return $parents->reverse();
    }

    // Obtener la ruta completa de la categoría
    public function getRutaCompletaAttribute()
    {
        $ruta = $this->vNombre;
        $parent = $this->parent;

        while (!is_null($parent)) {
            $ruta = $parent->vNombre . ' > ' . $ruta;
            $parent = $parent->parent;
        }

        return $ruta;
    }

    // Verificar si es categoría padre
    public function getEsPadreAttribute()
    {
        return $this->id_parent === null;
    }

    // Verificar si tiene hijos
    public function getTieneHijosAttribute()
    {
        return $this->children->count() > 0;
    }

    // Obtener todas las categorías hijas (IDs)
    public function getAllChildrenIdsAttribute()
    {
        $ids = [$this->id_categoria];
        
        foreach ($this->children as $child) {
            $ids = array_merge($ids, $child->allChildrenIds);
        }
        
        return $ids;
    }

    // Scope para categorías padre
    public function scopePadres($query)
    {
        return $query->whereNull('id_parent');
    }

    // Scope para categorías activas
    public function scopeActivas($query)
    {
        return $query->where('bActivo', true);
    }

    // Scope para ordenar
    public function scopeOrdenadas($query)
    {
        return $query->orderBy('iOrden')->orderBy('vNombre');
    }
}
