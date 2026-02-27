<?php

namespace App\Services\Checkout;

use App\Models\Cupon;

class CalcularDescuentoService
{
    public function ejecutar(?Cupon $cupon, string $subtotal, string $envioBase): array
    {
        $descuento = '0.00';
        $envio = $envioBase;

        if (!$cupon) {
            return [$descuento, $envio];
        }

        // Validar monto mínimo
        if ($cupon->dMonto_minimo && bccomp($subtotal, $cupon->dMonto_minimo, 2) === -1) {
            return ['0.00', $envioBase];
        }

        switch ($cupon->eTipo) {
            case 'envio_gratis':
                $envio = '0.00';
                break;

            case 'porcentaje':
                $descuento = bcmul($subtotal, bcdiv($cupon->dDescuento, '100', 4), 2);

                // Evitar descuento mayor al subtotal
                if (bccomp($descuento, $subtotal, 2) === 1) {
                    $descuento = $subtotal;
                }
                break;

            case 'monto':
                $descuento = $cupon->dDescuento;

                // Evitar descuento mayor al subtotal
                if (bccomp($descuento, $subtotal, 2) === 1) {
                    $descuento = $subtotal;
                }
                break;
        }

        return [$descuento, $envio];
    }
}
