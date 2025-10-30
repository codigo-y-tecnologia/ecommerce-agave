@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detalle del Producto</h1>

    <div class="row">
        <div class="col-md-6">
            {{-- Mostrar imágenes del producto --}}
            @if(count($producto->imagenes) > 0)
                <div class="mb-3">
                    <img src="{{ $producto->imagenes[0] }}" alt="{{ $producto->vNombre }}" 
                         class="img-fluid rounded" style="max-height: 400px; width: 100%; object-fit: cover;">
                </div>
                
                {{-- Miniaturas de imágenes adicionales --}}
                @if(count($producto->imagenes) > 1)
                    <div class="row">
                        @foreach($producto->imagenes as $index => $imagen)
                            @if($index > 0)
                                <div class="col-3 mb-2">
                                    <img src="{{ $imagen }}" alt="{{ $producto->vNombre }} - Imagen {{ $index + 1 }}"
                                         class="img-thumbnail" style="height: 80px; width: 100%; object-fit: cover;">
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            @else
                <div class="text-center py-5 bg-light rounded">
                    <p class="text-muted">No hay imágenes disponibles</p>
                </div>
            @endif
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">{{ $producto->vNombre }}</h2>
                    
                    <p><strong>Código de barras:</strong> {{ $producto->vCodigo_barras }}</p>
                    <p><strong>Precio de compra:</strong> ${{ number_format($producto->dPrecio_compra, 2) }}</p>
                    <p><strong>Precio de venta:</strong> ${{ number_format($producto->dPrecio_venta, 2) }}</p>
                    <p><strong>Stock:</strong> {{ $producto->iStock }}</p>
                    <p><strong>Categoría:</strong> {{ $producto->categoria->vNombre ?? 'N/A' }}</p>
                    <p><strong>Marca:</strong> {{ $producto->marca->vNombre ?? 'N/A' }}</p>
                    
                    @if($producto->tDescripcion_corta)
                        <p><strong>Descripción corta:</strong> {{ $producto->tDescripcion_corta }}</p>
                    @endif
                    
                    @if($producto->tDescripcion_larga)
                        <p><strong>Descripción larga:</strong> {{ $producto->tDescripcion_larga }}</p>
                    @endif
                    
                    <p><strong>Estado:</strong> 
                        <span class="badge {{ $producto->bActivo ? 'bg-success' : 'bg-secondary' }}">
                            {{ $producto->bActivo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </p>
                    
                    <p><strong>Etiquetas:</strong>
                        @foreach ($producto->etiquetas as $etiqueta)
                            <span class="badge bg-info text-dark">{{ $etiqueta->vNombre }}</span>
                        @endforeach
                    </p>
                </div>
            </div>

            <div class="mt-3">
                <a href="{{ route('productos.index') }}" class="btn btn-secondary">Volver a Productos</a>
                <a href="{{ route('productos.edit', $producto) }}" class="btn btn-warning">Editar</a>
            </div>
        </div>
    </div>
</div>
@endsection