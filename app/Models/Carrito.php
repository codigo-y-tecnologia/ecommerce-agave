<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrito extends Model
{
    protected $table = 'tbl_carritos';
    protected $primaryKey = 'id_carrito';
    public $timestamps = false;

    protected $fillable = ['id_usuario', 'vGuest_token', 'vEmail_invitado', 'eEstado'];

    // Relación con detalles
    public function detalles()
    {
        return $this->hasMany(CarritoDetalle::class, 'id_carrito', 'id_carrito');
    }


    //Relación: un carrito pertenece a un usuario

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function reminders()
    {
        return $this->hasMany(
            CartNotification::class,
            'id_carrito',
            'id_carrito'
        );
    }
}
