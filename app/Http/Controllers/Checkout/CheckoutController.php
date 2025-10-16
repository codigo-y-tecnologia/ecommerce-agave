<?php

namespace App\Http\Controllers\Checkout;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Carrito;
use App\Models\Pedido;
use App\Models\PedidoDetalle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CheckoutController extends Controller
{
    /**
     * Muestra el resumen del pedido antes de confirmar
     */
    public function index()
    {
        $usuario = Auth::user();

        $carrito = Carrito::where('id_usuario', $usuario->id_usuario)
            ->with('detalles.producto')
            ->first();

        if (!$carrito || $carrito->detalles->isEmpty()) {
            return redirect()->route('carrito.index')->with('warning', 'Tu carrito está vacío.');
        }

        $total = $carrito->detalles->sum(fn($d) => $d->cantidad * $d->precio_unitario);

        return view('checkout.index', compact('carrito', 'total'));
    }

    /**
     * Crea el pedido en base al carrito del usuario
     */
    public function store(Request $request)
    {
        $usuario = Auth::user();

        DB::transaction(function () use ($usuario) {
            $carrito = Carrito::where('id_usuario', $usuario->id_usuario)
                ->with('detalles')
                ->firstOrFail();

            if ($carrito->detalles->isEmpty()) {
                abort(400, 'El carrito está vacío.');
            }

            $total = $carrito->detalles->sum(fn($d) => $d->cantidad * $d->precio_unitario);

            // Crear pedido principal
            $pedido = Pedido::create([
                'id_usuario'    => $usuario->id_usuario,
                'id_direccion'  => null, // más adelante puedes enlazar la dirección
                'eEstado'       => 'pendiente',
                'dTotal'        => $total,
                'tFecha_pedido' => Carbon::now(),
            ]);

            // Insertar los detalles del pedido
            foreach ($carrito->detalles as $detalle) {
                PedidoDetalle::create([
                    'id_pedido'        => $pedido->id_pedido,
                    'id_producto'      => $detalle->id_producto,
                    'iCantidad'        => $detalle->cantidad,
                    'dPrecio_unitario' => $detalle->precio_unitario,
                ]);
            }

            // Vaciar carrito después de crear el pedido
            $carrito->detalles()->delete();
        });

        return redirect()->route('home')->with('success', '✅ Tu pedido fue creado exitosamente.');
    }

}
