@extends('admin.productos.administrar-productos')

@section('title', 'Marcas de Agave')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-industry me-2"></i>Marcas de Agave</h1>
    <a href="{{ route('marcas.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Nueva Marca
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        @if($marcas->count() > 0)
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
        @else
        <div class="text-center py-5">
            <i class="fas fa-industry fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">No hay marcas registradas</h4>
            <p class="text-muted">Comienza agregando marcas de bebidas de agave</p>
            <a href="{{ route('marcas.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Crear Primera Marca
            </a>
        </div>
        @endif
    </div>
</div>
@endsection