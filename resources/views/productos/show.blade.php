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
        $imagenesProducto = $producto->imagenes ?? []; // Array simple de URLs
        $productoData = [
            'id' => 'producto',
            'sku' => $producto->vCodigo_barras,
            'precio' => (float)$producto->dPrecio_venta,
            'precio_oferta' => (float)($producto->dPrecio_oferta ?? 0),
            'tiene_oferta' => (bool)$producto->bTiene_oferta,
            'stock' => (int)$producto->iStock,
            'imagenes' => $imagenesProducto,
            'descripcion_corta' => $producto->tDescripcion_corta ?? '',
            'descripcion_larga' => $producto->tDescripcion_larga ?? '',
            'peso' => $producto->dPeso,
            'largo' => $producto->dLargo_cm,
            'ancho' => $producto->dAncho_cm,
            'alto' => $producto->dAlto_cm,
            'clase_envio' => $producto->vClase_envio,
        ];

        // --- DATOS DE LAS VARIACIONES (separados) ---
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
                // Aquí van SOLO las imágenes de la variación
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
        <!-- Columna de imagen y galería (tamaño fijo) -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <!-- Contenedor de imagen principal con tamaño fijo y controles -->
                    <div class="text-center mb-3" style="height: 300px; display: flex; flex-direction: column;">
                        <div class="position-relative d-flex justify-content-center align-items-center" style="height: 250px; background-color: #f8f9fa; border-radius: 8px;">
                            <img id="mainImage" 
                                 src="{{ !empty($imagenesProducto) ? $imagenesProducto[0] : 'https://via.placeholder.com/400x400?text=Sin+Imagen' }}" 
                                 class="img-fluid rounded-3 border" 
                                 style="max-height: 240px; max-width: 100%; object-fit: contain;"
                                 alt="{{ $producto->vNombre }}"
                                 onerror="this.onerror=null; this.src='https://via.placeholder.com/400x400?text=Error';">
                            
                            @if($producto->bActivo)
                                <span class="position-absolute top-0 start-0 badge bg-success mt-2 ms-2 px-3 py-2">
                                    <i class="fas fa-check-circle me-1"></i>Activo
                                </span>
                            @endif
                            
                            <!-- Controles de navegación de imágenes -->
                            <div id="imageControls" class="position-absolute w-100 d-flex justify-content-between px-2" style="top: 50%; transform: translateY(-50%);">
                                <button type="button" class="btn btn-sm btn-light rounded-circle shadow-sm" onclick="cambiarImagen(-1)" style="width: 36px; height: 36px; opacity: 0.8;" {{ count($imagenesProducto) <= 1 ? 'disabled' : '' }}>
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-light rounded-circle shadow-sm" onclick="cambiarImagen(1)" style="width: 36px; height: 36px; opacity: 0.8;" {{ count($imagenesProducto) <= 1 ? 'disabled' : '' }}>
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Contador de imágenes -->
                        <div id="imageCounter" class="mt-2">
                            <span class="badge bg-secondary" id="contador-imagenes">
                                <span id="imagen-actual">1</span> / <span id="total-imagenes">{{ count($imagenesProducto) }}</span>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Miniaturas (scroll horizontal) -->
                    <div id="miniaturas-container" class="d-flex gap-2 overflow-auto pb-2" style="scrollbar-width: thin;">
                        @foreach($imagenesProducto as $index => $imgUrl)
                            <div class="miniatura-item flex-shrink-0" onclick="seleccionarImagen({{ $index }})">
                                <img src="{{ $imgUrl }}" 
                                     class="img-thumbnail miniatura {{ $index === 0 ? 'activa' : '' }}" 
                                     style="width: 70px; height: 70px; object-fit: cover; cursor: pointer; border: 2px solid transparent;"
                                     alt="Miniatura {{ $index + 1 }}"
                                     onerror="this.onerror=null; this.src='https://via.placeholder.com/70x70?text=Error';">
                            </div>
                        @endforeach
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
                                        @if($producto->tieneVariaciones())
                                            <span class="badge bg-info">Variable por variaciones</span>
                                        @else
                                            <span class="{{ $producto->iStock > 10 ? 'text-success' : ($producto->iStock > 0 ? 'text-warning' : 'text-danger') }}" id="stock-texto">
                                                {{ number_format($producto->iStock) }} unidades
                                            </span>
                                        @endif
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- SELECTOR DE VARIACIONES CON TOGGLE -->
            @if($producto->tieneVariaciones() && $producto->variaciones->count() > 0)
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-cubes me-2 text-primary"></i>Variaciones del Producto
                    </h5>
                </div>
                <div class="card-body px-4">
                    <div class="row g-3">
                        @foreach($producto->variaciones as $variacion)
                            @php
                                $imagenVar = $variacion->imagen_principal; // Primera imagen de la variación
                                $stockClase = $variacion->iStock > 10 ? 'success' : ($variacion->iStock > 0 ? 'warning' : 'danger');
                                $precioActual = $variacion->ofertaVigente() ? $variacion->dPrecio_oferta : $variacion->dPrecio;
                                $atributosTexto = [];
                                foreach($variacion->atributos as $atributoRel) {
                                    if($atributoRel->atributo && $atributoRel->valor) {
                                        $atributosTexto[] = $atributoRel->atributo->vNombre . ': ' . $atributoRel->valor->vValor;
                                    }
                                }
                            @endphp
                            <div class="col-md-6">
                                <div class="variacion-card card border" 
                                     onclick="toggleVariacion({{ $variacion->id_variacion }})"
                                     data-variacion-id="{{ $variacion->id_variacion }}"
                                     data-sku="{{ $variacion->vSKU }}"
                                     data-precio="{{ $variacion->dPrecio }}"
                                     data-precio-oferta="{{ $variacion->dPrecio_oferta ?? 0 }}"
                                     data-tiene-oferta="{{ $variacion->bTiene_oferta ? 'true' : 'false' }}"
                                     data-stock="{{ $variacion->iStock }}"
                                     data-descripcion="{{ $variacion->tDescripcion ?? '' }}"
                                     data-peso="{{ $variacion->dPeso ?? '' }}"
                                     data-largo="{{ $variacion->dLargo_cm ?? '' }}"
                                     data-ancho="{{ $variacion->dAncho_cm ?? '' }}"
                                     data-alto="{{ $variacion->dAlto_cm ?? '' }}"
                                     data-clase-envio="{{ $variacion->vClase_envio ?? '' }}"
                                     data-imagenes='@json($variacion->imagenes ?? [])'
                                     data-atributos='@json($atributosTexto)'
                                     style="cursor: pointer; transition: all 0.2s;">
                                    <div class="row g-0">
                                        <div class="col-4">
                                            @if($imagenVar)
                                                <img src="{{ $imagenVar }}" 
                                                     class="img-fluid rounded-start" 
                                                     style="height: 100px; width: 100%; object-fit: cover;"
                                                     onerror="this.onerror=null; this.src='https://via.placeholder.com/100x100?text=Var';">
                                            @else
                                                <div style="height: 100px; background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-image fa-2x text-muted"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-8">
                                            <div class="card-body p-2">
                                                <h6 class="card-title mb-1">{{ implode(' | ', $atributosTexto) }}</h6>
                                                <p class="card-text mb-1">
                                                    <small class="text-muted">SKU: {{ $variacion->vSKU }}</small>
                                                </p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="fw-bold text-primary">
                                                        ${{ number_format($precioActual, 2) }}
                                                        @if($variacion->ofertaVigente())
                                                            <small class="text-danger ms-1">-{{ $variacion->porcentajeDescuento }}%</small>
                                                        @endif
                                                    </span>
                                                    <span class="badge bg-{{ $stockClase }}">{{ $variacion->iStock }} uds</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- SEGUNDA FILA: Precios e Impuestos (estos se actualizarán dinámicamente) -->
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
                                        @if($producto->bTiene_oferta && $producto->dPrecio_oferta && $producto->dFecha_inicio_oferta && $producto->dFecha_fin_oferta && now()->between($producto->dFecha_inicio_oferta, $producto->dFecha_fin_oferta))
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
                                            $totalImpuestos = 0;
                                            foreach($producto->impuestos as $impuesto) {
                                                $totalImpuestos += $producto->dPrecio_venta * ($impuesto->dPorcentaje / 100);
                                            }
                                        @endphp
                                        +${{ number_format($totalImpuestos, 2) }}
                                    </td>
                                </tr>
                                <tr class="bg-light">
                                    <td class="py-3 px-3 rounded-start">
                                        <strong class="text-primary">TOTAL (con impuestos)</strong>
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="fw-bold text-primary fs-5" id="precio-total">
                                            ${{ number_format($producto->dPrecio_venta + $totalImpuestos, 2) }}
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

    <!-- DESCRIPCIÓN DE VARIACIÓN SELECCIONADA (se actualiza dinámicamente) -->
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

    <!-- DESCRIPCIÓN DEL PRODUCTO (Larga y Corta) -->
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

    <!-- ATRIBUTOS DEL PRODUCTO PADRE (NO VARIACIONES) -->
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
                        $atributosAgrupados = [];
                        foreach($producto->valoresAtributos as $valor) {
                            $atributo = $valor->atributo;
                            if($atributo) {
                                if(!isset($atributosAgrupados[$atributo->id_atributo])) {
                                    $atributosAgrupados[$atributo->id_atributo] = [
                                        'nombre' => $atributo->vNombre,
                                        'valores' => []
                                    ];
                                }
                                $atributosAgrupados[$atributo->id_atributo]['valores'][] = [
                                    'valor' => $valor->vValor,
                                    'precio_extra' => $valor->pivot->dPrecio_extra ?? 0
                                ];
                            }
                        }
                    @endphp
                    
                    <div class="row">
                        @foreach($atributosAgrupados as $atributo)
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
let imagenesActuales = @json($imagenesProducto); // Solo imágenes del producto activo (padre o variación)
let variacionesData = @json($variacionesData);
let productoData = @json($productoData);
let variacionSeleccionadaId = null;

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
    
    if (mainImage && imagenesActuales[currentImageIndex]) {
        mainImage.src = imagenesActuales[currentImageIndex];
        
        // Actualizar contador
        if (imagenActualSpan) {
            imagenActualSpan.textContent = currentImageIndex + 1;
        }
        
        // Actualizar clase activa en miniaturas
        miniaturas.forEach((thumb, index) => {
            if (index === currentImageIndex) {
                thumb.classList.add('activa');
                thumb.style.borderColor = '#007bff';
            } else {
                thumb.classList.remove('activa');
                thumb.style.borderColor = 'transparent';
            }
        });
        
        // Habilitar/deshabilitar controles
        const botones = document.querySelectorAll('#imageControls button');
        if (botones.length === 2) {
            botones[0].disabled = imagenesActuales.length <= 1;
            botones[1].disabled = imagenesActuales.length <= 1;
        }
    }
}

