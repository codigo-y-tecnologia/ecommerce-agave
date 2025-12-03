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
    <p class="title">¡Gracias por tu compra, {{ $pedido->usuario->vNombre }}!</p>

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
            </tr>
        </thead>
        <tbody>
            @foreach($pedido->detalles as $detalle)
            <tr>
                <td>{{ $detalle->producto->vNombre }}</td>
                <td>{{ $detalle->iCantidad }}</td>
                <td>${{ number_format($detalle->dPrecio_unitario, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p style="margin-top:25px;">
        Puedes ver los detalles en tu cuenta o responder este correo si necesitas ayuda.
    </p>

</div>

<p class="footer">Este es un correo automático. No respondas a este mensaje.</p>

</body>
</html>
