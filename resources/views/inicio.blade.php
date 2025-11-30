<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ecommerce Agave - Inicio</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }

        header {
            background-color: #f8f9fa;
            padding: 15px 0;
            text-align: center;
            border-bottom: 1px solid #dee2e6;
        }

        .user-welcome {
            background: #e3f2fd;
            padding: 10px 0;
            text-align: center;
            border-bottom: 1px solid #bbdefb;
        }

        .user-welcome p {
            margin: 0;
            font-weight: bold;
            color: #1976d2;
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

        .productos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .sin-resultados {
            text-align: center;
            padding: 60px 20px;
            color: #666;
            background: #fff;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
        
        .sin-resultados h3 {
            margin-bottom: 15px;
            color: #333;
        }

        /* Estilos de tarjetas de producto */
        .producto-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-align: left;
            border: 1px solid #e1e1e1;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: visible;
        }

        .producto-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .producto-imagen-container {
            position: relative;
            padding: 15px;
            text-align: center;
            background: #f8f9fa;
            border-radius: 8px 8px 0 0;
        }

        .producto-imagen {
            width: 100%;
            height: 200px;
            object-fit: contain;
            border-radius: 8px;
            transition: transform 0.3s ease;
        }

        .producto-card:hover .producto-imagen {
            transform: scale(1.05);
        }

        .no-imagen {
            width: 100%;
            height: 200px;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            color: #6c757d;
        }

        .producto-info {
            padding: 15px;
        }

        .producto-card h3 {
            margin-top: 0;
            margin-bottom: 8px;
            color: #333;
            font-size: 16px;
            line-height: 1.3;
            height: 40px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .producto-precio {
            font-weight: bold;
            color: #2e7d32;
            margin-bottom: 8px;
            font-size: 22px;
        }

        .stock-info {
            font-size: 14px;
            margin-bottom: 10px;
        }

        .stock-bueno {
            color: #00a650;
        }

        .stock-bajo {
            color: #ff6b00;
            font-weight: bold;
        }

        .sin-stock {
            color: #dc3545;
            font-weight: bold;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            font-size: 11px;
            background-color: #6c757d;
            color: white;
            border-radius: 4px;
            margin-right: 3px;
            font-weight: bold;
        }

        .badge-stock {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
            color: #000;
        }

        .ver-detalle {
            margin-top: 12px;
            text-align: center;
        }

        .ver-detalle a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
        }

        .ver-detalle a:hover {
            text-decoration: underline;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
        }

        .btn:hover {
            background: #0056b3;
        }

        /* Barra de búsqueda única */
        .barra-busqueda-principal {
            text-align: center;
            margin: 20px 0;
            padding: 0 20px;
        }

        .barra-busqueda-principal form {
            display: inline-block;
            max-width: 600px;
            width: 100%;
        }

        .barra-busqueda-principal input[type="text"] {
            padding: 12px 20px;
            width: 70%;
            border: 2px solid #007bff;
            border-radius: 25px 0 0 25px;
            font-size: 16px;
            outline: none;
        }

        .barra-busqueda-principal button {
            padding: 12px 25px;
            background: #007bff;
            color: white;
            border: 2px solid #007bff;
            border-radius: 0 25px 25px 0;
            font-size: 16px;
            cursor: pointer;
            margin-left: -5px;
        }

        .barra-busqueda-principal button:hover {
            background: #0056b3;
            border-color: #0056b3;
        }

        /* Corazón de favoritos en productos - Estilo Mercado Libre */
        .corazon-favorito {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 100;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: none;
            font-size: 20px;
            transition: all 0.3s ease;
            backdrop-filter: blur(5px);
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        }

        .corazon-favorito:hover {
            background: rgba(255, 255, 255, 1);
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        .corazon-favorito.inactivo {
            color: rgba(0,0,0,0.3);
        }

        .corazon-favorito.activo {
            color: #3483fa;
            animation: latido 0.3s ease;
        }

        .corazon-favorito.activo::before {
            content: '❤️';
        }

        .corazon-favorito.inactivo::before {
            content: '🤍';
        }

        @keyframes latido {
            0% { transform: scale(1); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }

        /* Notificación toast */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #333;
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            z-index: 10000;
            font-size: 14px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        /* Banner de bienvenida */
        .banner-inicio {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
            padding: 60px 20px;
            margin-bottom: 40px;
        }

        .banner-inicio h2 {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .banner-inicio p {
            font-size: 1.2rem;
            margin-bottom: 25px;
        }

        .btn-banner {
            background: white;
            color: #667eea;
            padding: 12px 30px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .btn-banner:hover {
            background: #f8f9fa;
            transform: translateY(-2px);
        }

        /* Sección de productos destacados */
        .seccion-destacados {
            max-width: 1200px;
            margin: 0 auto 40px;
            padding: 0 20px;
        }

        .titulo-seccion {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
            font-size: 2rem;
        }

        .alert {
            padding: 15px;
            margin: 20px;
            border-radius: 5px;
            text-align: center;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        @media (max-width: 768px) {
            .productos-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 20px;
            }

            .barra-busqueda-principal input[type="text"] {
                width: 60%;
            }
        }

        @media (max-width: 480px) {
            .barra-busqueda-principal input[type="text"] {
                width: 100%;
                border-radius: 25px;
                margin-bottom: 10px;
            }
            
            .barra-busqueda-principal button {
                width: 100%;
                border-radius: 25px;
                margin-left: 0;
            }
            
            .barra-busqueda-principal form {
                display: flex;
                flex-direction: column;
            }

            .productos-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Ecommerce Agave</h1>
        <p>Encuentra los mejores productos de agave y mezcal</p>
    </header>

    <!-- Mostrar bienvenida al usuario si está autenticado -->
    @auth
    <div class="user-welcome">
        <p>¡Hola {{ Auth::user()->vNombre }}! 👋 Bienvenido a Ecommerce Agave</p>
    </div>
    @endauth

    <nav class="navbar">
        <ul>
            <li><a href="{{ route('home') }}">Inicio</a></li>
            <li><a href="{{ route('busqueda.resultados') }}">Todos los Productos</a></li>
            <li>
                @auth
                    <a href="{{ route('favoritos.index') }}" style="color: #dc3545;">❤️ Mis Favoritos</a>
                @else
                    <a href="{{ route('login') }}" style="color: #dc3545;">❤️ Mis Favoritos</a>
                @endauth
            </li>
            @auth
                <li><a href="#">Mi Carrito</a></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                        @csrf
                        <button type="submit" style="background: none; border: none; color: #495057; cursor: pointer; font-weight: bold;">Cerrar Sesión</button>
                    </form>
                </li>
            @else
                <li><a href="{{ route('login') }}">Ingresar</a></li>
                <li><a href="{{ route('usuarios.create') }}">Crear Cuenta</a></li>
            @endauth
        </ul>

        <!-- SOLO UNA BARRA DE BÚSQUEDA -->
        <div class="barra-busqueda-principal">
            <form action="{{ route('busqueda.resultados') }}" method="GET">
                <input type="text" name="q" placeholder="Buscar productos (agave, mezcal, espadín...)" 
                       value="{{ request('q') }}" autocomplete="off">
                <button type="submit">Buscar</button>
            </form>
        </div>
    </nav>

    <!-- Mostrar mensajes de éxito -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info">
            {{ session('info') }}
        </div>
    @endif

    <!-- Banner de bienvenida -->
    <div class="banner-inicio">
        <h2>Bienvenido a Ecommerce Agave</h2>
        <p>Descubre nuestra exclusiva selección de productos de agave y mezcal</p>
        <a href="{{ route('busqueda.resultados') }}" class="btn-banner">Explorar Productos</a>
    </div>

    <!-- Sección de productos destacados -->
    <div class="seccion-destacados">
        <h2 class="titulo-seccion">Productos Destacados</h2>
        
        @if(isset($productos) && $productos->count() > 0)
            <div class="productos-grid">
                @foreach($productos as $producto)
                    @php
                        $estaBajoStock = $producto->estaBajoEnStock();
                        $esFavorito = $producto->esFavorito();
                    @endphp
                    
                    <div class="producto-card" onclick="window.location.href='{{ route('productos.show.public', $producto->id_producto) }}'">
                        
                        <div class="producto-imagen-container">
                            <!-- BOTÓN DEL CORAZÓN - DENTRO DEL CONTENEDOR DE IMAGEN -->
                            <button class="corazon-favorito {{ $esFavorito ? 'activo' : 'inactivo' }}" 
                                    data-producto="{{ $producto->id_producto }}"
                                    data-es-favorito="{{ $esFavorito ? 'true' : 'false' }}"
                                    onclick="event.stopPropagation(); toggleFavorito(this, {{ $producto->id_producto }})">
                                <!-- El contenido se maneja con CSS -->
                            </button>

                            <!-- Solo badge de stock bajo si aplica -->
                            @if($estaBajoStock)
                                <div style="position: absolute; top: 10px; left: 10px; z-index: 99;">
                                    <span class="badge badge-stock">¡Últimas!</span>
                                </div>
                            @endif

                            @if(count($producto->imagenes) > 0)
                                <img src="{{ $producto->imagenes[0] }}" alt="{{ $producto->vNombre }}" class="producto-imagen">
                            @else
                                <div class="no-imagen">
                                    <span>🛒 Sin imagen</span>
                                </div>
                            @endif
                        </div>
                        
                        <div class="producto-info">
                            <h3>{{ $producto->vNombre }}</h3>

                            <!-- Precio - DATOS REALES -->
                            <div class="producto-precio">
                                ${{ number_format($producto->dPrecio_venta, 2) }}
                            </div>

                            <!-- Envío - INFORMACIÓN REAL -->
                            <div style="color: #666; font-size: 14px;">
                                📦 Envío gratis
                            </div>

                            <!-- Stock - DATOS REALES -->
                            <div class="stock-info {{ $producto->iStock > 10 ? 'stock-bueno' : ($producto->iStock > 0 ? 'stock-bajo' : 'sin-stock') }}">
                                @if($producto->iStock > 10)
                                    ✅ En stock
                                @elseif($producto->iStock > 0)
                                    ⚠️ Solo {{ $producto->iStock }} unidades
                                @else
                                    ❌ Sin stock
                                @endif
                            </div>

                            <div class="ver-detalle">
                                <a href="{{ route('productos.show.public', $producto->id_producto) }}" onclick="event.stopPropagation();">Ver detalle →</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="text-align: center; margin-top: 30px;">
                <a href="{{ route('busqueda.resultados') }}" class="btn">Ver Todos los Productos</a>
            </div>
        @else
            <div class="sin-resultados">
                <h3>No hay productos disponibles</h3>
                <p>Pronto agregaremos nuevos productos a nuestro catálogo.</p>
            </div>
        @endif
    </div>

    <script>
        // Función para toggle favoritos en productos
        function toggleFavorito(button, productoId) {
            // Verificar primero si el usuario está autenticado
            @if(!Auth::check())
                // Si no está autenticado, redirigir directamente al login
                window.location.href = '{{ route("login") }}';
                return;
            @endif

            const esFavorito = button.getAttribute('data-es-favorito') === 'true';
            
            fetch(`/favoritos/toggle/${productoId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (response.status === 401) {
                    // No autenticado - redirigir al login
                    window.location.href = '{{ route("login") }}';
                    return null;
                }
                return response.json();
            })
            .then(data => {
                if (!data) return; // Si es null (401), ya redirigimos
                
                if (data.success) {
                    if (data.action === 'added') {
                        button.classList.remove('inactivo');
                        button.classList.add('activo');
                        button.setAttribute('data-es-favorito', 'true');
                        showNotification('✅ Producto agregado a favoritos');
                    } else {
                        button.classList.remove('activo');
                        button.classList.add('inactivo');
                        button.setAttribute('data-es-favorito', 'false');
                        showNotification('❌ Producto eliminado de favoritos');
                    }
                } else {
                    showNotification('❌ ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('❌ Error al gestionar favoritos');
            });
        }

        // Función para mostrar notificaciones
        function showNotification(message) {
            // Crear elemento de notificación
            const notification = document.createElement('div');
            notification.className = 'toast';
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            // Remover después de 3 segundos
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => {
                    if (document.body.contains(notification)) {
                        document.body.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }

        // Auto-focus en la barra de búsqueda al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('.barra-busqueda-principal input[type="text"]');
            if (searchInput) {
                searchInput.focus();
            }
        });
    </script>
</body>
</html>