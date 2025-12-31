@extends('layouts.admins')

@section('title', 'Postventa')

@section('content')

<h2 class="fw-bold mb-3">🔄 Solicitudes de Postventa</h2>

{{-- Filtros rápidos --}}
<div class="mb-3 d-flex gap-2">
    <a href="{{ route('admin.postventa.index', ['estado' => 'pendiente']) }}"
       class="btn btn-warning btn-sm">Pendientes</a>

    <a href="{{ route('admin.postventa.index', ['tipo' => 'cancelacion']) }}"
       class="btn btn-outline-secondary btn-sm">Cancelaciones</a>

    <a href="{{ route('admin.postventa.index', ['tipo' => 'devolucion']) }}"
       class="btn btn-outline-secondary btn-sm">Devoluciones</a>

    <a href="{{ route('admin.postventa.index', ['estado' => 'reembolsada']) }}"
       class="btn btn-success btn-sm">Reembolsadas</a>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Tipo</th>
                    <th>Cliente</th>
                    <th>Pedido</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($solicitudes as $solicitud)
                    <tr>
                        <td>#{{ $solicitud->id_solicitud }}</td>
                        <td>{{ ucfirst($solicitud->eTipo) }}</td>
                        <td>{{ $solicitud->pedido->usuario->vNombre }}</td>
                        <td>#{{ $solicitud->id_pedido }}</td>
                        <td>${{ number_format($solicitud->pedido->venta->dTotal, 2) }}</td>
                        <td>
                            <span class="badge bg-warning">
                                {{ ucfirst($solicitud->eEstado) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.postventa.show', $solicitud) }}"
                               class="btn btn-outline-primary btn-sm">
                                Ver
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            No hay solicitudes
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $solicitudes->links() }}
</div>

@endsection
