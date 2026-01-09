@extends('layouts.app')

@section('title', 'Valoraciones - ' . $producto->vNombre)
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1><i class="fas fa-cubes me-2"></i>{{ $producto->vNombre }}</h1>
            <p class="text-muted">Gestionar valoraciones del producto</p>
        </div>
        <div>
            <a href="{{ route('valoraciones.create', $producto->id_producto) }}" class="btn btn-success">
                <i class="fas fa-plus me-1"></i> Nueva Valoración
            </a>
            <a href="{{ route('valoraciones.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información del Producto Base</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>SKU Base:</strong>
                            <br>
                            <code>{{ $producto->vCodigo_barras }}</code>
                        </div>
                        <div class="col-md-3">
                            <strong>Categoría:</strong>
                            <br>
                            {{ $producto->categoria->vNombre ?? 'Sin categoría' }}
                        </div>
                        <div class="col-md-3">
                            <strong>Marca:</strong>
                            <br>
                            {{ $producto->marca->vNombre ?? 'Sin marca' }}
                        </div>
                        <div class="col-md-3">
                            <strong>Precio Base:</strong>
                            <br>
                            ${{ number_format($producto->dPrecio_venta, 2) }}
                        </div>
                    </div>
                    
                    @if($producto->atributosAgrupados)
                        <div class="mt-3">
                            <strong>Atributos Disponibles:</strong>
                            <div class="mt-2">
                                @foreach($producto->atributosAgrupados as $atributo)
                                    <span class="badge bg-info me-2 mb-2">
                                        {{ $atributo['nombre'] }}: 
                                        {{ collect($atributo['valores'])->pluck('valor')->implode(', ') }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-list-alt me-2"></i>Valoraciones ({{ $producto->variaciones->count() }})</h5>
                </div>
                <div class="card-body">
                    @if($producto->variaciones->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Imagen</th>
                                        <th>SKU</th>
                                        <th>Combinación</th>
                                        <th>Código Barras</th>
                                        <th>Precio</th>
                                        <th>Stock</th>
                                        <th>Peso (kg)</th>
                                        <th>Clase Envío</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($producto->variaciones as $variacion)
                                        <tr>
                                            <td>
                                                @if($variacion->vImagen)
                                                    <img src="{{ $variacion->vImagen }}" 
                                                         alt="{{ $variacion->nombre_combinacion }}"
                                                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                                @else
                                                    <div style="width: 50px; height: 50px; background-color: #f8f9fa; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #6c757d;">
                                                        <i class="fas fa-image"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <code>{{ $variacion->vSKU }}</code>
                                            </td>
                                            <td>
                                                <small>{{ $variacion->nombre_combinacion }}</small>
                                            </td>
                                            <td>
                                                <small>{{ $variacion->vCodigo_barras }}</small>
                                            </td>
                                            <td>
                                                <strong>${{ number_format($variacion->dPrecio, 2) }}</strong>
                                                @if($variacion->tieneOferta())
                                                    <br>
                                                    <small class="text-success">
                                                        Oferta: ${{ number_format($variacion->dPrecio_oferta, 2) }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge {{ $variacion->iStock > 10 ? 'bg-success' : ($variacion->iStock > 0 ? 'bg-warning' : 'bg-danger') }}">
                                                    {{ $variacion->iStock }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ $variacion->dPeso ? number_format($variacion->dPeso, 2) . ' kg' : '-' }}
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $variacion->vClase_envio ?: 'Estándar' }}</span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $variacion->bActivo ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $variacion->bActivo ? 'Activo' : 'Inactivo' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('valoraciones.edit', ['producto_id' => $producto->id_producto, 'variacion_id' => $variacion->id_variacion]) }}" 
                                                       class="btn btn-warning" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('valoraciones.destroy', ['producto_id' => $producto->id_producto, 'variacion_id' => $variacion->id_variacion]) }}" 
                                                          method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta valoración?')">
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
                        <div class="text-center py-4">
                            <i class="fas fa-cubes fa-3x text-muted mb-2"></i>
                            <h4 class="text-muted">No hay valoraciones registradas</h4>
                            <p class="text-muted">Crea tu primera valoración para este producto</p>
                            <a href="{{ route('valoraciones.create', $producto->id_producto) }}" class="btn btn-success">
                                <i class="fas fa-plus me-1"></i> Crear Primera Valoración
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection