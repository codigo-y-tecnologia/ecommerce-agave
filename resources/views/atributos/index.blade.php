<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atributos de Productos - Ecommerce Agave</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 20px;
        }
        .container {
            max-width: 1400px;
        }
        .card-header {
            font-weight: 600;
        }
        .btn-primary {
            background-color: #2E8B57;
            border-color: #2E8B57;
        }
        .btn-primary:hover {
            background-color: #26734A;
            border-color: #26734A;
        }
        h1 {
            color: #333;
            margin-bottom: 25px;
            font-weight: 600;
        }
        .table th {
            background-color: #2E8B57;
            color: white;
        }
        .badge {
            font-size: 0.85em;
            padding: 0.4em 0.7em;
        }
        .search-card {
            border: 1px solid #2E8B57;
            border-left: 5px solid #2E8B57;
        }
        .empty-state {
            padding: 50px 20px;
            text-align: center;
        }
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #2E8B57;">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-wine-bottle me-2"></i>Ecommerce Agave
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-tags me-2"></i>Atributos de Productos</h1>
            <a href="{{ route('atributos.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Nuevo Atributo
            </a>
        </div>

        <!-- SweetAlert2 para mensajes de sesión -->
        @if(session('success'))
            <script>
                Swal.fire({
                    title: "¡Éxito!",
                    text: "{{ session('success') }}",
                    icon: "success",
                    confirmButtonText: "Aceptar"
                });
            </script>
        @endif

        @if(session('error'))
            <script>
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "{{ session('error') }}",
                    footer: '<a href="#">¿Por qué tengo este problema?</a>',
                    confirmButtonText: "Entendido"
                });
            </script>
        @endif

        <!-- BUSCADOR -->
        <div class="card shadow-sm mb-4 search-card">
            <div class="card-body">
                <form method="GET" action="{{ route('atributos.index') }}" id="searchForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="search" class="form-label fw-bold">
                                <i class="fas fa-search me-1"></i> Buscar por ID o Nombre
                            </label>
                            <div class="input-group">
                                <input type="text" 
                                       name="search" 
                                       id="search" 
                                       class="form-control"
                                       value="{{ request('search') }}"
                                       placeholder="Ej: 1 o 'Tamaño'">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-search"></i>
                                </button>
                                @if(request()->has('search'))
                                <a href="{{ route('atributos.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i>
                                </a>
                                @endif
                            </div>
                            <small class="text-muted mt-1 d-block">
                                <i class="fas fa-info-circle me-1"></i>
                                Puedes buscar por ID numérico o por cualquier parte del nombre
                            </small>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="orden" class="form-label fw-bold">
                                <i class="fas fa-sort me-1"></i> Ordenar por
                            </label>
                            <select name="orden" id="orden" class="form-select" onchange="this.form.submit()">
                                <option value="">Seleccionar...</option>
                                <option value="nombre" {{ request('orden') == 'nombre' ? 'selected' : '' }}>Nombre (A-Z)</option>
                                <option value="nombre_desc" {{ request('orden') == 'nombre_desc' ? 'selected' : '' }}>Nombre (Z-A)</option>
                                <option value="id" {{ request('orden') == 'id' ? 'selected' : '' }}>ID (Menor a Mayor)</option>
                                <option value="id_desc" {{ request('orden') == 'id_desc' ? 'selected' : '' }}>ID (Mayor a Menor)</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Muestra filtros aplicados -->
                    @if(request()->has('search') || request()->has('orden'))
                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="fas fa-filter me-1"></i>
                            Filtros aplicados:
                            @if(request('search'))
                            <span class="badge bg-info me-1">Búsqueda: "{{ request('search') }}"</span>
                            @endif
                            @if(request('orden'))
                            @php
                                $ordenTexto = '';
                                switch(request('orden')) {
                                    case 'nombre':
                                        $ordenTexto = 'Nombre (A-Z)';
                                        break;
                                    case 'nombre_desc':
                                        $ordenTexto = 'Nombre (Z-A)';
                                        break;
                                    case 'id':
                                        $ordenTexto = 'ID (Menor a Mayor)';
                                        break;
                                    case 'id_desc':
                                        $ordenTexto = 'ID (Mayor a Menor)';
                                        break;
                                    default:
                                        $ordenTexto = '';
                                        break;
                                }
                            @endphp
                            <span class="badge bg-info">
                                Orden: {{ $ordenTexto }}
                            </span>
                            @endif
                        </small>
                    </div>
                    @endif
                </form>
            </div>
        </div>

        <!-- TABLA DE ATRIBUTOS -->
        <div class="card shadow-sm">
            <div class="card-body">
                @if($atributos->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Slug</th>
                                <th>Valores</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($atributos as $atributo)
                            <tr>
                                <td>
                                    <span class="badge bg-secondary">#{{ $atributo->id_atributo }}</span>
                                </td>
                                <td>
                                    <strong>{{ $atributo->vNombre }}</strong>
                                    @if($atributo->tDescripcion)
                                    <br>
                                    <small class="text-muted">{{ Str::limit($atributo->tDescripcion, 50) }}</small>
                                    @endif
                                </td>
                                <td>
                                    <code>{{ $atributo->vSlug }}</code>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ $atributo->valores_count }} valores
                                    </span>
                                    <a href="{{ route('atributos.valores', $atributo) }}" class="btn btn-sm btn-outline-primary ms-2">
                                        <i class="fas fa-cog"></i> Gestionar
                                    </a>
                                </td>
                                <td>
                                    <span class="badge {{ $atributo->bActivo ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $atributo->bActivo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('atributos.edit', $atributo) }}" class="btn btn-warning" 
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('atributos.show', $atributo) }}" class="btn btn-primary" 
                                           title="Ver detalle">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('atributos.valores', $atributo) }}" class="btn btn-info" 
                                           title="Ver valores">
                                            <i class="fas fa-list"></i>
                                        </a>
                                        <!-- Botón de eliminar con SweetAlert2 -->
                                        <button type="button" class="btn btn-danger btn-eliminar" 
                                                data-id="{{ $atributo->id_atributo }}"
                                                data-nombre="{{ $atributo->vNombre }}"
                                                title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        
                                        <!-- Formulario oculto para eliminación -->
                                        <form id="delete-form-{{ $atributo->id_atributo }}" 
                                              action="{{ route('atributos.destroy', $atributo) }}" 
                                              method="POST" style="display: none;">
                                            @csrf @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="empty-state">
                    @if(request()->has('search'))
                    <i class="fas fa-search"></i>
                    <h4 class="text-muted">No se encontraron resultados</h4>
                    <p class="text-muted">Intenta con otros términos de búsqueda</p>
                    <a href="{{ route('atributos.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> Limpiar búsqueda
                    </a>
                    @else
                    <i class="fas fa-tags"></i>
                    <h4 class="text-muted">No hay atributos registrados</h4>
                    <p class="text-muted">Comienza creando tu primer atributo para mezcales</p>
                    <a href="{{ route('atributos.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Crear Primer Atributo
                    </a>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 para eliminación -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Confirmación de eliminación con SweetAlert2
        document.querySelectorAll('.btn-eliminar').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const nombre = this.getAttribute('data-nombre');
                
                Swal.fire({
                    title: "¿Estás seguro?",
                    html: `Vas a eliminar el atributo: <strong>"${nombre}"</strong><br>
                           <span class="text-danger">¡No podrás revertir esta acción! Todos los valores asociados también serán eliminados.</span>`,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Sí, eliminar",
                    cancelButtonText: "Cancelar"
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Mostrar loader
                        Swal.fire({
                            title: "Eliminando...",
                            text: "Por favor espera",
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        
                        // Enviar formulario
                        document.getElementById(`delete-form-${id}`).submit();
                        
                        // Mostrar mensaje de éxito
                        Swal.fire({
                            title: "¡Eliminado!",
                            text: "El atributo ha sido eliminado correctamente.",
                            icon: "success"
                        });
                    }
                });
            });
        });
        
        // Auto-focus en el campo de búsqueda
        const searchInput = document.getElementById('search');
        if (searchInput && searchInput.value) {
            searchInput.focus();
            searchInput.select();
        }
        
        // Confirmación para limpiar búsqueda
        const clearSearchBtn = document.querySelector('a[href="{{ route("atributos.index") }}"]');
        if (clearSearchBtn && request().has('search')) {
            clearSearchBtn.addEventListener('click', function(e) {
                if (document.getElementById('search').value.trim() !== '') {
                    e.preventDefault();
                    Swal.fire({
                        title: "¿Limpiar búsqueda?",
                        text: "Se eliminarán los filtros aplicados",
                        icon: "question",
                        showCancelButton: true,
                        confirmButtonText: "Sí, limpiar",
                        cancelButtonText: "Cancelar"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "{{ route('atributos.index') }}";
                        }
                    });
                }
            });
        }
    });
    </script>
</body>
</html>