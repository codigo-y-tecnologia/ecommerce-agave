<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ecommerce Agave - @yield('title', 'Tienda Online')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }

        /* Header */
        .public-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .public-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        .public-header a {
            color: white;
            text-decoration: none;
        }

        /* Navbar público */
        .public-nav {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 15px 0;
        }

        .public-nav .nav-links {
            display: flex;
            justify-content: center;
            gap: 30px;
            list-style: none;
            margin: 0;
            padding: 0;
            flex-wrap: wrap;
        }

        .public-nav .nav-links a {
            color: #555;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
            padding: 5px 0;
        }

        .public-nav .nav-links a:hover {
            color: #667eea;
        }

        .public-nav .nav-links a.active {
            color: #764ba2;
            font-weight: 600;
            border-bottom: 2px solid #764ba2;
        }

        /* Barra de búsqueda */
        .search-bar {
            max-width: 600px;
            margin: 20px auto;
            padding: 0 20px;
        }

        .search-bar form {
            display: flex;
            gap: 10px;
        }

        .search-bar input {
            flex: 1;
            padding: 12px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 25px;
            font-size: 16px;
            outline: none;
            transition: border-color 0.3s ease;
        }

        .search-bar input:focus {
            border-color: #667eea;
        }

        .search-bar button {
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .search-bar button:hover {
            transform: translateY(-2px);
        }

        /* Main content */
        .public-main {
            min-height: calc(100vh - 200px);
            padding: 30px 0;
        }

        /* Footer */
        .public-footer {
            background-color: #333;
            color: white;
            padding: 40px 0 20px;
            margin-top: 50px;
        }

        .public-footer a {
            color: #ccc;
            text-decoration: none;
        }

        .public-footer a:hover {
            color: white;
        }

        /* Notificaciones flotantes */
        .notificacion-flotante {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            z-index: 9999;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            animation: slideIn 0.3s ease;
        }

        .notificacion-flotante.success {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .notificacion-flotante.error {
            background: linear-gradient(135deg, #ef4444, #dc2626);
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        /* Corazón de favoritos */
        .corazon-favorito {
            background: white;
            border: 1px solid #ddd;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 20px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .corazon-favorito:hover {
            transform: scale(1.1);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .corazon-favorito.activo {
            background: #ff4757;
            color: white;
            border-color: #ff4757;
        }

        .corazon-favorito.inactivo {
            color: rgba(0, 0, 0, 0.25);
        }

        .corazon-favorito.loading {
            opacity: 0.5;
            pointer-events: none;
            position: relative;
        }

        .corazon-favorito.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #ff4757;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Badge de invitado */
        .badge-invitado {
            background: #ff6b6b;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            display: inline-block;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .public-nav .nav-links {
                flex-direction: column;
                align-items: center;
                gap: 15px;
            }
            
            .search-bar form {
                flex-direction: column;
            }
            
            .search-bar button {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .public-header .d-flex {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Header -->
    <header class="public-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 15px;">
                <h1>
                    <a href="{{ route('inicio') }}">
                        <i class="fas fa-wine-bottle me-2"></i>Ecommerce Agave
                    </a>
                </h1>
                <div>
                    @auth
                        <span class="me-3">
                            <i class="fas fa-user"></i> {{ Auth::user()->vNombre }}
                        </span>
                        <a href="{{ route('favoritos.index') }}" class="text-white me-3">
                            <i class="fas fa-heart"></i> Mis Favoritos
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-link text-white" style="text-decoration: none;">
                                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                            </button>
                        </form>
                    @else
                        <a href="{{ route('favoritos.invitado.index') }}" class="text-white me-3" style="background: rgba(255,255,255,0.2); padding: 8px 15px; border-radius: 25px;">
                            <i class="fas fa-user"></i> Invitado
                        </a>
                        <a href="{{ route('login') }}" class="text-white me-3">
                            <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                        </a>
                        <a href="{{ route('usuarios.create') }}" class="text-white">
                            <i class="fas fa-user-plus"></i> Registrarse
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Barra de búsqueda -->
    <div class="search-bar">
        <form action="{{ route('busqueda.resultados') }}" method="GET">
            <input type="text" 
                   name="q" 
                   placeholder="Buscar productos (agave, mezcal, espadín...)" 
                   value="{{ request('q') }}"
                   autocomplete="off">
            <button type="submit">
                <i class="fas fa-search"></i> Buscar
            </button>
        </form>
    </div>

    <!-- Navbar público -->
    <nav class="public-nav">
        <div class="container">
            <ul class="nav-links">
                <li>
                    <a href="{{ route('inicio') }}" class="{{ request()->routeIs('inicio') ? 'active' : '' }}">
                        <i class="fas fa-home"></i> Inicio
                    </a>
                </li>
                <li>
                    <a href="{{ route('busqueda.resultados') }}" class="{{ request()->routeIs('busqueda.resultados') ? 'active' : '' }}">
                        <i class="fas fa-store"></i> Todos los Productos
                    </a>
                </li>
                <li>
                    @auth
                        <a href="{{ route('favoritos.index') }}" class="{{ request()->routeIs('favoritos.index') ? 'active' : '' }}">
                            <i class="fas fa-heart" style="color: #ff4757;"></i> Mis Favoritos
                        </a>
                    @else
                        <a href="{{ route('favoritos.invitado.index') }}" class="{{ request()->routeIs('favoritos.invitado.index') ? 'active' : '' }}">
                            <i class="fas fa-heart" style="color: #ff4757;"></i> Mis Favoritos (Invitado)
                        </a>
                    @endauth
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="public-main">
        <div class="container">
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

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle"></i> {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Page Content -->
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="public-footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5><i class="fas fa-wine-bottle me-2"></i>Ecommerce Agave</h5>
                    <p>Tu tienda especializada en productos de agave, mezcales y tequilas.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Enlaces Rápidos</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('inicio') }}">Inicio</a></li>
                        <li><a href="{{ route('busqueda.resultados') }}">Productos</a></li>
                        <li><a href="{{ route('favoritos.index') }}">Lista de Deseos</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Contacto</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-envelope me-2"></i> info@ecommerceagave.com</li>
                        <li><i class="fas fa-phone me-2"></i> (123) 456-7890</li>
                    </ul>
                </div>
            </div>
            <hr class="bg-light">
            <p class="text-center mb-0">&copy; {{ date('Y') }} Ecommerce Agave. Todos los derechos reservados.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Script para migrar favoritos al iniciar sesión -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @auth
                // Verificar si hay favoritos temporales para migrar
                fetch('{{ route("favoritos.invitado.migrar") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.migrados > 0) {
                        // Mostrar notificación de migración exitosa
                        const toast = document.createElement('div');
                        toast.className = 'notificacion-flotante success';
                        toast.innerHTML = `<i class="fas fa-check-circle me-2"></i> Se migraron ${data.migrados} favoritos a tu cuenta`;
                        document.body.appendChild(toast);
                        
                        setTimeout(() => {
                            toast.style.animation = 'slideOut 0.3s ease';
                            setTimeout(() => toast.remove(), 300);
                        }, 3000);
                        
                        // Limpiar localStorage
                        localStorage.removeItem('ultima_accion_favorito');
                    }
                })
                .catch(error => console.error('Error migrando favoritos:', error));
            @endauth
        });
    </script>
    
    @stack('scripts')
</body>
</html>