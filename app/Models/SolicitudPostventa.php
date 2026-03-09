<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudPostventa extends Model
{
    protected $table = 'tbl_solicitudes_postventa';
    protected $primaryKey = 'id_solicitud';

    protected $fillable = [
        'id_pedido',
        'id_usuario',
        'eTipo',
        'eEstado',
        'vMotivo',
        'tRespuesta_admin'
    ];

    protected $casts = [
        'eTipo' => 'string',
        'eEstado' => 'string',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }
}
