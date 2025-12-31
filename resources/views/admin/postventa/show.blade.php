@extends('layouts.admins')

@section('title', 'Detalle Postventa')

@section('content')

<h2 class="fw-bold mb-4">
    Solicitud #{{ $solicitud->id_solicitud }}
</h2>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <p><strong>Cliente:</strong> {{ $solicitud->pedido->usuario->vNombre }}</p>
        <p><strong>Pedido:</strong> #{{ $solicitud->id_pedido }}</p>
        <p><strong>Tipo:</strong> {{ ucfirst($solicitud->eTipo) }}</p>
        <p><strong>Motivo:</strong> {{ $solicitud->vMotivo }}</p>
        <p><strong>Estado:</strong>
            <span class="badge bg-warning">
                {{ ucfirst($solicitud->eEstado) }}
            </span>
        </p>
    </div>
</div>

@if($solicitud->eEstado === 'pendiente')
<div class="d-flex gap-2">
    <form method="POST" action="{{ route('admin.postventa.aprobar', $solicitud) }}">
        @csrf
        <button class="btn btn-success">
            Aprobar y reembolsar
        </button>
    </form>

    <form method="POST" action="{{ route('admin.postventa.rechazar', $solicitud) }}">
        @csrf
        <input type="hidden" name="respuesta" value="Solicitud rechazada por política interna">
        <button class="btn btn-danger">
            Rechazar
        </button>
    </form>
</div>
@endif

@endsection
