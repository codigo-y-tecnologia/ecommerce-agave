@extends('admin.productos.administrar-productos')

@section('title', 'Categorías de Agave')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-tags me-2"></i>Categorías de Agave</h1>
    <a href="{{ route('categorias.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Nueva Categoría
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        @if($categorias->count() > 0)
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
                    @foreach($categorias as $categoria)
                    <tr>
                        <td>{{ $categoria->id_categoria }}</td>
                        <td>
                            <strong>{{ $categoria->vNombre }}</strong>
                        </td>
                        <td>
                            @if($categoria->tDescripcion)
                                {{ Str::limit($categoria->tDescripcion, 50) }}
                            @else
                                <span class="text-muted">Sin descripción</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-info">
                                {{ $categoria->productos->count() }} productos
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('categorias.edit', $categoria) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <form action="{{ route('categorias.destroy', $categoria) }}" method="POST" 
                                      class="d-inline" onsubmit="return confirm('¿Eliminar categoría?')">
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
            <h4 class="text-muted">No hay categorías registradas</h4>
            <p class="text-muted">Comienza agregando categorías para organizar tus productos</p>
            <a href="{{ route('categorias.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Crear Primera Categoría
            </a>
        </div>
        @endif
    </div>
</div>
@endsection