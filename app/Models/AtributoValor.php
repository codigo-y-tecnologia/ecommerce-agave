<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AtributoValor extends Model
{
    use HasFactory;

    protected $table = 'tbl_atributo_valores';
    protected $primaryKey = 'id_atributo_valor';
    
    // DESACTIVA LOS TIMESTAMPS AUTOMÁTICOS
    public $timestamps = false;
    
    protected $fillable = [
        'id_atributo',
        'vValor',
        'vSlug',
        'dPrecio_extra',
        'iStock',
        'iOrden',
        'bActivo',
        'vHexColor',
        'vImagenUrl'
    ];

    protected $casts = [
        'dPrecio_extra' => 'decimal:2',
        'iStock' => 'integer',
        'iOrden' => 'integer',
        'bActivo' => 'boolean'
    ];

    public function atributo()
    {
        return $this->belongsTo(Atributo::class, 'id_atributo');
    }
}