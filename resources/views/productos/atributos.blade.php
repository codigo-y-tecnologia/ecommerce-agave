@extends('layouts.app')

@section('title', 'Atributos y Variaciones - ' . $producto->vNombre)
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1><i class="fas fa-wine-bottle me-2"></i>{{ $producto->vNombre }}</h1>
            <p class="text-muted">Gestionar atributos y variaciones del producto</p>
        </div>
        <div>
            <a href="{{ route('productos.edit', $producto) }}" class="btn btn-outline-primary">
                <i class="fas fa-edit me-1"></i> Editar Producto
            </a>
            <a href="{{ route('productos.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Sección de atributos (simplificada) -->
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-tags me-2"></i>Atributos Asignados al Producto</h5>
                </div>
                <div class="card-body">
                    @if($producto->atributosAgrupados && count($producto->atributosAgrupados) > 0)
                        <div class="row">
                            @foreach($producto->atributosAgrupados as $atributo)
                                <div class="col-md-6 mb-3">
                                    <div class="border rounded p-3">
                                        <h6 class="fw-bold mb-2">{{ $atributo['nombre'] }}</h6>
                                        <div>
                                            @foreach($atributo['valores'] as $valor)
                                                <span class="badge bg-secondary me-1 mb-1">
                                                    {{ $valor['valor'] }}
                                                    @if($valor['precio_extra'] > 0)
                                                        <small class="ms-1">(+${{ number_format($valor['precio_extra'], 2) }})</small>
                                                    @endif
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Información:</strong> Para crear variaciones del producto, dirígete a la sección de 
                            <a href="{{ route('variaciones.index') }}" class="alert-link">Variaciones</a> o 
                            <a href="{{ route('variaciones.create', $producto->id_producto) }}" class="alert-link">crea una nueva variación</a>.
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">No hay atributos asignados</h4>
                            <p class="text-muted mb-3">Para crear variaciones, primero asigna atributos al producto</p>
                            <a href="{{ route('productos.asignar-atributos', $producto->id_producto) }}" 
                               class="btn btn-primary">
                                <i class="fas fa-tags me-1"></i> Asignar Atributos
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Sección para variaciones existentes -->
    @if($producto->variaciones->count() > 0)
    <div class="card">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>Variaciones Existentes ({{ $producto->variaciones->count() }})</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Combinación</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($producto->variaciones as $variacion)
                        <tr>
                            <td><code>{{ $variacion->vSKU }}</code></td>
                            <td>
                                <small>{{ $variacion->nombre_combinacion }}</small>
                            </td>
                            <td>
                                ${{ number_format($variacion->dPrecio, 2) }}
                                @if($variacion->tieneDescuentoActivo())
                                <br>
                                <small class="text-success">Descuento: ${{ number_format($variacion->dPrecio_descuento, 2) }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $variacion->iStock > 10 ? 'bg-success' : ($variacion->iStock > 0 ? 'bg-warning' : 'bg-danger') }}">
                                    {{ $variacion->iStock }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $variacion->bActivo ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $variacion->bActivo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('variaciones.edit', ['producto_id' => $producto->id_producto, 'variacion_id' => $variacion->id_variacion]) }}" 
                                       class="btn btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('variaciones.destroy', ['producto_id' => $producto->id_producto, 'variacion_id' => $variacion->id_variacion]) }}" 
                                          method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta variación?')">
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
        </div>
    </div>
    @else
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-cubes me-2"></i>Crear Variaciones</h5>
            </div>
            <div class="card-body text-center py-5">
                <i class="fas fa-cubes fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No hay variaciones creadas</h4>
                <p class="text-muted mb-3">Crea variaciones manualmente desde la sección de variaciones</p>
                <div class="mt-3">
                    <a href="{{ route('variaciones.create', $producto->id_producto) }}" class="btn btn-success me-2">
                        <i class="fas fa-plus me-1"></i> Crear Nueva Variación
                    </a>
                    <a href="{{ route('variaciones.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-list me-1"></i> Ver Todas las Variaciones
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@section('styles')
<style>
.badge {
    font-size: 14px;
    padding: 5px 10px;
}

.card {
    border: 1px solid #dee2e6;
}

.card-header {
    border-bottom: 1px solid rgba(0,0,0,0.125);
}
</style>
@endsection