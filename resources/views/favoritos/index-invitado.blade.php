<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Favoritos (Invitado) - Ecommerce Agave</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            font-size: 24px;
            margin-bottom: 5px;
        }

        header p {
            font-size: 14px;
            opacity: 0.9;
        }

        .guest-banner {
            background: linear-gradient(135deg, #ff6b6b, #ff4757);
            color: white;
            padding: 15px 0;
            text-align: center;
            border-bottom: 1px solid #ff6b6b;
        }

        .guest-banner p {
            margin: 0;
            font-weight: bold;
            font-size: 16px;
        }

        .guest-banner a {
            color: white;
            text-decoration: underline;
            font-weight: bold;
        }

        .guest-banner a:hover {
            text-decoration: none;
        }

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
            flex-wrap: wrap;
        }

        .nav-links li a {
            color: #495057;
            text-decoration: none;
            font-weight: bold;
        }

        .nav-links li a:hover {
            text-decoration: underline;
        }

        .nav-links li a.active {
            color: #764ba2;
            border-bottom: 2px solid #764ba2;
        }

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

        .guest-info {
            background: #e7f1ff;
            border-left: 4px solid #007bff;
            padding: 15px 20px;
            margin-bottom: 25px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
        }

        .guest-info p {
            margin: 0;
            color: #0056b3;
            font-weight: 500;
        }

        .guest-info .btn-migrar {
            background: #28a745;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .guest-info .btn-migrar:hover {
            background: #218838;
            transform: translateY(-2px);
        }

        .favoritos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

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

        .favorito-imagen {
            width: 100%;
            height: 100%;
            object-fit: contain;
            transition: transform 0.5s ease;
            background-color: #f8f9fa;
            padding: 10px;
        }

        .favorito-card:hover .favorito-imagen {
            transform: scale(1.02);
        }

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
            height: 46px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
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
            flex-wrap: wrap;
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
            min-width: 120px;
        }

        .btn-primary {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(45deg, #5a67d8, #6b46c1);
            transform: translateY(-2px);
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-success:hover {
            background: #218838;
            transform: translateY(-2px);
        }

        .btn-danger {
            background: #ff4757;
            color: white;
        }

        .btn-danger:hover {
            background: #ff2e43;
            transform: translateY(-2px);
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
        }

        .badge-invitado {
            position: absolute;
            top: 15px;
            right: 60px;
            background: #ff6b6b;
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
            max-width: 350px;
        }

        .single-notification.show {
            transform: translateX(0);
        }

        .single-notification.error {
            background: #e74c3c;
        }

        .single-notification.success {
            background: #2ecc71;
        }

        .single-notification.info {
            background: #3498db;
        }

        .toast-icon {
            font-size: 24px;
            line-height: 1;
        }

        .toast-message {
            flex: 1;
            line-height: 1.4;
        }

        @media (max-width: 768px) {
            .favoritos-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }
            
            .page-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .guest-info {
                flex-direction: column;
                text-align: center;
            }
            
            .nav-links {
                gap: 15px;
            }
            
            .favorito-actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .favoritos-grid {
                grid-template-columns: 1fr;
            }
            
            .nav-links {
                flex-direction: column;
                gap: 10px;
                align-items: center;
            }
            
            .nav-links li a {
                display: block;
                padding: 5px 0;
            }
            
            .badge-variacion {
                font-size: 9px;
                padding: 2px 6px;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Ecommerce Agave</h1>
        <p>Mis Favoritos (Modo Invitado)</p>
    </header>

    <div class="guest-banner">
        <div class="nav-container">
            <p>
                <i class="fas fa-info-circle"></i> 
                Estás navegando como invitado. Tus favoritos se guardan en este navegador.
                <a href="{{ route('login') }}">Inicia sesión</a> o 
                <a href="{{ route('usuarios.create') }}">regístrate</a> para guardarlos permanentemente.
            </p>
        </div>
    </div>

    <nav class="navbar">
        <div class="nav-container">
            <ul class="nav-links">
                <li><a href="{{ route('home') }}">Inicio</a></li>
                <li><a href="{{ route('busqueda.resultados') }}">Todos los Productos</a></li>
                <li><a href="{{ route('favoritos.invitado.index') }}" class="active" style="color: #dc3545; font-weight: bold;">❤️ Mis Favoritos</a></li>
                <li><a href="{{ route('login') }}">Ingresar</a></li>
                <li><a href="{{ route('usuarios.create') }}">Crear Cuenta</a></li>
            </ul>
        </div>
    </nav>

    <div class="main-container">
        <div class="page-header">
            <div class="page-title">
                <div class="heart-icon">❤️</div>
                <h1>Mi Lista de Deseos (Invitado)</h1>
            </div>
            <div>
                <a href="{{ route('busqueda.resultados') }}" class="btn btn-primary" style="display: inline-block; padding: 12px 25px;">
                    <span>🛍️</span> Seguir Comprando
                </a>
            </div>
        </div>

        <div class="guest-info">
            <p>
                <i class="fas fa-shopping-bag"></i> 
                Tienes <strong>{{ $favoritos->count() }}</strong> producto(s) guardados en este navegador
            </p>
            <div>
                <a href="{{ route('login') }}" class="btn-migrar" style="margin-right: 10px;">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                </a>
                <a href="{{ route('usuarios.create') }}" class="btn-migrar" style="background: #007bff;">
                    <i class="fas fa-user-plus"></i> Registrarse
                </a>
            </div>
        </div>

        <div class="favoritos-count-container">
            <p>Favoritos temporales: <span class="favoritos-count">{{ $favoritos->count() }}</span></p>
        </div>

        @if($favoritos->count() > 0)
            <div class="favoritos-grid">
                @foreach($favoritos as $favorito)
                    @php
                        // Determinar si es variación o producto
                        $esVariacion = !is_null($favorito->id_variacion);
                        
                        if ($esVariacion) {
                            $variacion = DB::table('tbl_producto_variaciones')
                                ->where('id_variacion', $favorito->id_variacion)
                                ->first();
                                
                            $producto = DB::table('tbl_productos')
                                ->where('id_producto', $favorito->id_producto)
                                ->first();
                                
                            $nombreProducto = $producto->vNombre;
                            $precio = $variacion->dPrecio;
                            $precioDescuento = $variacion->dPrecio_descuento;
                            $tieneDescuento = $variacion->bTiene_descuento && $variacion->dPrecio_descuento > 0;
                            $stock = $variacion->iStock;
                            
                            $atributos = DB::table('tbl_variacion_atributos as va')
                                ->join('tbl_atributo_valores as av', 'va.id_atributo_valor', '=', 'av.id_atributo_valor')
                                ->where('va.id_variacion', $favorito->id_variacion)
                                ->select('av.vValor')
                                ->get();
                                
                            $atributosTexto = $atributos->pluck('vValor')->implode(' - ');
                            $nombreCompleto = $nombreProducto . ($atributosTexto ? ' - ' . $atributosTexto : '');
                            
                            $imagenPrincipal = $variacion->vImagen ? Storage::url($variacion->vImagen) : null;
                            $imagenes = [];
                            if ($imagenPrincipal) $imagenes[] = $imagenPrincipal;
                            
                            $url = route('productos.show.public', [$producto->id_producto, 'variacion' => $favorito->id_variacion]);
                            
                            $impuesto = DB::table('tbl_impuestos')->where('id_impuesto', $variacion->id_impuesto)->first();
                            $porcentajeImpuesto = $impuesto ? $impuesto->dPorcentaje : 0;
                        } else {
                            $producto = DB::table('tbl_productos')
                                ->where('id_producto', $favorito->id_producto)
                                ->first();
                                
                            $nombreCompleto = $producto->vNombre;
                            $precio = $producto->dPrecio_venta;
                            $precioDescuento = $producto->dPrecio_descuento;
                            $tieneDescuento = $producto->bTiene_descuento && $producto->dPrecio_descuento > 0;
                            $stock = $producto->iStock;
                            
                            $imagenes = [];
                            if ($producto->vImagen_principal) {
                                $imagenes[] = Storage::url($producto->vImagen_principal);
                            }
                            
                            $url = route('productos.show.public', $producto->id_producto);
                            
                            $impuestosProducto = DB::table('tbl_producto_impuestos')
                                ->join('tbl_impuestos', 'tbl_producto_impuestos.id_impuesto', '=', 'tbl_impuestos.id_impuesto')
                                ->where('tbl_producto_impuestos.id_producto', $producto->id_producto)
                                ->select('tbl_impuestos.dPorcentaje')
                                ->get();
                            $porcentajeImpuesto = $impuestosProducto->sum('dPorcentaje');
                        }
                        
                        $precioBase = $tieneDescuento ? $precioDescuento : $precio;
                        $precioFinal = $precioBase + ($precioBase * $porcentajeImpuesto / 100);
                        $porcentajeDescuento = $tieneDescuento ? round((($precio - $precioDescuento) / $precio) * 100) : 0;
                        
                        $stockClass = $stock > 10 ? 'stock-disponible' : ($stock > 0 ? 'stock-bajo' : 'sin-stock');
                        $stockMessage = $stock > 10 
                            ? '✅ En stock (' . $stock . ' unidades)' 
                            : ($stock > 0 
                                ? '⚠️ Solo ' . $stock . ' disponibles' 
                                : '❌ Agotado');
                        
                        $cardId = $esVariacion 
                            ? 'favorito-temp-var-' . $favorito->id_variacion . '-prod-' . $favorito->id_producto
                            : 'favorito-temp-prod-' . $favorito->id_producto;
                    @endphp
                    
                    <div class="favorito-card" id="{{ $cardId }}" data-tipo="{{ $esVariacion ? 'variacion' : 'producto' }}" data-id="{{ $esVariacion ? $favorito->id_variacion : $favorito->id_producto }}" data-producto-padre="{{ $favorito->id_producto }}">
                        <div class="favorito-img-container">
                            <button class="eliminar-favorito-btn" 
                                    onclick="event.stopPropagation(); eliminarFavoritoTemporal({{ $favorito->id_producto }}, {{ $esVariacion ? $favorito->id_variacion : 'null' }})"
                                    title="Eliminar de favoritos temporales">
                                ❌
                            </button>

                            <div class="badge-invitado">
                                <i class="fas fa-user"></i> Invitado
                            </div>

                            @if($tieneDescuento && $porcentajeDescuento > 0)
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
                                <img src="{{ $imagenes[0] }}" alt="{{ $nombreCompleto }}" class="favorito-imagen" onerror="this.src='https://via.placeholder.com/200x200?text=Sin+imagen'">
                            @else
                                <div style="display: flex; align-items: center; justify-content: center; height: 100%; background: linear-gradient(45deg, #f8f9fa, #e9ecef);">
                                    <span style="font-size: 48px; color: #adb5bd;">🛒</span>
                                </div>
                            @endif
                        </div>

                        <div class="favorito-info">
                            <h3 class="favorito-nombre" onclick="window.location.href='{{ $url }}'">
                                {{ $nombreCompleto }}
                            </h3>
                            
                            <div class="favorito-precio">
                                @if($tieneDescuento && $porcentajeDescuento > 0)
                                    <span style="text-decoration: line-through; color: #999; font-size: 16px; margin-right: 8px;">
                                        ${{ number_format($precio, 2) }}
                                    </span>
                                @endif
                                ${{ number_format($precioFinal, 2) }}
                            </div>

                            <div class="favorito-stock {{ $stockClass }}">
                                {!! $stockMessage !!}
                            </div>

                            <div class="favorito-actions">
                                <a href="javascript:void(0)" 
                                   class="btn btn-success" 
                                   onclick="event.stopPropagation(); agregarAlCarrito({{ $favorito->id_producto }}, {{ $esVariacion ? $favorito->id_variacion : 'null' }})">
                                    <span>🛒</span> Agregar al carrito
                                </a>
                                <a href="{{ $url }}" class="btn btn-primary" onclick="event.stopPropagation();">
                                    <span>👁️</span> Ver Producto
                                </a>
                                <button class="btn btn-danger" onclick="event.stopPropagation(); eliminarFavoritoTemporal({{ $favorito->id_producto }}, {{ $esVariacion ? $favorito->id_variacion : 'null' }})">
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
                <p>Como invitado, puedes guardar productos en este navegador. Inicia sesión para guardarlos permanentemente.</p>
                <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                    <a href="{{ route('login') }}" class="btn btn-primary" style="display: inline-flex; width: auto; padding: 12px 25px;">
                        <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                    </a>
                    <a href="{{ route('usuarios.create') }}" class="btn btn-success" style="display: inline-flex; width: auto; padding: 12px 25px;">
                        <i class="fas fa-user-plus"></i> Registrarse
                    </a>
                    <a href="{{ route('busqueda.resultados') }}" class="btn btn-primary" style="display: inline-flex; width: auto; padding: 12px 25px;">
                        <span>🛍️</span> Explorar Productos
                    </a>
                </div>
            </div>
        @endif
    </div>

    <script>
        // Configuración global
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        let notificacionTimeout = null;
        let notificacionActual = null;

        // Función para eliminar notificación
        function removerNotificacion() {
            if (notificacionActual) {
                notificacionActual.classList.remove('show');
                setTimeout(() => {
                    if (notificacionActual?.parentNode) {
                        notificacionActual.parentNode.removeChild(notificacionActual);
                    }
                    notificacionActual = null;
                }, 300);
            }
            if (notificacionTimeout) {
                clearTimeout(notificacionTimeout);
                notificacionTimeout = null;
            }
        }

        // Función para mostrar notificación
        function mostrarNotificacion(mensaje, tipo = 'success') {
            removerNotificacion();
            
            const notificacion = document.createElement('div');
            notificacion.className = `single-notification ${tipo}`;
            
            let emoji = tipo === 'success' ? '✅' : tipo === 'error' ? '❌' : 'ℹ️';
            
            notificacion.innerHTML = `
                <span class="toast-icon">${emoji}</span>
                <span class="toast-message">${mensaje}</span>
            `;
            
            document.body.appendChild(notificacion);
            notificacionActual = notificacion;
            
            setTimeout(() => notificacion.classList.add('show'), 10);
            
            notificacionTimeout = setTimeout(() => {
                if (notificacion.classList.contains('show')) {
                    notificacion.classList.remove('show');
                    setTimeout(() => {
                        if (notificacion.parentNode) {
                            notificacion.parentNode.removeChild(notificacion);
                        }
                        notificacionActual = null;
                        notificacionTimeout = null;
                    }, 400);
                }
            }, 3000);
        }

        // Función para eliminar favorito temporal (CORREGIDA)
        function eliminarFavoritoTemporal(productoId, variacionId = null) {
            // Encontrar la tarjeta
            let selector = '';
            if (variacionId && variacionId !== 'null') {
                selector = `[data-id="${variacionId}"][data-tipo="variacion"]`;
            } else {
                selector = `[data-id="${productoId}"][data-tipo="producto"]`;
            }
            
            const card = document.querySelector(selector) || 
                        document.querySelector(`[id*="prod-${productoId}"]`);
            
            if (card) {
                card.style.opacity = '0.5';
                card.style.transform = 'scale(0.95)';
            }

            // Preparar datos para enviar
            const datos = {
                id_producto: productoId
            };
            
            if (variacionId && variacionId !== 'null') {
                datos.id_variacion = variacionId;
            }

            // Hacer la petición fetch
            fetch('{{ route("favoritos.invitado.destroy") }}', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(datos)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    if (card) {
                        card.style.transform = 'scale(0.8)';
                        card.style.opacity = '0';
                        setTimeout(() => {
                            if (card?.parentNode) {
                                card.remove();
                                actualizarContadorFavoritos();
                                
                                if (document.querySelectorAll('.favorito-card').length === 0) {
                                    setTimeout(() => location.reload(), 500);
                                }
                            }
                        }, 300);
                    }
                    
                    mostrarNotificacion('Producto eliminado de favoritos', 'success');
                } else {
                    mostrarNotificacion('Error al eliminar', 'error');
                    if (card) {
                        card.style.opacity = '1';
                        card.style.transform = 'scale(1)';
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarNotificacion('Error de conexión', 'error');
                if (card) {
                    card.style.opacity = '1';
                    card.style.transform = 'scale(1)';
                }
            });
        }

        // Función para actualizar contador
        function actualizarContadorFavoritos() {
            const cards = document.querySelectorAll('.favorito-card').length;
            const contadorElement = document.querySelector('.favoritos-count');
            const contadorContainer = document.querySelector('.favoritos-count-container p');
            
            if (contadorElement) {
                contadorElement.textContent = cards;
            }
            
            if (contadorContainer) {
                contadorContainer.innerHTML = `Favoritos temporales: <span class="favoritos-count">${cards}</span>`;
            }
        }

        // Función para agregar al carrito
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
                        mostrarNotificacion('Producto agregado al carrito', 'success');
                    } else {
                        mostrarNotificacion(data.message || 'Error al agregar', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarNotificacion('Error de conexión', 'error');
                });
            @else
                const redirectUrl = new URL('{{ route("login") }}');
                redirectUrl.searchParams.set('from_carrito', 'true');
                redirectUrl.searchParams.set('redirect', window.location.href);
                window.location.href = redirectUrl.toString();
            @endauth
        }

        // Inicialización
        document.addEventListener('DOMContentLoaded', function() {
            // Prevenir propagación en botones
            const botones = document.querySelectorAll('.favorito-card button, .favorito-card a');
            botones.forEach(boton => {
                boton.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            });
            
            // Enfoque en búsqueda para escritorio
            if (window.innerWidth > 768) {
                const searchInput = document.querySelector('.barra-busqueda-principal input');
                if (searchInput) searchInput.focus();
            }
        });
    </script>
</body>
</html>