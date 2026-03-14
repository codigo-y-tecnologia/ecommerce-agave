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

    /**
     * Crear o obtener usuario temporal basado en session_id
     */
    public static function obtenerOcrear($sessionId)
    {
        $usuarioTemporal = self::where('session_id', $sessionId)->first();
        
        if (!$usuarioTemporal) {
            $usuarioTemporal = self::create([
                'session_id' => $sessionId,
                'vToken' => self::generarToken(),
                'tFecha_expiracion' => now()->addDays(30)
            ]);
        }
        
        return $usuarioTemporal;
    }

    /**
     * Relación con favoritos
     */
    public function favoritos()
    {
        return $this->hasMany(Favorito::class, 'id_usuario_temporal', 'id_temp_usuario');
    }
}