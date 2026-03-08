@extends('layouts.app')

@section('title', 'Detalle del Producto - ' . $producto->vNombre)

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header con breadcrumbs y acciones -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-transparent">
                <div class="card-body p-0">
                    <!-- Breadcrumb solo con jerarquía de categorías -->
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-2">
                            @php
                                // Función para obtener la jerarquía de categorías
                                function getCategoriaPadres($categoria, &$padres = []) {
                                    if ($categoria && $categoria->categoriaPadre) {
                                        getCategoriaPadres($categoria->categoriaPadre, $padres);
                                    }
                                    if ($categoria) {
                                        $padres[] = $categoria;
                                    }
                                    return $padres;
                                }
                                
                                $categoriaActual = $producto->categoria;
                                $jerarquiaCategorias = [];
                                if ($categoriaActual) {
                                    $jerarquiaCategorias = getCategoriaPadres($categoriaActual);
                                }
                            @endphp
                            
                            @foreach($jerarquiaCategorias as $categoria)
                                <li class="breadcrumb-item">
                                    <a href="{{ route('categorias.show', $categoria->id_categoria) }}" class="text-decoration-none">
                                        {{ $categoria->vNombre }}
                                    </a>
                                </li>
                            @endforeach
                            
                            <li class="breadcrumb-item active" aria-current="page">{{ $producto->vNombre }}</li>
                        </ol>
                    </nav>

                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <div>
                            <h2 class="fw-bold mb-1">{{ $producto->vNombre }}</h2>
                            <span class="text-muted">
                                <i class="fas fa-barcode me-1"></i>SKU: <span class="fw-semibold" id="producto-sku">{{ $producto->vCodigo_barras }}</span>
                                <span class="mx-2">|</span>
                                <i class="fas fa-calendar-alt me-1"></i>Registro: {{ $producto->tFecha_registro ? \Carbon\Carbon::parse($producto->tFecha_registro)->format('d/m/Y') : 'N/A' }}
                            </span>
                        </div>
                        <div>
                            <a href="{{ route('productos.edit', $producto->id_producto) }}" class="btn btn-primary me-2">
                                <i class="fas fa-edit me-1"></i>Editar
                            </a>
                            <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Volver
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @php
        // --- DATOS DEL PRODUCTO PADRE ---
        $imagenesProducto = $producto->imagenes ?? [];
        $tieneVariaciones = $producto->tieneVariaciones();
        
        // Calcular si el producto padre tiene oferta vigente
        $productoTieneOferta = $producto->ofertaVigente();
        $precioBaseProducto = $productoTieneOferta ? $producto->dPrecio_oferta : $producto->dPrecio_venta;
        
        // Calcular impuestos del producto padre
        $totalImpuestosProducto = 0;
        $detalleImpuestosProducto = [];
        foreach($producto->impuestos as $impuesto) {
            $montoImpuesto = $precioBaseProducto * ($impuesto->dPorcentaje / 100);
            $totalImpuestosProducto += $montoImpuesto;
            $detalleImpuestosProducto[] = $impuesto->vNombre . ': $' . number_format($montoImpuesto, 2);
        }
        
        $productoData = [
            'id' => 'producto',
            'sku' => $producto->vCodigo_barras,
            'precio_original' => (float)$producto->dPrecio_venta,
            'precio_oferta' => (float)($producto->dPrecio_oferta ?? 0),
            'tiene_oferta' => $productoTieneOferta,
            'precio_base' => (float)$precioBaseProducto,
            'total_impuestos' => (float)$totalImpuestosProducto,
            'precio_final' => (float)($precioBaseProducto + $totalImpuestosProducto),
            'detalle_impuestos' => $detalleImpuestosProducto,
            'stock' => (int)$producto->iStock,
            'tiene_variaciones' => $tieneVariaciones,
            'imagenes' => $imagenesProducto,
            'descripcion_corta' => $producto->tDescripcion_corta ?? '',
            'descripcion_larga' => $producto->tDescripcion_larga ?? '',
            'peso' => $producto->dPeso,
            'largo' => $producto->dLargo_cm,
            'ancho' => $producto->dAncho_cm,
            'alto' => $producto->dAlto_cm,
            'clase_envio' => $producto->vClase_envio,
        ];

        // --- DATOS DE LAS VARIACIONES ---
        $variacionesData = [];
        $atributosAgrupados = [];
        foreach ($producto->variaciones as $var) {
            $atributosTexto = [];
            $atributosParaMapa = [];
            foreach($var->atributos as $atributoRel) {
                if($atributoRel->atributo && $atributoRel->valor) {
                    $atributosTexto[] = $atributoRel->atributo->vNombre . ': ' . $atributoRel->valor->vValor;
                    $atributosParaMapa[$atributoRel->id_atributo] = $atributoRel->id_atributo_valor;

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
            $variacionTieneOferta = $var->ofertaVigente();
            $precioBaseVariacion = $variacionTieneOferta ? $var->dPrecio_oferta : $var->dPrecio;
            
            // Calcular impuestos de la variación
            $impuestoVariacion = $var->impuesto ?? $producto->impuestos->first();
            $totalImpuestosVariacion = 0;
            $detalleImpuestosVariacion = [];
            
            if ($impuestoVariacion) {
                $montoImpuesto = $precioBaseVariacion * ($impuestoVariacion->dPorcentaje / 100);
                $totalImpuestosVariacion = $montoImpuesto;
                $detalleImpuestosVariacion[] = $impuestoVariacion->vNombre . ': $' . number_format($montoImpuesto, 2);
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
                'detalle_impuestos' => $detalleImpuestosVariacion,
                'stock' => (int)$var->iStock,
                'atributos_texto' => $atributosTexto,
                'atributos_mapa' => $atributosParaMapa,
                'imagenes' => $var->imagenes ?? [],
                'descripcion' => $var->tDescripcion ?? '',
                'peso' => $var->dPeso ? (float)$var->dPeso : null,
                'largo' => $var->dLargo_cm ? (float)$var->dLargo_cm : null,
                'ancho' => $var->dAncho_cm ? (float)$var->dAncho_cm : null,
                'alto' => $var->dAlto_cm ? (float)$var->dAlto_cm : null,
                'clase_envio' => $var->vClase_envio
            ];
        }
    @endphp

    <!-- PRIMERA FILA: Imagen principal con ZOOM + Información básica + Selector de variaciones -->
    <div class="row g-4 mb-4">
        <!-- Columna de imagen y galería con ZOOM -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <!-- Contenedor de imagen principal con ZOOM -->
                    <div class="position-relative mb-3" style="background-color: #ffffff; border-radius: 8px; border: 1px solid #e0e0e0; overflow: hidden;">
                        <!-- Contenedor del zoom -->
                        <div id="zoom-container" class="zoom-container" style="position: relative; width: 100%; height: 400px; overflow: hidden; cursor: crosshair;">
                            <!-- Imagen principal -->
                            <img id="mainImage" 
                                 src="{{ !empty($imagenesProducto) ? $imagenesProducto[0] : 'https://via.placeholder.com/400x400?text=Sin+Imagen' }}" 
                                 class="img-fluid zoom-image" 
                                 style="width: 100%; height: 100%; object-fit: contain; transition: transform 0.1s ease;"
                                 alt="{{ $producto->vNombre }}"
                                 onclick="abrirModalImagen()"
                                 onerror="this.onerror=null; this.src='https://via.placeholder.com/400x400?text=Error';">
                            
                            <!-- Lupa (el área que sigue al mouse) -->
                            <div id="zoom-lens" class="zoom-lens" style="display: none; position: absolute; width: 150px; height: 150px; border: 2px solid #007bff; background-color: rgba(255,255,255,0.3); pointer-events: none; z-index: 10; border-radius: 4px;"></div>
                            
                            @if($producto->bActivo)
                                <span class="position-absolute top-0 start-0 badge bg-success mt-2 ms-2 px-3 py-2" style="z-index: 15;">
                                    <i class="fas fa-check-circle me-1"></i>Activo
                                </span>
                            @endif

                            <!-- Controles de navegación -->
                            <div id="imageControls" class="position-absolute w-100 d-flex justify-content-between px-2" style="top: 50%; transform: translateY(-50%); z-index: 15;">
                                <button type="button" class="btn btn-sm btn-light rounded-circle shadow-sm" onclick="event.stopPropagation(); cambiarImagen(-1)" style="width: 36px; height: 36px; opacity: 0.8; background-color: rgba(255,255,255,0.9);" {{ count($imagenesProducto) <= 1 ? 'disabled' : '' }}>
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-light rounded-circle shadow-sm" onclick="event.stopPropagation(); cambiarImagen(1)" style="width: 36px; height: 36px; opacity: 0.8; background-color: rgba(255,255,255,0.9);" {{ count($imagenesProducto) <= 1 ? 'disabled' : '' }}>
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Miniaturas horizontales con flex-wrap -->
                    <div id="miniaturas-container" class="d-flex flex-wrap justify-content-center gap-2 mt-2" style="padding-bottom: 5px; max-height: 170px; overflow-y: auto; border: 1px solid #eee; border-radius: 8px; padding: 10px;"></div>

                    <!-- Contador de imágenes -->
                    <div id="imageCounter" class="text-center mt-2" {{ count($imagenesProducto) <= 0 ? 'style=display:none;' : '' }}>
                        <span class="badge bg-light text-dark" id="contador-imagenes">
                            <span id="imagen-actual">1</span> / <span id="total-imagenes">{{ count($imagenesProducto) }}</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna de información básica y selector de variaciones -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-info-circle me-2 text-primary"></i>Información General
                    </h5>
                </div>
                <div class="card-body pt-0 px-4">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                                    <i class="fas fa-tag text-success"></i>
                                </div>
                                <div>
                                    <small class="text-muted text-uppercase">Categoría</small>
                                    <h6 class="fw-bold mb-0">{{ $producto->categoria->vNombre ?? 'Sin categoría' }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                                    <i class="fas fa-industry text-info"></i>
                                </div>
                                <div>
                                    <small class="text-muted text-uppercase">Marca</small>
                                    <h6 class="fw-bold mb-0">{{ $producto->marca->vNombre ?? 'Sin marca' }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                                    <i class="fas fa-boxes text-warning"></i>
                                </div>
                                <div>
                                    <small class="text-muted text-uppercase">Stock</small>
                                    <h6 class="fw-bold mb-0" id="producto-stock-display">
                                        <span class="{{ $producto->iStock > 10 ? 'text-success' : ($producto->iStock > 0 ? 'text-warning' : 'text-danger') }}" id="stock-texto">
                                            {{ number_format($producto->iStock) }} {{ $producto->iStock == 1 ? 'unidad' : 'unidades' }}
                                        </span>
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SELECTOR DE VARIACIONES CON BOTONES POR ATRIBUTO -->
            @if($producto->tieneVariaciones() && $producto->variaciones->count() > 0)
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-cubes me-2 text-primary"></i>Variaciones del Producto
                    </h5>
                </div>
                <div class="card-body px-4">
                    <div id="variaciones-selector-container">
                        @foreach($atributosAgrupados as $idAtributo => $atributo)
                            <div class="mb-4">
                                <label class="form-label fw-bold text-primary">{{ $atributo['nombre'] }}</label>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($atributo['valores'] as $idValor => $valor)
                                        <button type="button"
                                                class="btn btn-outline-secondary variacion-atributo-btn"
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
            </div>
            @endif
        </div>
    </div>

    <!-- SEGUNDA FILA: Precios e Impuestos -->
    <div class="row g-4 mb-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-chart-line me-2 text-primary"></i>
                        <span id="precios-titulo">Información de Precios - Producto Principal</span>
                    </h5>
                </div>
                <div class="card-body px-4">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead class="bg-light">
                                <tr>
                                    <th class="py-3 px-3 rounded-start">Concepto</th>
                                    <th class="py-3 px-3">Precio</th>
                                    <th class="py-3 px-3 rounded-end">Impuestos</th>
                                </tr>
                            </thead>
                            <tbody id="tabla-precios-body">
                                <!-- PRODUCTO PADRE (visible por defecto) -->
                                <tr id="producto-precio-compra-row">
                                    <td class="py-3 px-3">
                                        <strong>Precio de compra</strong>
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="fw-semibold" id="producto-precio-compra">${{ number_format($producto->dPrecio_compra, 2) }}</span>
                                    </td>
                                    <td class="py-3 px-3 text-muted">-</td>
                                </tr>
                                <tr id="producto-precio-venta-row">
                                    <td class="py-3 px-3">
                                        <strong>Precio de venta</strong>
                                        @if($productoTieneOferta)
                                            <span class="badge bg-danger ms-2" id="producto-oferta-badge">Oferta activa</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3" id="producto-precio-container">
                                        @if($productoTieneOferta)
                                            <span class="text-decoration-line-through text-muted me-2" id="producto-precio-original">
                                                ${{ number_format($producto->dPrecio_venta, 2) }}
                                            </span>
                                            <span class="fw-bold text-danger" id="producto-precio-actual">
                                                ${{ number_format($producto->dPrecio_oferta, 2) }}
                                            </span>
                                        @else
                                            <span class="fw-bold" id="producto-precio-actual">${{ number_format($producto->dPrecio_venta, 2) }}</span>
                                            <span id="producto-precio-original" style="display: none;"></span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3" id="producto-impuestos-container">
                                        +${{ number_format($totalImpuestosProducto, 2) }}
                                        @if(count($detalleImpuestosProducto) > 0)
                                            <br><small class="text-muted">{{ implode(' | ', $detalleImpuestosProducto) }}</small>
                                        @endif
                                    </td>
                                </tr>
                                <tr class="bg-light" id="producto-total-row">
                                    <td class="py-3 px-3 rounded-start">
                                        <strong class="text-primary">TOTAL (con impuestos)</strong>
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="fw-bold text-primary fs-5" id="producto-precio-total">
                                            ${{ number_format($precioBaseProducto + $totalImpuestosProducto, 2) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 rounded-end"></td>
                                </tr>
                                
                                <!-- VARIACIÓN (oculto por defecto) -->
                                <tr id="variacion-precio-compra-row" style="display: none;">
                                    <td class="py-3 px-3">
                                        <strong>Precio de compra (variación)</strong>
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="fw-semibold" id="variacion-precio-compra">$0.00</span>
                                    </td>
                                    <td class="py-3 px-3 text-muted">-</td>
                                </tr>
                                <tr id="variacion-precio-venta-row" style="display: none;">
                                    <td class="py-3 px-3">
                                        <strong>Precio de venta (variación)</strong>
                                        <span class="badge bg-danger ms-2" id="variacion-oferta-badge" style="display: none;">Oferta activa</span>
                                    </td>
                                    <td class="py-3 px-3" id="variacion-precio-container">
                                        <span class="text-decoration-line-through text-muted me-2" id="variacion-precio-original" style="display: none;"></span>
                                        <span class="fw-bold" id="variacion-precio-actual">$0.00</span>
                                    </td>
                                    <td class="py-3 px-3" id="variacion-impuestos-container">
                                        +$0.00
                                        <br><small class="text-muted" id="variacion-detalle-impuestos"></small>
                                    </td>
                                </tr>
                                <tr class="bg-light" id="variacion-total-row" style="display: none;">
                                    <td class="py-3 px-3 rounded-start">
                                        <strong class="text-primary">TOTAL variación (con impuestos)</strong>
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="fw-bold text-primary fs-5" id="variacion-precio-total">$0.00</span>
                                    </td>
                                    <td class="py-3 px-3 rounded-end"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-file-invoice-dollar me-2 text-primary"></i>Impuestos Aplicados
                    </h5>
                </div>
                <div class="card-body px-4" id="impuestos-card-body">
                    @if($producto->impuestos->count() > 0)
                        @foreach($producto->impuestos as $impuesto)
                            <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3 mb-3 producto-impuesto-item">
                                <div>
                                    <strong>{{ $impuesto->vNombre }}</strong>
                                    <div><small class="text-muted">{{ $impuesto->eTipo }}</small></div>
                                </div>
                                <span class="badge bg-primary fs-6">{{ $impuesto->dPorcentaje }}%</span>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5" id="producto-sin-impuestos">
                            <i class="fas fa-file-invoice-dollar fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Sin impuestos asignados</p>
                        </div>
                    @endif
                    
                    <!-- Contenedor para impuestos de variación (oculto por defecto) -->
                    <div id="variacion-impuestos-items" style="display: none;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- DESCRIPCIÓN DE VARIACIÓN SELECCIONADA -->
    <div id="variacion-descripcion-container" style="display: none;" class="mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0">
                    <i class="fas fa-align-left me-2 text-primary"></i>Descripción de la Variación
                </h5>
            </div>
            <div class="card-body px-4">
                <p id="variacion-descripcion-texto" class="mb-0"></p>
            </div>
        </div>
    </div>

    <!-- TERCERA FILA: Dimensiones y Envío -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-ruler-combined me-2 text-primary"></i>Dimensiones y Envío
                    </h5>
                </div>
                <div class="card-body px-4">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="p-3 bg-light rounded-3 text-center">
                                <i class="fas fa-weight-hanging fa-2x text-primary mb-2"></i>
                                <small class="d-block text-muted">Peso</small>
                                <strong id="producto-peso">{{ $producto->dPeso ? number_format($producto->dPeso, 3) . ' kg' : '—' }}</strong>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="p-3 bg-light rounded-3 text-center">
                                <i class="fas fa-ruler-vertical fa-2x text-primary mb-2"></i>
                                <small class="d-block text-muted">Largo</small>
                                <strong id="producto-largo">{{ $producto->dLargo_cm ? number_format($producto->dLargo_cm, 2) . ' cm' : '—' }}</strong>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="p-3 bg-light rounded-3 text-center">
                                <i class="fas fa-ruler-horizontal fa-2x text-primary mb-2"></i>
                                <small class="d-block text-muted">Ancho</small>
                                <strong id="producto-ancho">{{ $producto->dAncho_cm ? number_format($producto->dAncho_cm, 2) . ' cm' : '—' }}</strong>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="p-3 bg-light rounded-3 text-center">
                                <i class="fas fa-arrows-alt-v fa-2x text-primary mb-2"></i>
                                <small class="d-block text-muted">Alto</small>
                                <strong id="producto-alto">{{ $producto->dAlto_cm ? number_format($producto->dAlto_cm, 2) . ' cm' : '—' }}</strong>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="text-muted">Clase de envío</small>
                                    @php
                                        $claseEnvioText = '';
                                        $claseEnvioClass = '';
                                        switch($producto->vClase_envio) {
                                            case 'estandar':
                                                $claseEnvioText = 'Estándar';
                                                $claseEnvioClass = 'bg-primary';
                                                break;
                                            case 'express':
                                                $claseEnvioText = 'Express';
                                                $claseEnvioClass = 'bg-success';
                                                break;
                                            case 'fragil':
                                                $claseEnvioText = 'Frágil';
                                                $claseEnvioClass = 'bg-warning text-dark';
                                                break;
                                            case 'grandes_dimensiones':
                                                $claseEnvioText = 'Grandes dimensiones';
                                                $claseEnvioClass = 'bg-danger';
                                                break;
                                            default:
                                                $claseEnvioText = 'No especificada';
                                                $claseEnvioClass = 'bg-secondary';
                                        }
                                    @endphp
                                    <span class="badge {{ $claseEnvioClass }}" id="producto-clase-envio">{{ $claseEnvioText }}</span>
                                </div>
                                @if($producto->dLargo_cm && $producto->dAncho_cm && $producto->dAlto_cm)
                                    <small class="text-muted" id="producto-volumen">Volumen: {{ number_format($producto->dLargo_cm * $producto->dAncho_cm * $producto->dAlto_cm, 2) }} cm³</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- DESCRIPCIÓN DEL PRODUCTO -->
    @if($producto->tDescripcion_corta || $producto->tDescripcion_larga)
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-align-left me-2 text-primary"></i>Descripción del Producto
                    </h5>
                </div>
                <div class="card-body px-4">
                    @if($producto->tDescripcion_corta)
                        <div class="mb-4">
                            <small class="text-muted text-uppercase">Descripción corta</small>
                            <p class="fs-5 mb-0 p-3 bg-light rounded-3">{{ $producto->tDescripcion_corta }}</p>
                        </div>
                    @endif

                    @if($producto->tDescripcion_larga)
                        <div>
                            <small class="text-muted text-uppercase">Descripción detallada</small>
                            <div class="p-3 bg-light rounded-3" style="white-space: pre-line;">
                                {{ $producto->tDescripcion_larga }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- ETIQUETAS -->
    @if($producto->etiquetas->count() > 0)
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-tags me-2 text-primary"></i>Etiquetas
                    </h5>
                </div>
                <div class="card-body px-4">
                    @foreach($producto->etiquetas as $etiqueta)
                        <span class="badge me-2 mb-2 p-3"
                              style="background-color: {{ $etiqueta->color ?? '#6c757d' }}; color: white; font-size: 14px;">
                            <i class="fas fa-tag me-1"></i>{{ $etiqueta->vNombre }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- ATRIBUTOS DEL PRODUCTO PADRE -->
    @if($producto->valoresAtributos->count() > 0)
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-list-alt me-2 text-primary"></i>Atributos Generales
                    </h5>
                </div>
                <div class="card-body px-4">
                    @php
                        $atributosAgrupadosGenerales = [];
                        foreach($producto->valoresAtributos as $valor) {
                            $atributo = $valor->atributo;
                            if($atributo) {
                                if(!isset($atributosAgrupadosGenerales[$atributo->id_atributo])) {
                                    $atributosAgrupadosGenerales[$atributo->id_atributo] = [
                                        'nombre' => $atributo->vNombre,
                                        'valores' => []
                                    ];
                                }
                                $atributosAgrupadosGenerales[$atributo->id_atributo]['valores'][] = [
                                    'valor' => $valor->vValor,
                                    'precio_extra' => $valor->pivot->dPrecio_extra ?? 0
                                ];
                            }
                        }
                    @endphp

                    <div class="row">
                        @foreach($atributosAgrupadosGenerales as $atributo)
                            <div class="col-md-4 mb-3">
                                <div class="bg-light rounded-3 p-3">
                                    <strong class="text-primary">{{ $atributo['nombre'] }}</strong>
                                    <div class="mt-2">
                                        @foreach($atributo['valores'] as $valor)
                                            <span class="badge bg-white text-dark border me-1 mb-1 p-2">
                                                {{ $valor['valor'] }}
                                                @if($valor['precio_extra'] > 0)
                                                    <span class="text-success">(+${{ number_format($valor['precio_extra'], 2) }})</span>
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal para ampliar imágenes (mejorado) -->
    <div class="modal fade" id="imagenModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-body text-center p-0 position-relative">
                    <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close" style="z-index: 20; background-color: rgba(0,0,0,0.5); padding: 10px; border-radius: 50%;"></button>
                    <img id="imagenAmpliada" src="" alt="" class="img-fluid" style="max-height: 90vh; border-radius: 8px; box-shadow: 0 0 20px rgba(0,0,0,0.5);">
                </div>
            </div>
        </div>
    </div>

    <!-- ACCIONES FINALES -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('productos.edit', $producto->id_producto) }}" class="btn btn-primary px-5 py-3">
                    <i class="fas fa-edit me-2"></i>Editar Producto
                </a>
                <button type="button" class="btn btn-outline-danger px-5 py-3" onclick="confirmDelete({{ $producto->id_producto }})">
                    <i class="fas fa-trash me-2"></i>Eliminar
                </button>
                <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary px-5 py-3">
                    <i class="fas fa-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Formulario de eliminación oculto -->
    <form id="deleteForm-{{ $producto->id_producto }}" action="{{ route('productos.destroy', $producto->id_producto) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Variables globales
let currentImageIndex = 0;
let imagenesActuales = @json($imagenesProducto);
let variacionesData = @json($variacionesData);
let productoData = @json($productoData);
let variacionSeleccionadaId = null;
let atributosSeleccionados = {};

// ============ FUNCIONES DE ZOOM ============
let zoomActive = false;
const zoomContainer = document.getElementById('zoom-container');
const zoomImage = document.getElementById('mainImage');
const zoomLens = document.getElementById('zoom-lens');

function iniciarZoom() {
    if (!zoomContainer || !zoomImage || !zoomLens) return;
    
    // Calcular la relación de zoom (2.5x más grande)
    const zoomRatio = 2.5;
    
    // Crear imagen de zoom si no existe
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
    
    // Evento mouseenter
    zoomContainer.addEventListener('mouseenter', function(e) {
        zoomActive = true;
        zoomLens.style.display = 'block';
        zoomResult.style.display = 'block';
        zoomImage.style.transform = 'scale(1.1)';
        actualizarZoom(e);
    });
    
    // Evento mousemove
    zoomContainer.addEventListener('mousemove', function(e) {
        if (!zoomActive) return;
        actualizarZoom(e);
    });
    
    // Evento mouseleave
    zoomContainer.addEventListener('mouseleave', function() {
        zoomActive = false;
        zoomLens.style.display = 'none';
        zoomResult.style.display = 'none';
        zoomImage.style.transform = 'scale(1)';
    });
    
    function actualizarZoom(e) {
        const rect = zoomContainer.getBoundingClientRect();
        
        // Posición del mouse relativa al contenedor (0 a 1)
        let x = (e.clientX - rect.left) / rect.width;
        let y = (e.clientY - rect.top) / rect.height;
        
        // Limitar entre 0 y 1
        x = Math.max(0, Math.min(1, x));
        y = Math.max(0, Math.min(1, y));
        
        // Tamaño de la lupa (150x150)
        const lensWidth = 150;
        const lensHeight = 150;
        
        // Posición de la lupa (centrada en el mouse)
        let lensLeft = (e.clientX - rect.left) - lensWidth / 2;
        let lensTop = (e.clientY - rect.top) - lensHeight / 2;
        
        // Limitar la lupa dentro del contenedor
        lensLeft = Math.max(0, Math.min(rect.width - lensWidth, lensLeft));
        lensTop = Math.max(0, Math.min(rect.height - lensHeight, lensTop));
        
        // Posicionar la lupa
        zoomLens.style.left = lensLeft + 'px';
        zoomLens.style.top = lensTop + 'px';
        
        // Calcular posición del fondo en el zoom result (inverso)
        const bgX = (lensLeft / (rect.width - lensWidth)) * 100;
        const bgY = (lensTop / (rect.height - lensHeight)) * 100;
        
        // Actualizar el zoom result
        zoomResult.style.backgroundImage = `url('${zoomImage.src}')`;
        zoomResult.style.backgroundPosition = `${bgX}% ${bgY}%`;
        
        // Actualizar transform de la imagen principal para efecto adicional
        const scale = 1.1 + (0.4 * (1 - Math.abs(x - 0.5) * 2));
        zoomImage.style.transform = `scale(${scale})`;
        zoomImage.style.transformOrigin = `${x * 100}% ${y * 100}%`;
    }
}

// Función para abrir modal con imagen ampliada
function abrirModalImagen() {
    if (!imagenesActuales || imagenesActuales.length === 0) return;
    const modalImg = document.getElementById('imagenAmpliada');
    modalImg.src = imagenesActuales[currentImageIndex];
    const modal = new bootstrap.Modal(document.getElementById('imagenModal'));
    modal.show();
}

// Función para seleccionar imagen
function seleccionarImagen(index) {
    if (!imagenesActuales || imagenesActuales.length === 0) return;
    currentImageIndex = index;
    actualizarImagenPrincipal();
}

// Función para cambiar imagen con flechas
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

// Función para actualizar imagen principal, miniaturas y contador
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
        
        // Reiniciar zoom para la nueva imagen
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

// Función para seleccionar valor de atributo - CON TOGGLE
function seleccionarValorAtributo(btn) {
    const atributoId = btn.getAttribute('data-atributo-id');
    const valorId = btn.getAttribute('data-valor-id');
    
    const estaSeleccionado = btn.classList.contains('btn-primary');
    
    if (estaSeleccionado) {
        btn.classList.remove('btn-primary', 'active');
        btn.classList.add('btn-outline-secondary');
        
        delete atributosSeleccionados[atributoId];
        
        if (Object.keys(atributosSeleccionados).length === 0) {
            restaurarProductoOriginal();
            showNotification('Mostrando producto original', 'info');
        } else {
            buscarYActualizarVariacion();
        }
    } else {
        document.querySelectorAll(`.variacion-atributo-btn[data-atributo-id="${atributoId}"]`).forEach(b => {
            b.classList.remove('btn-primary', 'active');
            b.classList.add('btn-outline-secondary');
        });
        
        btn.classList.remove('btn-outline-secondary');
        btn.classList.add('btn-primary', 'active');
        
        atributosSeleccionados[atributoId] = valorId;
        
        buscarYActualizarVariacion();
    }
}

// Función para buscar y actualizar variación
function buscarYActualizarVariacion() {
    if (Object.keys(atributosSeleccionados).length === 0) {
        restaurarProductoOriginal();
        return;
    }

    let variacionEncontrada = null;

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
            variacionSeleccionadaId = varId;
            break;
        }
    }

    if (variacionEncontrada) {
        aplicarDatosVariacion(variacionEncontrada);
    } else {
        restaurarProductoOriginal();
        showNotification('Combinación de atributos no disponible', 'warning');
    }
}

// Función para aplicar datos de variación (ACTUALIZA TODOS LOS DATOS)
function aplicarDatosVariacion(variacion) {
    console.log('Variación seleccionada:', variacion);
    
    // Ocultar filas del producto padre
    document.getElementById('producto-precio-compra-row').style.display = 'none';
    document.getElementById('producto-precio-venta-row').style.display = 'none';
    document.getElementById('producto-total-row').style.display = 'none';
    
    // Mostrar filas de la variación
    document.getElementById('variacion-precio-compra-row').style.display = 'table-row';
    document.getElementById('variacion-precio-venta-row').style.display = 'table-row';
    document.getElementById('variacion-total-row').style.display = 'table-row';
    
    // Actualizar título
    document.getElementById('precios-titulo').textContent = 'Información de Precios - Variación Seleccionada';
    
    // ACTUALIZAR SKU (en el header)
    document.getElementById('producto-sku').textContent = variacion.sku;
    
    // ACTUALIZAR IMÁGENES
    if (variacion.imagenes && Array.isArray(variacion.imagenes) && variacion.imagenes.length > 0) {
        imagenesActuales = variacion.imagenes.slice();
    } else {
        imagenesActuales = productoData.imagenes.slice();
    }
    
    actualizarMiniaturas();
    currentImageIndex = 0;
    actualizarImagenPrincipal();

    // ACTUALIZAR PRECIOS DE VARIACIÓN
    document.getElementById('variacion-precio-compra').textContent = '$' + formatNumber(variacion.precio_original);
    
    const precioActualSpan = document.getElementById('variacion-precio-actual');
    const precioOriginalSpan = document.getElementById('variacion-precio-original');
    const ofertaBadge = document.getElementById('variacion-oferta-badge');
    
    if (variacion.tiene_oferta && variacion.precio_oferta > 0 && variacion.precio_oferta < variacion.precio_original) {
        precioOriginalSpan.style.display = 'inline';
        precioOriginalSpan.textContent = '$' + formatNumber(variacion.precio_original);
        precioActualSpan.className = 'fw-bold text-danger';
        precioActualSpan.textContent = '$' + formatNumber(variacion.precio_oferta);
        ofertaBadge.style.display = 'inline-block';
    } else {
        precioOriginalSpan.style.display = 'none';
        precioActualSpan.className = 'fw-bold';
        precioActualSpan.textContent = '$' + formatNumber(variacion.precio_original);
        ofertaBadge.style.display = 'none';
    }
    
    // ACTUALIZAR IMPUESTOS
    document.getElementById('variacion-impuestos-container').innerHTML = 
        '+$' + formatNumber(variacion.total_impuestos) + 
        (variacion.detalle_impuestos.length > 0 ? '<br><small class="text-muted">' + variacion.detalle_impuestos.join(' | ') + '</small>' : '');
    
    document.getElementById('variacion-precio-total').textContent = '$' + formatNumber(variacion.precio_final);

    // ACTUALIZAR STOCK
    const stockDisplay = document.getElementById('producto-stock-display');
    stockDisplay.innerHTML = '';
    
    const stockSpan = document.createElement('span');
    stockSpan.id = 'stock-texto';
    
    const stockValue = parseInt(variacion.stock) || 0;
    
    let stockClass = '';
    if (stockValue > 10) {
        stockClass = 'text-success';
    } else if (stockValue > 0) {
        stockClass = 'text-warning';
    } else {
        stockClass = 'text-danger';
    }
    
    stockSpan.className = stockClass;
    stockSpan.textContent = formatNumber(stockValue, 0) + ' ' + (stockValue === 1 ? 'unidad' : 'unidades');
    stockDisplay.appendChild(stockSpan);

    // ACTUALIZAR DESCRIPCIÓN DE VARIACIÓN
    const descripcionContainer = document.getElementById('variacion-descripcion-container');
    const descripcionTexto = document.getElementById('variacion-descripcion-texto');
    if (variacion.descripcion && variacion.descripcion.trim() !== '') {
        descripcionTexto.textContent = variacion.descripcion;
        descripcionContainer.style.display = 'block';
    } else {
        descripcionContainer.style.display = 'none';
    }

    // ACTUALIZAR DIMENSIONES
    document.getElementById('producto-peso').textContent = variacion.peso ? formatNumber(variacion.peso, 3) + ' kg' : (productoData.peso ? formatNumber(productoData.peso, 3) + ' kg' : '—');
    document.getElementById('producto-largo').textContent = variacion.largo ? formatNumber(variacion.largo, 2) + ' cm' : (productoData.largo ? formatNumber(productoData.largo, 2) + ' cm' : '—');
    document.getElementById('producto-ancho').textContent = variacion.ancho ? formatNumber(variacion.ancho, 2) + ' cm' : (productoData.ancho ? formatNumber(productoData.ancho, 2) + ' cm' : '—');
    document.getElementById('producto-alto').textContent = variacion.alto ? formatNumber(variacion.alto, 2) + ' cm' : (productoData.alto ? formatNumber(productoData.alto, 2) + ' cm' : '—');

    // ACTUALIZAR CLASE DE ENVÍO
    const claseEnvioSpan = document.getElementById('producto-clase-envio');
    if (claseEnvioSpan) {
        let claseText = '';
        let claseClass = '';
        const claseEnvio = variacion.clase_envio || productoData.clase_envio;
        switch(claseEnvio) {
            case 'estandar': claseText = 'Estándar'; claseClass = 'bg-primary'; break;
            case 'express': claseText = 'Express'; claseClass = 'bg-success'; break;
            case 'fragil': claseText = 'Frágil'; claseClass = 'bg-warning text-dark'; break;
            case 'grandes_dimensiones': claseText = 'Grandes dimensiones'; claseClass = 'bg-danger'; break;
            default: claseText = claseEnvio ? claseEnvio : 'No especificada'; claseClass = 'bg-secondary';
        }
        claseEnvioSpan.className = 'badge ' + claseClass;
        claseEnvioSpan.textContent = claseText;
    }
}

// Función para formatear números con comas
function formatNumber(num, decimals = 2) {
    if (num === null || num === undefined) return '0';
    return parseFloat(num).toFixed(decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

// Función para restaurar datos del producto original
function restaurarProductoOriginal() {
    document.querySelectorAll('.variacion-atributo-btn').forEach(b => {
        b.classList.remove('btn-primary', 'active');
        b.classList.add('btn-outline-secondary');
    });
    atributosSeleccionados = {};
    variacionSeleccionadaId = null;

    // Mostrar filas del producto padre
    document.getElementById('producto-precio-compra-row').style.display = 'table-row';
    document.getElementById('producto-precio-venta-row').style.display = 'table-row';
    document.getElementById('producto-total-row').style.display = 'table-row';
    
    // Ocultar filas de la variación
    document.getElementById('variacion-precio-compra-row').style.display = 'none';
    document.getElementById('variacion-precio-venta-row').style.display = 'none';
    document.getElementById('variacion-total-row').style.display = 'none';
    
    // Actualizar título
    document.getElementById('precios-titulo').textContent = 'Información de Precios - Producto Principal';

    // RESTAURAR SKU
    document.getElementById('producto-sku').textContent = productoData.sku;

    // RESTAURAR IMÁGENES
    imagenesActuales = productoData.imagenes.slice();
    actualizarMiniaturas();
    currentImageIndex = 0;
    actualizarImagenPrincipal();

    // RESTAURAR STOCK DEL PRODUCTO PADRE
    const stockDisplay = document.getElementById('producto-stock-display');
    stockDisplay.innerHTML = '';
    
    const stockSpan = document.createElement('span');
    stockSpan.id = 'stock-texto';
    
    const stockValue = parseInt(productoData.stock) || 0;
    let stockClass = '';
    if (stockValue > 10) {
        stockClass = 'text-success';
    } else if (stockValue > 0) {
        stockClass = 'text-warning';
    } else {
        stockClass = 'text-danger';
    }
    
    stockSpan.className = stockClass;
    stockSpan.textContent = formatNumber(stockValue, 0) + ' ' + (stockValue === 1 ? 'unidad' : 'unidades');
    stockDisplay.appendChild(stockSpan);

    // Ocultar descripción de variación
    document.getElementById('variacion-descripcion-container').style.display = 'none';
    
    // RESTAURAR DIMENSIONES
    document.getElementById('producto-peso').textContent = productoData.peso ? formatNumber(productoData.peso, 3) + ' kg' : '—';
    document.getElementById('producto-largo').textContent = productoData.largo ? formatNumber(productoData.largo, 2) + ' cm' : '—';
    document.getElementById('producto-ancho').textContent = productoData.ancho ? formatNumber(productoData.ancho, 2) + ' cm' : '—';
    document.getElementById('producto-alto').textContent = productoData.alto ? formatNumber(productoData.alto, 2) + ' cm' : '—';
    
    // RESTAURAR CLASE DE ENVÍO
    const claseEnvioSpan = document.getElementById('producto-clase-envio');
    if (claseEnvioSpan && productoData.clase_envio) {
        let claseText = '';
        let claseClass = '';
        switch(productoData.clase_envio) {
            case 'estandar': claseText = 'Estándar'; claseClass = 'bg-primary'; break;
            case 'express': claseText = 'Express'; claseClass = 'bg-success'; break;
            case 'fragil': claseText = 'Frágil'; claseClass = 'bg-warning text-dark'; break;
            case 'grandes_dimensiones': claseText = 'Grandes dimensiones'; claseClass = 'bg-danger'; break;
            default: claseText = productoData.clase_envio; claseClass = 'bg-secondary';
        }
        claseEnvioSpan.className = 'badge ' + claseClass;
        claseEnvioSpan.textContent = claseText;
    }
}

// Función para actualizar miniaturas
function actualizarMiniaturas() {
    const miniaturasContainer = document.getElementById('miniaturas-container');
    const imageCounter = document.getElementById('imageCounter');
    const totalImagenesSpan = document.getElementById('total-imagenes');
    
    if (!miniaturasContainer) return;

    miniaturasContainer.innerHTML = '';
    
    if (!imagenesActuales || imagenesActuales.length === 0) {
        if (imageCounter) imageCounter.style.display = 'none';
        return;
    }
    
    if (imageCounter) imageCounter.style.display = 'block';
    
    imagenesActuales.forEach((imgUrl, index) => {
        const div = document.createElement('div');
        div.className = 'miniatura-item flex-shrink-0';
        div.setAttribute('onclick', `seleccionarImagen(${index})`);
        div.style.width = '70px';

        const img = document.createElement('img');
        img.src = imgUrl;
        img.className = `img-thumbnail miniatura ${index === 0 ? 'activa' : ''}`;
        img.style.cssText = 'width: 70px; height: 70px; object-fit: cover; cursor: pointer; border: 2px solid transparent; border-radius: 8px;';
        img.alt = `Miniatura ${index + 1}`;
        img.onerror = function() { this.src = 'https://via.placeholder.com/70x70?text=Error'; };
        if (index === 0) img.style.borderColor = '#007bff';
        div.appendChild(img);
        miniaturasContainer.appendChild(div);
    });

    if (totalImagenesSpan) {
        totalImagenesSpan.textContent = imagenesActuales.length;
    }
}

// Función para mostrar notificación
function showNotification(message, type = 'success') {
    Swal.fire({
        icon: type,
        title: message,
        showConfirmButton: false,
        timer: 2000,
        toast: true,
        position: 'top-end'
    });
}

// Función para eliminar producto
function confirmDelete(id) {
    Swal.fire({
        title: '¿Eliminar producto?',
        text: 'Esta acción no se puede deshacer. Se eliminarán todas las variaciones, imágenes y relaciones asociadas.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('deleteForm-' + id).submit();
        }
    });
}

// Inicializar al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    actualizarMiniaturas();
    
    if (imagenesActuales.length > 0) {
        currentImageIndex = 0;
        actualizarImagenPrincipal();
        // Iniciar zoom después de que la imagen esté cargada
        setTimeout(iniciarZoom, 500);
    }

    console.log('Producto data:', productoData);
    console.log('Variaciones data:', variacionesData);

    document.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowLeft') cambiarImagen(-1);
        else if (e.key === 'ArrowRight') cambiarImagen(1);
    });
    
    // Reiniciar zoom cuando cambia el tamaño de la ventana
    window.addEventListener('resize', function() {
        const zoomResult = document.getElementById('zoom-result');
        if (zoomResult) zoomResult.remove();
        iniciarZoom();
    });
});
</script>

<style>
/* Estilos para el zoom */
.zoom-container {
    position: relative;
    width: 100%;
    height: 400px;
    overflow: hidden;
    background-color: #f8f9fa;
}

.zoom-container img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    transition: transform 0.1s ease;
    cursor: crosshair;
}

