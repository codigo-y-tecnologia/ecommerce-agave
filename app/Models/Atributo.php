<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atributo extends Model
{
    use HasFactory;

    protected $table = 'tbl_atributos';
    protected $primaryKey = 'id_atributo';

    public $timestamps = false;

    protected $fillable = [
        'vNombre',
        'tDescripcion',
        'eTipo',
        'vLabel',
        'vPlaceholder',
        'bRequerido',
        'iOrden',
        'bActivo'
    ];

    protected $casts = [
        'bRequerido' => 'boolean',
        'bActivo' => 'boolean'
    ];

    public function opciones()
    {
        return $this->hasMany(AtributoOpcion::class, 'id_atributo');
    }

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'tbl_producto_atributos', 'id_atributo', 'id_producto')
                    ->withPivot('vValor', 'id_opcion');
    }

    public function opcionesActivas()
    {
        return $this->opciones()->orderBy('iOrden');
    }
}