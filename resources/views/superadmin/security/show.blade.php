@extends('layouts.admins')
@section('title', 'Detalle del Log #' . $log->id)

@section('content')
<div class="d-flex justify-content-between mb-4">
    <h2>🔍 Detalle del evento #{{ $log->id }}</h2>
    <a href="{{ route('superadmin.security.index') }}" class="btn btn-outline-secondary">← Volver</a>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold">{{ $log->event_type }}</span>
                <span class="badge bg-{{ $log->severityBadgeClass() }} fs-6">
                    {{ $log->severityIcon() }} {{ ucfirst($log->severity) }}
                </span>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Descripción</dt>
                    <dd class="col-sm-9">{{ $log->description }}</dd>

                    <dt class="col-sm-3">Categoría</dt>
                    <dd class="col-sm-9"><span class="badge bg-secondary">{{ $log->category }}</span></dd>

                    <dt class="col-sm-3">IP</dt>
                    <dd class="col-sm-9"><code>{{ $log->ip_address }}</code></dd>

                    <dt class="col-sm-3">User Agent</dt>
                    <dd class="col-sm-9 small text-muted">{{ $log->user_agent ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Usuario</dt>
                    <dd class="col-sm-9">{{ $log->user?->vNombre ?? 'Anónimo' }}</dd>

                    <dt class="col-sm-3">Fecha</dt>
                    <dd class="col-sm-9">{{ $log->created_at->format('d/m/Y H:i:s') }}</dd>
                </dl>

                @if ($log->metadata)
                <hr>
                <h6 class="fw-semibold">Metadata</h6>
                <pre class="bg-light p-3 rounded small">{{ json_encode($log->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header fw-bold">Estado de resolución</div>
            <div class="card-body text-center">
                @if ($log->is_resolved)
                    <p class="text-success fw-bold">✅ Resuelto</p>
                    <p class="small text-muted">
                        Por {{ $log->resolvedBy?->vNombre }}<br>
                        {{ $log->resolved_at->format('d/m/Y H:i') }}
                    </p>
                @else
                    <p class="text-danger fw-bold">⏳ Sin resolver</p>
                    <form method="POST" action="{{ route('superadmin.security.resolve', $log) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-success w-100">
                            ✅ Marcar como resuelto
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection