@extends('layouts.admins')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Cupón Usado #{{ $cupon->id_cupon }}</h4>
                    <div class="btn-group">
                        <a href="{{ route('cupones_usados.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
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
                                    <span>{{ $cupon->id_cupon }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>ID Venta:</strong>
                                    <span>{{ $cupon->id_venta }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Fecha de Uso:</strong>
                                    <span>{{ \Carbon\Carbon::parse($cupon->tFecha_uso)->format('d/m/Y H:i:s') }}</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>Información del Usuario</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>ID Usuario:</strong>
                                    <span>{{ $cupon->id_usuario ?? '—' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <strong>Guest Token:</strong>
                                    <span class="font-monospace text-break text-end" style="max-width: 60%">
                                        {{ $cupon->guest_token ?? '—' }}
                                    </span>
                                </li>
                                @if($cupon->usuario)
                                    <li class="list-group-item d-flex justify-content-between">
                                        <strong>Nombre:</strong>
                                        <span>{{ $cupon->usuario->vNombre ?? '—' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <strong>Email:</strong>
                                        <span>{{ $cupon->usuario->vEmail ?? '—' }}</span>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <a href="{{ route('cupones_usados.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Volver al listado
                        </a>
                        <form action="{{ route('cupones_usados.destroy', $cupon->id_cupon) }}"
                              method="POST"
                              onsubmit="return confirm('¿Estás seguro de eliminar este cupón usado?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection