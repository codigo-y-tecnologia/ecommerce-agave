<?php

namespace App\Services\Checkout;

use App\Models\Carrito;

class CalcularTotalesService
{
    public function ejecutar(Carrito $carrito): array
    {
        $subtotal = 0;
        $totalImpuestos = 0;

        foreach ($carrito->detalles as $detalle) {

            $producto = $detalle->producto;
            $precio_base = $producto->dPrecio_venta;
            $cantidad = $detalle->cantidad;

            // Obtener impuestos
            $impuestos = $producto->impuestos->where('bActivo', 1);

            $ieps = 0;
            $iva = 0;

            foreach ($impuestos as $imp) {
                if ($imp->eTipo === 'IEPS') {
                    $ieps = $precio_base * ($imp->dPorcentaje / 100);
                }
            }

            foreach ($impuestos as $imp) {
                if ($imp->eTipo === 'IVA') {
                    $iva = ($precio_base + $ieps) * ($imp->dPorcentaje / 100);
                }
            }

            // Precio final unitario
            $precio_final_unitario = $precio_base + $ieps + $iva;

            // Totales por cantidad
            $subtotal_producto = $precio_base * $cantidad;
            $impuestos_producto = ($ieps + $iva) * $cantidad;

            $subtotal += $subtotal_producto;
            $totalImpuestos += $impuestos_producto;
        }

        $total = $subtotal + $totalImpuestos;

        return [$subtotal, $totalImpuestos, $total];
    }
}
