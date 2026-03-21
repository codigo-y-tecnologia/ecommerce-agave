@extends('layouts.app')

@section('title', 'Variaciones - ' . $producto->vNombre)

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
                            <li class="breadcrumb-item active" aria-current="page">{{ $producto->vNombre }}</li>
                        </ol>
                    </nav>

                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <div>
                            <h2 class="fw-bold mb-1">Variaciones de: {{ $producto->vNombre }}</h2>
                            <span class="text-muted">
                                <i class="fas fa-barcode me-1"></i>SKU Base: <span class="fw-semibold">{{ $producto->vCodigo_barras }}</span>
                                <span class="mx-2">|</span>
                                <i class="fas fa-cubes me-1"></i>{{ $producto->variaciones->count() }} variaciones
                            </span>
                        </div>
                        <div>
                            <a href="{{ route('variaciones.create', $producto->id_producto) }}" class="btn btn-success me-2">
                                <i class="fas fa-plus-circle me-1"></i>Nueva Variación
                            </a>
                            <a href="{{ route('variaciones.index') }}" class="btn btn-outline-secondary">
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
        // Calcular stock total
        $stockTotal = $producto->variaciones->sum('iStock');
        
        // Contar variaciones con descuento
        $variacionesConDescuento = $producto->variaciones->filter(function($v) {
            return $v->tieneDescuentoActivo() && $v->dPrecio_descuento < $v->dPrecio;
        })->count();
    @endphp

    <!-- SOLO LA TABLA DE VARIACIONES -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-cubes me-2 text-primary"></i>
                        Listado de Variaciones
                    </h5>
                    <div class="d-flex gap-2">
                        <span class="badge bg-primary p-2">{{ $producto->variaciones->count() }} variaciones</span>
                        @if($variacionesConDescuento > 0)
                            <span class="badge bg-danger p-2">
                                <i class="fas fa-tag me-1"></i>{{ $variacionesConDescuento }} con descuento
                            </span>
                        @endif
                        <span class="badge bg-success p-2">Stock: {{ number_format($stockTotal) }}</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="py-3 px-4" style="width: 100px;">Imagen</th>
                                    <th class="py-3">SKU / Nombre</th>
                                    <th class="py-3">Atributos</th>
                                    <th class="py-3">Precios</th>
                                    <th class="py-3">Stock</th>
                                    <th class="py-3">Dimensiones</th>
                                    <th class="py-3">Peso</th>
                                    <th class="py-3">Clase Envío</th>
                                    <th class="py-3">Estado</th>
                                    <th class="py-3 text-end pe-4">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($producto->variaciones as $variacion)
                                    @php
                                        // Verificar si tiene descuento activo
                                        $tieneDescuento = $variacion->tieneDescuentoActivo();
                                        $precioBase = $tieneDescuento ? $variacion->dPrecio_descuento : $variacion->dPrecio;
                                        $porcentajeDescuento = $variacion->porcentaje_descuento;
                                        
                                        // Obtener imágenes
                                        $imagenes = $variacion->imagenes ?? [];
                                        
                                        // Obtener el nombre de la variación (primer valor de atributo)
                                        $nombreVariacion = '';
                                        $todosAtributos = [];
                                        foreach($variacion->atributos as $atributoRel) {
                                            if($atributoRel->atributo && $atributoRel->valor) {
                                                $todosAtributos[] = $atributoRel->atributo->vNombre . ': ' . $atributoRel->valor->vValor;
                                                if(empty($nombreVariacion)) {
                                                    $nombreVariacion = $atributoRel->valor->vValor;
                                                }
                                            }
                                        }
                                        
                                        // Calcular impuestos
                                        $impuestoVariacion = $variacion->impuesto ?? $producto->impuestos->first();
                                        $totalImpuestos = 0;
                                        $porcentajeImpuesto = 0;
                                        if ($impuestoVariacion) {
                                            $porcentajeImpuesto = $impuestoVariacion->dPorcentaje;
                                            $totalImpuestos = $precioBase * ($porcentajeImpuesto / 100);
                                        }
                                        $precioFinal = $precioBase + $totalImpuestos;
                                        
                                        // Dimensiones
                                        $largo = $variacion->dLargo_cm ? floatval($variacion->dLargo_cm) : null;
                                        $ancho = $variacion->dAncho_cm ? floatval($variacion->dAncho_cm) : null;
                                        $alto = $variacion->dAlto_cm ? floatval($variacion->dAlto_cm) : null;
                                        $peso = $variacion->dPeso ? floatval($variacion->dPeso) : null;
                                        
                                        // Clase de envío
                                        $claseEnvioText = '';
                                        $claseEnvioClass = '';
                                        switch($variacion->vClase_envio ?? $producto->vClase_envio) {
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
                                                $claseEnvioText = $variacion->vClase_envio ?: 'Estándar';
                                                $claseEnvioClass = 'bg-secondary';
                                        }
                                        
                                        // Stock class
                                        $stockValue = intval($variacion->iStock);
                                        $stockClass = $stockValue > 50 ? 'success' : ($stockValue > 10 ? 'warning' : 'danger');
                                        
                                        // Calcular ahorro para mostrar tooltip
                                        $ahorro = $tieneDescuento ? ($variacion->dPrecio - $variacion->dPrecio_descuento) : 0;
                                    @endphp
                                    <tr>
                                        <td class="px-4">
                                            @if(!empty($imagenes) && count($imagenes) > 0)
                                                <div class="position-relative d-inline-block">
                                                    <img src="{{ $imagenes[0] }}" 
                                                         alt="Imagen"
                                                         class="rounded-3 border"
                                                         style="width: 60px; height: 60px; object-fit: cover; cursor: pointer;"
                                                         onclick="verImagenesVariacion({{ $variacion->id_variacion }}, '{{ $variacion->vSKU }}', {{ json_encode($imagenes) }})"
                                                         onerror="this.onerror=null; this.src='https://via.placeholder.com/60x60?text=Error';">
                                                    @if(count($imagenes) > 1)
                                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary" style="z-index: 10;">
                                                            +{{ count($imagenes)-1 }}
                                                        </span>
                                                    @endif
                                                    
                                                    @if($tieneDescuento && $variacion->dPrecio_descuento < $variacion->dPrecio)
                                                        <span class="position-absolute top-0 start-0 badge bg-danger" style="z-index: 10; font-size: 10px;">
                                                            -{{ $porcentajeDescuento }}%
                                                        </span>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="bg-light rounded-3 d-flex align-items-center justify-content-center position-relative" 
                                                     style="width: 60px; height: 60px; cursor: pointer;"
                                                     onclick="verImagenesVariacion({{ $variacion->id_variacion }}, '{{ $variacion->vSKU }}', [])">
                                                    <i class="fas fa-image text-muted fa-2x"></i>
                                                    @if($tieneDescuento && $variacion->dPrecio_descuento < $variacion->dPrecio)
                                                        <span class="position-absolute top-0 start-0 badge bg-danger" style="z-index: 10; font-size: 10px;">
                                                            -{{ $porcentajeDescuento }}%
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $nombreVariacion ?: 'Variación' }}</div>
                                            <small class="text-muted d-block">
                                                <i class="fas fa-barcode me-1"></i>{{ $variacion->vSKU }}
                                            </small>
                                        </td>
                                        <td>
                                            @if(!empty($todosAtributos))
                                                @foreach($todosAtributos as $texto)
                                                    <span class="badge bg-light text-dark border p-2 mb-1 d-block text-start">
                                                        <i class="fas fa-tag text-primary me-1"></i>{{ $texto }}
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="badge bg-secondary">Sin atributos</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="vstack gap-1">
                                                <div class="d-flex align-items-center flex-wrap gap-1">
                                                    <span class="fw-bold">${{ number_format($variacion->dPrecio, 2) }}</span>
                                                    @if($tieneDescuento && $variacion->dPrecio_descuento < $variacion->dPrecio)
                                                        <span class="badge bg-danger">
                                                            -{{ $porcentajeDescuento }}%
                                                        </span>
                                                    @endif
                                                </div>
                                                
                                                @if($tieneDescuento && $variacion->dPrecio_descuento < $variacion->dPrecio)
                                                    <div class="text-success small">
                                                        <i class="fas fa-tag me-1"></i>
                                                        <span class="fw-bold">Con descuento:</span> 
                                                        <span class="text-danger">${{ number_format($variacion->dPrecio_descuento, 2) }}</span>
                                                    </div>
                                                    
                                                    @if($variacion->vMotivo_descuento)
                                                        <small class="text-muted" title="{{ $variacion->vMotivo_descuento }}">
                                                            <i class="fas fa-comment me-1"></i>{{ Str::limit($variacion->vMotivo_descuento, 15) }}
                                                        </small>
                                                    @endif
                                                    
                                                    @if($variacion->dFecha_inicio_descuento && $variacion->dFecha_fin_descuento)
                                                        <small class="text-muted">
                                                            <i class="fas fa-calendar-alt me-1"></i>
                                                            {{ \Carbon\Carbon::parse($variacion->dFecha_inicio_descuento)->format('d/m') }} - 
                                                            {{ \Carbon\Carbon::parse($variacion->dFecha_fin_descuento)->format('d/m') }}
                                                        </small>
                                                    @endif
                                                @endif
                                                
                                                @if($impuestoVariacion)
                                                    <small class="text-muted">
                                                        <i class="fas fa-file-invoice-dollar me-1"></i>
                                                        {{ $impuestoVariacion->vNombre }} ({{ $impuestoVariacion->dPorcentaje }}%)
                                                    </small>
                                                @endif
                                                
                                                <small class="text-primary fw-bold">
                                                    Total: ${{ number_format($precioFinal, 2) }}
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $stockClass }} p-2" style="font-size: 14px;" 
                                                  title="{{ $stockValue }} unidades disponibles">
                                                <i class="fas fa-boxes me-1"></i>{{ number_format($stockValue) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($largo || $ancho || $alto)
                                                <div class="small">
                                                    @if($largo)<div>L: {{ number_format($largo, 2) }} cm</div>@endif
                                                    @if($ancho)<div>A: {{ number_format($ancho, 2) }} cm</div>@endif
                                                    @if($alto)<div>Al: {{ number_format($alto, 2) }} cm</div>@endif
                                                </div>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($peso)
                                                <span>{{ number_format($peso, 3) }} kg</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $claseEnvioClass }} p-2">
                                                <i class="fas fa-shipping-fast me-1"></i>{{ $claseEnvioText }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($variacion->bActivo)
                                                <span class="badge bg-success p-2">
                                                    <i class="fas fa-check-circle me-1"></i>Activo
                                                </span>
                                            @else
                                                <span class="badge bg-secondary p-2">
                                                    <i class="fas fa-times-circle me-1"></i>Inactivo
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="btn-group" role="group">
                                                <!-- BOTÓN VER - REDIRIGE A LA NUEVA VISTA DE DETALLE -->
                                                <a href="{{ route('variaciones.show.variacion', ['producto_id' => $producto->id_producto, 'variacion_id' => $variacion->id_variacion]) }}" 
                                                   class="btn btn-sm btn-outline-primary" title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('variaciones.edit', ['producto_id' => $producto->id_producto, 'variacion_id' => $variacion->id_variacion]) }}" 
                                                   class="btn btn-sm btn-outline-warning" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger" title="Eliminar"
                                                        onclick="confirmDelete({{ $variacion->id_variacion }}, '{{ $variacion->vSKU }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                            <form id="delete-form-{{ $variacion->id_variacion }}" 
                                                  action="{{ route('variaciones.destroy', ['producto_id' => $producto->id_producto, 'variacion_id' => $variacion->id_variacion]) }}" 
                                                  method="POST" class="d-none">
                                                @csrf @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-5">
                                            <i class="fas fa-cubes fa-4x text-muted mb-3"></i>
                                            <h4 class="text-muted">No hay variaciones registradas</h4>
                                            <p class="text-muted">Crea tu primera variación para este producto</p>
                                            <a href="{{ route('variaciones.create', $producto->id_producto) }}" class="btn btn-success">
                                                <i class="fas fa-plus-circle me-1"></i>Crear Primera Variación
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Función para ver imágenes de variación
function verImagenesVariacion(id, sku, imagenes) {
    // Crear modal dinámico para mostrar imágenes
    let imagenesHtml = '';
    
    if (imagenes && imagenes.length > 0) {
        imagenesHtml = `
            <div id="carousel-${id}" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
        `;
        
        imagenes.forEach((img, index) => {
            imagenesHtml += `
                <div class="carousel-item ${index === 0 ? 'active' : ''}">
                    <img src="${img}" class="d-block w-100" style="max-height: 400px; object-fit: contain;" alt="Imagen ${index + 1}">
                </div>
            `;
        });
        
        imagenesHtml += `
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carousel-${id}" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carousel-${id}" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Siguiente</span>
                </button>
            </div>
        `;
    } else {
        imagenesHtml = `
            <div class="text-center py-5">
                <i class="fas fa-image fa-4x text-muted mb-3"></i>
                <p class="text-muted">Esta variación no tiene imágenes</p>
            </div>
        `;
    }
    
    Swal.fire({
        title: `Imágenes de Variación - SKU: ${sku}`,
        html: imagenesHtml,
        showCloseButton: true,
        showConfirmButton: false,
        width: '800px',
        customClass: {
            popup: 'border-0 rounded-4'
        }
    });
}

