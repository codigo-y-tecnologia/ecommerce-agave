<?php

namespace App\Helpers;

use App\Models\Carrito;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Services\Stock\LiberarReservaPorCarritoService;

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

            // 1 Buscar carrito activo
            $carrito = Carrito::where('id_usuario', Auth::id())
                ->where('eEstado', 'activo')
                ->first();

            if ($carrito) {
                return $carrito;
            }

            // 2 Buscar carrito reservado
            $reservado = Carrito::where('id_usuario', Auth::id())
                ->where('eEstado', 'reservado')
                ->first();

            if ($reservado) {
                // 🔓 liberar reserva
                app(LiberarReservaPorCarritoService::class)->ejecutar($reservado);

                $reservado->eEstado = 'activo';
                $reservado->save();

                return $reservado;
            }

            return Carrito::create(
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

        $carrito = Carrito::where('vGuest_token', $guestToken)
            ->where('eEstado', 'activo')
            ->first();

        if ($carrito) {
            return $carrito;
        }

        $reservado = Carrito::where('vGuest_token', $guestToken)
            ->where('eEstado', 'reservado')
            ->first();

        if ($reservado) {
            app(LiberarReservaPorCarritoService::class)
                ->ejecutar($reservado);

            $reservado->eEstado = 'activo';
            $reservado->save();

            return $reservado;
        }

        return Carrito::create(
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
