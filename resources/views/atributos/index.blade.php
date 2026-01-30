@extends('layouts.app')

@section('title', 'Atributos de Productos')
@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-tags me-2"></i>Atributos de Productos</h1>
        <a href="{{ route('atributos.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Nuevo Atributo
        </a>
    </div>

    <!-- BUSCADOR - NUEVO (se agrega arriba de la tabla) -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('atributos.index') }}" id="searchForm">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="search" class="form-label fw-bold">
                            <i class="fas fa-search me-1"></i> Buscar por ID o Nombre
                        </label>
                        <div class="input-group">
                            <input type="text" 
                                   name="search" 
                                   id="search" 
                                   class="form-control"
                                   value="{{ request('search') }}"
                                   placeholder="Ej: 1 o 'Tamaño'">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-search"></i>
                            </button>
                            @if(request()->has('search'))
                            <a href="{{ route('atributos.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i>
                            </a>
                            @endif
                        </div>
                        <small class="text-muted mt-1 d-block">
                            <i class="fas fa-info-circle me-1"></i>
                            Puedes buscar por ID numérico o por cualquier parte del nombre
                        </small>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="orden" class="form-label fw-bold">
                            <i class="fas fa-sort me-1"></i> Ordenar por
                        </label>
                        <select name="orden" id="orden" class="form-select" onchange="this.form.submit()">
                            <option value="">Seleccionar...</option>
                            <option value="nombre" {{ request('orden') == 'nombre' ? 'selected' : '' }}>Nombre (A-Z)</option>
                            <option value="nombre_desc" {{ request('orden') == 'nombre_desc' ? 'selected' : '' }}>Nombre (Z-A)</option>
                            <option value="id" {{ request('orden') == 'id' ? 'selected' : '' }}>ID (Menor a Mayor)</option>
                            <option value="id_desc" {{ request('orden') == 'id_desc' ? 'selected' : '' }}>ID (Mayor a Menor)</option>
                        </select>
                    </div>
                </div>
                
                <!-- Muestra filtros aplicados -->
                @if(request()->has('search') || request()->has('orden'))
                <div class="mt-3">
                    <small class="text-muted">
                        <i class="fas fa-filter me-1"></i>
                        Filtros aplicados:
                        @if(request('search'))
                        <span class="badge bg-info me-1">Búsqueda: "{{ request('search') }}"</span>
                        @endif
                        @if(request('orden'))
                        @php
                            $ordenTexto = '';
                            switch(request('orden')) {
                                case 'nombre':
                                    $ordenTexto = 'Nombre (A-Z)';
                                    break;
                                case 'nombre_desc':
                                    $ordenTexto = 'Nombre (Z-A)';
                                    break;
                                case 'id':
                                    $ordenTexto = 'ID (Menor a Mayor)';
                                    break;
                                case 'id_desc':
                                    $ordenTexto = 'ID (Mayor a Menor)';
                                    break;
                                default:
                                    $ordenTexto = '';
                                    break;
                            }
                        @endphp
                        <span class="badge bg-info">
                            Orden: {{ $ordenTexto }}
                        </span>
                        @endif
                    </small>
                </div>
                @endif
            </form>
        </div>
    </div>

    <!-- TABLA ORIGINAL (se mantiene igual) -->
    <div class="card shadow-sm">
        <div class="card-body">
            @if($atributos->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th> <!-- Agregado columna ID -->
                            <th>Nombre</th>
                            <th>Slug</th>
                            <th>Valores</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($atributos as $atributo)
                        <tr>
                            <td>
                                <span class="badge bg-secondary">#{{ $atributo->id_atributo }}</span>
                            </td>
                            <td>
                                <strong>{{ $atributo->vNombre }}</strong>
                                @if($atributo->tDescripcion)
                                <br>
                                <small class="text-muted">{{ Str::limit($atributo->tDescripcion, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                <code>{{ $atributo->vSlug }}</code>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    {{ $atributo->valores_count }} valores
                                </span>
                                <a href="{{ route('atributos.valores', $atributo) }}" class="btn btn-sm btn-outline-primary ms-2">
                                    <i class="fas fa-cog"></i> Gestionar
                                </a>
                            </td>
                            <td>
                                <span class="badge {{ $atributo->bActivo ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $atributo->bActivo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('atributos.edit', $atributo) }}" class="btn btn-warning" 
                                       title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('atributos.valores', $atributo) }}" class="btn btn-info" 
                                       title="Ver valores">
                                        <i class="fas fa-list"></i>
                                    </a>
                                    <form action="{{ route('atributos.destroy', $atributo) }}" method="POST" 
                                          class="d-inline" onsubmit="return confirmDelete()">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                @if(request()->has('search'))
                <i class="fas fa-search fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">No se encontraron resultados</h4>
                <p class="text-muted">Intenta con otros términos de búsqueda</p>
                <a href="{{ route('atributos.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-1"></i> Limpiar búsqueda
                </a>
                @else
                <i class="fas fa-tags fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">No hay atributos registrados</h4>
                <p class="text-muted">Comienza creando tu primer atributo para mezcales</p>
                <a href="{{ route('atributos.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Crear Primer Atributo
                </a>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function confirmDelete() {
    return confirm('¿Estás seguro de que deseas eliminar este atributo? Todos sus valores también se eliminarán.');
}

// Auto-focus en el campo de búsqueda
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    if (searchInput && searchInput.value) {
        searchInput.focus();
        searchInput.select();
    }
});
</script>
@endsection