<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DireccionGuest extends Model
{

    use HasFactory;

    protected $table = 'tbl_direcciones_guest';
    protected $primaryKey = 'id_direccion_guest';
    public $timestamps = false;

    protected $fillable = [
        'vGuest_token',
        'vNombre',
        'vApaterno',
        'vAmaterno',
        'vEmail',
        'vTelefono_contacto',
        'vRFC',
        'vCalle',
        'vNumero_exterior',
        'vNumero_interior',
        'vColonia',
        'vCodigo_postal',
        'vCiudad',
        'vEstado',
        'vEntre_calle_1',
        'vEntre_calle_2',
        'tReferencias',
        'bDireccion_principal',
    ];

    protected $casts = [
        'bDireccion_principal' => 'boolean',
    ];

    /**
     * Scope: obtener direcciones por guest token
     */
    public function scopeByGuestToken($query, string $guestToken)
    {
        return $query->where('vGuest_token', $guestToken);
    }

    /**
     * Scope: dirección principal del invitado
     */
    public function scopePrincipal($query)
    {
        return $query->where('bDireccion_principal', true);
    }
}
