<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{

    use Notifiable;

    protected $table = 'tbl_usuarios';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false;

    protected $fillable = [
        'vNombre',
        'vApaterno',
        'vAmaterno',
        'vEmail',
        'vPassword',
        'remember_token',
        'api_token',
        'dFecha_nacimiento',
        'eRol'
    ];

    protected $hidden = [
        'vPassword',
        'remember_token',
        'api_token'
    ];

    // Esto le dice a Laravel qué columna usar como "email" y "password"
    public function getAuthPassword()
    {
        return $this->vPassword;
    }

    // public function getAuthIdentifierName()
    // {
    //     return 'vEmail';
    // }

    // // Relación: un usuario tiene muchos carritos
    // public function carritos()
    // {
    //     return $this->hasMany(Carrito::class, 'id_usuario', 'id_usuario');
    // }
}
