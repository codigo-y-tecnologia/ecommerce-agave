<?php

namespace App\Helpers;

use App\Models\Carrito;
use Illuminate\Support\Facades\Auth;

class CheckoutHelper
{
    /**
     * Obtiene el carrito SOLO si existe
     * (USAR EN CHECKOUT, PAGOS, NOTIFICACIONES)
     */
    public static function carritoCheckout(): ?Carrito
    {
        if (Auth::check()) {
            return Carrito::where('id_usuario', Auth::id())
                ->where('eEstado', 'activo')
                ->with(['detalles.producto.impuestos'])
                ->first();
        }

        if (!session()->has('guest_token')) {
            return null;
        }

        return Carrito::where('vGuest_token', session('guest_token'))
            ->where('eEstado', 'activo')
            ->with(['detalles.producto.impuestos'])
            ->first();
    }
}
