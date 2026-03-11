<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Lista de Deseos - Ecommerce Agave</title>
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

        /* Navbar */
        .navbar {
            background-color: #e9ecef;
            padding: 10px 0;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .nav-links {
            list-style: none;
            display: flex;
            justify-content: center;
            gap: 25px;
            padding: 0;
            margin: 0;
        }

        .nav-links li {
            display: inline;
        }

        .nav-links li a {
            color: #495057;
            text-decoration: none;
            font-weight: bold;
        }

        .nav-links li a:hover {
            text-decoration: underline;
        }

        /* Barra de búsqueda */
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

        /* Contenedor principal */
        .main-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .page-title {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .page-title h1 {
            color: #333;
            font-size: 28px;
        }

        .heart-icon {
            color: #ff4757;
            font-size: 32px;
        }

        /* Grid de favoritos */
        .favoritos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        /* Tarjeta de favorito - SOLO CORREGIDO EL ZOOM */
        .favorito-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            position: relative;
        }

        .favorito-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .favorito-img-container {
            position: relative;
            height: 200px;
            overflow: hidden;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* SOLO CAMBIÉ ESTO - de 'cover' a 'contain' para eliminar el zoom */
        .favorito-imagen {
            width: 100%;
            height: 100%;
            object-fit: contain; /* Cambiado de 'cover' a 'contain' */
            transition: transform 0.5s ease;
            background-color: #f8f9fa;
            padding: 10px; /* Añadí padding para mejor presentación */
        }

        /* Reduje el zoom al mínimo */
        .favorito-card:hover .favorito-imagen {
            transform: scale(1.02); /* Zoom mínimo en lugar de 1.05 */
        }

        /* Botón de eliminar favorito */
        .eliminar-favorito-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 2;
            transition: all 0.3s ease;
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
        }

        .eliminar-favorito-btn:hover {
            background: white;
            transform: scale(1.1);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .favorito-info {
            padding: 20px;
        }

        .favorito-nombre {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
            line-height: 1.3;
            cursor: pointer;
        }

        .favorito-nombre:hover {
            color: #007bff;
        }

        .favorito-precio {
            font-size: 24px;
            font-weight: bold;
            color: #2e7d32;
            margin-bottom: 15px;
        }

        .favorito-stock {
            font-size: 14px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .stock-disponible {
            color: #00a650;
        }

        .stock-bajo {
            color: #ff9500;
            font-weight: bold;
        }

        .sin-stock {
            color: #dc3545;
            font-weight: bold;
        }

        .favorito-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(45deg, #5a67d8, #6b46c1);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-danger {
            background: #ff4757;
            color: white;
        }

        .btn-danger:hover {
            background: #ff2e43;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 71, 87, 0.4);
        }

        /* Badge de oferta/descuento */
        .badge-oferta {
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

        /* Corazón de favoritos */
        .corazon-favorito {
            position: absolute;
            top: 15px;
            right: 60px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 2;
            border: 1px solid rgba(0, 0, 0, 0.1);
            font-size: 20px;
            transition: all 0.3s ease;
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
        }

        .corazon-favorito:hover {
            background: white;
            transform: scale(1.1);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .corazon-favorito.activo {
            color: #ff4757;
        }

        /* Estado vacío */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .empty-icon {
            font-size: 80px;
            color: #adb5bd;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            color: #666;
            margin-bottom: 15px;
            font-size: 24px;
        }

        .empty-state p {
            color: #999;
            max-width: 600px;
            margin: 0 auto 25px;
        }

        /* NOTIFICACIÓN ÚNICA */
        .single-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #2ecc71;
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            z-index: 9999;
            font-size: 14px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            gap: 10px;
            transform: translateX(120%);
            transition: transform 0.4s ease;
        }

        .single-notification.show {
            transform: translateX(0);
        }

        .single-notification.error {
            background: #e74c3c;
        }

        .favoritos-count-container {
            background: white;
            padding: 10px 20px;
            border-radius: 6px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .favoritos-count {
            font-weight: bold;
            color: #007bff;
            font-size: 18px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .favoritos-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 20px;
            }
            
            .page-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .barra-busqueda-principal input[type="text"] {
                width: 60%;
            }

            .nav-links {
                flex-wrap: wrap;
                gap: 15px;
            }
        }

        @media (max-width: 480px) {
            .favoritos-grid {
                grid-template-columns: 1fr;
            }
            
            .favorito-actions {
                flex-direction: column;
            }
            
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

            .nav-links {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <h1>Ecommerce Agave</h1>
        <p>Mi Lista de Deseos</p>
    </header>

    <!-- Mostrar bienvenida al usuario si está autenticado -->
    @auth
    <div class="user-welcome">
        <p>¡Hola {{ Auth::user()->vNombre }}! 👋 Tus productos favoritos</p>
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

        <!-- Barra de búsqueda -->
        <div class="barra-busqueda-principal">
            <form action="{{ route('busqueda.resultados') }}" method="GET">
                <input type="text" name="q" placeholder="Buscar productos (agave, mezcal, espadín...)" 
                       value="{{ request('q') }}" autocomplete="off">
                <button type="submit">Buscar</button>
            </form>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="main-container">
        <!-- Encabezado de página -->
        <div class="page-header">
            <div class="page-title">
                <div class="heart-icon">❤️</div>
                <h1>Mi Lista de Deseos</h1>
            </div>
            <div>
                <a href="{{ route('busqueda.resultados') }}" class="btn btn-primary">
                    <span>🛍️</span> Seguir Comprando
                </a>
            </div>
        </div>

        <!-- Contador de favoritos -->
        <div class="favoritos-count-container">
            <p>Tienes <span class="favoritos-count">{{ $favoritos->count() }}</span> producto(s) en tu lista de deseos</p>
        </div>

        <!-- Lista de favoritos -->
        @if($favoritos->count() > 0)
            <div class="favoritos-grid">
                @foreach($favoritos as $favorito)
                    @php
                        // Determinar si es producto o variación
                        $esVariacion = !is_null($favorito->id_variacion) && $favorito->variacion;
                        
                        if ($esVariacion) {
                            // Es una variación
                            $item = $favorito->variacion;
                            $producto = $item->producto;
                            
                            // Obtener texto de atributos (guardado pero no mostrado)
                            $atributosTexto = [];
                            foreach($item->atributos as $atributoRel) {
                                if($atributoRel->atributo && $atributoRel->valor) {
                                    $atributosTexto[] = $atributoRel->valor->vValor;
                                }
                            }
                            $atributosTexto = implode(' - ', $atributosTexto);
                            
                            // NOMBRE SIMPLE: solo el nombre del producto + atributos separados por guiones
                            $nombreCompleto = $producto->vNombre . ' - ' . $atributosTexto;
                            
                            $precioActual = $item->tieneDescuentoActivo() ? $item->dPrecio_oferta : $item->dPrecio;
                            $precioOriginal = $item->dPrecio;
                            $tieneDescuento = $item->tieneDescuentoActivo();
                            $porcentajeDescuento = $item->porcentaje_descuento;
                            $stock = $item->iStock;
                            $imagenes = $item->imagenes;
                            $url = route('productos.show.public', [$producto->id_producto, 'variacion' => $item->id_variacion]);
                            $productoPadreId = $producto->id_producto;
                            $tipo = 'variacion';
                            $id = $item->id_variacion;
                        } else {
                            // Es un producto
                            $item = $favorito->producto;
                            
                            // NOMBRE SIMPLE: solo el nombre del producto
                            $nombreCompleto = $item->vNombre;
                            
                            $precioActual = $item->tieneDescuentoActivo() ? $item->dPrecio_oferta : $item->dPrecio_venta;
                            $precioOriginal = $item->dPrecio_venta;
                            $tieneDescuento = $item->tieneDescuentoActivo();
                            $porcentajeDescuento = $item->porcentaje_descuento;
                            $stock = $item->iStock;
                            $imagenes = $item->imagenes;
                            $url = route('productos.show.public', $item->id_producto);
                            $productoPadreId = $item->id_producto;
                            $tipo = 'producto';
                            $id = $item->id_producto;
                        }
                        
                        // Determinar clase de stock
                        $stockClass = $stock > 10 ? 'stock-disponible' : ($stock > 0 ? 'stock-bajo' : 'sin-stock');
                        
                        // Mensaje de stock
                        $stockMessage = $stock > 10 
                            ? '✅ En stock (' . $stock . ' unidades)' 
                            : ($stock > 0 
                                ? '⚠️ Solo ' . $stock . ' disponibles' 
                                : '❌ Agotado');
                        
                        // Generar ID único para la card
                        $cardId = $esVariacion 
                            ? 'favorito-var-' . $id . '-prod-' . $productoPadreId
                            : 'favorito-prod-' . $id;
                    @endphp
                    
                    <div class="favorito-card" id="{{ $cardId }}" data-tipo="{{ $tipo }}" data-id="{{ $id }}" data-producto-padre="{{ $productoPadreId }}">
                        <!-- Imagen del producto/variación -->
                        <div class="favorito-img-container">
                            <!-- Botón de corazón para quitar de favoritos -->
                            <button class="corazon-favorito activo" 
                                    onclick="event.stopPropagation(); eliminarFavorito({{ $productoPadreId }}, '{{ $tipo }}', {{ $id }})"
                                    title="Quitar de favoritos">
                                ❤️
                            </button>

                            <!-- Botón X para eliminar de favoritos -->
                            <button class="eliminar-favorito-btn" 
                                    onclick="event.stopPropagation(); eliminarFavorito({{ $productoPadreId }}, '{{ $tipo }}', {{ $id }})"
                                    title="Eliminar de favoritos">
                                ❌
                            </button>

                            @if($tieneDescuento)
                                <div class="badge-oferta">
                                    -{{ $porcentajeDescuento }}%
                                </div>
                            @endif

                            @if(count($imagenes) > 0)
                                <img src="{{ $imagenes[0] }}" alt="{{ $nombreCompleto }}" class="favorito-imagen">
                            @else
                                <div style="display: flex; align-items: center; justify-content: center; height: 100%; background: linear-gradient(45deg, #f8f9fa, #e9ecef);">
                                    <span style="font-size: 48px; color: #adb5bd;">🛒</span>
                                </div>
                            @endif
                        </div>

                        <!-- Información del producto/variación -->
                        <div class="favorito-info">
                            <h3 class="favorito-nombre" onclick="window.location.href='{{ $url }}'">
                                {{ $nombreCompleto }}
                            </h3>
                            
                            <div class="favorito-precio">
                                @if($tieneDescuento)
                                    <span style="text-decoration: line-through; color: #999; font-size: 16px; margin-right: 8px;">
                                        ${{ number_format($precioOriginal, 2) }}
                                    </span>
                                @endif
                                ${{ number_format($precioActual, 2) }}
                            </div>

                            <div class="favorito-stock {{ $stockClass }}">
                                {!! $stockMessage !!}
                            </div>

                            <div class="favorito-actions">
                                <a href="{{ $url }}" class="btn btn-primary">
                                    <span>👁️</span> Ver Producto
                                </a>
                                <button class="btn btn-danger" onclick="eliminarFavorito({{ $productoPadreId }}, '{{ $tipo }}', {{ $id }})">
                                    <span>🗑️</span> Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    ❤️
                </div>
                <h3>Tu lista de deseos está vacía</h3>
                <p>Añade productos que te gusten para verlos aquí y recibir notificaciones cuando bajen de precio o se estén agotando.</p>
                <a href="{{ route('busqueda.resultados') }}" class="btn btn-primary" style="display: inline-flex; width: auto; padding: 15px 40px;">
                    <span>🛍️</span> Explorar Productos
                </a>
            </div>
        @endif
    </div>

    <script>
        // VARIABLES GLOBALES
        let currentNotification = null;
        let notificationTimeout = null;

        // Función para eliminar favorito
        function eliminarFavorito(productoId, tipo, id) {
            const variacionId = tipo === 'variacion' ? id : null;
            
            // Encontrar la card específica
            let selector = '';
            if (tipo === 'variacion' && id) {
                selector = `[id="favorito-var-${id}-prod-${productoId}"]`;
            } else {
                selector = `[id="favorito-prod-${productoId}"]`;
            }
            
            const cards = document.querySelectorAll(selector);
            
            if (cards.length === 0) {
                showNotification('Error: No se encontró el producto ❌');
                return;
            }

            cards.forEach(card => {
                if (card) {
                    card.style.opacity = '0.5';
                    card.style.transform = 'scale(0.95)';
                    card.style.transition = 'all 0.3s ease';
                }
            });

            // Eliminar notificación anterior
            removeNotification();

            // Construir URL con parámetros
            let url = `/favoritos/${productoId}`;
            if (variacionId) {
                url += `?id_variacion=${variacionId}`;
            }

            fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Eliminar las cards del DOM
                    let eliminadas = 0;
                    cards.forEach(card => {
                        if (card && card.parentNode) {
                            // Animación de salida
                            card.style.transform = 'scale(0.8)';
                            card.style.opacity = '0';
                            
                            setTimeout(() => {
                                if (card && card.parentNode) {
                                    card.remove();
                                    eliminadas++;
                                    
                                    if (eliminadas === cards.length) {
                                        updateFavoritosCount();
                                        
                                        setTimeout(() => {
                                            const remainingCards = document.querySelectorAll('.favorito-card');
                                            if (remainingCards.length === 0) {
                                                showEmptyState();
                                            }
                                        }, 100);
                                    }
                                }
                            }, 300);
                        }
                    });
                    
                    const mensaje = 'Producto eliminado de favoritos ✅';
                    showNotification(mensaje);
                } else {
                    showNotification('Error al eliminar ❌');
                    cards.forEach(card => {
                        if (card) {
                            card.style.opacity = '1';
                            card.style.transform = 'scale(1)';
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error de conexión ❌');
                cards.forEach(card => {
                    if (card) {
                        card.style.opacity = '1';
                        card.style.transform = 'scale(1)';
                    }
                });
            });
        }

        // Función para eliminar notificación existente
        function removeNotification() {
            if (currentNotification) {
                currentNotification.classList.remove('show');
                setTimeout(() => {
                    if (currentNotification && currentNotification.parentNode) {
                        currentNotification.parentNode.removeChild(currentNotification);
                    }
                    currentNotification = null;
                }, 300);
            }
            
            if (notificationTimeout) {
                clearTimeout(notificationTimeout);
                notificationTimeout = null;
            }
            
            // Eliminar cualquier otra notificación
            const allNotifications = document.querySelectorAll('.single-notification');
            allNotifications.forEach(notification => {
                notification.classList.remove('show');
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            });
        }

        // Función para mostrar una sola notificación
        function showNotification(message) {
            removeNotification();
            
            const notification = document.createElement('div');
            notification.className = 'single-notification';
            
            if (message.includes('✅')) {
                notification.className = 'single-notification';
            } else {
                notification.className = 'single-notification error';
            }
            
            const cleanMessage = message.replace('✅', '').replace('❌', '').trim();
            
            notification.innerHTML = `
                <span>${message.includes('✅') ? '✅' : '❌'}</span>
                <span>${cleanMessage}</span>
            `;
            
            document.body.appendChild(notification);
            currentNotification = notification;
            
            setTimeout(() => {
                notification.classList.add('show');
            }, 10);
            
            notificationTimeout = setTimeout(() => {
                if (notification.classList.contains('show')) {
                    notification.classList.remove('show');
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.parentNode.removeChild(notification);
                        }
                        currentNotification = null;
                        notificationTimeout = null;
                    }, 400);
                }
            }, 3000);
        }

        // Función para actualizar el contador de favoritos
        function updateFavoritosCount() {
            const remainingCards = document.querySelectorAll('.favorito-card').length;
            const countElement = document.querySelector('.favoritos-count');
            if (countElement) {
                countElement.textContent = remainingCards;
            }
            
            const countContainer = document.querySelector('.favoritos-count-container p');
            if (countContainer) {
                countContainer.innerHTML = `Tienes <span class="favoritos-count">${remainingCards}</span> producto(s) en tu lista de deseos`;
            }
        }

        // Función para mostrar estado vacío
        function showEmptyState() {
            const mainContainer = document.querySelector('.main-container');
            if (mainContainer) {
                const favoritosGrid = document.querySelector('.favoritos-grid');
                const countContainer = document.querySelector('.favoritos-count-container');
                const pageHeader = document.querySelector('.page-header');
                
                if (favoritosGrid) {
                    favoritosGrid.style.display = 'none';
                    favoritosGrid.remove();
                }
                if (countContainer) {
                    countContainer.style.display = 'none';
                    countContainer.remove();
                }
                if (pageHeader) {
                    pageHeader.style.display = 'none';
                    pageHeader.remove();
                }
                
                if (!document.getElementById('empty-state-dynamic')) {
                    const emptyStateHTML = `
                        <div class="empty-state" id="empty-state-dynamic">
                            <div class="empty-icon">
                                ❤️
                            </div>
                            <h3>Tu lista de deseos está vacía</h3>
                            <p>Añade productos que te gusten para verlos aquí y recibir notificaciones cuando bajen de precio o se estén agotando.</p>
                            <a href="{{ route('busqueda.resultados') }}" class="btn btn-primary" style="display: inline-flex; width: auto; padding: 15px 40px;">
                                <span>🛍️</span> Explorar Productos
                            </a>
                        </div>
                    `;
                    
                    mainContainer.innerHTML = emptyStateHTML;
                }
            }
        }

        // Prevenir que el clic en los botones se propague a la card
        document.addEventListener('DOMContentLoaded', function() {
            // Limpiar localStorage
            localStorage.removeItem('favorito_removed');
            localStorage.removeItem('favorito_removed_time');
            
            // Auto-focus en la barra de búsqueda
            const searchInput = document.querySelector('.barra-busqueda-principal input[type="text"]');
            if (searchInput) {
                searchInput.focus();
            }
            
            // Prevenir propagación en todos los botones dentro de las cards
            const buttons = document.querySelectorAll('.favorito-card button, .favorito-card a');
            buttons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            });
        });
    </script>
</body>
</html>