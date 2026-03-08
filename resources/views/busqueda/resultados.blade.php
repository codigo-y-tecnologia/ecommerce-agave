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
            flex-wrap: wrap;
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
        
        /* PAGINACIÓN PERSONALIZADA */
        .paginacion {
            display: flex;
            justify-content: center;
            margin: 30px 0 20px;
        }

        .pagination {
            display: flex;
            list-style: none;
            gap: 8px;
            padding: 0;
            margin: 0;
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
            min-width: 40px;
            height: 40px;
            padding: 0 8px;
            border-radius: 8px;
            text-decoration: none;
            color: #495057;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            background-color: white;
            border: 1px solid #dee2e6;
        }

        .pagination li a:hover {
            background-color: #e9ecef;
            border-color: #adb5bd;
            color: #007bff;
        }

        .pagination li.active span {
            background: #007bff;
            color: white;
            border-color: #007bff;
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
            background-color: #f8f9fa;
            border-color: #dee2e6;
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
        }

        .producto-imagen {
            width: 100%;
            height: 200px;
            object-fit: contain;
            border-radius: 8px;
            transition: transform 0.3s ease;
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
            flex: 1;
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

        /* ESTILOS DE PRECIOS */
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

        .badge-oferta {
            position: absolute;
            top: 15px;
            left: 15px;
            background: #dc3545;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            z-index: 99;
        }

        .badge-stock-bajo {
            position: absolute;
            top: 15px;
            left: 15px;
            background: #ffc107;
            color: #000;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            z-index: 99;
        }

        /* Badge para variaciones */
        .badge-variacion {
            position: absolute;
            top: 15px;
            right: 60px;
            background: #6c757d;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            z-index: 99;
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

        /* Corazón de favoritos */
        .corazon-favorito {
            position: absolute;
            top: 15px;
            right: 15px;
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
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        }

        .corazon-favorito:hover {
            background: rgba(255, 255, 255, 1);
            transform: scale(1.15);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
        }

        .corazon-favorito.activo {
            color: #3483fa;
            background: rgba(52, 131, 250, 0.1);
            border-color: #3483fa;
        }

        .corazon-favorito.inactivo {
            color: rgba(0, 0, 0, 0.25);
        }

        .corazon-favorito.activo::before {
            content: '❤️';
        }

        .corazon-favorito.inactivo::before {
            content: '🤍';
        }

        /* Toast notifications */
        .toast {
            position: fixed;
            top: 30px;
            right: 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 18px 25px;
            border-radius: 10px;
            z-index: 10000;
            font-size: 15px;
            font-weight: 600;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            transition: all 0.4s ease;
            display: flex;
            align-items: center;
            gap: 12px;
            max-width: 350px;
            transform: translateX(120%);
        }

        .toast.show {
            transform: translateX(0);
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
                width: 60%;
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
            <li><a href="{{ route('inicio') }}">Inicio</a></li>
            <li><a href="{{ route('busqueda.resultados') }}">Todos los Productos</a></li>
            <li><a href="{{ route('busqueda.resultados', ['en_descuento' => '1']) }}" style="color: #dc3545; font-weight: bold;">🔥 En Descuento</a></li>
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

        <!-- SOLO UNA BARRA DE BÚSQUEDA -->
        <div class="barra-busqueda-principal">
            <form action="{{ route('busqueda.resultados') }}" method="GET">
                <input type="text" name="q" placeholder="Buscar productos" 
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
                        
                        <!-- Pasar todas las categorías seleccionadas -->
                        @if(is_array(request('categorias')))
                            @foreach(request('categorias') as $categoria_id)
                                <input type="hidden" name="categorias[]" value="{{ $categoria_id }}">
                            @endforeach
                        @endif
                        
                        <!-- Pasar todas las marcas seleccionadas -->
                        @if(is_array(request('marcas')))
                            @foreach(request('marcas') as $marca_id)
                                <input type="hidden" name="marcas[]" value="{{ $marca_id }}">
                            @endforeach
                        @endif
                        
                        <!-- Pasar todas las etiquetas seleccionadas -->
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
                            
                            // Determinar si tiene descuento activo (cada uno con su propia lógica)
                            if ($esVariacion) {
                                $tieneDescuento = $producto->ofertaVigente(); // Método en modelo Variacion
                                $precioOriginal = $producto->dPrecio;
                                $precioOferta = $producto->dPrecio_oferta;
                                $stock = $producto->iStock;
                                $nombreProducto = $producto->productoPadre->vNombre . ' - ' . $producto->getAtributosTexto();
                                $imagenes = $producto->imagenes ?? $producto->productoPadre->imagenes;
                                $categoria = $producto->productoPadre->categoria->vNombre ?? 'N/A';
                                $marca = $producto->productoPadre->marca->vNombre ?? 'N/A';
                                $etiquetas = $producto->productoPadre->etiquetas;
                                $url = route('productos.show.public', [$producto->productoPadre->id_producto, 'variacion' => $producto->id_variacion]);
                                $sku = $producto->vSKU;
                                $esFavorito = $producto->productoPadre->esFavorito(); // O podrías tener favoritos por variación
                            } else {
                                $tieneDescuento = $producto->tieneDescuentoActivo(); // Método en modelo Producto
                                $precioOriginal = $producto->dPrecio_venta;
                                $precioOferta = $producto->dPrecio_oferta;
                                $stock = $producto->iStock;
                                $nombreProducto = $producto->vNombre;
                                $imagenes = $producto->imagenes;
                                $categoria = $producto->categoria->vNombre ?? 'N/A';
                                $marca = $producto->marca->vNombre ?? 'N/A';
                                $etiquetas = $producto->etiquetas;
                                $url = route('productos.show.public', $producto->id_producto);
                                $sku = $producto->vCodigo_barras;
                                $esFavorito = $producto->esFavorito();
                            }
                            
                            $precioActual = $tieneDescuento ? $precioOferta : $precioOriginal;
                            $porcentajeDescuento = $tieneDescuento ? round((($precioOriginal - $precioOferta) / $precioOriginal) * 100) : 0;
                            
                            $estaBajoStock = $stock > 0 && $stock <= 10;
                            
                            // Lógica de envío (puedes ajustar estos valores según tu negocio)
                            $envioGratis = $precioActual >= 150;
                            $costoEnvio = 50;
                        @endphp
                        
                        <div class="producto-card" onclick="window.location.href='{{ $url }}'">
                            <div class="producto-imagen-container">
                                <!-- BOTÓN DEL CORAZÓN -->
                                <button class="corazon-favorito {{ $esFavorito ? 'activo' : 'inactivo' }}" 
                                        data-producto="{{ $esVariacion ? $producto->productoPadre->id_producto : $producto->id_producto }}"
                                        onclick="event.stopPropagation(); toggleFavorito(this, {{ $esVariacion ? $producto->productoPadre->id_producto : $producto->id_producto }})"
                                        title="{{ $esFavorito ? 'Quitar de favoritos' : 'Agregar a favoritos' }}">
                                </button>

                                <!-- Badge de variación -->
                                @if($esVariacion)
                                    <div class="badge-variacion">
                                        🔄 Variación
                                    </div>
                                @endif

                                <!-- Badge de descuento -->
                                @if($tieneDescuento)
                                    <div class="badge-oferta">
                                        -{{ $porcentajeDescuento }}%
                                    </div>
                                @elseif($estaBajoStock)
                                    <div class="badge-stock-bajo">
                                        ¡Últimas!
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
                                
                                <!-- Precio con descuento -->
                                <div class="producto-precio">
                                    @if($tieneDescuento)
                                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 5px; flex-wrap: wrap;">
                                            <span class="precio-original">
                                                ${{ number_format($precioOriginal, 2) }}
                                            </span>
                                            <span class="precio-actual">
                                                ${{ number_format($precioOferta, 2) }}
                                            </span>
                                            <span class="badge-descuento">
                                                -{{ $porcentajeDescuento }}%
                                            </span>
                                        </div>
                                    @else
                                        <span class="precio-actual">
                                            ${{ number_format($precioOriginal, 2) }}
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
                                    <a href="{{ $url }}" onclick="event.stopPropagation();">Ver detalle</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- PAGINACIÓN PERSONALIZADA -->
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

        // Función para toggle favoritos en productos
        function toggleFavorito(button, productoId) {
            if (button.disabled) return;
            button.disabled = true;

            @if(!Auth::check())
                window.location.href = '{{ route("login") }}?from_favoritos=true&redirect=' + encodeURIComponent(window.location.href);
                return;
            @endif

            const esFavorito = button.getAttribute('data-es-favorito') === 'true';
            
            button.style.transform = 'scale(0.9)';
            
            fetch(`/favoritos/toggle/${productoId}`, {
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
                        button.classList.remove('inactivo');
                        button.classList.add('activo');
                        button.setAttribute('data-es-favorito', 'true');
                        showNotification('✅ ¡Producto agregado a tu lista de deseos!', 'success');
                        
                        localStorage.setItem('last_favorito_action', 'added');
                        localStorage.setItem('last_favorito_id', productoId);
                        localStorage.setItem('last_favorito_time', Date.now());
                    } else {
                        button.classList.remove('activo');
                        button.classList.add('inactivo');
                        button.setAttribute('data-es-favorito', 'false');
                        showNotification('❌ Producto eliminado de tu lista de deseos', 'error');
                        
                        localStorage.setItem('last_favorito_action', 'removed');
                        localStorage.setItem('last_favorito_id', productoId);
                        localStorage.setItem('last_favorito_time', Date.now());
                    }
                } else {
                    showNotification(data.message || 'Error al gestionar favoritos', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error de conexión. Intenta nuevamente.', 'error');
            })
            .finally(() => {
                setTimeout(() => {
                    button.disabled = false;
                    button.style.transform = '';
                }, 500);
            });
        }

        // Función mejorada para mostrar notificaciones
        function showNotification(message, type = 'success') {
            const existingToasts = document.querySelectorAll('.toast');
            existingToasts.forEach(toast => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            });

            const notification = document.createElement('div');
            notification.className = `toast ${type}`;
            notification.innerHTML = `
                <span class="toast-icon">${type === 'success' ? '✅' : type === 'error' ? '❌' : '⚠️'}</span>
                <span class="toast-message">${message}</span>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.classList.add('show');
            }, 10);

            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => {
                    if (document.body.contains(notification)) {
                        document.body.removeChild(notification);
                    }
                }, 400);
            }, 3500);
        }

        // Verificar acciones recientes al cargar
        document.addEventListener('DOMContentLoaded', function() {
            const lastAction = localStorage.getItem('last_favorito_action');
            const lastId = localStorage.getItem('last_favorito_id');
            const lastTime = localStorage.getItem('last_favorito_time');
            
            if (lastAction && (Date.now() - lastTime) < 5000) {
                const button = document.querySelector(`.corazon-favorito[data-producto="${lastId}"]`);
                if (button) {
                    if (lastAction === 'removed') {
                        button.classList.remove('activo');
                        button.classList.add('inactivo');
                        button.setAttribute('data-es-favorito', 'false');
                    } else if (lastAction === 'added') {
                        button.classList.remove('inactivo');
                        button.classList.add('activo');
                        button.setAttribute('data-es-favorito', 'true');
                    }
                }
            }
            
            // Limpiar después de 5 segundos
            setTimeout(() => {
                localStorage.removeItem('last_favorito_action');
                localStorage.removeItem('last_favorito_id');
                localStorage.removeItem('last_favorito_time');
            }, 5000);

            const searchInput = document.querySelector('.barra-busqueda-principal input[type="text"]');
            if (searchInput) {
                searchInput.focus();
                searchInput.select();
            }

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

            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('favorito_agregado')) {
                showNotification('✅ ¡Producto agregado a tu lista de deseos!', 'success');
                const url = new URL(window.location);
                url.searchParams.delete('favorito_agregado');
                window.history.replaceState({}, '', url);
            }
        });
    </script>
</body>
</html>