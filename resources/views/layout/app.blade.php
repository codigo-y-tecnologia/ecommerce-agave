<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reembolsos - AgaveShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- NAVBAR CON BUSCADOR -->
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <div class="d-flex align-items-center w-100">
                <!-- LOGO -->
                <a class="navbar-brand me-4" href="/">
                    <i class="fas fa-store me-2"></i>AgaveShop
                </a>

                <!-- BUSCADOR -->
                <form action="{{ route('reembolsos.index') }}" method="GET" class="flex-grow-1 me-4">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" 
                               value="{{ request('search') }}" 
                               placeholder="Buscar por ID, venta o motivo...">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>

                <!-- ENLACES -->
                <div class="d-flex">
                    <a href="{{ route('reembolsos.index') }}" class="btn btn-sm btn-outline-light me-2">
                        <i class="fas fa-money-bill-wave me-1"></i> Reembolsos
                    </a>
                    <a href="#" class="btn btn-sm btn-outline-light me-2">
                        <i class="fas fa-sign-in-alt me-1"></i> Iniciar Sesión
                    </a>
                    <a href="#" class="btn btn-sm btn-light">
                        <i class="fas fa-user-plus me-1"></i> Registrarse
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="py-4">
        <div class="container">
            @yield('content')
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>