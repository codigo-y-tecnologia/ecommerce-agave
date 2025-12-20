<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Ecommerce Agave')</title>
    @vite(['resources/css/styles.css'])
    @stack('styles')

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    

    <style>
        /* Estilos personalizados para la barra */
        .ml-header { background: #0c0c0962; }
        .ml-links { background: #22221917; font-size: 0.95rem; }

        /* Espaciado entre enlaces de la barra inferior */
        .ml-links .container {
            gap: 18px; /* Ajusta a tu gusto: 12px, 16px, 20px */
        }

        .search-box input {
            border-radius: 8px 0 0 8px;
        }
        .search-box button {
            border-radius: 0 8px 8px 0;
        }

        /* Dropdown */
        .user-dropdown:hover .dropdown-menu {
            display: block;
            margin-top: 0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .search-box { width: 100% !important; }
            .ml-links .nav-link { padding: 10px 6px; font-size: 0.85rem; }
        }
    </style>

</head>

<body class="bg-light">

    <!-- BARRA SUPERIOR (LOGO + BUSCADOR + USUARIO) -->
    <header class="ml-header py-2 shadow-sm">
        <div class="container d-flex align-items-center justify-content-between">

            <!-- Logo -->
            <a href="{{ route('home') }}" class="d-flex align-items-center text-dark fw-bold fs-4 text-decoration-none">
                <img src="ruta/a/tu/imagen.jpg"
                     style="width:38px;" class="me-2">
                AgaveShop
            </a>

            <!-- BUSCADOR -->
            @if(!auth()->check() || auth()->user()->eRol === 'cliente')
                <form action="{{ route('busqueda.resultados') }}" method="GET"
                      class="d-flex mx-3 flex-grow-1 search-box" style="max-width: 600px;">
                    <input type="search" name="q" class="form-control"
                           placeholder="Buscar productos..." value="{{ request('q') }}">
                    <button class="btn btn-dark"><i class="bi bi-search"></i></button>
                </form>
            @endif

            {{-- 🌟 Invitado (no autenticado) --}}
            @guest
                <div class="d-flex gap-2">
                    <a href="{{ route('login') }}" class="btn btn-outline-dark btn-sm">Ingresar</a>
                    <a href="{{ route('usuarios.create') }}" class="btn btn-dark btn-sm">Registrarse</a>
                </div>
            @endguest

            @auth
                <div class="dropdown user-dropdown">
                    <a class="d-flex align-items-center text-dark fw-semibold dropdown-toggle text-decoration-none"
                       href="#" data-bs-toggle="dropdown">

                        <i class="bi bi-person-circle fs-4 me-1"></i>
                        {{ Auth::user()->vNombre }}
                    </a>

                    {{-- 👤 Cliente --}}
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        @if(Auth::user()->eRol === 'cliente')
                            <li><a class="dropdown-item" href="{{ route('perfil.index') }}">Mi Perfil</a></li>
                            <li><a class="dropdown-item" href="{{ route('favoritos.index') }}">Mis Favoritos</a></li>
                            <li><a class="dropdown-item" href="{{ route('carrito.index') }}">Mi Carrito</a></li>
                            <li><a class="dropdown-item" href="{{ route('pedidos.index') }}">Mis Pedidos</a></li>
                        @endif

                        {{-- ⚙️ Admin --}}
                        @if(Auth::user()->eRol === 'admin')
                            <li><a class="dropdown-item" href="{{ route('admin.usuarios') }}">Clientes Registrados</a></li>
                            <li><a class="dropdown-item" href="{{ route('cupones.index') }}">Cupones</a></li>
                            <li><a class="dropdown-item" href="{{ route('impuestos.index') }}">Impuestos</a></li>
                            <li><a class="dropdown-item" href="{{ route('productos.index') }}">Productos</a></li>
                            <li><a class="dropdown-item" href="#">Pedidos</a></li>
                            <li><a class="dropdown-item" href="{{ route('ventas.index') }}">Reportes</a></li>
                        @endif

                        {{-- 👑 Superadmin --}}
                        @if(Auth::user()->eRol === 'superadmin')
                            <li><a class="dropdown-item" href="{{ route('superadmin.admins.index') }}">Gestión de Administradores</a></li>
                            <li><a class="dropdown-item" href="#">Monitoreo del sistema</a></li>
                            <li><a class="dropdown-item" href="#">Logs de seguridad</a></li>
                            <li><a class="dropdown-item" href="#">Configuración Global</a></li>
                            <li><a class="dropdown-item" href="#">Gestión de permisos</a></li>
                        @endif

                        <li><hr class="dropdown-divider"></li>

                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="dropdown-item text-danger">Cerrar sesión</button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endauth
        </div>
    </header>

    <!-- BARRA INFERIOR -->
    <nav class="ml-links py-1 shadow-sm">
        <div class="container d-flex flex-wrap">

            @guest
                <a class="nav-link text-dark" href="{{ route('usuarios.create') }}">
                    <i class="bi bi-person-plus"></i> Crear cuenta
                </a>
                <a class="nav-link text-dark" href="{{ route('login') }}">
                    <i class="bi bi-box-arrow-in-right"></i> Ingresar
                </a>
            @endguest

            @auth
                @if(Auth::user()->eRol === 'cliente')
                    <a class="nav-link text-dark" href="{{ route('favoritos.index') }}">
                        ❤️ Mis Favoritos
                    </a>
                    <a class="nav-link text-dark" href="{{ route('carrito.index') }}">
                        🛒 Carrito
                    </a>
                    <a class="nav-link text-dark" href="{{ route('pedidos.index') }}">
                        📦 Mis Pedidos
                    </a>
                    <a class="nav-link text-dark" href="{{ route('perfil.index') }}">
                        👤 Perfil
                    </a>
                @endif
            @endauth

            <a class="nav-link text-dark" href="{{ route('busqueda.resultados') }}">
                🔍 Todos los productos
            </a>
        </div>
    </nav>

    <!-- CONTENIDO -->
    <main class="container my-4">
        @yield('content')
    </main>

    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
