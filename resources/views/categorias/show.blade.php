@extends('layouts.app')

@section('title', 'Detalles de Categoría')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2>Detalles de Categoría</h2>
                    <a href="{{ route('categorias.index') }}" class="btn btn-secondary">← Volver</a>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            @if($categoria->tiene_imagen)
                                <img src="{{ $categoria->imagen_url }}" 
                                     class="img-fluid rounded mb-3" 
                                     style="max-height: 200px; object-fit: cover;"
                                     alt="{{ $categoria->vNombre }}">
                            @else
                                <div class="border rounded p-4 mb-3 text-muted text-center">
                                    <div style="font-size: 3rem; margin-bottom: 15px;">📷</div>
                                    <p>Sin imagen</p>
                                </div>
                            @endif
                            
                            <h3>{{ $categoria->vNombre }}</h3>
                            
                            <div class="mb-3">
                                @if($categoria->bActivo)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-secondary">Inactivo</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-8">
                            <!-- Mostrar Slug -->
                            <div class="mb-3">
                                <strong>Slug (URL):</strong>
                                <div class="mt-1">
                                    <code class="bg-light p-2 rounded d-block">
                                        {{ $categoria->vSlug }}
                                    </code>
                                    <small class="text-muted">URL amigable para la categoría</small>
                                </div>
                            </div>

                            @if($categoria->tDescripcion)
                            <div class="mb-3">
                                <strong>Descripción:</strong>
                                <p>{{ $categoria->tDescripcion }}</p>
                            </div>
                            @endif

                            <div class="mb-3">
                                <strong>ID:</strong> {{ $categoria->id_categoria }}
                            </div>

                            @if($categoria->padre)
                            <div class="mb-3">
                                <strong>Categoría Padre:</strong>
                                <a href="{{ route('categorias.show', $categoria->padre) }}">
                                    {{ $categoria->padre->vNombre }}
                                </a>
                            </div>
                            @endif

                            @if($categoria->hijos->count() > 0)
                            <div class="mb-3">
                                <strong>Subcategorías ({{ $categoria->hijos->count() }}):</strong>
                                <ul class="mt-2">
                                    @foreach($categoria->hijos as $hijo)
                                        <li>
                                            <a href="{{ route('categorias.show', $hijo) }}">
                                                {{ $hijo->vNombre }}
                                            </a>
                                            <small class="text-muted">({{ $hijo->vSlug }})</small>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif

                            @if($categoria->productos->count() > 0)
                            <div class="mb-3">
                                <strong>Productos ({{ $categoria->productos->count() }}):</strong>
                                <div class="mt-2">
                                    @foreach($categoria->productos as $producto)
                                        <div class="border p-2 mb-1 rounded">
                                            <strong>{{ $producto->vNombre }}</strong>
                                            <br>
                                            Precio: ${{ number_format($producto->dPrecio_venta, 2) }}
                                            <span class="badge bg-secondary">{{ $producto->iStock }} unidades</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <div class="mt-4">
                                <a href="{{ route('categorias.edit', $categoria) }}" class="btn btn-warning">
                                    ✏️ Editar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection