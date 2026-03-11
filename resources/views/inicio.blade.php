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
            min-width: 0;
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

        /* Estilos de tarjetas de producto - ESTILO MERCADO LIBRE */
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

        /* PRECIOS ESTILO MERCADO LIBRE */
        .precio-container {
            margin-bottom: 8px;
        }

        .precio-original {
            text-decoration: line-through;
            color: #999;
            font-size: 12px;
            display: block;
        }

        .precio-actual {
            font-weight: 600;
            color: #333;
            font-size: 24px;
            line-height: 1.2;
        }

        .precio-actual small {
            font-size: 14px;
            font-weight: 400;
            color: #666;
            margin-left: 4px;
        }

        .descuento-badge {
            background: #00a650;
            color: white;
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 8px;
            display: inline-block;
        }

        /* ENVÍO */
        .envio-info {
            font-size: 14px;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .envio-gratis {
            color: #00a650;
            font-weight: 600;
        }

        .envio-gratis i {
            font-size: 16px;
        }

        .envio-pago {
            color: #ff6b00;
            font-weight: 600;
        }

        .envio-pago i {
            font-size: 16px;
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

        .badge-descuento {
            position: absolute;
            top: 15px;
            left: 15px;
            background: #00a650;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            z-index: 99;
            box-shadow: 0 2px 5px rgba(0,166,80,0.3);
        }

        .badge-stock-bajo {
            position: absolute;
            top: 15px;
            left: 15px;
            background: #ff6b00;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            z-index: 99;
        }

        /* BADGE DE VARIACIÓN */
        .badge-variacion {
            position: absolute;
            bottom: 10px;
            left: 10px;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            z-index: 99;
            max-width: 90%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .badge-etiqueta {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            margin: 2px;
            color: white;
        }

        /* BADGE DE MÁS VENDIDO */
        .badge-mas-vendido {
            position: absolute;
            top: 15px;
            right: 60px;
            background: #ffd700;
            color: #333;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            z-index: 99;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
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

        .btn-descuento {
            background: #00a650;
        }

        .btn-descuento:hover {
            background: #008f45;
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
            color: #ff4757;
            background: rgba(255, 71, 87, 0.1);
            border-color: #ff4757;
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

        /* Banner de bienvenida estilo ML */
        .banner-inicio {
            background: linear-gradient(135deg, #3483fa 0%, #1e4d8c 100%);
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
            color: #3483fa;
            padding: 12px 30px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
            display: inline-block;
            font-size: clamp(0.9rem, 3vw, 1rem);
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            margin: 5px;
        }

        .btn-banner:hover {
            background: #f8f9fa;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.3);
        }

        .btn-banner:active {
            transform: translateY(0);
        }

        .btn-banner.descuento {
            background: #00a650;
            color: white;
        }

        .btn-banner.descuento:hover {
            background: #008f45;
        }

        /* Sección de productos */
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
            position: relative;
        }

        .titulo-seccion::after {
            content: '';
            display: block;
            width: 60px;
            height: 3px;
            background: #3483fa;
            margin: 10px auto 0;
        }

        .titulo-seccion.descuento {
            color: #00a650;
        }

        .titulo-seccion.descuento::after {
            background: #00a650;
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

        /* PAGINACIÓN */
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

        .pagination li.active span {
            background: #3483fa;
            color: white;
            font-weight: 600;
        }

        .pagination li:first-child a::before {
            content: "←";
            font-size: 18px;
        }

        .pagination li:last-child a::before {
            content: "→";
            font-size: 18px;
        }

        .pagination li.disabled span {
            color: #adb5bd;
            cursor: not-allowed;
            background-color: transparent;
        }

        .etiquetas-container {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-top: 8px;
        }

        /* Media Queries */
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
            
            .badge-variacion {
                font-size: 10px;
                padding: 3px 6px;
            }
        }

        @media (max-width: 480px) {
            .productos-grid {
                grid-template-columns: 1fr;
                gap: 10px;
                padding: 0 10px;
            }

            .producto-info {
                padding: 12px;
            }

            .producto-card h3 {
                font-size: 13px;
                height: 34px;
            }

            .precio-actual {
                font-size: 20px;
            }

            .corazon-favorito {
                width: 32px;
                height: 32px;
                font-size: 16px;
            }

            .badge-descuento {
                font-size: 10px;
                padding: 4px 8px;
            }

            .banner-inicio {
                padding: 25px 15px;
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

            .nav-links li a {
                display: block;
                padding: 8px;
                white-space: normal;
            }

            .barra-busqueda-principal form {
                flex-direction: column;
                gap: 8px;
            }

            .barra-busqueda-principal input[type="text"] {
                width: 100%;
                border-radius: 25px;
            }

            .barra-busqueda-principal button {
                width: 100%;
                border-radius: 25px;
                margin-left: 0;
            }

            .pagination li a,
            .pagination li span {
                min-width: 28px;
                height: 28px;
                font-size: 11px;
            }
            
            .badge-variacion {
                font-size: 9px;
                padding: 2px 5px;
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
                <li><a href="{{ route('inicio') }}">Inicio</a></li>
                <li><a href="{{ route('busqueda.resultados') }}">Todos los Productos</a></li>
                <li><a href="{{ route('busqueda.resultados', ['en_descuento' => '1']) }}" style="color: #dc3545; font-weight: bold;" id="link-descuento">🔥 En Descuento</a></li>
                <li>
                    @auth
                        <a href="{{ route('favoritos.index') }}" style="color: #495057; font-weight: bold;">❤️ Mis Favoritos</a>
                    @else
                        <a href="{{ route('login') }}" style="color: #495057; font-weight: bold;">❤️ Mis Favoritos</a>
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

        <!-- Barra de búsqueda -->
        <div class="barra-busqueda-principal">
            <form action="{{ route('busqueda.resultados') }}" method="GET" id="form-busqueda">
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

    <!-- Banner de bienvenida estilo ML -->
    <div class="banner-inicio">
        <h2>¡Bienvenido a Ecommerce Agave!</h2>
        <p>Descubre nuestra exclusiva selección de productos de agave y mezcal</p>
        <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
            <a href="{{ route('busqueda.resultados') }}" class="btn-banner">Explorar Productos</a>
            <a href="{{ route('busqueda.resultados', ['en_descuento' => '1']) }}" class="btn-banner descuento" id="banner-descuento">🔥 Ver Descuentos</a>
        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- SECCIÓN 1: PRODUCTOS EN DESCUENTO -->
    <!-- ===================================================================== -->
    @if(isset($productosDescuento) && $productosDescuento->count() > 0)
    <div class="seccion-destacados" style="margin-top: 30px;">
        <h2 class="titulo-seccion descuento">
            🔥 Productos con Descuento
        </h2>
        
        <div class="productos-grid">
            @foreach($productosDescuento as $item)
                @php
                    $esVariacion = isset($item->id_variacion);
                    
                    if ($esVariacion) {
                        // Es una variación
                        $tieneDescuento = $item->tieneDescuentoActivo();
                        $precioOriginal = $item->dPrecio;
                        $precioDescuento = $item->dPrecio_oferta;
                        $stock = $item->iStock;
                        $nombreProducto = $item->productoPadre->vNombre . ' - ' . $item->getAtributosTexto();
                        $imagenes = $item->imagenes;
                        $url = route('productos.show.public', [$item->productoPadre->id_producto, 'variacion' => $item->id_variacion]);
                        $productoId = $item->productoPadre->id_producto;
                        $variacionId = $item->id_variacion;
                        $esFavorito = $item->esFavorito();
                        $atributosTexto = $item->getAtributosTexto();
                        $marca = $item->productoPadre->marca->vNombre ?? 'Marca genérica';
                    } else {
                        // Es un producto
                        $tieneDescuento = $item->tieneDescuentoActivo();
                        $precioOriginal = $item->dPrecio_venta;
                        $precioDescuento = $item->dPrecio_oferta;
                        $stock = $item->iStock;
                        $nombreProducto = $item->vNombre;
                        $imagenes = $item->imagenes;
                        $url = route('productos.show.public', $item->id_producto);
                        $productoId = $item->id_producto;
                        $variacionId = null;
                        $esFavorito = $item->esFavorito();
                        $atributosTexto = '';
                        $marca = $item->marca->vNombre ?? 'Marca genérica';
                    }
                    
                    $precioActual = $tieneDescuento ? $precioDescuento : $precioOriginal;
                    $porcentajeDescuento = $tieneDescuento ? round((($precioOriginal - $precioDescuento) / $precioOriginal) * 100) : 0;
                    
                    $envioGratis = $precioActual >= 150;
                    $costoEnvio = 50;
                    
                    $estaBajoStock = $stock > 0 && $stock <= 10;
                @endphp
                
                <div class="producto-card" onclick="window.location.href='{{ $url }}'">
                    <div class="producto-imagen-container">
                        <!-- Botón de corazón para favoritos -->
                        <button class="corazon-favorito {{ $esFavorito ? 'activo' : 'inactivo' }}" 
                                data-producto="{{ $productoId }}"
                                data-variacion="{{ $variacionId ?? '' }}"
                                data-tipo="{{ $esVariacion ? 'variacion' : 'producto' }}"
                                onclick="event.stopPropagation(); toggleFavorito(this, {{ $productoId }}, {{ $variacionId ?? 'null' }})"
                                title="{{ $esFavorito ? 'Quitar de favoritos' : 'Agregar a favoritos' }}">
                            {{ $esFavorito ? '❤️' : '🤍' }}
                        </button>

                        <!-- Badge de descuento -->
                        @if($tieneDescuento)
                            <div class="badge-descuento">
                                -{{ $porcentajeDescuento }}% OFF
                            </div>
                        @elseif($estaBajoStock)
                            <div class="badge-stock-bajo">
                                ¡Últimas!
                            </div>
                        @endif

                        <!-- Badge de variación (si es variación) -->
                        @if($esVariacion && !empty($atributosTexto))
                            <div class="badge-variacion" title="{{ $atributosTexto }}">
                                {{ $atributosTexto }}
                            </div>
                        @endif

                        @if(count($imagenes) > 0)
                            <img src="{{ $imagenes[0] }}" alt="{{ $nombreProducto }}" class="producto-imagen">
                        @else
                            <div class="no-imagen">
                                <span>🛒 Sin imagen</span>
                            </div>
                        @endif
                    </div>
                    
                    <div class="producto-info">
                        <h3>{{ $nombreProducto }}</h3>

                        <!-- PRECIOS -->
                        <div class="precio-container">
                            @if($tieneDescuento)
                                <span class="precio-original">${{ number_format($precioOriginal, 2) }}</span>
                                <div style="display: flex; align-items: center; flex-wrap: wrap;">
                                    <span class="precio-actual">${{ number_format($precioActual, 2) }} <small>sin interés</small></span>
                                    <span class="descuento-badge">{{ $porcentajeDescuento }}% OFF</span>
                                </div>
                            @else
                                <span class="precio-actual">${{ number_format($precioActual, 2) }} <small>sin interés</small></span>
                            @endif
                        </div>

                        <!-- ENVÍO -->
                        <div class="envio-info">
                            @if($envioGratis)
                                <span class="envio-gratis">
                                    <span>🚚</span> Envío gratis
                                </span>
                            @else
                                <span class="envio-pago">
                                    <span>📦</span> + ${{ number_format($costoEnvio, 2) }} envío
                                </span>
                            @endif
                        </div>

                        <!-- Stock -->
                        <div class="stock-info {{ $stock > 10 ? 'stock-bueno' : ($stock > 0 ? 'stock-bajo' : 'sin-stock') }}">
                            @if($stock > 10)
                                ✅ En stock ({{ $stock }} disponibles)
                            @elseif($stock > 0)
                                ⚠️ Solo {{ $stock }} unidades
                            @else
                                ❌ Sin stock
                            @endif
                        </div>

                        <!-- Marca -->
                        <p style="font-size: 13px; color: #666; margin-bottom: 5px;">
                            {{ $marca }}
                        </p>

                        <div class="ver-detalle">
                            <a href="{{ $url }}" onclick="event.stopPropagation();">Ver más</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div style="text-align: center; margin-top: 20px; margin-bottom: 30px;">
            <a href="{{ route('busqueda.resultados', ['en_descuento' => '1']) }}" class="btn btn-descuento">Ver Todos los Descuentos</a>
        </div>
    </div>
    @endif

    <!-- ===================================================================== -->
    <!-- SECCIÓN 2: PRODUCTOS DESTACADOS (RECIENTES) -->
    <!-- ===================================================================== -->
    <div class="seccion-destacados">
        <h2 class="titulo-seccion">Productos Destacados</h2>
        
        @if(isset($productosDestacados) && $productosDestacados->count() > 0)
            <div class="productos-grid">
                @foreach($productosDestacados as $item)
                    @php
                        $esVariacion = isset($item->id_variacion);
                        
                        if ($esVariacion) {
                            // Es una variación
                            $tieneDescuento = $item->tieneDescuentoActivo();
                            $precioOriginal = $item->dPrecio;
                            $precioDescuento = $item->dPrecio_oferta;
                            $stock = $item->iStock;
                            $nombreProducto = $item->productoPadre->vNombre . ' - ' . $item->getAtributosTexto();
                            $imagenes = $item->imagenes;
                            $url = route('productos.show.public', [$item->productoPadre->id_producto, 'variacion' => $item->id_variacion]);
                            $productoId = $item->productoPadre->id_producto;
                            $variacionId = $item->id_variacion;
                            $esFavorito = $item->esFavorito();
                            $atributosTexto = $item->getAtributosTexto();
                            $marca = $item->productoPadre->marca->vNombre ?? 'Marca genérica';
                        } else {
                            // Es un producto
                            $tieneDescuento = $item->tieneDescuentoActivo();
                            $precioOriginal = $item->dPrecio_venta;
                            $precioDescuento = $item->dPrecio_oferta;
                            $stock = $item->iStock;
                            $nombreProducto = $item->vNombre;
                            $imagenes = $item->imagenes;
                            $url = route('productos.show.public', $item->id_producto);
                            $productoId = $item->id_producto;
                            $variacionId = null;
                            $esFavorito = $item->esFavorito();
                            $atributosTexto = '';
                            $marca = $item->marca->vNombre ?? 'Marca genérica';
                        }
                        
                        $precioActual = $tieneDescuento ? $precioDescuento : $precioOriginal;
                        $porcentajeDescuento = $tieneDescuento ? round((($precioOriginal - $precioDescuento) / $precioOriginal) * 100) : 0;
                        
                        $envioGratis = $precioActual >= 150;
                        $costoEnvio = 50;
                        
                        $estaBajoStock = $stock > 0 && $stock <= 10;
                    @endphp
                    
                    <div class="producto-card" onclick="window.location.href='{{ $url }}'">
                        <div class="producto-imagen-container">
                            <!-- Botón de corazón para favoritos -->
                            <button class="corazon-favorito {{ $esFavorito ? 'activo' : 'inactivo' }}" 
                                    data-producto="{{ $productoId }}"
                                    data-variacion="{{ $variacionId ?? '' }}"
                                    data-tipo="{{ $esVariacion ? 'variacion' : 'producto' }}"
                                    onclick="event.stopPropagation(); toggleFavorito(this, {{ $productoId }}, {{ $variacionId ?? 'null' }})"
                                    title="{{ $esFavorito ? 'Quitar de favoritos' : 'Agregar a favoritos' }}">
                                {{ $esFavorito ? '❤️' : '🤍' }}
                            </button>

                            @if($tieneDescuento)
                                <div class="badge-descuento">
                                    -{{ $porcentajeDescuento }}%
                                </div>
                            @elseif($estaBajoStock)
                                <div class="badge-stock-bajo">
                                    ¡Últimas!
                                </div>
                            @endif

                            <!-- Badge de variación (si es variación) -->
                            @if($esVariacion && !empty($atributosTexto))
                                <div class="badge-variacion" title="{{ $atributosTexto }}">
                                    {{ $atributosTexto }}
                                </div>
                            @endif

                            @if(count($imagenes) > 0)
                                <img src="{{ $imagenes[0] }}" alt="{{ $nombreProducto }}" class="producto-imagen">
                            @else
                                <div class="no-imagen">
                                    <span>🛒 Sin imagen</span>
                                </div>
                            @endif
                        </div>
                        
                        <div class="producto-info">
                            <h3>{{ $nombreProducto }}</h3>

                            <!-- PRECIOS -->
                            <div class="precio-container">
                                @if($tieneDescuento)
                                    <span class="precio-original">${{ number_format($precioOriginal, 2) }}</span>
                                    <div style="display: flex; align-items: center; flex-wrap: wrap;">
                                        <span class="precio-actual">${{ number_format($precioActual, 2) }} <small>sin interés</small></span>
                                        <span class="descuento-badge">{{ $porcentajeDescuento }}% OFF</span>
                                    </div>
                                @else
                                    <span class="precio-actual">${{ number_format($precioActual, 2) }} <small>sin interés</small></span>
                                @endif
                            </div>

                            <!-- ENVÍO -->
                            <div class="envio-info">
                                @if($envioGratis)
                                    <span class="envio-gratis">
                                        <span>🚚</span> Envío gratis
                                    </span>
                                @else
                                    <span class="envio-pago">
                                        <span>📦</span> + ${{ number_format($costoEnvio, 2) }} envío
                                    </span>
                                @endif
                            </div>

                            <!-- Stock -->
                            <div class="stock-info {{ $stock > 10 ? 'stock-bueno' : ($stock > 0 ? 'stock-bajo' : 'sin-stock') }}">
                                @if($stock > 10)
                                    ✅ En stock ({{ $stock }} disponibles)
                                @elseif($stock > 0)
                                    ⚠️ Solo {{ $stock }} unidades
                                @else
                                    ❌ Sin stock
                                @endif
                            </div>

                            <!-- Marca -->
                            <p style="font-size: 13px; color: #666; margin-bottom: 5px;">
                                {{ $marca }}
                            </p>

                            <div class="ver-detalle">
                                <a href="{{ $url }}" onclick="event.stopPropagation();">Ver más</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- ===================================================================== -->
        <!-- SECCIÓN 3: PRODUCTOS MÁS VENDIDOS -->
        <!-- ===================================================================== -->
        @if(isset($productosMasVendidos) && $productosMasVendidos->count() > 0)
        <h2 class="titulo-seccion" style="margin-top: 40px;">⭐ Más Vendidos</h2>
        
        <div class="productos-grid">
            @foreach($productosMasVendidos as $item)
                @php
                    $esVariacion = isset($item->id_variacion);
                    
                    if ($esVariacion) {
                        // Es una variación
                        $tieneDescuento = $item->tieneDescuentoActivo();
                        $precioOriginal = $item->dPrecio;
                        $precioDescuento = $item->dPrecio_oferta;
                        $stock = $item->iStock;
                        $nombreProducto = $item->productoPadre->vNombre . ' - ' . $item->getAtributosTexto();
                        $imagenes = $item->imagenes;
                        $url = route('productos.show.public', [$item->productoPadre->id_producto, 'variacion' => $item->id_variacion]);
                        $productoId = $item->productoPadre->id_producto;
                        $variacionId = $item->id_variacion;
                        $esFavorito = $item->esFavorito();
                        $atributosTexto = $item->getAtributosTexto();
                        $marca = $item->productoPadre->marca->vNombre ?? 'Marca genérica';
                    } else {
                        // Es un producto
                        $tieneDescuento = $item->tieneDescuentoActivo();
                        $precioOriginal = $item->dPrecio_venta;
                        $precioDescuento = $item->dPrecio_oferta;
                        $stock = $item->iStock;
                        $nombreProducto = $item->vNombre;
                        $imagenes = $item->imagenes;
                        $url = route('productos.show.public', $item->id_producto);
                        $productoId = $item->id_producto;
                        $variacionId = null;
                        $esFavorito = $item->esFavorito();
                        $atributosTexto = '';
                        $marca = $item->marca->vNombre ?? 'Marca genérica';
                    }
                    
                    $precioActual = $tieneDescuento ? $precioDescuento : $precioOriginal;
                    $porcentajeDescuento = $tieneDescuento ? round((($precioOriginal - $precioDescuento) / $precioOriginal) * 100) : 0;
                    
                    $envioGratis = $precioActual >= 150;
                    $costoEnvio = 50;
                @endphp
                
                <div class="producto-card" onclick="window.location.href='{{ $url }}'">
                    <div class="producto-imagen-container">
                        <!-- Badge de más vendido -->
                        <div class="badge-mas-vendido">
                            ⭐ Más vendido
                        </div>

                        <button class="corazon-favorito {{ $esFavorito ? 'activo' : 'inactivo' }}" 
                                data-producto="{{ $productoId }}"
                                data-variacion="{{ $variacionId ?? '' }}"
                                data-tipo="{{ $esVariacion ? 'variacion' : 'producto' }}"
                                onclick="event.stopPropagation(); toggleFavorito(this, {{ $productoId }}, {{ $variacionId ?? 'null' }})"
                                title="{{ $esFavorito ? 'Quitar de favoritos' : 'Agregar a favoritos' }}">
                            {{ $esFavorito ? '❤️' : '🤍' }}
                        </button>

                        @if($tieneDescuento)
                            <div class="badge-descuento">
                                -{{ $porcentajeDescuento }}%
                            </div>
                        @endif

                        @if($esVariacion && !empty($atributosTexto))
                            <div class="badge-variacion" title="{{ $atributosTexto }}">
                                {{ $atributosTexto }}
                            </div>
                        @endif

                        @if(count($imagenes) > 0)
                            <img src="{{ $imagenes[0] }}" alt="{{ $nombreProducto }}" class="producto-imagen">
                        @else
                            <div class="no-imagen">
                                <span>🛒 Sin imagen</span>
                            </div>
                        @endif
                    </div>
                    
                    <div class="producto-info">
                        <h3>{{ $nombreProducto }}</h3>

                        <div class="precio-container">
                            @if($tieneDescuento)
                                <span class="precio-original">${{ number_format($precioOriginal, 2) }}</span>
                                <div style="display: flex; align-items: center; flex-wrap: wrap;">
                                    <span class="precio-actual">${{ number_format($precioActual, 2) }} <small>sin interés</small></span>
                                    <span class="descuento-badge">{{ $porcentajeDescuento }}% OFF</span>
                                </div>
                            @else
                                <span class="precio-actual">${{ number_format($precioActual, 2) }} <small>sin interés</small></span>
                            @endif
                        </div>

                        <div class="envio-info">
                            @if($envioGratis)
                                <span class="envio-gratis"><span>🚚</span> Envío gratis</span>
                            @else
                                <span class="envio-pago"><span>📦</span> + ${{ number_format($costoEnvio, 2) }} envío</span>
                            @endif
                        </div>

                        <div class="stock-info {{ $stock > 10 ? 'stock-bueno' : ($stock > 0 ? 'stock-bajo' : 'sin-stock') }}">
                            @if($stock > 0)
                                ✅ Disponible ({{ $stock }} unidades)
                            @else
                                ❌ Sin stock
                            @endif
                        </div>

                        <div class="ver-detalle">
                            <a href="{{ $url }}" onclick="event.stopPropagation();">Ver más</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @endif

        <!-- ===================================================================== -->
        <!-- SECCIÓN 4: TODOS LOS PRODUCTOS CON PAGINACIÓN (NUEVA) -->
        <!-- ===================================================================== -->
        @if(isset($todosLosProductos) && $todosLosProductos->count() > 0)
        <h2 class="titulo-seccion" style="margin-top: 40px;">📦 Todos Nuestros Productos</h2>
        
        <div class="productos-grid">
            @foreach($todosLosProductos as $item)
                @php
                    $esVariacion = isset($item->id_variacion);
                    
                    if ($esVariacion) {
                        // Es una variación
                        $tieneDescuento = $item->tieneDescuentoActivo();
                        $precioOriginal = $item->dPrecio;
                        $precioDescuento = $item->dPrecio_oferta;
                        $stock = $item->iStock;
                        $nombreProducto = $item->productoPadre->vNombre . ' - ' . $item->getAtributosTexto();
                        $imagenes = $item->imagenes;
                        $url = route('productos.show.public', [$item->productoPadre->id_producto, 'variacion' => $item->id_variacion]);
                        $productoId = $item->productoPadre->id_producto;
                        $variacionId = $item->id_variacion;
                        $esFavorito = $item->esFavorito();
                        $atributosTexto = $item->getAtributosTexto();
                        $marca = $item->productoPadre->marca->vNombre ?? 'Marca genérica';
                    } else {
                        // Es un producto
                        $tieneDescuento = $item->tieneDescuentoActivo();
                        $precioOriginal = $item->dPrecio_venta;
                        $precioDescuento = $item->dPrecio_oferta;
                        $stock = $item->iStock;
                        $nombreProducto = $item->vNombre;
                        $imagenes = $item->imagenes;
                        $url = route('productos.show.public', $item->id_producto);
                        $productoId = $item->id_producto;
                        $variacionId = null;
                        $esFavorito = $item->esFavorito();
                        $atributosTexto = '';
                        $marca = $item->marca->vNombre ?? 'Marca genérica';
                    }
                    
                    $precioActual = $tieneDescuento ? $precioDescuento : $precioOriginal;
                    $porcentajeDescuento = $tieneDescuento ? round((($precioOriginal - $precioDescuento) / $precioOriginal) * 100) : 0;
                    
                    $envioGratis = $precioActual >= 150;
                    $costoEnvio = 50;
                    
                    $estaBajoStock = $stock > 0 && $stock <= 10;
                @endphp
                
                <div class="producto-card" onclick="window.location.href='{{ $url }}'">
                    <div class="producto-imagen-container">
                        <!-- Botón de corazón para favoritos -->
                        <button class="corazon-favorito {{ $esFavorito ? 'activo' : 'inactivo' }}" 
                                data-producto="{{ $productoId }}"
                                data-variacion="{{ $variacionId ?? '' }}"
                                data-tipo="{{ $esVariacion ? 'variacion' : 'producto' }}"
                                onclick="event.stopPropagation(); toggleFavorito(this, {{ $productoId }}, {{ $variacionId ?? 'null' }})"
                                title="{{ $esFavorito ? 'Quitar de favoritos' : 'Agregar a favoritos' }}">
                            {{ $esFavorito ? '❤️' : '🤍' }}
                        </button>

                        @if($tieneDescuento)
                            <div class="badge-descuento">
                                -{{ $porcentajeDescuento }}% OFF
                            </div>
                        @elseif($estaBajoStock)
                            <div class="badge-stock-bajo">
                                ¡Últimas!
                            </div>
                        @endif

                        <!-- Badge de variación (si es variación) -->
                        @if($esVariacion && !empty($atributosTexto))
                            <div class="badge-variacion" title="{{ $atributosTexto }}">
                                {{ $atributosTexto }}
                            </div>
                        @endif

                        @if(count($imagenes) > 0)
                            <img src="{{ $imagenes[0] }}" alt="{{ $nombreProducto }}" class="producto-imagen">
                        @else
                            <div class="no-imagen">
                                <span>🛒 Sin imagen</span>
                            </div>
                        @endif
                    </div>
                    
                    <div class="producto-info">
                        <h3>{{ $nombreProducto }}</h3>

                        <!-- PRECIOS -->
                        <div class="precio-container">
                            @if($tieneDescuento)
                                <span class="precio-original">${{ number_format($precioOriginal, 2) }}</span>
                                <div style="display: flex; align-items: center; flex-wrap: wrap;">
                                    <span class="precio-actual">${{ number_format($precioActual, 2) }} <small>sin interés</small></span>
                                    <span class="descuento-badge">{{ $porcentajeDescuento }}% OFF</span>
                                </div>
                            @else
                                <span class="precio-actual">${{ number_format($precioActual, 2) }} <small>sin interés</small></span>
                            @endif
                        </div>

                        <!-- ENVÍO -->
                        <div class="envio-info">
                            @if($envioGratis)
                                <span class="envio-gratis">
                                    <span>🚚</span> Envío gratis
                                </span>
                            @else
                                <span class="envio-pago">
                                    <span>📦</span> + ${{ number_format($costoEnvio, 2) }} envío
                                </span>
                            @endif
                        </div>

                        <!-- Stock -->
                        <div class="stock-info {{ $stock > 10 ? 'stock-bueno' : ($stock > 0 ? 'stock-bajo' : 'sin-stock') }}">
                            @if($stock > 10)
                                ✅ En stock ({{ $stock }} disponibles)
                            @elseif($stock > 0)
                                ⚠️ Solo {{ $stock }} unidades
                            @else
                                ❌ Sin stock
                            @endif
                        </div>

                        <!-- Marca -->
                        <p style="font-size: 13px; color: #666; margin-bottom: 5px;">
                            {{ $marca }}
                        </p>

                        <div class="ver-detalle">
                            <a href="{{ $url }}" onclick="event.stopPropagation();">Ver más</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- PAGINACIÓN PARA TODOS LOS PRODUCTOS -->
        <div class="paginacion">
            {{ $todosLosProductos->links() }}
        </div>

        <div style="text-align: center; margin-top: 20px; margin-bottom: 20px;">
            <a href="{{ route('busqueda.resultados') }}" class="btn">Ver Todos los Productos</a>
        </div>
        @endif
    </div>

    <script>
        // VARIABLE GLOBAL para controlar UNA sola notificación
        let singleToast = null;
        let singleToastTimeout = null;

        // Función para toggle favoritos en productos y variaciones
        function toggleFavorito(button, productoId, variacionId = null) {
            if (button.disabled) return;
            button.disabled = true;

            // Verificar si el usuario está autenticado
            @if(!Auth::check())
                window.location.href = '{{ route("login") }}?from_favoritos=true&redirect=' + encodeURIComponent(window.location.href);
                return;
            @endif

            const esFavorito = button.classList.contains('activo');
            const tipo = variacionId ? 'variación' : 'producto';
            
            // Animación simple
            button.style.transform = 'scale(0.9)';
            
            // Eliminar notificación anterior
            removeSingleToast();
            
            // Construir URL según el tipo
            let url;
            if (variacionId) {
                url = `/favoritos/toggle-variacion/${variacionId}`;
            } else {
                url = `/favoritos/toggle-producto/${productoId}`;
            }
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                cache: 'no-store'
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
                        
                        // Mensaje específico según el tipo
                        const mensaje = data.tipo === 'variacion' 
                            ? '✅ Variación agregada a favoritos' 
                            : '✅ Producto agregado a favoritos';
                        
                        showSingleNotification(mensaje, 3000);
                        
                        localStorage.setItem('last_favorito_action', 'added');
                        localStorage.setItem('last_favorito_id', data.tipo === 'variacion' ? variacionId : productoId);
                        localStorage.setItem('last_favorito_tipo', data.tipo);
                        localStorage.setItem('last_favorito_time', Date.now());
                        
                    } else {
                        // Cambiar a estado inactivo
                        button.classList.remove('activo');
                        button.classList.add('inactivo');
                        button.innerHTML = '🤍';
                        
                        // Mensaje específico según el tipo
                        const mensaje = data.tipo === 'variacion' 
                            ? '❌ Variación eliminada de favoritos' 
                            : '❌ Producto eliminado de favoritos';
                        
                        showSingleNotification(mensaje, 3000);
                        
                        localStorage.setItem('last_favorito_action', 'removed');
                        localStorage.setItem('last_favorito_id', data.tipo === 'variacion' ? variacionId : productoId);
                        localStorage.setItem('last_favorito_tipo', data.tipo);
                        localStorage.setItem('last_favorito_time', Date.now());
                    }
                } else {
                    showSingleNotification('❌ Error al gestionar favoritos', 3000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showSingleNotification('❌ Error de conexión', 3000);
            })
            .finally(() => {
                setTimeout(() => {
                    button.disabled = false;
                    button.style.transform = '';
                }, 500);
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
            const emoji = message.includes('✅') ? '✅' : message.includes('❌') ? '❌' : 'ℹ️';
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

        // Verificar acciones recientes al cargar
        document.addEventListener('DOMContentLoaded', function() {
            const lastAction = localStorage.getItem('last_favorito_action');
            const lastId = localStorage.getItem('last_favorito_id');
            const lastTipo = localStorage.getItem('last_favorito_tipo');
            const lastTime = localStorage.getItem('last_favorito_time');
            
            if (lastAction && (Date.now() - lastTime) < 5000) {
                // Buscar el botón correspondiente
                let selector;
                if (lastTipo === 'variacion') {
                    selector = `[data-variacion="${lastId}"]`;
                } else {
                    selector = `[data-producto="${lastId}"][data-variacion=""]`;
                }
                
                const button = document.querySelector(selector);
                if (button) {
                    if (lastAction === 'removed') {
                        button.classList.remove('activo');
                        button.classList.add('inactivo');
                        button.innerHTML = '🤍';
                    } else if (lastAction === 'added') {
                        button.classList.remove('inactivo');
                        button.classList.add('activo');
                        button.innerHTML = '❤️';
                    }
                }
            }
            
            // Limpiar después de 5 segundos
            setTimeout(() => {
                localStorage.removeItem('last_favorito_action');
                localStorage.removeItem('last_favorito_id');
                localStorage.removeItem('last_favorito_tipo');
                localStorage.removeItem('last_favorito_time');
            }, 5000);

            // Auto-focus en la barra de búsqueda (solo en desktop)
            if (window.innerWidth > 768) {
                const searchInput = document.querySelector('.barra-busqueda-principal input[type="text"]');
                if (searchInput) {
                    searchInput.focus();
                }
            }

            // Prevenir zoom en inputs para móviles
            const inputs = document.querySelectorAll('input[type="text"], input[type="number"], select');
            inputs.forEach(input => {
                input.addEventListener('touchstart', function() {
                    this.style.fontSize = '16px';
                });
            });
            
            // Prevenir propagación en botones
            const buttons = document.querySelectorAll('.producto-card button, .producto-card a');
            buttons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            });
            
            // Agregar event listener al link de descuento
            const linkDescuento = document.getElementById('link-descuento');
            if (linkDescuento) {
                linkDescuento.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = new URL(this.href);
                    window.location.href = url.toString();
                });
            }
            
            // Agregar event listener al banner de descuento
            const bannerDescuento = document.getElementById('banner-descuento');
            if (bannerDescuento) {
                bannerDescuento.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = new URL(this.href);
                    window.location.href = url.toString();
                });
            }
        });
    </script>
</body>
</html>