<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
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
            line-height: 1.5;
            overflow-x: hidden;
            width: 100%;
        }

        header {
            background-color: #f8f9fa;
            padding: 15px 0;
            text-align: center;
            border-bottom: 1px solid #dee2e6;
        }

        header h1 {
            font-size: clamp(1.5rem, 5vw, 2rem);
            padding: 0 15px;
        }

        header p {
            font-size: clamp(0.9rem, 3vw, 1rem);
            padding: 0 15px;
            color: #666;
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
            font-size: clamp(0.85rem, 3vw, 1rem);
            padding: 0 15px;
            word-break: break-word;
        }

        /* Navbar */
        .navbar {
            background-color: #e9ecef;
            padding: 10px 0;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .nav-links {
            list-style: none;
            display: flex;
            justify-content: center;
            gap: 25px;
            padding: 0;
            margin: 0;
            flex-wrap: wrap;
        }

        .nav-links li {
            display: inline;
        }

        .nav-links li a {
            color: #495057;
            text-decoration: none;
            font-weight: bold;
            font-size: clamp(0.85rem, 2.5vw, 1rem);
            white-space: nowrap;
        }

        .nav-links li a:hover {
            text-decoration: underline;
        }

        .nav-links li button {
            font-size: clamp(0.85rem, 2.5vw, 1rem);
        }

        .productos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
            padding: 0 15px;
        }
        
        .sin-resultados {
            text-align: center;
            padding: 40px 20px;
            color: #666;
            background: #fff;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            margin: 0 15px;
        }
        
        .sin-resultados h3 {
            margin-bottom: 15px;
            color: #333;
            font-size: clamp(1.2rem, 4vw, 1.5rem);
        }

        .sin-resultados p {
            font-size: clamp(0.9rem, 3vw, 1rem);
            margin-bottom: 15px;
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
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
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
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            aspect-ratio: 1 / 1;
        }

        .producto-imagen {
            width: 100%;
            height: 100%;
            max-height: 200px;
            object-fit: contain;
            border-radius: 8px;
            transition: transform 0.3s ease;
        }

        .producto-card:hover .producto-imagen {
            transform: scale(1.05);
        }

        .no-imagen {
            width: 100%;
            height: 100%;
            min-height: 200px;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            color: #6c757d;
            font-size: 14px;
        }

        .producto-info {
            padding: 15px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .producto-card h3 {
            margin-top: 0;
            margin-bottom: 8px;
            color: #333;
            font-size: clamp(0.9rem, 3vw, 1rem);
            line-height: 1.3;
            height: 40px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            word-break: break-word;
        }

        .producto-precio {
            font-weight: bold;
            color: #2e7d32;
            margin-bottom: 8px;
            font-size: clamp(1.2rem, 4vw, 1.4rem);
        }

        .stock-info {
            font-size: clamp(0.8rem, 2.5vw, 0.9rem);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 5px;
            flex-wrap: wrap;
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
            font-size: clamp(0.8rem, 2.5vw, 0.9rem);
            display: inline-block;
            padding: 8px 0;
        }

        .ver-detalle a:hover {
            text-decoration: underline;
        }

        .btn {
            display: inline-block;
            padding: 12px 25px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 25px;
            border: none;
            cursor: pointer;
            font-size: clamp(0.9rem, 3vw, 1rem);
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,123,255,0.3);
            margin: 0 15px;
        }

        .btn:hover {
            background: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,123,255,0.4);
        }

        .btn:active {
            transform: translateY(0);
        }

        /* Barra de búsqueda única */
        .barra-busqueda-principal {
            text-align: center;
            margin: 15px 0;
            padding: 0 15px;
        }

        .barra-busqueda-principal form {
            display: flex;
            max-width: 600px;
            width: 100%;
            margin: 0 auto;
        }

        .barra-busqueda-principal input[type="text"] {
            flex: 1;
            padding: 12px 20px;
            border: 2px solid #007bff;
            border-radius: 25px 0 0 25px;
            font-size: 16px;
            outline: none;
            min-width: 0; /* Previene overflow en flex */
        }

        .barra-busqueda-principal button {
            padding: 12px 25px;
            background: #007bff;
            color: white;
            border: 2px solid #007bff;
            border-radius: 0 25px 25px 0;
            font-size: 16px;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.3s ease;
        }

        .barra-busqueda-principal button:hover {
            background: #0056b3;
            border-color: #0056b3;
        }

        .barra-busqueda-principal button:active {
            transform: scale(0.98);
        }

        /* Corazón de favoritos */
        .corazon-favorito {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 100;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: 1px solid rgba(0, 0, 0, 0.1);
            font-size: 20px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            -webkit-tap-highlight-color: transparent;
        }

        .corazon-favorito:hover {
            background: rgba(255, 255, 255, 1);
            transform: scale(1.1);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.25);
        }

        .corazon-favorito:active {
            transform: scale(0.95);
        }

        .corazon-favorito.activo {
            color: #3483fa;
            background: rgba(52, 131, 250, 0.1);
            border-color: #3483fa;
        }

        .corazon-favorito.inactivo {
            color: rgba(0, 0, 0, 0.25);
        }

        /* NOTIFICACIÓN ÚNICA */
        .toast-single {
            position: fixed;
            top: 20px;
            right: 20px;
            left: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            z-index: 10000;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            transition: all 0.4s ease;
            display: flex;
            align-items: center;
            gap: 12px;
            max-width: 350px;
            margin: 0 auto;
            transform: translateY(-120%);
            opacity: 0;
        }

        .toast-single.show {
            transform: translateY(0);
            opacity: 1;
        }

        /* Banner de bienvenida */
        .banner-inicio {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
            padding: 40px 20px;
            margin-bottom: 30px;
        }

        .banner-inicio h2 {
            font-size: clamp(1.5rem, 6vw, 2.5rem);
            margin-bottom: 10px;
            padding: 0 15px;
            word-break: break-word;
        }

        .banner-inicio p {
            font-size: clamp(1rem, 4vw, 1.2rem);
            margin-bottom: 20px;
            padding: 0 15px;
        }

        .btn-banner {
            background: white;
            color: #667eea;
            padding: 12px 30px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
            display: inline-block;
            font-size: clamp(0.9rem, 3vw, 1rem);
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }

        .btn-banner:hover {
            background: #f8f9fa;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.3);
        }

        .btn-banner:active {
            transform: translateY(0);
        }

        /* Sección de productos destacados */
        .seccion-destacados {
            max-width: 1200px;
            margin: 0 auto 30px;
            padding: 0;
        }

        .titulo-seccion {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
            font-size: clamp(1.3rem, 5vw, 2rem);
            padding: 0 15px;
        }

        .alert {
            padding: 12px 15px;
            margin: 15px auto;
            border-radius: 5px;
            text-align: center;
            max-width: 800px;
            font-size: clamp(0.85rem, 3vw, 0.95rem);
            width: calc(100% - 30px);
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

        /* PAGINACIÓN PERSONALIZADA - SOLO FLECHAS Y NÚMEROS */
        .paginacion {
            display: flex;
            justify-content: center;
            margin: 30px 0 20px;
            padding: 0 15px;
        }

        .pagination {
            display: flex;
            list-style: none;
            gap: 5px;
            padding: 5px;
            margin: 0;
            background: white;
            border-radius: 40px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            flex-wrap: wrap;
            justify-content: center;
        }

        .pagination li {
            display: inline-flex;
        }

        .pagination li a,
        .pagination li span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 6px;
            border-radius: 50%;
            text-decoration: none;
            color: #495057;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s ease;
            background-color: transparent;
            border: 1px solid transparent;
        }

        .pagination li a:hover {
            background-color: #e9ecef;
            border-color: #dee2e6;
            color: #007bff;
        }

        .pagination li a:active {
            transform: scale(0.95);
        }

        .pagination li.active span {
            background: #007bff;
            color: white;
            font-weight: 600;
        }

        /* Estilos específicos para los botones de anterior/siguiente */
        .pagination li:first-child a,
        .pagination li:last-child a {
            font-size: 16px;
            font-weight: bold;
        }

        .pagination li.disabled span {
            color: #adb5bd;
            cursor: not-allowed;
            background-color: transparent;
        }

        /* Ocultar el texto y mostrar solo flechas */
        .pagination li:first-child a span,
        .pagination li:last-child a span {
            display: none;
        }

        .pagination li:first-child a::before {
            content: "←";
            font-size: 18px;
        }

        .pagination li:last-child a::before {
            content: "→";
            font-size: 18px;
        }

        /* Botón flotante para móviles (opcional) */
        .btn-flotante {
            display: none;
        }

        /* Media Queries específicas para móviles */
        @media (max-width: 768px) {
            .productos-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 15px;
                padding: 0 12px;
            }

            .barra-busqueda-principal input[type="text"] {
                font-size: 14px;
                padding: 10px 15px;
            }

            .barra-busqueda-principal button {
                padding: 10px 18px;
                font-size: 14px;
            }

            .nav-links {
                gap: 12px;
                padding: 0 10px;
            }

            .nav-links li a,
            .nav-links li button {
                font-size: 13px;
            }

            .producto-imagen-container {
                min-height: 180px;
                padding: 10px;
            }

            .producto-imagen {
                max-height: 160px;
            }

            .no-imagen {
                min-height: 160px;
            }

            .pagination {
                gap: 3px;
                padding: 4px;
            }

            .pagination li a,
            .pagination li span {
                min-width: 32px;
                height: 32px;
                font-size: 12px;
            }

            .pagination li:first-child a::before,
            .pagination li:last-child a::before {
                font-size: 16px;
            }
        }

        @media (max-width: 480px) {
            .productos-grid {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
                gap: 10px;
                padding: 0 10px;
            }

            .producto-info {
                padding: 12px;
            }

            .producto-card h3 {
                font-size: 13px;
                height: 34px;
                margin-bottom: 5px;
            }

            .producto-precio {
                font-size: 16px;
                margin-bottom: 5px;
            }

            .stock-info {
                font-size: 11px;
                margin-bottom: 8px;
            }

            .ver-detalle a {
                font-size: 12px;
                padding: 6px 0;
            }

            .corazon-favorito {
                width: 32px;
                height: 32px;
                font-size: 16px;
                top: 8px;
                right: 8px;
            }

            .badge {
                padding: 3px 6px;
                font-size: 9px;
            }

            .banner-inicio {
                padding: 25px 15px;
            }

            .banner-inicio h2 {
                font-size: 1.3rem;
            }

            .banner-inicio p {
                font-size: 0.9rem;
            }

            .btn-banner {
                padding: 8px 20px;
                font-size: 0.85rem;
            }

            .nav-links {
                flex-direction: column;
                gap: 8px;
                align-items: center;
            }

            .nav-links li {
                width: 100%;
                text-align: center;
            }

            .nav-links li a,
            .nav-links li button {
                display: block;
                padding: 8px;
                white-space: normal;
                font-size: 14px;
            }

            .nav-links li button {
                width: 100%;
            }

            .barra-busqueda-principal form {
                flex-direction: column;
                gap: 8px;
            }

            .barra-busqueda-principal input[type="text"] {
                width: 100%;
                border-radius: 25px;
                font-size: 14px;
            }

            .barra-busqueda-principal button {
                width: 100%;
                border-radius: 25px;
                margin-left: 0;
                font-size: 14px;
                padding: 10px;
            }

            .producto-imagen-container {
                min-height: 140px;
                padding: 8px;
            }

            .producto-imagen {
                max-height: 130px;
            }

            .no-imagen {
                min-height: 130px;
                font-size: 12px;
            }

            .pagination li a,
            .pagination li span {
                min-width: 28px;
                height: 28px;
                font-size: 11px;
            }

            .pagination li:first-child a::before,
            .pagination li:last-child a::before {
                font-size: 14px;
            }

            .toast-single {
                left: 15px;
                right: 15px;
                max-width: none;
                padding: 12px 15px;
                font-size: 13px;
            }
        }

        /* Para pantallas muy pequeñas */
        @media (max-width: 320px) {
            .productos-grid {
                grid-template-columns: 1fr;
            }

            .producto-imagen-container {
                min-height: 200px;
            }

            .pagination li a,
            .pagination li span {
                min-width: 26px;
                height: 26px;
                font-size: 10px;
            }
        }

        /* Mejoras para touch en móviles */
        @media (hover: none) and (pointer: coarse) {
            .corazon-favorito {
                -webkit-tap-highlight-color: transparent;
            }

            .corazon-favorito:active {
                background: rgba(52, 131, 250, 0.2);
                transform: scale(0.95);
            }

            .btn:active,
            .btn-banner:active,
            .barra-busqueda-principal button:active {
                transform: scale(0.97);
            }

            .producto-card:active {
                transform: scale(0.99);
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

    <!-- Navbar -->
    <nav class="navbar">
        <div class="nav-container">
            <ul class="nav-links">
                <li><a href="{{ route('inicio.real') }}">Inicio</a></li>
                <li><a href="{{ route('busqueda.resultados') }}">Todos los Productos</a></li>
                <li>
                    @auth
                        <a href="{{ route('favoritos.index') }}" style="color: #dc3545; font-weight: bold;">❤️ Mis Favoritos</a>
                    @else
                        <a href="{{ route('login') }}" style="color: #dc3545; font-weight: bold;">❤️ Mis Favoritos</a>
                    @endauth
                </li>
                @auth
                    <li><a href="#">Mi Carrito</a></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                            @csrf
                            <button type="submit" style="background: none; border: none; color: #495057; cursor: pointer; font-weight: bold; font-size: 16px;">Cerrar Sesión</button>
                        </form>
                    </li>
                @else
                    <li><a href="{{ route('login') }}">Ingresar</a></li>
                    <li><a href="{{ route('usuarios.create') }}">Crear Cuenta</a></li>
                @endauth
            </ul>
        </div>

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
                            <!-- BOTÓN DEL CORAZÓN -->
                            <button class="corazon-favorito {{ $esFavorito ? 'activo' : 'inactivo' }}" 
                                    data-producto="{{ $producto->id_producto }}"
                                    onclick="event.stopPropagation(); toggleFavorito(this, {{ $producto->id_producto }})"
                                    title="{{ $esFavorito ? 'Quitar de favoritos' : 'Agregar a favoritos' }}">
                                {{ $esFavorito ? '❤️' : '🤍' }}
                            </button>

                            <!-- Solo badge de stock bajo si aplica -->
                            @if($estaBajoStock)
                                <div style="position: absolute; top: 15px; left: 15px; z-index: 99;">
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
                            <div style="color: #666; font-size: clamp(0.7rem, 2.5vw, 0.85rem); margin-bottom: 5px;">
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

            <!-- PAGINACIÓN PERSONALIZADA - SOLO FLECHAS Y NÚMEROS -->
            <div class="paginacion">
                @if ($productos instanceof \Illuminate\Pagination\LengthAwarePaginator && $productos->hasPages())
                    <ul class="pagination">
                        {{-- Flecha Anterior --}}
                        @if ($productos->onFirstPage())
                            <li class="disabled" aria-disabled="true">
                                <span></span>
                            </li>
                        @else
                            <li>
                                <a href="{{ $productos->previousPageUrl() }}" rel="prev" aria-label="Anterior"></a>
                            </li>
                        @endif

                        {{-- Números de página --}}
                        @foreach ($productos->getUrlRange(max(1, $productos->currentPage() - 2), min($productos->lastPage(), $productos->currentPage() + 2)) as $page => $url)
                            @if ($page == $productos->currentPage())
                                <li class="active" aria-current="page"><span>{{ $page }}</span></li>
                            @else
                                <li><a href="{{ $url }}">{{ $page }}</a></li>
                            @endif
                        @endforeach

                        {{-- Flecha Siguiente --}}
                        @if ($productos->hasMorePages())
                            <li>
                                <a href="{{ $productos->nextPageUrl() }}" rel="next" aria-label="Siguiente"></a>
                            </li>
                        @else
                            <li class="disabled" aria-disabled="true">
                                <span></span>
                            </li>
                        @endif
                    </ul>
                @endif
            </div>

            <div style="text-align: center; margin-top: 20px; margin-bottom: 20px;">
                <a href="{{ route('busqueda.resultados') }}" class="btn">Ver Todos los Productos</a>
            </div>
        @else
            <div class="sin-resultados">
                <h3>No hay productos disponibles</h3>
                <p>Pronto agregaremos nuevos productos a nuestro catálogo.</p>
                <a href="{{ route('busqueda.resultados') }}" class="btn" style="margin-top: 15px;">Ver todos los productos</a>
            </div>
        @endif
    </div>

    <script>
        // VARIABLE GLOBAL para controlar UNA sola notificación
        let singleToast = null;
        let singleToastTimeout = null;

        // Función para toggle favoritos en productos
        function toggleFavorito(button, productoId) {
            if (button.disabled) return;
            button.disabled = true;

            // Verificar si el usuario está autenticado
            @if(!Auth::check())
                window.location.href = '{{ route("login") }}?from_favoritos=true&redirect=' + encodeURIComponent(window.location.href);
                return;
            @endif

            const esFavorito = button.classList.contains('activo');
            
            // Animación simple
            button.style.transform = 'scale(0.9)';
            
            // 1. ELIMINAR NOTIFICACIÓN ANTERIOR
            removeSingleToast();
            
            fetch(`/favoritos/toggle/${productoId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (response.status === 401) {
                    window.location.href = '{{ route("login") }}?from_favoritos=true&redirect=' + encodeURIComponent(window.location.href);
                    return null;
                }
                return response.json();
            })
            .then(data => {
                if (!data) return;
                
                if (data.success) {
                    if (data.action === 'added') {
                        // Cambiar a estado activo
                        button.classList.remove('inactivo');
                        button.classList.add('activo');
                        button.innerHTML = '❤️';
                        
                        // 2. MOSTRAR SOLO UNA NOTIFICACIÓN - 3 SEGUNDOS
                        showSingleNotification('Producto agregado a favoritos ✅', 3000);
                        
                    } else {
                        // Cambiar a estado inactivo
                        button.classList.remove('activo');
                        button.classList.add('inactivo');
                        button.innerHTML = '🤍';
                        
                        // 3. MOSTRAR SOLO UNA NOTIFICACIÓN - 3 SEGUNDOS
                        showSingleNotification('Producto eliminado de favoritos ❌', 3000);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showSingleNotification('Error de conexión ❌', 3000);
            })
            .finally(() => {
                setTimeout(() => {
                    button.disabled = false;
                    button.style.transform = '';
                }, 300);
            });
        }

        // FUNCIÓN PARA ELIMINAR NOTIFICACIÓN ANTERIOR
        function removeSingleToast() {
            if (singleToast) {
                singleToast.classList.remove('show');
                setTimeout(() => {
                    if (singleToast && singleToast.parentNode) {
                        singleToast.parentNode.removeChild(singleToast);
                    }
                    singleToast = null;
                }, 300);
            }
            
            if (singleToastTimeout) {
                clearTimeout(singleToastTimeout);
                singleToastTimeout = null;
            }
            
            // Eliminar cualquier otro toast
            const allToasts = document.querySelectorAll('.toast-single');
            allToasts.forEach(toast => {
                toast.classList.remove('show');
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 300);
            });
        }

        // FUNCIÓN PARA MOSTRAR UNA SOLA NOTIFICACIÓN
        function showSingleNotification(message, duration = 3000) {
            // 1. Eliminar notificación anterior
            removeSingleToast();
            
            // 2. Crear nueva notificación
            const toast = document.createElement('div');
            toast.className = 'toast-single';
            
            // Determinar emoji basado en el mensaje
            const emoji = message.includes('✅') ? '✅' : '❌';
            const cleanMessage = message.replace('✅', '').replace('❌', '').trim();
            
            toast.innerHTML = `
                <span style="font-size: 20px;">${emoji}</span>
                <span>${cleanMessage}</span>
            `;
            
            document.body.appendChild(toast);
            singleToast = toast;
            
            // 3. Mostrar con animación
            setTimeout(() => {
                toast.classList.add('show');
            }, 10);
            
            // 4. Configurar para eliminar después del tiempo especificado
            singleToastTimeout = setTimeout(() => {
                if (toast.classList.contains('show')) {
                    toast.classList.remove('show');
                    setTimeout(() => {
                        if (toast.parentNode) {
                            toast.parentNode.removeChild(toast);
                        }
                        singleToast = null;
                        singleToastTimeout = null;
                    }, 400);
                }
            }, duration);
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Limpiar localStorage
            localStorage.removeItem('favorito_removed');
            localStorage.removeItem('favorito_removed_time');
            localStorage.removeItem('favorito_added');
            localStorage.removeItem('favorito_added_time');
            
            // Auto-focus en la barra de búsqueda (solo en desktop)
            if (window.innerWidth > 768) {
                const searchInput = document.querySelector('.barra-busqueda-principal input[type="text"]');
                if (searchInput) {
                    searchInput.focus();
                }
            }

            // Prevenir zoom en inputs para móviles (opcional)
            const inputs = document.querySelectorAll('input[type="text"], input[type="number"], select');
            inputs.forEach(input => {
                input.addEventListener('touchstart', function() {
                    this.style.fontSize = '16px';
                });
            });
        });

        // Detectar cambios de orientación en móviles
        window.addEventListener('resize', function() {
            // Ajustar algo si es necesario
        });
    </script>
</body>
</html>