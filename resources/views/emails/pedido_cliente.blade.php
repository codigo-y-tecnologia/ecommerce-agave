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
        @php
    $emailSubtotal = 0;
    $emailImpuestos = [];
    $emailTotalImpuestos = 0;
@endphp

@foreach($pedido->detalles as $detalle)
    @php
        $producto = $detalle->producto;
        $cantidad = $detalle->iCantidad;

        // Precio base
        $precio_base = $producto->dPrecio_venta;

        // IEPS
        $ieps = 0;
        foreach($producto->impuestos->where('bActivo',1) as $imp) {
            if ($imp->eTipo === 'IEPS') {
                $ieps = $precio_base * ($imp->dPorcentaje / 100);
            }
        }

        // IVA (sobre precio base + IEPS)
        $iva = 0;
        foreach($producto->impuestos->where('bActivo',1) as $imp) {
            if ($imp->eTipo === 'IVA') {
                $iva = ($precio_base + $ieps) * ($imp->dPorcentaje / 100);
            }
        }

        // Precio unitario final real
        $precioConImp = $precio_base + $ieps + $iva;

        // Subtotal de este producto
        $lineSubtotal = $precioConImp * $cantidad;

        // Acumular impuestos por tipo
        if ($ieps > 0) {
            $emailImpuestos['IEPS'] = ($emailImpuestos['IEPS'] ?? 0) + ($ieps * $cantidad);
        }
        if ($iva > 0) {
            $emailImpuestos['IVA'] = ($emailImpuestos['IVA'] ?? 0) + ($iva * $cantidad);
        }

        // Sumar subtotal general
        $emailSubtotal += $lineSubtotal;
    @endphp

    <tr>
        <td>{{ $producto->vNombre }}</td>
        <td>{{ $cantidad }}</td>
        <td>${{ number_format($precioConImp, 2) }}</td>
        <td>${{ number_format($lineSubtotal, 2) }}</td>
    </tr>
@endforeach

    </tbody>
</table>

<!-- Resumen financiero (email) -->
<table style="margin-top:20px; width:100%; border-collapse:collapse;">
    <tr>
        <td style="padding:6px;">Subtotal:</td>
        <td style="padding:6px; text-align:right;">${{ number_format($emailSubtotal, 2) }}</td>
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
            @if($envio == 0) Gratis @else ${{ number_format($envio, 2) }} @endif
        </td>
    </tr>

    @if($descuento > 0)
    <tr>
        <td style="padding:6px;">Descuento{{ $cupon ? ' (' . $cupon->vCodigo_cupon . ')' : '' }}:</td>
        <td style="padding:6px; text-align:right; color:#d9534f;">- ${{ number_format($descuento, 2) }}</td>
    </tr>
    @endif

    <tr>
        <td style="padding:6px; font-weight:bold;">Total Final:</td>
        <td style="padding:6px; text-align:right; font-weight:bold;">${{ number_format($totalFinal, 2) }}</td>
    </tr>
</table>

    <p style="margin-top:25px;">
        Puedes ver los detalles en tu cuenta o responder este correo si necesitas ayuda.
    </p>

</div>

<p class="footer">Este es un correo automático. No respondas a este mensaje.</p>

</body>
</html>
