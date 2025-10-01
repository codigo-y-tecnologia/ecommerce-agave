<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Etiqueta extends Model
{
    //
      use HasFactory;

   protected $table = 'tbl_etiquetas';
    protected $primaryKey = 'id_etiqueta';
    
    protected $fillable = [
        'vNombre',
        'tDescripcion'
    ];

    public $timestamps = false; 

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'tbl_producto_etiquetas', 'id_etiqueta', 'id_producto');
    }
}
