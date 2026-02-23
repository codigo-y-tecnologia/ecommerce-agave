<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Impuesto extends Model
{
    use HasFactory;

    protected $table = 'tbl_impuestos';
    protected $primaryKey = 'id_impuesto';

    /**
     * Desactivar timestamps automáticos de Laravel
     * porque la tabla usa dFecha_creacion en lugar de created_at/updated_at
     */
    public $timestamps = false;

    protected $fillable = [
        'vNombre',
        'eTipo',
        'dPorcentaje',
        'tDescripcion',
        'bActivo',
        'dFecha_creacion'
    ];

    protected $casts = [
        'dPorcentaje' => 'decimal:2',
        'bActivo' => 'boolean',
        'dFecha_creacion' => 'datetime'
    ];

    /**
     * Boot method para establecer fecha de creación automáticamente
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($impuesto) {
            if (empty($impuesto->dFecha_creacion)) {
                $impuesto->dFecha_creacion = now();
            }
        });
    }

    /**
     * Relación muchos a muchos con productos
     */
    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'tbl_producto_impuestos', 'id_impuesto', 'id_producto')
                    ->withTimestamps();
    }

    /**
     * Scope para impuestos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('bActivo', true);
    }

    /**
     * Scope para filtrar por tipo de impuesto
     */
    public function scopeDeTipo($query, $tipo)
    {
        return $query->where('eTipo', $tipo);
    }

    /**
     * Obtener el tipo de impuesto con formato
     */
    public function getTipoFormateadoAttribute()
    {
        switch ($this->eTipo) {
            case 'IVA':
                return '<span class="badge bg-primary">IVA</span>';
            case 'IEPS':
                return '<span class="badge bg-warning text-dark">IEPS</span>';
            case 'OTRO':
                return '<span class="badge bg-secondary">OTRO</span>';
            default:
                return '<span class="badge bg-dark">' . $this->eTipo . '</span>';
        }
    }

    /**
     * Obtener el porcentaje formateado
     */
    public function getPorcentajeFormateadoAttribute()
    {
        return number_format($this->dPorcentaje, 2) . '%';
    }

    /**
     * Obtener el nombre completo del impuesto con porcentaje
     */
    public function getNombreCompletoAttribute()
    {
        return $this->vNombre . ' (' . $this->eTipo . ' - ' . $this->porcentajeFormateado . ')';
    }

    /**
     * Obtener el badge de estado
     */
    public function getEstadoBadgeAttribute()
    {
        return $this->bActivo 
            ? '<span class="badge bg-success">Activo</span>'
            : '<span class="badge bg-danger">Inactivo</span>';
    }
}