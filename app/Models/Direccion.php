<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Direccion extends Model
{
    use HasFactory;

    protected $table = 'tbl_direcciones';
    protected $primaryKey = 'id_direccion';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'vTelefono_contacto',
        'vCalle',
        'vNumero_exterior',
        'vNumero_interior',
        'vColonia',
        'vCodigo_postal',
        'vCiudad',
        'vEstado',
        'vEntre_calle_1',
        'vEntre_calle_2',
        'tReferencias',
        'bDireccion_principal',
    ];

    // Relaciones
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
}
