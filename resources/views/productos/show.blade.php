@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detalle del Producto</h1>

    <div class="card">
        <div class="card-body">
            <p><strong>Código de barras:</strong> {{ $producto->vCodigo_barras }}</p>
            <p><strong>Nombre:</strong> {{ $producto->vNombre }}</p>
            <p><strong>Precio de compra:</strong> {{ $producto->dPrecio_compra }}</p>
            <p><strong>Precio de venta:</strong> {{ $producto->dPrecio_venta }}</p>
            <p><strong>Stock:</strong> {{ $producto->iStock }}</p>
            <p><strong>Categoría:</strong> {{ $producto->categoria->vNombre ?? 'N/A' }}</p>
            <p><strong>Marca:</strong> {{ $producto->marca->vNombre ?? 'N/A' }}</p>
            <p><strong>Etiquetas:</strong>
                @foreach ($producto->etiquetas as $etiqueta)
                    <span class="badge bg-info text-dark">{{ $etiqueta->vNombre }}</span>
                @endforeach
            </p>
        </div>
    </div>

    <a href="{{ route('productos.index') }}" class="btn btn-secondary mt-3">Volver</a>
</div>
@endsection