<?php

namespace App\Services\Checkout;

use App\Models\Cupon;

class CalcularDescuentoService
{
    public function ejecutar(?Cupon $cupon, float $subtotal, float $envioBase): array
    {
        $descuento = 0.0;
        $envio = $envioBase;
        $mensaje = null;
        $warning = false;

        if (!$cupon) {
            return [
                'descuento' => $descuento,
                'envio' => $envio,
                'mensaje' => $mensaje,
                'warning' => $warning
            ];
        }

        $mensaje = "Cupón aplicado correctamente: {$cupon->vCodigo_cupon}";

        // Validar monto mínimo
        if ($cupon->dMonto_minimo && $subtotal < $cupon->dMonto_minimo) {
            return [
                'descuento' => 0.0,
                'envio' => $envioBase,
                'mensaje' => "El monto mínimo para aplicar este cupón es de: $" . $cupon->dMonto_minimo,
                'warning' => true
            ];
        }

        switch ($cupon->eTipo) {
            case 'envio_gratis':
                $envio = 0.0;
                $mensaje .= " — Envío gratis activado 🚚";
                break;

            case 'porcentaje':
                $descuento = $subtotal * ($cupon->dDescuento / 100);
                $mensaje .= " — Descuento: $" . number_format($descuento, 2, '.', ',');
                break;

            case 'monto':
                $descuento = $cupon->dDescuento;
                $mensaje .= " — Descuento: $" . number_format($descuento, 2, '.', ',');
                break;
        }

        // 🔒 Protección contra descuento mayor al subtotal
        if ($descuento > $subtotal) {
            $descuento = $subtotal;
        }

        return [
            'descuento' => $descuento,
            'envio' => $envio,
            'mensaje' => $mensaje,
            'warning' => $warning
        ];
    }
}
