<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial; background: #f5f5f5; margin:0; padding:20px; }
        .card { background:white; padding:25px; border-radius:8px; max-width:600px; margin:auto; }
        .title { font-size:22px; font-weight:bold; margin-bottom:20px; }
        .footer { margin-top:25px; font-size:12px; color:#777; text-align:center; }
        table { width:100%; border-collapse: collapse; margin-top:20px; }
        th, td { padding:8px; border-bottom:1px solid #eee; text-align:left; }
    </style>
</head>
<body>

<div class="card">
    <p class="title">¡Gracias por tu compra, {{ $pedido->vNombre }}!</p>

    <p>Hemos recibido tu pedido correctamente.</p>

    <p><strong>ID del pedido:</strong> #{{ $pedido->id_pedido }}</p>
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

<!-- Portal de consulta -->
    <div style="margin-top:35px; text-align:center;">
        <a href="{{ route('consulta.pedido.form') }}" class="btn">
            Consultar estado de mi pedido
        </a>
    </div>

    <p style="margin-top:20px; font-size:14px; color:#555;">
        Para consultar tu pedido más adelante, visita el portal e ingresa:
        <br><br>
        <strong>Número de pedido:</strong> #{{ $pedido->id_pedido }}<br>
        <strong>Correo electrónico:</strong> {{ $pedido->vEmail }}
    </p>

    <p style="margin-top:25px;">
        Puedes ver los detalles en tu cuenta o responder este correo si necesitas ayuda.
    </p>

</div>

<p class="footer">Este es un correo automático. No respondas a este mensaje.</p>

</body>
</html>
