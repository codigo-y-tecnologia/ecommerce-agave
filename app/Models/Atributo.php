<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atributo extends Model
{
    use HasFactory;

    protected $table = 'tbl_atributos';
    protected $primaryKey = 'id_atributo';
    
    // DESACTIVA LOS TIMESTAMPS AUTOMÁTICOS
    public $timestamps = false;
    
    protected $fillable = [
        'vNombre',
        'vSlug',
        'tDescripcion',
        'bActivo'
    ];

    protected $casts = [
        'bActivo' => 'boolean'
    ];

    public function valores()
    {
        return $this->hasMany(AtributoValor::class, 'id_atributo');
    }

    public function valoresActivos()
    {
        return $this->hasMany(AtributoValor::class, 'id_atributo')
                    ->where('bActivo', true)
                    ->orderBy('iOrden');
    }
}