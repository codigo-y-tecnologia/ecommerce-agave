<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Checkout</title>
</head>
<body>
    <div class="container mt-5">
    <h2>Resumen de tu pedido</h2>

    <table class="table table-striped mt-3">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($carrito->detalles as $detalle)
                <tr>
                    <td>{{ $detalle->producto->vNombre }}</td>
                    <td>{{ $detalle->cantidad }}</td>
                    <td>${{ number_format($detalle->precio_unitario, 2) }}</td>
                    <td>${{ number_format($detalle->cantidad * $detalle->precio_unitario, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-end">Total:</th>
                <th>${{ number_format($total, 2) }}</th>
            </tr>
        </tfoot>
    </table>

    <form action="{{ route('checkout.store') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-success">Confirmar pedido</button>
        <a href="{{ route('carrito.index') }}" class="btn btn-secondary">Volver al carrito</a>
    </form>
</div>
</body>
</html>