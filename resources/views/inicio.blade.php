<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Inicio</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #f8f9fa;
            padding: 15px 0;
            text-align: center;
            border-bottom: 1px solid #dee2e6;
        }
        nav.navbar {
            background-color: #e9ecef;
            padding: 10px 0;
        }
        nav.navbar ul {
            list-style: none;
            display: flex;
            justify-content: center;
            gap: 25px;
            padding: 0;
            margin: 0;
        }
        nav.navbar ul li {
            display: inline;
        }
        nav.navbar ul li a {
            color: #495057;
            text-decoration: none;
            font-weight: bold;
        }
        nav.navbar ul li a:hover {
            text-decoration: underline;
        }
        main {
            padding: 30px;
            text-align: center;
        }
        button.logout-btn {
            background: none; 
            color: #495057; 
            border: none; 
            cursor: pointer;
            font-weight: bold;
        }
        .productos-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .producto-card {
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
            text-align: left;
            border: 1px solid #dee2e6;
        }
        .producto-card h3 {
            margin-top: 0;
        }
        .badge {
            display: inline-block;
            padding: 3px 6px;
            font-size: 12px;
            background-color: #6c757d;
            color: white;
            border-radius: 4px;
            margin-right: 3px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Bienvenido a la página de inicio</h1>
    </header>

    <nav class="navbar">
        <p style="text-align:center; font-size:14px;">
            Auth::check() = {{ Auth::check() ? 'true' : 'false' }} |
            Usuario = {{ Auth::user() ? Auth::user()->id_usuario : 'ninguno' }}
        </p>

        <ul>
            @auth('web')
                <li>Hola, {{ Auth::user()->vNombre }}</li>
                <li><a href="#">Mi Carrito</a></li>
                <li><a href="#">Mis Pedidos</a></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="logout-btn">Cerrar Sesión</button>
                    </form>
                </li>
            @endauth

            @guest
                <li><a href="{{ route('login') }}">Ingresar</a></li>
                <li><a href="{{ route('usuarios.create') }}">Crear Cuenta</a></li>
            @endguest
        </ul>
    </nav>

    <main>
        @auth
            <p>Hola, {{ Auth::user()->vNombre }}. Has iniciado sesión correctamente.</p>
        @else
            <p>No has iniciado sesión.</p>
            <a href="{{ route('login') }}">Iniciar sesión</a> |
            <a href="{{ route('usuarios.create') }}">Registrarse</a>
        @endauth

        {{-- Mostrar productos registrados --}}
        @if ($productos->count() > 0)
            <h2>Productos Disponibles</h2>
            <div class="productos-container">
                @foreach ($productos as $producto)
                    <div class="producto-card">
                        <h3>{{ $producto->vNombre }}</h3>
                        <p><strong>Código de barras:</strong> {{ $producto->vCodigo_barras }}</p>
                        <p><strong>Precio de venta:</strong> ${{ number_format($producto->dPrecio_venta, 2) }}</p>
                        <p><strong>Stock:</strong> {{ $producto->iStock }}</p>
                        <p><strong>Categoría:</strong> {{ $producto->categoria->vNombre ?? 'N/A' }}</p>
                        <p><strong>Marca:</strong> {{ $producto->marca->vNombre ?? 'N/A' }}</p>
                        @if ($producto->etiquetas->count() > 0)
                            <p>
                                @foreach ($producto->etiquetas as $etiqueta)
                                    <span class="badge">{{ $etiqueta->vNombre }}</span>
                                @endforeach
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <p>No hay productos registrados aún.</p>
        @endif
    </main>
</body>
</html>