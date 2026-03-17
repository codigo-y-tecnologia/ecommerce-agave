@extends('admin.productos.administrar-productos')

@section('title', 'Categorías')
@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Categorías</h2>
                <a href="{{ route('categorias.create') }}" class="btn btn-primary">+ Nueva</a>
            </div>
            
            <!-- Formulario de búsqueda -->
            <form method="GET" action="{{ route('categorias.index') }}" class="mt-3" id="searchForm">
                <div class="row">
                    <div class="col-md-8">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Buscar por nombre, ID, slug o descripción..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Buscar</button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('categorias.index') }}" class="btn btn-secondary w-100">Limpiar</a>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if(request('search') && request('search') != '')
                <div class="alert alert-info mb-3">
                    Resultados para: "{{ request('search') }}"
                </div>
            @endif

            @if($categorias->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Slug</th>
                            <th>Productos</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            function mostrarCategoriasConFlechas($categorias, $nivel = 0) {
                                foreach($categorias as $categoria) {
                                    if($nivel == 0) {
                                        $icono = '🏠';
                                    } elseif($nivel == 1) {
                                        $icono = '↳';
                                    } elseif($nivel == 2) {
                                        $icono = '↳ ↳';
                                    } elseif($nivel >= 3) {
                                        $icono = str_repeat('↳ ', $nivel);
                                    }
                                    
                                    $margen = $nivel * 20;
                        @endphp
                        
                        <tr>
                            <td>#{{ $categoria->id_categoria }}</td>
                            <td>
                                @if($categoria->tiene_imagen)
                                    <img src="{{ $categoria->imagen_url }}" 
                                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;"
                                         alt="{{ $categoria->vNombre }}">
                                @else
                                    <div style="width: 50px; height: 50px; background-color: #f8f9fa; 
                                                display: flex; align-items: center; justify-content: center; 
                                                border-radius: 5px;">
                                        <span class="text-muted">—</span>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div style="margin-left: {{ $margen }}px;">
                                    <strong>{{ $icono }} {{ $categoria->vNombre }}</strong>
                                </div>
                            </td>
                            <td>
                                <code class="text-primary">{{ $categoria->vSlug }}</code>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $categoria->productos->count() }}</span>
                            </td>
                            <td>
                                @if($categoria->bActivo)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-secondary">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('categorias.show', $categoria) }}" class="btn btn-info">
                                        Ver
                                    </a>
                                    <a href="{{ route('categorias.edit', $categoria) }}" class="btn btn-warning">
                                        Editar
                                    </a>
                                    <form action="{{ route('categorias.destroy', $categoria) }}" method="POST" 
                                          style="display: inline;" class="delete-form" data-categoria="{{ $categoria->vNombre }}">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger delete-btn">
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        
                        @php
                                    if($categoria->hijos && $categoria->hijos->count() > 0) {
                                        mostrarCategoriasConFlechas($categoria->hijos, $nivel + 1);
                                    }
                                }
                            }
                            
                            $categoriasRaiz = $categorias->where('id_categoria_padre', null);
                            
                            if(request('search')) {
                                foreach($categorias as $categoria) {
                                    $nivelActual = 0;
                                    $categoriaActual = $categoria;
                                    
                                    while($categoriaActual->padre) {
                                        $nivelActual++;
                                        $categoriaActual = $categoriaActual->padre;
                                    }
                                    
                                    if($nivelActual == 0) {
                                        $icono = '🏠';
                                    } elseif($nivelActual == 1) {
                                        $icono = '↳';
                                    } elseif($nivelActual == 2) {
                                        $icono = '↳ ↳';
                                    } elseif($nivelActual >= 3) {
                                        $icono = str_repeat('↳ ', $nivelActual);
                                    }
                                    
                                    $margen = $nivelActual * 20;
                        @endphp
                        
                        <tr>
                            <td>#{{ $categoria->id_categoria }}</td>
                            <td>
                                @if($categoria->tiene_imagen)
                                    <img src="{{ $categoria->imagen_url }}" 
                                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;"
                                         alt="{{ $categoria->vNombre }}">
                                @else
                                    <div style="width: 50px; height: 50px; background-color: #f8f9fa; 
                                                display: flex; align-items: center; justify-content: center; 
                                                border-radius: 5px;">
                                        <span class="text-muted">—</span>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div style="margin-left: {{ $margen }}px;">
                                    <strong>{{ $icono }} {{ $categoria->vNombre }}</strong>
                                </div>
                            </td>
                            <td>
                                <code class="text-primary">{{ $categoria->vSlug }}</code>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $categoria->productos->count() }}</span>
                            </td>
                            <td>
                                @if($categoria->bActivo)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-secondary">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('categorias.show', $categoria) }}" class="btn btn-info">
                                        Ver
                                    </a>
                                    <a href="{{ route('categorias.edit', $categoria) }}" class="btn btn-warning">
                                        Editar
                                    </a>
                                </div>
                            </td>
                        </tr>
                        
                        @php
                                }
                            } else {
                                mostrarCategoriasConFlechas($categoriasRaiz);
                            }
                        @endphp
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3 p-3 bg-light rounded">
                <strong>Total:</strong> {{ $categorias->count() }} categorías
            </div>
            
            @else
            <div class="text-center py-5">
                @if(request('search'))
                    <h4 class="text-muted">No se encontraron resultados</h4>
                    <p class="text-muted">No hay categorías que coincidan con "{{ request('search') }}"</p>
                    <a href="{{ route('categorias.index') }}" class="btn btn-primary">
                        Ver todas las categorías
                    </a>
                @else
                    <h4 class="text-muted">No hay categorías registradas</h4>
                    <p class="text-muted">Comienza agregando tu primera categoría</p>
                    <a href="{{ route('categorias.create') }}" class="btn btn-primary">
                        + Crear Primera Categoría
                    </a>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

<script>
    // Función para mostrar alerta de confirmación al eliminar
    document.addEventListener('DOMContentLoaded', function() {
        // Configurar todos los botones de eliminar
        const deleteButtons = document.querySelectorAll('.delete-btn');
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Obtener el formulario padre
                const form = this.closest('.delete-form');
                const categoriaNombre = form.getAttribute('data-categoria');
                
                // Mostrar SweetAlert de confirmación
                Swal.fire({
                    title: "¿Estás seguro?",
                    text: `Vas a eliminar la categoría "${categoriaNombre}". ¡No podrás revertir esto!`,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Sí, eliminar",
                    cancelButtonText: "Cancelar"
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Mostrar alerta de eliminación en progreso
                        Swal.fire({
                            title: "Eliminando...",
                            text: "Por favor espera",
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        
                        // Si confirma, enviar el formulario
                        setTimeout(() => {
                            form.submit();
                        }, 500);
                    }
                });
            });
        });
        
        // Mostrar alerta de éxito después de eliminar
        @if(session('success') && strpos(session('success'), 'eliminada') !== false)
        Swal.fire({
            title: "¡Eliminado!",
            text: "{{ session('success') }}",
            icon: "success",
            timer: 3000,
            showConfirmButton: false
        });
        @endif
        
        // Mostrar alerta de error después de intentar eliminar
        @if(session('error'))
        Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "{{ session('error') }}",
            footer: 'Por favor, verifica que no tenga productos o subcategorías asociadas'
        });
        @endif
    });
</script>
@endsection
