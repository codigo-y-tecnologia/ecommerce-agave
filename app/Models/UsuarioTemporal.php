<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UsuarioTemporal extends Model
{
    protected $table = 'tbl_usuarios_temporales';
    protected $primaryKey = 'id_temp_usuario';
    
    public $timestamps = false;

    protected $fillable = [
        'session_id',
        'vToken',
        'tFecha_expiracion'
    ];

    protected $casts = [
        'tFecha_creacion' => 'datetime',
        'tFecha_expiracion' => 'datetime'
    ];

    /**
     * Generar un nuevo token para invitado
     */
    public static function generarToken()
    {
        return Str::random(60);
    }
}