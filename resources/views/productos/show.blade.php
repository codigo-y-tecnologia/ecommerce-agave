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

    <!-- PRIMERA FILA: Imagen principal + Información básica -->
    <div class="row g-4 mb-4">
        <!-- Columna de imagen -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    @php
                        $imagenes = $producto->imagenes;
                        $imagenPrincipal = !empty($imagenes) ? $imagenes[0] : null;
                    @endphp
                    
                    @if($imagenPrincipal)
                        <div class="position-relative d-inline-block">
                            <img src="{{ $imagenPrincipal }}" 
                                 class="img-fluid rounded-3 border" 
                                 style="max-height: 280px; width: 100%; object-fit: contain;"
                                 alt="{{ $producto->vNombre }}">
                            @if($producto->bActivo)
                                <span class="position-absolute top-0 start-0 badge bg-success mt-2 ms-2 px-3 py-2">
                                    <i class="fas fa-check-circle me-1"></i>Activo
                                </span>
                            @endif
                        </div>
                    @else
                        <div class="bg-light rounded-3 d-flex align-items-center justify-content-center" style="height: 280px;">
                            <i class="fas fa-image fa-4x text-muted"></i>
                        </div>
                    @endif
                    
                    @if(count($imagenes) > 1)
                        <div class="mt-3">
                            <span class="badge bg-light text-dark py-2 px-3">
                                <i class="fas fa-images me-2 text-primary"></i>{{ count($imagenes) }} imágenes en total
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Columna de información básica -->
        <div class="col-lg-8">
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
                                        @if($producto->bTiene_oferta && $producto->dPrecio_oferta)
                                            <br><small class="text-danger">(Oferta activa)</small>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3">
                                        @if($producto->bTiene_oferta && $producto->dPrecio_oferta && $producto->dFecha_inicio_oferta && $producto->dFecha_fin_oferta && now()->between($producto->dFecha_inicio_oferta, $producto->dFecha_fin_oferta))
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

    <!-- CUARTA FILA: Descripción -->
    @if($producto->tDescripcion_corta || $producto->tDescripcion_larga)
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-align-left me-2 text-primary"></i>Descripción
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

    <!-- SEXTA FILA: Galería de imágenes adicionales -->
    @if(count($imagenes) > 1)
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-images me-2 text-primary"></i>Galería de Imágenes
                    </h5>
                </div>
                <div class="card-body px-4">
                    <div class="row g-3">
                        @foreach($imagenes as $index => $imagen)
                            @if($index > 0)
                            <div class="col-lg-2 col-md-3 col-4">
                                <div class="border rounded-3 p-2 text-center bg-light h-100" style="cursor: pointer;" onclick="ampliarImagen('{{ $imagen }}')">
                                    <img src="{{ $imagen }}" 
                                         class="img-fluid rounded" 
                                         style="height: 100px; width: 100%; object-fit: contain;"
                                         alt="Imagen {{ $index + 1 }}">
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- SÉPTIMA FILA: Atributos -->
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

    <!-- OCTAVA FILA: Variaciones -->
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
                                            @if($variacion->bTiene_oferta && $variacion->dPrecio_oferta && $variacion->dFecha_inicio_oferta && $variacion->dFecha_fin_oferta && now()->between($variacion->dFecha_inicio_oferta, $variacion->dFecha_fin_oferta))
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

    <!-- NOVENA FILA: Historial -->
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

function ampliarImagen(url) {
    document.getElementById('imagenAmpliada').src = url;
    const modal = new bootstrap.Modal(document.getElementById('imagenModal'));
    modal.show();
}
</script>
@endpush

@endsection