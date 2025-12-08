<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $producto->vNombre }} - Detalles del Producto</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        /* Header Styles */
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

        /* Navbar Styles */
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

        /* Search Bar */
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

        /* Main Container */
        .container {
            max-width: 1200px;
            margin: 20px auto;
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .back-btn {
            color: #495057;
            text-decoration: none;
            font-weight: bold;
            padding: 8px 16px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            background: white;
            margin-bottom: 20px;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            color: #007bff;
            border-color: #007bff;
            background: #f8f9fa;
            transform: translateY(-2px);
        }

        .producto-detalle {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 20px;
        }

        .imagenes-container {
            position: relative;
        }

        .imagen-principal {
            width: 100%;
            height: 400px;
            object-fit: contain;
            border-radius: 8px;
            background: #f8f9fa;
            padding: 20px;
        }

        .miniaturas {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            overflow-x: auto;
            padding: 10px 0;
        }

        .miniatura {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 6px;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .miniatura:hover {
            border-color: #007bff;
            transform: scale(1.05);
        }

        .miniatura.activa {
            border-color: #007bff;
            box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.3);
        }

        .btn-favorito-detalle {
            background: #fff;
            border: 2px solid #3483fa;
            color: #3483fa;
            padding: 16px 30px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            transition: all 0.3s ease;
            margin: 20px 0;
            width: 100%;
            max-width: 400px;
            position: relative;
            overflow: hidden;
        }

        .btn-favorito-detalle:hover {
            background: #f8f9fa;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(52, 131, 250, 0.3);
            border-color: #2968c8;
        }

        .btn-favorito-detalle:active {
            transform: translateY(0);
        }

        .btn-favorito-detalle.activo {
            background: #3483fa;
            border-color: #3483fa;
            color: white;
        }

        .btn-favorito-detalle.activo:hover {
            background: #2968c8;
            border-color: #2968c8;
        }

        .btn-favorito-detalle .btn-icon {
            font-size: 22px;
            transition: transform 0.3s ease;
        }

        .btn-favorito-detalle:hover .btn-icon {
            transform: scale(1.2);
        }

        .btn-favorito-detalle.activo .btn-icon {
            animation: latido 0.5s ease;
        }

        @keyframes latido {
            0% { transform: scale(1); }
            25% { transform: scale(1.3); }
            50% { transform: scale(1.1); }
            75% { transform: scale(1.25); }
            100% { transform: scale(1); }
        }

        .producto-info-detalle h1 {
            color: #333;
            margin-bottom: 15px;
            font-size: 28px;
            line-height: 1.3;
        }

        .producto-precio-detalle {
            font-size: 32px;
            font-weight: bold;
            color: #2e7d32;
            margin-bottom: 15px;
        }

        .stock-info-detalle {
            font-size: 16px;
            margin-bottom: 15px;
            padding: 12px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .stock-bueno {
            background: #e8f5e8;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }

        .stock-bajo {
            background: #fff3e0;
            color: #ef6c00;
            border: 1px solid #ffcc80;
        }

        .sin-stock {
            background: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }

        .detalles-adicionales {
            margin-top: 30px;
        }

        .detalle-item {
            margin-bottom: 15px;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .detalle-item:last-child {
            border-bottom: none;
        }

        .detalle-item strong {
            color: #333;
            display: inline-block;
            margin-bottom: 5px;
        }

        .detalle-item h3 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 18px;
        }

        /* NOTIFICACIÓN ÚNICA */
        .toast-notification {
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
            border-left: 5px solid #00ff88;
            max-width: 350px;
            transform: translateX(120%);
        }

        .toast-notification.show {
            transform: translateX(0);
        }

        .image-controls {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin: 15px 0;
        }

        .image-controls button {
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .image-controls button:hover {
            background: #0056b3;
            transform: translateY(-2px);
        }

        .image-counter {
            text-align: center;
            margin: 10px 0;
            color: #666;
            font-size: 14px;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .btn-comprar {
            background: #28a745;
            color: white;
            border: none;
            padding: 16px 30px;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            flex: 1;
            transition: all 0.3s ease;
        }

        .btn-comprar:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }

        @media (max-width: 768px) {
            .producto-detalle {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .imagen-principal {
                height: 300px;
            }
            
            .btn-favorito-detalle,
            .btn-comprar {
                width: 100%;
                max-width: 100%;
            }
            
            .action-buttons {
                flex-direction: column;
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
            .container {
                padding: 15px;
            }
            
            .producto-info-detalle h1 {
                font-size: 24px;
            }
            
            .producto-precio-detalle {
                font-size: 28px;
            }
            
            .imagen-principal {
                height: 250px;
            }
            
            .miniatura {
                width: 60px;
                height: 60px;
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
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(52, 131, 250, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(52, 131, 250, 0); }
            100% { box-shadow: 0 0 0 0 rgba(52, 131, 250, 0); }
        }

        .nuevo-favorito {
            animation: pulse 1s ease;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <h1>Ecommerce Agave</h1>
        <p>Detalles del producto</p>
    </header>

    <!-- Mostrar bienvenida al usuario si está autenticado -->
    @auth
    <div class="user-welcome">
        <p>¡Hola {{ Auth::user()->vNombre }}! 👋</p>
    </div>
    @endauth

    <!-- Navbar -->
    <nav class="navbar">
        <div class="nav-container">
            <ul class="nav-links">
                <li><a href="{{ route('home') }}">Inicio</a></li>
                <li><a href="{{ route('busqueda.resultados') }}">Todos los Productos</a></li>
                <li>
                    @auth
                        <a href="{{ route('favoritos.index') }}" style="color: #dc3545; font-weight: bold;">❤️ Mis Favoritos</a>
                    @else
                        <a href="{{ route('login') }}" style="color: #dc3545; font-weight: bold;">❤️ Mis Favoritos</a>
                    @endauth
                </li>
                @auth
                    <li><a href="{{ route('carrito.index') }}">Mi Carrito</a></li>
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

            <!-- Barra de búsqueda -->
            <div class="barra-busqueda-principal">
                <form action="{{ route('busqueda.resultados') }}" method="GET">
                    <input type="text" name="q" placeholder="Buscar productos" 
                           value="{{ request('q') }}" autocomplete="off">
                    <button type="submit">Buscar</button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="container">
        <a href="javascript:history.back()" class="back-btn">← Volver</a>
        
        <div class="producto-detalle">
            <div class="imagenes-container">
                @if(count($producto->imagenes) > 0)
                    <div>
                        <img id="mainImage" src="{{ $producto->imagenes[0] }}" 
                             alt="{{ $producto->vNombre }}" class="imagen-principal">
                        
                        @if(count($producto->imagenes) > 1)
                            <div class="image-counter">
                                <span id="currentImage">1</span> / <span id="totalImages">{{ count($producto->imagenes) }}</span>
                            </div>
                            <div class="image-controls">
                                <button onclick="changeImage(-1)">← Anterior</button>
                                <button onclick="changeImage(1)">Siguiente →</button>
                            </div>
                        @endif
                    </div>
                    
                    @if(count($producto->imagenes) > 1)
                        <div class="miniaturas">
                            @foreach($producto->imagenes as $index => $imagen)
                                <img src="{{ $imagen }}" 
                                     alt="{{ $producto->vNombre }} - Imagen {{ $index + 1 }}"
                                     class="miniatura {{ $index === 0 ? 'activa' : '' }}"
                                     onclick="selectImage({{ $index }})">
                            @endforeach
                        </div>
                    @endif
                @else
                    <div style="text-align: center; padding: 60px;">
                        <div style="font-size: 48px; color: #6c757d; margin-bottom: 15px;">🛒</div>
                        <p style="color: #6c757d;">No hay imágenes disponibles</p>
                    </div>
                @endif
            </div>

            <div class="producto-info-detalle">
                <h1>{{ $producto->vNombre }}</h1>
                
                <div class="producto-precio-detalle">
                    @if($producto->tieneDescuento())
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 5px;">
                            <span style="text-decoration: line-through; color: #6c757d; font-size: 22px;">
                                ${{ number_format($producto->dPrecio_compra, 2) }}
                            </span>
                            <span style="color: #dc3545; font-size: 16px; font-weight: bold; background: #ffebee; padding: 4px 8px; border-radius: 4px;">
                                -{{ $producto->porcentajeDescuento() }}%
                            </span>
                        </div>
                    @endif
                    ${{ number_format($producto->dPrecio_venta, 2) }}
                </div>

                <div class="stock-info-detalle {{ $producto->iStock > 10 ? 'stock-bueno' : ($producto->iStock > 0 ? 'stock-bajo' : 'sin-stock') }}">
                    @if($producto->iStock > 10)
                        ✅ En stock ({{ $producto->iStock }} unidades disponibles)
                    @elseif($producto->iStock > 0)
                        ⚠️ Stock bajo (Solo {{ $producto->iStock }} unidades disponibles)
                    @else
                        ❌ Sin stock (Próximamente)
                    @endif
                </div>

                <div style="background: #e3f2fd; padding: 12px; border-radius: 6px; margin: 15px 0; border: 1px solid #bbdefb;">
                    <div style="display: flex; align-items: center; gap: 8px; color: #1976d2; font-weight: bold;">
                        📦 <span>Envío gratis a todo el país</span>
                    </div>
                </div>

                <button class="btn-favorito-detalle {{ $producto->esFavorito() ? 'activo' : '' }}" 
                        onclick="toggleFavoritoDetalle(this, {{ $producto->id_producto }})"
                        id="btn-favorito-{{ $producto->id_producto }}">
                    <span class="btn-icon">{{ $producto->esFavorito() ? '❤️' : '🤍' }}</span>
                    <span class="btn-text">
                        {{ $producto->esFavorito() ? 'En tu lista de deseos' : 'Añadir a la lista de deseos' }}
                    </span>
                </button>

                <div class="action-buttons">
                    <form action="{{ route('carrito.store', $producto->id_producto) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-comprar">🛒 Comprar Ahora</button>
                </form>
                </div>

                <div class="detalles-adicionales">
                    <div class="detalle-item">
                        <strong>Código de barras:</strong> 
                        <span style="background: #f8f9fa; padding: 4px 8px; border-radius: 4px; font-family: monospace;">
                            {{ $producto->vCodigo_barras }}
                        </span>
                    </div>
                    <div class="detalle-item">
                        <strong>Categoría:</strong> 
                        <span style="background: #e9ecef; padding: 4px 12px; border-radius: 4px;">
                            {{ $producto->categoria->vNombre ?? 'Sin categoría' }}
                        </span>
                    </div>
                    <div class="detalle-item">
                        <strong>Marca:</strong> 
                        <span style="background: #e9ecef; padding: 4px 12px; border-radius: 4px;">
                            {{ $producto->marca->vNombre ?? 'Sin marca' }}
                        </span>
                    </div>

                    @if($producto->etiquetas->count() > 0)
                        <div class="detalle-item">
                            <strong>Etiquetas:</strong><br>
                            <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-top: 8px;">
                                @foreach($producto->etiquetas as $etiqueta)
                                    <span style="background: #007bff; color: white; padding: 6px 12px; border-radius: 4px; font-size: 14px;">
                                        {{ $etiqueta->vNombre }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($producto->tDescripcion_corta)
                        <div class="detalle-item">
                            <h3>Descripción</h3>
                            <p style="line-height: 1.6; color: #555;">{{ $producto->tDescripcion_corta }}</p>
                        </div>
                    @endif

                    @if($producto->tDescripcion_larga)
                        <div class="detalle-item">
                            <h3>Información detallada</h3>
                            <p style="line-height: 1.6; color: #555; white-space: pre-line;">{{ $producto->tDescripcion_larga }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para login - ESTILO MERCADO LIBRE -->
    <div class="overlay-login" id="overlayLogin">
        <div class="modal-login">
            <h3>¡Inicia sesión para guardar favoritos! ❤️</h3>
            <p>Para agregar productos a tu lista de deseos, necesitas tener una cuenta.</p>
            
            <div class="modal-buttons">
                <a href="{{ route('login') }}" class="btn-modal btn-primary-modal">Iniciar Sesión</a>
                <a href="{{ route('usuarios.create') }}" class="btn-modal btn-secondary-modal">Crear Cuenta</a>
                <button class="btn-modal btn-close-modal" onclick="cerrarModalLogin()">Seguir comprando</button>
            </div>
        </div>
    </div>

    <script>
        // VARIABLE GLOBAL ÚNICA para controlar la notificación
        let currentToast = null;
        let toastTimeout = null;

        let currentImageIndex = 0;
        const totalImages = {{ count($producto->imagenes) }};
        const images = @json($producto->imagenes);

        function changeImage(direction) {
            currentImageIndex += direction;
            
            if (currentImageIndex < 0) {
                currentImageIndex = totalImages - 1;
            } else if (currentImageIndex >= totalImages) {
                currentImageIndex = 0;
            }
            
            updateMainImage();
        }

        function selectImage(index) {
            currentImageIndex = index;
            updateMainImage();
        }

        function updateMainImage() {
            document.getElementById('mainImage').src = images[currentImageIndex];
            document.getElementById('currentImage').textContent = currentImageIndex + 1;
            
            document.querySelectorAll('.miniatura').forEach((thumb, index) => {
                if (index === currentImageIndex) {
                    thumb.classList.add('activa');
                } else {
                    thumb.classList.remove('activa');
                }
            });
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft') {
                changeImage(-1);
            } else if (e.key === 'ArrowRight') {
                changeImage(1);
            }
        });

        // FUNCIÓN PRINCIPAL MEJORADA - SOLO UNA NOTIFICACIÓN
        function toggleFavoritoDetalle(button, productoId) {
            if (button.disabled) return;
            
            // Bloquear botón inmediatamente
            button.disabled = true;
            
            @if(!Auth::check())
                window.location.href = '{{ route("login") }}?from_favoritos=true&redirect=' + encodeURIComponent(window.location.href);
                return;
            @endif

            const esFavorito = button.classList.contains('activo');
            const iconSpan = button.querySelector('.btn-icon');
            const textSpan = button.querySelector('.btn-text');
            
            // Animación simple
            button.style.transform = 'scale(0.95)';
            
            // 1. ELIMINAR NOTIFICACIÓN ANTERIOR SI EXISTE
            removeExistingToast();
            
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
                        button.classList.add('activo');
                        iconSpan.textContent = '❤️';
                        textSpan.textContent = 'En tu lista de deseos';
                        
                        // 2. MOSTRAR SOLO UNA NOTIFICACIÓN - 3 SEGUNDOS PARA AMBOS
                        showSingleToast('Producto agregado a favoritos ✅', 3000);
                        
                    } else {
                        button.classList.remove('activo');
                        iconSpan.textContent = '🤍';
                        textSpan.textContent = 'Añadir a la lista de deseos';
                        
                        // 3. MOSTRAR SOLO UNA NOTIFICACIÓN - 3 SEGUNDOS PARA AMBOS
                        showSingleToast('Producto eliminado de favoritos ❌', 3000);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showSingleToast('Error de conexión ❌', 3000);
            })
            .finally(() => {
                setTimeout(() => {
                    button.disabled = false;
                    button.style.transform = '';
                }, 300);
            });
        }

        // FUNCIÓN PARA ELIMINAR NOTIFICACIONES EXISTENTES
        function removeExistingToast() {
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
            
            // Eliminar también cualquier otro toast que pueda existir
            const allToasts = document.querySelectorAll('.toast-notification');
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
        function showSingleToast(message, duration = 3000) {
            // 1. Eliminar notificación anterior
            removeExistingToast();
            
            // 2. Crear nueva notificación
            const toast = document.createElement('div');
            toast.className = 'toast-notification';
            toast.innerHTML = `
                <span style="font-size: 20px;">${message.includes('✅') ? '✅' : '❌'}</span>
                <span>${message.replace('✅', '').replace('❌', '').trim()}</span>
            `;
            
            document.body.appendChild(toast);
            currentToast = toast;
            
            // 3. Mostrar con animación
            setTimeout(() => {
                toast.classList.add('show');
            }, 10);
            
            // 4. Configurar para eliminar después del tiempo especificado (3 SEGUNDOS PARA AMBOS)
            toastTimeout = setTimeout(() => {
                if (currentToast === toast) {
                    toast.classList.remove('show');
                    setTimeout(() => {
                        if (toast.parentNode) {
                            toast.parentNode.removeChild(toast);
                        }
                        if (currentToast === toast) {
                            currentToast = null;
                        }
                    }, 400);
                }
            }, duration);
        }

        // Eliminar código redundante de localStorage
        document.addEventListener('DOMContentLoaded', function() {
            // Limpiar localStorage al cargar
            localStorage.removeItem('last_favorito_action');
            localStorage.removeItem('last_favorito_id');
            localStorage.removeItem('last_favorito_time');
            localStorage.removeItem('favorito_removed');
            localStorage.removeItem('favorito_removed_time');
            localStorage.removeItem('favorito_added');
            localStorage.removeItem('favorito_added_time');
        });

        function agregarAlCarrito(productoId) {
            showSingleToast('Producto agregado al carrito 🛒', 3000);
        }
    </script>
</body>
</html>