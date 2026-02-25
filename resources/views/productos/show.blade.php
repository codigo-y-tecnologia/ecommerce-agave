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
                                <i class="fas fa-barcode me-1"></i>SKU: <span class="fw-semibold">{{ $producto->vCodigo_barras }}</span>
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

    <!-- PRIMERA FILA: Galería de imágenes estilo Mercado Libre + Información básica -->
    <div class="row g-4 mb-4">
        <!-- Columna de galería de imágenes -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    @php
                        // Usar el nuevo accesor que devuelve todas las imágenes en orden
                        $multimedia = $producto->todas_las_imagenes ?? [];
                        
                        // Separar por tipos para facilitar el manejo
                        $imagenPrincipal = null;
                        $video = null;
                        $gif = null;
                        $adicionales = [];
                        
                        foreach($multimedia as $item) {
                            if($item['tipo'] == 'principal') {
                                $imagenPrincipal = $item['url'];
                            } elseif($item['tipo'] == 'video') {
                                $video = $item['url'];
                            } elseif($item['tipo'] == 'gif') {
                                $gif = $item['url'];
                            } elseif($item['tipo'] == 'adicional') {
                                $adicionales[] = $item['url'];
                            }
                        }
                        
                        // Construir el array en el orden correcto para miniaturas
                        $todasLasMiniaturas = [];
                        
                        // 1. Imagen Principal
                        if($imagenPrincipal) {
                            $todasLasMiniaturas[] = [
                                'url' => $imagenPrincipal,
                                'tipo' => 'principal',
                                'nombre' => 'Principal'
                            ];
                        }
                        
                        // 2. Video (si existe)
                        if($video) {
                            $todasLasMiniaturas[] = [
                                'url' => $video,
                                'tipo' => 'video',
                                'nombre' => 'Video'
                            ];
                        }
                        
                        // 3. GIF (si existe)
                        if($gif) {
                            $todasLasMiniaturas[] = [
                                'url' => $gif,
                                'tipo' => 'gif',
                                'nombre' => 'GIF'
                            ];
                        }
                        
                        // 4. Imágenes adicionales
                        foreach($adicionales as $index => $imgUrl) {
                            $todasLasMiniaturas[] = [
                                'url' => $imgUrl,
                                'tipo' => 'adicional',
                                'nombre' => 'Adicional ' . ($index + 1)
                            ];
                        }
                    @endphp
                    
                    @if(count($todasLasMiniaturas) > 0)
                        <div class="row g-2">
                            <!-- Columna de miniaturas (izquierda) -->
                            <div class="col-md-2">
                                <div class="d-flex flex-column gap-2">
                                    @foreach($todasLasMiniaturas as $index => $item)
                                        @if($item['tipo'] != 'video')
                                            <div class="thumbnail-item {{ $index === 0 ? 'active' : '' }}" 
                                                 onclick="cambiarImagenPrincipal('{{ $item['url'] }}', this)"
                                                 data-tipo="{{ $item['tipo'] }}"
                                                 style="cursor: pointer; border: 2px solid {{ $index === 0 ? '#0d6efd' : '#dee2e6' }}; border-radius: 8px; overflow: hidden; padding: 2px; background: white;">
                                                <img src="{{ $item['url'] }}" 
                                                     class="img-fluid" 
                                                     style="height: 60px; width: 100%; object-fit: contain;"
                                                     alt="Miniatura {{ $item['nombre'] }}">
                                                @if($item['tipo'] == 'gif')
                                                    <span class="badge bg-success position-absolute top-0 start-0" style="font-size: 8px;">GIF</span>
                                                @endif
                                            </div>
                                        @else
                                            <div class="thumbnail-item" 
                                                 onclick="reproducirVideo('{{ $item['url'] }}', this)"
                                                 data-tipo="video"
                                                 style="cursor: pointer; border: 2px solid #dee2e6; border-radius: 8px; overflow: hidden; padding: 2px; background: white; position: relative;">
                                                <div class="position-relative">
                                                    <img src="{{ asset('img/video-thumbnail.jpg') }}" 
                                                         class="img-fluid" 
                                                         style="height: 60px; width: 100%; object-fit: cover;"
                                                         alt="Video">
                                                    <div class="position-absolute top-50 start-50 translate-middle">
                                                        <i class="fas fa-play-circle fa-2x text-white"></i>
                                                    </div>
                                                </div>
                                                <span class="badge bg-danger position-absolute top-0 start-0" style="font-size: 8px;">VIDEO</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            
                            <!-- Columna de contenido principal (derecha) -->
                            <div class="col-md-10">
                                <div class="position-relative">
                                    <!-- Contenedor de imagen principal -->
                                    <div id="imagenPrincipalContainer" class="text-center border rounded-3 p-3 bg-light" style="min-height: 400px;">
                                        @php
                                            $primerElemento = $todasLasMiniaturas[0] ?? null;
                                            $mostrarUrl = $primerElemento ? $primerElemento['url'] : '';
                                        @endphp
                                        
                                        @if($primerElemento && $primerElemento['tipo'] != 'video')
                                            <img id="imagenPrincipal" 
                                                 src="{{ $mostrarUrl }}" 
                                                 class="img-fluid" 
                                                 style="max-height: 380px; width: 100%; object-fit: contain;"
                                                 alt="{{ $producto->vNombre }}">
                                        @elseif($primerElemento && $primerElemento['tipo'] == 'video')
                                            <video id="imagenPrincipalVideo" controls style="max-height: 380px; width: 100%;">
                                                <source src="{{ $mostrarUrl }}" type="video/mp4">
                                                Tu navegador no soporta el elemento de video.
                                            </video>
                                        @else
                                            <div class="d-flex align-items-center justify-content-center" style="height: 380px;">
                                                <i class="fas fa-image fa-4x text-muted"></i>
                                            </div>
                                        @endif
                                        
                                        @if($producto->bActivo)
                                            <span class="position-absolute top-0 start-0 badge bg-success mt-2 ms-2 px-3 py-2">
                                                <i class="fas fa-check-circle me-1"></i>Activo
                                            </span>
                                        @endif
                                        
                                        @if($producto->bTiene_oferta && $producto->dPrecio_oferta)
                                            @php
                                                $ofertaVigente = $producto->dFecha_inicio_oferta && $producto->dFecha_fin_oferta && 
                                                                 now()->between($producto->dFecha_inicio_oferta, $producto->dFecha_fin_oferta);
                                            @endphp
                                            @if($ofertaVigente)
                                                <span class="position-absolute top-0 end-0 badge bg-danger mt-2 me-2 px-3 py-2">
                                                    <i class="fas fa-tag me-1"></i>OFERTA
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                    
                                    <!-- Contenedor de video (oculto inicialmente) -->
                                    <div id="videoContainer" class="text-center border rounded-3 p-3 bg-light" style="min-height: 400px; display: none;">
                                        <video id="videoPlayer" controls style="max-height: 380px; width: 100%;">
                                            <source src="" type="video/mp4">
                                            Tu navegador no soporta el elemento de video.
                                        </video>
                                    </div>
                                </div>
                                
                                @if(count($todasLasMiniaturas) > 1)
                                    <div class="mt-3 text-center">
                                        <span class="badge bg-light text-dark py-2 px-3">
                                            <i class="fas fa-images me-2 text-primary"></i>
                                            @php
                                                $totalImagenes = count(array_filter($todasLasMiniaturas, function($item) { 
                                                    return $item['tipo'] != 'video'; 
                                                }));
                                                $tieneVideo = !empty(array_filter($todasLasMiniaturas, function($item) { 
                                                    return $item['tipo'] == 'video'; 
                                                }));
                                            @endphp
                                            {{ $totalImagenes }} {{ $totalImagenes == 1 ? 'imagen' : 'imágenes' }}
                                            @if($tieneVideo)
                                                <span class="mx-1">|</span>
                                                <i class="fas fa-video me-1 text-danger"></i>1 video
                                            @endif
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="bg-light rounded-3 d-flex align-items-center justify-content-center" style="height: 400px;">
                            <div class="text-center">
                                <i class="fas fa-image fa-4x text-muted mb-3"></i>
                                <p class="text-muted">Sin imágenes disponibles</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Columna de información básica -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
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
                                    <h6 class="fw-bold mb-0">{{ $producto->vCodigo_barras }}</h6>
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
                                    <h6 class="fw-bold mb-0">
                                        @if($producto->tieneVariaciones())
                                            <span class="badge bg-info">Variable por variaciones</span>
                                        @else
                                            <span class="{{ $producto->iStock > 10 ? 'text-success' : ($producto->iStock > 0 ? 'text-warning' : 'text-danger') }}">
                                                {{ number_format($producto->iStock) }} unidades
                                            </span>
                                        @endif
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($producto->tDescripcion_corta)
                    <div class="mt-4 p-3 bg-light rounded-3">
                        <small class="text-muted text-uppercase">Descripción corta</small>
                        <p class="mb-0 mt-1">{{ $producto->tDescripcion_corta }}</p>
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
                                        @php
                                            $ofertaVigente = $producto->bTiene_oferta && $producto->dPrecio_oferta && 
                                                             $producto->dFecha_inicio_oferta && $producto->dFecha_fin_oferta && 
                                                             now()->between($producto->dFecha_inicio_oferta, $producto->dFecha_fin_oferta);
                                        @endphp
                                        @if($ofertaVigente)
                                            <br><small class="text-danger">(Oferta activa)</small>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3">
                                        @if($ofertaVigente)
                                            <span class="text-decoration-line-through text-muted me-2">
                                                ${{ number_format($producto->dPrecio_venta, 2) }}
                                            </span>
                                            <span class="fw-bold text-danger">
                                                ${{ number_format($producto->dPrecio_oferta, 2) }}
                                            </span>
                                        @else
                                            <span class="fw-bold">${{ number_format($producto->dPrecio_venta, 2) }}</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3">
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
                                        <span class="fw-bold text-primary fs-5">
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

    <!-- OFERTA ESPECIAL (si tiene) -->
    @if($producto->bTiene_oferta && $producto->dPrecio_oferta)
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm border-start border-danger border-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="rounded-circle bg-danger bg-opacity-10 p-3 me-3">
                            <i class="fas fa-tag fa-lg text-danger"></i>
                        </div>
                        <h5 class="fw-bold mb-0">Oferta Especial</h5>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded-3 text-center">
                                <small class="text-muted">Precio normal</small>
                                <h5 class="text-decoration-line-through mb-0">${{ number_format($producto->dPrecio_venta, 2) }}</h5>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded-3 text-center">
                                <small class="text-muted">Precio oferta</small>
                                <h5 class="text-danger fw-bold mb-0">${{ number_format($producto->dPrecio_oferta, 2) }}</h5>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded-3 text-center">
                                <small class="text-muted">Descuento</small>
                                @php
                                    $porcentajeDescuento = 0;
                                    if($producto->dPrecio_venta > 0 && $producto->dPrecio_oferta < $producto->dPrecio_venta) {
                                        $porcentajeDescuento = round((($producto->dPrecio_venta - $producto->dPrecio_oferta) / $producto->dPrecio_venta) * 100);
                                    }
                                @endphp
                                <h5 class="text-success fw-bold mb-0">{{ $porcentajeDescuento }}%</h5>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted">Vigencia</small>
                                <h6 class="mb-0">
                                    @if($producto->dFecha_inicio_oferta && $producto->dFecha_fin_oferta)
                                        {{ \Carbon\Carbon::parse($producto->dFecha_inicio_oferta)->format('d/m/Y') }} - 
                                        {{ \Carbon\Carbon::parse($producto->dFecha_fin_oferta)->format('d/m/Y') }}
                                    @else
                                        <span class="text-muted">No especificada</span>
                                    @endif
                                </h6>
                            </div>
                        </div>
                    </div>
                    
                    @if($producto->vMotivo_oferta)
                        <div class="mt-3 p-3 bg-light rounded-3">
                            <small class="text-muted">Motivo</small>
                            <p class="mb-0">{{ $producto->vMotivo_oferta }}</p>
                        </div>
                    @endif
                    
                    @php
                        $ofertaVigente = $producto->bTiene_oferta && $producto->dFecha_inicio_oferta && $producto->dFecha_fin_oferta && 
                                          now()->between($producto->dFecha_inicio_oferta, $producto->dFecha_fin_oferta);
                    @endphp
                    @if($ofertaVigente)
                        <div class="alert alert-success mt-3 mb-0 py-2">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Oferta vigente</strong> - Activa hasta {{ \Carbon\Carbon::parse($producto->dFecha_fin_oferta)->format('d/m/Y') }}
                        </div>
                    @else
                        <div class="alert alert-warning mt-3 mb-0 py-2">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Oferta no vigente</strong> - Ha expirado o aún no comienza
                        </div>
                    @endif
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
                                <strong>{{ $producto->dPeso ? number_format($producto->dPeso, 3) . ' kg' : '—' }}</strong>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="p-3 bg-light rounded-3 text-center">
                                <i class="fas fa-ruler-vertical fa-2x text-primary mb-2"></i>
                                <small class="d-block text-muted">Largo</small>
                                <strong>{{ $producto->dLargo_cm ? number_format($producto->dLargo_cm, 2) . ' cm' : '—' }}</strong>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="p-3 bg-light rounded-3 text-center">
                                <i class="fas fa-ruler-horizontal fa-2x text-primary mb-2"></i>
                                <small class="d-block text-muted">Ancho</small>
                                <strong>{{ $producto->dAncho_cm ? number_format($producto->dAncho_cm, 2) . ' cm' : '—' }}</strong>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="p-3 bg-light rounded-3 text-center">
                                <i class="fas fa-arrows-alt-v fa-2x text-primary mb-2"></i>
                                <small class="d-block text-muted">Alto</small>
                                <strong>{{ $producto->dAlto_cm ? number_format($producto->dAlto_cm, 2) . ' cm' : '—' }}</strong>
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
                                    <span class="badge {{ $claseEnvioClass }}">{{ $claseEnvioText }}</span>
                                </div>
                                @if($producto->dLargo_cm && $producto->dAncho_cm && $producto->dAlto_cm)
                                    <small class="text-muted">Volumen: {{ number_format($producto->dLargo_cm * $producto->dAncho_cm * $producto->dAlto_cm, 2) }} cm³</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CUARTA FILA: Descripción larga -->
    @if($producto->tDescripcion_larga)
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-align-left me-2 text-primary"></i>Descripción Detallada
                    </h5>
                </div>
                <div class="card-body px-4">
                    <div class="p-3 bg-light rounded-3" style="white-space: pre-line;">
                        {{ $producto->tDescripcion_larga }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- QUINTA FILA: Etiquetas -->
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

    <!-- SEXTA FILA: Atributos -->
    @if($producto->valoresAtributos->count() > 0)
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-list-alt me-2 text-primary"></i>Atributos
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

    <!-- SÉPTIMA FILA: Variaciones -->
    @if($producto->variaciones->count() > 0)
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-cubes me-2 text-primary"></i>Variaciones
                        </h5>
                        <span class="badge bg-primary">{{ $producto->variaciones->count() }} variaciones</span>
                    </div>
                </div>
                <div class="card-body px-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="py-3">Atributos</th>
                                    <th class="py-3">SKU</th>
                                    <th class="py-3 text-end">Precio</th>
                                    <th class="py-3 text-center">Stock</th>
                                    <th class="py-3">Dimensiones</th>
                                    <th class="py-3 text-center">Estado</th>
                                    <th class="py-3 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($producto->variaciones as $variacion)
                                    <tr>
                                        <td>
                                            @foreach($variacion->atributos as $atributoRel)
                                                @if($atributoRel->atributo && $atributoRel->valor)
                                                    <span class="badge bg-info bg-opacity-10 text-dark border me-1 mb-1">
                                                        {{ $atributoRel->atributo->vNombre }}: {{ $atributoRel->valor->vValor }}
                                                    </span>
                                                @endif
                                            @endforeach
                                        </td>
                                        <td><code class="bg-light p-2 rounded">{{ $variacion->vSKU }}</code></td>
                                        <td class="text-end">
                                            @php
                                                $ofertaVariacionVigente = $variacion->bTiene_oferta && $variacion->dPrecio_oferta && 
                                                                           $variacion->dFecha_inicio_oferta && $variacion->dFecha_fin_oferta && 
                                                                           now()->between($variacion->dFecha_inicio_oferta, $variacion->dFecha_fin_oferta);
                                            @endphp
                                            @if($ofertaVariacionVigente)
                                                <span class="text-decoration-line-through text-muted small">
                                                    ${{ number_format($variacion->dPrecio, 2) }}
                                                </span><br>
                                                <span class="fw-bold text-danger">
                                                    ${{ number_format($variacion->dPrecio_oferta, 2) }}
                                                </span>
                                            @else
                                                <span class="fw-bold">${{ number_format($variacion->dPrecio, 2) }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $variacion->iStock > 10 ? 'bg-success' : ($variacion->iStock > 0 ? 'bg-warning text-dark' : 'bg-danger') }} py-2 px-3">
                                                {{ $variacion->iStock }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($variacion->dLargo_cm || $variacion->dAncho_cm || $variacion->dAlto_cm || $variacion->dPeso)
                                                <small>
                                                    @if($variacion->dPeso)<span class="d-block">{{ number_format($variacion->dPeso, 3) }} kg</span>@endif
                                                    @if($variacion->dLargo_cm && $variacion->dAncho_cm && $variacion->dAlto_cm)
                                                        <span class="d-block">{{ number_format($variacion->dLargo_cm, 2) }} × {{ number_format($variacion->dAncho_cm, 2) }} × {{ number_format($variacion->dAlto_cm, 2) }} cm</span>
                                                    @endif
                                                </small>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($variacion->bActivo)
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success py-2 px-3">
                                                    <i class="fas fa-check-circle me-1"></i>Activo
                                                </span>
                                            @else
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger py-2 px-3">
                                                    <i class="fas fa-times-circle me-1"></i>Inactivo
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('variaciones.edit', ['producto_id' => $producto->id_producto, 'variacion_id' => $variacion->id_variacion]) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- OCTAVA FILA: Historial -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">Fecha de registro</small>
                            <p class="fw-bold mb-0">
                                <i class="far fa-calendar-alt me-2 text-primary"></i>
                                {{ $producto->tFecha_registro ? \Carbon\Carbon::parse($producto->tFecha_registro)->format('d/m/Y H:i:s') : 'No disponible' }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Última actualización</small>
                            <p class="fw-bold mb-0">
                                <i class="far fa-clock me-2 text-warning"></i>
                                {{ $producto->tFecha_actualizacion ? \Carbon\Carbon::parse($producto->tFecha_actualizacion)->format('d/m/Y H:i:s') : 'No disponible' }}
                            </p>
                        </div>
                    </div>
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

@push('styles')
<style>
.thumbnail-item {
    transition: all 0.2s ease;
    position: relative;
}

.thumbnail-item:hover {
    border-color: #0d6efd !important;
    transform: scale(1.05);
}

.thumbnail-item.active {
    border-color: #0d6efd !important;
    box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.25);
}

#imagenPrincipalContainer, #videoContainer {
    transition: opacity 0.3s ease;
}

