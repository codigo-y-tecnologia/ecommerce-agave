<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados de Búsqueda - Ecommerce Agave</title>
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
        }
        
        .precio-inputs {
            display: flex;
            gap: 10px;
        }
        
        .precio-inputs input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
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
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .paginacion {
            text-align: center;
            margin-top: 30px;
        }
        
        .paginacion .pagination {
            display: inline-flex;
            list-style: none;
            gap: 5px;
        }
        
        .paginacion .pagination li {
            display: inline;
        }
        
        .paginacion .pagination li a,
        .paginacion .pagination li span {
            padding: 8px 12px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            text-decoration: none;
            color: #007bff;
        }
        
        .paginacion .pagination li.active span {
            background: #007bff;
            color: white;
            border-color: #007bff;
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

        .precio-original {
            text-decoration: line-through;
            color: #6c757d;
            font-size: 16px;
            margin-right: 8px;
        }

        .producto-rating {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .estrellas {
            color: #ffc107;
            margin-right: 5px;
        }

        .vendidos {
            color: #666;
            margin-left: 5px;
        }

        .cupon-badge {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .envio-info {
            color: #00a650;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 8px;
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

        .badge-oferta {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        }

        .badge-stock {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
            color: #000;
        }

        .badge-nuevo {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
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

        .btn-secondary {
            background: #6c757d;
        }

        .btn-secondary:hover {
            background: #545b62;
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
            opacity: 1 !important;
            visibility: visible !important;
        }

        .corazon-favorito:hover {
            background: rgba(255, 255, 255, 1);
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        .corazon-favorito.inactivo {
            color: rgba(0,0,0,0.3);
            background: rgba(255, 255, 255, 0.8);
        }

        .corazon-favorito.activo {
            color: #3483fa;
            background: rgba(255, 255, 255, 0.95);
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

        /* Overlay de login para favoritos */
        .overlay-login {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 10000;
        }

        .modal-login {
            background: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .modal-login h3 {
            margin-bottom: 15px;
            color: #333;
        }

        .modal-login p {
            margin-bottom: 20px;
            color: #666;
        }

        .modal-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-modal {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .btn-primary-modal {
            background: #3483fa;
            color: white;
        }

        .btn-primary-modal:hover {
            background: #2968c8;
        }

        .btn-secondary-modal {
            background: #6c757d;
            color: white;
        }

        .btn-secondary-modal:hover {
            background: #545b62;
        }

        .btn-close-modal {
            background: #dc3545;
            color: white;
        }

        .btn-close-modal:hover {
            background: #c82333;
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

        /* Hover info adicional */
        .hover-info {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(255,255,255,0.95);
            padding: 15px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            opacity: 0;
            transform: translateY(10px);
            transition: all 0.3s ease;
            pointer-events: none;
            z-index: 10;
        }

        .producto-card:hover .hover-info {
            opacity: 1;
            transform: translateY(0);
        }

        .hover-categoria {
            color: #666;
            font-size: 12px;
            margin-bottom: 5px;
        }

        .hover-descripcion {
            font-size: 13px;
            color: #555;
            line-height: 1.4;
            margin-bottom: 10px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
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
                gap: 15px;
                align-items: flex-start;
            }
            
            .productos-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 20px;
            }

            .barra-busqueda-principal input[type="text"] {
                width: 60%;
            }

            .hover-info {
                display: none;
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

    <nav class="navbar">
        <ul>
            <li><a href="{{ route('inicio') }}">Inicio</a></li>
            <li><a href="{{ route('busqueda.resultados') }}">Todos los Productos</a></li>
            <li><a href="{{ route('favoritos.index') }}" style="color: #dc3545;">❤️ Mis Favoritos</a></li>
            @auth('web')
                <li><a href="#">Mi Carrito</a></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                        @csrf
                        <button type="submit" style="background: none; border: none; color: #495057; cursor: pointer; font-weight: bold;">Cerrar Sesión</button>
                    </form>
                </li>
            @endauth
            @guest
                <li><a href="{{ route('login') }}">Ingresar</a></li>
                <li><a href="{{ route('usuarios.create') }}">Crear Cuenta</a></li>
            @endguest
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

    <div class="busqueda-container">
        <!-- Sidebar de Filtros -->
        <aside class="filtros-sidebar">
            <h3 style="margin-bottom: 15px; color: #333;">Filtros de Búsqueda</h3>
            <form id="filtrosForm" method="GET" action="{{ route('busqueda.resultados') }}">
                <input type="hidden" name="q" value="{{ request('q') }}">
                
                <!-- Filtro de Categorías -->
                <div class="filtro-grupo">
                    <div class="filtro-titulo">Categorías</div>
                    @foreach($categorias as $categoria)
                    <div class="filtro-opcion">
                        <input type="checkbox" name="categoria" value="{{ $categoria->id_categoria }}" 
                               id="cat_{{ $categoria->id_categoria }}"
                               {{ request('categoria') == $categoria->id_categoria ? 'checked' : '' }}
                               onchange="this.form.submit()">
                        <label for="cat_{{ $categoria->id_categoria }}">{{ $categoria->vNombre }}</label>
                    </div>
                    @endforeach
                </div>

                <!-- Filtro de Marcas -->
                <div class="filtro-grupo">
                    <div class="filtro-titulo">Marcas</div>
                    @foreach($marcas as $marca)
                    <div class="filtro-opcion">
                        <input type="checkbox" name="marca" value="{{ $marca->id_marca }}"
                               id="marca_{{ $marca->id_marca }}"
                               {{ request('marca') == $marca->id_marca ? 'checked' : '' }}
                               onchange="this.form.submit()">
                        <label for="marca_{{ $marca->id_marca }}">{{ $marca->vNombre }}</label>
                    </div>
                    @endforeach
                </div>

                <!-- Filtro de Etiquetas -->
                <div class="filtro-grupo">
                    <div class="filtro-titulo">Etiquetas</div>
                    @foreach($etiquetas as $etiqueta)
                    <div class="filtro-opcion">
                        <input type="checkbox" name="etiqueta" value="{{ $etiqueta->id_etiqueta }}"
                               id="etiqueta_{{ $etiqueta->id_etiqueta }}"
                               {{ request('etiqueta') == $etiqueta->id_etiqueta ? 'checked' : '' }}
                               onchange="this.form.submit()">
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
                               onchange="this.form.submit()">
                        <input type="number" name="precio_max" placeholder="Máx $" 
                               value="{{ request('precio_max') }}" min="0" step="0.01"
                               onchange="this.form.submit()">
                    </div>
                </div>

                <!-- Filtro de Stock -->
                <div class="filtro-grupo">
                    <div class="filtro-opcion">
                        <input type="checkbox" name="con_stock" value="1" id="con_stock"
                               {{ request('con_stock') == '1' ? 'checked' : '' }}
                               onchange="this.form.submit()">
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
                </div>
                
                <div class="ordenamiento">
                    <form method="GET" id="ordenForm">
                        <input type="hidden" name="q" value="{{ request('q') }}">
                        <input type="hidden" name="categoria" value="{{ request('categoria') }}">
                        <input type="hidden" name="marca" value="{{ request('marca') }}">
                        <input type="hidden" name="etiqueta" value="{{ request('etiqueta') }}">
                        <input type="hidden" name="precio_min" value="{{ request('precio_min') }}">
                        <input type="hidden" name="precio_max" value="{{ request('precio_max') }}">
                        <input type="hidden" name="con_stock" value="{{ request('con_stock') }}">
                        
                        <select name="orden" onchange="document.getElementById('ordenForm').submit()">
                            <option value="nombre" {{ request('orden') == 'nombre' ? 'selected' : '' }}>Ordenar por nombre</option>
                            <option value="precio_asc" {{ request('orden') == 'precio_asc' ? 'selected' : '' }}>Precio: menor a mayor</option>
                            <option value="precio_desc" {{ request('orden') == 'precio_desc' ? 'selected' : '' }}>Precio: mayor a menor</option>
                            <option value="recientes" {{ request('orden') == 'recientes' ? 'selected' : '' }}>Más recientes</option>
                        </select>
                    </form>
                </div>
            </div>

            <!-- Resultados -->
            @if($productos->count() > 0)
                <div class="productos-grid">
                    @foreach($productos as $producto)
                        @php
                            $tieneDescuento = $producto->tieneDescuento();
                            $estaBajoStock = $producto->estaBajoEnStock();
                            $esFavorito = $producto->esFavorito();
                            // Datos simulados para demo
                            $rating = rand(40, 50) / 10; // 4.0 - 5.0
                            $vendidos = rand(100, 5000);
                            $llegaManana = rand(0, 1);
                            $tieneCupon = rand(0, 1);
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

                                <!-- Badges de oferta y stock -->
                                @if($tieneDescuento)
                                    <div style="position: absolute; top: 10px; left: 10px; z-index: 99;">
                                        <span class="badge badge-oferta">{{ $producto->porcentajeDescuento() }}% OFF</span>
                                    </div>
                                @endif
                                @if($estaBajoStock)
                                    <div style="position: absolute; top: 50px; left: 10px; z-index: 99;">
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
                                
                                <!-- Rating y vendidos -->
                                <div class="producto-rating">
                                    <span class="estrellas">★★★★★</span>
                                    <span class="rating-num">{{ number_format($rating, 1) }}</span>
                                    <span class="vendidos">| +{{ $vendidos }} vendidos</span>
                                </div>

                                <!-- Precio -->
                                <div class="producto-precio">
                                    @if($tieneDescuento)
                                        <span class="precio-original">${{ number_format($producto->dPrecio_compra, 2) }}</span>
                                    @endif
                                    ${{ number_format($producto->dPrecio_venta, 2) }}
                                </div>

                                <!-- Cupón -->
                                @if($tieneCupon)
                                    <div class="cupon-badge">
                                        📌 Cupón ${{ rand(50, 200) }} OFF
                                    </div>
                                @endif

                                <!-- Envío -->
                                @if($llegaManana)
                                    <div class="envio-info">
                                        🚚 Llega mañana
                                    </div>
                                @else
                                    <div style="color: #666; font-size: 14px;">
                                        📦 Envío gratis
                                    </div>
                                @endif

                                <!-- Stock -->
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

                <!-- Paginación -->
                <div class="paginacion">
                    {{ $productos->appends(request()->query())->links() }}
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

    <!-- Modal para login -->
    <div class="overlay-login" id="overlayLogin">
        <div class="modal-login">
            <h3>¡Inicia sesión para guardar favoritos! ❤️</h3>
            <p>Para agregar productos a tu lista de deseos, necesitas tener una cuenta.</p>
            
            <div class="modal-buttons">
                <a href="{{ route('login') }}" class="btn-modal btn-primary-modal">Iniciar Sesión</a>
                <a href="{{ route('usuarios.create') }}" class="btn-modal btn-secondary-modal">Crear Cuenta</a>
                <button class="btn-modal btn-close-modal" onclick="cerrarModalLogin()">Cancelar</button>
            </div>
        </div>
    </div>

    <script>
        function limpiarFiltros() {
            // Mantener solo el término de búsqueda y limpiar todos los filtros
            const url = new URL(window.location.href);
            const searchTerm = url.searchParams.get('q');
            
            // Redirigir manteniendo solo el término de búsqueda
            if (searchTerm) {
                window.location.href = "{{ route('busqueda.resultados') }}?q=" + encodeURIComponent(searchTerm);
            } else {
                window.location.href = "{{ route('busqueda.resultados') }}";
            }
        }

        // Función para toggle favoritos en productos
        function toggleFavorito(button, productoId) {
            const esFavorito = button.getAttribute('data-es-favorito') === 'true';
            
            fetch(`/favoritos/toggle/${productoId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
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
                    if (data.redirect) {
                        // Mostrar modal de login si no está autenticado
                        mostrarModalLogin();
                    } else {
                        showNotification('❌ ' + data.message);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('❌ Error al gestionar favoritos');
            });
        }

        // Funciones para el modal de login
        function mostrarModalLogin() {
            document.getElementById('overlayLogin').style.display = 'flex';
        }

        function cerrarModalLogin() {
            document.getElementById('overlayLogin').style.display = 'none';
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

        // Cerrar modal al hacer click fuera
        document.getElementById('overlayLogin')?.addEventListener('click', function(e) {
            if (e.target === this) {
                cerrarModalLogin();
            }
        });

        // Auto-focus en la barra de búsqueda al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('.barra-busqueda-principal input[type="text"]');
            if (searchInput) {
                searchInput.focus();
                // Seleccionar el texto para facilitar nueva búsqueda
                searchInput.select();
            }
        });
    </script>
</body>
</html>