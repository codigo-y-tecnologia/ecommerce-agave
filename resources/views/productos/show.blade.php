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
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-images me-2"></i>Imágenes del Producto
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
                                        @if($index < 4)
                                            <div class="col-3 mb-2">
                                                <img src="{{ $imagen }}" 
                                                     alt="{{ $producto->vNombre }} - Imagen {{ $index + 1 }}"
                                                     class="img-fluid rounded"
                                                     style="height: 80px; width: 100%; object-fit: cover;">
                                            </div>
                                        @endif
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
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-info-circle me-2"></i>Información Básica
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td width="40%"><strong>Código de barras:</strong></td>
                                <td><code>{{ $producto->vCodigo_barras }}</code></td>
                            </tr>
                            <tr>
                                <td><strong>Categoría:</strong></td>
                                <td>{{ $producto->categoria->vNombre ?? 'Sin categoría' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Marca:</strong></td>
                                <td>{{ $producto->marca->vNombre ?? 'Sin marca' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Estado:</strong></td>
                                <td>
                                    <span class="badge {{ $producto->bActivo ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $producto->bActivo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Precio y stock -->
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-money-bill-wave me-2"></i>Precio y Stock
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Precio de venta</h5>
                                <div class="precio-destacado">
                                    @if($producto->tieneVariaciones())
                                        ${{ $producto->rangoPrecios }}
                                    @else
                                        ${{ number_format($producto->dPrecio_venta, 2) }}
                                    @endif
                                </div>
                                
                                @if($producto->dPrecio_compra)
                                    <div class="mt-2">
                                        <small class="text-muted">Precio de compra:</small>
                                        <div>${{ number_format($producto->dPrecio_compra, 2) }}</div>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="col-md-6">
                                <h5>Stock disponible</h5>
                                <div class="mt-2">
                                    <span class="badge stock-badge {{ $producto->iStock > 10 ? 'bg-success' : ($producto->iStock > 0 ? 'bg-warning' : 'bg-danger') }}">
                                        {{ $producto->iStock }} unidades
                                    </span>
                                </div>
                                
                                @if($producto->estaBajoEnStock())
                                    <div class="alert alert-warning mt-3 mb-0 py-2">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        Stock bajo
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Descripciones -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-file-alt me-2"></i>Descripción
                    </div>
                    <div class="card-body">
                        @if($producto->tDescripcion_corta)
                            <h5>Descripción corta</h5>
                            <p class="text-muted">{{ $producto->tDescripcion_corta }}</p>
                            <hr>
                        @endif
                        
                        @if($producto->tDescripcion_larga)
                            <h5>Descripción detallada</h5>
                            <p class="text-muted" style="white-space: pre-line;">{{ $producto->tDescripcion_larga }}</p>
                        @endif
                        
                        @if(!$producto->tDescripcion_corta && !$producto->tDescripcion_larga)
                            <div class="text-center py-3">
                                <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No hay descripción disponible</p>
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
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($producto->atributosAgrupados as $atributo)
                                    <div class="col-md-6 mb-3">
                                        <div class="border rounded p-3">
                                            <h6 class="fw-bold mb-2">{{ $atributo['nombre'] }}</h6>
                                            <div>
                                                @foreach($atributo['valores'] as $valor)
                                                    <span class="badge bg-secondary me-1 mb-1">
                                                        {{ $valor['valor'] }}
                                                        @if($valor['precio_extra'] > 0)
                                                            <small class="ms-1">(+${{ number_format($valor['precio_extra'], 2) }})</small>
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
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-edit me-1"></i> Gestionar Atributos
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Acciones -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-between">
                    <div>
                        <a href="{{ route('productos.edit', $producto) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i> Editar Producto
                        </a>
                        
                        @if(!$producto->tieneAtributos())
                            <a href="{{ route('productos.asignar-atributos', $producto->id_producto) }}" 
                               class="btn btn-outline-primary ms-2">
                                <i class="fas fa-tags me-1"></i> Agregar Atributos
                            </a>
                        @endif
                    </div>
                    
                    <div>
                        <form action="{{ route('productos.destroy', $producto) }}" method="POST" 
                              class="d-inline" onsubmit="return confirm('¿Eliminar este producto?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash me-1"></i> Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function confirmarEliminacion() {
            return confirm('¿Estás seguro de que deseas eliminar este producto? Esta acción no se puede deshacer.');
        }
    </script>
</body>
</html>