<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $producto->vNombre }} - Detalles</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { 
            background-color: #f8f9fa; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .card {
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .card-header {
            background-color: #2E8B57;
            color: white;
            font-weight: bold;
            border-bottom: none;
        }
        
        .card-header.bg-info {
            background-color: #17a2b8 !important;
        }
        
        .card-header.bg-warning {
            background-color: #ffc107 !important;
            color: #212529 !important;
        }
        
        .card-header.bg-danger {
            background-color: #dc3545 !important;
        }
        
        .imagen-producto {
            width: 100%;
            max-height: 400px;
            object-fit: contain;
            border-radius: 8px;
            background: white;
            padding: 15px;
        }
        
        .precio-destacado {
            font-size: 28px;
            font-weight: bold;
            color: #2E8B57;
            margin-bottom: 5px;
        }
        
        .precio-oferta {
            font-size: 24px;
            font-weight: bold;
            color: #dc3545;
        }
        
        .precio-original {
            font-size: 18px;
            color: #6c757d;
            text-decoration: line-through;
            margin-right: 10px;
        }
        
        .stock-badge {
            font-size: 14px;
            padding: 6px 12px;
        }
        
        .etiqueta-badge {
            background: #007bff;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 13px;
            margin-right: 8px;
            margin-bottom: 8px;
            display: inline-block;
        }
        
        .badge-oferta {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
        }
        
        .info-box {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid #2E8B57;
        }
        
        .info-box h6 {
            color: #2E8B57;
            margin-bottom: 10px;
        }
        
        .dimension-box {
            background: #e9f7fe;
            border-radius: 8px;
            padding: 15px;
            border-left: 4px solid #17a2b8;
        }
        
        .envio-box {
            background: #d4edda;
            border-radius: 8px;
            padding: 15px;
            border-left: 4px solid #28a745;
        }
        
        .oferta-box {
            background: #fff3cd;
            border-radius: 8px;
            padding: 15px;
            border-left: 4px solid #ffc107;
        }
        
        table.table-borderless td {
            padding: 8px 0;
            vertical-align: top;
        }
        
        table.table-borderless td strong {
            color: #495057;
        }
        
        .dimension-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .dimension-item:last-child {
            border-bottom: none;
        }
        
        .dimension-label {
            font-weight: 600;
            color: #495057;
        }
        
        .dimension-value {
            font-weight: bold;
            color: #17a2b8;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-wine-bottle me-2"></i>{{ $producto->vNombre }}</h1>
            <div>
                <a href="{{ route('productos.edit', $producto) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i> Editar
                </a>
                <a href="{{ route('productos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Columna izquierda: Imágenes -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-images me-2"></i>Imágenes del Producto
                        <span class="badge bg-secondary float-end">{{ count($producto->imagenes) }} imágenes</span>
                    </div>
                    <div class="card-body">
                        @if(count($producto->imagenes) > 0)
                            <div class="text-center">
                                <img src="{{ $producto->imagenes[0] }}" 
                                     alt="{{ $producto->vNombre }}" 
                                     class="imagen-producto mb-3">
                            </div>
                            
                            @if(count($producto->imagenes) > 1)
                                <div class="row mt-3">
                                    @foreach($producto->imagenes as $index => $imagen)
                                        <div class="col-4 col-md-3 mb-2">
                                            <img src="{{ $imagen }}" 
                                                 alt="{{ $producto->vNombre }} - Imagen {{ $index + 1 }}"
                                                 class="img-fluid rounded"
                                                 style="height: 80px; width: 100%; object-fit: cover; cursor: pointer;"
                                                 onclick="ampliarImagen('{{ $imagen }}')">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-image fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No hay imágenes disponibles</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Columna derecha: Información principal -->
            <div class="col-md-6">
                <!-- Información básica -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-info-circle me-2"></i>Información Básica
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td width="40%"><strong>SKU:</strong></td>
                                <td><code class="fs-5">{{ $producto->vCodigo_barras }}</code></td>
                            </tr>
                            <tr>
                                <td><strong>Categoría:</strong></td>
                                <td>
                                    <span class="badge bg-success">
                                        {{ $producto->categoria->vNombre ?? 'Sin categoría' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Marca:</strong></td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ $producto->marca->vNombre ?? 'Sin marca' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Estado:</strong></td>
                                <td>
                                    <span class="badge {{ $producto->bActivo ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $producto->bActivo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Fecha registro:</strong></td>
                                <td>{{ $producto->tFecha_registro ? \Carbon\Carbon::parse($producto->tFecha_registro)->format('d/m/Y H:i') : 'No disponible' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Última actualización:</strong></td>
                                <td>{{ $producto->tFecha_actualizacion ? \Carbon\Carbon::parse($producto->tFecha_actualizacion)->format('d/m/Y H:i') : 'No disponible' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- DIMENSIONES Y PESO - SECCIÓN NUEVA -->
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-ruler-combined me-2"></i>Dimensiones y Peso</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="dimension-box h-100">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-weight-hanging me-2"></i>Dimensiones
                                    </h6>
                                    
                                    <div class="dimension-item">
                                        <span class="dimension-label">Peso:</span>
                                        <span class="dimension-value">
                                            @if($producto->dPeso)
                                                {{ number_format($producto->dPeso, 3) }} kg
                                            @else
                                                <span class="text-muted">No especificado</span>
                                            @endif
                                        </span>
                                    </div>
                                    
                                    <div class="dimension-item">
                                        <span class="dimension-label">Largo:</span>
                                        <span class="dimension-value">
                                            @if($producto->dLargo_cm)
                                                {{ number_format($producto->dLargo_cm, 2) }} cm
                                            @else
                                                <span class="text-muted">No especificado</span>
                                            @endif
                                        </span>
                                    </div>
                                    
                                    <div class="dimension-item">
                                        <span class="dimension-label">Ancho:</span>
                                        <span class="dimension-value">
                                            @if($producto->dAncho_cm)
                                                {{ number_format($producto->dAncho_cm, 2) }} cm
                                            @else
                                                <span class="text-muted">No especificado</span>
                                            @endif
                                        </span>
                                    </div>
                                    
                                    <div class="dimension-item">
                                        <span class="dimension-label">Alto:</span>
                                        <span class="dimension-value">
                                            @if($producto->dAlto_cm)
                                                {{ number_format($producto->dAlto_cm, 2) }} cm
                                            @else
                                                <span class="text-muted">No especificado</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="envio-box h-100">
                                    <h6 class="text-success mb-3">
                                        <i class="fas fa-shipping-fast me-2"></i>Información de Envío
                                    </h6>
                                    
                                    <div class="mb-3">
                                        <strong>Clase de envío:</strong>
                                        <div class="mt-2">
                                            {!! $producto->clase_envio_badge !!}
                                        </div>
                                    </div>
                                    
                                    @if($producto->tieneDimensionesCompletas())
                                        <div class="mt-3">
                                            <strong>Volumen:</strong>
                                            <p class="mb-1">{{ $producto->volumen_formateado }}</p>
                                            <small class="text-muted">Largo × Ancho × Alto</small>
                                        </div>
                                        
                                        <div class="mt-3">
                                            <strong>Peso volumétrico:</strong>
                                            <p class="mb-1">{{ $producto->peso_volumetrico_formateado }}</p>
                                            <small class="text-muted">Volumen / 5000</small>
                                        </div>
                                        
                                        <div class="mt-3">
                                            <strong>Peso facturable:</strong>
                                            <p class="mb-1">{{ $producto->peso_facturable ? number_format($producto->peso_facturable, 3) . ' kg' : 'No calculable' }}</p>
                                            <small class="text-muted">Mayor entre peso real y volumétrico</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Precio y stock -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <i class="fas fa-money-bill-wave me-2"></i>Precio y Stock
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="info-box">
                            <h6><i class="fas fa-tag me-2"></i>Precio de venta</h6>
                            <div class="precio-destacado">
                                @if($producto->tieneVariaciones())
                                    ${{ $producto->rangoPrecios }}
                                    <small class="d-block text-muted fs-6">(Rango de precios)</small>
                                @else
                                    @if($producto->ofertaVigente())
                                        <div class="precio-original">
                                            ${{ number_format($producto->dPrecio_venta, 2) }}
                                        </div>
                                        <div class="precio-oferta">
                                            ${{ number_format($producto->dPrecio_oferta, 2) }}
                                        </div>
                                        <div class="mt-2">
                                            <span class="badge-oferta">
                                                <i class="fas fa-tag me-1"></i>Ahorra {{ $producto->porcentajeDescuento }}%
                                            </span>
                                        </div>
                                    @else
                                        ${{ number_format($producto->dPrecio_venta, 2) }}
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="info-box">
                            <h6><i class="fas fa-shopping-cart me-2"></i>Precio de compra</h6>
                            <div class="fs-4">
                                @if($producto->dPrecio_compra)
                                    ${{ number_format($producto->dPrecio_compra, 2) }}
                                @else
                                    <span class="text-muted">No especificado</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="info-box">
                            <h6><i class="fas fa-boxes me-2"></i>Stock disponible</h6>
                            <div class="mt-2">
                                <span class="badge stock-badge fs-5 {{ $producto->iStock > 10 ? 'bg-success' : ($producto->iStock > 0 ? 'bg-warning' : 'bg-danger') }}">
                                    {{ $producto->iStock }} unidades
                                </span>
                            </div>
                            
                            @if($producto->estaBajoEnStock())
                                <div class="alert alert-warning mt-3 mb-0 py-2">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    <strong>Stock bajo:</strong> Se recomienda reabastecer
                                </div>
                            @endif
                            
                            @if($producto->tieneVariaciones())
                                <div class="alert alert-info mt-2 mb-0 py-2">
                                    <i class="fas fa-info-circle me-1"></i>
                                    <small>Este producto tiene variaciones con stock distribuido</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- OFERTA ESPECIAL (si tiene) -->
        @if($producto->bTiene_oferta)
        <div class="card mb-4">
            <div class="card-header bg-danger text-white">
                <i class="fas fa-percentage me-2"></i>Oferta Especial
                @if($producto->ofertaVigente())
                    <span class="badge bg-success float-end"><i class="fas fa-clock me-1"></i>Vigente</span>
                @else
                    <span class="badge bg-secondary float-end"><i class="fas fa-clock me-1"></i>No vigente</span>
                @endif
            </div>
            <div class="card-body">
                <div class="oferta-box">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-danger mb-3">
                                <i class="fas fa-calendar-alt me-2"></i>Periodo de Oferta
                            </h6>
                            
                            <div class="dimension-item">
                                <span class="dimension-label">Inicio:</span>
                                <span class="dimension-value">
                                    @if($producto->dFecha_inicio_oferta)
                                        {{ \Carbon\Carbon::parse($producto->dFecha_inicio_oferta)->format('d/m/Y') }}
                                    @else
                                        <span class="text-muted">No especificado</span>
                                    @endif
                                </span>
                            </div>
                            
                            <div class="dimension-item">
                                <span class="dimension-label">Fin:</span>
                                <span class="dimension-value">
                                    @if($producto->dFecha_fin_oferta)
                                        {{ \Carbon\Carbon::parse($producto->dFecha_fin_oferta)->format('d/m/Y') }}
                                    @else
                                        <span class="text-muted">No especificado</span>
                                    @endif
                                </span>
                            </div>
                            
                            <div class="dimension-item">
                                <span class="dimension-label">Estado:</span>
                                <span class="dimension-value">
                                    @if($producto->ofertaVigente())
                                        <span class="badge bg-success">Vigente</span>
                                    @else
                                        <span class="badge bg-secondary">No vigente</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h6 class="text-danger mb-3">
                                <i class="fas fa-info-circle me-2"></i>Detalles de Oferta
                            </h6>
                            
                            <div class="dimension-item">
                                <span class="dimension-label">Precio original:</span>
                                <span class="dimension-value">
                                    ${{ number_format($producto->dPrecio_venta, 2) }}
                                </span>
                            </div>
                            
                            <div class="dimension-item">
                                <span class="dimension-label">Precio oferta:</span>
                                <span class="dimension-value">
                                    ${{ number_format($producto->dPrecio_oferta, 2) }}
                                </span>
                            </div>
                            
                            <div class="dimension-item">
                                <span class="dimension-label">Descuento:</span>
                                <span class="dimension-value">
                                    {{ $producto->porcentajeDescuento }}%
                                </span>
                            </div>
                            
                            @if($producto->vMotivo_oferta)
                                <div class="mt-3">
                                    <strong><i class="fas fa-comment me-2"></i>Motivo:</strong>
                                    <p class="mb-0 mt-1">{{ $producto->vMotivo_oferta }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    @if($producto->ofertaVigente())
                        <div class="alert alert-success mt-3 mb-0">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>¡Oferta activa!</strong> Esta oferta está vigente hasta el {{ $producto->dFecha_fin_oferta ? \Carbon\Carbon::parse($producto->dFecha_fin_oferta)->format('d/m/Y') : 'fecha no especificada' }}.
                        </div>
                    @else
                        <div class="alert alert-warning mt-3 mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Oferta no vigente.</strong> Esta oferta ha expirado o no ha comenzado.
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Descripciones -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-file-alt me-2"></i>Descripción
                    </div>
                    <div class="card-body">
                        @if($producto->tDescripcion_corta)
                            <div class="mb-4">
                                <h5 class="text-primary">
                                    <i class="fas fa-align-left me-2"></i>Descripción corta
                                </h5>
                                <p class="fs-5">{{ $producto->tDescripcion_corta }}</p>
                            </div>
                            <hr>
                        @endif
                        
                        @if($producto->tDescripcion_larga)
                            <div>
                                <h5 class="text-primary">
                                    <i class="fas fa-align-justify me-2"></i>Descripción detallada
                                </h5>
                                <div class="fs-5" style="white-space: pre-line;">{{ $producto->tDescripcion_larga }}</div>
                            </div>
                        @endif
                        
                        @if(!$producto->tDescripcion_corta && !$producto->tDescripcion_larga)
                            <div class="text-center py-5">
                                <i class="fas fa-file-alt fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">No hay descripción disponible</h5>
                                <p class="text-muted">Agrega una descripción para mejorar la presentación del producto</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Etiquetas -->
        @if($producto->etiquetas->count() > 0)
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-tags me-2"></i>Etiquetas
                            <span class="badge bg-secondary float-end">{{ $producto->etiquetas->count() }}</span>
                        </div>
                        <div class="card-body">
                            @foreach($producto->etiquetas as $etiqueta)
                                <span class="etiqueta-badge">
                                    <i class="fas fa-tag me-1"></i>{{ $etiqueta->vNombre }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Atributos (si tiene) -->
        @if($producto->tieneAtributos())
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-tags me-2"></i>Atributos del Producto
                            <span class="badge bg-secondary float-end">{{ $producto->valoresAtributos->count() }} valores</span>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($producto->atributosAgrupados as $atributo)
                                    <div class="col-md-6 mb-3">
                                        <div class="border rounded p-3 h-100">
                                            <h6 class="fw-bold mb-2 text-primary">
                                                <i class="fas fa-list me-1"></i>{{ $atributo['nombre'] }}
                                                <small class="text-muted">({{ count($atributo['valores']) }})</small>
                                            </h6>
                                            <div>
                                                @foreach($atributo['valores'] as $valor)
                                                    <span class="badge bg-secondary me-1 mb-1">
                                                        {{ $valor['valor'] }}
                                                        @if($valor['precio_extra'] > 0)
                                                            <small class="ms-1">(+${{ number_format($valor['precio_extra'], 2) }})</small>
                                                        @endif
                                                        @if($valor['stock'] > 0)
                                                            <small class="ms-1">(Stock: {{ $valor['stock'] }})</small>
                                                        @endif
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="text-center mt-3">
                                <a href="{{ route('productos.asignar-atributos', $producto->id_producto) }}" 
                                   class="btn btn-outline-primary btn-lg">
                                    <i class="fas fa-edit me-1"></i> Gestionar Atributos
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- VARIACIONES (si tiene) -->
        @if($producto->tieneVariaciones())
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-layer-group me-2"></i>Variaciones del Producto
                            <span class="badge bg-secondary float-end">{{ $producto->variacionesActivas()->count() }} variaciones</span>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Este producto tiene {{ $producto->variacionesActivas()->count() }} variaciones activas con diferentes combinaciones de atributos.
                            </div>
                            
                            <div class="text-center mt-3">
                                <a href="{{ route('productos.valoraciones') }}" 
                                   class="btn btn-outline-info btn-lg">
                                    <i class="fas fa-cog me-1"></i> Gestionar Variaciones
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- FAVORITOS (si está logueado) -->
        @if(auth()->check())
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-heart me-2"></i>Estado de Favorito
                        </div>
                        <div class="card-body text-center">
                            @if($producto->esFavorito())
                                <div class="alert alert-success">
                                    <i class="fas fa-heart text-danger me-2"></i>
                                    <strong>Este producto está en tus favoritos</strong>
                                    <p class="mb-0 mt-2">Recibirás notificaciones cuando haya cambios en stock o precio</p>
                                </div>
                            @else
                                <div class="alert alert-secondary">
                                    <i class="far fa-heart me-2"></i>
                                    <strong>No está en tus favoritos</strong>
                                    <p class="mb-0 mt-2">Agrega este producto a favoritos para recibir notificaciones</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- RESUMEN ESTADÍSTICAS -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-chart-bar me-2"></i>Resumen del Producto
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 col-6 text-center mb-3">
                                <div class="p-3 border rounded">
                                    <i class="fas fa-images fa-2x text-primary mb-2"></i>
                                    <h5 class="mb-1">{{ count($producto->imagenes) }}</h5>
                                    <small class="text-muted">Imágenes</small>
                                </div>
                            </div>
                            
                            <div class="col-md-3 col-6 text-center mb-3">
                                <div class="p-3 border rounded">
                                    <i class="fas fa-tags fa-2x text-success mb-2"></i>
                                    <h5 class="mb-1">{{ $producto->etiquetas->count() }}</h5>
                                    <small class="text-muted">Etiquetas</small>
                                </div>
                            </div>
                            
                            <div class="col-md-3 col-6 text-center mb-3">
                                <div class="p-3 border rounded">
                                    <i class="fas fa-layer-group fa-2x text-info mb-2"></i>
                                    <h5 class="mb-1">{{ $producto->tieneAtributos() ? $producto->valoresAtributos->count() : 0 }}</h5>
                                    <small class="text-muted">Atributos</small>
                                </div>
                            </div>
                            
                            <div class="col-md-3 col-6 text-center mb-3">
                                <div class="p-3 border rounded">
                                    <i class="fas fa-heart fa-2x text-danger mb-2"></i>
                                    <h5 class="mb-1">{{ $producto->favoritos->count() }}</h5>
                                    <small class="text-muted">Favoritos</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Acciones -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <a href="{{ route('productos.edit', $producto) }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-edit me-1"></i> Editar Producto
                        </a>
                        
                        @if(!$producto->tieneAtributos())
                            <a href="{{ route('productos.asignar-atributos', $producto->id_producto) }}" 
                               class="btn btn-outline-primary btn-lg ms-2">
                                <i class="fas fa-tags me-1"></i> Agregar Atributos
                            </a>
                        @endif
                        
                        @if($producto->tieneVariaciones())
                            <a href="{{ route('productos.valoraciones') }}" 
                               class="btn btn-outline-info btn-lg ms-2">
                                <i class="fas fa-cog me-1"></i> Gestionar Variaciones
                            </a>
                        @endif
                    </div>
                    
                    <div>
                        <form action="{{ route('productos.destroy', $producto) }}" method="POST" 
                              class="d-inline" onsubmit="return confirmarEliminacion()">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-lg">
                                <i class="fas fa-trash me-1"></i> Eliminar Producto
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="mt-3 text-center">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        ID del Producto: <code>{{ $producto->id_producto }}</code> | 
                        Última actualización: {{ $producto->tFecha_actualizacion ? \Carbon\Carbon::parse($producto->tFecha_actualizacion)->format('d/m/Y H:i') : 'N/A' }}
                    </small>
                </div>
            </div>
        </div>
    </div>

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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function confirmarEliminacion() {
            return confirm('¿Estás seguro de que deseas eliminar este producto? Esta acción no se puede deshacer y eliminará todas las imágenes, atributos y variaciones asociadas.');
        }
        
        function ampliarImagen(url) {
            document.getElementById('imagenAmpliada').src = url;
            const modal = new bootstrap.Modal(document.getElementById('imagenModal'));
            modal.show();
        }
    </script>
</body>
</html>