@extends('layouts.admins')

@section('content')
<div class="container">

    <!-- Título + Buscador -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0">Lista de Ventas</h1>

        <form action="{{ route('ventas.index') }}" method="GET" class="d-flex">
            <div class="input-group" style="width: 280px;">
                <input
                    type="text"
                    name="search"
                    class="form-control"
                    placeholder="Buscar venta..."
                    value="{{ request('search') }}"
                >
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-search"></i>
                </button>
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
                    <th>ID Venta</th>
                    <th>ID Pedido</th>
                    <th>ID Usuario</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Método Pago</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ventas as $venta)
                    <tr>
                        <td>{{ $venta->id_venta }}</td>
                        <td>{{ $venta->id_pedido }}</td>
                        <td>{{ $venta->id_usuario }}</td>
                        <td>
                            @if($venta->tFecha_venta instanceof \DateTime)
                                {{ $venta->tFecha_venta->format('d/m/Y H:i') }}
                            @else
                                {{ \Carbon\Carbon::parse($venta->tFecha_venta)->format('d/m/Y H:i') }}
                            @endif
                        </td>
                        <td>${{ number_format($venta->dTotal, 2) }}</td>
                        <td>
                            <span class="badge bg-info text-capitalize">{{ $venta->eMetodo_pago }}</span>
                        </td>
                        <td>
                            @if($venta->eEstado == 'completada')
                                <span class="badge bg-success text-capitalize">{{ $venta->eEstado }}</span>
                            @elseif($venta->eEstado == 'devuelta')
                                <span class="badge bg-warning text-capitalize">{{ $venta->eEstado }}</span>
                            @elseif($venta->eEstado == 'reembolsada')
                                <span class="badge bg-info text-capitalize">{{ $venta->eEstado }}</span>
                            @else
                                <span class="badge bg-danger text-capitalize">{{ $venta->eEstado }}</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('ventas.show', $venta->id_venta) }}" class="btn btn-sm btn-info">Ver</a>
                            <a href="{{ route('ventas.edit', $venta->id_venta) }}" class="btn btn-sm btn-warning">Editar</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <h5>No hay ventas registradas</h5>
                            <p>No hay ventas en el sistema</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    @if($ventas->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $ventas->links() }}
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
