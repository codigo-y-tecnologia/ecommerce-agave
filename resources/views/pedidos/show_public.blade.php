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
    {{ $pedido->fact_calle }} {{ $pedido->fact_numero_exterior }}<br>
    {{ $pedido->fact_colonia }}<br>
    {{ $pedido->fact_ciudad }}, {{ $pedido->fact_estado }}<br>
    CP {{ $pedido->fact_codigo_postal }}
</p>
@endif

<h4>Productos</h4>

<table class="table">
    <thead>
        <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th class="text-end">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($pedido->detalles as $det)
            <tr>
                <td>{{ optional($det->producto)->vNombre }}</td>
                <td>{{ $det->iCantidad }}</td>
                <td class="text-end">
                    ${{ number_format($det->iCantidad * $det->dPrecio_unitario, 2) }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

@endsection