<?php

if (!function_exists('estadoPedidoTexto')) {
    function estadoPedidoTexto($estado)
    {
        return match ($estado) {
            'pagado' => 'Pago recibido',
            'procesando' => 'En preparación',
            'enviado' => 'Enviado',
            'entregado' => 'Entregado',
            'cancelado' => 'Cancelado',
            default => ucfirst($estado),
        };
    }
}

if (!function_exists('estadoPedidoColor')) {
    function estadoPedidoColor($estado)
    {
        return match ($estado) {
            'pagado' => 'success',
            'procesando' => 'warning',
            'enviado' => 'info',
            'entregado' => 'primary',
            'cancelado' => 'danger',
            default => 'secondary',
        };
    }
}
