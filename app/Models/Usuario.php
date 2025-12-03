<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'tbl_usuarios';
    protected $primaryKey = 'id_usuario';
    
    public $timestamps = true;

    protected $fillable = [
        'vNombre',
        'vApaterno',
        'vAmaterno',
        'vEmail',
        'vPassword',
        'dFecha_nacimiento',
        'eRol',
        'remember_token',
        'api_token'
    ];

    protected $hidden = [
        'remember_token',
        'api_token'
    ];

    public function getAuthPassword()
    {
        return $this->vPassword;
    }

    public function getEmailForPasswordReset()
    {
        return $this->vEmail;
    }

    protected $casts = [
        'dFecha_nacimiento' => 'date',
        'email_verified_at' => 'datetime',
    ];

    public function favoritos()
    {
        return $this->hasMany(Favorito::class, 'id_usuario');
    }

    public function getNombreCompletoAttribute()
    {
        return $this->vNombre . ' ' . $this->vApaterno . ($this->vAmaterno ? ' ' . $this->vAmaterno : '');
    }

    public function esAdmin()
    {
        return $this->eRol === 'admin';
    }

    public function esCliente()
    {
        return $this->eRol === 'cliente';
    }
}