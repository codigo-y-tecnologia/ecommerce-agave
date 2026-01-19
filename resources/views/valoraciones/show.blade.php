@extends('layouts.app')

@section('title', 'Valoraciones - ' . $producto->vNombre)
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1><i class="fas fa-cubes me-2"></i>{{ $producto->vNombre }}</h1>
            <p class="text-muted">Gestionar valoraciones del producto</p>
        </div>
        <div>
            <a href="{{ route('valoraciones.create', $producto->id_producto) }}" class="btn btn-success">
                <i class="fas fa-plus me-1"></i> Nueva Valoración
            </a>
            <a href="{{ route('valoraciones.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información del Producto Base</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <strong>SKU Base:</strong>
                            <br>
                            <code>{{ $producto->vCodigo_barras }}</code>
                        </div>
                        <div class="col-md-4">
                            <strong>Categoría:</strong>
                            <br>
                            {{ $producto->categoria->vNombre ?? 'Sin categoría' }}
                        </div>
                        <div class="col-md-4">
                            <strong>Marca:</strong>
                            <br>
                            {{ $producto->marca->vNombre ?? 'Sin marca' }}
                        </div>
                    </div>
                    
                    @php
                        $atributosAgrupados = $producto->atributosAgrupados ?? [];
                    @endphp
                    
                    @if(!empty($atributosAgrupados))
                        <div class="mt-3">
                            <strong>Atributos Disponibles:</strong>
                            <div class="mt-2">
                                @foreach($atributosAgrupados as $atributo)
                                    @php
                                        $nombre = $atributo['nombre'] ?? 'Sin nombre';
                                        $valores = collect($atributo['valores'] ?? [])->pluck('valor')->filter()->implode(', ');
                                    @endphp
                                    @if($valores)
                                        <span class="badge bg-info me-2 mb-2">
                                            {{ $nombre }}: {{ $valores }}
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-list-alt me-2"></i>Valoraciones ({{ $producto->variaciones->count() }})</h5>
                </div>
                <div class="card-body">
                    @if($producto->variaciones->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Imagen</th>
                                        <th>SKU</th>
                                        <th>Precio</th>
                                        <th>Stock</th>
                                        <th>Largo (cm)</th>
                                        <th>Ancho (cm)</th>
                                        <th>Alto (cm)</th>
                                        <th>Peso (kg)</th>
                                        <th>Clase Envío</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($producto->variaciones as $variacion)
                                        @php
                                            $stockVariacion = $variacion->iStock ?? 0;
                                            $precioVariacion = floatval($variacion->dPrecio ?? 0);
                                            $precioOferta = $variacion->dPrecio_oferta ? floatval($variacion->dPrecio_oferta) : null;
                                            $pesoVariacion = $variacion->dPeso ? floatval($variacion->dPeso) : null;
                                            
                                            // Dimensiones separadas
                                            $largo = $variacion->dLargo_cm ? floatval($variacion->dLargo_cm) : null;
                                            $ancho = $variacion->dAncho_cm ? floatval($variacion->dAncho_cm) : null;
                                            $alto = $variacion->dAlto_cm ? floatval($variacion->dAlto_cm) : null;
                                        @endphp
                                        <tr>
                                            <td>
                                                @if($variacion->vImagen)
                                                    <img src="{{ asset($variacion->vImagen) }}" 
                                                         alt="Imagen"
                                                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                                @else
                                                    <div style="width: 50px; height: 50px; background-color: #f8f9fa; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #6c757d;">
                                                        <i class="fas fa-image"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <code>{{ $variacion->vSKU ?? 'N/A' }}</code>
                                            </td>
                                            <td>
                                                <strong>${{ number_format($precioVariacion, 2) }}</strong>
                                                @if($precioOferta && $precioOferta > 0)
                                                    <br>
                                                    <small class="text-success">
                                                        Oferta: ${{ number_format($precioOferta, 2) }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge {{ $stockVariacion > 10 ? 'bg-success' : ($stockVariacion > 0 ? 'bg-warning' : 'bg-danger') }}">
                                                    {{ $stockVariacion }} unidades
                                                </span>
                                            </td>
                                            <td style="text-align: center;">
                                                @if($largo)
                                                    {{ number_format($largo, 1) }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td style="text-align: center;">
                                                @if($ancho)
                                                    {{ number_format($ancho, 1) }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td style="text-align: center;">
                                                @if($alto)
                                                    {{ number_format($alto, 1) }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $pesoVariacion ? number_format($pesoVariacion, 2) : '-' }}
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $variacion->vClase_envio ?: 'Estándar' }}</span>
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
                                <tfoot>
                                    @php
                                        // Calcular SOLO precio total y stock total
                                        $stockTotal = $producto->variaciones->sum('iStock');
                                        $precioTotal = $producto->variaciones->sum('dPrecio');
                                    @endphp
                                    <tr class="table-info">
                                        <td colspan="2" class="text-end fw-bold">TOTALES:</td>
                                        <td class="fw-bold">
                                            ${{ number_format($precioTotal, 2) }}
                                        </td>
                                        <td class="fw-bold">
                                            {{ $stockTotal }} unidades
                                        </td>
                                        <td colspan="7"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-cubes fa-3x text-muted mb-2"></i>
                            <h4 class="text-muted">No hay valoraciones registradas</h4>
                            <p class="text-muted">Crea tu primera valoración para este producto</p>
                            <a href="{{ route('valoraciones.create', $producto->id_producto) }}" class="btn btn-success">
                                <i class="fas fa-plus me-1"></i> Crear Primera Valoración
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.table th {
    white-space: nowrap;
    vertical-align: middle;
}

.table td {
    vertical-align: middle;
}

.badge {
    font-size: 11px;
    font-weight: 500;
    padding: 4px 8px;
}

.table-dark th {
    background-color: #2E8B57;
    color: white;
}

.table-info {
    background-color: #d1ecf1;
}

.table-info td {
    font-weight: bold;
}

.card {
    border: 1px solid #dee2e6;
}

.card-header.bg-success {
    background-color: #2E8B57 !important;
}

.card-header.bg-primary {
    background-color: #2c3e50 !important;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 12px;
}

.btn-group-sm .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}
</style>
@endsection