.table-borderless td, .table-borderless th {
    border: none;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
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

function cambiarImagenPrincipal(url, element) {
    // Actualizar imagen principal
    const imagenContainer = document.getElementById('imagenPrincipalContainer');
    const videoContainer = document.getElementById('videoContainer');
    
    // Ocultar video
    if (videoContainer) {
        videoContainer.style.display = 'none';
        const videoPlayer = document.getElementById('videoPlayer');
        if (videoPlayer) {
            videoPlayer.pause();
        }
    }
    
    // Mostrar imagen
    imagenContainer.style.display = 'block';
    
    // Actualizar la imagen
    let imgElement = document.getElementById('imagenPrincipal');
    if (!imgElement) {
        // Si no existe, crearlo
        imgElement = document.createElement('img');
        imgElement.id = 'imagenPrincipal';
        imgElement.className = 'img-fluid';
        imgElement.style = 'max-height: 380px; width: 100%; object-fit: contain;';
        imagenContainer.innerHTML = '';
        imagenContainer.appendChild(imgElement);
    }
    
    imgElement.src = url;
    
    // Actualizar clase activa en miniaturas
    document.querySelectorAll('.thumbnail-item').forEach(item => {
        item.classList.remove('active');
        item.style.borderColor = '#dee2e6';
    });
    
    element.classList.add('active');
    element.style.borderColor = '#0d6efd';
}

function reproducirVideo(url, element) {
    // Ocultar imagen principal
    const imagenContainer = document.getElementById('imagenPrincipalContainer');
    const videoContainer = document.getElementById('videoContainer');
    const videoPlayer = document.getElementById('videoPlayer');
    
    if (videoContainer && videoPlayer) {
        imagenContainer.style.display = 'none';
        videoContainer.style.display = 'block';
        
        // Obtener el elemento source
        let source = videoPlayer.querySelector('source');
        if (!source) {
            source = document.createElement('source');
            videoPlayer.appendChild(source);
        }
        
        source.src = url;
        source.type = 'video/mp4';
        videoPlayer.load();
        videoPlayer.play();
        
        // Actualizar clase activa en miniaturas
        document.querySelectorAll('.thumbnail-item').forEach(item => {
            item.classList.remove('active');
            item.style.borderColor = '#dee2e6';
        });
        
        element.classList.add('active');
        element.style.borderColor = '#0d6efd';
    }
}

function ampliarImagen(url) {
    document.getElementById('imagenAmpliada').src = url;
    const modal = new bootstrap.Modal(document.getElementById('imagenModal'));
    modal.show();
}

// Inicializar la galería
document.addEventListener('DOMContentLoaded', function() {
    // Marcar la primera miniatura como activa
    const primeraMiniatura = document.querySelector('.thumbnail-item');
    if (primeraMiniatura) {
        primeraMiniatura.classList.add('active');
        primeraMiniatura.style.borderColor = '#0d6efd';
    }
    
    // Verificar si el primer elemento es un video
    const primerItem = document.querySelector('.thumbnail-item[data-tipo="video"]');
    if (primerItem && document.querySelector('.thumbnail-item.active') === primerItem) {
        // Si el primer elemento es video, iniciar con video
        const videoUrl = primerItem.getAttribute('onclick')?.match(/'([^']+)'/)?.[1];
        if (videoUrl) {
            reproducirVideo(videoUrl, primerItem);
        }
    }
});
</script>
@endpush

@endsection