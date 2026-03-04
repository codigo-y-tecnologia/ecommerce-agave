@extends('layouts.app')

@section('title', 'Detalle del Producto - ' . $producto->vNombre)

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header con breadcrumbs y acciones -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-transparent">
                <div class="card-body p-0">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-2">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('productos.index') }}" class="text-decoration-none">Productos</a></li>
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
        $productoData = [
            'id' => 'producto',
            'sku' => $producto->vCodigo_barras,
            'precio' => (float)$producto->dPrecio_venta,
            'precio_oferta' => (float)($producto->dPrecio_oferta ?? 0),
            'tiene_oferta' => (bool)$producto->bTiene_oferta,
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

            $variacionesData[$var->id_variacion] = [
                'id' => $var->id_variacion,
                'sku' => $var->vSKU,
                'precio' => (float)$var->dPrecio,
                'precio_oferta' => (float)($var->dPrecio_oferta ?? 0),
                'tiene_oferta' => (bool)$var->bTiene_oferta,
                'stock' => (int)$var->iStock,
                'atributos_texto' => $atributosTexto,
                'atributos_mapa' => $atributosParaMapa,
                'imagenes' => $var->imagenes ?? [],
                'descripcion' => $var->tDescripcion ?? '',
                'peso' => $var->dPeso,
                'largo' => $var->dLargo_cm,
                'ancho' => $var->dAncho_cm,
                'alto' => $var->dAlto_cm,
                'clase_envio' => $var->vClase_envio
            ];
        }
    @endphp

    <!-- PRIMERA FILA: Imagen principal + Información básica + Selector de variaciones -->
    <div class="row g-4 mb-4">
        <!-- Columna de imagen y galería -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <!-- Contenedor de imagen principal -->
                    <div class="text-center mb-3" style="background-color: #ffffff; border-radius: 8px; border: 1px solid #e0e0e0; overflow: hidden;">
                        <div class="position-relative d-flex justify-content-center align-items-center" style="height: 400px; background-color: #ffffff;">
                            <img id="mainImage" 
                                 src="{{ !empty($imagenesProducto) ? $imagenesProducto[0] : 'https://via.placeholder.com/400x400?text=Sin+Imagen' }}" 
                                 class="img-fluid" 
                                 style="max-height: 380px; max-width: 100%; object-fit: contain; cursor: pointer;"
                                 alt="{{ $producto->vNombre }}"
                                 onclick="abrirModalImagen()"
                                 onerror="this.onerror=null; this.src='https://via.placeholder.com/400x400?text=Error';">
                            
                            @if($producto->bActivo)
                                <span class="position-absolute top-0 start-0 badge bg-success mt-2 ms-2 px-3 py-2" style="z-index: 10;">
                                    <i class="fas fa-check-circle me-1"></i>Activo
                                </span>
                            @endif

                            <!-- Controles de navegación -->
                            <div id="imageControls" class="position-absolute w-100 d-flex justify-content-between px-2" style="top: 50%; transform: translateY(-50%); z-index: 5;">
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
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                                    <i class="fas fa-barcode text-primary"></i>
                                </div>
                                <div>
                                    <small class="text-muted text-uppercase">SKU</small>
                                    <h6 class="fw-bold mb-0" id="producto-sku-display">{{ $producto->vCodigo_barras }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
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
                        <div class="col-md-6">
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
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                                    <i class="fas fa-boxes text-warning"></i>
                                </div>
                                <div>
                                    <small class="text-muted text-uppercase">Stock</small>
                                    <h6 class="fw-bold mb-0" id="producto-stock-display">
                                        <span class="{{ $producto->iStock > 10 ? 'text-success' : ($producto->iStock > 0 ? 'text-warning' : 'text-danger') }}" id="stock-texto">
                                            {{ number_format($producto->iStock) }} unidades
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

    <!-- SEGUNDA FILA: Precios e Impuestos (CORREGIDA) -->
    <div class="row g-4 mb-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-chart-line me-2 text-primary"></i>Información de Precios
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
                            <tbody>
                                <tr>
                                    <td class="py-3 px-3">
                                        <strong>Precio de compra</strong>
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="fw-semibold">${{ number_format($producto->dPrecio_compra, 2) }}</span>
                                    </td>
                                    <td class="py-3 px-3 text-muted">-</td>
                                </tr>
                                <tr>
                                    <td class="py-3 px-3">
                                        <strong>Precio de venta</strong>
                                        <span id="oferta-badge" style="display: none;">
                                            <br><small class="text-danger">(Oferta activa)</small>
                                        </span>
                                    </td>
                                    <td class="py-3 px-3" id="precio-container">
                                        @if($producto->ofertaVigente())
                                            <span class="text-decoration-line-through text-muted me-2" id="precio-original">
                                                ${{ number_format($producto->dPrecio_venta, 2) }}
                                            </span>
                                            <span class="fw-bold text-danger" id="precio-actual">
                                                ${{ number_format($producto->dPrecio_oferta, 2) }}
                                            </span>
                                        @else
                                            <span class="fw-bold" id="precio-actual">${{ number_format($producto->dPrecio_venta, 2) }}</span>
                                            <span id="precio-original" style="display: none;"></span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3" id="impuestos-container">
                                        @php
                                            $precioBase = $producto->ofertaVigente() ? $producto->dPrecio_oferta : $producto->dPrecio_venta;
                                            $totalImpuestos = 0;
                                            $detalleImpuestos = [];
                                            foreach($producto->impuestos as $impuesto) {
                                                $montoImpuesto = $precioBase * ($impuesto->dPorcentaje / 100);
                                                $totalImpuestos += $montoImpuesto;
                                                $detalleImpuestos[] = $impuesto->vNombre . ': $' . number_format($montoImpuesto, 2);
                                            }
                                        @endphp
                                        +${{ number_format($totalImpuestos, 2) }}
                                        @if(count($detalleImpuestos) > 0)
                                            <br><small class="text-muted">{{ implode(' | ', $detalleImpuestos) }}</small>
                                        @endif
                                    </td>
                                </tr>
                                <tr class="bg-light">
                                    <td class="py-3 px-3 rounded-start">
                                        <strong class="text-primary">TOTAL (con impuestos)</strong>
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="fw-bold text-primary fs-5" id="precio-total">
                                            ${{ number_format($precioBase + $totalImpuestos, 2) }}
                                        </span>
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
                <div class="card-body px-4">
                    @if($producto->impuestos->count() > 0)
                        @foreach($producto->impuestos as $impuesto)
                            <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3 mb-3">
                                <div>
                                    <strong>{{ $impuesto->vNombre }}</strong>
                                    <div><small class="text-muted">{{ $impuesto->eTipo }}</small></div>
                                </div>
                                <span class="badge bg-primary fs-6">{{ $impuesto->dPorcentaje }}%</span>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-file-invoice-dollar fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Sin impuestos asignados</p>
                        </div>
                    @endif
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

    <!-- Modal para ampliar imágenes -->
    <div class="modal fade" id="imagenModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Imagen del Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="imagenAmpliada" src="" alt="" class="img-fluid" style="max-height: 70vh;">
                </div>
            </div>
        </div>
    </div>
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

// Función para aplicar datos de una variación
function aplicarDatosVariacion(variacion) {
    console.log('Variación seleccionada:', variacion);
    console.log('Imágenes de la variación:', variacion.imagenes);
    
    // Actualizar las imágenes
    if (variacion.imagenes && Array.isArray(variacion.imagenes) && variacion.imagenes.length > 0) {
        imagenesActuales = variacion.imagenes.slice();
    } else {
        imagenesActuales = productoData.imagenes.slice();
    }
    
    console.log('Imágenes actuales después de asignar:', imagenesActuales);
    
    // Reconstruir las miniaturas
    actualizarMiniaturas();
    
    // Reiniciar el índice y mostrar la primera imagen
    currentImageIndex = 0;
    actualizarImagenPrincipal();

    // Actualizar SKU
    document.getElementById('producto-sku-display').textContent = variacion.sku;

    // Actualizar precios
    const precioContainer = document.getElementById('precio-container');
    const precioActualSpan = document.getElementById('precio-actual');
    const precioOriginalSpan = document.getElementById('precio-original');
    const ofertaBadge = document.getElementById('oferta-badge');

    if (variacion.tiene_oferta && variacion.precio_oferta > 0 && variacion.precio_oferta < variacion.precio) {
        precioActualSpan.className = 'fw-bold text-danger';
        precioActualSpan.textContent = '$' + variacion.precio_oferta.toFixed(2);
        if (!precioOriginalSpan) {
            const nuevoOriginal = document.createElement('span');
            nuevoOriginal.id = 'precio-original';
            nuevoOriginal.className = 'text-decoration-line-through text-muted me-2';
            nuevoOriginal.textContent = '$' + variacion.precio.toFixed(2);
            precioContainer.insertBefore(nuevoOriginal, precioActualSpan);
        } else {
            precioOriginalSpan.style.display = 'inline';
            precioOriginalSpan.textContent = '$' + variacion.precio.toFixed(2);
        }
        if (ofertaBadge) ofertaBadge.style.display = 'inline';
    } else {
        precioActualSpan.className = 'fw-bold';
        precioActualSpan.textContent = '$' + variacion.precio.toFixed(2);
        if (precioOriginalSpan) precioOriginalSpan.style.display = 'none';
        if (ofertaBadge) ofertaBadge.style.display = 'none';
    }

    // Actualizar stock
    const stockDisplay = document.getElementById('producto-stock-display');
    stockDisplay.innerHTML = '';
    const stockSpan = document.createElement('span');
    stockSpan.id = 'stock-texto';
    let stockClass = variacion.stock > 10 ? 'text-success' : (variacion.stock > 0 ? 'text-warning' : 'text-danger');
    stockSpan.className = stockClass;
    stockSpan.textContent = variacion.stock + ' unidades';
    stockDisplay.appendChild(stockSpan);

    // Mostrar/ocultar descripción
    const descripcionContainer = document.getElementById('variacion-descripcion-container');
    const descripcionTexto = document.getElementById('variacion-descripcion-texto');
    if (variacion.descripcion && variacion.descripcion.trim() !== '') {
        descripcionTexto.textContent = variacion.descripcion;
        descripcionContainer.style.display = 'block';
    } else {
        descripcionContainer.style.display = 'none';
    }

    // Actualizar dimensiones
    document.getElementById('producto-peso').textContent = variacion.peso ? parseFloat(variacion.peso).toFixed(3) + ' kg' : (productoData.peso ? parseFloat(productoData.peso).toFixed(3) + ' kg' : '—');
    document.getElementById('producto-largo').textContent = variacion.largo ? parseFloat(variacion.largo).toFixed(2) + ' cm' : (productoData.largo ? parseFloat(productoData.largo).toFixed(2) + ' cm' : '—');
    document.getElementById('producto-ancho').textContent = variacion.ancho ? parseFloat(variacion.ancho).toFixed(2) + ' cm' : (productoData.ancho ? parseFloat(productoData.ancho).toFixed(2) + ' cm' : '—');
    document.getElementById('producto-alto').textContent = variacion.alto ? parseFloat(variacion.alto).toFixed(2) + ' cm' : (productoData.alto ? parseFloat(productoData.alto).toFixed(2) + ' cm' : '—');

    // Actualizar clase de envío
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

// Función para restaurar datos del producto original
function restaurarProductoOriginal() {
    document.querySelectorAll('.variacion-atributo-btn').forEach(b => {
        b.classList.remove('btn-primary', 'active');
        b.classList.add('btn-outline-secondary');
    });
    atributosSeleccionados = {};
    variacionSeleccionadaId = null;

    imagenesActuales = productoData.imagenes.slice();
    actualizarMiniaturas();
    currentImageIndex = 0;
    actualizarImagenPrincipal();

    document.getElementById('producto-sku-display').textContent = productoData.sku;
    
    const precioContainer = document.getElementById('precio-container');
    const precioActualSpan = document.getElementById('precio-actual');
    const precioOriginalSpan = document.getElementById('precio-original');
    const ofertaBadge = document.getElementById('oferta-badge');

    if (productoData.tiene_oferta && productoData.precio_oferta > 0 && productoData.precio_oferta < productoData.precio) {
        precioActualSpan.className = 'fw-bold text-danger';
        precioActualSpan.textContent = '$' + productoData.precio_oferta.toFixed(2);
        if (!precioOriginalSpan) {
            const nuevoOriginal = document.createElement('span');
            nuevoOriginal.id = 'precio-original';
            nuevoOriginal.className = 'text-decoration-line-through text-muted me-2';
            nuevoOriginal.textContent = '$' + productoData.precio.toFixed(2);
            precioContainer.insertBefore(nuevoOriginal, precioActualSpan);
        } else {
            precioOriginalSpan.style.display = 'inline';
            precioOriginalSpan.textContent = '$' + productoData.precio.toFixed(2);
        }
        if (ofertaBadge) ofertaBadge.style.display = 'inline';
    } else {
        precioActualSpan.className = 'fw-bold';
        precioActualSpan.textContent = '$' + productoData.precio.toFixed(2);
        if (precioOriginalSpan) precioOriginalSpan.style.display = 'none';
        if (ofertaBadge) ofertaBadge.style.display = 'none';
    }

    const stockDisplay = document.getElementById('producto-stock-display');
    stockDisplay.innerHTML = '';
    const stockSpan = document.createElement('span');
    stockSpan.id = 'stock-texto';
    let stockClass = productoData.stock > 10 ? 'text-success' : (productoData.stock > 0 ? 'text-warning' : 'text-danger');
    stockSpan.className = stockClass;
    stockSpan.textContent = productoData.stock + ' unidades';
    stockDisplay.appendChild(stockSpan);

    document.getElementById('variacion-descripcion-container').style.display = 'none';
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
    // Inicializar miniaturas
    actualizarMiniaturas();
    
    if (imagenesActuales.length > 0) {
        currentImageIndex = 0;
        actualizarImagenPrincipal();
    }

    console.log('Producto data:', productoData);
    console.log('Variaciones data:', variacionesData);

    document.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowLeft') cambiarImagen(-1);
        else if (e.key === 'ArrowRight') cambiarImagen(1);
    });
});
</script>

<style>
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