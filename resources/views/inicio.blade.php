<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Ecommerce Agave - Inicio</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 0;
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
            opacity: 0.9;
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
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
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
            transition: color 0.3s ease;
        }

        .nav-links li a:hover {
            color: #667eea;
            text-decoration: underline;
        }

        .nav-links li a.favorito-link {
            color: #495057;
        }

        .nav-links li a.favorito-link:hover {
            color: #667eea;
        }

        .nav-links li button {
            font-size: clamp(0.85rem, 2.5vw, 1rem);
            background: none;
            border: none;
            color: #495057;
            cursor: pointer;
            font-weight: bold;
        }

        .nav-links li button:hover {
            color: #667eea;
            text-decoration: underline;
        }

        /* Barra de búsqueda */
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
            border: 2px solid #667eea;
            border-radius: 25px 0 0 25px;
            font-size: 16px;
            outline: none;
            min-width: 0;
            transition: border-color 0.3s ease;
        }

        .barra-busqueda-principal input[type="text"]:focus {
            border-color: #764ba2;
        }

        .barra-busqueda-principal button {
            padding: 12px 25px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 0 25px 25px 0;
            font-size: 16px;
            cursor: pointer;
            white-space: nowrap;
            transition: transform 0.3s ease;
        }

        .barra-busqueda-principal button:hover {
            transform: translateY(-2px);
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

        /* PRECIOS */
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
            font-size: 12px;
            font-weight: 400;
            color: #666;
        }

        .descuento-badge {
            background: #00a650;
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            margin-left: 8px;
            display: inline-block;
        }

        .ahorro-info {
            background-color: #e8f5e9;
            border-radius: 4px;
            padding: 2px 6px;
            font-size: 11px;
            color: #2e7d32;
            display: inline-block;
            margin-top: 4px;
        }

        .ahorro-info i {
            font-size: 10px;
            margin-right: 2px;
        }

        .motivo-descuento {
            font-size: 10px;
            color: #666;
            margin-top: 2px;
            margin-bottom: 2px;
        }

        .motivo-descuento i {
            margin-right: 3px;
            color: #dc3545;
        }

        .periodo-descuento {
            font-size: 9px;
            color: #999;
            margin-bottom: 5px;
        }

        .periodo-descuento i {
            margin-right: 3px;
            color: #007bff;
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

        /* Botón agregar al carrito */
        .btn-agregar-carrito {
            width: 100%;
            padding: 8px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 25px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .btn-agregar-carrito:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(40, 167, 69, 0.3);
        }

        .btn-agregar-carrito:active {
            transform: translateY(0);
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

        .badge-descuento-rojo {
            position: absolute;
            top: 15px;
            right: 60px;
            background: #dc3545;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            z-index: 99;
            box-shadow: 0 2px 5px rgba(220,53,69,0.3);
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

        .badge-recomendado {
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
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            text-decoration: none;
            border-radius: 25px;
            border: none;
            cursor: pointer;
            font-size: clamp(0.9rem, 3vw, 1rem);
            font-weight: bold;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 2px 5px rgba(102,126,234,0.3);
            margin: 0 15px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(102,126,234,0.4);
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
            background: white;
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
            transform: scale(1.1);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.25);
        }

        .corazon-favorito:active {
            transform: scale(0.95);
        }

        .corazon-favorito.activo {
            color: #ff4757;
            background: #fff0f0;
            border-color: #ff4757;
        }

        .corazon-favorito.inactivo {
            color: #ccc;
        }

        .corazon-favorito.loading {
            opacity: 0.7;
            pointer-events: none;
            position: relative;
            animation: pulse 1.5s infinite;
        }

        .corazon-favorito.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #ff4757;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(0.95); }
        }

        /* NOTIFICACIÓN ÚNICA */
        .toast-single {
            position: fixed;
            top: 20px;
            right: 20px;
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
            transform: translateX(120%);
            opacity: 0;
            border-left: 5px solid transparent;
        }

        .toast-single.show {
            transform: translateX(0);
            opacity: 1;
        }

        .toast-single.error {
            background: linear-gradient(135deg, #f56565, #e53e3e);
        }

        .toast-single.success {
            background: linear-gradient(135deg, #48bb78, #38a169);
        }

        .toast-single.info {
            background: linear-gradient(135deg, #4299e1, #3182ce);
        }

        .toast-icon {
            font-size: 24px;
            line-height: 1;
        }

        .toast-message {
            flex: 1;
            line-height: 1.4;
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

        /* Secciones */
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
            background: linear-gradient(135deg, #667eea, #764ba2);
            margin: 10px auto 0;
            border-radius: 3px;
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

        /* Paginación */
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
            color: #667eea;
        }

        .pagination li.active span {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            font-weight: 600;
        }

        .pagination li.disabled span {
            color: #adb5bd;
            cursor: not-allowed;
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

            .badge-descuento,
            .badge-descuento-rojo {
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
                <li><a href="{{ route('busqueda.resultados', ['en_descuento' => '1']) }}" style="color: #dc3545; font-weight: bold;" id="link-descuento">🔥 En Descuento</a></li>
                <li>
                    @auth
                        <a href="{{ route('favoritos.index') }}" class="favorito-link">❤️ Mis Favoritos</a>
                    @else
                        <a href="{{ route('favoritos.invitado.index') }}" class="favorito-link">❤️ Mis Favoritos</a>
                    @endauth
                </li>
                @auth
                    <li><a href="#">Mi Carrito</a></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                            @csrf
                            <button type="submit">Cerrar Sesión</button>
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

    <!-- Mostrar mensajes -->
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
        <h2>¡Bienvenido a Ecommerce Agave!</h2>
        <p>Descubre nuestra exclusiva selección de productos de agave y mezcal</p>
        <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
            <a href="{{ route('busqueda.resultados') }}" class="btn-banner">Explorar Productos</a>
            <a href="{{ route('busqueda.resultados', ['en_descuento' => '1']) }}" class="btn-banner descuento" id="banner-descuento">🔥 Ver Descuentos</a>
        </div>
    </div>

    <!-- SECCIÓN 1: PRODUCTOS EN DESCUENTO -->
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
                        $tieneDescuento = $item->tieneDescuentoActivo();
                        $precioOriginal = $item->dPrecio;
                        $precioDescuento = $item->dPrecio_descuento;
                        $stock = $item->iStock;
                        $nombreProducto = $item->productoPadre->vNombre;
                        $nombreCompleto = $nombreProducto . ' - ' . $item->getAtributosTexto();
                        $imagen = $item->primera_imagen;
                        $url = route('productos.show.public', [$item->productoPadre->id_producto, 'variacion' => $item->id_variacion]);
                        $productoId = $item->productoPadre->id_producto;
                        $variacionId = $item->id_variacion;
                        $esFavorito = $item->esFavorito();
                        $atributosTexto = $item->getAtributosTexto();
                        $atributosCompletos = $item->getAtributosCompletosTexto();
                        $marca = $item->productoPadre->marca->vNombre ?? 'Marca genérica';
                        $sku = $item->vSKU;
                        $motivoDescuento = $item->vMotivo_descuento ?? '';
                        $fechaInicio = $item->dFecha_inicio_descuento ? \Carbon\Carbon::parse($item->dFecha_inicio_descuento)->format('d/m') : '';
                        $fechaFin = $item->dFecha_fin_descuento ? \Carbon\Carbon::parse($item->dFecha_fin_descuento)->format('d/m') : '';
                        $porcentajeImpuesto = $item->porcentaje_impuesto ?? 0;
                    } else {
                        $tieneDescuento = $item->tieneDescuentoActivo();
                        $precioOriginal = $item->dPrecio_venta;
                        $precioDescuento = $item->dPrecio_descuento;
                        $stock = $item->iStock;
                        $nombreProducto = $item->vNombre;
                        $nombreCompleto = $nombreProducto;
                        $imagen = $item->primera_imagen;
                        $url = route('productos.show.public', $item->id_producto);
                        $productoId = $item->id_producto;
                        $variacionId = null;
                        $esFavorito = $item->esFavorito();
                        $atributosTexto = '';
                        $atributosCompletos = '';
                        $marca = $item->marca->vNombre ?? 'Marca genérica';
                        $sku = $item->vCodigo_barras;
                        $motivoDescuento = $item->vMotivo_descuento ?? '';
                        $fechaInicio = $item->dFecha_inicio_descuento ? \Carbon\Carbon::parse($item->dFecha_inicio_descuento)->format('d/m') : '';
                        $fechaFin = $item->dFecha_fin_descuento ? \Carbon\Carbon::parse($item->dFecha_fin_descuento)->format('d/m') : '';
                        $porcentajeImpuesto = $item->porcentaje_impuestos ?? 0;
                    }
                    
                    // PRECIO BASE (con descuento si aplica)
                    $precioBase = $tieneDescuento ? $precioDescuento : $precioOriginal;
                    
                    // PRECIO FINAL (con impuesto)
                    $precioFinal = $precioBase + ($precioBase * $porcentajeImpuesto / 100);
                    
                    // PRECIO ORIGINAL CON IMPUESTO (para mostrar el ahorro)
                    $precioOriginalConImpuesto = $precioOriginal + ($precioOriginal * $porcentajeImpuesto / 100);
                    
                    // Porcentaje de descuento real
                    $porcentajeDescuento = $tieneDescuento ? round((($precioOriginal - $precioDescuento) / $precioOriginal) * 100) : 0;
                    $ahorroTotal = $precioOriginalConImpuesto - $precioFinal;
                    
                    $envioGratis = $precioFinal >= 150;
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

                        @if($tieneDescuento && $porcentajeDescuento > 0)
                            <div class="badge-descuento-rojo" title="{{ $motivoDescuento ?: 'Descuento especial' }}">
                                -{{ $porcentajeDescuento }}% OFF
                                @if($motivoDescuento)
                                    <br><small style="font-size: 8px;">{{ Str::limit($motivoDescuento, 15) }}</small>
                                @endif
                            </div>
                        @elseif($estaBajoStock)
                            <div class="badge-stock-bajo">
                                ¡Últimas!
                            </div>
                        @endif

                        @if($esVariacion && !empty($atributosTexto))
                            <div class="badge-variacion" title="{{ $atributosCompletos }}">
                                {{ $atributosTexto }}
                            </div>
                        @endif

                        @if($imagen)
                            <img src="{{ $imagen }}" alt="{{ $nombreProducto }}" class="producto-imagen" loading="lazy">
                        @else
                            <div class="no-imagen">
                                <span>🛒 Sin imagen</span>
                            </div>
                        @endif
                    </div>
                    
                    <div class="producto-info">
                        <h3 title="{{ $nombreCompleto }}">{{ Str::limit($nombreCompleto, 50) }}</h3>

                        <div class="precio-container">
                            @if($tieneDescuento && $porcentajeDescuento > 0)
                                <span class="precio-original">${{ number_format($precioOriginalConImpuesto, 2) }}</span>
                                <div style="display: flex; align-items: center; flex-wrap: wrap;">
                                    <span class="precio-actual">${{ number_format($precioFinal, 2) }} <small>sin interés</small></span>
                                    <span class="descuento-badge">{{ $porcentajeDescuento }}% OFF</span>
                                </div>
                                @if($ahorroTotal > 0)
                                    <div class="ahorro-info">
                                        <i class="fas fa-piggy-bank"></i> Ahorras ${{ number_format($ahorroTotal, 2) }}
                                    </div>
                                @endif
                                @if($motivoDescuento && $esVariacion)
                                    <div class="motivo-descuento" title="{{ $motivoDescuento }}">
                                        <i class="fas fa-tag"></i> {{ Str::limit($motivoDescuento, 25) }}
                                    </div>
                                @endif
                                @if($fechaInicio && $fechaFin)
                                    <div class="periodo-descuento">
                                        <i class="fas fa-calendar-alt"></i> {{ $fechaInicio }} - {{ $fechaFin }}
                                    </div>
                                @endif
                            @else
                                <span class="precio-actual">${{ number_format($precioFinal, 2) }} <small>sin interés</small></span>
                            @endif
                        </div>

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

                        <div class="stock-info {{ $stock > 10 ? 'stock-bueno' : ($stock > 0 ? 'stock-bajo' : 'sin-stock') }}">
                            @if($stock > 10)
                                ✅ En stock ({{ $stock }} disponibles)
                            @elseif($stock > 0)
                                ⚠️ Solo {{ $stock }} unidades
                            @else
                                ❌ Sin stock
                            @endif
                        </div>

                        <!-- Agregar al carrito -->
                        <div class="agregar-carrito-container" style="margin: 10px 0;">
                            <button type="button" 
                                    class="btn-agregar-carrito" 
                                    onclick="event.stopPropagation(); agregarAlCarrito({{ $productoId }}, {{ $variacionId ?? 'null' }})">
                                <i class="fas fa-shopping-cart"></i> Agregar al carrito
                            </button>
                        </div>

                        <p style="font-size: 13px; color: #666; margin-bottom: 5px;">
                            {{ $marca }} | SKU: {{ $sku }}
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

    <!-- SECCIÓN 2: PRODUCTOS DESTACADOS -->
    <div class="seccion-destacados">
        <h2 class="titulo-seccion">Productos Destacados</h2>
        
        @if(isset($productosDestacados) && $productosDestacados->count() > 0)
            <div class="productos-grid">
                @foreach($productosDestacados as $item)
                    @php
                        $esVariacion = isset($item->id_variacion);
                        
                        if ($esVariacion) {
                            $tieneDescuento = $item->tieneDescuentoActivo();
                            $precioOriginal = $item->dPrecio;
                            $precioDescuento = $item->dPrecio_descuento;
                            $stock = $item->iStock;
                            $nombreProducto = $item->productoPadre->vNombre;
                            $nombreCompleto = $nombreProducto . ' - ' . $item->getAtributosTexto();
                            $imagen = $item->primera_imagen;
                            $url = route('productos.show.public', [$item->productoPadre->id_producto, 'variacion' => $item->id_variacion]);
                            $productoId = $item->productoPadre->id_producto;
                            $variacionId = $item->id_variacion;
                            $esFavorito = $item->esFavorito();
                            $atributosTexto = $item->getAtributosTexto();
                            $atributosCompletos = $item->getAtributosCompletosTexto();
                            $marca = $item->productoPadre->marca->vNombre ?? 'Marca genérica';
                            $sku = $item->vSKU;
                            $motivoDescuento = $item->vMotivo_descuento ?? '';
                            $fechaInicio = $item->dFecha_inicio_descuento ? \Carbon\Carbon::parse($item->dFecha_inicio_descuento)->format('d/m') : '';
                            $fechaFin = $item->dFecha_fin_descuento ? \Carbon\Carbon::parse($item->dFecha_fin_descuento)->format('d/m') : '';
                            $porcentajeImpuesto = $item->porcentaje_impuesto ?? 0;
                        } else {
                            $tieneDescuento = $item->tieneDescuentoActivo();
                            $precioOriginal = $item->dPrecio_venta;
                            $precioDescuento = $item->dPrecio_descuento;
                            $stock = $item->iStock;
                            $nombreProducto = $item->vNombre;
                            $nombreCompleto = $nombreProducto;
                            $imagen = $item->primera_imagen;
                            $url = route('productos.show.public', $item->id_producto);
                            $productoId = $item->id_producto;
                            $variacionId = null;
                            $esFavorito = $item->esFavorito();
                            $atributosTexto = '';
                            $atributosCompletos = '';
                            $marca = $item->marca->vNombre ?? 'Marca genérica';
                            $sku = $item->vCodigo_barras;
                            $motivoDescuento = $item->vMotivo_descuento ?? '';
                            $fechaInicio = $item->dFecha_inicio_descuento ? \Carbon\Carbon::parse($item->dFecha_inicio_descuento)->format('d/m') : '';
                            $fechaFin = $item->dFecha_fin_descuento ? \Carbon\Carbon::parse($item->dFecha_fin_descuento)->format('d/m') : '';
                            $porcentajeImpuesto = $item->porcentaje_impuestos ?? 0;
                        }
                        
                        // PRECIO BASE (con descuento si aplica)
                        $precioBase = $tieneDescuento ? $precioDescuento : $precioOriginal;
                        
                        // PRECIO FINAL (con impuesto)
                        $precioFinal = $precioBase + ($precioBase * $porcentajeImpuesto / 100);
                        
                        // PRECIO ORIGINAL CON IMPUESTO (para mostrar el ahorro)
                        $precioOriginalConImpuesto = $precioOriginal + ($precioOriginal * $porcentajeImpuesto / 100);
                        
                        $porcentajeDescuento = $tieneDescuento ? round((($precioOriginal - $precioDescuento) / $precioOriginal) * 100) : 0;
                        $ahorroTotal = $precioOriginalConImpuesto - $precioFinal;
                        
                        $envioGratis = $precioFinal >= 150;
                        $costoEnvio = 50;
                        
                        $estaBajoStock = $stock > 0 && $stock <= 10;
                    @endphp
                    
                    <div class="producto-card" onclick="window.location.href='{{ $url }}'">
                        <div class="producto-imagen-container">
                            <button class="corazon-favorito {{ $esFavorito ? 'activo' : 'inactivo' }}" 
                                    data-producto="{{ $productoId }}"
                                    data-variacion="{{ $variacionId ?? '' }}"
                                    data-tipo="{{ $esVariacion ? 'variacion' : 'producto' }}"
                                    onclick="event.stopPropagation(); toggleFavorito(this, {{ $productoId }}, {{ $variacionId ?? 'null' }})"
                                    title="{{ $esFavorito ? 'Quitar de favoritos' : 'Agregar a favoritos' }}">
                                {{ $esFavorito ? '❤️' : '🤍' }}
                            </button>

                            @if($tieneDescuento && $porcentajeDescuento > 0)
                                <div class="badge-descuento-rojo" title="{{ $motivoDescuento ?: 'Descuento especial' }}">
                                    -{{ $porcentajeDescuento }}%
                                    @if($motivoDescuento)
                                        <br><small style="font-size: 8px;">{{ Str::limit($motivoDescuento, 15) }}</small>
                                    @endif
                                </div>
                            @elseif($estaBajoStock)
                                <div class="badge-stock-bajo">
                                    ¡Últimas!
                                </div>
                            @endif

                            @if($esVariacion && !empty($atributosTexto))
                                <div class="badge-variacion" title="{{ $atributosCompletos }}">
                                    {{ $atributosTexto }}
                                </div>
                            @endif

                            @if($imagen)
                                <img src="{{ $imagen }}" alt="{{ $nombreProducto }}" class="producto-imagen" loading="lazy">
                            @else
                                <div class="no-imagen">
                                    <span>🛒 Sin imagen</span>
                                </div>
                            @endif
                        </div>
                        
                        <div class="producto-info">
                            <h3 title="{{ $nombreCompleto }}">{{ Str::limit($nombreCompleto, 50) }}</h3>

                            <div class="precio-container">
                                @if($tieneDescuento && $porcentajeDescuento > 0)
                                    <span class="precio-original">${{ number_format($precioOriginalConImpuesto, 2) }}</span>
                                    <div style="display: flex; align-items: center; flex-wrap: wrap;">
                                        <span class="precio-actual">${{ number_format($precioFinal, 2) }} <small>sin interés</small></span>
                                        <span class="descuento-badge">{{ $porcentajeDescuento }}% OFF</span>
                                    </div>
                                    @if($ahorroTotal > 0)
                                        <div class="ahorro-info">
                                            <i class="fas fa-piggy-bank"></i> Ahorras ${{ number_format($ahorroTotal, 2) }}
                                        </div>
                                    @endif
                                    @if($motivoDescuento && $esVariacion)
                                        <div class="motivo-descuento" title="{{ $motivoDescuento }}">
                                            <i class="fas fa-tag"></i> {{ Str::limit($motivoDescuento, 25) }}
                                        </div>
                                    @endif
                                    @if($fechaInicio && $fechaFin)
                                        <div class="periodo-descuento">
                                            <i class="fas fa-calendar-alt"></i> {{ $fechaInicio }} - {{ $fechaFin }}
                                        </div>
                                    @endif
                                @else
                                    <span class="precio-actual">${{ number_format($precioFinal, 2) }} <small>sin interés</small></span>
                                @endif
                            </div>

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

                            <div class="stock-info {{ $stock > 10 ? 'stock-bueno' : ($stock > 0 ? 'stock-bajo' : 'sin-stock') }}">
                                @if($stock > 10)
                                    ✅ En stock ({{ $stock }} disponibles)
                                @elseif($stock > 0)
                                    ⚠️ Solo {{ $stock }} unidades
                                @else
                                    ❌ Sin stock
                                @endif
                            </div>

                            <!-- Agregar al carrito -->
                            <div class="agregar-carrito-container" style="margin: 10px 0;">
                                <button type="button" 
                                        class="btn-agregar-carrito" 
                                        onclick="event.stopPropagation(); agregarAlCarrito({{ $productoId }}, {{ $variacionId ?? 'null' }})">
                                    <i class="fas fa-shopping-cart"></i> Agregar al carrito
                                </button>
                            </div>

                            <p style="font-size: 13px; color: #666; margin-bottom: 5px;">
                                {{ $marca }} | SKU: {{ $sku }}
                            </p>

                            <div class="ver-detalle">
                                <a href="{{ $url }}" onclick="event.stopPropagation();">Ver más</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- SECCIÓN 3: PRODUCTOS RECOMENDADOS -->
        @if(isset($productosRecomendados) && $productosRecomendados->count() > 0)
        <h2 class="titulo-seccion" style="margin-top: 40px;">✨ Productos Recomendados</h2>
        
        <div class="productos-grid">
            @foreach($productosRecomendados as $item)
                @php
                    $esVariacion = isset($item->id_variacion);
                    
                    if ($esVariacion) {
                        $tieneDescuento = $item->tieneDescuentoActivo();
                        $precioOriginal = $item->dPrecio;
                        $precioDescuento = $item->dPrecio_descuento;
                        $stock = $item->iStock;
                        $nombreProducto = $item->productoPadre->vNombre;
                        $nombreCompleto = $nombreProducto . ' - ' . $item->getAtributosTexto();
                        $imagen = $item->primera_imagen;
                        $url = route('productos.show.public', [$item->productoPadre->id_producto, 'variacion' => $item->id_variacion]);
                        $productoId = $item->productoPadre->id_producto;
                        $variacionId = $item->id_variacion;
                        $esFavorito = $item->esFavorito();
                        $atributosTexto = $item->getAtributosTexto();
                        $atributosCompletos = $item->getAtributosCompletosTexto();
                        $marca = $item->productoPadre->marca->vNombre ?? 'Marca genérica';
                        $sku = $item->vSKU;
                        $motivoDescuento = $item->vMotivo_descuento ?? '';
                        $fechaInicio = $item->dFecha_inicio_descuento ? \Carbon\Carbon::parse($item->dFecha_inicio_descuento)->format('d/m') : '';
                        $fechaFin = $item->dFecha_fin_descuento ? \Carbon\Carbon::parse($item->dFecha_fin_descuento)->format('d/m') : '';
                        $porcentajeImpuesto = $item->porcentaje_impuesto ?? 0;
                    } else {
                        $tieneDescuento = $item->tieneDescuentoActivo();
                        $precioOriginal = $item->dPrecio_venta;
                        $precioDescuento = $item->dPrecio_descuento;
                        $stock = $item->iStock;
                        $nombreProducto = $item->vNombre;
                        $nombreCompleto = $nombreProducto;
                        $imagen = $item->primera_imagen;
                        $url = route('productos.show.public', $item->id_producto);
                        $productoId = $item->id_producto;
                        $variacionId = null;
                        $esFavorito = $item->esFavorito();
                        $atributosTexto = '';
                        $atributosCompletos = '';
                        $marca = $item->marca->vNombre ?? 'Marca genérica';
                        $sku = $item->vCodigo_barras;
                        $motivoDescuento = $item->vMotivo_descuento ?? '';
                        $fechaInicio = $item->dFecha_inicio_descuento ? \Carbon\Carbon::parse($item->dFecha_inicio_descuento)->format('d/m') : '';
                        $fechaFin = $item->dFecha_fin_descuento ? \Carbon\Carbon::parse($item->dFecha_fin_descuento)->format('d/m') : '';
                        $porcentajeImpuesto = $item->porcentaje_impuestos ?? 0;
                    }
                    
                    // PRECIO BASE (con descuento si aplica)
                    $precioBase = $tieneDescuento ? $precioDescuento : $precioOriginal;
                    
                    // PRECIO FINAL (con impuesto)
                    $precioFinal = $precioBase + ($precioBase * $porcentajeImpuesto / 100);
                    
                    // PRECIO ORIGINAL CON IMPUESTO
                    $precioOriginalConImpuesto = $precioOriginal + ($precioOriginal * $porcentajeImpuesto / 100);
                    
                    $porcentajeDescuento = $tieneDescuento ? round((($precioOriginal - $precioDescuento) / $precioOriginal) * 100) : 0;
                    $ahorroTotal = $precioOriginalConImpuesto - $precioFinal;
                    
                    $envioGratis = $precioFinal >= 150;
                    $costoEnvio = 50;
                @endphp
                
                <div class="producto-card" onclick="window.location.href='{{ $url }}'">
                    <div class="producto-imagen-container">
                        <div class="badge-recomendado">
                            ✨ Recomendado
                        </div>

                        <button class="corazon-favorito {{ $esFavorito ? 'activo' : 'inactivo' }}" 
                                data-producto="{{ $productoId }}"
                                data-variacion="{{ $variacionId ?? '' }}"
                                data-tipo="{{ $esVariacion ? 'variacion' : 'producto' }}"
                                onclick="event.stopPropagation(); toggleFavorito(this, {{ $productoId }}, {{ $variacionId ?? 'null' }})"
                                title="{{ $esFavorito ? 'Quitar de favoritos' : 'Agregar a favoritos' }}">
                            {{ $esFavorito ? '❤️' : '🤍' }}
                        </button>

                        @if($tieneDescuento && $porcentajeDescuento > 0)
                            <div class="badge-descuento-rojo" title="{{ $motivoDescuento ?: 'Descuento especial' }}">
                                -{{ $porcentajeDescuento }}%
                            </div>
                        @endif

                        @if($esVariacion && !empty($atributosTexto))
                            <div class="badge-variacion" title="{{ $atributosCompletos }}">
                                {{ $atributosTexto }}
                            </div>
                        @endif

                        @if($imagen)
                            <img src="{{ $imagen }}" alt="{{ $nombreProducto }}" class="producto-imagen" loading="lazy">
                        @else
                            <div class="no-imagen">
                                <span>🛒 Sin imagen</span>
                            </div>
                        @endif
                    </div>
                    
                    <div class="producto-info">
                        <h3 title="{{ $nombreCompleto }}">{{ Str::limit($nombreCompleto, 50) }}</h3>

                        <div class="precio-container">
                            @if($tieneDescuento && $porcentajeDescuento > 0)
                                <span class="precio-original">${{ number_format($precioOriginalConImpuesto, 2) }}</span>
                                <div style="display: flex; align-items: center; flex-wrap: wrap;">
                                    <span class="precio-actual">${{ number_format($precioFinal, 2) }} <small>sin interés</small></span>
                                    <span class="descuento-badge">{{ $porcentajeDescuento }}% OFF</span>
                                </div>
                                @if($ahorroTotal > 0)
                                    <div class="ahorro-info">
                                        <i class="fas fa-piggy-bank"></i> Ahorras ${{ number_format($ahorroTotal, 2) }}
                                    </div>
                                @endif
                                @if($motivoDescuento && $esVariacion)
                                    <div class="motivo-descuento" title="{{ $motivoDescuento }}">
                                        <i class="fas fa-tag"></i> {{ Str::limit($motivoDescuento, 25) }}
                                    </div>
                                @endif
                                @if($fechaInicio && $fechaFin)
                                    <div class="periodo-descuento">
                                        <i class="fas fa-calendar-alt"></i> {{ $fechaInicio }} - {{ $fechaFin }}
                                    </div>
                                @endif
                            @else
                                <span class="precio-actual">${{ number_format($precioFinal, 2) }} <small>sin interés</small></span>
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

                        <!-- Agregar al carrito -->
                        <div class="agregar-carrito-container" style="margin: 10px 0;">
                            <button type="button" 
                                    class="btn-agregar-carrito" 
                                    onclick="event.stopPropagation(); agregarAlCarrito({{ $productoId }}, {{ $variacionId ?? 'null' }})">
                                <i class="fas fa-shopping-cart"></i> Agregar al carrito
                            </button>
                        </div>

                        <p style="font-size: 13px; color: #666; margin-bottom: 5px;">
                            {{ $marca }} | SKU: {{ $sku }}
                        </p>

                        <div class="ver-detalle">
                            <a href="{{ $url }}" onclick="event.stopPropagation();">Ver más</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @endif

        <!-- SECCIÓN 4: TODOS LOS PRODUCTOS Y VARIACIONES -->
        @if(isset($todosLosItems) && $todosLosItems->count() > 0)
        <h2 class="titulo-seccion" style="margin-top: 40px;">📦 Todos Nuestros Productos</h2>
        
        <div class="productos-grid">
            @foreach($todosLosItems as $item)
                @php
                    $esVariacion = isset($item->id_variacion);
                    
                    if ($esVariacion) {
                        $tieneDescuento = $item->tieneDescuentoActivo();
                        $precioOriginal = $item->dPrecio;
                        $precioDescuento = $item->dPrecio_descuento;
                        $stock = $item->iStock;
                        $nombreProducto = $item->productoPadre->vNombre;
                        $nombreCompleto = $nombreProducto . ' - ' . $item->getAtributosTexto();
                        $imagen = $item->primera_imagen;
                        $url = route('productos.show.public', [$item->productoPadre->id_producto, 'variacion' => $item->id_variacion]);
                        $productoId = $item->productoPadre->id_producto;
                        $variacionId = $item->id_variacion;
                        $esFavorito = $item->esFavorito();
                        $atributosTexto = $item->getAtributosTexto();
                        $atributosCompletos = $item->getAtributosCompletosTexto();
                        $marca = $item->productoPadre->marca->vNombre ?? 'Marca genérica';
                        $sku = $item->vSKU;
                        $motivoDescuento = $item->vMotivo_descuento ?? '';
                        $fechaInicio = $item->dFecha_inicio_descuento ? \Carbon\Carbon::parse($item->dFecha_inicio_descuento)->format('d/m') : '';
                        $fechaFin = $item->dFecha_fin_descuento ? \Carbon\Carbon::parse($item->dFecha_fin_descuento)->format('d/m') : '';
                        $porcentajeImpuesto = $item->porcentaje_impuesto ?? 0;
                    } else {
                        $tieneDescuento = $item->tieneDescuentoActivo();
                        $precioOriginal = $item->dPrecio_venta;
                        $precioDescuento = $item->dPrecio_descuento;
                        $stock = $item->iStock;
                        $nombreProducto = $item->vNombre;
                        $nombreCompleto = $nombreProducto;
                        $imagen = $item->primera_imagen;
                        $url = route('productos.show.public', $item->id_producto);
                        $productoId = $item->id_producto;
                        $variacionId = null;
                        $esFavorito = $item->esFavorito();
                        $atributosTexto = '';
                        $atributosCompletos = '';
                        $marca = $item->marca->vNombre ?? 'Marca genérica';
                        $sku = $item->vCodigo_barras;
                        $motivoDescuento = $item->vMotivo_descuento ?? '';
                        $fechaInicio = $item->dFecha_inicio_descuento ? \Carbon\Carbon::parse($item->dFecha_inicio_descuento)->format('d/m') : '';
                        $fechaFin = $item->dFecha_fin_descuento ? \Carbon\Carbon::parse($item->dFecha_fin_descuento)->format('d/m') : '';
                        $porcentajeImpuesto = $item->porcentaje_impuestos ?? 0;
                    }
                    
                    // PRECIO BASE (con descuento si aplica)
                    $precioBase = $tieneDescuento ? $precioDescuento : $precioOriginal;
                    
                    // PRECIO FINAL (con impuesto)
                    $precioFinal = $precioBase + ($precioBase * $porcentajeImpuesto / 100);
                    
                    // PRECIO ORIGINAL CON IMPUESTO
                    $precioOriginalConImpuesto = $precioOriginal + ($precioOriginal * $porcentajeImpuesto / 100);
                    
                    $porcentajeDescuento = $tieneDescuento ? round((($precioOriginal - $precioDescuento) / $precioOriginal) * 100) : 0;
                    $ahorroTotal = $precioOriginalConImpuesto - $precioFinal;
                    
                    $envioGratis = $precioFinal >= 150;
                    $costoEnvio = 50;
                    
                    $estaBajoStock = $stock > 0 && $stock <= 10;
                @endphp
                
                <div class="producto-card" onclick="window.location.href='{{ $url }}'">
                    <div class="producto-imagen-container">
                        <button class="corazon-favorito {{ $esFavorito ? 'activo' : 'inactivo' }}" 
                                data-producto="{{ $productoId }}"
                                data-variacion="{{ $variacionId ?? '' }}"
                                data-tipo="{{ $esVariacion ? 'variacion' : 'producto' }}"
                                onclick="event.stopPropagation(); toggleFavorito(this, {{ $productoId }}, {{ $variacionId ?? 'null' }})"
                                title="{{ $esFavorito ? 'Quitar de favoritos' : 'Agregar a favoritos' }}">
                            {{ $esFavorito ? '❤️' : '🤍' }}
                        </button>

                        @if($tieneDescuento && $porcentajeDescuento > 0)
                            <div class="badge-descuento-rojo" title="{{ $motivoDescuento ?: 'Descuento especial' }}">
                                -{{ $porcentajeDescuento }}% OFF
                                @if($motivoDescuento)
                                    <br><small style="font-size: 8px;">{{ Str::limit($motivoDescuento, 15) }}</small>
                                @endif
                            </div>
                        @elseif($estaBajoStock)
                            <div class="badge-stock-bajo">
                                ¡Últimas!
                            </div>
                        @endif

                        @if($esVariacion && !empty($atributosTexto))
                            <div class="badge-variacion" title="{{ $atributosCompletos }}">
                                {{ $atributosTexto }}
                            </div>
                        @endif

                        @if($imagen)
                            <img src="{{ $imagen }}" alt="{{ $nombreProducto }}" class="producto-imagen" loading="lazy">
                        @else
                            <div class="no-imagen">
                                <span>🛒 Sin imagen</span>
                            </div>
                        @endif
                    </div>
                    
                    <div class="producto-info">
                        <h3 title="{{ $nombreCompleto }}">{{ Str::limit($nombreCompleto, 50) }}</h3>

                        <div class="precio-container">
                            @if($tieneDescuento && $porcentajeDescuento > 0)
                                <span class="precio-original">${{ number_format($precioOriginalConImpuesto, 2) }}</span>
                                <div style="display: flex; align-items: center; flex-wrap: wrap;">
                                    <span class="precio-actual">${{ number_format($precioFinal, 2) }} <small>sin interés</small></span>
                                    <span class="descuento-badge">{{ $porcentajeDescuento }}% OFF</span>
                                </div>
                                @if($ahorroTotal > 0)
                                    <div class="ahorro-info">
                                        <i class="fas fa-piggy-bank"></i> Ahorras ${{ number_format($ahorroTotal, 2) }}
                                    </div>
                                @endif
                                @if($motivoDescuento && $esVariacion)
                                    <div class="motivo-descuento" title="{{ $motivoDescuento }}">
                                        <i class="fas fa-tag"></i> {{ Str::limit($motivoDescuento, 25) }}
                                    </div>
                                @endif
                                @if($fechaInicio && $fechaFin)
                                    <div class="periodo-descuento">
                                        <i class="fas fa-calendar-alt"></i> {{ $fechaInicio }} - {{ $fechaFin }}
                                    </div>
                                @endif
                            @else
                                <span class="precio-actual">${{ number_format($precioFinal, 2) }} <small>sin interés</small></span>
                            @endif
                        </div>

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

                        <div class="stock-info {{ $stock > 10 ? 'stock-bueno' : ($stock > 0 ? 'stock-bajo' : 'sin-stock') }}">
                            @if($stock > 10)
                                ✅ En stock ({{ $stock }} disponibles)
                            @elseif($stock > 0)
                                ⚠️ Solo {{ $stock }} unidades
                            @else
                                ❌ Sin stock
                            @endif
                        </div>

                        <!-- Agregar al carrito -->
                        <div class="agregar-carrito-container" style="margin: 10px 0;">
                            <button type="button" 
                                    class="btn-agregar-carrito" 
                                    onclick="event.stopPropagation(); agregarAlCarrito({{ $productoId }}, {{ $variacionId ?? 'null' }})">
                                <i class="fas fa-shopping-cart"></i> Agregar al carrito
                            </button>
                        </div>

                        <p style="font-size: 13px; color: #666; margin-bottom: 5px;">
                            {{ $marca }} | SKU: {{ $sku }}
                        </p>

                        <div class="ver-detalle">
                            <a href="{{ $url }}" onclick="event.stopPropagation();">Ver más</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- PAGINACIÓN -->
        <div class="paginacion">
            {{ $todosLosItems->links() }}
        </div>

        <div style="text-align: center; margin-top: 20px; margin-bottom: 20px;">
            <a href="{{ route('busqueda.resultados') }}" class="btn">Ver Todos los Productos</a>
        </div>
        @endif
    </div>

    <script>
        // Configurar CSRF token para todas las peticiones fetch
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        let currentToast = null;
        let toastTimeout = null;

        function removeNotification() {
            if (currentToast) {
                currentToast.classList.remove('show');
                setTimeout(() => {
                    if (currentToast && currentToast.parentNode) {
                        currentToast.parentNode.removeChild(currentToast);
                    }
                    currentToast = null;
                }, 300);
            }
            if (toastTimeout) {
                clearTimeout(toastTimeout);
                toastTimeout = null;
            }
        }

        function showNotification(message, type = 'success') {
            removeNotification();
            
            const toast = document.createElement('div');
            toast.className = `toast-single ${type}`;
            
            let icon = '✅';
            if (type === 'error') icon = '❌';
            if (type === 'info') icon = 'ℹ️';
            
            const cleanMessage = message.replace('✅', '').replace('❌', '').trim();
            
            toast.innerHTML = `
                <span class="toast-icon">${icon}</span>
                <span class="toast-message">${cleanMessage}</span>
            `;
            
            document.body.appendChild(toast);
            currentToast = toast;
            
            setTimeout(() => toast.classList.add('show'), 10);
            
            toastTimeout = setTimeout(() => {
                if (toast.classList.contains('show')) {
                    toast.classList.remove('show');
                    setTimeout(() => {
                        if (toast.parentNode) {
                            toast.parentNode.removeChild(toast);
                        }
                        if (currentToast === toast) {
                            currentToast = null;
                        }
                        toastTimeout = null;
                    }, 400);
                }
            }, 3000);
        }

        function toggleFavorito(button, productoId, variacionId = null) {
            if (button.disabled) return;
            
            const estabaActivo = button.classList.contains('activo');
            
            button.disabled = true;
            button.classList.add('loading');
            button.innerHTML = '⏳';
            
            @auth
                // Usuario autenticado - ruta directa
                const url = variacionId 
                    ? `/favoritos/toggle-variacion/${variacionId}`
                    : `/favoritos/toggle-producto/${productoId}`;
                    
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(async response => {
                    if (!response.ok) {
                        if (response.status === 401) {
                            const redirectUrl = new URL('{{ route("login") }}');
                            redirectUrl.searchParams.set('from_favoritos', 'true');
                            redirectUrl.searchParams.set('redirect', window.location.href);
                            redirectUrl.searchParams.set('producto', productoId);
                            if (variacionId) {
                                redirectUrl.searchParams.set('variacion', variacionId);
                            }
                            window.location.href = redirectUrl.toString();
                            return null;
                        }
                        const errorData = await response.json().catch(() => ({}));
                        throw new Error(errorData.message || `HTTP ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data) return;
                    
                    if (data.success) {
                        if (data.action === 'added') {
                            button.classList.remove('inactivo');
                            button.classList.add('activo');
                            button.innerHTML = '❤️';
                            button.setAttribute('title', 'Quitar de favoritos');
                            
                            let tipoTexto = data.tipo === 'variacion' ? 'Variación' : 'Producto';
                            showNotification(`✅ ${tipoTexto} agregado a favoritos`, 'success');
                            
                            localStorage.setItem('last_favorito_action', 'added');
                            localStorage.setItem('last_favorito_id', variacionId || productoId);
                            localStorage.setItem('last_favorito_tipo', data.tipo);
                            localStorage.setItem('last_favorito_time', Date.now());
                        } else {
                            button.classList.remove('activo');
                            button.classList.add('inactivo');
                            button.innerHTML = '🤍';
                            button.setAttribute('title', 'Agregar a favoritos');
                            
                            let tipoTexto = data.tipo === 'variacion' ? 'Variación' : 'Producto';
                            showNotification(`❌ ${tipoTexto} eliminado de favoritos`, 'error');
                            
                            localStorage.setItem('last_favorito_action', 'removed');
                            localStorage.setItem('last_favorito_id', variacionId || productoId);
                            localStorage.setItem('last_favorito_tipo', data.tipo);
                            localStorage.setItem('last_favorito_time', Date.now());
                        }
                    } else {
                        revertirEstadoFavorito(button, estabaActivo);
                        showNotification('❌ ' + (data.message || 'Error al gestionar favoritos'), 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    revertirEstadoFavorito(button, estabaActivo);
                    showNotification('❌ ' + error.message, 'error');
                })
                .finally(() => {
                    setTimeout(() => {
                        button.disabled = false;
                        button.classList.remove('loading');
                    }, 500);
                });
            @else
                // Invitado - verificar estado actual primero
                const checkUrl = variacionId 
                    ? `/favoritos-invitado/check/${productoId}/${variacionId}`
                    : `/favoritos-invitado/check/${productoId}`;
                
                fetch(checkUrl, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error al verificar estado');
                    }
                    return response.json();
                })
                .then(checkData => {
                    if (checkData.success) {
                        const toggleUrl = variacionId 
                            ? `/favoritos-invitado/toggle-variacion/${variacionId}`
                            : `/favoritos-invitado/toggle-producto/${productoId}`;
                        
                        return fetch(toggleUrl, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        });
                    } else {
                        throw new Error('Error al verificar estado');
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw new Error(err.message || `HTTP ${response.status}`); });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        if (data.action === 'added') {
                            button.classList.remove('inactivo');
                            button.classList.add('activo');
                            button.innerHTML = '❤️';
                            button.setAttribute('title', 'Quitar de favoritos');
                            
                            let tipoTexto = data.tipo === 'variacion' ? 'Variación' : 'Producto';
                            showNotification(`✅ ${tipoTexto} agregado a favoritos`, 'success');
                            
                            localStorage.setItem('last_favorito_action', 'added');
                            localStorage.setItem('last_favorito_id', variacionId || productoId);
                            localStorage.setItem('last_favorito_tipo', data.tipo);
                            localStorage.setItem('last_favorito_time', Date.now());
                        } else {
                            button.classList.remove('activo');
                            button.classList.add('inactivo');
                            button.innerHTML = '🤍';
                            button.setAttribute('title', 'Agregar a favoritos');
                            
                            let tipoTexto = data.tipo === 'variacion' ? 'Variación' : 'Producto';
                            showNotification(`❌ ${tipoTexto} eliminado de favoritos`, 'error');
                            
                            localStorage.setItem('last_favorito_action', 'removed');
                            localStorage.setItem('last_favorito_id', variacionId || productoId);
                            localStorage.setItem('last_favorito_tipo', data.tipo);
                            localStorage.setItem('last_favorito_time', Date.now());
                        }
                    } else {
                        revertirEstadoFavorito(button, estabaActivo);
                        showNotification('❌ ' + (data.message || 'Error al gestionar favoritos'), 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    revertirEstadoFavorito(button, estabaActivo);
                    showNotification('❌ ' + error.message, 'error');
                })
                .finally(() => {
                    setTimeout(() => {
                        button.disabled = false;
                        button.classList.remove('loading');
                    }, 500);
                });
            @endauth
        }

        function revertirEstadoFavorito(button, estabaActivo) {
            if (estabaActivo) {
                button.classList.add('activo');
                button.classList.remove('inactivo');
                button.innerHTML = '❤️';
            } else {
                button.classList.remove('activo');
                button.classList.add('inactivo');
                button.innerHTML = '🤍';
            }
        }

        function agregarAlCarrito(productoId, variacionId = null) {
            @auth
                fetch('/carrito/agregar', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        producto_id: productoId,
                        variacion_id: variacionId,
                        cantidad: 1
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('✅ Producto agregado al carrito', 'success');
                    } else {
                        showNotification('❌ ' + (data.message || 'Error al agregar'), 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('❌ Error de conexión', 'error');
                });
            @else
                const redirectUrl = new URL('{{ route("login") }}');
                redirectUrl.searchParams.set('from_carrito', 'true');
                redirectUrl.searchParams.set('redirect', window.location.href);
                window.location.href = redirectUrl.toString();
            @endauth
        }

        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('.producto-card button, .producto-card a');
            buttons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            });

            const linkDescuento = document.getElementById('link-descuento');
            if (linkDescuento) {
                linkDescuento.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.location.href = this.href;
                });
            }

            const bannerDescuento = document.getElementById('banner-descuento');
            if (bannerDescuento) {
                bannerDescuento.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.location.href = this.href;
                });
            }

            if (window.innerWidth > 768) {
                const searchInput = document.querySelector('.barra-busqueda-principal input[type="text"]');
                if (searchInput) searchInput.focus();
            }

            const lastAction = localStorage.getItem('last_favorito_action');
            const lastId = localStorage.getItem('last_favorito_id');
            const lastTipo = localStorage.getItem('last_favorito_tipo');
            const lastTime = localStorage.getItem('last_favorito_time');
            
            if (lastAction && lastId && lastTime && (Date.now() - lastTime) < 5000) {
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
                        button.setAttribute('title', 'Agregar a favoritos');
                    } else if (lastAction === 'added') {
                        button.classList.remove('inactivo');
                        button.classList.add('activo');
                        button.innerHTML = '❤️';
                        button.setAttribute('title', 'Quitar de favoritos');
                    }
                }
            }
            
            setTimeout(() => {
                localStorage.removeItem('last_favorito_action');
                localStorage.removeItem('last_favorito_id');
                localStorage.removeItem('last_favorito_tipo');
                localStorage.removeItem('last_favorito_time');
            }, 5000);
        });
    </script>
</body>
</html>