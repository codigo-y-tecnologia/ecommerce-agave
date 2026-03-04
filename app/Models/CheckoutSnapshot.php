<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CheckoutSnapshot extends Model
{
    use HasFactory;

    protected $table = 'tbl_checkout_snapshots';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id_carrito',
        'subtotal',
        'impuestos',
        'impuestos_por_tipo',
        'subtotal_con_impuestos',
        'envio',
        'descuento',
        'cupon_codigo',
        'cupon_tipo',
        'cupon_valor',
        'cupon_monto_aplicado',
        'total_final',
        'payment_session',
    ];

    /**
     * Relación con carrito
     */
    public function carrito()
    {
        return $this->belongsTo(Carrito::class, 'id_carrito', 'id_carrito');
    }

    /**
     * Casts monetarios seguros (string decimal)
     * Evita problemas de precisión con float
     */
    protected $casts = [
        'subtotal'     => 'decimal:2',
        'impuestos'    => 'decimal:2',
        'subtotal_con_impuestos' => 'decimal:2',
        'envio'        => 'decimal:2',
        'descuento'    => 'decimal:2',
        'cupon_valor' => 'decimal:2',
        'cupon_monto_aplicado' => 'decimal:2',
        'total_final'  => 'decimal:2',
        'impuestos_por_tipo' => 'array',
    ];
}
