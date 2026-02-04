<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Atributo - Ecommerce Agave</title>
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
            max-width: 1200px;
        }
        .card-header {
            font-weight: 600;
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }
        h1 {
            color: #333;
            margin-bottom: 25px;
            font-weight: 600;
        }
        .valor-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            transition: all 0.3s;
        }
        .valor-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .color-preview {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: 2px solid #dee2e6;
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
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('atributos.index') }}">
                            <i class="fas fa-arrow-left me-1"></i> Volver a Atributos
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-tag me-2"></i>Atributo: {{ $atributo->vNombre }}</h1>
            <div>
                <a href="{{ route('atributos.edit', $atributo) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-1"></i> Editar
                </a>
                
                <!-- Botón para eliminar con SweetAlert2 -->
                <button type="button" class="btn btn-danger" id="btnEliminarAtributo">
                    <i class="fas fa-trash me-1"></i> Eliminar
                </button>
                
                <form action="{{ route('atributos.destroy', $atributo) }}" method="POST" id="deleteForm" style="display: none;">
                    @csrf 
                    @method('DELETE')
                </form>
                
                <a href="{{ route('atributos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Volver
                </a>
            </div>
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
                    title: "Error",
                    text: "{{ session('error') }}",
                    confirmButtonText: "Entendido"
                });
            </script>
        @endif

        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información General</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th width="30%">ID:</th>
                                <td><span class="badge bg-secondary">#{{ $atributo->id_atributo }}</span></td>
                            </tr>
                            <tr>
                                <th>Nombre:</th>
                                <td><strong>{{ $atributo->vNombre }}</strong></td>
                            </tr>
                            <tr>
                                <th>Slug:</th>
                                <td><code>{{ $atributo->vSlug }}</code></td>
                            </tr>
                            <tr>
                                <th>Descripción:</th>
                                <td>{{ $atributo->tDescripcion ?: 'Sin descripción' }}</td>
                            </tr>
                            <tr>
                                <th>Estado:</th>
                                <td>
                                    <span class="badge {{ $atributo->bActivo ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $atributo->bActivo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Total Valores:</th>
                                <td>
                                    <span class="badge bg-info">
                                        {{ $atributo->valores->count() }} valores
                                    </span>
                                    <a href="{{ route('atributos.valores', $atributo) }}" class="btn btn-sm btn-outline-primary ms-2">
                                        <i class="fas fa-cog"></i> Gestionar
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Valores del Atributo</h5>
                            <a href="{{ route('atributos.valores', $atributo) }}" class="btn btn-light btn-sm">
                                <i class="fas fa-plus me-1"></i> Agregar
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($atributo->valores->count() > 0)
                        <div class="row">
                            @foreach($atributo->valores as $valor)
                            <div class="col-12 mb-3">
                                <div class="valor-card">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="d-flex align-items-center">
                                            @if($valor->vHexColor)
                                            <div class="color-preview me-3" style="background-color: {{ $valor->vHexColor }};"></div>
                                            @endif
                                            <div>
                                                <h6 class="mb-1">{{ $valor->vValor }}</h6>
                                                <div class="small text-muted">
                                                    <span class="me-3">Slug: <code>{{ $valor->vSlug }}</code></span>
                                                    <span class="me-3">Orden: {{ $valor->iOrden }}</span>
                                                    <span>Estado: 
                                                        <span class="badge {{ $valor->bActivo ? 'bg-success' : 'bg-secondary' }}">
                                                            {{ $valor->bActivo ? 'Activo' : 'Inactivo' }}
                                                        </span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="badge bg-light text-dark">
                                                <i class="fas fa-coins me-1"></i>${{ number_format($valor->dPrecio_extra, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    @if($valor->vImagenUrl)
                                    <div class="mt-2">
                                        <img src="{{ $valor->vImagenUrl }}" 
                                             alt="{{ $valor->vValor }}" 
                                             style="width: 100%; max-height: 150px; object-fit: cover; border-radius: 4px;">
                                    </div>
                                    @endif
                                    
                                    @if($valor->iStock !== null)
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-box me-1"></i>Stock: {{ $valor->iStock }} unidades
                                        </small>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-4">
                            <i class="fas fa-tags fa-2x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No hay valores registrados para este atributo</p>
                            <a href="{{ route('atributos.valores', $atributo) }}" class="btn btn-primary mt-3">
                                <i class="fas fa-plus me-1"></i> Agregar Primer Valor
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 para confirmación de eliminación -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Botón de eliminar atributo
        document.getElementById('btnEliminarAtributo').addEventListener('click', function() {
            const atributoNombre = "{{ $atributo->vNombre }}";
            const valoresCount = {{ $atributo->valores->count() }};
            
            let mensaje = `¿Estás seguro de que quieres eliminar el atributo "<strong>${atributoNombre}</strong>"?`;
            
            if (valoresCount > 0) {
                mensaje += `<br><span class="text-danger">¡Se eliminarán también los ${valoresCount} valores asociados!</span>`;
            }
            
            mensaje += `<br><br>Esta acción no se puede deshacer.`;
            
            Swal.fire({
                title: "¿Estás seguro?",
                html: mensaje,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar",
                width: 600,
                padding: "3em",
                backdrop: `
                    rgba(0,0,123,0.4)
                    url("https://sweetalert2.github.io/images/nyan-cat.gif")
                    left top
                    no-repeat
                `
            }).then((result) => {
                if (result.isConfirmed) {
                    // Mostrar loader
                    Swal.fire({
                        title: "Eliminando...",
                        text: "Por favor espera mientras eliminamos el atributo y sus valores",
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Enviar formulario de eliminación después de 1 segundo
                    setTimeout(() => {
                        document.getElementById('deleteForm').submit();
                        
                        // Mostrar mensaje de éxito después de enviar
                        Swal.fire({
                            title: "¡Eliminado!",
                            text: "El atributo y sus valores han sido eliminados correctamente.",
                            icon: "success",
                            timer: 3000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = "{{ route('atributos.index') }}";
                        });
                    }, 1000);
                }
            });
        });
    });
    </script>
</body>
</html>