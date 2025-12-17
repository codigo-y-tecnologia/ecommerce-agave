@extends('layouts.app')

@section('title', 'Mis compras')

@section('content')

<h2 class="fw-bold mb-4">Mis compras</h2>

@forelse ($pedidos as $pedido)
    <div class="card mb-3 shadow-sm">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap">

            <div>
                <p class="mb-1 fw-bold">
                    Pedido #{{ $pedido->id_pedido }}
                </p>

                <p class="mb-0 text-muted">
                    {{ $pedido->tFecha_pedido->format('d/m/Y H:i') }}
                </p>

                <p class="mb-0">
                    Total:
                    <strong>${{ number_format($pedido->dTotal, 2) }}</strong>
                </p>
            </div>

            <div class="text-end mt-3 mt-md-0">
                <span class="badge bg-{{ estadoPedidoColor($pedido->eEstado) }}">
                    {{ estadoPedidoTexto($pedido->eEstado) }}
                </span>

                <div class="mt-2">
                    <a href="{{ route('pedidos.show', $pedido->id_pedido) }}"
                       class="btn btn-outline-primary btn-sm">
                        Ver pedido
                    </a>
                </div>
            </div>

        </div>
    </div>
@empty
    <div class="alert alert-info">
        No has realizado compras todavía.
    </div>
@endforelse

<div class="mt-4">
    {{ $pedidos->links() }}
</div>

@endsection
