@extends('layouts.admins')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        
    <h1 class="mb-0">Lista de Detalles de Venta</h1>

    <!-- Buscador integrado -->
    <form action="{{ route('detalle_venta.index') }}" method="GET"
          class="d-flex"
          style="min-width: 300px; max-width: 400px;">
        <div class="input-group shadow-sm">
            <input type="text"
                   name="search"
                   class="form-control"
                   value="{{ request('search') }}"
                   placeholder="Buscar venta"
                   aria-label="Buscar detalles de venta">

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i>
            </button>

            @if(request('search'))
                <a href="{{ route('detalle_venta.index') }}" class="btn btn-outline-secondary">
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

    @if(request('search'))
        <div class="alert alert-info mb-3 d-flex justify-content-between align-items-center" id="searchAlert">
            <div>
                <i class="fas fa-search me-2"></i>
                Resultados de búsqueda para: <strong>"{{ request('search') }}"</strong>
            </div>
            <a href="{{ route('detalle_venta.index') }}" class="btn btn-sm btn-outline-light">
                <i class="fas fa-times"></i> Limpiar
            </a>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID Detalle venta</th>
                    <th>ID Venta</th>
                    <th>ID Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Subtotal</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($detallesVenta as $detalle)
                    <tr>
                        <td>{{ $detalle->id_detalle_venta }}</td>
                        <td>{{ $detalle->id_venta }}</td>
                        <td>{{ $detalle->id_producto }}</td>
                        <td>{{ $detalle->iCantidad }}</td>
                        <td>${{ number_format($detalle->dPrecio_unitario, 2) }}</td>
                        <td>${{ number_format($detalle->dSubtotal, 2) }}</td>
                        <td class="text-end">
                            <a href="{{ route('detalle_venta.show', $detalle->id_detalle_venta) }}" class="btn btn-sm btn-info">Ver</a>
                            <!-- Botón para descargar PDF añadido aquí -->
                            <a href="{{ route('detalle_venta.pdf', $detalle->id_detalle_venta) }}" class="btn btn-sm btn-secondary">📥 PDF</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            @if(request('search'))
                                <h5>No se encontraron resultados</h5>
                                <p>No hay detalles de venta que coincidan con "{{ request('search') }}"</p>
                                <a href="{{ route('detalle_venta.index') }}" class="btn btn-outline-primary">
                                    Ver todos los detalles
                                </a>
                            @else
                                <h5>No hay detalles de venta registrados</h5>
                                <p>No hay detalles de venta en el sistema</p>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    @if($detallesVenta->count() > 0)
        <div class="d-flex justify-content-center mt-4">
            {{ $detallesVenta->links() }}
        </div>
    @endif

    <!-- Solo Total en subtotales alineado a la DERECHA -->
    @if($detallesVenta->count() > 0)
        <div class="mt-3 text-end">
            <span class="text-muted">Total en subtotales: </span>
            <strong>${{ number_format($detallesVenta->sum('dSubtotal'), 2) }}</strong>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const successAlert = document.getElementById('successAlert');
    if (successAlert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(successAlert);
            bsAlert.close();
        }, 2000);
    }

    const searchAlert = document.getElementById('searchAlert');
    if (searchAlert) {
        setTimeout(function() {
            searchAlert.style.transition = 'opacity 0.5s';
            searchAlert.style.opacity = '0';
            setTimeout(function() {
                searchAlert.style.display = 'none';
            }, 500);
        }, 3000);
    }
});
</script>
@endsection