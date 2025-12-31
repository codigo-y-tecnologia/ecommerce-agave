<h2>Reembolso aprobado</h2>

<p>Hola {{ $solicitud->pedido->usuario->vNombre }},</p>

<p>
Tu solicitud de {{ $solicitud->eTipo }} del pedido
<strong>#{{ $solicitud->pedido->id_pedido }}</strong> fue aprobada.
</p>

<p>
El reembolso por <strong>${{ number_format($solicitud->pedido->venta->dTotal, 2) }}</strong>
ha sido procesado y se verá reflejado en tu método de pago.
</p>

<p>Gracias por tu confianza.</p>
