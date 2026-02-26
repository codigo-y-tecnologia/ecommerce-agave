<?php

namespace App\Services\Checkout;

use App\Models\Cupon;

class CalcularDescuentoService
{
    public function ejecutar(?Cupon $cupon, float $subtotal, float $envioBase): array
    {
        $descuento = 0;
        $envio = $envioBase;

        if (!$cupon) {
            return [$descuento, $envio];
        }

        if ($cupon->dMonto_minimo && $subtotal < $cupon->dMonto_minimo) {
            return [0, $envioBase];
        }

        switch ($cupon->eTipo) {
            case 'envio_gratis':
                $envio = 0;
                break;

            case 'porcentaje':
                $descuento = $subtotal * ($cupon->dDescuento / 100);
                break;

            case 'monto':
                $descuento = $cupon->dDescuento;
                break;
        }

        return [$descuento, $envio];
    }
}
