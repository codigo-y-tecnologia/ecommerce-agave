<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura Pedido #{{ $pedido->id_pedido }}</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }

        h1 {
            font-size: 20px;
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        th {
            background: #f5f5f5;
            text-align: left;
        }

        .right {
            text-align: right;
        }
    </style>
</head>
<body>

<h1>Factura</h1>

<p>
    <strong>Pedido:</strong> #{{ $pedido->id_pedido }}<br>
    <strong>Fecha:</strong> {{ $venta->tFecha_venta->format('d/m/Y H:i') }}<br>
    <strong>Método de pago:</strong> {{ strtoupper($venta->eMetodo_pago) }}
</p>

<hr>

<h3>Cliente</h3>
<p>
    <strong>
        {{ $pedido->vNombre }}
        {{ $pedido->vApaterno }}
        {{ $pedido->vAmaterno }}
    </strong>
    <br>

    {{ $pedido->vEmail }}
    <br>

    @if($pedido->vRFC)
        RFC: {{ $pedido->vRFC }}<br>
    @endif

    {{ $pedido->env_ciudad }},
    {{ $pedido->env_estado }}
</p>

<h3>Dirección de envío</h3>
<p>
    {{ $pedido->env_calle }} {{ $pedido->env_numero_exterior }}
    {{ $pedido->env_numero_interior ?? '' }}<br>

    {{ $pedido->env_colonia }}<br>

    {{ $pedido->env_ciudad }},
    {{ $pedido->env_estado }}<br>

    CP {{ $pedido->env_codigo_postal }}
</p>

@if($pedido->fac_calle)
    <h3>Dirección de facturación</h3>
    <p>
        {{ $pedido->fac_calle }} {{ $pedido->fac_numero_exterior }}<br>
        {{ $pedido->fac_colonia }}<br>
        {{ $pedido->fac_ciudad }}, {{ $pedido->fac_estado }}<br>
        CP {{ $pedido->fac_codigo_postal }}
    </p>
@endif

<h3>Productos</h3>

<table>
    <thead>
        <tr>
            <th>Producto</th>
            <th class="right">Cantidad</th>
            <th class="right">Precio</th>
            <th class="right">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($pedido->detalles as $det)
            <tr>
                <td>{{ optional($det->producto)->vNombre }}</td>
                <td class="right">{{ $det->iCantidad }}</td>
                <td class="right">${{ number_format($det->dPrecio_unitario, 2) }}</td>
                <td class="right">
                    ${{ number_format($det->iCantidad * $det->dPrecio_unitario, 2) }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<br>

<table>
    <tr>
        <td>Subtotal</td>
        <td class="right">
            ${{ number_format($venta->dTotal + $venta->dDescuento - $venta->dCosto_envio, 2) }}
        </td>
    </tr>

    <tr>
        <td>Envío</td>
        <td class="right">
            {{ $venta->dCosto_envio == 0 ? 'Gratis' : '$' . number_format($venta->dCosto_envio, 2) }}
        </td>
    </tr>

    @if($venta->dDescuento > 0)
        <tr>
            <td>Descuento</td>
            <td class="right">
                - ${{ number_format($venta->dDescuento, 2) }}
            </td>
        </tr>
    @endif

    <tr>
        <th>Total</th>
        <th class="right">${{ number_format($venta->dTotal, 2) }}</th>
    </tr>
</table>

</body>
</html>
