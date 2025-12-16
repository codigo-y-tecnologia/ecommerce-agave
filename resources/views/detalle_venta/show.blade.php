@extends('layout.app')

@section('title', 'Detalle de Venta #' . $detalleVenta->id_detalle_venta)

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-eye"></i> Detalle de Venta #{{ $detalleVenta->id_detalle_venta }}
                    </h4>
                    <div class="btn-group">
                        <a href="{{ route('detalle_venta.edit', $detalleVenta->id_detalle_venta) }}" 
                           class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <form action="{{ route('detalle_venta.destroy', $detalleVenta->id_detalle_venta) }}" 
                              method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger ms-2"
                                    onclick="return confirm('¿Estás seguro de eliminar este detalle?')">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <!-- Información Principal -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-info-circle"></i> Información del Detalle
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%" class="text-muted">ID Detalle:</th>
                                            <td>
                                                <span class="badge bg-dark fs-6">
                                                    {{ $detalleVenta->id_detalle_venta }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Venta ID:</th>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    #{{ $detalleVenta->id_venta }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Producto ID:</th>
                                            <td>
                                                <span class="badge bg-info">
                                                    #{{ $detalleVenta->id_producto }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Cantidad:</th>
                                            <td>
                                                <span class="badge bg-primary rounded-pill fs-6">
                                                    {{ $detalleVenta->iCantidad }} unidades
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Precio Unitario:</th>
                                            <td>
                                                <span class="text-success fw-bold fs-5">
                                                    ${{ number_format($detalleVenta->dPrecio_unitario, 2) }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Cálculos -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-calculator"></i> Cálculos
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="text-center py-4">
                                        <div class="display-6 text-success mb-3">
                                            ${{ number_format($detalleVenta->dSubtotal, 2) }}
                                        </div>
                                        <p class="text-muted mb-0">Subtotal</p>
                                        <hr>
                                        <div class="mt-3">
                                            <p class="mb-1">
                                                <strong>Cálculo:</strong> 
                                                {{ $detalleVenta->iCantidad }} × 
                                                ${{ number_format($detalleVenta->dPrecio_unitario, 2) }}
                                            </p>
                                            <p class="mb-0 text-muted">
                                                <small>
                                                    <i class="fas fa-clock"></i> 
                                                    Creado el: {{ $detalleVenta->created_at ? $detalleVenta->created_at->format('d/m/Y H:i') : 'N/A' }}
                                                </small>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Botones de acción -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('detalle_venta.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Volver a la lista
                                </a>
                                <div>
                                    <a href="{{ route('detalle_venta.edit', $detalleVenta->id_detalle_venta) }}" 
                                       class="btn btn-warning me-2">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <a href="{{ route('detalle_venta.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Nuevo Detalle
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection