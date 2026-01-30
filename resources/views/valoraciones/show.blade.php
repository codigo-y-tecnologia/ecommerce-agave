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
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if(request('search') && request('search') != '')
                <div class="alert alert-info mb-3">
                    Resultados para: "{{ request('search') }}"
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
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Imagen</th>
                                <th>SKU</th>
                                <th>Precio</th>
                                <th>Stock</th>
                                <th>Dimensiones</th>
                                <th>Peso</th>
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
                                    
                                    // Calcular dimensiones concatenadas para mostrar
                                    $dimensiones = [];
                                    if($largo) $dimensiones[] = 'L: ' . number_format($largo, 1) . 'cm';
                                    if($ancho) $dimensiones[] = 'A: ' . number_format($ancho, 1) . 'cm';
                                    if($alto) $dimensiones[] = 'H: ' . number_format($alto, 1) . 'cm';
                                    $dimensionesStr = implode('<br>', $dimensiones);
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
                                    <td>
                                        @if($dimensionesStr)
                                            <small>{!! $dimensionesStr !!}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $pesoVariacion ? number_format($pesoVariacion, 2) . ' kg' : '-' }}
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
                                            <button type="button" class="btn btn-danger" 
                                                    onclick="confirmarEliminacion('{{ addslashes($variacion->vSKU) }}', {{ $variacion->id_variacion }})"
                                                    title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <form id="delete-form-{{ $variacion->id_variacion }}" 
                                                  action="{{ route('valoraciones.destroy', ['producto_id' => $producto->id_producto, 'variacion_id' => $variacion->id_variacion]) }}" 
                                                  method="POST" class="d-none">
                                                @csrf @method('DELETE')
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            @php
                                // Calcular SOLO precio total y stock total
                                $stockTotal = $variaciones->sum('iStock');
                                $precioTotal = $variaciones->sum('dPrecio');
                            @endphp
                            <tr class="table-info">
                                <td colspan="3" class="text-end fw-bold">TOTALES:</td>
                                <td class="fw-bold">
                                    {{ $stockTotal }} unidades
                                </td>
                                <td colspan="2" class="fw-bold">
                                    ${{ number_format($precioTotal, 2) }}
                                </td>
                                <td colspan="3"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <div class="mt-3 p-3 bg-light rounded">
                    <strong>Total:</strong> {{ $variaciones->count() }} valoraciones
                    @if(request('search'))
                        <span class="ms-3">(filtradas)</span>
                    @endif
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
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@push('scripts')
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function confirmarEliminacion(sku, id) {
    Swal.fire({
        title: "¿Estás seguro?",
        text: "¡La valoración con SKU \"" + sku + "\" será eliminada!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            // Enviar el formulario de eliminación
            document.getElementById('delete-form-' + id).submit();
        }
    });
}

// Mostrar mensaje de éxito después de eliminar
@if(session('success'))
Swal.fire({
    title: "¡Éxito!",
    text: "{{ session('success') }}",
    icon: "success",
    timer: 3000,
    showConfirmButton: false
});
@endif

// Mostrar mensaje de error si hubo un problema
@if(session('error'))
Swal.fire({
    title: "¡Error!",
    text: "{{ session('error') }}",
    icon: "error",
    timer: 3000,
    showConfirmButton: false
});
@endif
</script>
@endpush