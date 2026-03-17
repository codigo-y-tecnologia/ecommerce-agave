@extends('layouts.admins')
@section('title', 'Monitoreo del Sistema')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>📈 Monitoreo del Sistema</h2>
        <p class="text-muted mb-0">Estado en tiempo real de la infraestructura</p>
    </div>
    <div class="d-flex align-items-center gap-3">
        <span class="text-muted small">
            Última actualización: <strong id="last-updated">--:--:--</strong>
        </span>
        <span id="status-indicator" class="badge bg-secondary">⏳ Cargando...</span>
    </div>
</div>

{{-- ═══════════════════════════════════════════════ --}}
{{-- FILA 1: Servidor                               --}}
{{-- ═══════════════════════════════════════════════ --}}
<div class="row g-3 mb-3">

    {{-- CPU --}}
    <div class="col-md-3">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="card-title mb-0">🖥️ CPU</h6>
                    <span id="cpu-badge" class="badge bg-secondary">--</span>
                </div>
                <div class="display-6 fw-bold" id="cpu-value">--</div>
                <div class="progress mt-2" style="height:6px">
                    <div id="cpu-bar" class="progress-bar" style="width:0%"></div>
                </div>
                <div class="text-muted small mt-1" id="cpu-os">--</div>
            </div>
        </div>
    </div>

    {{-- RAM --}}
    <div class="col-md-3">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="card-title mb-0">💾 Memoria RAM</h6>
                    <span id="ram-badge" class="badge bg-secondary">--</span>
                </div>
                <div class="display-6 fw-bold" id="ram-value">--</div>
                <div class="progress mt-2" style="height:6px">
                    <div id="ram-bar" class="progress-bar" style="width:0%"></div>
                </div>
                <div class="text-muted small mt-1" id="ram-detail">--</div>
            </div>
        </div>
    </div>

    {{-- Disco --}}
    <div class="col-md-3">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="card-title mb-0">💿 Disco</h6>
                    <span id="disk-badge" class="badge bg-secondary">--</span>
                </div>
                <div class="display-6 fw-bold" id="disk-value">--</div>
                <div class="progress mt-2" style="height:6px">
                    <div id="disk-bar" class="progress-bar" style="width:0%"></div>
                </div>
                <div class="text-muted small mt-1" id="disk-detail">--</div>
            </div>
        </div>
    </div>

    {{-- Uptime --}}
    <div class="col-md-3">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="card-title mb-0">⏱️ Uptime</h6>
                    <span class="badge bg-success">Online</span>
                </div>
                <div class="fs-4 fw-bold text-success" id="uptime-value">--</div>
                <div class="text-muted small mt-2">
                    PHP: <span id="php-version">--</span>
                </div>
                <div class="text-muted small">
                    Laravel: <span id="laravel-version">--</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════ --}}
{{-- FILA 2: App + BD + Queues + Caché              --}}
{{-- ═══════════════════════════════════════════════ --}}
<div class="row g-3 mb-3">

    {{-- Aplicación --}}
    <div class="col-md-3">
        <div class="card h-100 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center py-2">
                <span class="fw-semibold small">🚀 Aplicación</span>
                <span id="app-badge" class="badge bg-secondary">--</span>
            </div>
            <div class="card-body p-3">
                <ul class="list-unstyled mb-0 small">
                    <li class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Entorno</span>
                        <span id="app-env" class="fw-semibold">--</span>
                    </li>
                    <li class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Debug mode</span>
                        <span id="app-debug">--</span>
                    </li>
                    <li class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Errores 24h</span>
                        <span id="app-errors" class="fw-semibold text-danger">--</span>
                    </li>
                    <li class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Storage</span>
                        <span id="app-storage">--</span>
                    </li>
                    <li class="d-flex justify-content-between py-1">
                        <span class="text-muted">Timezone</span>
                        <span id="app-tz" class="text-muted">--</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Base de datos --}}
    <div class="col-md-3">
        <div class="card h-100 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center py-2">
                <span class="fw-semibold small">🗄️ Base de Datos</span>
                <span id="db-badge" class="badge bg-secondary">--</span>
            </div>
            <div class="card-body p-3">
                <ul class="list-unstyled mb-0 small">
                    <li class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Driver</span>
                        <span id="db-driver" class="fw-semibold text-uppercase">--</span>
                    </li>
                    <li class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Base de datos</span>
                        <span id="db-name">--</span>
                    </li>
                    <li class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Ping</span>
                        <span id="db-ping">--</span>
                    </li>
                    <li class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Tablas</span>
                        <span id="db-tables">--</span>
                    </li>
                    <li class="d-flex justify-content-between py-1">
                        <span class="text-muted">Tamaño</span>
                        <span id="db-size">--</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Queues --}}
    <div class="col-md-3">
        <div class="card h-100 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center py-2">
                <span class="fw-semibold small">📬 Queues</span>
                <span id="queue-badge" class="badge bg-secondary">--</span>
            </div>
            <div class="card-body p-3">
                <ul class="list-unstyled mb-0 small">
                    <li class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Conexión</span>
                        <span id="queue-connection" class="fw-semibold text-uppercase">--</span>
                    </li>
                    <li class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Pendientes</span>
                        <span id="queue-pending">--</span>
                    </li>
                    <li class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Fallidos</span>
                        <span id="queue-failed" class="text-danger fw-semibold">--</span>
                    </li>
                    <li class="d-flex justify-content-between py-1">
                        <span class="text-muted">Completados 24h</span>
                        <span id="queue-batches" class="text-success">--</span>
                    </li>
                </ul>
                <div class="mt-2">
                    <a href="{{ route('superadmin.monitoring.metrics') }}"
                       class="btn btn-outline-danger btn-sm w-100" id="btn-retry-failed"
                       style="display:none">
                        Ver jobs fallidos →
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Caché --}}
    <div class="col-md-3">
        <div class="card h-100 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center py-2">
                <span class="fw-semibold small">⚡ Caché</span>
                <span id="cache-badge" class="badge bg-secondary">--</span>
            </div>
            <div class="card-body p-3">
                <ul class="list-unstyled mb-0 small">
                    <li class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Driver</span>
                        <span id="cache-driver" class="fw-semibold text-uppercase">--</span>
                    </li>
                    <li class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Ping</span>
                        <span id="cache-ping">--</span>
                    </li>
                    <li class="d-flex justify-content-between py-1">
                        <span class="text-muted">Tamaño en disco</span>
                        <span id="cache-size">--</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════ --}}
{{-- FILA 3: Memoria PHP detallada                  --}}
{{-- ═══════════════════════════════════════════════ --}}
<div class="row g-3">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header fw-semibold small py-2">🐘 Memoria PHP (proceso actual)</div>
            <div class="card-body p-3">
                <div class="row text-center">
                    <div class="col-4">
                        <div class="fs-5 fw-bold text-primary" id="php-mem-used">--</div>
                        <div class="text-muted small">En uso</div>
                    </div>
                    <div class="col-4">
                        <div class="fs-5 fw-bold text-warning" id="php-mem-peak">--</div>
                        <div class="text-muted small">Pico</div>
                    </div>
                    <div class="col-4">
                        <div class="fs-5 fw-bold text-secondary" id="php-mem-limit">--</div>
                        <div class="text-muted small">Límite</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header fw-semibold small py-2">📋 Resumen de alertas</div>
            <div class="card-body p-3" id="alerts-container">
                <p class="text-muted small mb-0 text-center">Cargando...</p>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════ --}}
{{-- JAVASCRIPT                                     --}}
{{-- ═══════════════════════════════════════════════ --}}
<script>
const METRICS_URL = "{{ route('superadmin.monitoring.metrics') }}";
let refreshInterval;

