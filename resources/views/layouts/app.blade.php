<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Ecommerce Agave')</title>
    @vite(['resources/css/styles.css'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

</head>

<body class="bg-light">
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
            <div class="container">
                <a class="navbar-brand fw-bold" href="{{ route('home') }}">AgaveShop</a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                        {{-- 🌟 Invitado (no autenticado) --}}
                        @guest
                            <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Iniciar Sesión</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('usuarios.create') }}">Registrarse</a></li>
                        @endguest

                        {{-- 👤 Cliente --}}
                        @role('cliente')
                                <li class="nav-item"><a class="nav-link" href="#">🛒 Mi Carrito</a></li>
                                <li class="nav-item"><a class="nav-link" href="#">🧾 Checkout</a></li>
                                <li class="nav-item"><a class="nav-link" href="#">📦 Mis Pedidos</a></li>
                                @can('ver_perfil')
                                    <li class="nav-item"><a class="nav-link" href="{{ route('perfil.index') }}">👤 Mi Perfil</a></li>
                                @endcan
                        @endrole

                            {{-- ⚙️ Admin --}}
                            @role('admin')
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="adminMenu" role="button" data-bs-toggle="dropdown">
                                        👨‍💼 Administración
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('admin.usuarios') }}">Clientes registrados</a></li>
                                        <li><a class="dropdown-item" href="#">Cupones</a></li>
                                        <li><a class="dropdown-item" href="#">Impuestos</a></li>
                                        <li><a class="dropdown-item" href="#">Productos</a></li>
                                        <li><a class="dropdown-item" href="#">Pedidos</a></li>
                                        <li><a class="dropdown-item" href="#">Reportes</a></li>
                                    </ul>
                                </li>
                            @endrole

                            {{-- 👑 Superadmin --}}
                            @role('superadmin')
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="superadminMenu" role="button" data-bs-toggle="dropdown">
                                        👑 Panel Superadmin
                                    </a>
                                    <ul class="dropdown-menu">
                                        @can('gestionar_administradores')
                                        <li><a class="dropdown-item" href="{{ route('superadmin.admins.index') }}">Gestión de administradores</a></li>
                                        @endcan
                                        <li><a class="dropdown-item" href="#">Monitoreo del sistema</a></li>
                                        <li><a class="dropdown-item" href="#">Logs de seguridad</a></li>
                                        <li><a class="dropdown-item" href="#">Configuración global</a></li>
                                        <li><a class="dropdown-item" href="#">Gestión de permisos</a></li>
                                    </ul>
                                </li>
                            @endrole
                    </ul>

                    {{-- 🟢 Usuario autenticado (lado derecho) --}}
                    @auth
                        <div class="d-flex align-items-center">
                            <span class="text-white me-3">Hola, {{ Auth::user()->vNombre }} <small>({{ Auth::user()->getRoleNames()->first() }})</small></span>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="btn btn-outline-light btn-sm">Cerrar sesión</button>
                            </form>
                        </div>
                    @endauth
                </div>
            </div>
        </nav>
    </header>

    <main class="container my-5">
        @yield('content')
    </main>

    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
