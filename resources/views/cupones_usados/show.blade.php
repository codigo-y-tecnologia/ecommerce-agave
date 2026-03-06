@extends('layout.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Cupón Usado — Venta #{{ $cuponUsado->id_venta }}</h4>
                    <div class="btn-group">
                        <a href="{{ route('cupones_usados.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                        <a href="{{ route('cupones_usados.edit', ['id' => $cuponUsado->id_cupon . '-' . $cuponUsado->id_venta]) }}"
                           class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Información del Cupón</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>ID Cupón:</strong>
                                    <span>{{ $cuponUsado->id_cupon }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Código:</strong>
                                    <span class="badge bg-primary fs-6">{{ $cuponUsado->codigo_cupon ?? 'N/A' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Nombre:</strong>
                                    <span>{{ $cuponUsado->codigo_cupon ?? 'N/A' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Tipo de Descuento:</strong>
                                    <span>{{ ucfirst($cuponUsado->tipo_descuento ?? 'N/A') }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Valor del Cupón:</strong>
                                    <span>
                                        @if(($cuponUsado->tipo_descuento ?? '') === 'porcentaje')
                                            {{ $cuponUsado->valor_descuento ?? 'N/A' }}%
                                        @else
                                            ${{ number_format($cuponUsado->valor_descuento ?? 0, 2) }}
                                        @endif
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Válido hasta:</strong>
                                    <span class="text-muted">
                                        {{ $cuponUsado->fecha_expiracion
                                            ? \Carbon\Carbon::parse($cuponUsado->fecha_expiracion)->format('d/m/Y')
                                            : 'Sin expiración' }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>Información de Uso</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>ID Venta:</strong>
                                    <span><strong>#{{ $cuponUsado->id_venta }}</strong></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Usuario:</strong>
                                    <span>
                                        @if($cuponUsado->usuario_nombre)
                                            {{ $cuponUsado->usuario_nombre }} {{ $cuponUsado->usuario_apellido1 }}
                                        @else
                                            <span class="text-muted">Invitado</span>
                                        @endif
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Email:</strong>
                                    <span>{{ $cuponUsado->usuario_email ?? 'N/A' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Guest Token:</strong>
                                    <span class="text-muted" style="font-size:0.85rem;">
                                        {{ $cuponUsado->guest_token ?? '—' }}
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Fecha de Uso:</strong>
                                    <span>{{ \Carbon\Carbon::parse($cuponUsado->tFecha_uso)->format('d/m/Y H:i') }}</span>
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