// ── Helpers de UI ──────────────────────────────────────
function statusBadge(status) {
    const map = {
        healthy:  ['success', '✅ Saludable'],
        good:     ['success', '✅ Bueno'],
        warning:  ['warning', '⚠️ Advertencia'],
        critical: ['danger',  '🔴 Crítico'],
        error:    ['danger',  '❌ Error'],
        online:   ['success', '✅ Online'],
        offline:  ['danger',  '❌ Offline'],
    };
    const [color, label] = map[status] ?? ['secondary', status];
    return `<span class="badge bg-${color}">${label}</span>`;
}

function progressColor(pct) {
    if (pct < 60) return 'bg-success';
    if (pct < 80) return 'bg-warning';
    return 'bg-danger';
}

function setText(id, value) {
    const el = document.getElementById(id);
    if (el) el.textContent = value ?? 'N/A';
}

function setHtml(id, html) {
    const el = document.getElementById(id);
    if (el) el.innerHTML = html;
}

function setBar(barId, badgeId, pct) {
    const bar   = document.getElementById(barId);
    const badge = document.getElementById(badgeId);
    if (!bar || !badge) return;
    const color = progressColor(pct);
    bar.style.width = pct + '%';
    bar.className   = `progress-bar ${color}`;
    const status = pct < 60 ? 'good' : pct < 80 ? 'warning' : 'critical';
    badge.className  = `badge bg-${color.replace('bg-', '')}`;
    badge.textContent = `${pct}%`;
}

