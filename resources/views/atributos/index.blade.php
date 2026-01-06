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

    <div class="card shadow-sm">
        <div class="card-body">
            @if($atributos->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
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
                <i class="fas fa-tags fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">No hay atributos registrados</h4>
                <p class="text-muted">Comienza creando tu primer atributo para mezcales</p>
                <a href="{{ route('atributos.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Crear Primer Atributo
                </a>
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
</script>
@endsection