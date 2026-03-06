@extends('layout.app')
@section('content')

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

<!-- CSS de Cupones -->
<link rel="stylesheet" href="{{ asset('css/cupones-usados.css') }}">

<div class="container-fluid">
    <h1 style="margin-bottom: 30px; font-size: 2rem; color: #2d3748;">Panel de Control - Cupones Usados</h1>

    <!-- Estadísticas -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div class="stat-card" onclick="openDetailModal('cupones')">
            <i class="fas fa-external-link-alt"></i>
            <div style="font-size: 0.9rem; opacity: 0.9;">Total Cupones Usados</div>
            <div style="font-size: 2rem; font-weight: bold; margin-top: 10px;">{{ $cuponesUsados->count() }}</div>
        </div>
        <div class="stat-card" onclick="openDetailModal('descuentos')">
            <i class="fas fa-external-link-alt"></i>
            <div style="font-size: 0.9rem; opacity: 0.9;">Descuento Total Aplicado</div>
            <div style="font-size: 2rem; font-weight: bold; margin-top: 10px;">${{ number_format($cuponesUsados->sum('dDescuento_aplicado'), 2) }}</div>
        </div>
        <div class="stat-card" onclick="openDetailModal('usuarios')">
            <i class="fas fa-external-link-alt"></i>
            <div style="font-size: 0.9rem; opacity: 0.9;">Usuarios con Cupón</div>
            <div style="font-size: 2rem; font-weight: bold; margin-top: 10px;">{{ $cuponesUsados->unique('id_usuario')->count() }}</div>
        </div>
        <div class="stat-card" onclick="openDetailModal('tipos')">
            <i class="fas fa-external-link-alt"></i>
            <div style="font-size: 0.9rem; opacity: 0.9;">Cupones Distintos</div>
            <div style="font-size: 2rem; font-weight: bold; margin-top: 10px;">{{ $cuponesUsados->unique('id_cupon')->count() }}</div>
        </div>
    </div>

    @php
        // Agrupar por cupón
        $cuponesAgrupados = $cuponesUsados->groupBy('codigo_cupon')->map(function($grupo) {
            return [
                'codigo'           => $grupo->first()->codigo_cupon,
                'usos'             => $grupo->count(),
                'descuento_total'  => $grupo->sum('dDescuento_aplicado'),
                'usuarios_unicos'  => $grupo->unique('id_usuario')->count(),
            ];
        })->sortByDesc('usos')->values();

        // Agrupar por usuario
        $usuariosAgrupados = $cuponesUsados->groupBy('id_usuario')->map(function($grupo) {
            return [
                'id_usuario'       => $grupo->first()->id_usuario,
                'nombre'           => $grupo->first()->usuario_nombre ?? 'N/A',
                'apellido'         => $grupo->first()->usuario_apellido1 ?? '',
                'usos'             => $grupo->count(),
                'descuento_total'  => $grupo->sum('dDescuento_aplicado'),
            ];
        })->sortByDesc('usos')->values();

        // Agrupar por mes
        $usosPorMes = $cuponesUsados->groupBy(function($item) {
            return \Carbon\Carbon::parse($item->tFecha_uso)->format('Y-m');
        })->map(function($grupo, $key) {
            $mesesEspanol = [
                '01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril',
                '05'=>'Mayo','06'=>'Junio','07'=>'Julio','08'=>'Agosto',
                '09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre'
            ];
            [$año, $mes] = explode('-', $key);
            return [
                'nombre_mes'      => ($mesesEspanol[$mes] ?? $mes) . ' ' . $año,
                'sort_key'        => $key,
                'usos'            => $grupo->count(),
                'descuento_total' => $grupo->sum('dDescuento_aplicado'),
            ];
        })->sortBy('sort_key')->values();
    @endphp

    <!-- Gráficas -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; align-items: start;">
        <!-- Cupones más usados -->
        <div class="chart-container" id="cuponesChartContainer">
            <div class="chart-title">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-ticket-alt" style="color: #667eea;"></i>
                    <span>Cupones Más Usados</span>
                </div>
                <button class="collapse-btn" onclick="toggleChart('cupones')">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
            <div class="chart-content" id="cuponesChartContent">
                <canvas id="cuponesChart" style="max-height: 400px;"></canvas>
            </div>
        </div>

        <!-- Descuento por cupón -->
        <div class="chart-container" id="descuentoChartContainer">
            <div class="chart-title">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-dollar-sign" style="color: #48bb78;"></i>
                    <span>Descuento Total por Cupón</span>
                </div>
                <button class="collapse-btn" onclick="toggleChart('descuento')">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
            <div class="chart-content" id="descuentoChartContent">
                <canvas id="descuentoChart" style="max-height: 400px;"></canvas>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; align-items: start;">
        <!-- Usos por mes -->
        <div class="chart-container" id="mesesChartContainer">
            <div class="chart-title">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-calendar-alt" style="color: #8b5cf6;"></i>
                    <span>Usos por Mes</span>
                </div>
                <button class="collapse-btn" onclick="toggleChart('meses')">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
            <div class="chart-content" id="mesesChartContent">
                <canvas id="mesesChart" style="max-height: 400px;"></canvas>
            </div>
        </div>

        <!-- Top usuarios -->
        <div class="chart-container" id="usuariosChartContainer">
            <div class="chart-title">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-users" style="color: #f59e0b;"></i>
                    <span>Top Usuarios por Uso de Cupones</span>
                </div>
                <button class="collapse-btn" onclick="toggleChart('usuarios')">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
            <div class="chart-content" id="usuariosChartContent">
                <canvas id="usuariosChart" style="max-height: 400px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Estilos tabla estilo referencia -->
    <style>
        #tabla-section { background: white; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden; }
        #tabla-section .tabla-header { padding: 18px 20px; background: #fff; border-bottom: 2px solid #e2e8f0; }
        #tabla-section .tabla-header h3 { margin: 0; color: #2d3748; font-size: 1.2rem; font-weight: 700; }

        /* Encabezado oscuro como la imagen de referencia */
        #cuponesTable thead tr th {
            background-color: #2d3748 !important;
            color: #ffffff !important;
            font-weight: 600;
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 12px 10px;
            border: none !important;
            white-space: nowrap;
        }

        /* Filas alternas */
        #cuponesTable tbody tr:nth-child(odd)  { background-color: #ffffff; }
        #cuponesTable tbody tr:nth-child(even) { background-color: #f7f8fa; }
        #cuponesTable tbody tr:hover           { background-color: #eef2ff !important; }

        #cuponesTable tbody td {
            vertical-align: middle;
            font-size: 0.88rem;
            padding: 10px 10px;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
        }

        /* Badge ID cupón */
        .badge-id {
            background: #667eea;
            color: white;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 700;
        }

        /* Badge código cupón */
        .badge-codigo-cupon {
            background: #1e293b;
            color: #f8fafc;
            padding: 3px 10px;
            border-radius: 6px;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.05em;
        }

        /* Badge tipo — verde como referencia */
        .badge-tipo {
            background: #d1fae5;
            color: #065f46;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 600;
        }
        .badge-tipo.porcentaje {
            background: #dbeafe;
            color: #1e40af;
        }
        .badge-tipo.envio_gratis, .badge-tipo.envio {
            background: #fef9c3;
            color: #854d0e;
        }

        /* Monto descuento */
        .monto-descuento {
            font-weight: 700;
            color: #059669;
            font-size: 0.9rem;
        }
        .monto-base {
            color: #6b7280;
            font-size: 0.85rem;
        }

        /* ID Venta badge */
        .badge-venta {
            background: #f1f5f9;
            color: #334155;
            padding: 3px 10px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 0.82rem;
        }

        /* Fix colapso de gráficas */
        .chart-content { display: block; }
        .chart-container { transition: min-height 0.2s ease; overflow: hidden; height: auto !important; }

        /* Botones exportar — colores como referencia */
        .dt-buttons { margin-bottom: 10px; }
        .dt-button {
            border-radius: 6px !important;
            font-size: 0.82rem !important;
            font-weight: 600 !important;
            padding: 6px 14px !important;
            border: none !important;
            color: #fff !important;
            margin-right: 5px !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.15) !important;
            transition: opacity 0.2s !important;
        }
        .dt-button:hover { opacity: 0.85 !important; color: #fff !important; }

        /* Copiar — azul */
        .dt-buttons .buttons-copy  { background-color: #3b82f6 !important; }
        /* Excel — verde */
        .dt-buttons .buttons-excel { background-color: #22c55e !important; }
        /* PDF — rojo */
        .dt-buttons .buttons-pdf   { background-color: #ef4444 !important; }
        /* Imprimir — gris oscuro */
        .dt-buttons .buttons-print { background-color: #64748b !important; }

        /* Filtros tfoot */
        #cuponesTable tfoot th {
            background: #f8fafc;
            padding: 6px 8px;
        }
        #cuponesTable tfoot .column-filter {
            width: 100%;
            padding: 4px 8px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            font-size: 0.78rem;
            color: #374151;
        }
        #cuponesTable tfoot .column-filter:focus {
            outline: none;
            border-color: #667eea;
        }
    </style>

    <!-- Tabla principal -->
    <div id="tabla-section">
        <div class="tabla-header">
            <h3><i class="fas fa-table" style="color:#667eea; margin-right:8px;"></i> Registro de Cupones Usados</h3>
        </div>

        <table id="cuponesTable" class="table table-hover" style="width:100%; margin:0;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Código Cupón</th>
                    <th>Tipo</th>
                    <th>Descuento</th>
                    <th>Usuario</th>
                    <th>Email</th>
                    <th>ID Venta</th>
                    <th>Desc. Aplicado</th>
                    <th>Fecha de Uso</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th><input type="text" class="column-filter" placeholder="ID" /></th>
                    <th><input type="text" class="column-filter" placeholder="Código Cupón" /></th>
                    <th><input type="text" class="column-filter" placeholder="Tipo" /></th>
                    <th></th>
                    <th><input type="text" class="column-filter" placeholder="Usuario" /></th>
                    <th><input type="text" class="column-filter" placeholder="Email" /></th>
                    <th><input type="text" class="column-filter" placeholder="ID Venta" /></th>
                    <th></th>
                    <th><input type="text" class="column-filter" placeholder="Fecha" /></th>
                    <th></th>
                </tr>
            </tfoot>
            <tbody>
                @foreach($cuponesUsados as $cupon)
                @php
                    $tipoClass = match(strtolower($cupon->tipo_cupon ?? '')) {
                        'porcentaje'   => 'porcentaje',
                        'envio_gratis' => 'envio_gratis',
                        'envio'        => 'envio',
                        default        => ''
                    };
                @endphp
                <tr>
                    {{-- Columna 1: ID --}}
                    <td><span class="badge-id">{{ $cupon->id_cupon ?? 'N/A' }}</span></td>
                    {{-- Columna 2: Código Cupón --}}
                    <td><span class="badge-codigo-cupon">{{ $cupon->codigo_cupon ?? 'N/A' }}</span></td>
                    {{-- Columna 3: Tipo --}}
                    <td><span class="badge-tipo {{ $tipoClass }}">{{ ucfirst($cupon->tipo_cupon ?? 'N/A') }}</span></td>
                    {{-- Columna 4: Descuento base --}}
                    <td><span class="monto-base">${{ number_format($cupon->descuento_cupon ?? 0, 2) }}</span></td>
                    {{-- Columna 5: Usuario --}}
                    <td><strong style="color:#1e293b;">{{ $cupon->usuario_nombre ?? 'Invitado' }} {{ $cupon->usuario_apellido1 ?? '' }}</strong></td>
                    {{-- Columna 6: Email --}}
                    <td><small style="color:#6b7280;">{{ $cupon->usuario_email ?? 'N/A' }}</small></td>
                    {{-- Columna 7: ID Venta --}}
                    <td><span class="badge-venta">#{{ $cupon->id_venta }}</span></td>
                    {{-- Columna 8: Descuento aplicado --}}
                    <td style="text-align: right;">
                        <span class="monto-descuento">${{ number_format($cupon->dDescuento_aplicado ?? 0, 2) }}</span>
                    </td>
                    {{-- Columna 9: Fecha de uso --}}
                    <td data-order="{{ \Carbon\Carbon::parse($cupon->tFecha_uso)->format('Y-m-d H:i:s') }}">
                        <small style="color: #666;">{{ \Carbon\Carbon::parse($cupon->tFecha_uso)->format('d/m/Y H:i') }}</small>
                    </td>
                    {{-- Columna 10: Acciones --}}
                    <td style="text-align: center;">
                        <div class="d-flex gap-1 justify-content-center">
                            <a href="{{ route('cupones_usados.show', ['id' => $cupon->id_cupon . '-' . $cupon->id_venta]) }}" 
                               class="btn btn-sm btn-info" title="Ver detalle">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('cupones_usados.edit', ['id' => $cupon->id_cupon . '-' . $cupon->id_venta]) }}" 
                               class="btn btn-sm btn-warning" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('cupones_usados.destroy', ['id' => $cupon->id_cupon . '-' . $cupon->id_venta]) }}" 
                                  method="POST" class="form-delete" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-danger btn-eliminar" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal de confirmación de eliminación -->
