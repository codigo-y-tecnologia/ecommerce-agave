<?php

namespace App\Helpers;

use App\Models\Carrito;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CarritoHelper
{
    /**
     * Obtiene o crea el carrito actual
     * - Usuario autenticado: por id_usuario (1 activo garantizado por triggers)
     * - Invitado: por vGuest_token en sesión
     */
    public static function carritoActual(): Carrito
    {
        // ========================
        // USUARIO AUTENTICADO
        // ========================
        if (Auth::check()) {

            return Carrito::firstOrCreate(
                [
                    'id_usuario' => Auth::id(),
                    'eEstado'    => 'activo',
                ],
                [
                    // Importante: guest_token debe ser null
                    'vGuest_token' => null,
                    'vEmail_invitado' => null,
                ]
            );
        }

        // ========================
        // USUARIO INVITADO
        // ========================

        // Generar token si no existe en sesión
        if (!session()->has('guest_token')) {
            session(['guest_token' => (string) Str::uuid()]);
        }

        $guestToken = session('guest_token');

        return Carrito::firstOrCreate(
            [
                'vGuest_token' => $guestToken,
                'eEstado'      => 'activo',
            ],
            [
                'id_usuario' => null,
            ]
        );
    }

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
