@extends('admin.productos.administrar-productos')

@section('title', 'Gestión de Variaciones')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1><i class="fas fa-cubes me-2"></i>Gestión de Variaciones</h1>
            <p class="text-muted">Administra las variaciones de tus productos</p>
        </div>
        <div>
            <a href="{{ route('productos.index') }}" class="btn btn-primary">
                <i class="fas fa-wine-bottle me-1"></i> Ir a Productos
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filtrar Productos</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Buscar producto:</label>
                    <input type="text" class="form-control" id="searchProduct" placeholder="Nombre, SKU...">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Estado:</label>
                    <select class="form-select" id="filterStatus">
                        <option value="">Todos</option>
                        <option value="with_variations">Con variaciones</option>
                        <option value="no_variations">Sin variaciones</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-list-alt me-2"></i>Productos con Variaciones</h5>
            <span class="badge bg-light text-dark" id="productCount">{{ $productos->count() }} productos</span>
        </div>
        <div class="card-body">
            @if($productos->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover" id="productsTable">
                        <thead class="table-dark">
                            <tr>
                                <th>Producto</th>
                                <th>SKU Base</th>
                                <th>Categoría</th>
                                <th>Marca</th>
                                <th>Variaciones</th>
                                <th>Stock Total</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productos as $producto)
                                @php
                                    $variacionesCount = $producto->variaciones->count();
                                    $stockTotal = $producto->tieneVariaciones() 
                                        ? $producto->variaciones->sum('iStock') 
                                        : $producto->iStock;
                                @endphp
                                <tr class="product-row" 
                                    data-name="{{ strtolower($producto->vNombre) }}"
                                    data-variations="{{ $variacionesCount }}">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if(count($producto->imagenes) > 0)
                                                <img src="{{ $producto->imagenes[0] }}" 
                                                     alt="{{ $producto->vNombre }}"
                                                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px; margin-right: 10px;">
                                            @else
                                                <div style="width: 50px; height: 50px; background-color: #f8f9fa; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #6c757d; margin-right: 10px;">
                                                    <i class="fas fa-wine-bottle"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <strong>{{ $producto->vNombre }}</strong>
                                                <br>
                                                <small class="text-muted">ID: {{ $producto->id_producto }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <code>{{ $producto->vCodigo_barras }}</code>
                                    </td>
                                    <td>
                                        @if($producto->categoria)
                                            <span class="badge bg-info">{{ $producto->categoria->vNombre }}</span>
                                        @else
                                            <span class="badge bg-secondary">Sin categoría</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($producto->marca)
                                            <span class="badge bg-secondary">{{ $producto->marca->vNombre }}</span>
                                        @else
                                            <span class="badge bg-secondary">Sin marca</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($variacionesCount > 0)
                                            <span class="badge bg-success">{{ $variacionesCount }} variaciones</span>
                                            <div class="mt-1">
                                                @foreach($producto->variaciones->take(2) as $variacion)
                                                    <small class="d-block">
                                                        <code>{{ $variacion->vSKU }}</code>
                                                    </small>
                                                @endforeach
                                                @if($variacionesCount > 2)
                                                    <small class="text-muted">+{{ $variacionesCount - 2 }} más</small>
                                                @endif
                                            </div>
                                        @else
                                            <span class="badge bg-secondary">Sin variaciones</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $stockTotal > 50 ? 'bg-success' : ($stockTotal > 10 ? 'bg-warning' : 'bg-danger') }}">
                                            {{ $stockTotal }} unidades
                                        </span>
                                        <small class="d-block text-muted">
                                            @if($producto->tieneVariaciones())
                                                @php
                                                    $precioMin = $producto->variaciones->min('dPrecio');
                                                    $precioMax = $producto->variaciones->max('dPrecio');
                                                @endphp
                                                @if($precioMin == $precioMax)
                                                    Precio: ${{ number_format($precioMin, 2) }}
                                                @else
                                                    Precio: ${{ number_format($precioMin, 2) }} - ${{ number_format($precioMax, 2) }}
                                                @endif
                                            @else
                                                Precio: ${{ number_format($producto->dPrecio_venta, 2) }}
                                            @endif
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge {{ $producto->bActivo ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $producto->bActivo ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            @if($variacionesCount > 0)
                                                <a href="{{ route('variaciones.show', $producto->id_producto) }}" 
                                                   class="btn btn-primary" title="Ver variaciones">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endif
                                            
                                            <a href="{{ route('variaciones.create', $producto->id_producto) }}" 
                                               class="btn btn-success" title="Crear variación">
                                                <i class="fas fa-plus"></i>
                                            </a>
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
                    <p class="text-muted">Comienza agregando productos o creando variaciones para productos existentes.</p>
                    <div class="mt-3">
                        <a href="{{ route('productos.index') }}" class="btn btn-primary me-2">
                            <i class="fas fa-wine-bottle me-1"></i> Ir a Productos
                        </a>
                        <a href="{{ route('productos.create') }}" class="btn btn-success">
                            <i class="fas fa-plus me-1"></i> Crear Producto
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchProduct');
    const filterStatus = document.getElementById('filterStatus');
    const productRows = document.querySelectorAll('.product-row');
    const productCount = document.getElementById('productCount');
    
    function filterProducts() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = filterStatus.value;
        
        let visibleCount = 0;
        
        productRows.forEach(row => {
            const productName = row.dataset.name;
            const variations = parseInt(row.dataset.variations);
            
            let visible = true;
            
            if (searchTerm && !productName.includes(searchTerm)) {
                visible = false;
            }
            
            if (statusValue) {
                if (statusValue === 'with_variations' && variations === 0) {
                    visible = false;
                } else if (statusValue === 'no_variations' && variations > 0) {
                    visible = false;
                }
            }
            
            if (visible) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        productCount.textContent = `${visibleCount} productos`;
    }
    
    searchInput.addEventListener('input', filterProducts);
    filterStatus.addEventListener('change', filterProducts);
});
</script>

<style>
.product-row:hover {
    background-color: #f8f9fa;
}

.table th {
    background-color: #2E8B57;
    color: white;
    position: sticky;
    top: 0;
}

.badge {
    font-size: 12px;
    font-weight: 600;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 12px;
}

.btn-group-sm .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.table-dark th {
    background-color: #343a40;
}
</style>
@endsection