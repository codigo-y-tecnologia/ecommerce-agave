<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cupon extends Model
{
    use HasFactory;

    protected $table = 'tbl_cupones';
    protected $primaryKey = 'id_cupon';
    public $timestamps = false;

    protected $fillable = [
        'vCodigo_cupon',
        'dDescuento',
        'dMonto_minimo',
        'eTipo',
        'dValido_desde',
        'dValido_hasta',
        'iUso_maximo',
        'iUsos_actuales',
        'iUsos_por_usuario',
        'bActivo',
    ];

    protected $casts = [
        'dDescuento'        => 'decimal:2',
        'dMonto_minimo'     => 'decimal:2',
        'iUso_maximo'       => 'integer',
        'iUsos_actuales'    => 'integer',
        'iUsos_por_usuario' => 'integer',
        'bActivo'           => 'boolean',
    ];

    public function usos()
    {
        return $this->hasMany(CuponUso::class, 'id_cupon', 'id_cupon');
    }

    public function reservas()
    {
        return $this->hasMany(CuponReserva::class, 'id_cupon', 'id_cupon');
    }

    /**
     * Scope: cupones activos y vigentes
     */
    public function scopeDisponible($query)
    {
        return $query
            ->where('bActivo', 1)
            ->where(function ($q) {
                $q->whereNull('dValido_desde')
                    ->orWhere('dValido_desde', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('dValido_hasta')
                    ->orWhere('dValido_hasta', '>=', now());
            });
    }

    /**
     * Verifica si aún tiene usos globales disponibles
     */
    public function tieneUsosDisponibles(): bool
    {
        if (is_null($this->iUso_maximo)) {
            return true;
        }

        return $this->iUsos_actuales < $this->iUso_maximo;
    }
}
