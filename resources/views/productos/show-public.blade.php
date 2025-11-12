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
            padding: 20px;
            background-color: #f5f5f5;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
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
        }

        .back-btn:hover {
            color: #007bff;
            border-color: #007bff;
        }

        .producto-detalle {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 20px;
        }

        /* Estilos para imágenes */
        .imagenes-container {
            position: relative;
        }

        .imagen-principal {
            width: 100%;
            height: 400px;
            object-fit: contain;
            border-radius: 8px;
            background: #f8f9fa;
        }

        .miniaturas {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            overflow-x: auto;
        }

        .miniatura {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
            cursor: pointer;
            border: 2px solid transparent;
        }

        .miniatura.activa {
            border-color: #007bff;
        }

        /* Botón de favoritos en página de producto - ESTILO MERCADO LIBRE */
        .btn-favorito-detalle {
            background: #fff;
            border: 2px solid #3483fa;
            color: #3483fa;
            padding: 12px 25px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            margin-top: 15px;
            width: 100%;
            justify-content: center;
        }

        .btn-favorito-detalle:hover {
            background: #f8f9fa;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(52,131,250,0.2);
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

        /* Información del producto */
        .producto-info-detalle h1 {
            color: #333;
            margin-bottom: 15px;
            font-size: 24px;
        }

        .producto-precio-detalle {
            font-size: 28px;
            font-weight: bold;
            color: #2e7d32;
            margin-bottom: 15px;
        }

        .stock-info-detalle {
            font-size: 16px;
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 4px;
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
            margin-top: 20px;
        }

        .detalle-item {
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .detalle-item strong {
            color: #333;
        }

        /* Overlay de login - ESTILO MERCADO LIBRE */
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
            font-size: 20px;
        }

        .modal-login p {
            margin-bottom: 25px;
            color: #666;
            font-size: 16px;
            line-height: 1.5;
        }

        .modal-buttons {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .btn-modal {
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
            font-size: 16px;
            text-align: center;
        }

        .btn-primary-modal {
            background: #3483fa;
            color: white;
        }

        .btn-primary-modal:hover {
            background: #2968c8;
        }

        .btn-secondary-modal {
            background: #fff;
            color: #3483fa;
            border: 2px solid #3483fa;
        }

        .btn-secondary-modal:hover {
            background: #f8f9fa;
        }

        .btn-close-modal {
            background: #fff;
            color: #666;
            border: 2px solid #dee2e6;
            margin-top: 10px;
        }

        .btn-close-modal:hover {
            background: #f8f9fa;
            border-color: #666;
        }

        /* Toast notifications */
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

        @media (max-width: 768px) {
            .producto-detalle {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .btn-favorito-detalle {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="javascript:history.back()" class="back-btn">← Volver</a>
        
        <div class="producto-detalle">
            <!-- Columna de imágenes -->
            <div class="imagenes-container">
                @if(count($producto->imagenes) > 0)
                    <div>
                        <img id="mainImage" src="{{ $producto->imagenes[0] }}" 
                             alt="{{ $producto->vNombre }}" class="imagen-principal">
                        
                        @if(count($producto->imagenes) > 1)
                            <div style="text-align: center; margin: 10px 0;">
                                <span id="currentImage">1</span> / <span id="totalImages">{{ count($producto->imagenes) }}</span>
                            </div>
                            <div style="text-align: center; margin: 10px 0;">
                                <button onclick="changeImage(-1)" style="padding: 8px 16px; margin: 0 5px;">←</button>
                                <button onclick="changeImage(1)" style="padding: 8px 16px; margin: 0 5px;">→</button>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Miniaturas -->
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
                    <div style="text-align: center; padding: 40px;">
                        <p>No hay imágenes disponibles</p>
                    </div>
                @endif
            </div>

            <!-- Columna de información -->
            <div class="producto-info-detalle">
                <h1>{{ $producto->vNombre }}</h1>
                
                <div class="producto-precio-detalle">
                    ${{ number_format($producto->dPrecio_venta, 2) }}
                </div>

                <div class="stock-info-detalle {{ $producto->iStock > 10 ? 'stock-bueno' : ($producto->iStock > 0 ? 'stock-bajo' : 'sin-stock') }}">
                    @if($producto->iStock > 10)
                        ✅ En stock ({{ $producto->iStock }} unidades)
                    @elseif($producto->iStock > 0)
                        ⚠️ Stock bajo ({{ $producto->iStock }} unidades)
                    @else
                        ❌ Sin stock
                    @endif
                </div>

                <!-- BOTÓN ÚNICO DE FAVORITOS - ESTILO MERCADO LIBRE -->
                <button class="btn-favorito-detalle {{ $producto->esFavorito() ? 'activo' : '' }}" 
                        onclick="toggleFavoritoDetalle(this, {{ $producto->id_producto }})">
                    {{ $producto->esFavorito() ? '❤️ En tu lista de deseos' : '🤍 Añadir a la lista de deseos' }}
                </button>

                <div class="detalles-adicionales">
                    <div class="detalle-item">
                        <strong>Código:</strong> {{ $producto->vCodigo_barras }}
                    </div>
                    <div class="detalle-item">
                        <strong>Categoría:</strong> {{ $producto->categoria->vNombre ?? 'N/A' }}
                    </div>
                    <div class="detalle-item">
                        <strong>Marca:</strong> {{ $producto->marca->vNombre ?? 'N/A' }}
                    </div>

                    @if($producto->etiquetas->count() > 0)
                        <div class="detalle-item">
                            <strong>Etiquetas:</strong><br>
                            @foreach($producto->etiquetas as $etiqueta)
                                <span style="background: #e9ecef; padding: 4px 8px; border-radius: 4px; margin: 2px; display: inline-block;">{{ $etiqueta->vNombre }}</span>
                            @endforeach
                        </div>
                    @endif

                    @if($producto->tDescripcion_corta)
                        <div class="detalle-item">
                            <h3>Descripción</h3>
                            <p>{{ $producto->tDescripcion_corta }}</p>
                        </div>
                    @endif

                    @if($producto->tDescripcion_larga)
                        <div class="detalle-item">
                            <h3>Información detallada</h3>
                            <p>{{ $producto->tDescripcion_larga }}</p>
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
            // Update main image
            document.getElementById('mainImage').src = images[currentImageIndex];
            
            // Update counter
            document.getElementById('currentImage').textContent = currentImageIndex + 1;
            
            // Update thumbnails
            document.querySelectorAll('.miniatura').forEach((thumb, index) => {
                if (index === currentImageIndex) {
                    thumb.classList.add('activa');
                } else {
                    thumb.classList.remove('activa');
                }
            });
        }

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft') {
                changeImage(-1);
            } else if (e.key === 'ArrowRight') {
                changeImage(1);
            }
        });

        // Función principal para toggle favoritos
        function toggleFavoritoDetalle(button, productoId) {
            const esFavorito = button.classList.contains('activo');
            
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
                        // Actualizar botón a estado activo
                        button.classList.add('activo');
                        button.innerHTML = '❤️ En tu lista de deseos';
                        showNotification('✅ Producto agregado a favoritos');
                    } else {
                        // Actualizar botón a estado inactivo
                        button.classList.remove('activo');
                        button.innerHTML = '🤍 Añadir a la lista de deseos';
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

        // Cerrar modal con ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                cerrarModalLogin();
            }
        });
    </script>
</body>
</html>