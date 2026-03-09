<p>Hola {{ $pedido->usuario->vNombre }},</p>

<p>
    Te informamos que tu pedido <strong>#{{ $pedido->id_pedido }}</strong> realizado el
    <strong>{{ $pedido->tFecha_pedido->format('d/m/Y') }}</strong>
    ha sido cancelado y el reembolso fue procesado correctamente.
</p>

<p><strong>Resumen del pedido:</strong></p>

<ul>
@foreach($pedido->detalles as $detalle)
    <li>
        {{ $detalle->producto->vNombre }}
        ({{ $detalle->iCantidad }})
    </li>
@endforeach
</ul>

<p><strong>Método de pago:</strong> {{ strtoupper($pedido->venta->eMetodo_pago) }}</p>

<p>
    <strong>Monto reembolsado:</strong>
    ${{ number_format($pedido->venta->dTotal, 2) }} MXN
</p>

@if($motivo)
<p><strong>Motivo de la cancelación:</strong> {{ $motivo }}</p>
@endif

<p>
    El reembolso puede tardar entre
    <strong>3 y 10 días hábiles</strong>
    dependiendo de tu banco o proveedor de pago.
</p>

<p>Si tienes alguna duda, contáctanos.</p>

<p>Gracias por tu comprensión.</p>
