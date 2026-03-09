<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class CuponReserva extends Model
{
    use HasFactory;

    protected $table = 'tbl_cupon_reservas';
    protected $primaryKey = 'id_cupon_reserva';

    protected $fillable = [
        'id_cupon',
        'id_carrito',
        'session_id',
        'expires_at',
    ];

    protected $casts = [
        'id_cupon'   => 'integer',
        'id_carrito' => 'integer',
        'expires_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function cupon()
    {
        return $this->belongsTo(Cupon::class, 'id_cupon', 'id_cupon');
    }

    public function carrito()
    {
        return $this->belongsTo(Carrito::class, 'id_carrito', 'id_carrito');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes útiles para concurrencia
    |--------------------------------------------------------------------------
    */

    /**
     * Reservas aún válidas (no expiradas)
     */
    public function scopeActivas($query)
    {
        return $query->where('expires_at', '>', now());
    }

    /**
     * Reservas expiradas
     */
    public function scopeExpiradas($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers de dominio
    |--------------------------------------------------------------------------
    */

    /**
     * Verifica si la reserva sigue vigente
     */
    public function estaActiva(): bool
    {
        return $this->expires_at instanceof Carbon
            && $this->expires_at->isFuture();
    }
}