<div id="confirmationModal" class="confirmation-modal">
    <div class="modal-content">
        <h3 style="margin: 0 0 15px 0; color: #2d3748;">¿Confirmar eliminación?</h3>
        <p style="color: #4b5563; margin-bottom: 20px;">¿Estás seguro de que deseas eliminar este registro de cupón usado? Esta acción no se puede deshacer.</p>
        <div class="modal-actions">
            <button id="confirmDeleteBtn" class="btn-confirm">Sí, eliminar</button>
            <button id="cancelDeleteBtn" class="btn-cancel">Cancelar</button>
        </div>
    </div>
</div>

<!-- MODAL: Cupones -->
<div id="modal-cupones" class="detail-modal">
    <div class="detail-modal-content">
        <div class="detail-modal-header">
            <h2>Detalle de Cupones Usados</h2>
            <button class="close-modal" onclick="closeDetailModal('cupones')">×</button>
        </div>
        <div class="detail-modal-body">
            <div class="stat-summary">
                <div class="stat-summary-item">
                    <div class="label">Total Usos</div>
                    <div class="value">{{ $cuponesUsados->count() }}</div>
                </div>
                <div class="stat-summary-item">
                    <div class="label">Cupones Distintos</div>
                    <div class="value">{{ $cuponesUsados->unique('id_cupon')->count() }}</div>
                </div>
                <div class="stat-summary-item">
                    <div class="label">Promedio de Descuento</div>
                    <div class="value">${{ number_format($cuponesUsados->avg('dDescuento_aplicado'), 2) }}</div>
                </div>
            </div>
            <h3>Cupones Más Usados</h3>
            <table class="detail-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Usos</th>
                        <th>Descuento Total</th>
                        <th>Usuarios Únicos</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cuponesAgrupados as $c)
                    <tr>
                        <td><strong>{{ $c['codigo'] }}</strong></td>
                        <td>{{ $c['usos'] }}</td>
                        <td>${{ number_format($c['descuento_total'], 2) }}</td>
                        <td>{{ $c['usuarios_unicos'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL: Descuentos -->
<div id="modal-descuentos" class="detail-modal">
    <div class="detail-modal-content">
        <div class="detail-modal-header">
            <h2>Detalle de Descuentos Aplicados</h2>
            <button class="close-modal" onclick="closeDetailModal('descuentos')">×</button>
        </div>
        <div class="detail-modal-body">
            @php
                $totalDesc = $cuponesUsados->sum('dDescuento_aplicado');
                $maxDesc   = $cuponesUsados->max('dDescuento_aplicado');
                $promDesc  = $cuponesUsados->avg('dDescuento_aplicado');
            @endphp
            <div class="stat-summary">
                <div class="stat-summary-item">
                    <div class="label">Descuento Total</div>
                    <div class="value">${{ number_format($totalDesc, 2) }}</div>
                </div>
                <div class="stat-summary-item">
                    <div class="label">Mayor Descuento</div>
                    <div class="value">${{ number_format($maxDesc, 2) }}</div>
                </div>
                <div class="stat-summary-item">
                    <div class="label">Promedio</div>
                    <div class="value">${{ number_format($promDesc, 2) }}</div>
                </div>
            </div>
            <h3>Desglose por Cupón</h3>
            <table class="detail-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Usos</th>
                        <th>Descuento Total</th>
                        <th>% del Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cuponesAgrupados as $c)
                    <tr>
                        <td>{{ $c['codigo'] }}</td>
                        <td>{{ $c['usos'] }}</td>
                        <td>${{ number_format($c['descuento_total'], 2) }}</td>
                        <td>{{ $totalDesc > 0 ? number_format(($c['descuento_total'] / $totalDesc) * 100, 1) : 0 }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL: Usuarios -->
<div id="modal-usuarios" class="detail-modal">
    <div class="detail-modal-content">
        <div class="detail-modal-header">
            <h2>Usuarios que Usaron Cupones</h2>
            <button class="close-modal" onclick="closeDetailModal('usuarios')">×</button>
        </div>
        <div class="detail-modal-body">
            <div class="stat-summary">
                <div class="stat-summary-item">
                    <div class="label">Usuarios Únicos</div>
                    <div class="value">{{ $usuariosAgrupados->count() }}</div>
                </div>
                <div class="stat-summary-item">
                    <div class="label">Promedio de Usos por Usuario</div>
                    <div class="value">{{ $usuariosAgrupados->count() > 0 ? number_format($cuponesUsados->count() / $usuariosAgrupados->count(), 1) : 0 }}</div>
                </div>
                <div class="stat-summary-item">
                    <div class="label">Mayor Ahorro por Usuario</div>
                    <div class="value">${{ number_format($usuariosAgrupados->max('descuento_total'), 2) }}</div>
                </div>
            </div>
            <h3>Top Usuarios</h3>
            <table class="detail-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Usos</th>
                        <th>Descuento Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usuariosAgrupados as $u)
                    <tr>
                        <td>{{ $u['id_usuario'] }}</td>
                        <td>{{ $u['nombre'] }} {{ $u['apellido'] }}</td>
                        <td>{{ $u['usos'] }}</td>
                        <td>${{ number_format($u['descuento_total'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL: Tipos -->
<div id="modal-tipos" class="detail-modal">
    <div class="detail-modal-content">
        <div class="detail-modal-header">
            <h2>Cupones Distintos Utilizados</h2>
            <button class="close-modal" onclick="closeDetailModal('tipos')">×</button>
        </div>
        <div class="detail-modal-body">
            <div class="stat-summary">
                <div class="stat-summary-item">
                    <div class="label">Cupones Distintos</div>
                    <div class="value">{{ $cuponesAgrupados->count() }}</div>
                </div>
                <div class="stat-summary-item">
                    <div class="label">Más Usado</div>
                    <div class="value">{{ $cuponesAgrupados->first()['codigo'] ?? 'N/A' }}</div>
                </div>
                <div class="stat-summary-item">
                    <div class="label">Mayor Descuento</div>
                    <div class="value">${{ number_format($cuponesAgrupados->max('descuento_total'), 2) }}</div>
                </div>
            </div>
            <h3>Listado de Cupones</h3>
            <table class="detail-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Usos</th>
                        <th>Usuarios Únicos</th>
                        <th>Descuento Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cuponesAgrupados as $c)
                    <tr>
                        <td><strong>{{ $c['codigo'] }}</strong></td>
                        <td>{{ $c['usos'] }}</td>
                        <td>{{ $c['usuarios_unicos'] }}</td>
                        <td>${{ number_format($c['descuento_total'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
// ✅ FIX: Variable de colores unificada con nombre consistente en español
const coloresOriginales = [
    'rgba(102, 126, 234, 0.8)', 'rgba(118, 75, 162, 0.8)',
    'rgba(237, 100, 166, 0.8)', 'rgba(255, 154, 158, 0.8)',
    'rgba(250, 208, 196, 0.8)', 'rgba(52, 211, 153, 0.8)',
    'rgba(251, 191, 36, 0.8)', 'rgba(248, 113, 113, 0.8)'
];

const cuponesData    = @json($cuponesAgrupados);
const usosPorMesData = @json($usosPorMes);
const usuariosData   = @json($usuariosAgrupados->take(10)->values());

window.charts = {};

// Gráfica: Cupones más usados
window.charts.cupones = new Chart(
    document.getElementById('cuponesChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: cuponesData.map(c => c.codigo),
        datasets: [{ label: 'Usos', data: cuponesData.map(c => c.usos),
            backgroundColor: coloresOriginales,
            borderColor: coloresOriginales.map(c => c.replace('0.8','1')),
            borderWidth: 2, borderRadius: 8 }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false },
            tooltip: { callbacks: { label: ctx => ctx.parsed.y + ' usos' } }
        },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});

// Gráfica: Descuento por cupón (doughnut)
window.charts.descuento = new Chart(
    document.getElementById('descuentoChart').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: cuponesData.map(c => c.codigo),
        datasets: [{ data: cuponesData.map(c => c.descuento_total),
            backgroundColor: coloresOriginales, borderWidth: 3, borderColor: '#fff' }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: {
            legend: { position: 'right', labels: { boxWidth: 15, padding: 15, font: { size: 11 } } },
            tooltip: { callbacks: { label: ctx => ctx.label + ': $' + ctx.parsed.toFixed(2) } }
        }
    }
});

// Gráfica: Usos por mes (línea)
window.charts.meses = new Chart(
    document.getElementById('mesesChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: usosPorMesData.map(m => m.nombre_mes),
        datasets: [{
            label: 'Usos',
            data: usosPorMesData.map(m => m.usos),
            backgroundColor: 'rgba(74, 246, 36, 0.2)', borderColor: 'rgb(18, 240, 47)',
            borderWidth: 3, fill: true, tension: 0.4,
            pointBackgroundColor: 'rgb(144, 207, 171)', pointBorderColor: '#fff',
            pointBorderWidth: 2, pointRadius: 6, pointHoverRadius: 8
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: true, position: 'top' },
            tooltip: { callbacks: { label: ctx => {
                const m = usosPorMesData[ctx.dataIndex];
                return ['Usos: ' + ctx.parsed.y, 'Descuento: $' + m.descuento_total.toFixed(2)];
            }}}
        },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: 'rgba(0,0,0,0.05)' } },
            x: { grid: { color: 'rgba(0,0,0,0.05)' } }
        }
    }
});

// Gráfica: Top usuarios
window.charts.usuarios = new Chart(
    document.getElementById('usuariosChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: usuariosData.map(u => u.nombre + ' ' + u.apellido),
        datasets: [{ label: 'Cupones Usados', data: usuariosData.map(u => u.usos),
            backgroundColor: 'rgba(245, 158, 11, 0.8)', borderColor: 'rgba(245, 158, 11, 1)',
            borderWidth: 2, borderRadius: 8 }]
    },
    options: {
        indexAxis: 'y',
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false },
            tooltip: { callbacks: { label: ctx => ctx.parsed.x + ' usos' } }
        },
        scales: { x: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});

$(document).ready(function() {
    var table = $('#cuponesTable').DataTable({
        language: {
            "emptyTable": "No hay datos disponibles en la tabla",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
            "infoEmpty": "Mostrando 0 a 0 de 0 registros",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "thousands": ",",
            "lengthMenu": "Mostrar _MENU_ registros",
            "loadingRecords": "Cargando...",
            "processing": "Procesando...",
            "search": "Buscar:",
            "zeroRecords": "No se encontraron registros coincidentes",
            "paginate": {
                "first": "Primero", "last": "Último",
                "next": "Siguiente", "previous": "Anterior"
            },
            "buttons": { "copy": "Copiar", "excel": "Excel", "pdf": "PDF", "print": "Imprimir" }
        },
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
        responsive: true,
        order: [[0, 'desc']],
        dom: 'Bfrtip',
        buttons: [
            { extend: 'copy',  text: '<i class="fas fa-copy"></i> Copiar',   className: 'dt-button', exportOptions: { columns: ':not(:last-child)' } },
            { extend: 'excel', text: '<i class="fas fa-file-excel"></i> Excel', className: 'dt-button', exportOptions: { columns: ':not(:last-child)' } },
            { extend: 'pdf',   text: '<i class="fas fa-file-pdf"></i> PDF',   className: 'dt-button', orientation: 'landscape', pageSize: 'A4', exportOptions: { columns: ':not(:last-child)' } },
            { extend: 'print', text: '<i class="fas fa-print"></i> Imprimir', className: 'dt-button', exportOptions: { columns: ':not(:last-child)' } }
        ],
        columnDefs: [
            { targets: [7], className: 'dt-right' },
            { targets: [9], orderable: false }
        ]
    });

    $('#cuponesTable tfoot th').each(function(i) {
        var column = table.column(i);
        var input = $(this).find('input, select');
        input.on('keyup change', function() {
            if (column.search() !== this.value) {
                column.search(this.value).draw();
            }
        });
    });

    // Modal de eliminación
    var formToDelete = null;
    $(document).on('click', '.btn-eliminar', function() {
        formToDelete = $(this).closest('form')[0];
        document.getElementById('confirmationModal').classList.add('show');
        document.body.style.overflow = 'hidden';
    });

    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (formToDelete) formToDelete.submit();
    });

    document.getElementById('cancelDeleteBtn').addEventListener('click', function() {
        document.getElementById('confirmationModal').classList.remove('show');
        document.body.style.overflow = 'auto';
        formToDelete = null;
    });
});

// Toggle gráficas
function toggleChart(chartType) {
    const container = document.getElementById(chartType + 'ChartContainer');
    const content   = document.getElementById(chartType + 'ChartContent');
    const button    = container.querySelector('.collapse-btn i');
    const isHidden  = content.style.display === 'none';

    if (isHidden) {
        // Abrir — mostrar contenido y expandir contenedor
        content.style.display = 'block';
        container.style.minHeight = '';
        container.style.paddingBottom = '';
        button.className = 'fas fa-chevron-down';
        setTimeout(() => {
            const chart = window.charts?.[chartType];
            if (chart) { chart.resize(); chart.update(); }
        }, 50);
    } else {
        // Cerrar — ocultar contenido y encoger contenedor al mínimo (solo título)
        content.style.display = 'none';
        container.style.minHeight = '0';
        container.style.paddingBottom = '0';
        button.className = 'fas fa-chevron-up';
    }
}

// Modales de detalle
function openDetailModal(type) {
    const modal = document.getElementById('modal-' + type);
    if (modal) { modal.classList.add('show'); document.body.style.overflow = 'hidden'; }
}
function closeDetailModal(type) {
    const modal = document.getElementById('modal-' + type);
    if (modal) { modal.classList.remove('show'); document.body.style.overflow = 'auto'; }
}
document.querySelectorAll('.detail-modal').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) { this.classList.remove('show'); document.body.style.overflow = 'auto'; }
    });
});
</script>

@endsection