.zoom-lens {
    position: absolute;
    width: 150px;
    height: 150px;
    border: 2px solid #007bff;
    background-color: rgba(255, 255, 255, 0.3);
    pointer-events: none;
    z-index: 10;
    border-radius: 4px;
    box-shadow: 0 0 0 1px rgba(0,0,0,0.1);
}

.zoom-result {
    position: absolute;
    top: 0;
    left: 105%;
    width: 400px;
    height: 400px;
    background-repeat: no-repeat;
    border: 2px solid #007bff;
    border-radius: 8px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.2);
    display: none;
    z-index: 100;
    background-color: white;
}

/* Responsive: ocultar zoom en pantallas pequeñas */
@media (max-width: 1200px) {
    .zoom-result {
        width: 300px;
        height: 300px;
    }
}

@media (max-width: 992px) {
    .zoom-result {
        display: none !important;
    }
    .zoom-lens {
        display: none !important;
    }
}

/* Animación suave para la imagen al hacer zoom */
.zoom-image {
    transition: transform 0.2s ease-out !important;
}

/* Estilo para el modal de imagen ampliada */
#imagenModal .modal-content {
    background: transparent;
    border: none;
}

#imagenModal .btn-close {
    filter: invert(1) grayscale(100%) brightness(200%);
    opacity: 0.8;
}

#imagenModal .btn-close:hover {
    opacity: 1;
}

/* Efecto hover en miniaturas */
.miniatura-item {
    transition: transform 0.2s ease;
}

.miniatura-item:hover {
    transform: scale(1.1);
    z-index: 5;
}

.variacion-atributo-btn {
    min-width: 60px;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    transition: all 0.2s ease;
}

.variacion-atributo-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.variacion-atributo-btn.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
}

.miniatura {
    transition: all 0.2s ease;
    cursor: pointer;
}

.miniatura:hover {
    transform: scale(1.1);
    border-color: #007bff !important;
}

.miniatura.activa {
    border-color: #007bff !important;
    box-shadow: 0 0 0 2px rgba(0,123,255,0.3);
}

#imageControls button {
    opacity: 0.5;
    transition: opacity 0.2s ease;
}

#imageControls button:hover {
    opacity: 1;
}

#imageControls button:disabled {
    opacity: 0.2;
    cursor: not-allowed;
}
</style>
@endpush

@endsection