@extends('layouts.admins')

@section('title', 'Pedidos')

@section('content')

<h2 class="fw-bold mb-3">
    Pedidos
</h2>

{{-- ============================
    FILTROS RÁPIDOS POR FECHA
============================ --}}
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

{{-- ============================
    BUSQUEDA RÁPIDA (USO DIARIO)
============================ --}}
<form method="GET" action="{{ route('admin.pedidos.index') }}" class="card mb-3 shadow-sm">
    <div class="card-body">
        <div class="row g-3 align-items-end">

            <div class="col-md-2">
                <input type="text"
                       name="pedido_id"
                       value="{{ request('pedido_id') }}"
                       class="form-control"
                       placeholder="# Pedido">
            </div>

            <div class="col-md-4">
                <input type="text"
                       name="cliente"
                       value="{{ request('cliente') }}"
                       class="form-control"
                       placeholder="Cliente">
            </div>

            <div class="col-md-3">
                <select name="estado_pedido" class="form-select">
                    <option value="">Estado del pedido</option>
                    <option value="pendiente" {{ request('estado_pedido')=='pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="pagado" {{ request('estado_pedido')=='pagado' ? 'selected' : '' }}>Pagado</option>
                    <option value="enviado" {{ request('estado_pedido')=='enviado' ? 'selected' : '' }}>Enviado</option>
                    <option value="entregado" {{ request('estado_pedido')=='entregado' ? 'selected' : '' }}>Entregado</option>
                    <option value="cancelado" {{ request('estado_pedido')=='cancelado' ? 'selected' : '' }}>Cancelado</option>
                </select>
            </div>

            <div class="col-md-3 d-flex gap-2">
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

{{-- ============================
    FILTROS AVANZADOS (OCULTOS)
============================ --}}
<div class="accordion mb-4" id="filtrosAvanzados">
    <div class="accordion-item">
        <h2 class="accordion-header">
            <button class="accordion-button collapsed"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#collapseFiltros">
                Filtros avanzados
            </button>
        </h2>

        <div id="collapseFiltros" class="accordion-collapse collapse">
            <div class="accordion-body">

                <form method="GET" action="{{ route('admin.pedidos.index') }}">
                    <div class="row g-3">

                        <div class="col-md-3">
                            <label class="form-label">Fecha desde</label>
                            <input type="date"
                                   name="fecha_desde"
                                   value="{{ request('fecha_desde') }}"
                                   class="form-control">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Fecha hasta</label>
                            <input type="date"
                                   name="fecha_hasta"
                                   value="{{ request('fecha_hasta') }}"
                                   class="form-control">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Método de pago</label>
                            <select name="metodo_pago" class="form-select">
                                <option value="">Todos</option>
                                <option value="paypal" {{ request('metodo_pago')=='paypal' ? 'selected' : '' }}>PayPal</option>
                                <option value="stripe" {{ request('metodo_pago')=='stripe' ? 'selected' : '' }}>Stripe</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Estado envío</label>
                            <select name="estado_envio" class="form-select">
                                <option value="">Todos</option>
                                <option value="pendiente" {{ request('estado_envio')=='pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="enviado" {{ request('estado_envio')=='enviado' ? 'selected' : '' }}>Enviado</option>
                                <option value="entregado" {{ request('estado_envio')=='entregado' ? 'selected' : '' }}>Entregado</option>
                                <option value="devuelto" {{ request('estado_envio')=='devuelto' ? 'selected' : '' }}>Devuelto</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Total mínimo</label>
                            <input type="number"
                                   step="0.01"
                                   name="total_min"
                                   value="{{ request('total_min') }}"
                                   class="form-control">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Total máximo</label>
                            <input type="number"
                                   step="0.01"
                                   name="total_max"
                                   value="{{ request('total_max') }}"
                                   class="form-control">
                        </div>

                        <div class="col-md-3 d-flex align-items-end gap-2">
                            <button class="btn btn-primary w-100">
                                Aplicar filtros
                            </button>

                            <a href="{{ route('admin.pedidos.index') }}"
                               class="btn btn-outline-secondary w-100">
                                Limpiar
                            </a>
                        </div>

                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

{{-- ============================
    INDICADOR DE FILTROS ACTIVOS
============================ --}}
@if(request()->except('page'))
    <div class="mb-3">
        <small class="text-muted">Filtros activos:</small>

        @foreach(request()->except('page') as $key => $value)
            <span class="badge bg-secondary me-1">
                {{ ucfirst(str_replace('_',' ', $key)) }}: {{ $value }}
            </span>
        @endforeach
    </div>
@endif

{{-- ============================
    TABLA DE PEDIDOS
============================ --}}
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
                            @php
                                // PRIORIDAD 1: Datos históricos del pedido
                                $nombre = trim(($pedido->vNombre ?? '').' '.($pedido->vApaterno ?? '').' '.($pedido->vAmaterno ?? ''));

                                // PRIORIDAD 2: Fallback a relación usuario (pedidos antiguos)
                                if (empty($nombre) && $pedido->usuario) {
                                    $nombre = trim($pedido->usuario->vNombre.' '.$pedido->usuario->vApaterno.' '.$pedido->usuario->vAmaterno);
                                }
                            @endphp

                            @if(!empty($nombre))
                                <div class="fw-semibold">{{ $nombre }}</div>
                                <small class="text-muted">
                                    {{ $pedido->vEmail ?? optional($pedido->usuario)->vEmail }}
                                </small>
                            @else
                                <span class="text-muted">Cliente no disponible</span>
                            @endif
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
                            @php $estadoEnvio = optional($pedido->envio)->eEstado; @endphp

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
