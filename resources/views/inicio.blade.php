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
            {{-- {{ dd(Auth::check(), Auth::user()) }} --}}
            <p>Auth::check() = {{ Auth::check() ? 'true' : 'false' }}</p>
<p>Usuario = {{ Auth::user() ? Auth::user()->id_usuario : 'ninguno' }}</p>

    <ul>
        @auth('web')
            <li>Hola, {{ Auth::user()->vNombre }}</li>
            <li><a href="{{ route('carrito.index') }}">Mi Carrito</a></li>
            <li><a href="#">Mis Pedidos</a></li>
            <li><a href="{{ route('logout') }}">Cerrar Sesión</a></li>
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
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Cerrar sesión</button>
            </form>
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