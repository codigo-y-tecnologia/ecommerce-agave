@extends('layouts.app')

@section('title', 'Detalle del pedido')

@section('content')

<h2 class="fw-bold mb-3">
    Pedido #{{ $pedido->id_pedido }}
</h2>

<p>
    Estado:
    <span class="badge bg-{{ estadoPedidoColor($pedido->eEstado) }}">
        {{ estadoPedidoTexto($pedido->eEstado) }}
    </span>
</p>

<hr>

<h4>Cliente</h4>
<p>
    {{ $pedido->vNombre }} {{ $pedido->vApaterno }}<br>
    {{ $pedido->vEmail }}
</p>

<h4>Dirección de envío</h4>
<p>
    {{ $pedido->env_calle }} {{ $pedido->env_numero_exterior }}<br>
    {{ $pedido->env_colonia }}<br>
    {{ $pedido->env_ciudad }}, {{ $pedido->env_estado }}<br>
    CP {{ $pedido->env_codigo_postal }}
</p>

@if($pedido->fac_calle)
<h4>Dirección de facturación</h4>
<p>
    {{ $pedido->fac_calle }} {{ $pedido->fac_numero_exterior }}<br>
    {{ $pedido->fac_colonia }}<br>
    {{ $pedido->fac_ciudad }}, {{ $pedido->fac_estado }}<br>
    CP {{ $pedido->fac_codigo_postal }}<br>
    @if($pedido->vRFC)
    RFC: {{ $pedido->vRFC }}
    @endif
</p>
@endif

<h4 class="mt-4">Productos</h4>

<table class="table">
    <thead>
        <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th class="text-end">Precio Unitario</th>
            <th class="text-end">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($pedido->detalles as $det)
            <tr>
                <td>{{ optional($det->producto)->vNombre }}</td>
                <td>{{ $det->iCantidad }}</td>
                <td class="text-end">
                    ${{ number_format($det->dPrecio_unitario, 2) }}
                </td>
                <td class="text-end">
                    ${{ number_format($det->dSubtotal, 2) }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

@php
    $subtotal = $pedido->detalles->sum('dSubtotal');
    $descuento = optional($pedido->venta)->dDescuento ?? 0;
    $envio = optional($pedido->venta)->dCosto_envio ?? 0;
    $total = optional($pedido->venta)->dTotal ?? $subtotal;
@endphp

<div class="card mt-4">
    <div class="card-body">

        <div class="d-flex justify-content-between">
            <span>Subtotal:</span>
            <strong>${{ number_format($subtotal, 2) }}</strong>
        </div>

        @if($descuento > 0)
            <div class="d-flex justify-content-between text-success">
                <span>Descuento:</span>
                <strong>- ${{ number_format($descuento, 2) }}</strong>
            </div>
        @endif

        <div class="d-flex justify-content-between">
            <span>Envío:</span>
            <strong>
                @if($envio == 0)
                    Gratis
                @else
                    ${{ number_format($envio, 2) }}
                @endif
            </strong>
        </div>

        <hr>

        <div class="d-flex justify-content-between fs-5">
            <span><strong>Total pagado:</strong></span>
            <strong>${{ number_format($total, 2) }}</strong>
        </div>

        @if($pedido->venta)
            <div class="mt-3">
                <strong>Método de pago:</strong>
                {{ strtoupper($pedido->venta->eMetodo_pago) }}
            </div>
        @endif

    </div>
</div>

{{-- Botón descargar factura --}}
@if($pedido->venta && $pedido->venta->eEstado === 'completada')
    <div class="mt-4 text-end">
        @php
    $signedUrl = URL::temporarySignedRoute(
        'descargar.factura',
        now()->addMinutes(60), // puedes cambiar duración
        ['id' => $pedido->id_pedido]
    );
@endphp

<a href="{{ $signedUrl }}" class="btn btn-outline-secondary">
    Descargar factura PDF
</a>
    </div>
@endif

@if($pedido->eEstado === 'pagado' && optional($pedido->envio)->eEstado === 'pendiente')
    <div class="alert alert-info mt-4">
        ¿Necesitas cancelar este pedido?
        <br>
        Para gestionar cancelaciones o devoluciones,
        <a href="{{ route('usuarios.create') }}" class="fw-semibold">
            crea una cuenta con este mismo correo
        </a>
        y podrás administrarlo desde tu perfil.
    </div>
@endif

@endsection