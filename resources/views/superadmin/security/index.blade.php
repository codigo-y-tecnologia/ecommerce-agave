@extends('layouts.admins')
@section('title', 'Logs de Seguridad')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>🛡️ Logs de Seguridad</h2>
        <p class="text-muted mb-0">Monitoreo de eventos críticos del sistema</p>
    </div>
    <a href="{{ route('superadmin.security.export.pdf', request()->query()) }}"
       class="btn btn-outline-danger">
        📄 Exportar PDF
    </a>
</div>

{{-- Estadísticas --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-danger text-center py-3">
            <div class="fs-2 fw-bold text-danger">{{ $stats['critical_unresolved'] }}</div>
            <div class="small text-muted">Alertas críticas sin resolver</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-warning text-center py-3">
            <div class="fs-2 fw-bold text-warning">{{ $stats['failed_logins_today'] }}</div>
            <div class="small text-muted">Logins fallidos hoy</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-info text-center py-3">
            <div class="fs-2 fw-bold text-info">{{ $stats['today'] }}</div>
            <div class="small text-muted">Eventos hoy</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-secondary text-center py-3">
            <div class="fs-2 fw-bold text-secondary">{{ $stats['total'] }}</div>
            <div class="small text-muted">Total histórico</div>
        </div>
    </div>
</div>

{{-- Filtros --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Buscar</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="form-control form-control-sm" placeholder="IP, descripción, evento...">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Severidad</label>
                <select name="severity" class="form-select form-select-sm">
                    <option value="">Todas</option>
                    <option value="info"     {{ request('severity') === 'info'     ? 'selected' : '' }}>ℹ️ Info</option>
                    <option value="warning"  {{ request('severity') === 'warning'  ? 'selected' : '' }}>⚠️ Warning</option>
                    <option value="critical" {{ request('severity') === 'critical' ? 'selected' : '' }}>🔴 Critical</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Categoría</label>
                <select name="category" class="form-select form-select-sm">
                    <option value="">Todas</option>
                    <option value="auth"   {{ request('category') === 'auth'   ? 'selected' : '' }}>🔐 Auth</option>
                    <option value="config" {{ request('category') === 'config' ? 'selected' : '' }}>⚙️ Config</option>
                    <option value="admin"  {{ request('category') === 'admin'  ? 'selected' : '' }}>🧑‍💼 Admin</option>
                    <option value="orders" {{ request('category') === 'orders' ? 'selected' : '' }}>🛒 Pedidos</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Desde</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="form-control form-control-sm">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Hasta</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="form-control form-control-sm">
            </div>
            <div class="col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-primary w-100">Filtrar</button>
                <a href="{{ route('superadmin.security.index') }}" class="btn btn-sm btn-outline-secondary">✕</a>
            </div>
        </form>
    </div>
</div>

{{-- Tabla --}}
<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover table-sm mb-0 align-middle">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Severidad</th>
                    <th>Categoría</th>
                    <th>Evento</th>
                    <th>Descripción</th>
                    <th>Usuario</th>
                    <th>IP</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                <tr class="{{ $log->severity === 'critical' && !$log->is_resolved ? 'table-danger' : '' }}">
                    <td class="text-muted small">{{ $log->id }}</td>
                    <td>
                        <span class="badge bg-{{ $log->severityBadgeClass() }}">
                            {{ $log->severityIcon() }} {{ ucfirst($log->severity) }}
                        </span>
                    </td>
                    <td><span class="badge bg-secondary">{{ $log->category }}</span></td>
                    <td><code class="small">{{ $log->event_type }}</code></td>
                    <td class="small">{{ Str::limit($log->description, 60) }}</td>
                    <td class="small">{{ $log->user?->vNombre ?? 'Anónimo' }}</td>
                    <td><code class="small">{{ $log->ip_address }}</code></td>
                    <td class="small text-muted">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        @if ($log->is_resolved)
                            <span class="badge bg-success">✅ Resuelto</span>
                        @else
                            <span class="badge bg-danger">⏳ Pendiente</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('superadmin.security.show', $log) }}"
                           class="btn btn-xs btn-outline-secondary btn-sm py-0 px-2">
                            Ver
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center text-muted py-4">
                        No hay logs registrados con los filtros actuales.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <small class="text-muted">{{ $logs->total() }} registros encontrados</small>
        {{ $logs->links() }}
    </div>
</div>
@endsection