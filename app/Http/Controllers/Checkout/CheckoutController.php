<?php

namespace App\Http\Controllers\Checkout;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{Carrito, Pedido, PedidoDetalle, Direccion, Cupon, CuponUso, Pago, Venta, DetalleVenta};
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

        $carrito = Carrito::with('detalles.producto')
            ->where('id_usuario', $usuario->id_usuario)
            ->first();

        if (!$carrito || $carrito->detalles->isEmpty()) {
            return redirect()->route('carrito.index')->with('warning', 'Tu carrito está vacío.');
        }

        $direcciones = Direccion::where('id_usuario', $usuario->id_usuario)->get();

        $subtotal = $carrito->detalles->sum(fn($d) => $d->cantidad * $d->precio_unitario);

        return view('checkout.index', compact('carrito', 'subtotal', 'direcciones'));
    }

    /**
     * Crea el pedido en base al carrito del usuario
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_direccion' => 'required|exists:tbl_direcciones,id_direccion',
            'metodo_pago'  => 'required|in:paypal,stripe',
            'codigo_cupon' => 'nullable|string|max:50',
        ]);

        $usuario = Auth::user();

        DB::transaction(function () use ($usuario, $request) {
            $carrito = Carrito::with('detalles.producto')->where('id_usuario', $usuario->id_usuario)->firstOrFail();

            // 🧾 Calcular subtotal
            $subtotal = $carrito->detalles->sum(fn($d) => $d->cantidad * $d->precio_unitario);
            $descuento = 0;

            // 🎟 Aplicar cupón si existe
            if ($request->filled('codigo_cupon')) {
                $cupon = Cupon::where('vCodigo_cupon', $request->codigo_cupon)
                    ->where('bActivo', 1)
                    ->whereDate('dValido_desde', '<=', now())
                    ->whereDate('dValido_hasta', '>=', now())
                    ->first();

                if ($cupon) {
                    $descuento = $cupon->eTipo === 'porcentaje'
                        ? $subtotal * ($cupon->dDescuento / 100)
                        : $cupon->dDescuento;
                }
            }

            $total = max(0, $subtotal - $descuento);

            // 📦 Crear pedido
            $pedido = Pedido::create([
                'id_usuario' => $usuario->id_usuario,
                'id_direccion' => $request->id_direccion,
                'eEstado' => 'pendiente',
                'dTotal' => $total,
                'tFecha_pedido' => Carbon::now(),
            ]);

            foreach ($carrito->detalles as $item) {
                PedidoDetalle::create([
                    'id_pedido' => $pedido->id_pedido,
                    'id_producto' => $item->id_producto,
                    'iCantidad' => $item->cantidad,
                    'dPrecio_unitario' => $item->precio_unitario,
                ]);
            }

            // 💳 Registrar pago
            $pago = Pago::create([
                'id_pedido' => $pedido->id_pedido,
                'eMetodo_pago' => $request->metodo_pago,
                'dMonto' => $total,
                'eEstado' => 'pendiente', // luego se actualiza al confirmarse
            ]);

            // 🛒 Vaciar carrito
            $carrito->detalles()->delete();

            // 🎟 Registrar uso de cupón
            if (!empty($cupon)) {
                CuponUso::create([
                    'id_cupon' => $cupon->id_cupon,
                    'id_venta' => null, // se vincula al completar la venta
                    'tFecha_uso' => now(),
                ]);
            }
        });

        return redirect()->route('home')->with('success', '✅ Pedido realizado con éxito. Te notificaremos cuando se confirme el pago.');
    }

}
