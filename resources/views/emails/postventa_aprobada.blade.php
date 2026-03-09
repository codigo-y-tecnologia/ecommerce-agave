<h2>Reembolso aprobado</h2>

<p>Hola {{ $solicitud->pedido->usuario->vNombre }},</p>

<p>
Tu solicitud de {{ $solicitud->eTipo }} del pedido
<strong>#{{ $solicitud->pedido->id_pedido }}</strong> fue aprobada.
</p>

<p><strong>Productos del pedido:</strong></p>

<ul>
@foreach($solicitud->pedido->detalles as $det)
    <li>
        {{ $det->producto->vNombre ?? 'Producto no disponible' }}
        (x{{ $det->iCantidad }})
    </li>
@endforeach
</ul>

<p>
Monto reembolsado:
<strong>${{ number_format($solicitud->pedido->venta->dTotal, 2) }}</strong>
</p>

<p>
El reembolso se verá reflejado en tu método de pago en un plazo de 3 a 10 días hábiles.
</p>

<p>Gracias por tu confianza.</p>
<p>Atentamente,<br>Equipo de Soporte</p>