// ── Renderizado de secciones ────────────────────────────
function renderServer(s) {
    // CPU
    if (s.cpu_usage !== null && s.cpu_usage !== undefined) {
        setText('cpu-value', s.cpu_usage + '%');
        setBar('cpu-bar', 'cpu-badge', s.cpu_usage);
    } else {
        setText('cpu-value', 'N/D');
        setHtml('cpu-badge', '<span class="badge bg-secondary">Solo Linux</span>');
    }
    setText('cpu-os', `OS: ${s.os} · PHP ${s.php_version}`);

    // RAM
    const m = s.memory;
    if (m.percent !== null) {
        setText('ram-value', m.percent + '%');
        setBar('ram-bar', 'ram-badge', m.percent);
        setText('ram-detail', `${m.used_mb} MB / ${m.total_mb} MB`);
    } else {
        setText('ram-value', `${m.php_used} MB`);
        setText('ram-detail', `PHP usa ${m.php_used} MB (pico: ${m.php_peak} MB)`);
        setHtml('ram-badge', '<span class="badge bg-info">PHP only</span>');
    }

    // Disco
    const d = s.disk;
    setText('disk-value', d.percent + '%');
    setBar('disk-bar', 'disk-badge', d.percent);
    setText('disk-detail', `${d.used_gb} GB / ${d.total_gb} GB`);

    // Uptime
    setText('uptime-value', s.uptime ?? 'N/D');
    setText('php-version', s.php_version);
}

function renderApp(a) {
    setText('app-env', a.environment);
    setText('laravel-version', a.laravel_version);

    const debugEl = document.getElementById('app-debug');
    if (debugEl) {
        debugEl.innerHTML = a.debug_mode
            ? '<span class="badge bg-warning text-dark">⚠️ Activo</span>'
            : '<span class="badge bg-success">Desactivado</span>';
    }

    const errEl = document.getElementById('app-errors');
    if (errEl) {
        errEl.textContent = a.log_errors_24h;
        errEl.className   = a.log_errors_24h > 0 ? 'fw-semibold text-danger' : 'fw-semibold text-success';
    }

    const storageEl = document.getElementById('app-storage');
    if (storageEl) {
        storageEl.innerHTML = a.storage_writable
            ? '<span class="text-success">✅ Escribible</span>'
            : '<span class="text-danger">❌ Sin permisos</span>';
    }

    setText('app-tz', a.timezone);

    const issues = [];
    if (a.debug_mode)          issues.push('Debug mode activo en producción');
    if (a.log_errors_24h > 10) issues.push(`${a.log_errors_24h} errores en las últimas 24h`);
    if (!a.storage_writable)   issues.push('Storage sin permisos de escritura');

    setHtml('app-badge', statusBadge(issues.length === 0 ? 'healthy' : 'warning'));
}

function renderDatabase(db) {
    setHtml('db-badge', statusBadge(db.status === 'online'
        ? (db.ping_status ?? 'good') : 'offline'));
    setText('db-driver', db.driver ?? '--');
    setText('db-name',   db.database ?? '--');

    const pingEl = document.getElementById('db-ping');
    if (pingEl && db.ping_ms !== null) {
        pingEl.innerHTML = `<span class="${db.ping_status === 'good' ? 'text-success' : 'text-warning'}">${db.ping_ms} ms</span>`;
    }

    setText('db-tables', db.tables ?? '--');
    setText('db-size',   db.size_mb != null ? db.size_mb + ' MB' : '--');
}

