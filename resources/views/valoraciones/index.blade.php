@extends('layouts.app')

@section('title', 'Valoraciones de Productos')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-cubes me-2"></i>Valoraciones de Productos</h1>
    </div>

    @if($productos->count() > 0)
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Producto</th>
                                <th>SKU Base</th>
                                <th>Categoría</th>
                                <th>Variaciones</th>
                                <th>Stock Total</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productos as $producto)
                                <tr>
                                    <td>
                                        <strong>{{ $producto->vNombre }}</strong>
                                        @if($producto->marca)
                                            <br>
                                            <small class="text-muted">Marca: {{ $producto->marca->vNombre }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <code>{{ $producto->vCodigo_barras }}</code>
                                    </td>
                                    <td>
                                        {{ $producto->categoria->vNombre ?? 'Sin categoría' }}
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $producto->variaciones->count() }} variaciones
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $producto->iStock > 10 ? 'bg-success' : ($producto->iStock > 0 ? 'bg-warning' : 'bg-danger') }}">
                                            {{ $producto->iStock }} unidades
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('valoraciones.show', $producto->id_producto) }}" 
                                               class="btn btn-primary" title="Ver valoraciones">
                                                <i class="fas fa-eye"></i> Ver
                                            </a>
                                            <a href="{{ route('valoraciones.create', $producto->id_producto) }}" 
                                               class="btn btn-success" title="Agregar valoración">
                                                <i class="fas fa-plus"></i> Nueva
                                            </a>
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
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="text-center py-5">
                    <i class="fas fa-cubes fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">No hay productos con valoraciones</h4>
                    <p class="text-muted">Los productos con atributos asignados aparecerán aquí</p>
                    <a href="{{ route('productos.index') }}" class="btn btn-primary">
                        <i class="fas fa-wine-bottle me-1"></i> Ir a Productos
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection