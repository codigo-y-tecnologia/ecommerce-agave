@extends('layouts.app')

@section('title', 'Gestión de Valoraciones')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1><i class="fas fa-cubes me-2"></i>Gestión de Valoraciones</h1>
            <p class="text-muted">Administra las variaciones y combinaciones de atributos de tus productos</p>
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
                <div class="col-md-4 mb-3">
                    <label class="form-label">Buscar producto:</label>
                    <input type="text" class="form-control" id="searchProduct" placeholder="Nombre, SKU...">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Estado:</label>
                    <select class="form-select" id="filterStatus">
                        <option value="">Todos</option>
                        <option value="with_variations">Con variaciones</option>
                        <option value="no_variations">Sin variaciones</option>
                        <option value="with_attributes">Con atributos</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Categoría:</label>
                    <select class="form-select" id="filterCategory">
                        <option value="">Todas las categorías</option>
                        @foreach($productos->pluck('categoria')->unique()->filter() as $categoria)
                            @if($categoria)
                                <option value="{{ $categoria->id_categoria }}">{{ $categoria->vNombre }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-list-alt me-2"></i>Productos con Atributos</h5>
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
                                <th>Atributos</th>
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
                                    $atributosCount = $producto->tieneAtributos() ? count($producto->atributosAgrupados ?? []) : 0;
                                @endphp
                                <tr class="product-row" 
                                    data-name="{{ strtolower($producto->vNombre) }}"
                                    data-category="{{ $producto->id_categoria }}"
                                    data-variations="{{ $variacionesCount }}"
                                    data-attributes="{{ $atributosCount }}">
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
                                        @if($atributosCount > 0)
                                            <span class="badge bg-primary">{{ $atributosCount }} atributos</span>
                                            <div class="mt-1">
                                                @if($producto->atributosAgrupados)
                                                    @foreach($producto->atributosAgrupados as $atributo)
                                                        <small class="d-block">
                                                            <strong>{{ $atributo['nombre'] }}:</strong>
                                                            {{ implode(', ', array_column($atributo['valores'], 'valor')) }}
                                                        </small>
                                                    @endforeach
                                                @endif
                                            </div>
                                        @else
                                            <span class="badge bg-warning text-dark">Sin atributos</span>
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
                                        @if($producto->tieneVariaciones())
                                            @php
                                                $stockTotal = $producto->variaciones->sum('iStock');
                                            @endphp
                                            <span class="badge {{ $stockTotal > 50 ? 'bg-success' : ($stockTotal > 10 ? 'bg-warning' : 'bg-danger') }}">
                                                {{ $stockTotal }} unidades
                                            </span>
                                            <small class="d-block text-muted">
                                                @if($producto->precioMinimo == $producto->precioMaximo)
                                                    Precio: ${{ number_format($producto->precioMinimo, 2) }}
                                                @else
                                                    Precio: ${{ number_format($producto->precioMinimo, 2) }} - ${{ number_format($producto->precioMaximo, 2) }}
                                                @endif
                                            </small>
                                        @else
                                            <span class="badge {{ $producto->iStock > 10 ? 'bg-success' : ($producto->iStock > 0 ? 'bg-warning' : 'bg-danger') }}">
                                                {{ $producto->iStock }} unidades
                                            </span>
                                            <small class="d-block text-muted">
                                                Precio: ${{ number_format($producto->dPrecio_venta, 2) }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $producto->bActivo ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $producto->bActivo ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            @if($variacionesCount > 0)
                                                <a href="{{ route('valoraciones.show', $producto->id_producto) }}" 
                                                   class="btn btn-primary" title="Ver valoraciones">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endif
                                            
                                            <a href="{{ route('valoraciones.create', $producto->id_producto) }}" 
                                               class="btn btn-success" title="Crear valoración">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                            
                                            <a href="{{ route('productos.asignar-atributos', $producto->id_producto) }}" 
                                               class="btn btn-warning" title="Gestionar atributos">
                                                <i class="fas fa-tags"></i>
                                            </a>
                                            
                                            <a href="{{ route('productos.atributos', $producto->id_producto) }}" 
                                               class="btn btn-info" title="Generar combinaciones">
                                                <i class="fas fa-cogs"></i>
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
                    <h4 class="text-muted">No hay productos con atributos</h4>
                    <p class="text-muted">Para gestionar valoraciones, primero asigna atributos a los productos.</p>
                    <div class="mt-3">
                        <a href="{{ route('productos.index') }}" class="btn btn-primary me-2">
                            <i class="fas fa-wine-bottle me-1"></i> Ir a Productos
                        </a>
                        <a href="{{ route('atributos.index') }}" class="btn btn-secondary">
                            <i class="fas fa-tags me-1"></i> Gestionar Atributos
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>¿Cómo gestionar valoraciones?</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="text-center p-3 border rounded">
                        <div class="display-6 text-primary mb-2">1</div>
                        <h6>Asignar Atributos</h6>
                        <p class="small text-muted">Asigna atributos como tamaño, color, etc. a tu producto</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="text-center p-3 border rounded">
                        <div class="display-6 text-primary mb-2">2</div>
                        <h6>Generar Combinaciones</h6>
                        <p class="small text-muted">Genera automáticamente todas las combinaciones posibles</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="text-center p-3 border rounded">
                        <div class="display-6 text-primary mb-2">3</div>
                        <h6>Crear Valoraciones</h6>
                        <p class="small text-muted">Crea valoraciones manualmente con SKU, precio y stock únicos</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="text-center p-3 border rounded">
                        <div class="display-6 text-primary mb-2">4</div>
                        <h6>Gestionar Stock</h6>
                        <p class="small text-muted">Administra stock y precios de cada variación por separado</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchProduct');
    const filterStatus = document.getElementById('filterStatus');
    const filterCategory = document.getElementById('filterCategory');
    const productRows = document.querySelectorAll('.product-row');
    const productCount = document.getElementById('productCount');
    
    function filterProducts() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = filterStatus.value;
        const categoryValue = filterCategory.value;
        
        let visibleCount = 0;
        
        productRows.forEach(row => {
            const productName = row.dataset.name;
            const productCategory = row.dataset.category;
            const variations = parseInt(row.dataset.variations);
            const attributes = parseInt(row.dataset.attributes);
            
            let visible = true;
            
            if (searchTerm && !productName.includes(searchTerm)) {
                visible = false;
            }
            
            if (categoryValue && productCategory !== categoryValue) {
                visible = false;
            }
            
            if (statusValue) {
                if (statusValue === 'with_variations' && variations === 0) {
                    visible = false;
                } else if (statusValue === 'no_variations' && variations > 0) {
                    visible = false;
                } else if (statusValue === 'with_attributes' && attributes === 0) {
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
    filterCategory.addEventListener('change', filterProducts);
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