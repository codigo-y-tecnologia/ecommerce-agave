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
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
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
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
            text-align: left;
            border: 1px solid #dee2e6;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .producto-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .producto-imagen {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .no-imagen {
            width: 100%;
            height: 150px;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            margin-bottom: 10px;
            color: #6c757d;
        }

        .producto-card h3 {
            margin-top: 0;
            margin-bottom: 8px;
            color: #333;
            font-size: 16px;
            line-height: 1.3;
        }

        .producto-precio {
            font-weight: bold;
            color: #28a745;
            margin-bottom: 5px;
            font-size: 18px;
        }

        .producto-card p {
            margin-bottom: 5px;
            font-size: 14px;
            color: #666;
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

        .ver-detalle {
            margin-top: 10px;
            text-align: center;
        }

        .ver-detalle a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
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
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 14px;
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
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
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
            <li><a href="{{ route('home') }}">Inicio</a></li>
            <li><a href="{{ route('busqueda.resultados') }}">Todos los Productos</a></li>
            @auth('web')
                <li><a href="#">Mi Carrito</a></li>
                <li><a href="#">Mis Pedidos</a></li>
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
                        <div class="producto-card" onclick="window.location.href='{{ route('productos.show.public', $producto->id_producto) }}'">
                            @if(count($producto->imagenes) > 0)
                                <img src="{{ $producto->imagenes[0] }}" alt="{{ $producto->vNombre }}" class="producto-imagen">
                            @else
                                <div class="no-imagen">
                                    <span>Sin imagen</span>
                                </div>
                            @endif
                            
                            <h3>{{ $producto->vNombre }}</h3>
                            <p class="producto-precio">${{ number_format($producto->dPrecio_venta, 2) }}</p>
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
                            
                            <div class="ver-detalle">
                                <a href="{{ route('productos.show.public', $producto->id_producto) }}">Ver detalle del producto</a>
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