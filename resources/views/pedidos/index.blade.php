@extends('layouts.app')

@section('title', 'Mis compras')

@section('content')

<h2 class="fw-bold mb-4">Mis compras</h2>

@forelse ($pedidos as $pedido)

    @php
        // Producto principal del pedido (estilo marketplaces)
        $detallePrincipal = $pedido->detalles->first();
        $producto = optional($detallePrincipal)->producto;
        $imagenes = $producto->imagenes ?? [];
    @endphp

    <div class="card mb-3 shadow-sm">
        <div class="card-body d-flex align-items-start gap-3 flex-wrap">

            {{-- IMAGEN DEL PRODUCTO --}}
            <div>
                @if(!empty($imagenes) && count($imagenes) > 0)
                    <img src="{{ $imagenes[0] }}"
                         alt="{{ $producto->vNombre }}"
                         class="rounded"
                         style="width:80px; height:80px; object-fit:cover;">
                @else
                    <div class="bg-light d-flex align-items-center justify-content-center rounded"
                         style="width:80px; height:80px;">
                        <span class="text-muted small">Sin imagen</span>
                    </div>
                @endif
            </div>

            {{-- INFO DEL PEDIDO --}}
            <div class="flex-grow-1">

                <p class="mb-1 fw-bold">
                    Pedido #{{ $pedido->id_pedido }}
                </p>

                <p class="mb-1 text-muted">
                    {{ optional($pedido->tFecha_pedido)->format('d/m/Y H:i') ?? 'Fecha no disponible' }}
                </p>

                <p class="mb-2">
                    Total:
                    <strong>${{ number_format($pedido->dTotal, 2) }}</strong>
                </p>

                {{-- PRODUCTO --}}
                @if($producto)
                    <div>
                        <a href="{{ route('productos.show.public', $producto->id_producto) }}"
                           class="fw-semibold text-decoration-none">
                            {{ $producto->vNombre }}
                        </a>

                        @if($pedido->detalles->count() > 1)
                            <span class="text-muted small">
                                y {{ $pedido->detalles->count() - 1 }} más
                            </span>
                        @endif
                    </div>
                @else
                    <span class="text-muted small">
                        Producto no disponible
                    </span>
                @endif

            </div>

            {{-- ESTADO Y ACCIONES --}}
            <div class="text-end mt-3 mt-md-0">

                <span class="badge bg-{{ estadoPedidoColor($pedido->eEstado) }}">
                    {{ estadoPedidoTexto($pedido->eEstado) }}
                </span>

                <div class="mt-2 d-flex flex-column gap-2">

                    <a href="{{ route('pedidos.show', $pedido->id_pedido) }}"
                       class="btn btn-outline-primary btn-sm">
                        Ver pedido
                    </a>

                    @if($producto)
                        <a href="{{ route('productos.show.public', $producto->id_producto) }}"
                           class="btn btn-outline-secondary btn-sm">
                            Volver a comprar
                        </a>
                    @endif

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
