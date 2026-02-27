<?php

namespace App\Services\Checkout;

use App\Models\{Carrito, CheckoutSnapshot};
use Illuminate\Support\Facades\DB;
use App\Services\Stock\ReservarStockService;
use App\Services\Cupones\ReservarCuponService;
use App\Services\Checkout\CalcularTotalesService;
use App\Services\Checkout\CalcularDescuentoService;

class CrearPagoDesdeCarritoService
{
    public function ejecutar(Carrito $carrito, ?string $codigoCupon): array
    {
        return DB::transaction(function () use ($carrito, $codigoCupon) {

            // 1. Reservar stock
            app(ReservarStockService::class)->ejecutar($carrito);

            // 2. Reservar cupón
            $cupon = app(ReservarCuponService::class)
                ->ejecutar($carrito, $codigoCupon);

            // 3. Calcular totales
            [$subtotal, $impuestos, $total] = app(CalcularTotalesService::class)
                ->ejecutar($carrito);

            $envioBase = $total >= 1500 ? 0 : 150;

            [$descuento, $envio] = app(CalcularDescuentoService::class)
                ->ejecutar($cupon, $total, $envioBase);

            $totalFinal = max(0, $total - $descuento + $envio);


            // 4. Guardar snapshot
            $snapshot = CheckoutSnapshot::updateOrCreate(
                ['id_carrito' => $carrito->id_carrito],
                [
                    'subtotal'     => $subtotal,
                    'impuestos'    => $impuestos,
                    'envio'        => $envio,
                    'descuento'    => $descuento,
                    'total_final'  => $totalFinal,
                    'payment_session' => null,
                ]
            );

            // 5. Retornar datos para el proceso de pago
            return [
                'snapshot'   => $snapshot,
                'cupon'      => $cupon,
                'subtotal'   => $subtotal,
                'impuestos'  => $impuestos,
                'envio'      => $envio,
                'descuento'  => $descuento,
                'totalFinal' => $totalFinal,
            ];
        });
    }
}
