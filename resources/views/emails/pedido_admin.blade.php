<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial; background:#f4f4f4; padding:20px; }
        .card { background:white; padding:20px; border-radius:8px; max-width:600px; margin:auto; }
        table { width:100%; border-collapse:collapse; margin-top:15px; }
        th, td { padding:8px; border-bottom:1px solid #ddd; }
        .title { font-size:20px; font-weight:bold; margin-bottom:15px; }
    </style>
</head>
<body>

<div class="card">
    <p class="title">Nuevo Pedido Recibido</p>

    <p><strong>ID del pedido:</strong> #{{ $pedido->id_pedido }}</p>
    <p><strong>Cliente:</strong> {{ $pedido->vNombre }} {{ $pedido->vApaterno }} {{ $pedido->vAmaterno }}</p>
    <p><strong>Total:</strong> ${{ number_format($pedido->dTotal, 2) }}</p>

    <h3>Productos:</h3>
<table>
    <thead>
        <tr>
            <th>Producto</th>
            <th>Cant.</th>
            <th>Precio</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>

@foreach($pedido->detalles as $detalle)
    <tr>
        <td>{{ $detalle->producto->vNombre }}</td>
        <td>{{ $detalle->iCantidad }}</td>
        <td>${{ number_format($detalle->dPrecio_unitario, 2) }}</td>
        <td>${{ number_format($detalle->dSubtotal, 2) }}</td>
    </tr>
@endforeach

    </tbody>
</table>

<!-- Resumen financiero (email) -->
<table style="margin-top:20px; width:100%; border-collapse:collapse;">
    <tr>
        <td style="padding:6px;">Subtotal:</td>
        <td style="padding:6px; text-align:right;">${{ number_format($snapshot->subtotal_con_impuestos, 2) }}</td>
    </tr>

    {{-- @foreach($emailImpuestos as $nombre => $monto)
    <tr>
        <td style="padding:6px;">{{ $nombre }}:</td>
        <td style="padding:6px; text-align:right;">${{ number_format($monto, 2) }}</td>
    </tr>
    @endforeach --}}

    <tr>
        <td style="padding:6px;">Envío:</td>
        <td style="padding:6px; text-align:right;">
            @if($snapshot->envio == 0) Gratis @else ${{ number_format($snapshot->envio, 2) }} @endif
        </td>
    </tr>

    @if($snapshot->descuento > 0)
    <tr>
        <td style="padding:6px;">Descuento{{ $snapshot->cupon_codigo ? ' (' . $snapshot->cupon_codigo . ')' : '' }}:</td>
        <td style="padding:6px; text-align:right; color:#d9534f;">- ${{ number_format($snapshot->descuento, 2) }}</td>
    </tr>
    @endif

    <tr>
        <td style="padding:6px; font-weight:bold;">Total Final:</td>
        <td style="padding:6px; text-align:right; font-weight:bold;">${{ number_format($snapshot->total_final, 2) }}</td>
    </tr>
</table>

</div>

</body>
</html>
