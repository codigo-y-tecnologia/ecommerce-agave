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
            $totales = app(CalcularTotalesService::class)
                ->ejecutar($carrito);

            $subtotal = $totales['subtotal'];
            $impuestos = $totales['total_impuestos'];
            $total = $totales['total'];
            $impuestosPorTipo = $totales['impuestos_por_tipo'];
            $subtotalConImpuestos = $totales['subtotal_con_impuestos'];

            $envioBase = $total >= 1500 ? 0 : 150;

            $resultado = app(CalcularDescuentoService::class)
                ->ejecutar($cupon, $total, $envioBase);

            $descuento = $resultado['descuento'];
            $envio = $resultado['envio'];

            $totalFinal = max(0, $total - $descuento + $envio);

            // 4. Guardar snapshot
            $snapshot = CheckoutSnapshot::updateOrCreate(
                ['id_carrito' => $carrito->id_carrito],
                [
                    'subtotal'     => $subtotal,
                    'impuestos'    => $impuestos,
                    'impuestos_por_tipo' => $impuestosPorTipo,
                    'subtotal_con_impuestos' => $subtotalConImpuestos,
                    'envio'        => $envio,
                    'descuento'    => $descuento,
                    'cupon_codigo' => $cupon?->vCodigo_cupon,
                    'cupon_tipo' => $cupon?->eTipo,
                    'cupon_valor' => $cupon?->dDescuento,
                    'cupon_monto_aplicado' => $descuento,
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
