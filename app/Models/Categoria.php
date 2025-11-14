<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    //
     use HasFactory;

      protected $table = 'tbl_categorias';
      protected $primaryKey = 'id_categoria';
    
       public $timestamps = false;
    
    protected $fillable = [
        'vNombre',
        'tDescripcion'
    ];

    public function productos()
    {
        return $this->hasMany(Producto::class, 'id_categoria');
    }
}
