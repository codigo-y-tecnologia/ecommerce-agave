<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $producto->vNombre }} - Ecommerce Agave</title>
    <style>
        /* ... (Estilos sin cambios, son los mismos que proporcionaste) ... */
        * { margin: 0; padding: 0; box-sizing: border-box; }
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
        header h1 { font-size: clamp(1.5rem, 5vw, 2rem); padding: 0 15px; }
        header p { font-size: clamp(0.9rem, 3vw, 1rem); padding: 0 15px; color: #666; }
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
        .nav-links li a {
            color: #495057;
            text-decoration: none;
            font-weight: bold;
            font-size: clamp(0.85rem, 2.5vw, 1rem);
            white-space: nowrap;
        }
        .nav-links li a:hover { text-decoration: underline; }
        .nav-links li button { font-size: clamp(0.85rem, 2.5vw, 1rem); }
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
        .imagenes-container { position: relative; }
        .imagen-principal-container {
            height: 400px;
            display: flex;
            flex-direction: column;
            margin-bottom: 15px;
        }
        .imagen-wrapper {
            position: relative;
            height: 350px;
            background-color: #f8f9fa;
            border-radius: 8px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .imagen-principal {
            max-height: 340px;
            max-width: 100%;
            object-fit: contain;
            border-radius: 8px;
        }
        .image-controls {
            position: absolute;
            width: 100%;
            top: 50%;
            left: 0;
            transform: translateY(-50%);
            display: flex;
            justify-content: space-between;
            padding: 0 10px;
            pointer-events: none;
        }
        .image-controls button {
            pointer-events: auto;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: none;
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            cursor: pointer;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            opacity: 0.7;
        }
        .image-controls button:hover { opacity: 1; transform: scale(1.1); }
        .image-controls button:disabled { opacity: 0.3; cursor: not-allowed; transform: none; }
        .image-counter {
            text-align: center;
            margin: 10px 0;
            color: #666;
            font-size: 14px;
        }
        .miniaturas {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            overflow-x: auto;
            padding: 10px 0;
            scrollbar-width: thin;
        }
        .miniatura {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 6px;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }
        .miniatura:hover { border-color: #007bff; transform: scale(1.05); }
        .miniatura.activa {
            border-color: #007bff;
            box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.3);
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
        .precio-original {
            text-decoration: line-through;
            color: #6c757d;
            font-size: 22px;
            margin-right: 10px;
        }
        .descuento-badge {
            background: #dc3545;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: bold;
            display: inline-block;
            margin-left: 10px;
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
        .stock-bueno { background: #e8f5e8; color: #2e7d32; border: 1px solid #c8e6c9; }
        .stock-bajo { background: #fff3e0; color: #ef6c00; border: 1px solid #ffcc80; }
        .sin-stock { background: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }
        .variaciones-selector {
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
        .variaciones-selector h3 {
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 18px;
            color: #333;
        }
        .variacion-opcion {
            display: flex;
            align-items: center;
            padding: 10px;
            margin-bottom: 10px;
            border: 2px solid transparent;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            background: white;
        }
        .variacion-opcion:hover {
            border-color: #007bff;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .variacion-opcion.seleccionada {
            border-color: #007bff;
            background: #e3f2fd;
        }
        .variacion-imagen-mini {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
            margin-right: 15px;
            border: 1px solid #dee2e6;
            flex-shrink: 0;
        }
        .variacion-info { flex: 1; }
        .variacion-nombre { font-weight: bold; color: #333; margin-bottom: 5px; }
        .variacion-precio { color: #28a745; font-weight: bold; font-size: 16px; }
        .variacion-stock { font-size: 14px; color: #6c757d; }
        .variacion-descripcion {
            margin-top: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #007bff;
        }
        .detalles-adicionales { margin-top: 30px; }
        .detalle-item {
            margin-bottom: 15px;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .detalle-item:last-child { border-bottom: none; }
        .detalle-item strong { color: #333; display: inline-block; margin-bottom: 5px; }
        .detalle-item h3 { margin: 0 0 10px 0; color: #333; font-size: 18px; }
        .sku-badge {
            background: #f8f9fa;
            padding: 4px 12px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 14px;
            border: 1px solid #dee2e6;
        }
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
        .toast-notification.show { transform: translateX(0); }
        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 20px;
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
        .btn-favorito-detalle.activo {
            background: #3483fa;
            border-color: #3483fa;
            color: white;
        }
        .btn-favorito-detalle.activo:hover { background: #2968c8; border-color: #2968c8; }
        .btn-favorito-detalle .btn-icon { font-size: 22px; transition: transform 0.3s ease; }
        .btn-favorito-detalle:hover .btn-icon { transform: scale(1.2); }
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
            .producto-detalle { grid-template-columns: 1fr; gap: 20px; }
            .imagen-principal-container { height: 350px; }
            .imagen-wrapper { height: 300px; }
            .btn-favorito-detalle, .btn-comprar { width: 100%; max-width: 100%; }
            .action-buttons { flex-direction: column; }
            .barra-busqueda-principal input[type="text"] { width: 60%; }
            .nav-links { flex-wrap: wrap; gap: 15px; }
            .variacion-opcion { flex-direction: column; text-align: center; }
            .variacion-imagen-mini { margin-right: 0; margin-bottom: 10px; }
        }
        @media (max-width: 480px) {
            .container { padding: 15px; }
            .producto-info-detalle h1 { font-size: 24px; }
            .producto-precio-detalle { font-size: 28px; }
            .imagen-principal-container { height: 300px; }
            .imagen-wrapper { height: 250px; }
            .miniatura { width: 60px; height: 60px; }
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
            .barra-busqueda-principal form { flex-direction: column; }
            .image-controls button { width: 32px; height: 32px; font-size: 14px; }
        }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(52, 131, 250, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(52, 131, 250, 0); }
            100% { box-shadow: 0 0 0 0 rgba(52, 131, 250, 0); }
        }
        .nuevo-favorito { animation: pulse 1s ease; }
    </style>
</head>
<body>
    <!-- Header (SIN CAMBIOS) -->
    <header>
        <h1>Ecommerce Agave</h1>
        <p>Detalles del producto</p>
    </header>

    <!-- Bienvenida al usuario si está autenticado (SIN CAMBIOS) -->
    @auth
    <div class="user-welcome">
        <p>¡Hola {{ Auth::user()->vNombre }}! 👋</p>
    </div>
    @endauth

    <!-- Navbar (SIN CAMBIOS) -->
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
        
        @php
            // --- DATOS DEL PRODUCTO PADRE (SIN CAMBIOS) ---
            $imagenesProducto = $producto->imagenes ?? [];
            
            $productoData = [
                'sku' => $producto->vCodigo_barras,
                'precio' => (float)$producto->dPrecio_venta,
                'precio_oferta' => (float)($producto->dPrecio_oferta ?? 0),
                'tiene_oferta' => (bool)$producto->bTiene_oferta,
                'stock' => (int)$producto->iStock,
                'imagenes' => $imagenesProducto,
                'descripcion_corta' => $producto->tDescripcion_corta ?? ''
            ];

            // --- DATOS DE LAS VARIACIONES (SIN CAMBIOS) ---
            $variacionesData = [];
            foreach ($producto->variaciones as $var) {
                $atributosTexto = [];
                foreach($var->atributos as $atributoRel) {
                    if($atributoRel->atributo && $atributoRel->valor) {
                        $atributosTexto[] = $atributoRel->atributo->vNombre . ': ' . $atributoRel->valor->vValor;
                    }
                }
                $variacionesData[$var->id_variacion] = [
                    'id' => $var->id_variacion,
                    'sku' => $var->vSKU,
                    'precio' => (float)$var->dPrecio,
                    'precio_oferta' => (float)($var->dPrecio_oferta ?? 0),
                    'tiene_oferta' => (bool)$var->bTiene_oferta,
                    'stock' => (int)$var->iStock,
                    'atributos_texto' => $atributosTexto,
                    'imagenes' => $var->imagenes ?? [],
                    'descripcion' => $var->tDescripcion ?? ''
                ];
            }
        @endphp
        
        <div class="producto-detalle">
            <!-- SECCIÓN DE IMÁGENES DEL PRODUCTO/VARIACIÓN ACTIVA (HTML SIN CAMBIOS) -->
            <div class="imagenes-container">
                <div class="imagen-principal-container">
                    <div class="imagen-wrapper">
                        <img id="mainImage" 
                             src="{{ !empty($imagenesProducto) ? $imagenesProducto[0] : 'https://via.placeholder.com/400x400?text=Sin+Imagen' }}" 
                             alt="{{ $producto->vNombre }}" class="imagen-principal"
                             onerror="this.onerror=null; this.src='https://via.placeholder.com/400x400?text=Sin+Imagen';">
                        
                        <div class="image-controls">
                            <button onclick="cambiarImagen(-1)" {{ count($imagenesProducto) <= 1 ? 'disabled' : '' }}>←</button>
                            <button onclick="cambiarImagen(1)" {{ count($imagenesProducto) <= 1 ? 'disabled' : '' }}>→</button>
                        </div>
                    </div>
                    
                    <div class="image-counter">
                        <span id="imagen-actual">1</span> / <span id="total-imagenes">{{ count($imagenesProducto) }}</span>
                    </div>
                </div>
                
                <div class="miniaturas" id="miniaturas-container">
                    @foreach($imagenesProducto as $index => $imgUrl)
                        <img src="{{ $imgUrl }}" 
                             alt="{{ $producto->vNombre }} - Imagen {{ $index + 1 }}"
                             class="miniatura {{ $index === 0 ? 'activa' : '' }}"
                             onclick="seleccionarImagen({{ $index }})"
                             onerror="this.onerror=null; this.src='https://via.placeholder.com/70x70?text=Error';">
                    @endforeach
                </div>
            </div>

            <!-- INFORMACIÓN DEL PRODUCTO (HTML SIN CAMBIOS, excepto el id del nombre) -->
            <div class="producto-info-detalle">
                <h1 id="producto-nombre">{{ $producto->vNombre }}</h1>
                
                <div class="producto-precio-detalle" id="precio-container">
                    @if($producto->tieneDescuentoActivo())
                        <span class="precio-original" id="precio-original">${{ number_format($producto->dPrecio_venta, 2) }}</span>
                        <span class="descuento-badge" id="descuento-badge">-{{ $producto->porcentajeDescuento }}%</span>
                        <div id="precio-actual">${{ number_format($producto->dPrecio_oferta, 2) }}</div>
                    @else
                        <div id="precio-actual">${{ number_format($producto->dPrecio_venta, 2) }}</div>
                        <span id="precio-original" style="display: none;"></span>
                        <span id="descuento-badge" style="display: none;"></span>
                    @endif
                </div>

                <div class="stock-info-detalle" id="stock-container">
                    <span id="stock-texto">
                        @if($producto->iStock > 10)
                            ✅ En stock ({{ $producto->iStock }} unidades disponibles)
                        @elseif($producto->iStock > 0)
                            ⚠️ Stock bajo (Solo {{ $producto->iStock }} unidades disponibles)
                        @else
                            ❌ Sin stock (Próximamente)
                        @endif
                    </span>
                </div>

                <div style="margin: 15px 0;">
                    <strong>Código de barras:</strong> 
                    <span class="sku-badge" id="sku-texto">{{ $producto->vCodigo_barras }}</span>
                </div>

                <!-- DESCRIPCIÓN DE VARIACIÓN SELECCIONADA -->
                <div id="variacion-descripcion-container" class="variacion-descripcion" style="display: none;">
                    <p id="variacion-descripcion-texto" style="margin: 0;"></p>
                </div>

                <!-- SELECTOR DE VARIACIONES CON TOGGLE (HTML SIN CAMBIOS) -->
                @if($producto->tieneVariaciones() && $producto->variaciones->count() > 0)
                    <div class="variaciones-selector">
                        <h3><i class="fas fa-cubes"></i> Selecciona una variación:</h3>
                        <div id="variaciones-container">
                            @foreach($producto->variaciones as $variacion)
                                @php
                                    $imagenVariacion = $variacion->imagen_principal;
                                    $stockClase = $variacion->iStock > 10 ? 'bueno' : ($variacion->iStock > 0 ? 'bajo' : 'sin-stock');
                                    $precioActual = $variacion->ofertaVigente() ? $variacion->dPrecio_oferta : $variacion->dPrecio;
                                    $atributosTexto = [];
                                    foreach($variacion->atributos as $atributoRel) {
                                        if($atributoRel->atributo && $atributoRel->valor) {
                                            $atributosTexto[] = $atributoRel->atributo->vNombre . ': ' . $atributoRel->valor->vValor;
                                        }
                                    }
                                @endphp
                                <div class="variacion-opcion" 
                                     onclick="toggleVariacion({{ $variacion->id_variacion }})"
                                     data-variacion-id="{{ $variacion->id_variacion }}"
                                     data-sku="{{ $variacion->vSKU }}"
                                     data-precio="{{ $variacion->dPrecio }}"
                                     data-precio-oferta="{{ $variacion->dPrecio_oferta ?? 0 }}"
                                     data-tiene-oferta="{{ $variacion->bTiene_oferta ? 'true' : 'false' }}"
                                     data-stock="{{ $variacion->iStock }}"
                                     data-descripcion="{{ $variacion->tDescripcion ?? '' }}"
                                     data-imagenes='@json($variacion->imagenes ?? [])'
                                     data-atributos='@json($atributosTexto)'>
                                    
                                    @if($imagenVariacion)
                                        <img src="{{ $imagenVariacion }}" alt="Variación" class="variacion-imagen-mini"
                                             onerror="this.onerror=null; this.src='https://via.placeholder.com/60x60?text=Var';">
                                    @else
                                        <div class="variacion-imagen-mini" style="background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                                            <span style="font-size: 24px;">🔄</span>
                                        </div>
                                    @endif
                                    
                                    <div class="variacion-info">
                                        <div class="variacion-nombre">
                                            {{ implode(' | ', $atributosTexto) }}
                                        </div>
                                        <div class="variacion-precio">
                                            @if($variacion->ofertaVigente())
                                                <span style="text-decoration: line-through; color: #999; font-size: 14px; margin-right: 8px;">
                                                    ${{ number_format($variacion->dPrecio, 2) }}
                                                </span>
                                                <span style="color: #dc3545;">${{ number_format($variacion->dPrecio_oferta, 2) }}</span>
                                                <span class="descuento-badge" style="margin-left: 8px;">-{{ $variacion->porcentajeDescuento }}%</span>
                                            @else
                                                ${{ number_format($variacion->dPrecio, 2) }}
                                            @endif
                                        </div>
                                        <div class="variacion-stock">
                                            Stock: {{ $variacion->iStock }} unidades
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Botón de favoritos (SIN CAMBIOS) -->
                <button class="btn-favorito-detalle {{ $producto->esFavorito() ? 'activo' : '' }}" 
                        onclick="toggleFavoritoDetalle(this, {{ $producto->id_producto }})"
                        id="btn-favorito-{{ $producto->id_producto }}">
                    <span class="btn-icon">{{ $producto->esFavorito() ? '❤️' : '🤍' }}</span>
                    <span class="btn-text">
                        {{ $producto->esFavorito() ? 'En tu lista de deseos' : 'Añadir a la lista de deseos' }}
                    </span>
                </button>

                <!-- Botón de compra (SIN CAMBIOS) -->
                <div class="action-buttons">
                    <button class="btn-comprar" onclick="agregarAlCarrito({{ $producto->id_producto }})">
                        🛒 Comprar Ahora
                    </button>
                </div>

                <!-- Detalles adicionales (SIN CAMBIOS) -->
                <div class="detalles-adicionales">
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
                            <p style="line-height: 1.6; color: #555;" id="producto-descripcion-corta">{{ $producto->tDescripcion_corta }}</p>
                        </div>
                    @endif

                    @if($producto->tDescripcion_larga)
                        <div class="detalle-item">
                            <h3>Información detallada</h3>
                            <p style="line-height: 1.6; color: #555; white-space: pre-line;" id="producto-descripcion-larga">{{ $producto->tDescripcion_larga }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        // --- VARIABLES GLOBALES (CORREGIDAS) ---
        let currentImageIndex = 0;
        let imagenesActuales = @json($imagenesProducto); // INICIA CON LAS DEL PADRE
        const variacionesData = @json($variacionesData);
        const productoOriginal = @json($productoData);
        let variacionSeleccionadaId = null;

        // --- FUNCIONES DE IMÁGENES (SIN CAMBIOS) ---
        function cambiarImagen(direccion) {
            if (imagenesActuales.length <= 1) return;
            currentImageIndex += direccion;
            if (currentImageIndex < 0) currentImageIndex = imagenesActuales.length - 1;
            if (currentImageIndex >= imagenesActuales.length) currentImageIndex = 0;
            updateMainImage();
        }

        function seleccionarImagen(index) {
            if (imagenesActuales.length === 0) return;
            currentImageIndex = index;
            updateMainImage();
        }

        function updateMainImage() {
            const mainImage = document.getElementById('mainImage');
            if (mainImage && imagenesActuales[currentImageIndex]) {
                mainImage.src = imagenesActuales[currentImageIndex];
                document.getElementById('imagen-actual').textContent = currentImageIndex + 1;
                
                document.querySelectorAll('.miniatura').forEach((thumb, index) => {
                    if (index === currentImageIndex) {
                        thumb.classList.add('activa');
                    } else {
                        thumb.classList.remove('activa');
                    }
                });
                
                const botones = document.querySelectorAll('.image-controls button');
                if (botones.length === 2) {
                    botones[0].disabled = imagenesActuales.length <= 1;
                    botones[1].disabled = imagenesActuales.length <= 1;
                }
            }
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft') cambiarImagen(-1);
            if (e.key === 'ArrowRight') cambiarImagen(1);
        });

        // --- FUNCIÓN DE TOGGLE PARA VARIACIONES (CORREGIDA) ---
        function toggleVariacion(variacionId) {
            const opcionSeleccionada = document.querySelector(`.variacion-opcion[data-variacion-id="${variacionId}"]`);
            
            if (variacionSeleccionadaId === variacionId) {
                // DESELECCIONAR
                variacionSeleccionadaId = null;
                document.querySelectorAll('.variacion-opcion').forEach(op => op.classList.remove('seleccionada'));
                restaurarProductoOriginal();
                showSingleToast('Mostrando producto original', 2000);
            } else {
                // SELECCIONAR NUEVA VARIACIÓN
                document.querySelectorAll('.variacion-opcion').forEach(op => op.classList.remove('seleccionada'));
                if (opcionSeleccionada) opcionSeleccionada.classList.add('seleccionada');
                
                variacionSeleccionadaId = variacionId;
                const variacion = variacionesData[variacionId];
                if (!variacion) return;
                
                aplicarDatosVariacion(variacion);
                showSingleToast('Variación seleccionada: ' + (variacion.atributos_texto?.join(' | ') || ''), 2000);
            }
        }

        // --- RESTAURAR DATOS DEL PRODUCTO ORIGINAL (CORREGIDA) ---
        function restaurarProductoOriginal() {
            imagenesActuales = productoOriginal.imagenes.slice();
            actualizarMiniaturas();
            currentImageIndex = 0;
            updateMainImage();
            document.getElementById('total-imagenes').textContent = imagenesActuales.length;
            
            document.getElementById('sku-texto').textContent = productoOriginal.sku;
            
            // Precio
            const precioOriginal = document.getElementById('precio-original');
            const descuentoBadge = document.getElementById('descuento-badge');
            const precioActual = document.getElementById('precio-actual');
            
            if (productoOriginal.tiene_oferta && productoOriginal.precio_oferta > 0 && productoOriginal.precio_oferta < productoOriginal.precio) {
                const porcentaje = Math.round(((productoOriginal.precio - productoOriginal.precio_oferta) / productoOriginal.precio) * 100);
                precioOriginal.style.display = 'inline';
                precioOriginal.textContent = '$' + productoOriginal.precio.toFixed(2);
                descuentoBadge.style.display = 'inline';
                descuentoBadge.textContent = '-' + porcentaje + '%';
                precioActual.textContent = '$' + productoOriginal.precio_oferta.toFixed(2);
            } else {
                precioOriginal.style.display = 'none';
                descuentoBadge.style.display = 'none';
                precioActual.textContent = '$' + productoOriginal.precio.toFixed(2);
            }
            
            // Stock
            const stockContainer = document.getElementById('stock-container');
            const stockTexto = document.getElementById('stock-texto');
            let stockMensaje = '', stockClase = '';
            if (productoOriginal.stock > 10) {
                stockMensaje = `✅ En stock (${productoOriginal.stock} unidades disponibles)`;
                stockClase = 'stock-bueno';
            } else if (productoOriginal.stock > 0) {
                stockMensaje = `⚠️ Stock bajo (Solo ${productoOriginal.stock} unidades disponibles)`;
                stockClase = 'stock-bajo';
            } else {
                stockMensaje = '❌ Sin stock (Próximamente)';
                stockClase = 'sin-stock';
            }
            stockContainer.className = `stock-info-detalle ${stockClase}`;
            stockTexto.textContent = stockMensaje;
            
            document.getElementById('variacion-descripcion-container').style.display = 'none';
            document.getElementById('producto-nombre').textContent = '{{ $producto->vNombre }}';
        }

        // --- APLICAR DATOS DE UNA VARIACIÓN (CORREGIDA) ---
        function aplicarDatosVariacion(variacion) {
            imagenesActuales = (variacion.imagenes && variacion.imagenes.length > 0) ? variacion.imagenes.slice() : productoOriginal.imagenes.slice();
            actualizarMiniaturas();
            currentImageIndex = 0;
            updateMainImage();
            document.getElementById('total-imagenes').textContent = imagenesActuales.length;
            
            document.getElementById('sku-texto').textContent = variacion.sku;
            
            // Precio
            const precioOriginal = document.getElementById('precio-original');
            const descuentoBadge = document.getElementById('descuento-badge');
            const precioActual = document.getElementById('precio-actual');
            
            if (variacion.tiene_oferta && variacion.precio_oferta > 0 && variacion.precio_oferta < variacion.precio) {
                const porcentaje = Math.round(((variacion.precio - variacion.precio_oferta) / variacion.precio) * 100);
                precioOriginal.style.display = 'inline';
                precioOriginal.textContent = '$' + variacion.precio.toFixed(2);
                descuentoBadge.style.display = 'inline';
                descuentoBadge.textContent = '-' + porcentaje + '%';
                precioActual.textContent = '$' + variacion.precio_oferta.toFixed(2);
            } else {
                precioOriginal.style.display = 'none';
                descuentoBadge.style.display = 'none';
                precioActual.textContent = '$' + variacion.precio.toFixed(2);
            }
            
            // Stock
            const stockContainer = document.getElementById('stock-container');
            const stockTexto = document.getElementById('stock-texto');
            let stockMensaje = '', stockClase = '';
            if (variacion.stock > 10) {
                stockMensaje = `✅ En stock (${variacion.stock} unidades disponibles)`;
                stockClase = 'stock-bueno';
            } else if (variacion.stock > 0) {
                stockMensaje = `⚠️ Stock bajo (Solo ${variacion.stock} unidades disponibles)`;
                stockClase = 'stock-bajo';
            } else {
                stockMensaje = '❌ Sin stock (Próximamente)';
                stockClase = 'sin-stock';
            }
            stockContainer.className = `stock-info-detalle ${stockClase}`;
            stockTexto.textContent = stockMensaje;
            
            // Descripción de variación
            const descripcionContainer = document.getElementById('variacion-descripcion-container');
            const descripcionTexto = document.getElementById('variacion-descripcion-texto');
            if (variacion.descripcion && variacion.descripcion.trim() !== '') {
                descripcionTexto.textContent = variacion.descripcion;
                descripcionContainer.style.display = 'block';
            } else {
                descripcionContainer.style.display = 'none';
            }
            
            // Nombre del producto + atributos
            if (variacion.atributos_texto && variacion.atributos_texto.length > 0) {
                document.getElementById('producto-nombre').textContent = 
                    '{{ $producto->vNombre }} - ' + variacion.atributos_texto.join(' | ');
            }
        }

        // --- ACTUALIZAR MINIATURAS (CORREGIDA) ---
        function actualizarMiniaturas() {
            const miniaturasContainer = document.getElementById('miniaturas-container');
            if (!miniaturasContainer) return;
            miniaturasContainer.innerHTML = '';
            
            imagenesActuales.forEach((imgUrl, idx) => {
                const img = document.createElement('img');
                img.src = imgUrl;
                img.alt = `Imagen ${idx + 1}`;
                img.className = `miniatura ${idx === 0 ? 'activa' : ''}`;
                img.onclick = function() { seleccionarImagen(idx); };
                img.onerror = function() { this.src = 'https://via.placeholder.com/70x70?text=Error'; };
                miniaturasContainer.appendChild(img);
            });
            
            document.getElementById('total-imagenes').textContent = imagenesActuales.length;
            document.getElementById('imagen-actual').textContent = 1;
        }

        // --- TOGGLE FAVORITOS (SIN CAMBIOS) ---
        let currentToast = null;
        let toastTimeout = null;

        function removeExistingToast() {
            if (currentToast) {
                currentToast.classList.remove('show');
                setTimeout(() => {
                    if (currentToast && currentToast.parentNode) currentToast.parentNode.removeChild(currentToast);
                    currentToast = null;
                }, 300);
            }
            if (toastTimeout) clearTimeout(toastTimeout);
            const allToasts = document.querySelectorAll('.toast-notification');
            allToasts.forEach(toast => {
                toast.classList.remove('show');
                setTimeout(() => { if (toast.parentNode) toast.parentNode.removeChild(toast); }, 300);
            });
        }

        function showSingleToast(message, duration = 3000) {
            removeExistingToast();
            const toast = document.createElement('div');
            toast.className = 'toast-notification';
            const emoji = message.includes('✅') ? '✅' : message.includes('❌') ? '❌' : 'ℹ️';
            const cleanMessage = message.replace('✅', '').replace('❌', '').trim();
            toast.innerHTML = `<span style="font-size: 20px;">${emoji}</span><span>${cleanMessage}</span>`;
            document.body.appendChild(toast);
            currentToast = toast;
            setTimeout(() => toast.classList.add('show'), 10);
            toastTimeout = setTimeout(() => {
                if (currentToast === toast) {
                    toast.classList.remove('show');
                    setTimeout(() => {
                        if (toast.parentNode) toast.parentNode.removeChild(toast);
                        if (currentToast === toast) currentToast = null;
                    }, 400);
                }
            }, duration);
        }

        function toggleFavoritoDetalle(button, productoId) {
            if (button.disabled) return;
            button.disabled = true;

            @if(!Auth::check())
                window.location.href = '{{ route("login") }}?from_favoritos=true&redirect=' + encodeURIComponent(window.location.href);
                return;
            @endif

            const esFavorito = button.classList.contains('activo');
            const iconSpan = button.querySelector('.btn-icon');
            const textSpan = button.querySelector('.btn-text');
            button.style.transform = 'scale(0.95)';
            removeExistingToast();

            fetch(`/favoritos/toggle/${productoId}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' }
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
                        showSingleToast('Producto agregado a favoritos ✅', 3000);
                    } else {
                        button.classList.remove('activo');
                        iconSpan.textContent = '🤍';
                        textSpan.textContent = 'Añadir a la lista de deseos';
                        showSingleToast('Producto eliminado de favoritos ❌', 3000);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showSingleToast('Error de conexión ❌', 3000);
            })
            .finally(() => {
                setTimeout(() => { button.disabled = false; button.style.transform = ''; }, 300);
            });
        }

        function agregarAlCarrito(productoId) {
            showSingleToast('Producto agregado al carrito 🛒', 3000);
        }

        // --- INICIALIZACIÓN ---
        document.addEventListener('DOMContentLoaded', function() {
            if (imagenesActuales.length > 0 && document.getElementById('mainImage')) {
                document.getElementById('mainImage').src = imagenesActuales[0];
            }
        });
    </script>
</body>
</html>