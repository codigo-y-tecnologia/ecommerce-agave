@extends('layouts.admins')

@section('title', 'Pedidos')

@section('content')

<h2 class="fw-bold mb-3 d-flex justify-content-between align-items-center">
    Pedidos
</h2>

<div class="mb-3 d-flex gap-2">
    <a href="{{ route('admin.pedidos.index', ['quick' => 'today']) }}"
       class="btn btn-outline-secondary btn-sm {{ request('quick')=='today' ? 'active' : '' }}">
        Hoy
    </a>

    <a href="{{ route('admin.pedidos.index', ['quick' => 'week']) }}"
       class="btn btn-outline-secondary btn-sm {{ request('quick')=='week' ? 'active' : '' }}">
        Esta semana
    </a>

    <a href="{{ route('admin.pedidos.index', ['quick' => 'month']) }}"
       class="btn btn-outline-secondary btn-sm {{ request('quick')=='month' ? 'active' : '' }}">
        Este mes
    </a>
</div>

<form method="GET" action="{{ route('admin.pedidos.index') }}" class="card mb-4 shadow-sm">
    <div class="card-body">
        <div class="row g-3">

            {{-- ID Pedido --}}
            <div class="col-md-2">
                <input type="text"
                       name="pedido_id"
                       value="{{ request('pedido_id') }}"
                       class="form-control"
                       placeholder="# Pedido">
            </div>

            {{-- Cliente --}}
            <div class="col-md-3">
                <input type="text"
                       name="cliente"
                       value="{{ request('cliente') }}"
                       class="form-control"
                       placeholder="Cliente">
            </div>

            {{-- Fecha desde --}}
            <div class="col-md-2">
                <input type="date"
                       name="fecha_desde"
                       value="{{ request('fecha_desde') }}"
                       class="form-control">
            </div>

            {{-- Fecha hasta --}}
            <div class="col-md-2">
                <input type="date"
                       name="fecha_hasta"
                       value="{{ request('fecha_hasta') }}"
                       class="form-control">
            </div>

            {{-- Método de pago --}}
            <div class="col-md-2">
                <select name="metodo_pago" class="form-select">
    <option value="">Pago</option>
    <option value="paypal" {{ request('metodo_pago')=='paypal' ? 'selected' : '' }}>PayPal</option>
    <option value="stripe" {{ request('metodo_pago')=='stripe' ? 'selected' : '' }}>Stripe</option>
</select>
            </div>

            {{-- Estado pedido --}}
            <div class="col-md-2">
                <select name="estado_pedido" class="form-select">
    <option value="">Estado pedido</option>
    <option value="pendiente" {{ request('estado_pedido')=='pendiente' ? 'selected' : '' }}>Pendiente</option>
    <option value="pagado" {{ request('estado_pedido')=='pagado' ? 'selected' : '' }}>Pagado</option>
    <option value="enviado" {{ request('estado_pedido')=='enviado' ? 'selected' : '' }}>Enviado</option>
    <option value="entregado" {{ request('estado_pedido')=='entregado' ? 'selected' : '' }}>Entregado</option>
    <option value="cancelado" {{ request('estado_pedido')=='cancelado' ? 'selected' : '' }}>Cancelado</option>
</select>
            </div>

            {{-- Estado envío --}}
            <div class="col-md-2">
                <select name="estado_envio" class="form-select">
    <option value="">Estado envío</option>
    <option value="pendiente" {{ request('estado_envio')=='pendiente' ? 'selected' : '' }}>Pendiente</option>
    <option value="enviado" {{ request('estado_envio')=='enviado' ? 'selected' : '' }}>Enviado</option>
    <option value="entregado" {{ request('estado_envio')=='entregado' ? 'selected' : '' }}>Entregado</option>
    <option value="devuelto" {{ request('estado_envio')=='devuelto' ? 'selected' : '' }}>Devuelto</option>
</select>
            </div>

            {{-- Total mínimo --}}
            <div class="col-md-2">
                <input type="number"
                       step="0.01"
                       name="total_min"
                       value="{{ request('total_min') }}"
                       class="form-control"
                       placeholder="Total min">
            </div>

            {{-- Total máximo --}}
            <div class="col-md-2">
                <input type="number"
                       step="0.01"
                       name="total_max"
                       value="{{ request('total_max') }}"
                       class="form-control"
                       placeholder="Total max">
            </div>

            {{-- Botones --}}
            <div class="col-md-3 d-flex align-items-end gap-2">
                <button class="btn btn-primary w-100">
                    Buscar
                </button>

                <a href="{{ route('admin.pedidos.index') }}"
                   class="btn btn-outline-secondary w-100">
                    Limpiar
                </a>
            </div>

        </div>
    </div>
</form>

{{-- TABLA PEDIDOS --}}
<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Cliente</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Pago</th>
                    <th>Envío</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pedidos as $pedido)
                    <tr>
                        <td>{{ $pedido->id_pedido }}</td>

                        <td>
    {{ data_get($pedido->usuario, 'vNombre')
        ? data_get($pedido->usuario, 'vNombre') . ' ' . data_get($pedido->usuario, 'vApaterno')
        : 'Cliente eliminado'
    }}
</td>

                        <td>
                            {{ optional($pedido->tFecha_pedido)->format('d/m/Y H:i') }}
                        </td>

                        <td>
                            ${{ number_format($pedido->dTotal, 2) }}
                        </td>

                        <td>
                            @if($pedido->venta)
                                <span class="badge bg-success">
                                    {{ strtoupper($pedido->venta->eMetodo_pago) }}
                                </span>
                            @else
                                <span class="badge bg-secondary">—</span>
                            @endif
                        </td>

                        <td>
                            @php
                                $estadoEnvio = optional($pedido->envio)->eEstado;
                            @endphp

                            @if($estadoEnvio)
                                <span class="badge bg-info">
                                    {{ ucfirst($estadoEnvio) }}
                                </span>
                            @else
                                <span class="text-muted">No asignado</span>
                            @endif
                        </td>

                        <td>
                            <span class="badge bg-{{ estadoPedidoColor($pedido->eEstado) }}">
                                {{ estadoPedidoTexto($pedido->eEstado) }}
                            </span>
                        </td>

                        <td class="text-end">
                            <a href="{{ route('admin.pedidos.show', $pedido->id_pedido) }}"
                               class="btn btn-outline-primary btn-sm">
                                Ver
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            No hay pedidos registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $pedidos->links() }}
</div>

@endsection
