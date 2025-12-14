@extends('layouts.admins')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Detalle de Venta #{{ $venta->id_venta }}</h4>
                    <div class="btn-group">
                        <a href="{{ route('ventas.edit', $venta->id_venta) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="{{ route('ventas.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Información de la Venta</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>ID Pedido:</strong>
                                    <span>{{ $venta->id_pedido }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>ID Usuario:</strong>
                                    <span>{{ $venta->id_usuario }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Fecha:</strong>
                                    <span>
                                        @if($venta->tFecha_venta instanceof \DateTime)
                                            {{ $venta->tFecha_venta->format('d/m/Y H:i') }}
                                        @else
                                            {{ \Carbon\Carbon::parse($venta->tFecha_venta)->format('d/m/Y H:i') }}
                                        @endif
                                    </span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>Detalles de Pago</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Total:</strong>
                                    <span class="fw-bold text-success">${{ number_format($venta->dTotal, 2) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Método Pago:</strong>
                                    <span class="badge bg-info">{{ $venta->eMetodo_pago }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Estado:</strong>
                                    @php
                                    $badgeClass = [
                                        'completada' => 'bg-success',
                                        'devuelta' => 'bg-warning',
                                        'reembolsada' => 'bg-info',
                                        'cancelada' => 'bg-danger'
                                    ][$venta->eEstado] ?? 'bg-secondary';
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $venta->eEstado }}</span>
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