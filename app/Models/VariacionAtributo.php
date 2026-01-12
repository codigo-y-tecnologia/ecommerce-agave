<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariacionAtributo extends Model
{
    use HasFactory;

    protected $table = 'tbl_variacion_atributos';
    protected $primaryKey = 'id_variacion_atributo';

    // Desactivar timestamps
    public $timestamps = false;

    protected $fillable = [
        'id_variacion',
        'id_atributo',
        'id_atributo_valor'
    ];

    public function variacion()
    {
        return $this->belongsTo(ProductoVariacion::class, 'id_variacion');
    }

    public function atributo()
    {
        return $this->belongsTo(Atributo::class, 'id_atributo');
    }

    public function valor()
    {
        return $this->belongsTo(AtributoValor::class, 'id_atributo_valor');
    }
}