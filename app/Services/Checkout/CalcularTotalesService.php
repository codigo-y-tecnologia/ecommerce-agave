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

        // foreach ($carrito->detalles as $detalle) {

        //     $producto = $detalle->producto;
        //     $cantidad = $detalle->cantidad;
        //     $precio_base = $producto->dPrecio_venta;

        //     // Obtener impuestos
        //     $impuestos = $producto->impuestos->where('bActivo', 1);

        //     $ieps = 0;
        //     $iva = 0;

        //     foreach ($impuestos as $imp) {
        //         if ($imp->eTipo === 'IEPS') {
        //             $ieps = $precio_base * ($imp->dPorcentaje / 100);
        //         }
        //     }

        //     foreach ($impuestos as $imp) {
        //         if ($imp->eTipo === 'IVA') {
        //             $iva = ($precio_base + $ieps) * ($imp->dPorcentaje / 100);
        //         }
        //     }

        //     // Impuestos por tipo
        //     if ($ieps > 0) {
        //         $impuestosPorTipo['IEPS'] = ($impuestosPorTipo['IEPS'] ?? 0) + ($ieps * $cantidad);
        //     }
        //     if ($iva > 0) {
        //         $impuestosPorTipo['IVA'] = ($impuestosPorTipo['IVA'] ?? 0) + ($iva * $cantidad);
        //     }

        //     // Totales por cantidad
        //     $subtotal_producto = $precio_base * $cantidad;
        //     $impuestos_producto = ($ieps + $iva) * $cantidad;

        //     $subtotal += $subtotal_producto;
        //     $totalImpuestos += $impuestos_producto;
        // }

        // Subtotal CON impuestos 
        // $subtotalConImpuestos = $subtotal + $totalImpuestos;

        // $total = $subtotal + $totalImpuestos;

        return [
            'subtotal' => $totalFinal,
            'total_impuestos' => $totalImpuestos,
            'total' => $totalFinal,
            'impuestos_por_tipo' => $impuestosPorTipo,
            'subtotal_con_impuestos' => $totalFinal,
        ];
    }
}
