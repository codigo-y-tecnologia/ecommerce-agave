@extends('layouts.app')

@section('title', 'Variación - ' . $variacion->vSKU)

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header con breadcrumbs y acciones -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-transparent">
                <div class="card-body p-0">
                    <!-- Breadcrumb -->
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-2">
                            <li class="breadcrumb-item">
                                <a href="{{ route('variaciones.index') }}" class="text-decoration-none">
                                    Variaciones
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('variaciones.show', $producto->id_producto) }}" class="text-decoration-none">
                                    {{ $producto->vNombre }}
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $variacion->vSKU }}</li>
                        </ol>
                    </nav>

                    @php
                        // Obtener el nombre de la variación basado en el primer atributo
                        $nombreVariacion = '';
                        foreach($variacion->atributos as $atributoRel) {
                            if($atributoRel->valor) {
                                $nombreVariacion = $atributoRel->valor->vValor;
                                break;
                            }
                        }
                    @endphp

                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <div>
                            <h2 class="fw-bold mb-1">{{ $nombreVariacion ?: 'Variación' }} - {{ $variacion->vSKU }}</h2>
                            <span class="text-muted">
                                <i class="fas fa-barcode me-1"></i>SKU: <span class="fw-semibold" id="variacion-sku">{{ $variacion->vSKU }}</span>
                                <span class="mx-2">|</span>
                                <i class="fas fa-cube me-1"></i>Producto base: <a href="{{ route('productos.show', $producto->id_producto) }}" class="text-decoration-none">{{ $producto->vNombre }}</a>
                                <span class="mx-2">|</span>
                                <i class="fas fa-calendar-alt me-1"></i>Registro: {{ $variacion->tFecha_registro ? \Carbon\Carbon::parse($variacion->tFecha_registro)->format('d/m/Y') : 'N/A' }}
                            </span>
                        </div>
                        <div>
                            <a href="{{ route('variaciones.edit', ['producto_id' => $producto->id_producto, 'variacion_id' => $variacion->id_variacion]) }}" class="btn btn-primary me-2">
                                <i class="fas fa-edit me-1"></i>Editar
                            </a>
                            <a href="{{ route('variaciones.show', $producto->id_producto) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Volver
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        // --- DATOS DE LA VARIACIÓN ---
        $imagenesVariacion = $variacion->imagenes ?? [];
        
        // Calcular si la variación tiene descuento activo
        $variacionTieneDescuento = $variacion->tieneDescuentoActivo();
        $precioBaseVariacion = $variacionTieneDescuento ? $variacion->dPrecio_descuento : $variacion->dPrecio;
        $porcentajeDescuento = $variacion->porcentaje_descuento;
        
        // Calcular impuestos de la variación
        $impuestoVariacion = $variacion->impuesto ?? $producto->impuestos->first();
        $totalImpuestosVariacion = 0;
        $detalleImpuestosVariacion = [];
        $montoImpuesto = 0;
        
        if ($impuestoVariacion) {
            $montoImpuesto = $precioBaseVariacion * ($impuestoVariacion->dPorcentaje / 100);
            $totalImpuestosVariacion = $montoImpuesto;
            $detalleImpuestosVariacion[] = $impuestoVariacion->vNombre . ': $' . number_format($montoImpuesto, 2);
        }

        // Calcular ahorro total si hay descuento
        $ahorroTotal = 0;
        if ($variacionTieneDescuento && $variacion->dPrecio_descuento < $variacion->dPrecio) {
            $precioOriginalConImpuestos = $variacion->dPrecio + ($impuestoVariacion ? $variacion->dPrecio * ($impuestoVariacion->dPorcentaje / 100) : 0);
            $precioActualConImpuestos = $precioBaseVariacion + $totalImpuestosVariacion;
            $ahorroTotal = $precioOriginalConImpuestos - $precioActualConImpuestos;
        }

        // Atributos de la variación
        $atributosTexto = [];
        foreach($variacion->atributos as $atributoRel) {
            if($atributoRel->atributo && $atributoRel->valor) {
                $atributosTexto[] = $atributoRel->atributo->vNombre . ': ' . $atributoRel->valor->vValor;
            }
        }

        // Clase de envío
        $claseEnvio = $variacion->vClase_envio ?: $producto->vClase_envio;
        $claseEnvioText = '';
        $claseEnvioClass = '';
        switch($claseEnvio) {
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
                $claseEnvioText = $claseEnvio ?: 'No especificada';
                $claseEnvioClass = 'bg-secondary';
        }
    @endphp

    <!-- PRIMERA FILA: Imagen principal con ZOOM + Información básica + Atributos -->
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
                                 src="{{ !empty($imagenesVariacion) ? $imagenesVariacion[0] : 'https://via.placeholder.com/400x400?text=Sin+Imagen' }}" 
                                 class="img-fluid zoom-image" 
                                 style="width: 100%; height: 100%; object-fit: contain; transition: transform 0.1s ease;"
                                 alt="{{ $variacion->vSKU }}"
                                 onclick="abrirModalImagen()"
                                 onerror="this.onerror=null; this.src='https://via.placeholder.com/400x400?text=Error';">
                            
                            <!-- Lupa (el área que sigue al mouse) -->
                            <div id="zoom-lens" class="zoom-lens" style="display: none; position: absolute; width: 150px; height: 150px; border: 2px solid #007bff; background-color: rgba(255,255,255,0.3); pointer-events: none; z-index: 10; border-radius: 4px;"></div>
                            
                            @if($variacion->bActivo)
                                <span class="position-absolute top-0 start-0 badge bg-success mt-2 ms-2 px-3 py-2" style="z-index: 15;">
                                    <i class="fas fa-check-circle me-1"></i>Activo
                                </span>
                            @endif

                            <!-- Badge de descuento en la imagen -->
                            @if($variacionTieneDescuento && $variacion->dPrecio_descuento < $variacion->dPrecio)
                                <span class="position-absolute top-0 end-0 badge bg-danger mt-2 me-2 px-3 py-2" style="z-index: 15; font-size: 14px;">
                                    <i class="fas fa-tag me-1"></i>-{{ $porcentajeDescuento }}%
                                </span>
                            @endif

                            <!-- Controles de navegación -->
                            <div id="imageControls" class="position-absolute w-100 d-flex justify-content-between px-2" style="top: 50%; transform: translateY(-50%); z-index: 15;">
                                <button type="button" class="btn btn-sm btn-light rounded-circle shadow-sm" onclick="event.stopPropagation(); cambiarImagen(-1)" style="width: 36px; height: 36px; opacity: 0.8; background-color: rgba(255,255,255,0.9);" {{ count($imagenesVariacion) <= 1 ? 'disabled' : '' }}>
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-light rounded-circle shadow-sm" onclick="event.stopPropagation(); cambiarImagen(1)" style="width: 36px; height: 36px; opacity: 0.8; background-color: rgba(255,255,255,0.9);" {{ count($imagenesVariacion) <= 1 ? 'disabled' : '' }}>
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Miniaturas horizontales con flex-wrap -->
                    <div id="miniaturas-container" class="d-flex flex-wrap justify-content-center gap-2 mt-2" style="padding-bottom: 5px; max-height: 170px; overflow-y: auto; border: 1px solid #eee; border-radius: 8px; padding: 10px;"></div>

                    <!-- Contador de imágenes -->
                    <div id="imageCounter" class="text-center mt-2" {{ count($imagenesVariacion) <= 0 ? 'style=display:none;' : '' }}>
                        <span class="badge bg-light text-dark" id="contador-imagenes">
                            <span id="imagen-actual">1</span> / <span id="total-imagenes">{{ count($imagenesVariacion) }}</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna de información básica y atributos -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-info-circle me-2 text-primary"></i>Información de la Variación
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
                                    <h6 class="fw-bold mb-0" id="variacion-stock-display">
                                        <span class="{{ $variacion->iStock > 10 ? 'text-success' : ($variacion->iStock > 0 ? 'text-warning' : 'text-danger') }}" id="stock-texto">
                                            {{ number_format($variacion->iStock) }} {{ $variacion->iStock == 1 ? 'unidad' : 'unidades' }}
                                        </span>
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Atributos de la variación -->
                    @if(!empty($atributosTexto))
                        <div class="mt-3 pt-3 border-top">
                            <small class="text-muted text-uppercase">Atributos de esta variación</small>
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                @foreach($atributosTexto as $texto)
                                    <span class="badge bg-info p-2">
                                        <i class="fas fa-tag me-1"></i>{{ $texto }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    <!-- Resumen rápido del descuento -->
                    @if($variacionTieneDescuento && $variacion->dPrecio_descuento < $variacion->dPrecio)
                        <div class="mt-3 pt-3 border-top">
                            <div class="alert alert-success mb-0">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="fas fa-tag fa-2x"></i>
                                    </div>
                                    <div>
                                        <strong class="text-success">¡DESCUENTO ACTIVO!</strong>
                                        <div class="d-flex align-items-center gap-2 mt-1">
                                            <span class="text-decoration-line-through text-muted">${{ number_format($variacion->dPrecio, 2) }}</span>
                                            <span class="fw-bold text-danger fs-5">${{ number_format($variacion->dPrecio_descuento, 2) }}</span>
                                            <span class="badge bg-danger">-{{ $porcentajeDescuento }}%</span>
                                        </div>
                                        @if($variacion->vMotivo_descuento)
                                            <small class="d-block mt-1"><i class="fas fa-comment me-1"></i>{{ $variacion->vMotivo_descuento }}</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- SEGUNDA FILA: Precios e Impuestos -->
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
                                        <strong>Precio de venta</strong>
                                        @if($variacionTieneDescuento)
                                            <span class="badge bg-danger ms-2">
                                                <i class="fas fa-tag me-1"></i>{{ $porcentajeDescuento }}% DESCUENTO
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3">
                                        @if($variacionTieneDescuento && $variacion->dPrecio_descuento < $variacion->dPrecio)
                                            <div class="d-flex align-items-center flex-wrap gap-2">
                                                <span class="text-decoration-line-through text-muted">
                                                    ${{ number_format($variacion->dPrecio, 2) }}
                                                </span>
                                                <span class="fw-bold text-danger fs-5">
                                                    ${{ number_format($variacion->dPrecio_descuento, 2) }}
                                                </span>
                                                <span class="badge bg-danger">
                                                    -{{ $porcentajeDescuento }}%
                                                </span>
                                            </div>
                                        @else
                                            <span class="fw-bold fs-5">${{ number_format($variacion->dPrecio, 2) }}</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3">
                                        +${{ number_format($totalImpuestosVariacion, 2) }}
                                        @if(count($detalleImpuestosVariacion) > 0)
                                            <br><small class="text-muted">{{ implode(' | ', $detalleImpuestosVariacion) }}</small>
                                        @endif
                                    </td>
                                </tr>
                                
                                <!-- Información del descuento -->
                                @if($variacionTieneDescuento)
                                    @if($variacion->vMotivo_descuento)
                                    <tr>
                                        <td class="py-3 px-3" colspan="3">
                                            <div class="alert alert-info mb-0 py-2">
                                                <i class="fas fa-comment me-2"></i>
                                                <strong>Motivo del descuento:</strong> {{ $variacion->vMotivo_descuento }}
                                            </div>
                                        </td>
                                    </tr>
                                    @endif
                                    
                                    @if($variacion->dFecha_inicio_descuento && $variacion->dFecha_fin_descuento)
                                    <tr>
                                        <td class="py-3 px-3" colspan="3">
                                            <div class="alert alert-warning mb-0 py-2">
                                                <i class="fas fa-calendar-alt me-2"></i>
                                                <strong>Período de descuento:</strong> 
                                                {{ \Carbon\Carbon::parse($variacion->dFecha_inicio_descuento)->format('d/m/Y') }} - 
                                                {{ \Carbon\Carbon::parse($variacion->dFecha_fin_descuento)->format('d/m/Y') }}
                                            </div>
                                        </td>
                                    </tr>
                                    @endif
                                @endif
                                
                                <tr class="bg-light">
                                    <td class="py-3 px-3 rounded-start">
                                        <strong class="text-primary">TOTAL (con impuestos)</strong>
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="fw-bold text-primary fs-4">
                                            ${{ number_format($precioBaseVariacion + $totalImpuestosVariacion, 2) }}
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
                    @if($impuestoVariacion)
                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3 mb-3">
                            <div>
                                <strong>{{ $impuestoVariacion->vNombre }}</strong>
                                <div><small class="text-muted">{{ $impuestoVariacion->eTipo }}</small></div>
                            </div>
                            <span class="badge bg-primary fs-6">{{ $impuestoVariacion->dPorcentaje }}%</span>
                        </div>
                        
                        <!-- Resumen de ahorro si hay descuento -->
                        @if($variacionTieneDescuento && $variacion->dPrecio_descuento < $variacion->dPrecio && $ahorroTotal > 0)
                            <div class="alert alert-success mt-3 mb-0">
                                <i class="fas fa-piggy-bank me-2"></i>
                                <strong>¡Ahorras ${{ number_format($ahorroTotal, 2) }}!</strong>
                                <small class="d-block mt-2">
                                    Precio original con impuestos: 
                                    ${{ number_format($variacion->dPrecio + ($impuestoVariacion ? $variacion->dPrecio * ($impuestoVariacion->dPorcentaje/100) : 0), 2) }}
                                </small>
                                <small class="d-block mt-1">
                                    Precio actual con impuestos: 
                                    ${{ number_format($precioBaseVariacion + $totalImpuestosVariacion, 2) }}
                                </small>
                            </div>
                        @else
                            <div class="alert alert-info mt-3 mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Precio final con impuestos:</strong>
                                <span class="d-block fs-5 fw-bold mt-1">${{ number_format($precioBaseVariacion + $totalImpuestosVariacion, 2) }}</span>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-file-invoice-dollar fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Sin impuestos asignados</p>
                            @if($variacionTieneDescuento && $variacion->dPrecio_descuento < $variacion->dPrecio)
                                <div class="alert alert-success mt-3 mb-0">
                                    <i class="fas fa-tag me-2"></i>
                                    <strong>¡Ahorras ${{ number_format($variacion->dPrecio - $variacion->dPrecio_descuento, 2) }}!</strong>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- DESCRIPCIÓN DE LA VARIACIÓN -->
    @if($variacion->tDescripcion)
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-align-left me-2 text-primary"></i>Descripción de la Variación
                    </h5>
                </div>
                <div class="card-body px-4">
                    <div class="p-3 bg-light rounded-3" style="white-space: pre-line;">
                        {{ $variacion->tDescripcion }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

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
                                <strong id="producto-peso">{{ $variacion->dPeso ? number_format($variacion->dPeso, 3) . ' kg' : '—' }}</strong>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="p-3 bg-light rounded-3 text-center">
                                <i class="fas fa-ruler-vertical fa-2x text-primary mb-2"></i>
                                <small class="d-block text-muted">Largo</small>
                                <strong id="producto-largo">{{ $variacion->dLargo_cm ? number_format($variacion->dLargo_cm, 2) . ' cm' : '—' }}</strong>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="p-3 bg-light rounded-3 text-center">
                                <i class="fas fa-ruler-horizontal fa-2x text-primary mb-2"></i>
                                <small class="d-block text-muted">Ancho</small>
                                <strong id="producto-ancho">{{ $variacion->dAncho_cm ? number_format($variacion->dAncho_cm, 2) . ' cm' : '—' }}</strong>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="p-3 bg-light rounded-3 text-center">
                                <i class="fas fa-arrows-alt-v fa-2x text-primary mb-2"></i>
                                <small class="d-block text-muted">Alto</small>
                                <strong id="producto-alto">{{ $variacion->dAlto_cm ? number_format($variacion->dAlto_cm, 2) . ' cm' : '—' }}</strong>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="text-muted">Clase de envío</small>
                                    <span class="badge {{ $claseEnvioClass }}" id="producto-clase-envio">{{ $claseEnvioText }}</span>
                                </div>
                                @if($variacion->dLargo_cm && $variacion->dAncho_cm && $variacion->dAlto_cm)
                                    @php
                                        $volumen = $variacion->dLargo_cm * $variacion->dAncho_cm * $variacion->dAlto_cm;
                                        $pesoVolumetrico = $volumen / 5000;
                                        $pesoFacturable = max($variacion->dPeso ?? 0, $pesoVolumetrico);
                                    @endphp
                                    <small class="text-muted d-block">Volumen: {{ number_format($volumen, 2) }} cm³</small>
                                    <small class="text-muted d-block">Peso volumétrico: {{ number_format($pesoVolumetrico, 3) }} kg</small>
                                    @if($variacion->dPeso)
                                        <small class="text-muted d-block">Peso facturable: {{ number_format($pesoFacturable, 3) }} kg</small>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                <a href="{{ route('variaciones.edit', ['producto_id' => $producto->id_producto, 'variacion_id' => $variacion->id_variacion]) }}" class="btn btn-primary px-5 py-3">
                    <i class="fas fa-edit me-2"></i>Editar Variación
                </a>
                <button type="button" class="btn btn-outline-danger px-5 py-3" onclick="confirmDelete({{ $variacion->id_variacion }})">
                    <i class="fas fa-trash me-2"></i>Eliminar
                </button>
                <a href="{{ route('variaciones.show', $producto->id_producto) }}" class="btn btn-outline-secondary px-5 py-3">
                    <i class="fas fa-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Formulario de eliminación oculto -->
    <form id="deleteForm-{{ $variacion->id_variacion }}" action="{{ route('variaciones.destroy', ['producto_id' => $producto->id_producto, 'variacion_id' => $variacion->id_variacion]) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Variables globales
let currentImageIndex = 0;
let imagenesActuales = @json($imagenesVariacion);

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

// Función para eliminar variación
function confirmDelete(id) {
    Swal.fire({
        title: '¿Eliminar variación?',
        text: 'Esta acción no se puede deshacer. Se eliminarán todas las imágenes asociadas.',
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

// Mostrar mensaje SweetAlert2 después de operaciones exitosas
@if(session('success'))
    Swal.fire({
        title: '¡Éxito!',
        text: "{{ session('success') }}",
        icon: 'success',
        timer: 3000,
        showConfirmButton: false
    });
@endif

// Mostrar mensaje SweetAlert2 si hay error
@if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: "{{ session('error') }}"
    });
@endif
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