// Función para eliminar variación
function confirmDelete(id, sku) {
    Swal.fire({
        title: '¿Eliminar variación?',
        html: `Estás a punto de eliminar la variación <strong>${sku}</strong>.<br>Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}

// Mostrar mensaje SweetAlert2 después de operaciones exitosas
@if(session('success'))
    @if(str_contains(session('success'), 'eliminada'))
        Swal.fire({
            title: '¡Eliminada!',
            text: "{{ session('success') }}",
            icon: 'success',
            timer: 3000,
            showConfirmButton: false
        });
    @elseif(str_contains(session('success'), 'creada'))
        Swal.fire({
            title: '¡Creada!',
            text: "{{ session('success') }}",
            icon: 'success',
            timer: 3000,
            showConfirmButton: false
        });
    @elseif(str_contains(session('success'), 'actualizada'))
        Swal.fire({
            title: '¡Actualizada!',
            text: "{{ session('success') }}",
            icon: 'success',
            timer: 3000,
            showConfirmButton: false
        });
    @else
        Swal.fire({
            title: '¡Éxito!',
            text: "{{ session('success') }}",
            icon: 'success',
            timer: 3000,
            showConfirmButton: false
        });
    @endif
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
.table th {
    background-color: #f8f9fa;
    font-weight: 600;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
}

.table td {
    vertical-align: middle;
    padding: 1rem 0.5rem;
}

.table tbody tr:hover {
    background-color: rgba(0,123,255,0.05);
}

.btn-group .btn {
    padding: 0.25rem 0.5rem;
    transition: all 0.2s ease;
}

.btn-group .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.badge {
    font-weight: 500;
}

.modal-header.bg-primary {
    background: linear-gradient(135deg, #007bff, #0056b3) !important;
}

.modal-content {
    border: none;
    border-radius: 12px;
}

.modal-header {
    border-radius: 12px 12px 0 0;
}

.carousel-control-prev,
.carousel-control-next {
    background-color: rgba(0,0,0,0.5);
    width: 50px;
    height: 50px;
    border-radius: 50%;
    top: 50%;
    transform: translateY(-50%);
}

.carousel-control-prev {
    left: 20px;
}

.carousel-control-next {
    right: 20px;
}

@media (max-width: 768px) {
    .table td {
        padding: 0.75rem 0.5rem;
    }
    
    .btn-group .btn {
        padding: 0.2rem 0.4rem;
    }
}
</style>
@endpush

@endsection