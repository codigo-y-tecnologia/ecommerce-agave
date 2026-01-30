@extends('layouts.app')

@section('title', 'Etiquetas de Productos')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-tags me-2"></i>Etiquetas de Productos</h1>
    <a href="{{ route('etiquetas.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Nueva Etiqueta
    </a>
</div>

<!-- Buscador -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('etiquetas.index') }}" method="GET" class="row g-3">
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Buscar por ID, nombre o descripción..." 
                           value="{{ $search ?? '' }}">
                </div>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-1"></i> Buscar
                </button>
            </div>
            @if($search ?? '')
            <div class="col-12">
                <div class="alert alert-info alert-dismissible fade show py-2" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    Resultados para: <strong>"{{ $search }}"</strong>
                    <a href="{{ route('etiquetas.index') }}" class="btn btn-sm btn-outline-info ms-3">
                        <i class="fas fa-times me-1"></i> Limpiar búsqueda
                    </a>
                </div>
            </div>
            @endif
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        @if($etiquetas->count() > 0)
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Productos</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($etiquetas as $etiqueta)
                    <tr>
                        <td>{{ $etiqueta->id_etiqueta }}</td>
                        <td>
                            <strong>{{ $etiqueta->vNombre }}</strong>
                        </td>
                        <td>
                            @if($etiqueta->tDescripcion)
                                {{ Str::limit($etiqueta->tDescripcion, 50) }}
                            @else
                                <span class="text-muted">Sin descripción</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-info">
                                {{ $etiqueta->productos->count() }} productos
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('etiquetas.edit', $etiqueta) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <button type="button" class="btn btn-danger" 
                                        onclick="confirmarEliminacion({{ $etiqueta->id_etiqueta }}, '{{ $etiqueta->vNombre }}')">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                                <form id="delete-form-{{ $etiqueta->id_etiqueta }}" 
                                      action="{{ route('etiquetas.destroy', $etiqueta) }}" 
                                      method="POST" class="d-none">
                                    @csrf @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Mensaje cuando no hay resultados de búsqueda -->
        @elseif($search ?? '')
        <div class="text-center py-5">
            <i class="fas fa-search fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">No se encontraron resultados</h4>
            <p class="text-muted">No hay etiquetas que coincidan con "{{ $search }}"</p>
            <a href="{{ route('etiquetas.index') }}" class="btn btn-primary">
                <i class="fas fa-list me-1"></i> Ver todas las etiquetas
            </a>
        </div>
        @else
        <!-- Mensaje cuando no hay datos -->
        <div class="text-center py-5">
            <i class="fas fa-tags fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">No hay etiquetas registradas</h4>
            <p class="text-muted">Comienza agregando etiquetas para organizar tus productos</p>
            <a href="{{ route('etiquetas.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Crear Primera Etiqueta
            </a>
        </div>
        @endif
    </div>
</div>

<!-- Modal de confirmación para eliminar -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i> Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar la etiqueta <strong id="etiquetaNombre"></strong>?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <small>Esta acción no se puede deshacer. Los productos asociados mantendrán esta etiqueta hasta que sea removida manualmente.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash me-1"></i> Sí, Eliminar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmarEliminacion(id, nombre) {
    // Establecer el nombre en el modal
    document.getElementById('etiquetaNombre').textContent = '"' + nombre + '"';
    
    // Configurar el botón de confirmación
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    confirmBtn.onclick = function() {
        document.getElementById('delete-form-' + id).submit();
    };
    
    // Mostrar el modal
    const modal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
    modal.show();
}
</script>
@endpush