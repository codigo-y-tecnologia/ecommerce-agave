<?php

namespace App\Services\Checkout;

use Illuminate\Support\Facades\DB;
use App\Services\Stock\ConsumirReservaService;
use App\Services\Cupones\ConsumirCuponService;
use App\Services\Checkout\CrearPedidoDesdeSnapshotService;
use Exception;
use App\Models\{
    Carrito,
    CheckoutSnapshot,
    Pedido,
    Venta,
    Pago
};

class FinalizarPedidoService
{
    public function ejecutar(string $paymentSession, string $metodo, string $referencia): void
    {
        DB::transaction(function () use ($paymentSession, $metodo, $referencia) {

            $snapshot = CheckoutSnapshot::where('payment_session', $paymentSession)
                ->lockForUpdate()
                ->first();

            if (!$snapshot) {
                throw new Exception('Snapshot de checkout no encontrado');
            }

            // Obtenemos el carrito asociado al snapshot
            $carrito = Carrito::where('id_carrito', $snapshot->id_carrito)
                ->lockForUpdate()
                ->first();

            if (!$carrito) {
                throw new Exception('Carrito no encontrado.');
            }

            if ($carrito->eEstado !== 'activo') {
                return;
            }

            // Consumir stock
            app(ConsumirReservaService::class)->ejecutar($paymentSession);

            // Crear pedido/venta/pago usando snapshot
            $pedido = app(CrearPedidoDesdeSnapshotService::class)
                ->ejecutar($snapshot, $metodo, $referencia);

            // Consumir cupón
            app(ConsumirCuponService::class)->ejecutar(
                $paymentSession,
                $pedido->id_venta,
                $pedido->id_usuario,
                $pedido->guest_token
            );
        });
    }
}
