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
        
        .producto-header { 
            background: linear-gradient(135deg, #2E8B57 0%, #26734A 100%); 
            color: white; 
            padding: 40px 0;
            margin-bottom: 30px;
        }
        
        .producto-header h1 {
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .valoracion-card { 
            transition: all 0.3s ease;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .valoracion-card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border-color: #2E8B57;
        }
        
        .valoracion-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
        }
        
        .atributo-badge {
            background: #e9ecef;
            color: #495057;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 13px;
            margin-right: 5px;
            margin-bottom: 5px;
            display: inline-block;
            border: 1px solid #dee2e6;
        }
        
        .imagen-producto {
            width: 100%;
            height: 300px;
            object-fit: contain;
            background: white;
            border-radius: 10px;
            padding: 20px;
            border: 1px solid #dee2e6;
        }
        
        .precio-destacado {
            font-size: 32px;
            font-weight: 800;
            color: #2E8B57;
            margin-bottom: 5px;
        }
        
        .precio-oferta {
            font-size: 20px;
            color: #dc3545;
            text-decoration: line-through;
            margin-right: 10px;
        }
        
        .info-box {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
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
        
        .stock-badge {
            font-size: 14px;
            padding: 6px 12px;
        }
        
        .stock-alto {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .stock-medio {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .stock-bajo {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .acciones-rapidas {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
            border: 1px solid #2E8B57;
        }
        
        .section-title {
            color: #2E8B57;
            border-bottom: 2px solid #2E8B57;
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .tabla-valoraciones th {
            background: #2E8B57;
            color: white;
        }
        
        .combinacion-atributos {
            font-size: 13px;
            color: #6c757d;
        }
        
        .badge-estado {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }
        
        .btn-valoracion {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-valoracion:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        
        .action-buttons .btn {
            flex: 1;
            min-width: 200px;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Header del producto -->
    <div class="producto-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1><i class="fas fa-wine-bottle me-2"></i>{{ $producto->vNombre }}</h1>
                    <p class="lead mb-0">
                        <i class="fas fa-barcode me-2"></i>Código: <strong>{{ $producto->vCodigo_barras }}</strong>
                        <span class="mx-3">|</span>
                        <i class="fas fa-tag me-2"></i>Categoría: <strong>{{ $producto->categoria->vNombre ?? 'Sin categoría' }}</strong>
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="btn-group">
                        <a href="{{ route('productos.edit', $producto) }}" class="btn btn-light btn-lg">
                            <i class="fas fa-edit me-1"></i> Editar
                        </a>
                        <a href="{{ route('productos.index') }}" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-arrow-left me-1"></i> Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <!-- Columna izquierda: Información principal -->
            <div class="col-lg-8">
                <!-- Imágenes del producto -->
                <div class="info-box">
                    <h4 class="section-title">
                        <i class="fas fa-images me-2"></i>Imágenes del Producto
                    </h4>
                    
                    @if(count($producto->imagenes) > 0)
                        <div class="row">
                            <div class="col-md-6">
                                <img src="{{ $producto->imagenes[0] }}" 
                                     alt="{{ $producto->vNombre }}" 
                                     class="imagen-producto mb-3">
                            </div>
                            @if(count($producto->imagenes) > 1)
                                <div class="col-md-6">
                                    <div class="row">
                                        @foreach($producto->imagenes as $index => $imagen)
                                            @if($index > 0 && $index < 4)
                                                <div class="col-6 mb-3">
                                                    <img src="{{ $imagen }}" 
                                                         alt="{{ $producto->vNombre }} - Imagen {{ $index + 1 }}"
                                                         class="img-fluid rounded"
                                                         style="height: 140px; width: 100%; object-fit: cover;">
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                        <p class="text-muted text-center mt-2">
                            <i class="fas fa-info-circle me-1"></i>
                            {{ count($producto->imagenes) }} imagen(es) disponibles
                        </p>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-image fa-4x text-muted mb-3"></i>
                            <p class="text-muted">No hay imágenes disponibles para este producto</p>
                        </div>
                    @endif
                </div>

                <!-- Precio y stock -->
                <div class="info-box">
                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="section-title">
                                <i class="fas fa-money-bill-wave me-2"></i>Precio
                            </h4>
                            <div class="precio-destacado">
                                @if($producto->tieneVariaciones())
                                    Desde ${{ number_format($producto->precioMinimo, 2) }}
                                @else
                                    ${{ number_format($producto->dPrecio_venta, 2) }}
                                @endif
                            </div>
                            
                            @if($producto->tieneDescuento())
                                <div class="mt-2">
                                    <span class="precio-oferta">${{ number_format($producto->dPrecio_compra, 2) }}</span>
                                    <span class="badge bg-danger">-{{ $producto->porcentajeDescuento() }}%</span>
                                </div>
                            @endif
                            
                            @if($producto->tieneVariaciones())
                                <p class="text-muted mt-2">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Este producto tiene {{ $producto->variaciones->count() }} variaciones con precios diferentes
                                </p>
                            @endif
                        </div>
                        
                        <div class="col-md-6">
                            <h4 class="section-title">
                                <i class="fas fa-boxes me-2"></i>Stock
                            </h4>
                            <div>
                                @if($producto->tieneVariaciones())
                                    <span class="badge stock-badge {{ $producto->iStock > 50 ? 'stock-alto' : ($producto->iStock > 10 ? 'stock-medio' : 'stock-bajo') }}">
                                        {{ $producto->iStock }} unidades totales
                                    </span>
                                    <p class="text-muted mt-2 small">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Stock distribuido en {{ $producto->variaciones->count() }} variaciones
                                    </p>
                                @else
                                    <span class="badge stock-badge {{ $producto->iStock > 50 ? 'stock-alto' : ($producto->iStock > 10 ? 'stock-medio' : 'stock-bajo') }}">
                                        {{ $producto->iStock }} unidades disponibles
                                    </span>
                                    @if($producto->estaBajoEnStock())
                                        <p class="text-warning mt-2">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            ¡Stock bajo! Considera reponer
                                        </p>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Descripciones -->
                <div class="info-box">
                    <h4 class="section-title">
                        <i class="fas fa-file-alt me-2"></i>Descripción
                    </h4>
                    
                    @if($producto->tDescripcion_corta)
                        <div class="mb-4">
                            <h5>Descripción corta</h5>
                            <p class="text-muted">{{ $producto->tDescripcion_corta }}</p>
                        </div>
                    @endif
                    
                    @if($producto->tDescripcion_larga)
                        <div>
                            <h5>Descripción detallada</h5>
                            <p class="text-muted" style="white-space: pre-line;">{{ $producto->tDescripcion_larga }}</p>
                        </div>
                    @endif
                    
                    @if(!$producto->tDescripcion_corta && !$producto->tDescripcion_larga)
                        <div class="text-center py-3">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No hay descripción disponible</p>
                        </div>
                    @endif
                </div>

                <!-- ATRIBUTOS DEL PRODUCTO - NUEVA SECCIÓN -->
                @if($producto->tieneAtributos())
                    <div class="info-box">
                        <h4 class="section-title">
                            <i class="fas fa-tags me-2"></i>Atributos del Producto
                        </h4>
                        
                        <div class="row">
                            @foreach($producto->atributosAgrupados as $atributo)
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">{{ $atributo['nombre'] }}</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach($atributo['valores'] as $valor)
                                                <span class="atributo-badge">
                                                    {{ $valor['valor'] }}
                                                    @if($valor['precio_extra'] > 0)
                                                        <small class="text-success ms-1">
                                                            (+${{ number_format($valor['precio_extra'], 2) }})
                                                        </small>
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
                               class="btn btn-warning">
                                <i class="fas fa-edit me-1"></i> Gestionar Atributos
                            </a>
                        </div>
                    </div>
                @endif

                <!-- VALORACIONES (VARIACIONES) - NUEVA SECCIÓN -->
                <div class="info-box">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="section-title mb-0">
                            <i class="fas fa-cubes me-2"></i>Valoraciones
                        </h4>
                        <span class="badge bg-info">
                            {{ $producto->variaciones->count() }}
                        </span>
                    </div>
                    
                    @if($producto->variaciones->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>SKU</th>
                                        <th>Combinación</th>
                                        <th>Precio</th>
                                        <th>Stock</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($producto->variaciones as $variacion)
                                        <tr>
                                            <td><code>{{ $variacion->vSKU }}</code></td>
                                            <td>
                                                <small>{{ $variacion->nombre_combinacion }}</small>
                                            </td>
                                            <td>
                                                <strong>${{ number_format($variacion->dPrecio, 2) }}</strong>
                                                @if($variacion->tieneOferta())
                                                    <br>
                                                    <small class="text-success">
                                                        Oferta: ${{ number_format($variacion->dPrecio_oferta, 2) }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge {{ $variacion->iStock > 10 ? 'bg-success' : ($variacion->iStock > 0 ? 'bg-warning' : 'bg-danger') }}">
                                                    {{ $variacion->iStock }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $variacion->bActivo ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $variacion->bActivo ? 'Activo' : 'Inactivo' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('valoraciones.edit', ['producto_id' => $producto->id_producto, 'variacion_id' => $variacion->id_variacion]) }}" 
                                                       class="btn btn-warning" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('valoraciones.destroy', ['producto_id' => $producto->id_producto, 'variacion_id' => $variacion->id_variacion]) }}" 
                                                          method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta valoración?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-danger" title="Eliminar">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-cubes fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Sin valoraciones</h5>
                            <p class="text-muted small">Este producto no tiene variaciones registradas</p>
                            <a href="{{ route('valoraciones.create', $producto->id_producto) }}" 
                               class="btn btn-success">
                                <i class="fas fa-plus me-1"></i> Crear Valoración
                            </a>
                        </div>
                    @endif
                </div>

                <!-- ACCIONES RÁPIDAS - NUEVA SECCIÓN -->
                <div class="action-buttons">
                    <a href="{{ route('valoraciones.create', $producto->id_producto) }}" 
                       class="btn btn-success">
                        <i class="fas fa-plus me-1"></i> Crear Valoración
                    </a>
                    <a href="{{ route('productos.asignar-atributos', $producto->id_producto) }}" 
                       class="btn btn-warning">
                        <i class="fas fa-tags me-1"></i> Asignar Atributos
                    </a>
                    <a href="{{ route('productos.atributos', $producto->id_producto) }}" 
                       class="btn btn-info">
                        <i class="fas fa-cogs me-1"></i> Generar Combinaciones
                    </a>
                </div>
            </div>

            <!-- Columna derecha: Información secundaria y valoraciones -->
            <div class="col-lg-4">
                <!-- Información de categorización -->
                <div class="info-box">
                    <h4 class="section-title">
                        <i class="fas fa-info-circle me-2"></i>Información General
                    </h4>
                    
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%"><strong>Código:</strong></td>
                            <td><code>{{ $producto->vCodigo_barras }}</code></td>
                        </tr>
                        <tr>
                            <td><strong>Categoría:</strong></td>
                            <td>{{ $producto->categoria->vNombre ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Marca:</strong></td>
                            <td>{{ $producto->marca->vNombre ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Estado:</strong></td>
                            <td>
                                <span class="badge-estado {{ $producto->bActivo ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $producto->bActivo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Creado:</strong></td>
                            <td>{{ $producto->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Actualizado:</strong></td>
                            <td>{{ $producto->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>

                <!-- Etiquetas -->
                @if($producto->etiquetas->count() > 0)
                    <div class="info-box">
                        <h4 class="section-title">
                            <i class="fas fa-tags me-2"></i>Etiquetas
                        </h4>
                        
                        <div>
                            @foreach($producto->etiquetas as $etiqueta)
                                <span class="etiqueta-badge">
                                    <i class="fas fa-tag me-1"></i>{{ $etiqueta->vNombre }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Resumen de valoraciones -->
                <div class="info-box">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="section-title mb-0">
                            <i class="fas fa-chart-pie me-2"></i>Resumen de Valoraciones
                        </h4>
                        <span class="badge bg-primary">{{ $producto->variaciones->count() }}</span>
                    </h4>
                    </div>
                    
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="display-5 text-primary">
                                @if($producto->tieneVariaciones())
                                    ${{ number_format($producto->precioMinimo, 2) }}
                                @else
                                    ${{ number_format($producto->dPrecio_venta, 2) }}
                                @endif
                            </div>
                            <small class="text-muted">Precio mínimo</small>
                        </div>
                        
                        <div class="col-6 mb-3">
                            <div class="display-5 text-success">
                                @if($producto->tieneVariaciones())
                                    ${{ number_format($producto->precioMaximo, 2) }}
                                @else
                                    ${{ number_format($producto->dPrecio_venta, 2) }}
                                @endif
                            </div>
                            <small class="text-muted">Precio máximo</small>
                        </div>
                        
                        <div class="col-6">
                            <div class="display-5 text-info">
                                {{ $producto->variaciones->where('bActivo', true)->count() }}
                            </div>
                            <small class="text-muted">Activas</small>
                        </div>
                        
                        <div class="col-6">
                            <div class="display-5 text-warning">
                                {{ $producto->variaciones->sum('iStock') }}
                            </div>
                            <small class="text-muted">Stock total</small>
                        </div>
                    </div>
                    
                    @if($producto->tieneVariaciones())
                        <div class="mt-3">
                            <div class="alert alert-info">
                                <i class="fas fa-lightbulb me-2"></i>
                                Este producto tiene <strong>{{ $producto->variaciones->count() }} valoraciones</strong> 
                                con diferentes combinaciones de atributos.
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Acciones rápidas -->
                <div class="acciones-rapidas">
                    <h5 class="mb-3 text-center">
                        <i class="fas fa-bolt me-2"></i>Acciones Rápidas
                    </h5>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('productos.edit', $producto) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i> Editar Producto
                        </a>
                        
                        <a href="{{ route('valoraciones.create', $producto->id_producto) }}" class="btn btn-valoracion">
                            <i class="fas fa-cube me-2"></i> Crear Valoración
                        </a>
                        
                        <a href="{{ route('productos.asignar-atributos', $producto->id_producto) }}" class="btn btn-warning">
                            <i class="fas fa-tags me-2"></i> Gestionar Atributos
                        </a>
                        
                        <a href="{{ route('productos.atributos', $producto->id_producto) }}" class="btn btn-info">
                            <i class="fas fa-cogs me-2"></i> Combinaciones
                        </a>
                        
                        <a href="{{ route('valoraciones.index') }}" class="btn btn-secondary">
                            <i class="fas fa-list me-2"></i> Ver Todas Valoraciones
                        </a>
                    </div>
                </div>

                <!-- Estadísticas -->
                <div class="info-box mt-4">
                    <h4 class="section-title">
                        <i class="fas fa-chart-bar me-2"></i>Estadísticas
                    </h4>
                    
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="display-6 text-primary">{{ $producto->variaciones->count() }}</div>
                            <small class="text-muted">Valoraciones</small>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="display-6 text-success">{{ $producto->etiquetas->count() }}</div>
                            <small class="text-muted">Etiquetas</small>
                        </div>
                        <div class="col-6">
                            <div class="display-6 text-info">
                                @if($producto->tieneAtributos())
                                    {{ count($producto->atributosAgrupados ?? []) }}
                                @else
                                    0
                                @endif
                            </div>
                            <small class="text-muted">Atributos</small>
                        </div>
                        <div class="col-6">
                            <div class="display-6 text-warning">{{ count($producto->imagenes) }}</div>
                            <small class="text-muted">Imágenes</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumen de valoraciones -->
        @if($producto->variaciones->count() > 0)
            <div class="row mt-4">
                <div class="col-12">
                    <div class="info-box">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="section-title mb-0">
                                <i class="fas fa-chart-pie me-2"></i>Resumen de Valoraciones
                            </h4>
                            <a href="{{ route('valoraciones.show', $producto->id_producto) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-external-link-alt me-1"></i> Ver completo
                            </a>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <div class="display-5 text-primary">
                                            ${{ number_format($producto->precioMinimo, 2) }}
                                        </div>
                                        <small class="text-muted">Precio más bajo</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 mb-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <div class="display-5 text-success">
                                            ${{ number_format($producto->precioMaximo, 2) }}
                                        </div>
                                        <small class="text-muted">Precio más alto</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 mb-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <div class="display-5 text-info">
                                            {{ $producto->variaciones->where('bActivo', true)->count() }}
                                        </div>
                                        <small class="text-muted">Valoraciones activas</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 mb-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <div class="display-5 text-warning">
                                            {{ $producto->variaciones->sum('iStock') }}
                                        </div>
                                        <small class="text-muted">Stock total</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <div class="alert alert-info">
                                <i class="fas fa-lightbulb me-2"></i>
                                Este producto tiene <strong>{{ $producto->variaciones->count() }} valoraciones</strong> 
                                (variaciones) con diferentes combinaciones de atributos, precios y stock.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Footer -->
    <div class="container mt-5 mb-4">
        <div class="row">
            <div class="col-12 text-center">
                <hr>
                <p class="text-muted">
                    <i class="fas fa-wine-bottle me-1"></i>
                    Ecommerce Agave - Sistema de Gestión de Productos
                    <span class="mx-2">•</span>
                    ID: {{ $producto->id_producto }}
                </p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Función para confirmar eliminación
        function confirmarEliminacion() {
            return confirm('¿Estás seguro de eliminar este producto? Esta acción no se puede deshacer.');
        }
        
        // Inicializar tooltips de Bootstrap
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
        
        // Mostrar/ocultar descripción detallada
        document.addEventListener('DOMContentLoaded', function() {
            const descripcionLarga = document.querySelector('.descripcion-detallada');
            if (descripcionLarga) {
                const textoCompleto = descripcionLarga.textContent;
                if (textoCompleto.length > 300) {
                    descripcionLarga.textContent = textoCompleto.substring(0, 300) + '...';
                    const btnVerMas = document.createElement('button');
                    btnVerMas.className = 'btn btn-link p-0';
                    btnVerMas.textContent = 'Ver más';
                    btnVerMas.onclick = function() {
                        descripcionLarga.textContent = textoCompleto;
                        btnVerMas.style.display = 'none';
                    };
                    descripcionLarga.parentNode.appendChild(btnVerMas);
                }
            }
        });
    </script>
</body>
</html>