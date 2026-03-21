<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Impuesto extends Model
{
    use HasFactory;

    protected $table = 'tbl_impuestos';
    protected $primaryKey = 'id_impuesto';

    public $timestamps = false;

    protected $fillable = [
        'vNombre',
        'eTipo',
        'dPorcentaje',
        'bActivo',
        'dFecha_creacion'
    ];

    protected $casts = [
        'dPorcentaje' => 'decimal:2',
        'bActivo' => 'boolean',
        'dFecha_creacion' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($impuesto) {
            if (empty($impuesto->dFecha_creacion)) {
                $impuesto->dFecha_creacion = now();
            }
        });
    }

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'tbl_producto_impuestos', 'id_impuesto', 'id_producto')
                    ->withTimestamps();
    }

    public function variaciones()
    {
        return $this->hasMany(ProductoVariacion::class, 'id_impuesto');
    }

    public function scopeActivos($query)
    {
        return $query->where('bActivo', true);
    }

    public function scopeDeTipo($query, $tipo)
    {
        return $query->where('eTipo', $tipo);
    }

    public function getTipoFormateadoAttribute()
    {
        switch ($this->eTipo) {
            case 'IVA':
                return '<span class="badge bg-primary">IVA</span>';
            case 'ISR':
                return '<span class="badge bg-warning text-dark">ISR</span>';
            default:
                return '<span class="badge bg-secondary">' . $this->eTipo . '</span>';
        }
    }

    public function getPorcentajeFormateadoAttribute()
    {
        return number_format($this->dPorcentaje, 2) . '%';
    }

    public function getNombreCompletoAttribute()
    {
        return $this->vNombre . ' (' . $this->eTipo . ' - ' . $this->porcentajeFormateado . ')';
    }

    public function getEstadoBadgeAttribute()
    {
        return $this->bActivo 
            ? '<span class="badge bg-success">Activo</span>'
            : '<span class="badge bg-danger">Inactivo</span>';
    }

    // Método para calcular IVA
    public static function calcularIVA($precio)
    {
        $iva = self::where('vNombre', 'IVA')
            ->where('bActivo', true)
            ->first();
        
        if ($iva) {
            return $precio * ($iva->dPorcentaje / 100);
        }
        
        // Si no existe IVA, usar 16% por defecto
        return $precio * 0.16;
    }

    // Método para calcular ISR (solo informativo)
    public static function calcularISR($utilidad)
    {
        $isr = self::where('vNombre', 'ISR')
            ->where('bActivo', true)
            ->first();
        
        if ($isr) {
            return $utilidad * ($isr->dPorcentaje / 100);
        }
        
        // Si no existe ISR, usar 30% por defecto como ejemplo
        return $utilidad * 0.30;
    }
}