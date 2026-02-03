<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1, h2 { margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px; }
        th { background: #f5f5f5; }
        .text-right { text-align: right; }
        .no-border td { border: none; }
    </style>
</head>
<body>

<h1>Recibo de compra</h1>
<p><strong>Pedido:</strong> #{{ $pedido->id_pedido }}</p>
<p><strong>Fecha:</strong> {{ $pedido->tFecha_pedido->format('d/m/Y H:i') }}</p>
<p><strong>Método de pago:</strong> {{ strtoupper($payment_method) }}</p>

<hr>

<h2>Datos del cliente</h2>
<p>
    {{ $pedido->vNombre }} {{ $pedido->vApaterno }} {{ $pedido->vAmaterno }}<br>
    {{ $pedido->vEmail }}<br>
    Tel: {{ $pedido->env_telefono_contacto }}
</p>

<h2>Dirección de envío</h2>
<p>
    {{ $pedido->env_calle }} {{ $pedido->env_numero_exterior }}
    @if($pedido->env_numero_interior)
        Int. {{ $pedido->env_numero_interior }}
    @endif<br>
    {{ $pedido->env_colonia }}, {{ $pedido->env_ciudad }}, {{ $pedido->env_estado }}<br>
    CP {{ $pedido->env_codigo_postal }}
</p>

@if($pedido->vRFC)
<hr>
<h2>Datos de facturación</h2>
<p>
    <strong>RFC:</strong> {{ $pedido->vRFC }}<br>
    {{ $pedido->fac_calle }} {{ $pedido->fac_numero_exterior }}
</p>
@endif

<hr>

<h2>Productos</h2>
<table>
    <thead>
        <tr>
            <th>Producto</th>
            <th>Cant.</th>
            <th class="text-right">Precio</th>
            <th class="text-right">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($pedido->detalles as $det)
            <tr>
                <td>{{ $det->producto->vNombre }}</td>
                <td>{{ $det->iCantidad }}</td>
                <td class="text-right">${{ number_format($det->producto->dPrecio_venta, 2) }}</td>
                <td class="text-right">
                    ${{ number_format($det->producto->dPrecio_venta * $det->iCantidad, 2) }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<table class="no-border">
    <tr>
        <td class="text-right"><strong>Subtotal:</strong></td>
        <td class="text-right">${{ number_format($subtotal, 2) }}</td>
    </tr>
    <tr>
        <td class="text-right"><strong>Impuestos:</strong></td>
        <td class="text-right">${{ number_format($totalImpuestos, 2) }}</td>
    </tr>
    @if($descuento > 0)
    <tr>
        <td class="text-right"><strong>Descuento @if($cupon) ({{ $cupon->vCodigo_cupon }}) @endif:</strong></td>
        <td class="text-right">- ${{ number_format($descuento, 2) }}</td>
    </tr>
    @endif
    <tr>
        <td class="text-right"><strong>Envío:</strong></td>
        <td class="text-right">${{ number_format($envio, 2) }}</td>
    </tr>
    <tr>
        <td class="text-right"><strong>Total:</strong></td>
        <td class="text-right"><strong>${{ number_format($totalFinal, 2) }}</strong></td>
    </tr>
</table>

</body>
</html>
