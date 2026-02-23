@extends('layout.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Detalle de Venta #{{ $detalleVenta->id_detalle_venta }}</h4>
                    <div class="btn-group">
                        <a href="{{ route('detalle_venta.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Información del Detalle</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>ID Detalle:</strong>
                                    <span>{{ $detalleVenta->id_detalle_venta }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>ID Venta:</strong>
                                    <span>{{ $detalleVenta->id_venta }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>ID Producto:</strong>
                                    <span>{{ $detalleVenta->id_producto }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Cantidad:</strong>
                                    <span>{{ $detalleVenta->iCantidad }}</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>Información de Precios</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Precio Unitario:</strong>
                                    <span class="fw-bold text-primary">${{ number_format($detalleVenta->dPrecio_unitario, 2) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Subtotal:</strong>
                                    <span class="fw-bold text-success">${{ number_format($detalleVenta->dSubtotal, 2) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Producto:</strong>
                                    <span>{{ $detalleVenta->producto->vNombre ?? 'Producto no encontrado' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Cálculo:</strong>
                                    <span class="text-muted">{{ $detalleVenta->iCantidad }} × ${{ number_format($detalleVenta->dPrecio_unitario, 2) }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