// Función de toggle para variaciones
function toggleVariacion(variacionId) {
    const cardSeleccionada = document.querySelector(`.variacion-card[data-variacion-id="${variacionId}"]`);
    
    // Si ya hay una variación seleccionada y es la misma que se está dando clic
    if (variacionSeleccionadaId === variacionId) {
        // DESELECCIONAR - Volver al producto original
        variacionSeleccionadaId = null;
        
        // Quitar clase seleccionada de todas las cards
        document.querySelectorAll('.variacion-card').forEach(card => {
            card.classList.remove('border-primary', 'bg-light');
        });
        
        // Restaurar datos del producto original
        restaurarProductoOriginal();
        showNotification('Mostrando producto original', 'info');
    } else {
        // SELECCIONAR NUEVA VARIACIÓN
        document.querySelectorAll('.variacion-card').forEach(card => {
            card.classList.remove('border-primary', 'bg-light');
        });
        
        if (cardSeleccionada) {
            cardSeleccionada.classList.add('border-primary', 'bg-light');
        }
        
        variacionSeleccionadaId = variacionId;
        const variacion = variacionesData[variacionId];
        if (!variacion) return;
        
        aplicarDatosVariacion(variacion);
        showNotification('Variación seleccionada: ' + (variacion.atributos_texto?.join(' | ') || ''), 'success');
    }
}

// Función para restaurar datos del producto original
function restaurarProductoOriginal() {
    // 1. RESTAURAR IMÁGENES (solo del producto padre)
    imagenesActuales = productoData.imagenes;
    actualizarMiniaturas();
    currentImageIndex = 0;
    actualizarImagenPrincipal();
    document.getElementById('total-imagenes').textContent = imagenesActuales.length;
    
    // 2. RESTAURAR SKU
    document.getElementById('producto-sku-display').textContent = productoData.sku;
    
    // 3. RESTAURAR PRECIO
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
    
    // 4. RESTAURAR STOCK
    const stockDisplay = document.getElementById('producto-stock-display');
    stockDisplay.innerHTML = '';
    if ({{ $producto->tieneVariaciones() ? 'true' : 'false' }}) {
        stockDisplay.innerHTML = '<span class="badge bg-info">Variable por variaciones</span>';
    } else {
        const stockSpan = document.createElement('span');
        stockSpan.id = 'stock-texto';
        let stockClass = productoData.stock > 10 ? 'text-success' : (productoData.stock > 0 ? 'text-warning' : 'text-danger');
        stockSpan.className = stockClass;
        stockSpan.textContent = productoData.stock + ' unidades';
        stockDisplay.appendChild(stockSpan);
    }
    
    // 5. RESTAURAR DESCRIPCIÓN DE VARIACIÓN (ocultar)
    document.getElementById('variacion-descripcion-container').style.display = 'none';
    
    // 6. RESTAURAR DIMENSIONES
    document.getElementById('producto-peso').textContent = productoData.peso ? parseFloat(productoData.peso).toFixed(3) + ' kg' : '—';
    document.getElementById('producto-largo').textContent = productoData.largo ? parseFloat(productoData.largo).toFixed(2) + ' cm' : '—';
    document.getElementById('producto-ancho').textContent = productoData.ancho ? parseFloat(productoData.ancho).toFixed(2) + ' cm' : '—';
    document.getElementById('producto-alto').textContent = productoData.alto ? parseFloat(productoData.alto).toFixed(2) + ' cm' : '—';
    
    // 7. RESTAURAR CLASE DE ENVÍO
    const claseEnvioSpan = document.getElementById('producto-clase-envio');
    if (claseEnvioSpan) {
        let claseText = '';
        let claseClass = '';
        switch(productoData.clase_envio) {
            case 'estandar': claseText = 'Estándar'; claseClass = 'bg-primary'; break;
            case 'express': claseText = 'Express'; claseClass = 'bg-success'; break;
            case 'fragil': claseText = 'Frágil'; claseClass = 'bg-warning text-dark'; break;
            case 'grandes_dimensiones': claseText = 'Grandes dimensiones'; claseClass = 'bg-danger'; break;
            default: claseText = productoData.clase_envio ? productoData.clase_envio : 'No especificada'; claseClass = 'bg-secondary';
        }
        claseEnvioSpan.className = 'badge ' + claseClass;
        claseEnvioSpan.textContent = claseText;
    }
}

// Función para aplicar datos de una variación
function aplicarDatosVariacion(variacion) {
    // 1. ACTUALIZAR IMÁGENES (con las de la variación, si tiene)
    imagenesActuales = (variacion.imagenes && variacion.imagenes.length > 0) ? variacion.imagenes : productoData.imagenes;
    actualizarMiniaturas();
    currentImageIndex = 0;
    actualizarImagenPrincipal();
    document.getElementById('total-imagenes').textContent = imagenesActuales.length;
    
    // 2. ACTUALIZAR SKU
    document.getElementById('producto-sku-display').textContent = variacion.sku;
    
    // 3. ACTUALIZAR PRECIO
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
    
    // 4. ACTUALIZAR STOCK
    const stockDisplay = document.getElementById('producto-stock-display');
    stockDisplay.innerHTML = '';
    const stockSpan = document.createElement('span');
    stockSpan.id = 'stock-texto';
    let stockClass = variacion.stock > 10 ? 'text-success' : (variacion.stock > 0 ? 'text-warning' : 'text-danger');
    stockSpan.className = stockClass;
    stockSpan.textContent = variacion.stock + ' unidades';
    stockDisplay.appendChild(stockSpan);
    
    // 5. ACTUALIZAR DESCRIPCIÓN DE VARIACIÓN
    const descripcionContainer = document.getElementById('variacion-descripcion-container');
    const descripcionTexto = document.getElementById('variacion-descripcion-texto');
    if (variacion.descripcion && variacion.descripcion.trim() !== '') {
        descripcionTexto.textContent = variacion.descripcion;
        descripcionContainer.style.display = 'block';
    } else {
        descripcionContainer.style.display = 'none';
    }
    
    // 6. ACTUALIZAR DIMENSIONES (priorizar las de la variación, si no, las del padre)
    document.getElementById('producto-peso').textContent = variacion.peso ? parseFloat(variacion.peso).toFixed(3) + ' kg' : (productoData.peso ? parseFloat(productoData.peso).toFixed(3) + ' kg' : '—');
    document.getElementById('producto-largo').textContent = variacion.largo ? parseFloat(variacion.largo).toFixed(2) + ' cm' : (productoData.largo ? parseFloat(productoData.largo).toFixed(2) + ' cm' : '—');
    document.getElementById('producto-ancho').textContent = variacion.ancho ? parseFloat(variacion.ancho).toFixed(2) + ' cm' : (productoData.ancho ? parseFloat(productoData.ancho).toFixed(2) + ' cm' : '—');
    document.getElementById('producto-alto').textContent = variacion.alto ? parseFloat(variacion.alto).toFixed(2) + ' cm' : (productoData.alto ? parseFloat(productoData.alto).toFixed(2) + ' cm' : '—');
    
    // 7. ACTUALIZAR CLASE DE ENVÍO
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

// Función para actualizar miniaturas
function actualizarMiniaturas() {
    const miniaturasContainer = document.getElementById('miniaturas-container');
    if (!miniaturasContainer) return;
    
    miniaturasContainer.innerHTML = '';
    
    imagenesActuales.forEach((imgUrl, index) => {
        const div = document.createElement('div');
        div.className = 'miniatura-item flex-shrink-0';
        div.setAttribute('onclick', `seleccionarImagen(${index})`);
        
        const img = document.createElement('img');
        img.src = imgUrl;
        img.className = `img-thumbnail miniatura ${index === 0 ? 'activa' : ''}`;
        img.style.cssText = 'width: 70px; height: 70px; object-fit: cover; cursor: pointer; border: 2px solid transparent;';
        img.alt = `Miniatura ${index + 1}`;
        img.onerror = function() { this.src = 'https://via.placeholder.com/70x70?text=Error'; };
        if (index === 0) img.style.borderColor = '#007bff';
        div.appendChild(img);
        miniaturasContainer.appendChild(div);
    });
    
    // Actualizar contador
    document.getElementById('total-imagenes').textContent = imagenesActuales.length;
    document.getElementById('imagen-actual').textContent = 1;
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
    if (imagenesActuales.length > 0) {
        currentImageIndex = 0;
        actualizarImagenPrincipal();
    }
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowLeft') cambiarImagen(-1);
        else if (e.key === 'ArrowRight') cambiarImagen(1);
    });
});
</script>

<style>
.variacion-card {
    transition: all 0.3s ease;
    cursor: pointer;
    border: 2px solid transparent;
}
.variacion-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    border-color: #007bff;
}
.variacion-card.border-primary {
    border-color: #007bff !important;
    background-color: #f8f9fa;
}
.miniatura {
    transition: all 0.2s ease;
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