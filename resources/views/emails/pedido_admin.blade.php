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
    <p><strong>Cliente:</strong> {{ $pedido->usuario->vNombre }} {{ $pedido->usuario->vApaterno }}</p>
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
            @foreach($pedido->detalles as $d)
            <tr>
                <td>{{ $d->producto->vNombre }}</td>
                <td>{{ $d->iCantidad }}</td>
                <td>${{ number_format($d->dPrecio_unitario, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>

</body>
</html>
