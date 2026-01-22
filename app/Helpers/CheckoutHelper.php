<?php

use App\Models\Carrito;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

if (!function_exists('carritoActual')) {
    function carritoActual(): Carrito
    {
        // Usuario autenticado
        if (Auth::check()) {
            return Carrito::firstOrCreate([
                'id_usuario' => Auth::user()->id_usuario,
                'eEstado' => 'activo'
            ]);
        }

        // Invitado con carrito en sesión
        if (Session::has('carrito_id')) {
            $carrito = Carrito::find(Session::get('carrito_id'));
            if ($carrito) {
                return $carrito;
            }
        }

        // Crear carrito invitado
        $carrito = Carrito::create([
            'id_usuario' => null,
            'eEstado' => 'activo'
        ]);

        Session::put('carrito_id', $carrito->id_carrito);

        return $carrito;
    }
}

if (!function_exists('autoRegistrarEnCheckout')) {
    function autoRegistrarEnCheckout(): bool
    {
        return (bool) DB::table('tbl_configuracion')
            ->where('clave', 'checkout_auto_register')
            ->value('valor');
    }
}
