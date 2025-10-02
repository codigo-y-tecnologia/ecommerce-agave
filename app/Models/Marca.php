<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    //
    use HasFactory;

    protected $table = 'tbl_marcas';
    protected $primaryKey = 'id_marca';
    
    protected $fillable = [
        'vNombre',
        'tDescripcion'
    ];

    public $timestamps = false; 

    public function productos()
    {
        return $this->hasMany(Producto::class, 'id_marca');
    }
}
