@extends('layouts.app')

@section('title', 'Valoraciones - ' . $producto->vNombre)
@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>{{ $producto->vNombre }}</h2>
                    <p class="text-muted mb-0">Gestionar valoraciones del producto</p>
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
            
            <!-- Información del producto -->
            <div class="mt-3 pt-3 border-top">
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
                    <div class="mt-2">
                        <strong>Atributos Disponibles:</strong>
                        <div class="mt-1">
                            @foreach($atributosAgrupados as $atributo)
                                @php
                                    $nombre = $atributo['nombre'] ?? 'Sin nombre';
                                    $valores = collect($atributo['valores'] ?? [])->pluck('valor')->filter()->implode(', ');
                                @endphp
                                @if($valores)
                                    <span class="badge bg-info me-1 mb-1">
                                        {{ $nombre }}: {{ $valores }}
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
            
            <!-- Formulario de búsqueda -->
            <form method="GET" action="{{ request()->url() }}" class="mt-3">
                <div class="row">
                    <div class="col-md-8">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Buscar valoraciones por SKU, precio o stock..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Buscar</button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('valoraciones.show', $producto->id_producto) }}" class="btn btn-secondary w-100">Limpiar</a>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(request('search') && request('search') != '')
                <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
                    Resultados para: "{{ request('search') }}"
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($producto->variaciones->count() > 0)
                <!-- Filtrado de variaciones si hay búsqueda -->
                @php
                    $variaciones = $producto->variaciones;
                    
                    if(request('search') && request('search') != '') {
                        $searchTerm = strtolower(request('search'));
                        $variaciones = $variaciones->filter(function($variacion) use ($searchTerm) {
                            return str_contains(strtolower($variacion->vSKU ?? ''), $searchTerm) ||
                                   str_contains(strtolower($variacion->dPrecio ?? ''), $searchTerm) ||
                                   str_contains(strtolower($variacion->iStock ?? ''), $searchTerm);
                        });
                    }
                @endphp
                
                @if($variaciones->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-2">
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
                            @foreach($variaciones as $variacion)
                                @php
                                    $stockVariacion = $variacion->iStock ?? 0;
                                    $precioVariacion = floatval($variacion->dPrecio ?? 0);
                                    $precioOferta = $variacion->dPrecio_oferta ? floatval($variacion->dPrecio_oferta) : null;
                                    $pesoVariacion = $variacion->dPeso ? floatval($variacion->dPeso) : null;
                                    
                                    // Dimensiones separadas
                                    $largo = $variacion->dLargo_cm ? floatval($variacion->dLargo_cm) : null;
                                    $ancho = $variacion->dAncho_cm ? floatval($variacion->dAncho_cm) : null;
                                    $alto = $variacion->dAlto_cm ? floatval($variacion->dAlto_cm) : null;
                                    
                                    // Información de oferta especial
                                    $tieneOferta = $variacion->bTiene_oferta ?? false;
                                    $motivoOferta = $variacion->vMotivo_oferta ?? null;
                                    $fechaInicioOferta = $variacion->dFecha_inicio_oferta ?? null;
                                    $fechaFinOferta = $variacion->dFecha_fin_oferta ?? null;
                                    
                                    // Atributos de la variación
                                    $atributosVariacion = $variacion->atributosValores ?? [];
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
                                        @if(!empty($atributosVariacion))
                                            <br>
                                            <small class="text-muted">
                                                @foreach($atributosVariacion as $atributo)
                                                    <span class="badge bg-secondary">{{ $atributo }}</span>
                                                @endforeach
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="text-primary fw-bold">
                                            ${{ number_format($precioVariacion, 2) }}
                                        </div>
                                        @if($tieneOferta && $precioOferta && $precioOferta > 0)
                                            <div class="text-success">
                                                <small class="d-block">
                                                    <strong>Oferta: ${{ number_format($precioOferta, 2) }}</strong>
                                                </small>
                                                @if($motivoOferta)
                                                    <small class="d-block text-muted">
                                                        {{ Str::limit($motivoOferta, 20) }}
                                                    </small>
                                                @endif
                                                @if($fechaInicioOferta && $fechaFinOferta)
                                                    <small class="d-block text-muted">
                                                        {{ date('d/m/Y', strtotime($fechaInicioOferta)) }} - {{ date('d/m/Y', strtotime($fechaFinOferta)) }}
                                                    </small>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $stockVariacion > 10 ? 'bg-success' : ($stockVariacion > 0 ? 'bg-warning' : 'bg-danger') }}">
                                            {{ $stockVariacion }} unidades
                                        </span>
                                    </td>
                                    <td>
                                        @if($largo)
                                            <span class="text-primary">{{ number_format($largo, 2) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($ancho)
                                            <span class="text-primary">{{ number_format($ancho, 2) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($alto)
                                            <span class="text-primary">{{ number_format($alto, 2) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($pesoVariacion)
                                            <span class="text-primary">{{ number_format($pesoVariacion, 3) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
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
                                            <button type="button" class="btn btn-danger eliminar-btn" 
                                                    data-sku="{{ addslashes($variacion->vSKU) }}" 
                                                    data-id="{{ $variacion->id_variacion }}"
                                                    title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <form id="delete-form-{{ $variacion->id_variacion }}" 
                                                  action="{{ route('valoraciones.destroyValoracion', ['producto_id' => $producto->id_producto, 'variacion_id' => $variacion->id_variacion]) }}" 
                                                  method="POST" class="d-none">
                                                @csrf @method('DELETE')
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Footer azul delgado alineado a la derecha -->
                <div class="table-footer bg-primary text-white p-2 rounded">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-start">
                            <strong>Total valoraciones:</strong> {{ $variaciones->count() }}
                            @if(request('search'))
                                <span class="ms-2">(filtradas)</span>
                            @endif
                        </div>
                        <div class="text-end">
                            @php
                                $stockTotal = $variaciones->sum('iStock');
                                $precioPromedio = $variaciones->avg('dPrecio');
                            @endphp
                            <strong>Stock total:</strong> {{ number_format($stockTotal) }} unidades | 
                            <strong>Precio promedio:</strong> ${{ number_format($precioPromedio, 2) }}
                        </div>
                    </div>
                </div>
                
                @else
                <div class="text-center py-5">
                    <h4 class="text-muted">No se encontraron resultados</h4>
                    <p class="text-muted">No hay valoraciones que coincidan con "{{ request('search') }}"</p>
                    <a href="{{ route('valoraciones.show', $producto->id_producto) }}" class="btn btn-primary">
                        Ver todas las valoraciones
                    </a>
                </div>
                @endif
                
            @else
                <div class="text-center py-5">
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
@endsection

@push('styles')
<style>
    .table th {
        background-color: #2E8B57;
        color: white;
        vertical-align: middle;
        white-space: nowrap;
        font-size: 14px;
        padding: 10px 8px;
    }
    
    .table td {
        vertical-align: middle;
        padding: 8px;
        font-size: 13px;
    }
    
    .badge {
        font-size: 11px;
        font-weight: 600;
        padding: 4px 8px;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.03);
    }
    
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0, 0, 0, 0.01);
    }
    
    .btn-group-sm .btn {
        padding: 0.2rem 0.4rem;
        font-size: 11px;
        border-radius: 3px;
    }
    
    .btn-group-sm .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .text-primary {
        font-weight: 500;
        color: #0d6efd !important;
    }
    
    .table-footer {
        background-color: #0d6efd;
        color: white;
        font-size: 13px;
        padding: 8px 12px;
        border-radius: 4px;
        margin-top: 0;
    }
    
    .table-footer strong {
        color: white;
    }
    
    .alert {
        font-size: 14px;
        padding: 10px 15px;
        margin-bottom: 15px;
    }
    
    .alert .btn-close {
        padding: 12px;
    }
    
    code {
        font-size: 12px;
        background: #f8f9fa;
        padding: 2px 5px;
        border-radius: 3px;
        color: #d63384;
    }
    
    .text-muted {
        font-size: 12px;
    }
</style>
@endpush

@push('scripts')
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejar eliminación con SweetAlert2
    document.querySelectorAll('.eliminar-btn').forEach(button => {
        button.addEventListener('click', function() {
            const sku = this.getAttribute('data-sku');
            const id = this.getAttribute('data-id');
            
            Swal.fire({
                title: "¿Estás seguro?",
                text: "¡No podrás revertir esta acción!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar",
                position: "center"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        });
    });

    // Mostrar mensaje SweetAlert2 después de eliminar exitosamente
    @if(session('success') && str_contains(session('success'), 'eliminada'))
    Swal.fire({
        title: "¡Eliminado!",
        text: "{{ session('success') }}",
        icon: "success",
        position: "center",
        timer: 3000,
        showConfirmButton: false
    });
    @endif

    // Mostrar mensaje SweetAlert2 después de crear exitosamente
    @if(session('success') && str_contains(session('success'), 'creada'))
    Swal.fire({
        title: "¡Registrado!",
        text: "{{ session('success') }}",
        icon: "success",
        draggable: true,
        position: "center",
        timer: 3000,
        showConfirmButton: false
    });
    @endif

    // Mostrar mensaje SweetAlert2 si hay error
    @if(session('error') || $errors->any())
    @php
        $errorMessage = session('error');
        if (!$errorMessage && $errors->any()) {
            $errorMessage = 'Por favor corrige los errores en el formulario.';
        }
    @endphp
    Swal.fire({
        icon: "error",
        title: "Oops...",
        text: "{{ $errorMessage }}",
        footer: '<a href="#form-errors">Ver errores en el formulario</a>',
        position: "center",
        draggable: true
    });
    @endif
});
</script>
@endpush