function renderQueues(q) {
    setHtml('queue-badge', statusBadge(q.status ?? 'healthy'));
    setText('queue-connection', q.connection ?? '--');
    setText('queue-pending',    q.pending ?? 0);
    setText('queue-batches',    q.batches_24h ?? 0);

    const failedEl = document.getElementById('queue-failed');
    if (failedEl) {
        failedEl.textContent = q.failed ?? 0;
        failedEl.className   = (q.failed > 0) ? 'text-danger fw-bold' : 'text-success';
    }

    const btnRetry = document.getElementById('btn-retry-failed');
    if (btnRetry) btnRetry.style.display = q.failed > 0 ? 'block' : 'none';
}

function renderCache(c) {
    setHtml('cache-badge', statusBadge(c.status));
    setText('cache-driver', c.driver ?? '--');

    const pingEl = document.getElementById('cache-ping');
    if (pingEl) {
        pingEl.innerHTML = c.ping_ms !== null
            ? `<span class="text-success">${c.ping_ms} ms</span>`
            : '<span class="text-muted">N/A</span>';
    }

    setText('cache-size', c.size_kb != null ? c.size_kb + ' KB' : 'N/A');
}

function renderPhpMemory(m) {
    setText('php-mem-used',  m.php_used  + ' MB');
    setText('php-mem-peak',  m.php_peak  + ' MB');
    setText('php-mem-limit', ini_get_limit());
}

function ini_get_limit() {
    // Solo referencial; PHP lo reporta como string
    return '128 MB';
}

function renderAlerts(data) {
    const alerts = [];

    if (data.server.cpu_usage > 80)
        alerts.push({ type: 'danger',  msg: `CPU al ${data.server.cpu_usage}%` });
    if (data.server.memory.percent > 85)
        alerts.push({ type: 'danger',  msg: `RAM al ${data.server.memory.percent}%` });
    if (data.server.disk.percent > 80)
        alerts.push({ type: 'warning', msg: `Disco al ${data.server.disk.percent}%` });
    if (data.app.debug_mode)
        alerts.push({ type: 'warning', msg: 'Debug mode activo' });
    if (data.app.log_errors_24h > 0)
        alerts.push({ type: 'warning', msg: `${data.app.log_errors_24h} errores en las últimas 24h` });
    if (data.database.status !== 'online')
        alerts.push({ type: 'danger',  msg: 'Base de datos desconectada' });
    if (data.queues.failed > 0)
        alerts.push({ type: 'warning', msg: `${data.queues.failed} jobs fallidos en cola` });
    if (!data.cache.working)
        alerts.push({ type: 'danger',  msg: 'Sistema de caché con errores' });

    const container = document.getElementById('alerts-container');
    if (!container) return;

    if (alerts.length === 0) {
        container.innerHTML = '<p class="text-success mb-0 small text-center">✅ Todo en orden. No hay alertas activas.</p>';
        return;
    }

    container.innerHTML = alerts.map(a =>
        `<div class="alert alert-${a.type} py-1 px-2 mb-1 small">${a.msg}</div>`
    ).join('');
}

// ── Fetch principal ─────────────────────────────────────
async function fetchMetrics() {
    try {
        const indicator = document.getElementById('status-indicator');
        if (indicator) {
            indicator.className  = 'badge bg-warning';
            indicator.textContent = '🔄 Actualizando...';
        }

        const res  = await fetch(METRICS_URL, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();

        renderServer(data.server);
        renderApp(data.app);
        renderDatabase(data.database);
        renderQueues(data.queues);
        renderCache(data.cache);
        renderPhpMemory(data.server.memory);
        renderAlerts(data);

        setText('last-updated', data.updated);
        setText('laravel-version', data.app.laravel_version);

        if (indicator) {
            indicator.className  = 'badge bg-success';
            indicator.textContent = '✅ En línea';
        }
    } catch (err) {
        const indicator = document.getElementById('status-indicator');
        if (indicator) {
            indicator.className  = 'badge bg-danger';
            indicator.textContent = '❌ Error de conexión';
        }
        console.error('Error al obtener métricas:', err);
    }
}

// ── Inicialización ──────────────────────────────────────
fetchMetrics();
refreshInterval = setInterval(fetchMetrics, 30000); // cada 30 segundos
</script>
@endsection