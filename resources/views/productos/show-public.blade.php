<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $producto->vNombre }} | Ecommerce Agave</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.5;
        }

        /* Header con los colores originales */
        .ml-header {
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 8px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .ml-header .container {
            display: flex;
            align-items: center;
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 16px;
        }

        .ml-logo {
            font-size: 24px;
            font-weight: 700;
            color: #2c3e50;
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .ml-logo i {
            color: #007bff;
            margin-right: 8px;
        }

        .ml-search {
            flex: 1;
            display: flex;
            background: white;
            border-radius: 25px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .ml-search input {
            flex: 1;
            border: none;
            padding: 12px 20px;
            font-size: 14px;
            outline: none;
        }

        .ml-search button {
            background: #007bff;
            border: none;
            padding: 12px 25px;
            color: white;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .ml-search button:hover {
            background: #0056b3;
        }

        .ml-nav {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .ml-nav a {
            color: #495057;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        .ml-nav a:hover {
            color: #007bff;
        }

        .btn-invitado {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white !important;
            padding: 8px 15px;
            border-radius: 25px;
            font-weight: bold;
            display: inline-block;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .btn-invitado:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(102, 126, 234, 0.4);
            color: white !important;
            text-decoration: none;
        }

        .user-welcome {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 0;
            text-align: center;
            font-size: 0.95rem;
        }

        .user-welcome .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 16px;
        }

        /* Contenedor principal */
        .ml-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 16px;
        }

        /* Breadcrumb */
        .ml-breadcrumb {
            margin-bottom: 16px;
            font-size: 14px;
        }

        .ml-breadcrumb a {
            color: #007bff;
            text-decoration: none;
        }

        .ml-breadcrumb a:hover {
            color: #0056b3;
        }

        .ml-breadcrumb span {
            color: #6c757d;
        }

        /* Layout principal */
        .ml-layout {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 20px;
        }

        /* Galería de imágenes */
        .ml-gallery {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .zoom-container {
            position: relative;
            width: 100%;
            height: 400px;
            background-color: #ffffff;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            overflow: hidden;
            cursor: crosshair;
            margin-bottom: 20px;
        }

        .zoom-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
            transition: transform 0.1s ease;
        }

        .zoom-lens {
            position: absolute;
            width: 150px;
            height: 150px;
            border: 2px solid #007bff;
            background-color: rgba(255,255,255,0.3);
            pointer-events: none;
            z-index: 10;
            border-radius: 4px;
            display: none;
        }

        .zoom-result {
            position: absolute;
            top: 0;
            left: 105%;
            width: 400px;
            height: 400px;
            background-repeat: no-repeat;
            background-size: 1000px 1000px;
            border: 2px solid #007bff;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            display: none;
            z-index: 100;
            background-color: white;
        }

        .ml-image-controls {
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            transform: translateY(-50%);
            display: flex;
            justify-content: space-between;
            padding: 0 10px;
            pointer-events: none;
            z-index: 15;
        }

        .ml-image-controls button {
            pointer-events: auto;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: rgba(255,255,255,0.9);
            border: none;
            color: #495057;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            opacity: 0.8;
            transition: opacity 0.2s ease;
        }

        .ml-image-controls button:hover {
            opacity: 1;
        }

        .ml-image-controls button:disabled {
            opacity: 0.2;
            cursor: not-allowed;
        }

        .ml-thumbnails {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 8px;
            margin-top: 15px;
            padding: 10px;
            max-height: 170px;
            overflow-y: auto;
            border: 1px solid #eee;
            border-radius: 8px;
        }

        .ml-thumb {
            width: 70px;
            height: 70px;
            border: 2px solid transparent;
            border-radius: 8px;
            cursor: pointer;
            overflow: hidden;
            flex-shrink: 0;
            transition: all 0.2s ease;
        }

        .ml-thumb:hover {
            transform: scale(1.1);
            border-color: #007bff;
        }

        .ml-thumb.active {
            border-color: #007bff;
            box-shadow: 0 0 0 2px rgba(0,123,255,0.3);
        }

        .ml-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .ml-image-counter {
            text-align: center;
            margin-top: 8px;
            font-size: 13px;
            color: #6c757d;
        }

        .ml-image-counter .badge {
            background-color: #f8f9fa;
            color: #333;
            padding: 5px 10px;
        }

        /* Información del producto */
        .ml-product-info {
            background: white;
            border-radius: 8px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .ml-condition {
            font-size: 14px;
            color: #28a745;
            margin-bottom: 8px;
        }

        .ml-condition i {
            margin-right: 4px;
        }

        .ml-title {
            font-size: 22px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            line-height: 1.3;
        }

        .ml-sku {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 16px;
            padding-bottom: 16px;
            border-bottom: 1px solid #dee2e6;
        }

        .ml-sku span {
            background: #f8f9fa;
            padding: 4px 8px;
            border-radius: 5px;
            font-family: monospace;
        }

        /* Precios */
        .ml-price-container {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .ml-price-original {
            font-size: 16px;
            color: #6c757d;
            text-decoration: line-through;
            margin-right: 10px;
        }

        .ml-price-current {
            font-size: 36px;
            font-weight: 300;
            color: #28a745;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .ml-discount {
            background: #dc3545;
            color: white;
            padding: 4px 8px;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 600;
        }

        .ml-price-installments {
            font-size: 14px;
            color: #28a745;
            margin-top: 8px;
        }

        .ml-tax-info {
            font-size: 13px;
            color: #6c757d;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed #dee2e6;
        }

        /* Stock */
        .ml-stock {
            margin: 20px 0;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 15px;
        }

        .ml-stock-high {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .ml-stock-low {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }

        .ml-stock-out {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Variaciones */
        .ml-variations {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .ml-variation-group {
            margin-bottom: 16px;
        }

        .ml-variation-group:last-child {
            margin-bottom: 0;
        }

        .ml-variation-label {
            font-size: 14px;
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }

        .ml-variation-options {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .ml-variation-btn {
            padding: 8px 16px;
            border: 2px solid #dee2e6;
            background: white;
            border-radius: 25px;
            font-size: 14px;
            color: #495057;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .ml-variation-btn:hover {
            border-color: #007bff;
            color: #007bff;
            transform: translateY(-2px);
        }

        .ml-variation-btn.active {
            background: #007bff;
            border-color: #007bff;
            color: white;
        }

        /* Características */
        .ml-features {
            background: white;
            border-radius: 8px;
            padding: 24px;
            margin-top: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .ml-features-title {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid #dee2e6;
        }

        .ml-features-title i {
            color: #007bff;
            margin-right: 8px;
        }

        .ml-features-table {
            width: 100%;
            border-collapse: collapse;
        }

        .ml-features-table tr {
            border-bottom: 1px solid #f0f0f0;
        }

        .ml-features-table tr:last-child {
            border-bottom: none;
        }

        .ml-features-table td {
            padding: 12px 8px;
            font-size: 14px;
        }

        .ml-features-table td:first-child {
            color: #666;
            font-weight: 500;
            width: 40%;
            background-color: #fafafa;
            padding-left: 16px;
            border-right: 1px solid #f0f0f0;
        }

        .ml-features-table td:last-child {
            color: #333;
            padding-left: 16px;
        }

        .ml-feature-extra {
            color: #28a745;
            font-size: 12px;
            margin-left: 8px;
        }

        /* Descripción */
        .ml-description {
            background: white;
            border-radius: 8px;
            padding: 24px;
            margin-top: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .ml-description-title {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 16px;
        }

        .ml-description-title i {
            color: #007bff;
            margin-right: 8px;
        }

        .ml-description-short {
            font-size: 16px;
            color: #495057;
            margin-bottom: 20px;
            padding: 16px;
            background: #f8f9fa;
            border-left: 4px solid #007bff;
            border-radius: 4px;
        }

        .ml-description-long {
            font-size: 15px;
            color: #495057;
            line-height: 1.6;
            white-space: pre-line;
        }

        /* Etiquetas */
        .ml-tags {
            background: white;
            border-radius: 8px;
            padding: 24px;
            margin-top: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .ml-tags-title {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 16px;
        }

        .ml-tags-title i {
            color: #007bff;
            margin-right: 8px;
        }

        .ml-tags-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .ml-tag {
            background: #e7f1ff;
            color: #007bff;
            padding: 6px 12px;
            border-radius: 25px;
            font-size: 13px;
            border: 1px solid #007bff;
        }

        .ml-tag i {
            margin-right: 4px;
        }

        /* Botones de acción */
        .ml-actions {
            display: flex;
            gap: 12px;
            margin: 24px 0;
        }

        .ml-btn-favorite {
            flex: 1;
            background: white;
            border: 2px solid #dc3545;
            color: #dc3545;
            padding: 14px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .ml-btn-favorite:hover {
            background: #dc3545;
            color: white;
            transform: translateY(-2px);
        }

        .ml-btn-favorite.active {
            background: #dc3545;
            color: white;
        }

        .ml-btn-favorite.loading {
            position: relative;
            color: transparent;
            pointer-events: none;
        }

        .ml-btn-favorite.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #dc3545;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .ml-btn-buy {
            flex: 2;
            background: #28a745;
            border: none;
            color: white;
            padding: 14px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .ml-btn-buy:hover {
            background: #218838;
            transform: translateY(-2px);
        }

        /* Modal */
        .ml-modal .modal-content {
            background: transparent;
            border: none;
        }

        .ml-modal .btn-close {
            filter: invert(1);
            opacity: 0.8;
            background-color: rgba(0,0,0,0.5);
            padding: 10px;
            border-radius: 50%;
        }

        /* Toast */
        .ml-toast {
            position: fixed;
            top: 30px;
            right: 30px;
            background: white;
            color: #333;
            padding: 15px 25px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            z-index: 10000;
            display: flex;
            align-items: center;
            gap: 12px;
            border-left: 5px solid;
            transform: translateX(120%);
            transition: transform 0.4s ease;
            max-width: 350px;
        }

        .ml-toast.show {
            transform: translateX(0);
        }

        .ml-toast.success {
            border-left-color: #28a745;
        }

        .ml-toast.error {
            border-left-color: #dc3545;
        }

        .ml-toast.info {
            border-left-color: #007bff;
        }

        .ml-toast i {
            font-size: 20px;
        }

        .ml-toast.success i {
            color: #28a745;
        }

        .ml-toast.error i {
            color: #dc3545;
        }

        .ml-toast.info i {
            color: #007bff;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .zoom-result {
                width: 300px;
                height: 300px;
            }
        }

        @media (max-width: 992px) {
            .ml-layout {
                grid-template-columns: 1fr;
            }
            
            .ml-header .container {
                flex-wrap: wrap;
            }
            
            .ml-search {
                order: 3;
                width: 100%;
            }

            .zoom-result {
                display: none !important;
            }
            
            .zoom-lens {
                display: none !important;
            }
        }

        @media (max-width: 768px) {
            .zoom-container {
                height: 300px;
            }
            
            .ml-price-current {
                font-size: 28px;
            }
            
            .ml-actions {
                flex-direction: column;
            }
            
            .ml-features-table td:first-child {
                width: 50%;
            }
        }
    </style>
</head>
<body>

    <!-- Header con colores originales -->
    <header class="ml-header">
        <div class="container">
            <a href="{{ route('inicio.real') }}" class="ml-logo">
                <i class="fas fa-wine-bottle"></i>
                <span>Ecommerce Agave</span>
            </a>
            
            <div class="ml-search">
                <form action="{{ route('busqueda.resultados') }}" method="GET" style="display: flex; width: 100%;">
                    <input type="text" name="q" placeholder="Buscar productos..." value="{{ request('q') }}">
                    <button type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>

            <div class="ml-nav">
                <a href="{{ route('busqueda.resultados') }}">Productos</a>
                @auth
                    <a href="{{ route('favoritos.index') }}" style="color: #dc3545;">
                        <i class="fas fa-heart"></i> Favoritos
                    </a>
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" style="background: none; border: none; color: #495057; cursor: pointer; font-weight: 500;">
                            <i class="fas fa-sign-out-alt"></i> Salir
                        </button>
                    </form>
                @else
                    <a href="{{ route('favoritos.invitado.index') }}" class="btn-invitado">
                        <i class="fas fa-user"></i> Invitado
                    </a>
                    <a href="{{ route('login') }}">Ingresar</a>
                    <a href="{{ route('usuarios.create') }}">Registrarse</a>
                @endauth
            </div>
        </div>
    </header>

    @auth
    <div class="user-welcome">
        <div class="container">
            <i class="fas fa-smile me-2"></i> ¡Hola, {{ Auth::user()->vNombre }}! Bienvenido a tu tienda de confianza.
        </div>
    </div>
    @endauth

    <main class="ml-container">

        @php
            // --- DATOS DEL PRODUCTO ---
            $imagenesProducto = $producto->imagenes ?? [];
            $tieneVariaciones = $producto->tieneVariaciones();
            
            // Calcular si el producto padre tiene oferta vigente
            $productoTieneOferta = $producto->tieneDescuentoActivo();
            $precioBaseProducto = $productoTieneOferta ? $producto->dPrecio_oferta : $producto->dPrecio_venta;
            
            // Calcular impuestos del producto padre
            $totalImpuestosProducto = 0;
            $textoImpuestosProducto = '';
            $contadorImpuestos = 0;
            foreach($producto->impuestos as $impuesto) {
                $montoImpuesto = $precioBaseProducto * ($impuesto->dPorcentaje / 100);
                $totalImpuestosProducto += $montoImpuesto;
                
                if ($contadorImpuestos > 0) {
                    $textoImpuestosProducto .= ' + ';
                }
                $textoImpuestosProducto .= $impuesto->vNombre . ' ' . $impuesto->dPorcentaje . '%';
                $contadorImpuestos++;
            }
            
            // Obtener características del producto padre
            $caracteristicasProducto = [];
            
            // Atributos generales
            foreach($producto->valoresAtributos as $valor) {
                if($valor->atributo) {
                    $caracteristicasProducto[] = [
                        'nombre' => $valor->atributo->vNombre,
                        'valor' => $valor->vValor,
                        'precio_extra' => $valor->pivot->dPrecio_extra ?? 0
                    ];
                }
            }
            
            // Categoría
            if($producto->categoria) {
                $caracteristicasProducto[] = [
                    'nombre' => 'Categoría',
                    'valor' => $producto->categoria->vNombre,
                    'precio_extra' => 0
                ];
            }
            
            // Marca
            if($producto->marca) {
                $caracteristicasProducto[] = [
                    'nombre' => 'Marca',
                    'valor' => $producto->marca->vNombre,
                    'precio_extra' => 0
                ];
            }
            
            // Peso
            if($producto->dPeso) {
                $caracteristicasProducto[] = [
                    'nombre' => 'Peso',
                    'valor' => number_format($producto->dPeso, 3) . ' kg',
                    'precio_extra' => 0
                ];
            }
            
            // Clase de envío
            if($producto->vClase_envio) {
                $claseEnvioTexto = '';
                switch($producto->vClase_envio) {
                    case 'estandar': $claseEnvioTexto = 'Estándar'; break;
                    case 'express': $claseEnvioTexto = 'Express'; break;
                    case 'fragil': $claseEnvioTexto = 'Frágil'; break;
                    case 'grandes_dimensiones': $claseEnvioTexto = 'Grandes dimensiones'; break;
                    default: $claseEnvioTexto = $producto->vClase_envio;
                }
                $caracteristicasProducto[] = [
                    'nombre' => 'Clase de envío',
                    'valor' => $claseEnvioTexto,
                    'precio_extra' => 0
                ];
            }
            
            // Obtener parámetro de variación de la URL
            $variacionUrl = request()->get('variacion');
            
            $productoData = [
                'id' => $producto->id_producto,
                'sku' => $producto->vCodigo_barras,
                'nombre' => $producto->vNombre,
                'precio_original' => (float)$producto->dPrecio_venta,
                'precio_oferta' => (float)($producto->dPrecio_oferta ?? 0),
                'tiene_oferta' => $productoTieneOferta,
                'precio_base' => (float)$precioBaseProducto,
                'total_impuestos' => (float)$totalImpuestosProducto,
                'precio_final' => (float)($precioBaseProducto + $totalImpuestosProducto),
                'texto_impuestos' => $textoImpuestosProducto,
                'stock' => (int)$producto->iStock,
                'tiene_variaciones' => $tieneVariaciones,
                'imagenes' => $imagenesProducto,
                'descripcion_corta' => $producto->tDescripcion_corta ?? '',
                'descripcion_larga' => $producto->tDescripcion_larga ?? '',
                'categoria' => $producto->categoria->vNombre ?? 'Sin categoría',
                'marca' => $producto->marca->vNombre ?? 'Sin marca',
                'etiquetas' => $producto->etiquetas ?? [],
                'caracteristicas' => $caracteristicasProducto,
                'variacion_seleccionada' => $variacionUrl
            ];

            // --- DATOS DE LAS VARIACIONES ---
            $variacionesData = [];
            $atributosAgrupados = [];
            $variacionInicial = null;
            
            foreach ($producto->variaciones as $var) {
                $atributosTexto = [];
                $atributosParaMapa = [];
                $caracteristicasVariacion = [];
                
                foreach($var->atributos as $atributoRel) {
                    if($atributoRel->atributo && $atributoRel->valor) {
                        $atributosTexto[] = $atributoRel->atributo->vNombre . ': ' . $atributoRel->valor->vValor;
                        $atributosParaMapa[$atributoRel->id_atributo] = $atributoRel->id_atributo_valor;
                        
                        $caracteristicasVariacion[] = [
                            'nombre' => $atributoRel->atributo->vNombre,
                            'valor' => $atributoRel->valor->vValor,
                            'precio_extra' => 0
                        ];

                        $nombreAtributo = $atributoRel->atributo->vNombre;
                        $idAtributo = $atributoRel->id_atributo;
                        $valor = $atributoRel->valor->vValor;
                        $idValor = $atributoRel->id_atributo_valor;

                        if (!isset($atributosAgrupados[$idAtributo])) {
                            $atributosAgrupados[$idAtributo] = [
                                'nombre' => $nombreAtributo,
                                'valores' => []
                            ];
                        }
                        if (!isset($atributosAgrupados[$idAtributo]['valores'][$idValor])) {
                            $atributosAgrupados[$idAtributo]['valores'][$idValor] = $valor;
                        }
                    }
                }
                
                // Calcular si la variación tiene oferta vigente
                $variacionTieneOferta = $var->tieneDescuentoActivo();
                $precioBaseVariacion = $variacionTieneOferta ? $var->dPrecio_oferta : $var->dPrecio;
                
                $impuestoVariacion = $var->impuesto ?? $producto->impuestos->first();
                $totalImpuestosVariacion = 0;
                $textoImpuestosVariacion = '';
                
                if ($impuestoVariacion) {
                    $montoImpuesto = $precioBaseVariacion * ($impuestoVariacion->dPorcentaje / 100);
                    $totalImpuestosVariacion = $montoImpuesto;
                    $textoImpuestosVariacion = $impuestoVariacion->vNombre . ' ' . $impuestoVariacion->dPorcentaje . '%';
                }

                if($var->dPeso) {
                    $caracteristicasVariacion[] = [
                        'nombre' => 'Peso',
                        'valor' => number_format($var->dPeso, 3) . ' kg',
                        'precio_extra' => 0
                    ];
                }
                
                if($var->vClase_envio) {
                    $claseEnvioTexto = '';
                    switch($var->vClase_envio) {
                        case 'estandar': $claseEnvioTexto = 'Estándar'; break;
                        case 'express': $claseEnvioTexto = 'Express'; break;
                        case 'fragil': $claseEnvioTexto = 'Frágil'; break;
                        case 'grandes_dimensiones': $claseEnvioTexto = 'Grandes dimensiones'; break;
                        default: $claseEnvioTexto = $var->vClase_envio;
                    }
                    $caracteristicasVariacion[] = [
                        'nombre' => 'Clase de envío',
                        'valor' => $claseEnvioTexto,
                        'precio_extra' => 0
                    ];
                }

                $variacionesData[$var->id_variacion] = [
                    'id' => $var->id_variacion,
                    'sku' => $var->vSKU,
                    'precio_original' => (float)$var->dPrecio,
                    'precio_oferta' => (float)($var->dPrecio_oferta ?? 0),
                    'tiene_oferta' => $variacionTieneOferta,
                    'precio_base' => (float)$precioBaseVariacion,
                    'total_impuestos' => (float)$totalImpuestosVariacion,
                    'precio_final' => (float)($precioBaseVariacion + $totalImpuestosVariacion),
                    'texto_impuestos' => $textoImpuestosVariacion,
                    'stock' => (int)$var->iStock,
                    'atributos_texto' => $atributosTexto,
                    'atributos_mapa' => $atributosParaMapa,
                    'imagenes' => $var->imagenes ?? [],
                    'descripcion' => $var->tDescripcion ?? '',
                    'caracteristicas' => $caracteristicasVariacion,
                ];
                
                // Si hay una variación en la URL y coincide con esta, guardarla como inicial
                if ($variacionUrl && $var->id_variacion == $variacionUrl) {
                    $variacionInicial = $variacionesData[$var->id_variacion];
                }
            }
            
            // Determinar qué datos mostrar inicialmente
            $mostrarProductoInicial = true;
            $datosIniciales = $productoData;
            
            if ($variacionInicial) {
                $mostrarProductoInicial = false;
                $datosIniciales = $variacionInicial;
            }
        @endphp

        <!-- Breadcrumb -->
        <div class="ml-breadcrumb">
            <a href="{{ route('inicio.real') }}">Inicio</a> <span>›</span>
            <a href="{{ route('busqueda.resultados') }}">Productos</a> <span>›</span>
            @if($producto->categoria)
                <span>{{ $producto->categoria->vNombre }}</span> <span>›</span>
            @endif
            <span>{{ $producto->vNombre }}</span>
        </div>

        <!-- Layout principal -->
        <div class="ml-layout">
            <!-- Columna izquierda - Galería con ZOOM -->
            <div class="ml-gallery">
                <div class="position-relative mb-3" style="background-color: #ffffff; border-radius: 8px; border: 1px solid #e0e0e0; overflow: hidden;">
                    <!-- Contenedor del zoom -->
                    <div id="zoom-container" class="zoom-container" style="position: relative; width: 100%; height: 400px; overflow: hidden; cursor: crosshair;">
                        <!-- Imagen principal -->
                        <img id="mainImage" 
                             src="{{ $variacionInicial ? ($variacionInicial['imagenes'][0] ?? $imagenesProducto[0] ?? 'https://via.placeholder.com/400x400?text=Sin+Imagen') : ($imagenesProducto[0] ?? 'https://via.placeholder.com/400x400?text=Sin+Imagen') }}" 
                             class="img-fluid zoom-image" 
                             style="width: 100%; height: 100%; object-fit: contain; transition: transform 0.1s ease;"
                             alt="{{ $producto->vNombre }}"
                             onclick="abrirModalImagen()"
                             onerror="this.onerror=null; this.src='https://via.placeholder.com/400x400?text=Error';">
                        
                        <!-- Lupa -->
                        <div id="zoom-lens" class="zoom-lens" style="display: none; position: absolute; width: 150px; height: 150px; border: 2px solid #007bff; background-color: rgba(255,255,255,0.3); pointer-events: none; z-index: 10; border-radius: 4px;"></div>

                        <!-- Controles de navegación -->
                        <div id="imageControls" class="position-absolute w-100 d-flex justify-content-between px-2" style="top: 50%; transform: translateY(-50%); z-index: 15;">
                            <button type="button" class="btn btn-sm btn-light rounded-circle shadow-sm" onclick="event.stopPropagation(); cambiarImagen(-1)" style="width: 36px; height: 36px; opacity: 0.8; background-color: rgba(255,255,255,0.9);" {{ count($variacionInicial ? ($variacionInicial['imagenes'] ?? $imagenesProducto) : $imagenesProducto) <= 1 ? 'disabled' : '' }}>
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-light rounded-circle shadow-sm" onclick="event.stopPropagation(); cambiarImagen(1)" style="width: 36px; height: 36px; opacity: 0.8; background-color: rgba(255,255,255,0.9);" {{ count($variacionInicial ? ($variacionInicial['imagenes'] ?? $imagenesProducto) : $imagenesProducto) <= 1 ? 'disabled' : '' }}>
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Miniaturas -->
                <div id="miniaturas-container" class="d-flex flex-wrap justify-content-center gap-2 mt-2" style="padding-bottom: 5px; max-height: 170px; overflow-y: auto; border: 1px solid #eee; border-radius: 8px; padding: 10px;">
                    @php
                        $imagenesAMostrar = $variacionInicial ? ($variacionInicial['imagenes'] ?? $imagenesProducto) : $imagenesProducto;
                    @endphp
                    @foreach($imagenesAMostrar as $index => $imgUrl)
                        <div class="miniatura-item" style="width: 70px;" onclick="seleccionarImagen({{ $index }})">
                            <img src="{{ $imgUrl }}" 
                                 class="img-thumbnail miniatura {{ $index === 0 ? 'activa' : '' }}" 
                                 style="width: 70px; height: 70px; object-fit: cover; cursor: pointer; border: 2px solid {{ $index === 0 ? '#007bff' : 'transparent' }}; border-radius: 8px;"
                                 alt="Miniatura {{ $index + 1 }}">
                        </div>
                    @endforeach
                </div>

                <!-- Contador de imágenes -->
                <div id="imageCounter" class="text-center mt-2" {{ count($imagenesAMostrar) <= 0 ? 'style=display:none;' : '' }}>
                    <span class="badge bg-light text-dark" id="contador-imagenes">
                        <span id="imagen-actual">1</span> / <span id="total-imagenes">{{ count($imagenesAMostrar) }}</span>
                    </span>
                </div>
            </div>

            <!-- Columna derecha - Información del producto -->
            <div class="ml-product-info">
                <div class="ml-condition">
                    <i class="fas fa-check-circle"></i>
                    @if($datosIniciales['stock'] > 0)
                        Nuevo | {{ number_format($datosIniciales['stock']) }} disponibles
                    @else
                        Sin stock
                    @endif
                </div>

                <!-- NOMBRE DEL PRODUCTO -->
                <h1 class="ml-title" id="producto-nombre">{{ $producto->vNombre }}</h1>

                <div class="ml-sku">
                    <span id="producto-sku">{{ $datosIniciales['sku'] }}</span>
                </div>

                <!-- Precios -->
                <div class="ml-price-container" id="precio-container">
                    @if($mostrarProductoInicial)
                        <div id="producto-precio-info" style="display: block;">
                            @if($productoTieneOferta)
                                <span class="ml-price-original" id="producto-precio-original">${{ number_format($producto->dPrecio_venta, 2) }}</span>
                                <div class="ml-price-current">
                                    ${{ number_format($producto->dPrecio_oferta, 2) }}
                                    <span class="ml-discount" id="producto-descuento-badge">-{{ $producto->porcentajeDescuento }}%</span>
                                </div>
                            @else
                                <div class="ml-price-current" id="producto-precio-actual">
                                    ${{ number_format($producto->dPrecio_venta, 2) }}
                                </div>
                            @endif
                        </div>
                        <div id="variacion-precio-info" style="display: none;">
                            <span class="ml-price-original" id="variacion-precio-original"></span>
                            <div class="ml-price-current">
                                <span id="variacion-precio-actual"></span>
                                <span class="ml-discount" id="variacion-descuento-badge" style="display: none;"></span>
                            </div>
                        </div>
                    @else
                        <div id="producto-precio-info" style="display: none;"></div>
                        <div id="variacion-precio-info" style="display: block;">
                            <span class="ml-price-original" id="variacion-precio-original" {{ $variacionInicial['tiene_oferta'] ? '' : 'style=display:none;' }}>
                                ${{ number_format($variacionInicial['precio_original'], 2) }}
                            </span>
                            <div class="ml-price-current">
                                <span id="variacion-precio-actual">
                                    ${{ number_format($variacionInicial['tiene_oferta'] ? $variacionInicial['precio_oferta'] : $variacionInicial['precio_original'], 2) }}
                                </span>
                                @if($variacionInicial['tiene_oferta'])
                                    <span class="ml-discount" id="variacion-descuento-badge">
                                        -{{ round((($variacionInicial['precio_original'] - $variacionInicial['precio_oferta']) / $variacionInicial['precio_original']) * 100) }}%
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="ml-price-installments">
                        <i class="fas fa-credit-card"></i> Hasta 12 cuotas sin interés
                    </div>

                    <div class="ml-tax-info" id="impuestos-info">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <span id="impuestos-texto">
                            @if(!empty($datosIniciales['texto_impuestos']))
                                Impuestos: {{ $datosIniciales['texto_impuestos'] }}
                            @else
                                Sin impuestos adicionales
                            @endif
                        </span>
                    </div>
                </div>

                <!-- Stock -->
                <div class="ml-stock {{ $datosIniciales['stock'] > 10 ? 'ml-stock-high' : ($datosIniciales['stock'] > 0 ? 'ml-stock-low' : 'ml-stock-out') }}" id="stock-container">
                    <span id="stock-texto">
                        @if($datosIniciales['stock'] > 10)
                            <i class="fas fa-check-circle"></i> Stock disponible ({{ number_format($datosIniciales['stock']) }} unidades)
                        @elseif($datosIniciales['stock'] > 0)
                            <i class="fas fa-exclamation-triangle"></i> ¡Últimas unidades! (Solo {{ number_format($datosIniciales['stock']) }} disponibles)
                        @else
                            <i class="fas fa-times-circle"></i> Producto agotado
                        @endif
                    </span>
                </div>

                <!-- Variaciones -->
                @if($tieneVariaciones && count($atributosAgrupados) > 0)
                <div class="ml-variations">
                    <div id="variaciones-selector-container">
                        @foreach($atributosAgrupados as $idAtributo => $atributo)
                            <div class="ml-variation-group">
                                <div class="ml-variation-label">{{ $atributo['nombre'] }}</div>
                                <div class="ml-variation-options">
                                    @foreach($atributo['valores'] as $idValor => $valor)
                                        @php
                                            $activo = false;
                                            if ($variacionInicial) {
                                                foreach ($variacionInicial['atributos_mapa'] as $atribId => $valId) {
                                                    if ($atribId == $idAtributo && $valId == $idValor) {
                                                        $activo = true;
                                                        break;
                                                    }
                                                }
                                            }
                                        @endphp
                                        <button type="button"
                                                class="ml-variation-btn {{ $activo ? 'active' : '' }}"
                                                data-atributo-id="{{ $idAtributo }}"
                                                data-valor-id="{{ $idValor }}"
                                                data-valor-nombre="{{ $valor }}"
                                                onclick="seleccionarValorAtributo(this)">
                                            {{ $valor }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <input type="hidden" id="current-attribute-selection" value="">
                </div>
                @endif

                <!-- Descripción de variación -->
                <div id="variacion-descripcion-container" style="display: {{ $variacionInicial && $variacionInicial['descripcion'] ? 'block' : 'none' }}; margin: 15px 0; padding: 12px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #007bff;">
                    <span id="variacion-descripcion-texto">{{ $variacionInicial['descripcion'] ?? '' }}</span>
                </div>

                <!-- Botones de acción -->
                <div class="ml-actions">
                    <button class="ml-btn-favorite {{ $producto->esFavorito() ? 'active' : '' }}" 
                            onclick="toggleFavorito(this, {{ $producto->id_producto }})"
                            id="btn-favorito-{{ $producto->id_producto }}">
                        <i class="fas fa-heart"></i>
                        <span class="btn-text">{{ $producto->esFavorito() ? 'En favoritos' : 'Favorito' }}</span>
                    </button>
                    <button class="ml-btn-buy" onclick="agregarAlCarrito({{ $producto->id_producto }})">
                        <i class="fas fa-shopping-cart"></i>
                        Comprar ahora
                    </button>
                </div>
            </div>
        </div>

        <!-- Características del producto -->
        @if(count($datosIniciales['caracteristicas']) > 0)
        <div class="ml-features">
            <h2 class="ml-features-title">
                <i class="fas fa-list-alt"></i>
                Características del producto
            </h2>
            <table class="ml-features-table" id="caracteristicas-container">
                @foreach($datosIniciales['caracteristicas'] as $caracteristica)
                    <tr>
                        <td>{{ $caracteristica['nombre'] }}</td>
                        <td>
                            {{ $caracteristica['valor'] }}
                            @if($caracteristica['precio_extra'] > 0)
                                <span class="ml-feature-extra">+${{ number_format($caracteristica['precio_extra'], 2) }}</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
        @endif

        <!-- Descripción -->
        <div class="ml-description">
            <h2 class="ml-description-title">
                <i class="fas fa-align-left"></i>
                Descripción
            </h2>
            @if($producto->tDescripcion_corta)
                <div class="ml-description-short" id="producto-descripcion-corta">
                    {{ $producto->tDescripcion_corta }}
                </div>
            @endif
            
            @if($producto->tDescripcion_larga)
                <div class="ml-description-long" id="producto-descripcion-larga">
                    {{ $producto->tDescripcion_larga }}
                </div>
            @endif
        </div>

        <!-- Etiquetas -->
        @if($producto->etiquetas->count() > 0)
        <div class="ml-tags">
            <h2 class="ml-tags-title">
                <i class="fas fa-tags"></i>
                Etiquetas
            </h2>
            <div class="ml-tags-list">
                @foreach($producto->etiquetas as $etiqueta)
                    <span class="ml-tag">
                        <i class="fas fa-tag" style="color: {{ $etiqueta->color ?? '#007bff' }};"></i>
                        {{ $etiqueta->vNombre }}
                    </span>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Modal para imagen ampliada -->
        <div class="modal fade ml-modal" id="imagenModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content bg-transparent border-0">
                    <div class="modal-body text-center p-0 position-relative">
                        <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close" style="z-index: 20; background-color: rgba(0,0,0,0.5); padding: 10px; border-radius: 50%;"></button>
                        <img id="imagenAmpliada" src="" alt="" class="img-fluid" style="max-height: 90vh; border-radius: 8px; box-shadow: 0 0 20px rgba(0,0,0,0.5);">
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Variables globales
        let currentImageIndex = 0;
        let imagenesActuales = @json($imagenesAMostrar);
        let variacionesData = @json($variacionesData);
        let productoData = @json($productoData);
        let variacionSeleccionadaId = {{ $variacionInicial ? $variacionInicial['id'] : 'null' }};
        let atributosSeleccionados = {};

        // Guardar el nombre original del producto
        const nombreOriginalProducto = "{{ $producto->vNombre }}";
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        // Inicializar atributos seleccionados si hay una variación en la URL
        @if($variacionInicial)
            atributosSeleccionados = @json($variacionInicial['atributos_mapa'] ?? []);
        @endif

        // ============ FUNCIONES DE ZOOM ============
        let zoomActive = false;
        const zoomContainer = document.getElementById('zoom-container');
        const zoomImage = document.getElementById('mainImage');
        const zoomLens = document.getElementById('zoom-lens');

        function iniciarZoom() {
            if (!zoomContainer || !zoomImage || !zoomLens) return;
            
            const zoomRatio = 2.5;
            
            let zoomResult = document.getElementById('zoom-result');
            if (!zoomResult) {
                zoomResult = document.createElement('div');
                zoomResult.id = 'zoom-result';
                zoomResult.className = 'zoom-result';
                zoomResult.style.cssText = `
                    position: absolute;
                    top: 0;
                    left: 105%;
                    width: 400px;
                    height: 400px;
                    background-repeat: no-repeat;
                    background-size: ${zoomContainer.offsetWidth * zoomRatio}px ${zoomContainer.offsetHeight * zoomRatio}px;
                    border: 2px solid #007bff;
                    border-radius: 8px;
                    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
                    display: none;
                    z-index: 100;
                    background-color: white;
                `;
                zoomContainer.style.position = 'relative';
                zoomContainer.appendChild(zoomResult);
            }
            
            zoomContainer.addEventListener('mouseenter', function(e) {
                zoomActive = true;
                zoomLens.style.display = 'block';
                zoomResult.style.display = 'block';
                zoomImage.style.transform = 'scale(1.1)';
                actualizarZoom(e);
            });
            
            zoomContainer.addEventListener('mousemove', function(e) {
                if (!zoomActive) return;
                actualizarZoom(e);
            });
            
            zoomContainer.addEventListener('mouseleave', function() {
                zoomActive = false;
                zoomLens.style.display = 'none';
                zoomResult.style.display = 'none';
                zoomImage.style.transform = 'scale(1)';
            });
            
            function actualizarZoom(e) {
                const rect = zoomContainer.getBoundingClientRect();
                
                let x = (e.clientX - rect.left) / rect.width;
                let y = (e.clientY - rect.top) / rect.height;
                
                x = Math.max(0, Math.min(1, x));
                y = Math.max(0, Math.min(1, y));
                
                const lensWidth = 150;
                const lensHeight = 150;
                
                let lensLeft = (e.clientX - rect.left) - lensWidth / 2;
                let lensTop = (e.clientY - rect.top) - lensHeight / 2;
                
                lensLeft = Math.max(0, Math.min(rect.width - lensWidth, lensLeft));
                lensTop = Math.max(0, Math.min(rect.height - lensHeight, lensTop));
                
                zoomLens.style.left = lensLeft + 'px';
                zoomLens.style.top = lensTop + 'px';
                
                const bgX = (lensLeft / (rect.width - lensWidth)) * 100;
                const bgY = (lensTop / (rect.height - lensHeight)) * 100;
                
                zoomResult.style.backgroundImage = `url('${zoomImage.src}')`;
                zoomResult.style.backgroundPosition = `${bgX}% ${bgY}%`;
                
                const scale = 1.1 + (0.4 * (1 - Math.abs(x - 0.5) * 2));
                zoomImage.style.transform = `scale(${scale})`;
                zoomImage.style.transformOrigin = `${x * 100}% ${y * 100}%`;
            }
        }

        function abrirModalImagen() {
            if (!imagenesActuales || imagenesActuales.length === 0) return;
            const modalImg = document.getElementById('imagenAmpliada');
            modalImg.src = imagenesActuales[currentImageIndex];
            const modal = new bootstrap.Modal(document.getElementById('imagenModal'));
            modal.show();
        }

        function seleccionarImagen(index) {
            if (!imagenesActuales || imagenesActuales.length === 0) return;
            currentImageIndex = index;
            actualizarImagenPrincipal();
        }

        function cambiarImagen(direccion) {
            if (imagenesActuales.length <= 1) return;
            currentImageIndex += direccion;
            if (currentImageIndex < 0) {
                currentImageIndex = imagenesActuales.length - 1;
            } else if (currentImageIndex >= imagenesActuales.length) {
                currentImageIndex = 0;
            }
            actualizarImagenPrincipal();
        }

        function actualizarImagenPrincipal() {
            const mainImage = document.getElementById('mainImage');
            const miniaturas = document.querySelectorAll('.miniatura');
            const imagenActualSpan = document.getElementById('imagen-actual');
            const imageCounter = document.getElementById('imageCounter');
            const totalImagenesSpan = document.getElementById('total-imagenes');
            
            if (!imagenesActuales || imagenesActuales.length === 0) {
                mainImage.src = 'https://via.placeholder.com/400x400?text=Sin+Imagen';
                if (imageCounter) imageCounter.style.display = 'none';
                
                const miniaturasContainer = document.getElementById('miniaturas-container');
                if (miniaturasContainer) {
                    miniaturasContainer.innerHTML = '';
                }
                return;
            }
            
            if (mainImage && imagenesActuales[currentImageIndex]) {
                mainImage.src = imagenesActuales[currentImageIndex];
                
                const zoomResult = document.getElementById('zoom-result');
                if (zoomResult) {
                    zoomResult.style.backgroundImage = `url('${mainImage.src}')`;
                }
                
                if (imagenActualSpan) {
                    imagenActualSpan.textContent = currentImageIndex + 1;
                }
                
                if (imageCounter) imageCounter.style.display = 'block';
                
                miniaturas.forEach((thumb, index) => {
                    if (index === currentImageIndex) {
                        thumb.classList.add('activa');
                        thumb.style.borderColor = '#007bff';
                    } else {
                        thumb.classList.remove('activa');
                        thumb.style.borderColor = 'transparent';
                    }
                });
                
                if (totalImagenesSpan) {
                    totalImagenesSpan.textContent = imagenesActuales.length;
                }

                const botones = document.querySelectorAll('#imageControls button');
                if (botones.length === 2) {
                    botones[0].disabled = imagenesActuales.length <= 1;
                    botones[1].disabled = imagenesActuales.length <= 1;
                }
            }
        }

        // ============ FUNCIONES DE VARIACIONES ============
        function seleccionarValorAtributo(btn) {
            const atributoId = btn.getAttribute('data-atributo-id');
            const valorId = btn.getAttribute('data-valor-id');
            
            const estaSeleccionado = btn.classList.contains('active');
            
            if (estaSeleccionado) {
                btn.classList.remove('active');
                delete atributosSeleccionados[atributoId];
                
                if (Object.keys(atributosSeleccionados).length === 0) {
                    restaurarProductoOriginal();
                } else {
                    buscarYActualizarVariacion();
                }
            } else {
                document.querySelectorAll(`.ml-variation-btn[data-atributo-id="${atributoId}"]`).forEach(b => {
                    b.classList.remove('active');
                });
                
                btn.classList.add('active');
                atributosSeleccionados[atributoId] = valorId;
                buscarYActualizarVariacion();
            }
        }

        function buscarYActualizarVariacion() {
            if (Object.keys(atributosSeleccionados).length === 0) {
                restaurarProductoOriginal();
                return;
            }

            let variacionEncontrada = null;
            let variacionId = null;

            for (const varId in variacionesData) {
                const variacion = variacionesData[varId];
                let coincide = true;

                for (const [atribId, valorId] of Object.entries(atributosSeleccionados)) {
                    if (variacion.atributos_mapa[atribId] != valorId) {
                        coincide = false;
                        break;
                    }
                }

                if (coincide) {
                    variacionEncontrada = variacion;
                    variacionId = varId;
                    break;
                }
            }

            if (variacionEncontrada) {
                aplicarDatosVariacion(variacionEncontrada);
                variacionSeleccionadaId = variacionId;
            } else {
                restaurarProductoOriginal();
                showNotification('Combinación no disponible', 'info');
            }
        }

        function aplicarDatosVariacion(variacion) {
            console.log('Aplicando variación:', variacion);
            
            // Imágenes
            if (variacion.imagenes && variacion.imagenes.length > 0) {
                imagenesActuales = variacion.imagenes.slice();
            } else {
                imagenesActuales = productoData.imagenes.slice();
            }
            actualizarMiniaturas();
            currentImageIndex = 0;
            actualizarImagenPrincipal();

            // SKU
            document.getElementById('producto-sku').textContent = variacion.sku;

            // NOMBRE DEL PRODUCTO - NO CAMBIA
            document.getElementById('producto-nombre').textContent = nombreOriginalProducto;

            // Precios
            document.getElementById('producto-precio-info').style.display = 'none';
            document.getElementById('variacion-precio-info').style.display = 'block';
            
            const precioOriginalSpan = document.getElementById('variacion-precio-original');
            const descuentoBadge = document.getElementById('variacion-descuento-badge');
            const precioActualSpan = document.getElementById('variacion-precio-actual');
            
            const tieneOfertaVigente = variacion.tiene_oferta === true && 
                                       variacion.precio_oferta > 0 && 
                                       variacion.precio_oferta < variacion.precio_original;
            
            if (tieneOfertaVigente) {
                precioOriginalSpan.style.display = 'inline';
                precioOriginalSpan.textContent = '$' + formatNumber(variacion.precio_original);
                descuentoBadge.style.display = 'inline-block';
                
                const porcentaje = Math.round(((variacion.precio_original - variacion.precio_oferta) / variacion.precio_original) * 100);
                descuentoBadge.textContent = '-' + porcentaje + '%';
                
                precioActualSpan.textContent = '$' + formatNumber(variacion.precio_oferta);
            } else {
                precioOriginalSpan.style.display = 'none';
                descuentoBadge.style.display = 'none';
                precioActualSpan.textContent = '$' + formatNumber(variacion.precio_original);
            }

            // Impuestos
            const impuestosInfo = document.getElementById('impuestos-texto');
            if (variacion.texto_impuestos && variacion.texto_impuestos.trim() !== '') {
                impuestosInfo.textContent = 'Impuestos: ' + variacion.texto_impuestos;
            } else {
                impuestosInfo.textContent = 'Sin impuestos adicionales';
            }

            // Stock
            const stockContainer = document.getElementById('stock-container');
            const stockTexto = document.getElementById('stock-texto');
            const stockValue = parseInt(variacion.stock) || 0;
            
            stockContainer.className = 'ml-stock ' + (stockValue > 10 ? 'ml-stock-high' : (stockValue > 0 ? 'ml-stock-low' : 'ml-stock-out'));
            
            if (stockValue > 10) {
                stockTexto.innerHTML = '<i class="fas fa-check-circle"></i> Stock disponible (' + formatNumber(stockValue, 0) + ' unidades)';
            } else if (stockValue > 0) {
                stockTexto.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ¡Últimas unidades! (Solo ' + formatNumber(stockValue, 0) + ' disponibles)';
            } else {
                stockTexto.innerHTML = '<i class="fas fa-times-circle"></i> Producto agotado';
            }

            // Descripción de variación
            const descripcionContainer = document.getElementById('variacion-descripcion-container');
            const descripcionTexto = document.getElementById('variacion-descripcion-texto');
            if (variacion.descripcion && variacion.descripcion.trim() !== '') {
                descripcionTexto.textContent = variacion.descripcion;
                descripcionContainer.style.display = 'block';
            } else {
                descripcionContainer.style.display = 'none';
            }

            // Características
            actualizarCaracteristicas(variacion.caracteristicas || []);
        }

        function restaurarProductoOriginal() {
            document.querySelectorAll('.ml-variation-btn').forEach(b => {
                b.classList.remove('active');
            });
            atributosSeleccionados = {};
            variacionSeleccionadaId = null;

            imagenesActuales = productoData.imagenes.slice();
            actualizarMiniaturas();
            currentImageIndex = 0;
            actualizarImagenPrincipal();

            document.getElementById('producto-sku').textContent = productoData.sku;
            document.getElementById('producto-nombre').textContent = nombreOriginalProducto;

            document.getElementById('producto-precio-info').style.display = 'block';
            document.getElementById('variacion-precio-info').style.display = 'none';

            const impuestosInfo = document.getElementById('impuestos-texto');
            if (productoData.texto_impuestos && productoData.texto_impuestos.trim() !== '') {
                impuestosInfo.textContent = 'Impuestos: ' + productoData.texto_impuestos;
            } else {
                impuestosInfo.textContent = 'Sin impuestos adicionales';
            }

            const stockContainer = document.getElementById('stock-container');
            const stockTexto = document.getElementById('stock-texto');
            const stockValue = parseInt(productoData.stock) || 0;
            
            stockContainer.className = 'ml-stock ' + (stockValue > 10 ? 'ml-stock-high' : (stockValue > 0 ? 'ml-stock-low' : 'ml-stock-out'));
            
            if (stockValue > 10) {
                stockTexto.innerHTML = '<i class="fas fa-check-circle"></i> Stock disponible (' + formatNumber(stockValue, 0) + ' unidades)';
            } else if (stockValue > 0) {
                stockTexto.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ¡Últimas unidades! (Solo ' + formatNumber(stockValue, 0) + ' disponibles)';
            } else {
                stockTexto.innerHTML = '<i class="fas fa-times-circle"></i> Producto agotado';
            }

            document.getElementById('variacion-descripcion-container').style.display = 'none';
            actualizarCaracteristicas(productoData.caracteristicas || []);
        }

        function actualizarCaracteristicas(caracteristicas) {
            const container = document.getElementById('caracteristicas-container');
            if (!container) return;
            
            if (!caracteristicas || caracteristicas.length === 0) {
                container.innerHTML = '';
                return;
            }
            
            let html = '';
            caracteristicas.forEach(car => {
                html += `
                    <tr>
                        <td>${car.nombre}</td>
                        <td>
                            ${car.valor}
                            ${car.precio_extra && car.precio_extra > 0 ? 
                                '<span class="ml-feature-extra">+$' + formatNumber(car.precio_extra, 2) + '</span>' : ''}
                        </td>
                    </tr>
                `;
            });
            
            container.innerHTML = html;
        }

        function actualizarMiniaturas() {
            const miniaturasContainer = document.getElementById('miniaturas-container');
            const totalImagenesSpan = document.getElementById('total-imagenes');
            
            if (!miniaturasContainer) return;
            
            miniaturasContainer.innerHTML = '';
            
            if (!imagenesActuales || imagenesActuales.length === 0) {
                if (totalImagenesSpan) totalImagenesSpan.textContent = '0';
                return;
            }
            
            imagenesActuales.forEach((imgUrl, index) => {
                const div = document.createElement('div');
                div.className = 'miniatura-item';
                div.style.width = '70px';
                div.setAttribute('onclick', `seleccionarImagen(${index})`);

                const img = document.createElement('img');
                img.src = imgUrl;
                img.className = `img-thumbnail miniatura ${index === currentImageIndex ? 'activa' : ''}`;
                img.style.cssText = 'width: 70px; height: 70px; object-fit: cover; cursor: pointer; border: 2px solid ' + (index === currentImageIndex ? '#007bff' : 'transparent') + '; border-radius: 8px;';
                img.alt = `Miniatura ${index + 1}`;
                img.onerror = function() { this.src = 'https://via.placeholder.com/70x70?text=Error'; };
                
                div.appendChild(img);
                miniaturasContainer.appendChild(div);
            });
            
            if (totalImagenesSpan) totalImagenesSpan.textContent = imagenesActuales.length;
        }

        function formatNumber(num, decimals = 2) {
            if (num === null || num === undefined) return '0';
            return parseFloat(num).toFixed(decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }

        // Toast notification
        let currentToast = null;
        let toastTimeout = null;

        function showNotification(message, type = 'success') {
            if (currentToast) {
                currentToast.classList.remove('show');
                setTimeout(() => {
                    if (currentToast && currentToast.parentNode) currentToast.parentNode.removeChild(currentToast);
                    currentToast = null;
                }, 300);
            }
            if (toastTimeout) clearTimeout(toastTimeout);

            const toast = document.createElement('div');
            toast.className = `ml-toast ${type}`;
            
            let icon = 'fa-check-circle';
            if (type === 'error') icon = 'fa-exclamation-circle';
            if (type === 'info') icon = 'fa-info-circle';
            
            toast.innerHTML = `<i class="fas ${icon}"></i><span>${message}</span>`;
            
            document.body.appendChild(toast);
            currentToast = toast;
            
            setTimeout(() => toast.classList.add('show'), 10);
            
            toastTimeout = setTimeout(() => {
                if (currentToast === toast) {
                    toast.classList.remove('show');
                    setTimeout(() => {
                        if (toast.parentNode) toast.parentNode.removeChild(toast);
                        if (currentToast === toast) currentToast = null;
                    }, 300);
                }
            }, 3000);
        }

        // Favoritos
        function toggleFavorito(button, productoId) {
            if (button.disabled) return;
            
            const estabaActivo = button.classList.contains('active');
            
            button.disabled = true;
            button.classList.add('loading');
            
            @if(!Auth::check())
                // Invitado - redirigir a favoritos de invitado
                window.location.href = '{{ route("favoritos.invitado.index") }}?from_favoritos=true&producto=' + productoId;
                button.disabled = false;
                button.classList.remove('loading');
                return;
            @endif

            fetch(`/favoritos/toggle-producto/${productoId}`, {
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
                        window.location.href = '{{ route("login") }}?from_favoritos=true&redirect=' + encodeURIComponent(window.location.href);
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
                        button.classList.add('active');
                        button.querySelector('.btn-text').textContent = 'En favoritos';
                        showNotification('Producto agregado a favoritos', 'success');
                        
                        localStorage.setItem('last_favorito_action', 'added');
                        localStorage.setItem('last_favorito_id', productoId);
                        localStorage.setItem('last_favorito_tipo', 'producto');
                        localStorage.setItem('last_favorito_time', Date.now());
                    } else {
                        button.classList.remove('active');
                        button.querySelector('.btn-text').textContent = 'Favorito';
                        showNotification('Producto eliminado de favoritos', 'info');
                        
                        localStorage.setItem('last_favorito_action', 'removed');
                        localStorage.setItem('last_favorito_id', productoId);
                        localStorage.setItem('last_favorito_tipo', 'producto');
                        localStorage.setItem('last_favorito_time', Date.now());
                    }
                } else {
                    if (estabaActivo) {
                        button.classList.add('active');
                        button.querySelector('.btn-text').textContent = 'En favoritos';
                    } else {
                        button.classList.remove('active');
                        button.querySelector('.btn-text').textContent = 'Favorito';
                    }
                    showNotification(data.message || 'Error al gestionar favoritos', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (estabaActivo) {
                    button.classList.add('active');
                    button.querySelector('.btn-text').textContent = 'En favoritos';
                } else {
                    button.classList.remove('active');
                    button.querySelector('.btn-text').textContent = 'Favorito';
                }
                showNotification('Error de conexión', 'error');
            })
            .finally(() => {
                setTimeout(() => { 
                    button.disabled = false;
                    button.classList.remove('loading');
                }, 500);
            });
        }

        function agregarAlCarrito(productoId) {
            showNotification('Producto agregado al carrito', 'success');
        }

        // Inicialización
        document.addEventListener('DOMContentLoaded', function() {
            actualizarMiniaturas();
            
            if (zoomImage.complete) {
                iniciarZoom();
            } else {
                zoomImage.addEventListener('load', iniciarZoom);
            }
            
            document.addEventListener('keydown', function(e) {
                if (e.key === 'ArrowLeft') cambiarImagen(-1);
                if (e.key === 'ArrowRight') cambiarImagen(1);
            });

            window.addEventListener('resize', function() {
                const zoomResult = document.getElementById('zoom-result');
                if (zoomResult) zoomResult.remove();
                iniciarZoom();
            });
            
            console.log('Producto data:', productoData);
            console.log('Variaciones data:', variacionesData);
            console.log('Variación seleccionada:', variacionSeleccionadaId);

            // Verificar acciones recientes de localStorage
            const lastAction = localStorage.getItem('last_favorito_action');
            const lastId = localStorage.getItem('last_favorito_id');
            const lastTipo = localStorage.getItem('last_favorito_tipo');
            const lastTime = localStorage.getItem('last_favorito_time');
            
            if (lastAction && lastId && lastTime && (Date.now() - lastTime) < 5000 && lastTipo === 'producto') {
                const button = document.getElementById(`btn-favorito-${lastId}`);
                if (button) {
                    if (lastAction === 'removed') {
                        button.classList.remove('active');
                        button.querySelector('.btn-text').textContent = 'Favorito';
                    } else if (lastAction === 'added') {
                        button.classList.add('active');
                        button.querySelector('.btn-text').textContent = 'En favoritos';
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