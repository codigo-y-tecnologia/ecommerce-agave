@extends('layouts.app')

@section('title', 'Detalles de Categoría')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h2 class="mb-0"><i class="fas fa-eye me-2"></i>Detalles de Categoría</h2>
                    <a href="{{ route('categorias.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Volver
                    </a>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            @if($categoria->tiene_imagen)
                                <div class="border rounded p-3 bg-light mb-3">
                                    <img src="{{ $categoria->imagen_url }}" 
                                         class="img-fluid rounded" 
                                         style="max-height: 200px; object-fit: contain;"
                                         alt="{{ $categoria->vNombre }}">
                                </div>
                            @else
                                <div class="border rounded p-4 mb-3 text-center bg-light">
                                    <i class="fas fa-camera fa-4x text-muted mb-3"></i>
                                    <p class="text-muted">Sin imagen</p>
                                </div>
                            @endif
                            
                            <h3 class="mb-2">{{ $categoria->vNombre }}</h3>
                            
                            <div class="mb-3">
                                @if($categoria->bActivo)
                                    <span class="badge bg-success p-2">Activo</span>
                                @else
                                    <span class="badge bg-secondary p-2">Inactivo</span>
                                @endif
                            </div>
                            
                            @if($categoria->esRaiz())
                                <span class="badge bg-primary">Categoría Raíz</span>
                            @else
                                <span class="badge bg-warning">Subcategoría</span>
                            @endif
                        </div>

                        <div class="col-md-8">
                            <div class="mb-4">
                                <h5 class="border-bottom pb-2">Información de la Categoría</h5>
                                
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="150">ID:</th>
                                        <td><span class="badge bg-secondary">#{{ $categoria->id_categoria }}</span></td>
                                    </tr>
                                    <tr>
                                        <th>Slug:</th>
                                        <td><code class="bg-light p-2 rounded">{{ $categoria->vSlug }}</code></td>
                                    </tr>
                                    <tr>
                                        <th>Fecha creación:</th>
                                        <td>{{ $categoria->created_at ? date('d/m/Y H:i', strtotime($categoria->created_at)) : 'No disponible' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Última actualización:</th>
                                        <td>{{ $categoria->updated_at ? date('d/m/Y H:i', strtotime($categoria->updated_at)) : 'No disponible' }}</td>
                                    </tr>
                                </table>
                            </div>

                            @if($categoria->tDescripcion)
                            <div class="mb-4">
                                <h5 class="border-bottom pb-2">Descripción</h5>
                                <div class="p-3 bg-light rounded">
                                    {{ $categoria->tDescripcion }}
                                </div>
                            </div>
                            @endif

                            @if($categoria->padre)
                            <div class="mb-4">
                                <h5 class="border-bottom pb-2">Categoría Padre</h5>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-arrow-up text-success me-2"></i>
                                    <a href="{{ route('categorias.show', $categoria->padre) }}" class="text-decoration-none">
                                        @if($categoria->padre->tiene_imagen)
                                            <img src="{{ $categoria->padre->imagen_url }}" 
                                                 style="width: 30px; height: 30px; object-fit: cover; border-radius: 5px; margin-right: 8px;">
                                        @endif
                                        {{ $categoria->padre->vNombre }}
                                    </a>
                                </div>
                            </div>
                            @endif

                            @if($categoria->hijos->count() > 0)
                            <div class="mb-4">
                                <h5 class="border-bottom pb-2">
                                    Subcategorías ({{ $categoria->hijos->count() }})
                                </h5>
                                <div class="list-group">
                                    @foreach($categoria->hijos as $hijo)
                                        <a href="{{ route('categorias.show', $hijo) }}" 
                                           class="list-group-item list-group-item-action d-flex align-items-center">
                                            @if($hijo->tiene_imagen)
                                                <img src="{{ $hijo->imagen_url }}" 
                                                     style="width: 30px; height: 30px; object-fit: cover; border-radius: 5px; margin-right: 10px;">
                                            @else
                                                <div style="width: 30px; height: 30px; background-color: #f8f9fa; border-radius: 5px; margin-right: 10px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-image text-muted small"></i>
                                                </div>
                                            @endif
                                            <div class="flex-grow-1">
                                                {{ $hijo->vNombre }}
                                                <small class="text-muted d-block">{{ $hijo->vSlug }}</small>
                                            </div>
                                            <span class="badge bg-info">{{ $hijo->productos->count() }} productos</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            @if($categoria->productos->count() > 0)
                            <div class="mb-4">
                                <h5 class="border-bottom pb-2">
                                    Productos en esta categoría ({{ $categoria->productos->count() }})
                                </h5>
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover">
                                        <thead>
                                            <tr>
                                                <th>SKU</th>
                                                <th>Nombre</th>
                                                <th>Precio</th>
                                                <th>Stock</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($categoria->productos->take(5) as $producto)
                                            <tr>
                                                <td><code>{{ $producto->vCodigo_barras }}</code></td>
                                                <td>{{ $producto->vNombre }}</td>
                                                <td>${{ number_format($producto->dPrecio_venta, 2) }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $producto->iStock > 0 ? 'success' : 'danger' }}">
                                                        {{ $producto->iStock }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @if($categoria->productos->count() > 5)
                                        <div class="text-center">
                                            <small class="text-muted">... y {{ $categoria->productos->count() - 5 }} productos más</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @endif

                            <div class="mt-4 d-flex gap-2">
                                <a href="{{ route('categorias.edit', $categoria) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-1"></i>Editar
                                </a>
                                <a href="{{ route('categorias.create') }}" class="btn btn-success">
                                    <i class="fas fa-plus-circle me-1"></i>Nueva Categoría
                                </a>
                                <button type="button" class="btn btn-danger" 
                                        onclick="confirmarEliminacion({{ $categoria->id_categoria }}, '{{ $categoria->vNombre }}')">
                                    <i class="fas fa-trash-alt me-1"></i>Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="delete-form" action="" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmarEliminacion(id, nombre) {
        Swal.fire({
            title: '¿Eliminar categoría?',
            html: `
                <div class="text-center">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <p>Estás a punto de eliminar la categoría:</p>
                    <strong class="fs-5">"${nombre}"</strong>
                    <p class="text-danger mt-3">¡Esta acción no se puede deshacer!</p>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                const form = document.getElementById('delete-form');
                form.action = `/categorias/${id}`;
                return new Promise((resolve) => {
                    setTimeout(() => {
                        form.submit();
                        resolve();
                    }, 500);
                });
            }
        });
    }
    
    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: '{{ session('success') }}',
        timer: 3000,
        showConfirmButton: false
    });
    @endif
    
    @if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '{{ session('error') }}'
    });
    @endif
</script>
@endpush
@endsection