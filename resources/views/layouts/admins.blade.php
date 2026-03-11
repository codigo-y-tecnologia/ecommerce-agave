<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Ecommerce Agave')</title>
    @vite(['resources/css/styles.css'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-light">
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
            <div class="container">
                <a class="navbar-brand fw-bold" href="{{ route('home') }}">{{ config_sistema('nombre_tienda') }}</a>

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

                        {{-- ⚙️ Admin --}}
                        @role('admin')
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="adminMenu" role="button" data-bs-toggle="dropdown">
                                        👨‍💼 Administración
                                    </a>
                                    <ul class="dropdown-menu">
                                        @can('mi_perfil_admin')
                                            <li><a class="dropdown-item" href="{{ route('admin.perfil.index') }}">Mi Perfil</a></li>
                                        @endcan
                                    @can('ver_clientes')
                                        <li><a class="dropdown-item" href="{{ route('admin.usuarios') }}">Clientes registrados</a></li>
                                    @endcan
                                    @can('gestionar_cupones')
                                        <li><a class="dropdown-item" href="{{ route('cupones.index') }}">Cupones</a></li>
                                    @endcan
                                    @can('gestionar_impuestos')
                                        <li><a class="dropdown-item" href="{{ route('impuestos.index') }}">Impuestos</a></li>
                                    @endcan    
                                    @can('gestionar_productos')
                                        <li><a class="dropdown-item" href="{{ route('productos.index') }}">Productos</a></li>
                                    @endcan
                                    @can('ver_pedidos')    
                                        <li><a class="dropdown-item" href="{{ route('admin.pedidos.index') }}">Pedidos</a></li>
                                    @endcan    
                                        @can('ver_reportes')
                                            <li><a class="dropdown-item" href="{{ route('reportes.index') }}">Reportes</a></li>
                                        @endcan
                                        @can('gestionar_tienda')
                                        <li><a class="dropdown-item" href="{{ route('admin.postventa.index') }}">Postventa</a></li>
                                        @endcan
                                        @can('procesar_reembolsos')
                                            <li><a class="dropdown-item" href="{{ route('reembolsos.index') }}">Reembolsos</a></li>
                                        @endcan
                                        @can('gestionar_tienda')
                                             <li><a class="dropdown-item" href="{{ route('admin.settings.index') }}">Ajustes de la tienda</a></li>
                                        @endcan
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
                                    @can('mi_perfil_superadmin')
                                        <li><a class="dropdown-item" href="{{ route('superadmin.perfil.index') }}">Mi Perfil</a></li>
                                    @endcan
                                    @can('gestionar_administradores')
                                        <li><a class="dropdown-item" href="{{ route('superadmin.admins.index') }}">Gestión de administradores</a></li>
                                    @endcan
                                    @can('ver_monitoreo')
                                        <li><a class="dropdown-item" href="#">Monitoreo del sistema</a></li>
                                    @endcan
                                    @can('ver_logs_seguridad')
                                        <li><a class="dropdown-item" href="#">Logs de seguridad</a></li>
                                    @endcan
                                    @can('configurar_sistema')
                                        <li><a class="dropdown-item" href="{{ route('superadmin.configuracion.index') }}">Configuración global</a></li>
                                    @endcan
                                    @can('gestionar_permisos')
                                        <li><a class="dropdown-item" href="{{ route('roles.permisos') }}">Gestión de permisos y roles</a></li>
                                    @endcan
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