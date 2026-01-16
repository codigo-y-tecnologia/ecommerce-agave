<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

class Usuario extends Authenticatable implements CanResetPassword
{

    use Notifiable, CanResetPasswordTrait, HasRoles;

    protected $guard_name = 'web';

    protected $table = 'tbl_usuarios';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false;

    protected $fillable = [
        'vNombre',
        'vApaterno',
        'vAmaterno',
        'vEmail',
        'email_pending',
        'email_verification_token',
        'email_verified_at',
        'is_verified',
        'verification_token',
        'vPassword',
        'remember_token',
        'api_token',
        'dFecha_nacimiento',
        'eRol'
    ];

    protected $hidden = [
        'email_verification_token',
        'verification_token',
        'vPassword',
        'remember_token',
        'api_token'
    ];

    public function getEmailForPasswordReset()
    {
        return $this->vEmail;
    }

    // Esto le dice a Laravel qué columna usar como "email" y "password"
    public function getAuthPassword()
    {
        return $this->vPassword;
    }

    public function routeNotificationForMail($notification = null)
    {
        return $this->vEmail;
    }

    // Método para generar token de verificación
    public function generateVerificationToken()
    {
        $this->verification_token = Str::random(60);
        $this->save();

        return $this->verification_token;
    }

    // Método para verificar el email
    public function markEmailAsVerified()
    {
        $this->email_verified_at = now();
        $this->is_verified = true;
        $this->verification_token = null;
        $this->save();
    }

    // Verificar si el email está verificado
    public function hasVerifiedEmail()
    {
        return $this->is_verified;
    }


    // public function getAuthIdentifierName()
    // {
    //     return 'vEmail';
    // }

    //Relación: un usuario tiene muchos carritos
    public function carritos()
    {
        return $this->hasMany(Carrito::class, 'id_usuario', 'id_usuario');
    }
}
