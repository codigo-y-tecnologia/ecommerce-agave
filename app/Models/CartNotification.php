<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CartNotification extends Model
{
    use HasFactory;

    protected $table = 'tbl_cart_notifications';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id_carrito',
        'canal',
        'etapa',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function carrito()
    {
        return $this->belongsTo(Carrito::class, 'id_carrito');
    }
}
