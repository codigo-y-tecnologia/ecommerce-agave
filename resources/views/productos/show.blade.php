@extends('layouts.app')

@section('title', 'Detalles del Producto')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-eye me-2"></i>Detalles del Producto</h1>
            <a href="{{ route('productos.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h4>Información Básica</h4>
                        <p><strong>Código Barras:</strong> {{ $producto->vCodigo_barras }}</p>
                        <p><strong>Nombre:</strong> {{ $producto->vNombre }}</p>
                        <p><strong>Precio Venta:</strong> ${{ number_format($producto->dPrecio_venta, 2) }}</p>
                        <p><strong>Stock:</strong> {{ $producto->iStock }} unidades</p>
                    </div>
                    <div class="col-md-6">
                        <h4>Clasificación</h4>
                        <p><strong>Marca:</strong> {{ $producto->marca->vNombre ?? 'N/A' }}</p>
                        <p><strong>Categoría:</strong> {{ $producto->categoria->vNombre ?? 'N/A' }}</p>
                        <p><strong>Estado:</strong> 
                            <span class="badge {{ $producto->bActivo ? 'bg-success' : 'bg-secondary' }}">
                                {{ $producto->bActivo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </p>
                    </div>
                </div>

                <div class="mt-3">
                    <h4>Descripción Corta</h4>
                    <p>{{ $producto->tDescripcion_corta ?? 'Sin descripción corta' }}</p>
                </div>

                <div class="mt-3">
                    <h4>Descripción Larga</h4>
                    <p>{{ $producto->tDescripcion_larga ?? 'Sin descripción larga' }}</p>
                </div>

                @if($producto->etiquetas->count() > 0)
                <div class="mt-3">
                    <h4>Etiquetas</h4>
                    @foreach($producto->etiquetas as $etiqueta)
                        <span class="badge bg-primary me-1">{{ $etiqueta->vNombre }}</span>
                    @endforeach
                </div>
                @endif

                <div class="mt-4">
                    <a href="{{ route('productos.edit', $producto) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i> Editar Producto
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection