@extends('layouts.admins')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Detalle de Reembolso #{{ $reembolso->id_reembolso }}</h4>
                    <div class="btn-group">
                        <a href="{{ route('reembolsos.edit', $reembolso->id_reembolso) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="{{ route('reembolsos.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Información del Reembolso</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>ID Reembolso:</strong>
                                    <span>{{ $reembolso->id_reembolso }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>ID Venta:</strong>
                                    <span>{{ $reembolso->id_venta }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Fecha Reembolso:</strong>
                                    <span>
                                        @if($reembolso->tFecha_reembolso instanceof \DateTime)
                                            {{ $reembolso->tFecha_reembolso->format('d/m/Y H:i') }}
                                        @else
                                            {{ \Carbon\Carbon::parse($reembolso->tFecha_reembolso)->format('d/m/Y H:i') }}
                                        @endif
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Motivo:</strong>
                                    <span>{{ $reembolso->vMotivo ?? 'Sin motivo especificado' }}</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>Detalles de Pago</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Monto:</strong>
                                    <span class="fw-bold text-success">${{ number_format($reembolso->dMonto, 2) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Método Pago:</strong>
                                    <span class="badge bg-info text-capitalize">{{ $reembolso->eMetodo_pago }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Estado:</strong>
                                    @php
                                    $badgeClass = [
                                        'completado' => 'bg-success',
                                        'procesando' => 'bg-warning',
                                        'pendiente' => 'bg-info',
                                        'fallido' => 'bg-danger'
                                    ][$reembolso->eEstado] ?? 'bg-secondary';
                                    @endphp
                                    <span class="badge {{ $badgeClass }} text-capitalize">{{ $reembolso->eEstado }}</span>
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