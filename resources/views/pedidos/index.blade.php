@extends('layouts.app')

@section('title', 'Mis compras')

@section('content')

<h2 class="fw-bold mb-4">Mis compras</h2>

{{-- @if(!$allowClaimeOrders) --}}
{{-- ==============================
     RECLAMAR PEDIDO GUEST
================================ --}}
<div class="card mb-4 shadow-sm border-primary">
    <div class="card-header fw-bold text-primary">
        ¿Compraste sin cuenta? Vincula tu pedido
    </div>
    <div class="card-body">

        @if(session('reclamar_ok'))
            <div class="alert alert-success mb-3">
                {{ session('reclamar_ok') }}
            </div>
        @endif

        @if($errors->has('reclamar'))
            <div class="alert alert-danger mb-3">
                {{ $errors->first('reclamar') }}
            </div>
        @endif

        <p class="text-muted small mb-3">
            Ingresa el número de pedido y el email con el que realizaste la compra.
        </p>

        <form method="POST" action="{{ route('pedidos.reclamar') }}" class="row g-2 align-items-end">
            @csrf

            <div class="col-sm-4">
                <label class="form-label small fw-semibold"># Pedido</label>
                <input type="number"
                       name="numero_pedido"
                       class="form-control form-control-sm @error('numero_pedido') is-invalid @enderror"
                       placeholder="Ej: 1042"
                       value="{{ old('numero_pedido') }}"
                       required>
                @error('numero_pedido')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-sm-5">
                <label class="form-label small fw-semibold">Email usado en la compra</label>
                <input type="email"
                       name="email"
                       class="form-control form-control-sm @error('email') is-invalid @enderror"
                       placeholder="correo@ejemplo.com"
                       value="{{ old('email') }}"
                       maxlength="95"
                       required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-sm-3">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    Vincular pedido
                </button>
            </div>

        </form>
    </div>
</div>
{{-- @endif --}}

<form method="GET" class="d-flex align-items-center mb-4 gap-2">
    <span class="fw-semibold">Pedidos realizados en</span>

    <select name="fecha"
            class="form-select w-auto"
            onchange="this.form.submit()">

        <option value="30d" {{ $fechaFiltro === '30d' ? 'selected' : '' }}>
            últimos 30 días
        </option>

        <option value="3m" {{ $fechaFiltro === '3m' ? 'selected' : '' }}>
            últimos 3 meses
        </option>

        @foreach ($years as $year)
            <option value="{{ $year }}" {{ $fechaFiltro == $year ? 'selected' : '' }}>
                {{ $year }}
            </option>
        @endforeach

    </select>
</form>

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

                             @if($detallePrincipal->vNombre_variacion)
                            <br>
                            <small class="text-muted">
                                {{ $detallePrincipal->vNombre_variacion }}
                            </small>
                        @endif
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

            @if($allowOrderReturns)

                @if(
    $pedido->ultimaSolicitudPostventa &&
    !(
        $pedido->ultimaSolicitudPostventa->eTipo === 'cancelacion' &&
        $pedido->ultimaSolicitudPostventa->eEstado === 'rechazada' &&
        optional($pedido->envio)->eEstado === \App\Models\Envio::ESTADO_ENTREGADO
    )
)

@php $s = $pedido->ultimaSolicitudPostventa; @endphp

<div class="mt-1 small
    @if($s->eEstado === 'pendiente') text-warning
    @elseif($s->eEstado === 'rechazada') text-danger
    @elseif($s->eEstado === 'reembolsada') text-success
    @endif
">
Postventa: {{ ucfirst($s->eEstado) }}
</div>
@endif

@endif

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
