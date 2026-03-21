<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados de Búsqueda - Ecommerce Agave</title>
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
        }

        nav.navbar {
            background-color: #e9ecef;
            padding: 10px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        nav.navbar ul {
            list-style: none;
            display: flex;
            justify-content: center;
            gap: 25px;
            padding: 0;
            margin: 0;
            flex-wrap: wrap;
        }

        nav.navbar ul li {
            display: inline;
        }

        nav.navbar ul li a {
            color: #495057;
            text-decoration: none;
            font-weight: bold;
            font-size: clamp(0.85rem, 2.5vw, 1rem);
            transition: color 0.3s ease;
        }

        nav.navbar ul li a:hover {
            color: #667eea;
            text-decoration: underline;
        }

        nav.navbar ul li a.favorito-link {
            color: #495057;
        }

        nav.navbar ul li a.favorito-link:hover {
            color: #667eea;
        }

        nav.navbar ul li button {
            font-size: clamp(0.85rem, 2.5vw, 1rem);
            background: none;
            border: none;
            color: #495057;
            cursor: pointer;
            font-weight: bold;
        }

        nav.navbar ul li button.logout-btn {
            color: #495057;
        }

        nav.navbar ul li button.logout-btn:hover {
            color: #667eea;
            text-decoration: underline;
        }

        .barra-busqueda-principal {
            text-align: center;
            margin: 15px 0;
            padding: 0 20px;
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

        .busqueda-container {
            display: flex;
            gap: 20px;
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }
        
        .filtros-sidebar {
            width: 280px;
            flex-shrink: 0;
        }
        
        .resultados-main {
            flex: 1;
        }
        
        .filtro-grupo {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .filtro-titulo {
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
            font-size: 16px;
        }
        
        .filtro-opcion {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }
        
        .filtro-opcion input {
            margin-right: 8px;
        }
        
        .filtro-opcion label {
            cursor: pointer;
            flex: 1;
        }
        
        .precio-inputs {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }
        
        .precio-inputs input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .btn-precio {
            width: 100%;
            padding: 8px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .btn-precio:hover {
            background: #218838;
        }
        
        .busqueda-superior {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px;
            background: #fff;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .resultados-info {
            color: #666;
            font-size: 14px;
        }
        
        .ordenamiento select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .productos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .paginacion {
            display: flex;
            justify-content: center;
            margin: 30px 0 20px;
        }

        .pagination {
            display: flex;
            list-style: none;
            gap: 8px;
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
            min-width: 40px;
            height: 40px;
            padding: 0 8px;
            border-radius: 50%;
            text-decoration: none;
            color: #495057;
            font-size: 14px;
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

        .pagination li:first-child a::before {
            content: "←";
            font-size: 18px;
        }

        .pagination li:last-child a::before {
            content: "→";
            font-size: 18px;
        }

        .pagination li:first-child a span,
        .pagination li:last-child a span {
            display: none;
        }

        .pagination li.disabled span {
            color: #adb5bd;
            cursor: not-allowed;
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
            min-height: 250px;
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
            font-size: 16px;
            line-height: 1.3;
            height: 40px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            word-break: break-word;
        }

        /* PRECIOS */
        .producto-precio {
            margin-bottom: 5px;
        }

        .precio-original {
            text-decoration: line-through;
            color: #6c757d;
            font-size: 14px;
            font-weight: normal;
            margin-right: 8px;
        }

        .precio-actual {
            font-weight: bold;
            color: #28a745;
            font-size: 18px;
        }

        .badge-descuento {
            display: inline-block;
            padding: 4px 8px;
            font-size: 11px;
            background-color: #dc3545;
            color: white;
            border-radius: 4px;
            margin-left: 8px;
            font-weight: bold;
        }

        .motivo-descuento {
            font-size: 10px;
            color: #666;
            margin-top: 2px;
            margin-bottom: 5px;
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

        .envio-info {
            font-size: 13px;
            margin-bottom: 8px;
        }

        .envio-gratis {
            color: #00a650;
        }

        .envio-pago {
            color: #ff6b00;
        }

        .stock-info {
            margin-bottom: 5px;
            font-size: 14px;
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

        .badge-descuento-imagen {
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

        .badge-descuento-rojo {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #dc3545;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            z-index: 99;
            box-shadow: 0 2px 5px rgba(220,53,69,0.3);
        }

        .ver-detalle {
            margin-top: 10px;
            text-align: center;
        }

        .ver-detalle a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
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
            font-size: 16px;
            font-weight: bold;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 2px 5px rgba(102,126,234,0.3);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(102,126,234,0.4);
        }

        .btn-secondary {
            background: #6c757d;
            box-shadow: 0 2px 5px rgba(108,117,125,0.3);
        }

        .btn-secondary:hover {
            background: #5a6268;
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

        /* Corazón de favoritos */
        .corazon-favorito {
            position: absolute;
            top: 15px;
            right: 15px;
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

        /* Toast notifications */
        .toast {
            position: fixed;
            top: 30px;
            right: 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 25px;
            border-radius: 10px;
            z-index: 10000;
            font-size: 15px;
            font-weight: 600;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            display: flex;
            align-items: center;
            gap: 12px;
            max-width: 350px;
            transform: translateX(120%);
            opacity: 0;
            border-left: 5px solid transparent;
        }

        .toast.show {
            transform: translateX(0);
            opacity: 1;
        }

        .toast.error {
            background: linear-gradient(135deg, #f56565, #e53e3e);
        }

        .toast.success {
            background: linear-gradient(135deg, #48bb78, #38a169);
        }

        .toast.info {
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

        .etiquetas-container {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-top: 8px;
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

        /* Responsive */
        @media (max-width: 768px) {
            .busqueda-container {
                flex-direction: column;
            }
            
            .filtros-sidebar {
                width: 100%;
            }
            
            .busqueda-superior {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .productos-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }

            .barra-busqueda-principal input[type="text"] {
                font-size: 14px;
                padding: 10px 15px;
            }

            .barra-busqueda-principal button {
                padding: 10px 18px;
                font-size: 14px;
            }

            .pagination {
                gap: 5px;
            }
            
            .pagination li a,
            .pagination li span {
                min-width: 36px;
                height: 36px;
                font-size: 13px;
            }

            nav.navbar ul {
                gap: 15px;
                padding: 0 15px;
            }
        }

        @media (max-width: 480px) {
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
            
            .productos-grid {
                grid-template-columns: 1fr;
            }
            
            .pagination li a,
            .pagination li span {
                min-width: 32px;
                height: 32px;
                font-size: 12px;
            }

            nav.navbar ul {
                flex-direction: column;
                align-items: center;
                gap: 10px;
            }

            nav.navbar ul li {
                width: 100%;
                text-align: center;
            }

            nav.navbar ul li a {
                display: block;
                padding: 5px;
            }
            
            .badge-variacion {
                font-size: 9px;
                padding: 2px 6px;
            }

            .corazon-favorito {
                width: 32px;
                height: 32px;
                font-size: 16px;
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
        <p>¡Hola {{ Auth::user()->vNombre }}! 👋 Resultados de búsqueda</p>
    </div>
    @endauth

    <nav class="navbar">
        <ul>
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
                        <button type="submit" class="logout-btn">Cerrar Sesión</button>
                    </form>
                </li>
            @else
                <li><a href="{{ route('login') }}">Ingresar</a></li>
                <li><a href="{{ route('usuarios.create') }}">Crear Cuenta</a></li>
            @endauth
        </ul>

        <!-- Barra de búsqueda -->
        <div class="barra-busqueda-principal">
            <form action="{{ route('busqueda.resultados') }}" method="GET" id="form-busqueda">
                <input type="text" name="q" placeholder="Buscar productos (agave, mezcal, espadín...)" 
                       value="{{ request('q') }}" autocomplete="off">
                <button type="submit">Buscar</button>
            </form>
        </div>
    </nav>

    <div class="busqueda-container">
        <!-- Sidebar de Filtros -->
        <aside class="filtros-sidebar">
            <h3 style="margin-bottom: 15px; color: #333;">Filtros de Búsqueda</h3>
            <form id="filtrosForm" method="GET" action="{{ route('busqueda.resultados') }}">
                <input type="hidden" name="q" value="{{ request('q') }}">
                
                <!-- Filtro de Descuentos -->
                <div class="filtro-grupo">
                    <div class="filtro-titulo">Descuentos</div>
                    <div class="filtro-opcion">
                        <input type="checkbox" name="en_descuento" value="1" 
                               id="en_descuento"
                               {{ request('en_descuento') == '1' ? 'checked' : '' }}
                               onchange="document.getElementById('filtrosForm').submit()">
                        <label for="en_descuento">🔥 Solo productos en descuento</label>
                    </div>
                </div>
                
                <!-- Filtro de Categorías -->
                <div class="filtro-grupo">
                    <div class="filtro-titulo">Categorías</div>
                    @foreach($categorias as $categoria)
                    <div class="filtro-opcion">
                        <input type="checkbox" name="categorias[]" value="{{ $categoria->id_categoria }}" 
                               id="cat_{{ $categoria->id_categoria }}"
                               {{ is_array(request('categorias')) && in_array($categoria->id_categoria, request('categorias')) ? 'checked' : '' }}
                               onchange="document.getElementById('filtrosForm').submit()">
                        <label for="cat_{{ $categoria->id_categoria }}">{{ $categoria->vNombre }}</label>
                    </div>
                    @endforeach
                </div>

                <!-- Filtro de Marcas -->
                <div class="filtro-grupo">
                    <div class="filtro-titulo">Marcas</div>
                    @foreach($marcas as $marca)
                    <div class="filtro-opcion">
                        <input type="checkbox" name="marcas[]" value="{{ $marca->id_marca }}"
                               id="marca_{{ $marca->id_marca }}"
                               {{ is_array(request('marcas')) && in_array($marca->id_marca, request('marcas')) ? 'checked' : '' }}
                               onchange="document.getElementById('filtrosForm').submit()">
                        <label for="marca_{{ $marca->id_marca }}">{{ $marca->vNombre }}</label>
                    </div>
                    @endforeach
                </div>

                <!-- Filtro de Etiquetas -->
                <div class="filtro-grupo">
                    <div class="filtro-titulo">Etiquetas</div>
                    @foreach($etiquetas as $etiqueta)
                    <div class="filtro-opcion">
                        <input type="checkbox" name="etiquetas[]" value="{{ $etiqueta->id_etiqueta }}"
                               id="etiqueta_{{ $etiqueta->id_etiqueta }}"
                               {{ is_array(request('etiquetas')) && in_array($etiqueta->id_etiqueta, request('etiquetas')) ? 'checked' : '' }}
                               onchange="document.getElementById('filtrosForm').submit()">
                        <label for="etiqueta_{{ $etiqueta->id_etiqueta }}">{{ $etiqueta->vNombre }}</label>
                    </div>
                    @endforeach
                </div>

                <!-- Filtro de Precio -->
                <div class="filtro-grupo">
                    <div class="filtro-titulo">Rango de Precio</div>
                    <div class="precio-inputs">
                        <input type="number" name="precio_min" placeholder="Mín $" 
                               value="{{ request('precio_min') }}" min="0" step="0.01"
                               id="precio_min">
                        <input type="number" name="precio_max" placeholder="Máx $" 
                               value="{{ request('precio_max') }}" min="0" step="0.01"
                               id="precio_max">
                    </div>
                    <button type="button" onclick="aplicarFiltroPrecio()" class="btn-precio">
                        Aplicar Precio
                    </button>
                </div>

                <!-- Filtro de Stock -->
                <div class="filtro-grupo">
                    <div class="filtro-opcion">
                        <input type="checkbox" name="con_stock" value="1" id="con_stock"
                               {{ request('con_stock') == '1' ? 'checked' : '' }}
                               onchange="document.getElementById('filtrosForm').submit()">
                        <label for="con_stock">Solo productos con stock</label>
                    </div>
                </div>

                <button type="button" onclick="limpiarFiltros()" class="btn btn-secondary" style="width: 100%;">
                    Limpiar Filtros
                </button>
            </form>
        </aside>

        <!-- Área Principal de Resultados -->
        <main class="resultados-main">
            <!-- Información y ordenamiento -->
            <div class="busqueda-superior">
                <div class="resultados-info">
                    <strong>{{ $productos->total() }}</strong> resultado(s) encontrado(s)
                    @if(request('q'))
                        para "<strong>{{ request('q') }}</strong>"
                    @endif
                    @if(request('en_descuento') == '1')
                        <span style="color: #dc3545; font-weight: bold;">🔥 en descuento</span>
                    @endif
                </div>
                
                <div class="ordenamiento">
                    <form method="GET" id="ordenForm">
                        <input type="hidden" name="q" value="{{ request('q') }}">
                        <input type="hidden" name="precio_min" value="{{ request('precio_min') }}">
                        <input type="hidden" name="precio_max" value="{{ request('precio_max') }}">
                        <input type="hidden" name="con_stock" value="{{ request('con_stock') }}">
                        <input type="hidden" name="en_descuento" value="{{ request('en_descuento') }}">
                        
                        @if(is_array(request('categorias')))
                            @foreach(request('categorias') as $categoria_id)
                                <input type="hidden" name="categorias[]" value="{{ $categoria_id }}">
                            @endforeach
                        @endif
                        
                        @if(is_array(request('marcas')))
                            @foreach(request('marcas') as $marca_id)
                                <input type="hidden" name="marcas[]" value="{{ $marca_id }}">
                            @endforeach
                        @endif
                        
                        @if(is_array(request('etiquetas')))
                            @foreach(request('etiquetas') as $etiqueta_id)
                                <input type="hidden" name="etiquetas[]" value="{{ $etiqueta_id }}">
                            @endforeach
                        @endif
                        
                        <select name="orden" onchange="document.getElementById('ordenForm').submit()">
                            <option value="nombre" {{ request('orden') == 'nombre' ? 'selected' : '' }}>Ordenar por nombre</option>
                            <option value="precio_asc" {{ request('orden') == 'precio_asc' ? 'selected' : '' }}>Precio: menor a mayor</option>
                            <option value="precio_desc" {{ request('orden') == 'precio_desc' ? 'selected' : '' }}>Precio: mayor a menor</option>
                            <option value="recientes" {{ request('orden') == 'recientes' ? 'selected' : '' }}>Más recientes</option>
                            <option value="descuento_mayor" {{ request('orden') == 'descuento_mayor' ? 'selected' : '' }}>Mayor descuento</option>
                        </select>
                    </form>
                </div>
            </div>

            <!-- Resultados -->
            @if($productos->count() > 0)
                <div class="productos-grid">
                    @foreach($productos as $producto)
                        @php
                            // Verificar si es un producto padre o una variación
                            $esVariacion = isset($producto->id_variacion);
                            
                            // Determinar si tiene descuento activo
                            if ($esVariacion) {
                                $tieneDescuento = $producto->tieneDescuentoActivo();
                                $precioOriginal = $producto->dPrecio;
                                $precioDescuento = $producto->dPrecio_descuento;
                                $stock = $producto->iStock;
                                $nombreProducto = $producto->productoPadre->vNombre;
                                $nombreCompleto = $nombreProducto . ' - ' . $producto->getAtributosTexto();
                                $imagen = $producto->primera_imagen;
                                $categoria = $producto->productoPadre->categoria->vNombre ?? 'N/A';
                                $marca = $producto->productoPadre->marca->vNombre ?? 'N/A';
                                $etiquetas = $producto->productoPadre->etiquetas;
                                $url = route('productos.show.public', [$producto->productoPadre->id_producto, 'variacion' => $producto->id_variacion]);
                                $sku = $producto->vSKU;
                                $esFavorito = $producto->esFavorito();
                                $productoId = $producto->productoPadre->id_producto;
                                $variacionId = $producto->id_variacion;
                                $atributosTexto = $producto->getAtributosCompletosTexto();
                                $atributosCorto = $producto->getAtributosTexto();
                                $motivoDescuento = $producto->vMotivo_descuento ?? '';
                                $fechaInicio = $producto->dFecha_inicio_descuento ? \Carbon\Carbon::parse($producto->dFecha_inicio_descuento)->format('d/m') : '';
                                $fechaFin = $producto->dFecha_fin_descuento ? \Carbon\Carbon::parse($producto->dFecha_fin_descuento)->format('d/m') : '';
                                $porcentajeImpuesto = $producto->porcentaje_impuesto ?? 0;
                            } else {
                                $tieneDescuento = $producto->tieneDescuentoActivo();
                                $precioOriginal = $producto->dPrecio_venta;
                                $precioDescuento = $producto->dPrecio_descuento;
                                $stock = $producto->iStock;
                                $nombreProducto = $producto->vNombre;
                                $nombreCompleto = $nombreProducto;
                                $imagen = $producto->primera_imagen;
                                $categoria = $producto->categoria->vNombre ?? 'N/A';
                                $marca = $producto->marca->vNombre ?? 'N/A';
                                $etiquetas = $producto->etiquetas;
                                $url = route('productos.show.public', $producto->id_producto);
                                $sku = $producto->vCodigo_barras;
                                $esFavorito = $producto->esFavorito();
                                $productoId = $producto->id_producto;
                                $variacionId = null;
                                $atributosTexto = '';
                                $atributosCorto = '';
                                $motivoDescuento = $producto->vMotivo_descuento ?? '';
                                $fechaInicio = $producto->dFecha_inicio_descuento ? \Carbon\Carbon::parse($producto->dFecha_inicio_descuento)->format('d/m') : '';
                                $fechaFin = $producto->dFecha_fin_descuento ? \Carbon\Carbon::parse($producto->dFecha_fin_descuento)->format('d/m') : '';
                                $porcentajeImpuesto = $producto->porcentaje_impuestos ?? 0;
                            }
                            
                            $precioBase = $tieneDescuento ? $precioDescuento : $precioOriginal;
                            $precioFinal = $precioBase + ($precioBase * $porcentajeImpuesto / 100);
                            $porcentajeDescuento = $tieneDescuento ? round((($precioOriginal - $precioDescuento) / $precioOriginal) * 100) : 0;
                            
                            $estaBajoStock = $stock > 0 && $stock <= 10;
                            
                            // Lógica de envío
                            $envioGratis = $precioFinal >= config('tienda.envio_gratis_desde');
                            $costoEnvio = config('tienda.costo_de_envio');
                        @endphp
                        
                        <div class="producto-card" onclick="window.location.href='{{ $url }}'">
                            <div class="producto-imagen-container">
                                <!-- BOTÓN DEL CORAZÓN -->
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
                                @elseif($estaBajoStock && !$tieneDescuento)
                                    <div class="badge-stock-bajo">
                                        ¡Últimas!
                                    </div>
                                @endif

                                <!-- Badge de variación (solo si es variación) -->
                                @if($esVariacion && !empty($atributosCorto))
                                    <div class="badge-variacion" title="{{ $atributosTexto }}">
                                        {{ $atributosCorto }}
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
                                
                                <!-- Precio con descuento -->
                                <div class="producto-precio">
                                    @if($tieneDescuento && $porcentajeDescuento > 0)
                                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 5px; flex-wrap: wrap;">
                                            <span class="precio-original">
                                                ${{ number_format($precioOriginal, 2) }}
                                            </span>
                                            <span class="precio-actual">
                                                ${{ number_format($precioFinal, 2) }}
                                            </span>
                                            <span class="badge-descuento">
                                                -{{ $porcentajeDescuento }}%
                                            </span>
                                        </div>
                                        
                                        @if($motivoDescuento)
                                            <div class="motivo-descuento" title="{{ $motivoDescuento }}">
                                                <i class="fas fa-comment"></i> {{ Str::limit($motivoDescuento, 30) }}
                                            </div>
                                        @endif
                                        
                                        @if($fechaInicio && $fechaFin)
                                            <div class="periodo-descuento">
                                                <i class="fas fa-calendar-alt"></i> {{ $fechaInicio }} - {{ $fechaFin }}
                                            </div>
                                        @endif
                                    @else
                                        <span class="precio-actual">
                                            ${{ number_format($precioFinal, 2) }}
                                        </span>
                                    @endif
                                </div>

                                <!-- ENVÍO -->
                                <div class="envio-info">
                                    @if($envioGratis)
                                        <span class="envio-gratis">
                                            🚚 Envío gratis
                                        </span>
                                    @else
                                        <span class="envio-pago">
                                            📦 + ${{ number_format($costoEnvio, 2) }} envío
                                        </span>
                                    @endif
                                </div>
                                
                                <!-- SKU -->
                                <div style="font-size: 11px; color: #999; margin-bottom: 5px;">
                                    SKU: {{ $sku }}
                                </div>
                                
                                <!-- Stock -->
                                <div class="stock-info" style="color: {{ $stock > 10 ? '#00a650' : ($stock > 0 ? '#ff6b00' : '#dc3545') }};">
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
                                    <strong>Categoría:</strong> {{ $categoria }}<br>
                                    <strong>Marca:</strong> {{ $marca }}
                                </p>
                                
                                @if ($etiquetas->count() > 0)
                                    <div class="etiquetas-container">
                                        @foreach ($etiquetas as $etiqueta)
                                            <span class="badge-etiqueta" style="background-color: {{ $etiqueta->color ?? '#007bff' }};">
                                                {{ $etiqueta->vNombre }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                                
                                <div class="ver-detalle">
                                    <a href="{{ $url }}" onclick="event.stopPropagation();">Ver más</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- PAGINACIÓN -->
                <div class="paginacion">
                    @if ($productos->hasPages())
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
            @else
                <div class="sin-resultados">
                    <h3>No se encontraron productos</h3>
                    <p>Intenta con otros términos de búsqueda o ajusta los filtros.</p>
                    <a href="{{ route('busqueda.resultados') }}" class="btn" style="margin-top: 15px;">Ver todos los productos</a>
                </div>
            @endif
        </main>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        function limpiarFiltros() {
            const url = new URL(window.location.href);
            const searchTerm = url.searchParams.get('q');
            
            if (searchTerm) {
                window.location.href = "{{ route('busqueda.resultados') }}?q=" + encodeURIComponent(searchTerm);
            } else {
                window.location.href = "{{ route('busqueda.resultados') }}";
            }
        }

        function aplicarFiltroPrecio() {
            const precioMin = document.getElementById('precio_min').value;
            const precioMax = document.getElementById('precio_max').value;
            
            const url = new URL(window.location.href);
            
            if (precioMin) {
                url.searchParams.set('precio_min', precioMin);
            } else {
                url.searchParams.delete('precio_min');
            }
            
            if (precioMax) {
                url.searchParams.set('precio_max', precioMax);
            } else {
                url.searchParams.delete('precio_max');
            }
            
            window.location.href = url.toString();
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
                        throw new Error(`HTTP ${response.status}`);
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
                            
                            mostrarToast('success', '✅', `${tipoTexto} agregado a favoritos`);
                            
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
                            
                            mostrarToast('error', '❌', `${tipoTexto} eliminado de favoritos`);
                            
                            localStorage.setItem('last_favorito_action', 'removed');
                            localStorage.setItem('last_favorito_id', variacionId || productoId);
                            localStorage.setItem('last_favorito_tipo', data.tipo);
                            localStorage.setItem('last_favorito_time', Date.now());
                        }
                    } else {
                        // Revertir cambios si hubo error
                        revertirEstadoFavorito(button, estabaActivo);
                        mostrarToast('error', '❌', data.message || 'Error al gestionar favoritos');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    revertirEstadoFavorito(button, estabaActivo);
                    mostrarToast('error', '❌', 'Error de conexión');
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
                
                // Verificar estado actual
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
                        // Proceder con el toggle
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
                        throw new Error(`HTTP ${response.status}`);
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
                            
                            mostrarToast('success', '✅', `${tipoTexto} agregado a favoritos`);
                            
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
                            
                            mostrarToast('error', '❌', `${tipoTexto} eliminado de favoritos`);
                            
                            localStorage.setItem('last_favorito_action', 'removed');
                            localStorage.setItem('last_favorito_id', variacionId || productoId);
                            localStorage.setItem('last_favorito_tipo', data.tipo);
                            localStorage.setItem('last_favorito_time', Date.now());
                        }
                    } else {
                        revertirEstadoFavorito(button, estabaActivo);
                        mostrarToast('error', '❌', data.message || 'Error al gestionar favoritos');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    revertirEstadoFavorito(button, estabaActivo);
                    mostrarToast('error', '❌', 'Error de conexión');
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

        function mostrarToast(tipo, icono, mensaje) {
            let toast = document.createElement('div');
            toast.className = `toast ${tipo}`;
            toast.innerHTML = `<span class="toast-icon">${icono}</span><span class="toast-message">${mensaje}</span>`;
            document.body.appendChild(toast);
            
            setTimeout(() => toast.classList.add('show'), 10);
            
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
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
                        mostrarToast('success', '✅', 'Producto agregado al carrito');
                    } else {
                        mostrarToast('error', '❌', data.message || 'Error al agregar');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarToast('error', '❌', 'Error de conexión');
                });
            @else
                // Redirigir a login para invitados
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

            if (window.innerWidth > 768) {
                const searchInput = document.querySelector('.barra-busqueda-principal input[type="text"]');
                if (searchInput) searchInput.focus();
            }

            // Verificar acciones recientes de localStorage
            const lastAction = localStorage.getItem('last_favorito_action');
            const lastId = localStorage.getItem('last_favorito_id');
            const lastTipo = localStorage.getItem('last_favorito_tipo');
            const lastTime = localStorage.getItem('last_favorito_time');
            
            if (lastAction && lastId && lastTime && (Date.now() - lastTime) < 5000) {
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
                        button.setAttribute('title', 'Agregar a favoritos');
                    } else if (lastAction === 'added') {
                        button.classList.remove('inactivo');
                        button.classList.add('activo');
                        button.innerHTML = '❤️';
                        button.setAttribute('title', 'Quitar de favoritos');
                    }
                }
            }
            
            // Limpiar localStorage después de 5 segundos
            setTimeout(() => {
                localStorage.removeItem('last_favorito_action');
                localStorage.removeItem('last_favorito_id');
                localStorage.removeItem('last_favorito_tipo');
                localStorage.removeItem('last_favorito_time');
            }, 5000);

            // Debounce para cambios en checkboxes
            let timeoutId;
            document.querySelectorAll('#filtrosForm input[type="checkbox"]').forEach(input => {
                input.addEventListener('change', function() {
                    clearTimeout(timeoutId);
                    timeoutId = setTimeout(() => {
                        document.getElementById('filtrosForm').submit();
                    }, 500);
                });
            });

            document.getElementById('precio_min').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    aplicarFiltroPrecio();
                }
            });

            document.getElementById('precio_max').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    aplicarFiltroPrecio();
                }
            });
        });
    </script>
</body>
</html>