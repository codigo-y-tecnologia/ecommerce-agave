<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <header>
        <h1>Bienvenido a la página de inicio</h1>

         <nav class="navbar">

    <ul>
        @auth

        @role('cliente')
           {{-- Panel de cliente --}}
            <li>Hola, {{ Auth::user()->vNombre }}</li>
            @can('ver_carrito')
                <li><a href="{{ route('carrito.index') }}">Mi Carrito</a></li>
            @endcan
            @can('ver_pedidos_propios')
                <li><a href="#">Mis Pedidos</a></li>
            @endcan
            
            {{-- Botón de cerrar sesión --}}
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Cerrar sesión</button>
    </form>
        @endrole

        @role('admin')
        {{-- Panel de administrador --}}
        <nav class="navbar">
            <ul>
                @can('mi_perfil_admin')
                <li><a href="{{ route('admin.perfil.index') }}">Mi Perfil</a></li>
                @endcan
                @can('ver_clientes')
                <li><a href="{{ route('admin.usuarios') }}">Clientes registrados</a></li>
                @endcan
                <li><a href="#">Ver Carritos de Clientes</a></li>
                <li><a href="#">Reportes</a></li>
            </ul>
        </nav>
    @endrole

    @role('superadmin')
        {{-- Panel de superadmin --}}
        <nav class="navbar">
            <ul>
                @can('mi_perfil_superadmin')
                <li><a href="{{ route('superadmin.perfil.index') }}">Mi Perfil</a></li>
                @endcan
                @can('gestionar_administradores')
                <li><a href="{{ route('superadmin.admins.index') }}">Gestión de Administradores</a></li>
                @endcan
                <li><a href="#">⚙ Configuración General</a></li>
                <li><a href="#">Monitoreo del Sistema</a></li>
                @can('gestionar_permisos')
                <li><a href="{{ route('roles.permisos') }}">Gestión de permisos</a></li>
                @endcan
            </ul>
        </nav>
    @endrole
        @endauth

        @guest
            <li><a href="{{ route('login') }}">Ingresar</a></li>
            <li><a href="{{ route('usuarios.create') }}">Crear Cuenta</a></li>
        @endguest
    </ul>
</nav>
    </header>
    <main>
        @auth
            <p>Hola, {{ Auth::user()->vNombre }}. Has iniciado sesión.</p>
        @else
            <p>No has iniciado sesión.</p>
            <a href="{{ route('login') }}">Iniciar sesión</a> |
            <a href="{{ route('usuarios.create') }}">Registrarse</a>
        @endauth

        <h2>Agregar productos al carrito</h2>

@if($productos->isEmpty())
    <p>No hay productos disponibles por ahora.</p>
@else
    <div class="row">
    @foreach($productos as $producto)
        <div class="col-md-4 mb-3">
            <div class="card p-3">
                <h5>{{ $producto->vNombre }}</h5>
                <p>Precio: ${{ number_format($producto->dPrecio_venta,2) }}</p>

                <form action="{{ route('carrito.store', $producto->id_producto) }}" method="POST" class="d-flex align-items-center">
                    @csrf
                    <input type="number" name="cantidad" value="1" min="1" class="form-control w-25 me-2">
                    <button type="submit" class="btn btn-success btn-sm">🛒 Agregar</button>
                </form>
            </div>
        </div>
    @endforeach
    </div>
@endif

    </main>
</body>
</html>