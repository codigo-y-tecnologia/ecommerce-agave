@extends('layouts.app')

@section('title', 'Detalles de Categoría')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-eye me-2"></i>Detalles de Categoría</h1>
            <a href="{{ route('categorias.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h4>{{ $categoria->vNombre }}</h4>
                <p><strong>Descripción:</strong> {{ $categoria->tDescripcion ?? 'Sin descripción' }}</p>
                <p><strong>Productos en esta categoría:</strong> {{ $categoria->productos->count() }}</p>

                @if($categoria->productos->count() > 0)
                <div class="mt-3">
                    <h5>Productos:</h5>
                    <ul>
                        @foreach($categoria->productos as $producto)
                            <li>{{ $producto->vNombre }} - ${{ number_format($producto->dPrecio_venta, 2) }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="mt-4">
                    <a href="{{ route('categorias.edit', $categoria) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i> Editar Categoría
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection