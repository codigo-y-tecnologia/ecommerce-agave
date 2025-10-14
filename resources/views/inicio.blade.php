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
            <li><a href="{{ route('carrito.index') }}">🛒 Mi Carrito</a></li>
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
        
        <h1>Bienvenido a la tienda en línea</h1>
        <p>Explora nuestros productos y realiza tus compras de manera fácil y segura.</p>

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