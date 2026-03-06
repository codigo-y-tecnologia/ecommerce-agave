@extends('layouts.admins')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">

        <h1 class="mb-0">Lista de Cupones Usados</h1>

        <!-- Buscador integrado -->
        <form action="{{ route('cupones_usados.index') }}" method="GET"
              class="d-flex"
              style="min-width: 300px; max-width: 400px;">
            <div class="input-group shadow-sm">
                <input type="text"
                       name="search"
                       class="form-control"
                       value="{{ request('search') }}"
                       placeholder="Buscar por venta o token"
                       aria-label="Buscar cupones usados">

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                </button>

                @if(request('search'))
                    <a href="{{ route('cupones_usados.index') }}" class="btn btn-outline-secondary">
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
            <a href="{{ route('cupones_usados.index') }}" class="btn btn-sm btn-outline-light">
                <i class="fas fa-times"></i> Limpiar
            </a>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID Cupón</th>
                    <th>ID Venta</th>
                    <th>ID Usuario</th>
                    <th>Guest Token</th>
                    <th>Fecha de Uso</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cuponesUsados as $cupon)
                    <tr>
                        <td>{{ $cupon->id_cupon }}</td>
                        <td>{{ $cupon->id_venta }}</td>
                        <td>{{ $cupon->id_usuario ?? '—' }}</td>
                        <td>
                            @if($cupon->guest_token)
                                <span class="badge bg-secondary font-monospace">{{ $cupon->guest_token }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($cupon->tFecha_uso)->format('d/m/Y H:i') }}</td>
                        <td class="text-end">
                            <a href="{{ route('cupones_usados.show', $cupon->id_cupon) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> Ver
                            </a>
                            <form action="{{ route('cupones_usados.destroy', $cupon->id_cupon) }}"
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('¿Eliminar este cupón usado?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            @if(request('search'))
                                <h5>No se encontraron resultados</h5>
                                <p>No hay cupones usados que coincidan con "{{ request('search') }}"</p>
                                <a href="{{ route('cupones_usados.index') }}" class="btn btn-outline-primary">
                                    Ver todos los cupones
                                </a>
                            @else
                                <h5>No hay cupones usados registrados</h5>
                                <p>Aún no se ha registrado el uso de ningún cupón.</p>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    @if($cuponesUsados->count() > 0)
        <div class="d-flex justify-content-center mt-4">
            {{ $cuponesUsados->links() }}
        </div>
    @endif

    <!-- Total de registros -->
    @if($cuponesUsados->count() > 0)
        <div class="mt-3 text-end">
            <span class="text-muted">Total de cupones usados: </span>
            <strong>{{ $cuponesUsados->total() }}</strong>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const successAlert = document.getElementById('successAlert');
    if (successAlert) {
        setTimeout(function () {
            const bsAlert = new bootstrap.Alert(successAlert);
            bsAlert.close();
        }, 2000);
    }

    const searchAlert = document.getElementById('searchAlert');
    if (searchAlert) {
        setTimeout(function () {
            searchAlert.style.transition = 'opacity 0.5s';
            searchAlert.style.opacity = '0';
            setTimeout(function () {
                searchAlert.style.display = 'none';
            }, 500);
        }, 3000);
    }
});
</script>
@endsection