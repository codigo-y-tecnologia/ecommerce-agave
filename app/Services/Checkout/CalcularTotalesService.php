<?php

namespace App\Services\Checkout;

use App\Models\Carrito;

class CalcularTotalesService
{
    public function ejecutar(Carrito $carrito): array
    {
        $subtotal = 0;
        $totalFinal = 0;
        $totalImpuestos = 0;
        $impuestosPorTipo = [];

        foreach ($carrito->detalles as $detalle) {

            $cantidad = $detalle->cantidad;

            // Usar directamente el precio final
            $precio_final = $detalle->dPrecio_unitario;

            $totalFinal += $precio_final * $cantidad;
        }

        return [
            'subtotal' => $totalFinal,
            'total_impuestos' => $totalImpuestos,
            'total' => $totalFinal,
            'impuestos_por_tipo' => $impuestosPorTipo,
            'subtotal_con_impuestos' => $totalFinal,
        ];
    }
}
