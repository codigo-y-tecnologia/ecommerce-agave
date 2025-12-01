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
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <!-- Logo izquierda -->
            <a class="navbar-brand" href="/">AgaveShop</a>
            
            <!-- Buscador centro-derecha -->
            <form action="{{ route('reembolsos.index') }}" method="GET" class="d-flex">
                <div class="input-group">
                    <input type="text" 
                           name="id_search" 
                           class="form-control" 
                           value="{{ request('id_search') }}" 
                           placeholder="Buscar por ID Reembolso">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
            
            <!-- Enlaces derecha -->
            <div class="d-flex">
                <a href="#" class="text-white me-3">Iniciar Sesión</a>
                <a href="#" class="text-white">Registrarse</a>
            </div>
        </div>
    </nav>

    <main class="py-4">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>