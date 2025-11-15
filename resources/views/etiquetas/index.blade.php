@extends('admin.productos.administrar-productos')

@section('title', 'Etiquetas de Productos')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-tags me-2"></i>Etiquetas de Productos</h1>
    <a href="{{ route('etiquetas.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Nueva Etiqueta
    </a>
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
                                <form action="{{ route('etiquetas.destroy', $etiqueta) }}" method="POST" 
                                      class="d-inline" onsubmit="return confirm('¿Eliminar etiqueta?')">
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
        @else
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
@endsection