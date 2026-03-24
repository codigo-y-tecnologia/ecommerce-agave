@extends('admin.productos.administrar-productos')

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
                        <th>Precio Final</th>
                        <th>Stock</th>
                        <th>Marca</th>
                        <th>Categoría</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($productos as $producto)
                    @php
                        // Usar el precio final del PRODUCTO PADRE directamente de la base de datos
                        $precioFinalProducto = $producto->dPrecio_final ?? $producto->dPrecio_venta;
                    @endphp
                    <tr>
                        <td><span class="badge bg-secondary">{{ $producto->vCodigo_barras }}</span></td>
                        <td>{{ $producto->vNombre }}</td>
                        <td>
                            <span class="fw-bold">${{ number_format($precioFinalProducto, 2) }}</span>
                            
                            @if($producto->tieneDescuentoActivo())
                                <br>
                                <small class="text-success">
                                    <i class="fas fa-tag me-1"></i>-{{ $producto->porcentaje_descuento }}%
                                </small>
                            @endif
                            
                            @if($producto->tieneVariaciones())
                                <br>
                                <small class="text-muted">
                                    <i class="fas fa-cubes me-1"></i>Con variaciones
                                </small>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $producto->iStock > 10 ? 'bg-success' : ($producto->iStock > 0 ? 'bg-warning' : 'bg-danger') }}">
                                {{ number_format($producto->iStock) }} {{ $producto->iStock == 1 ? 'unidad' : 'unidades' }}
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
                                <!-- Botón Ver detalles -->
                                <a href="{{ route('productos.show', $producto->id_producto) }}" class="btn btn-info" title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <!-- Botón Gestionar atributos -->
                                <a href="{{ route('productos.atributos', $producto->id_producto) }}" class="btn btn-success" title="Gestionar atributos">
                                    <i class="fas fa-tags"></i>
                                </a>
                                
                                <!-- Botón Editar -->
                                <a href="{{ route('productos.edit', $producto->id_producto) }}" class="btn btn-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <!-- Botón Eliminar -->
                                <form action="{{ route('productos.destroy', $producto->id_producto) }}" method="POST" class="d-inline" onsubmit="return confirmDelete(event, this)">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDelete(event, form) {
    event.preventDefault();
    
    Swal.fire({
        title: '¿Estás seguro?',
        text: 'Esta acción no se puede deshacer. Se eliminarán todas las variaciones, imágenes y relaciones asociadas.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
    
    return false;
}

// Mostrar mensajes de sesión con SweetAlert
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        Swal.fire({
            title: '¡Éxito!',
            text: "{{ session('success') }}",
            icon: 'success',
            timer: 3000,
            showConfirmButton: false
        });
    @endif
    
    @if(session('error'))
        Swal.fire({
            title: 'Error',
            text: "{{ session('error') }}",
            icon: 'error',
            timer: 3000,
            showConfirmButton: false
        });
    @endif
    
    @if(session('swal_success'))
        Swal.fire({
            title: '¡Éxito!',
            text: "{{ session('success') ?? 'Operación exitosa' }}",
            icon: 'success',
            timer: 3000,
            showConfirmButton: false
        });
    @endif
    
    @if(session('swal_error'))
        Swal.fire({
            title: 'Error',
            text: "{{ session('error') ?? 'Error en la operación' }}",
            icon: 'error',
            timer: 3000,
            showConfirmButton: false
        });
    @endif
});
</script>
@endsection