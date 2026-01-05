<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de Ventas - AgaveShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <!-- Logo izquierda -->
            <a class="navbar-brand fw-bold" href="/">AgaveShop</a>
            
            <!-- Botón para móviles -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Contenido del navbar -->
            <div class="collapse navbar-collapse" id="navbarContent">
                <!-- Menú de navegación -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('detalle_venta.index') }}">
                           Detalles de Ventas
                        </a>
                    </li>
                </ul>
                
                <!-- Buscador integrado -->
                <form action="{{ route('detalle_venta.index') }}" method="GET" class="d-flex mx-2" style="min-width: 300px; max-width: 400px;">
                    <div class="input-group shadow-sm">
                        <input type="text" 
                               name="search" 
                               class="form-control" 
                               value="{{ request('search') }}" 
                               placeholder="Buscar"
                               aria-label="Buscar detalles">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                        @if(request('search'))
                            <a href="{{ route('detalle_venta.index') }}" class="btn btn-outline-light">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </nav>

    <main class="py-4">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>