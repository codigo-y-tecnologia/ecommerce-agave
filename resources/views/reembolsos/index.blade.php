@extends('layouts.admins')

@section('content')
<div class="container">

    <!-- Título + Buscador -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h1 class="mb-0">Lista de Reembolsos</h1>

        <!-- Buscador -->
        <form action="{{ route('reembolsos.index') }}" method="GET" class="d-flex" style="min-width: 280px; max-width: 380px;">
            <div class="input-group shadow-sm">
                <input
                    type="text"
                    name="search"
                    class="form-control"
                    value="{{ request('search') }}"
                    placeholder="Buscar reembolso..."
                    aria-label="Buscar reembolsos"
                >
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                </button>

                @if(request('search'))
                    <a href="{{ route('reembolsos.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" id="successAlert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID Reembolso</th>
                    <th>ID Venta</th>
                    <th>Fecha Reembolso</th>
                    <th>Monto</th>
                    <th>Motivo</th>
                    <th>Método Pago</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reembolsos as $reembolso)
                    <tr>
                        <td>{{ $reembolso->id_reembolso }}</td>
                        <td>{{ $reembolso->id_venta }}</td>
                        <td>
                            @if($reembolso->tFecha_reembolso instanceof \DateTime)
                                {{ $reembolso->tFecha_reembolso->format('d/m/Y H:i') }}
                            @else
                                {{ \Carbon\Carbon::parse($reembolso->tFecha_reembolso)->format('d/m/Y H:i') }}
                            @endif
                        </td>
                        <td>${{ number_format($reembolso->dMonto, 2) }}</td>
                        <td>
                            @if($reembolso->vMotivo)
                                <span title="{{ $reembolso->vMotivo }}">
                                    {{ Str::limit($reembolso->vMotivo, 30) }}
                                </span>
                            @else
                                <span class="text-muted">Sin motivo</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-info text-capitalize">{{ $reembolso->eMetodo_pago }}</span>
                        </td>
                        <td>
                            @if($reembolso->eEstado == 'procesado')
                                <span class="badge bg-success text-capitalize">{{ $reembolso->eEstado }}</span>
                            @elseif($reembolso->eEstado == 'pendiente')
                                <span class="badge bg-warning text-capitalize">{{ $reembolso->eEstado }}</span>
                            @else
                                <span class="badge bg-danger text-capitalize">{{ $reembolso->eEstado }}</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('reembolsos.show', $reembolso->id_reembolso) }}" class="btn btn-sm btn-info">Ver</a>
                            <a href="{{ route('reembolsos.edit', $reembolso->id_reembolso) }}" class="btn btn-sm btn-warning">Editar</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <h5>No hay reembolsos registrados</h5>
                            <p>No hay reembolsos en el sistema</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    @if($reembolsos->count() > 0)
        <div class="d-flex justify-content-center mt-4">
            {{ $reembolsos->links() }}
        </div>
    @endif
</div>

<!-- SCRIPT PARA QUE EL MENSAJE DESAPAREZCA AUTOMÁTICAMENTE -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const successAlert = document.getElementById('successAlert');
    if (successAlert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(successAlert);
            bsAlert.close();
        }, 2000);
    }
});
</script>

@endsection
