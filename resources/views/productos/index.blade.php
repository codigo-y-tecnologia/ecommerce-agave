@extends('layouts.app')

@section('title', 'Productos de Agave')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-wine-bottle me-2"></i>Productos de Agave</h1>
    <a href="{{ route('productos.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Nuevo Producto
    </a>
</div>

<!-- BUSCADOR PARA PRODUCTOS -->
<div class="card mb-4">
    <div class="card-header bg-info text-white">
        <i class="fas fa-search me-2"></i>Buscar Productos
    </div>
    <div class="card-body">
        <form action="{{ route('productos.index') }}" method="GET" class="row g-3">
            <div class="col-md-8">
                <input type="text" 
                       name="search" 
                       class="form-control" 
                       placeholder="Buscar por nombre o SKU..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-1"></i> Buscar
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        @if($productos->count() > 0)
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>SKU</th>
                        <th>Nombre</th>
                        <th>Precio Venta</th>
                        <th>Stock</th>
                        <th>Marca</th>
                        <th>Categoría</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($productos as $producto)
                    <tr>
                        <td><span class="badge bg-secondary">{{ $producto->vCodigo_barras }}</span></td>
                        <td>{{ $producto->vNombre }}</td>
                        <td>
                            @if($producto->tieneVariaciones())
                                ${{ $producto->rangoPrecios }}
                            @else
                                ${{ number_format($producto->dPrecio_venta, 2) }}
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $producto->iStock > 10 ? 'bg-success' : 'bg-warning' }}">
                                {{ $producto->iStock }} unidades
                            </span>
                        </td>
                        <td>{{ $producto->marca->vNombre ?? 'N/A' }}</td>
                        <td>{{ $producto->categoria->vNombre ?? 'N/A' }}</td>
                        <td>
                            <span class="badge {{ $producto->bActivo ? 'bg-success' : 'bg-secondary' }}">
                                {{ $producto->bActivo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('productos.show', $producto) }}" class="btn btn-info" 
                                   title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('productos.atributos', $producto) }}" class="btn btn-success" 
                                   title="Gestionar atributos">
                                    <i class="fas fa-tags"></i>
                                </a>
                                <a href="{{ route('productos.edit', $producto) }}" class="btn btn-warning" 
                                   title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('productos.destroy', $producto) }}" method="POST" 
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
            <i class="fas fa-wine-bottle fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">No hay productos registrados</h4>
            <p class="text-muted">Comienza agregando tu primer producto de agave</p>
            <a href="{{ route('productos.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Crear Primer Producto
            </a>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
function confirmDelete() {
    return confirm('¿Estás seguro de que deseas eliminar este producto? Esta acción no se puede deshacer.');
}
</script>
@endsection