@extends('layouts.app')

@section('title', 'Detalle del pedido')

@section('content')

<a href="{{ route('pedidos.index') }}" class="btn btn-link mb-3">
    ← Volver a mis compras
</a>

<h2 class="fw-bold mb-3">
    Pedido #{{ $pedido->id_pedido }}
</h2>

<p class="mb-4">
    Estado:
    <span class="badge bg-{{ estadoPedidoColor($pedido->eEstado) }}">
        {{ estadoPedidoTexto($pedido->eEstado) }}
    </span>
</p>

{{-- ===============================
     PRODUCTOS
================================ --}}
<div class="card mb-4 shadow-sm">
    <div class="card-header fw-bold">
        Productos
    </div>

    <div class="card-body p-0">
        @if($pedido->detalles && $pedido->detalles->count())
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Producto</th>
                        <th class="text-center">Cantidad</th>
                        <th class="text-end">Precio unitario</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pedido->detalles as $det)
                        <tr>
                            <td>
                                {{ optional($det->producto)->vNombre ?? 'Producto no disponible' }}
                            </td>
                            <td class="text-center">
                                {{ $det->iCantidad }}
                            </td>
                            <td class="text-end">
                                ${{ number_format($det->dPrecio_unitario, 2) }}
                            </td>
                            <td class="text-end">
                                ${{ number_format($det->iCantidad * $det->dPrecio_unitario, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-muted p-3 mb-0">
                No hay productos registrados en este pedido.
            </p>
        @endif
    </div>
</div>

{{-- ===============================
     RESUMEN DEL PEDIDO
================================ --}}
<div class="row mb-4">

    {{-- DIRECCIÓN --}}
    <div class="col-md-6">
        <div class="card shadow-sm mb-3">
            <div class="card-header fw-bold">
                Dirección de envío
            </div>
            <div class="card-body">
                @if($pedido->direccion)
                    <p class="mb-1">
                        {{ $pedido->direccion->vCalle }}
                        {{ $pedido->direccion->vNumero ?? '' }}
                    </p>
                    <p class="mb-1">
                        {{ $pedido->direccion->vColonia }},
                        {{ $pedido->direccion->vCiudad }}
                    </p>
                    <p class="mb-0">
                        {{ $pedido->direccion->vEstado }},
                        {{ $pedido->direccion->vCodigo_postal }}
                    </p>
                @else
                    <p class="text-muted mb-0">
                        Dirección no disponible
                    </p>
                @endif
            </div>
        </div>

        {{-- PAGO --}}
        <div class="card shadow-sm">
            <div class="card-header fw-bold">
                Pago
            </div>
            <div class="card-body">
                @if($pedido->pago)
                    <p class="mb-1">
                        Método:
                        <strong>{{ strtoupper($pedido->pago->eMetodo_pago) }}</strong>
                    </p>

                    <p class="mb-1">
                        Referencia:
                        <span class="text-muted">
                            {{ $pedido->pago->vReferencia ?? '—' }}
                        </span>
                    </p>

                    <p class="mb-0">
                        Fecha:
                        @if($pedido->pago->tFecha_pago)
                            {{ optional($pedido->pago->tFecha_pago)->format('d/m/Y H:i') }}
                        @else
                            <span class="text-muted">Pendiente</span>
                        @endif
                    </p>
                @else
                    <p class="text-muted mb-0">
                        Información de pago no disponible
                    </p>
                @endif
            </div>
        </div>
    </div>

    {{-- ===============================
         TOTALES
    ================================ --}}
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header fw-bold">
                Resumen
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal</span>
                    <span>${{ number_format($subtotal ?? 0, 2) }}</span>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span>Envío</span>
                    <span>${{ number_format($envio ?? 0, 2) }}</span>
                </div>

                @if(($descuento ?? 0) > 0)
                    <div class="d-flex justify-content-between mb-2 text-success">
                        <span>Descuento</span>
                        <span>- ${{ number_format($descuento, 2) }}</span>
                    </div>
                @endif

                <hr>

                <div class="d-flex justify-content-between fw-bold fs-5">
                    <span>Total</span>
                    <span>${{ number_format($pedido->dTotal, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection
