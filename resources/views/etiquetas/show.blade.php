@extends('layouts.app')

@section('title', 'Detalles de Etiqueta')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-eye me-2"></i>Detalles de Etiqueta</h1>
            <a href="{{ route('etiquetas.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h4>{{ $etiqueta->vNombre }}</h4>
                <p><strong>Descripción:</strong> {{ $etiqueta->tDescripcion ?? 'Sin descripción' }}</p>
                <p><strong>Productos con esta etiqueta:</strong> {{ $etiqueta->productos->count() }}</p>

                @if($etiqueta->productos->count() > 0)
                <div class="mt-3">
                    <h5>Productos:</h5>
                    <ul>
                        @foreach($etiqueta->productos as $producto)
                            <li>{{ $producto->vNombre }} - ${{ number_format($producto->dPrecio_venta, 2) }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="mt-4">
                    <a href="{{ route('etiquetas.edit', $etiqueta) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i> Editar Etiqueta
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection