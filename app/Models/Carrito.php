<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrito extends Model
{
    protected $table = 'tbl_carritos';
    protected $primaryKey = 'id_carrito';
    public $timestamps = true;

    const CREATED_AT = 'tFecha_creacion';
    const UPDATED_AT = 'tFecha_actualizacion';

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

    public function stockReservas()
    {
        return $this->hasMany(StockReserva::class, 'id_carrito');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers de estado
    |--------------------------------------------------------------------------
    */

    public function marcarComoActivo(): void
    {
        $this->update(['eEstado' => 'activo']);
    }

    public function marcarComoReservado(): void
    {
        $this->update(['eEstado' => 'reservado']);
    }

    public function marcarComoConvertido(): void
    {
        $this->update(['eEstado' => 'convertido']);
    }

    public function marcarComoAbandonado(): void
    {
        $this->update(['eEstado' => 'abandonado']);
    }
}
