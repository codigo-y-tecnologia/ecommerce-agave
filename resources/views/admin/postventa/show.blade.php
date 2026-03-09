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

    {{-- APROBAR --}}
    <form method="POST"
      action="{{ route('admin.postventa.aprobar', $solicitud) }}"
      class="form-aprobar">
    @csrf
    <button type="button"
            class="btn btn-success"
            onclick="aprobarSolicitud(this)">
        Aprobar y reembolsar
    </button>
</form>

    {{-- RECHAZAR --}}
    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rechazarModal">
        Rechazar
    </button>

</div>
@endif

{{-- Modal Rechazar --}}
<div class="modal fade" id="rechazarModal" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">

<form method="POST"
      action="{{ route('admin.postventa.rechazar', $solicitud) }}"
      onsubmit="return confirmRechazo(this)">
@csrf

<div class="modal-header">
    <h5 class="modal-title">Rechazar solicitud</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
    <label class="form-label fw-semibold">
        Motivo del rechazo
    </label>

    <textarea name="respuesta"
              class="form-control"
              rows="4"
              required
              placeholder="Ej. Producto fuera de política de devolución"></textarea>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
        Cancelar
    </button>

    <button type="submit" class="btn btn-danger">
        Rechazar solicitud
    </button>
</div>

</form>
</div>
</div>
</div>

<script>
function aprobarSolicitud(button) {
    if (!confirm('¿Aprobar solicitud y procesar el reembolso?')) {
        return;
    }

    const form = button.closest('form');

    // Deshabilitar botón
    button.disabled = true;

    // Mostrar loader
    button.innerHTML = `
        <span class="spinner-border spinner-border-sm me-2"></span>
        Procesando reembolso...
    `;

    // Enviar formulario manualmente
    form.submit();
}

function confirmRechazo(form) {
    showLoader(form, 'Enviando respuesta...');
    return true;
}

function showLoader(form, message) {
    const button = form.querySelector('button[type="submit"]');
    button.disabled = true;
    button.innerHTML = `
        <span class="spinner-border spinner-border-sm me-2"></span>
        ${message}
    `;
}
</script>

@endsection
