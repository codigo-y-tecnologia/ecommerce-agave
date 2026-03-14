<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #222; }
        h1   { font-size: 14px; margin-bottom: 4px; }
        .subtitle { color: #666; font-size: 9px; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th    { background: #1a1a2e; color: #fff; padding: 5px 4px; text-align: left; }
        td    { border-bottom: 1px solid #ddd; padding: 4px; vertical-align: top; }
        tr:nth-child(even) { background: #f8f8f8; }
        .badge-critical { color: #c0392b; font-weight: bold; }
        .badge-warning  { color: #e67e22; font-weight: bold; }
        .badge-info     { color: #2980b9; }
        .stats          { display: flex; gap: 20px; margin-bottom: 12px; }
        .stat-box       { border: 1px solid #ddd; padding: 6px 12px; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>🛡️ Reporte de Logs de Seguridad</h1>
    <div class="subtitle">Generado el {{ now()->format('d/m/Y H:i:s') }} — Total: {{ $logs->count() }} registros</div>

    <table style="width:auto; border:none; margin-bottom:12px;">
        <tr>
            <td style="padding:4px 16px 4px 0; border:none;"><strong>Alertas críticas sin resolver:</strong> {{ $stats['critical_unresolved'] }}</td>
            <td style="padding:4px 16px; border:none;"><strong>Eventos hoy:</strong> {{ $stats['today'] }}</td>
            <td style="padding:4px 0; border:none;"><strong>Total histórico:</strong> {{ $stats['total'] }}</td>
        </tr>
    </table>

    <table>
        <thead>
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
            </tr>
        </thead>
        <tbody>
            @foreach ($logs as $log)
            <tr>
                <td>{{ $log->id }}</td>
                <td class="badge-{{ $log->severity }}">{{ strtoupper($log->severity) }}</td>
                <td>{{ $log->category }}</td>
                <td>{{ $log->event_type }}</td>
                <td>{{ $log->description }}</td>
                <td>{{ $log->user?->vNombre ?? 'Anónimo' }}</td>
                <td>{{ $log->ip_address }}</td>
                <td>{{ $log->created_at->format('d/m/y H:i') }}</td>
                <td>{{ $log->is_resolved ? 'Resuelto' : 'Pendiente' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>