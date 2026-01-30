@extends('layouts.app')

@section('title', 'Marcas de Agave')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-industry me-2"></i>Marcas de Agave</h1>
    <a href="{{ route('marcas.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Nueva Marca
    </a>
</div>

<!-- Agregar mensajes de éxito/error -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Card del buscador simplificado -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="fas fa-search me-2"></i>Buscar Marcas</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('marcas.index') }}" method="GET">
            <div class="input-group">
                <input type="text" 
                       class="form-control" 
                       id="search" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Buscar por nombre de marca o ID..."
                       aria-label="Buscar marcas">
                <button class="btn btn-outline-primary" type="submit">
                    <i class="fas fa-search"></i> Buscar
                </button>
                @if(request('search'))
                <a href="{{ route('marcas.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times"></i> Limpiar
                </a>
                @endif
            </div>
            <small class="form-text text-muted mt-2 d-block">
                <i class="fas fa-info-circle me-1"></i> Puedes buscar por nombre de marca (ej: "Patrón") o por ID (ej: "5")
            </small>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        @if($marcas->count() > 0)
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <span class="text-muted">
                    Mostrando {{ $marcas->firstItem() ?? 0 }} - {{ $marcas->lastItem() ?? 0 }} de {{ $marcas->total() }} marcas
                </span>
                @if(request('search'))
                    <span class="badge bg-info ms-2">Búsqueda: "{{ request('search') }}"</span>
                @endif
            </div>
            <div>
                @if($marcas->total() > 0)
                    <span class="text-muted">Total: {{ $marcas->total() }} marcas</span>
                @endif
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'id_marca', 'order' => request('order', 'asc') == 'asc' ? 'desc' : 'asc']) }}" 
                               class="text-white text-decoration-none">
                                # 
                                @if(request('sort') == 'id_marca')
                                    <i class="fas fa-sort-{{ request('order', 'asc') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                @else
                                    <i class="fas fa-sort ms-1"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'vNombre', 'order' => request('order', 'asc') == 'asc' ? 'desc' : 'asc']) }}" 
                               class="text-white text-decoration-none">
                                Nombre 
                                @if(request('sort') == 'vNombre')
                                    <i class="fas fa-sort-{{ request('order', 'asc') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                @else
                                    <i class="fas fa-sort ms-1"></i>
                                @endif
                            </a>
                        </th>
                        <th>Descripción</th>
                        <th>Productos</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($marcas as $marca)
                    <tr>
                        <td>{{ $marca->id_marca }}</td>
                        <td>
                            <strong>{{ $marca->vNombre }}</strong>
                        </td>
                        <td>
                            @if($marca->tDescripcion)
                                {{ Str::limit($marca->tDescripcion, 50) }}
                            @else
                                <span class="text-muted">Sin descripción</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-info">
                                {{ $marca->productos->count() }} productos
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('marcas.edit', $marca) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <form action="{{ route('marcas.destroy', $marca) }}" method="POST" 
                                      class="d-inline" onsubmit="return confirm('¿Eliminar marca?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        @if($marcas->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $marcas->appends(request()->query())->links() }}
        </div>
        @endif
        
        @else
        <div class="text-center py-5">
            <i class="fas fa-industry fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">
                @if(request('search'))
                    No se encontraron marcas con "{{ request('search') }}"
                @else
                    No hay marcas registradas
                @endif
            </h4>
            <p class="text-muted">Comienza agregando marcas de bebidas de agave</p>
            <a href="{{ route('marcas.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Crear Primera Marca
            </a>
            @if(request('search'))
                <a href="{{ route('marcas.index') }}" class="btn btn-outline-secondary ms-2">
                    <i class="fas fa-list me-1"></i> Ver todas las marcas
                </a>
            @endif
        </div>
        @endif
    </div>
</div>

<script>
// Auto-enfocar el campo de búsqueda al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    if (searchInput) {
        searchInput.focus();
        searchInput.select();
    }
});
</script>
@endsection