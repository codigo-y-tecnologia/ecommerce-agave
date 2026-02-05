<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ecommerce Agave - @yield('title')</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .navbar-brand { font-weight: bold; color: #2E8B57 !important; }
        .bg-primary { background-color: #2E8B57 !important; }
        .btn-primary { background-color: #2E8B57; border-color: #2E8B57; }
        .btn-primary:hover { background-color: #26734A; border-color: #26734A; }
        .sidebar { min-height: 100vh; background-color: #f8f9fa; }
        .sidebar .nav-link { color: #333; padding: 10px 15px; }
        .sidebar .nav-link:hover { background-color: #e9ecef; }
        .sidebar .nav-link.active { background-color: #2E8B57; color: white; }
        .table th { background-color: #2E8B57; color: white; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <i class="fas fa-wine-bottle"></i> 
            <a style="color: black; text-shadow: -1px -1px 0 white, 1px -1px 0 white, -1px 1px 0 white, 1px 1px 0 white;">Ecommerce Agave</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/') }}">Inicio</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar d-md-block">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('productos*') && !request()->is('productos/valoraciones') ? 'active' : '' }}" 
                               href="{{ route('productos.index') }}">
                                <i class="fas fa-wine-bottle"></i> Productos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('categorias*') ? 'active' : '' }}" 
                               href="{{ route('categorias.index') }}">
                                <i class="fas fa-tags"></i> Categorías
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('marcas*') ? 'active' : '' }}" 
                               href="{{ route('marcas.index') }}">
                                <i class="fas fa-industry"></i> Marcas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('etiquetas*') ? 'active' : '' }}" 
                               href="{{ route('etiquetas.index') }}">
                                <i class="fas fa-tag"></i> Etiquetas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('atributos*') ? 'active' : '' }}" 
                               href="{{ route('atributos.index') }}">
                                <i class="fas fa-list-alt"></i> Atributos
                            </a>
                        </li>
                        <!-- NUEVO ENLACE PARA VALORACIONES -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('valoraciones*') || request()->is('productos/valoraciones') ? 'active' : '' }}" 
                               href="{{ route('valoraciones.index') }}">
                                <i class="fas fa-cubes"></i> Valoraciones
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <!-- Alert Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle"></i> Por favor corrige los errores del formulario
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Page Content -->
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Scripts -->
    <script>
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Confirmación para eliminar
        function confirmDelete() {
            return confirm('¿Estás seguro de que deseas eliminar este registro? Esta acción no se puede deshacer.');
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')
</body>
</html>