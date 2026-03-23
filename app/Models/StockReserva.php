<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class StockReserva extends Model
{
    protected $table = 'tbl_stock_reservas';
    protected $primaryKey = 'id_stock_reserva';

    protected $fillable = [
        'id_producto',
        'id_variacion',
        'id_carrito',
        'session_id',
        'cantidad',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    public function carrito()
    {
        return $this->belongsTo(Carrito::class, 'id_carrito');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes elegantes
    |--------------------------------------------------------------------------
    */

    /**
     * Reservas que aún no se han consumido (no ligadas a Stripe)
     */
    public function scopePendientes(Builder $query): Builder
    {
        return $query->whereNull('session_id');
    }

    /**
     * Reservas ya expiradas
     */
    public function scopeExpiradas(Builder $query): Builder
    {
        return $query->where('expires_at', '<', now())
            ->whereNull('pagada_at');
    }

    /**
     * Reservas activas (válidas)
     */
    public function scopeActivas(Builder $query): Builder
    {
        return $query
            ->pendientes()
            ->where('expires_at', '>', now());
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers de dominio
    |--------------------------------------------------------------------------
    */

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function markAsConsumed(string $sessionId): void
    {
        $this->update([
            'session_id' => $sessionId,
        ]);
    }
}
