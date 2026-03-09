<h2>Solicitud rechazada</h2>

<p>Hola {{ $solicitud->pedido->usuario->vNombre }},</p>

<p>
Lamentamos informarte que tu solicitud de {{ $solicitud->eTipo }}
del pedido <strong>#{{ $solicitud->pedido->id_pedido }}</strong>
ha sido rechazada.
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

@if($solicitud->tRespuesta_admin)
<p><strong>Motivo:</strong> {{ $solicitud->tRespuesta_admin }}</p>
@endif

<p>Si tienes dudas, contáctanos.</p>
<p>Saludos,<br>Equipo de Soporte</p>