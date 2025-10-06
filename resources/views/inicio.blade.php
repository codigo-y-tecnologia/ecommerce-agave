<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <header>
          @auth
          <p>Hola, {{ Auth::user()->vNombre }}. Tu rol es: {{ Auth::user()->eRol }}</p>

          @if(Auth::user()->eRol === 'cliente')
           {{-- Panel de cliente --}}
         <nav class="navbar">
    <ul>
            <li><a href="#">🛒 Mi Carrito</a></li>
            <li><a href="#">Mis Pedidos</a></li>
        </ul>
    </nav>

    @elseif(Auth::user()->eRol === 'admin')
        {{-- Panel de administrador --}}
        <nav class="navbar">
            <ul>
                <li><a href="#">Gestionar Usuarios</a></li>
                <li><a href="#">Ver Carritos de Clientes</a></li>
                <li><a href="#">Reportes</a></li>
            </ul>
        </nav>
    @elseif(Auth::user()->eRol === 'superadmin')
        {{-- Panel de superadmin --}}
        <nav class="navbar">
            <ul>
                <li><a href="#">⚙ Configuración General</a></li>
                <li><a href="#">Gestión Avanzada de Usuarios</a></li>
                <li><a href="#">Monitoreo del Sistema</a></li>
            </ul>
        </nav>
    @endif

    {{-- Botón de cerrar sesión --}}
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Cerrar sesión</button>
    </form>
    @endauth

        @guest
            <p>No has iniciado sesión.</p>
            <li><a href="{{ route('login') }}">Ingresar</a></li>
            <li><a href="{{ route('usuarios.create') }}">Crear Cuenta</a></li>
        @endguest
    </header>
    <main>

    </main>
</body>
</html>