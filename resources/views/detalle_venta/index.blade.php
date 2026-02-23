@extends('layout.app')
@section('content')

<!-- Font Awesome para los iconos -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

<!-- CSS de Ventas -->
<link rel="stylesheet" href="{{ asset('css/detalle-ventas.css') }}">

<div class="container-fluid">
    <h1 style="margin-bottom: 30px; font-size: 2rem; color: #2d3748;">Panel de Control - Ventas</h1>
    
    <!-- Estadísticas de Ventas -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div class="stat-card" onclick="openDetailModal('usuarios')">
            <i class="fas fa-external-link-alt"></i>
            <div style="font-size: 0.9rem; opacity: 0.9;">Usuarios Registrados</div>
            <div style="font-size: 2rem; font-weight: bold; margin-top: 10px;">{{ \DB::table('tbl_usuarios')->count() }}</div>
        </div>
        <div class="stat-card" onclick="openDetailModal('detalles')">
            <i class="fas fa-external-link-alt"></i>
            <div style="font-size: 0.9rem; opacity: 0.9;">Total de Detalles</div>
            <div style="font-size: 2rem; font-weight: bold; margin-top: 10px;">{{ $detallesVenta->count() }}</div>
        </div>
        <div class="stat-card" onclick="openDetailModal('productos')">
            <i class="fas fa-external-link-alt"></i>
            <div style="font-size: 0.9rem; opacity: 0.9;">Productos Vendidos</div>
            <div style="font-size: 2rem; font-weight: bold; margin-top: 10px;">{{ $detallesVenta->sum('iCantidad') }}</div>
        </div>
        <div class="stat-card" onclick="openDetailModal('ingresos')">
            <i class="fas fa-external-link-alt"></i>
            <div style="font-size: 0.9rem; opacity: 0.9;">Ingresos Totales</div>
            <div style="font-size: 2rem; font-weight: bold; margin-top: 10px;">${{ number_format($detallesVenta->sum('dSubtotal'), 2) }}</div>
        </div>
    </div>

    @php
        // Función para obtener estado REAL de pago
        function obtenerEstadoReal($detalle) {
            if (isset($detalle->eEstado)) {
                return $detalle->eEstado;
            }
            return 'completada';
        }
        
        function obtenerClaseEstado($estado) {
            switch(strtolower($estado)) {
                case 'completada':
                case 'pagado':
                    return 'estado-completada';
                case 'pendiente':
                    return 'estado-pendiente';
                case 'cancelada':
                case 'cancelado':
                    return 'estado-cancelada';
                case 'en proceso':
                case 'proceso':
                    return 'estado-proceso';
                default:
                    return 'estado-completada';
            }
        }
        
        function obtenerTextoEstado($estado) {
            switch(strtolower($estado)) {
                case 'completada': return 'Completada';
                case 'pendiente': return 'Pendiente';
                case 'cancelada': return 'Cancelada';
                case 'en proceso': return 'En Proceso';
                default: return ucfirst($estado);
            }
        }
        
        // Obtener todos los usuarios
        $usuarios = \DB::table('tbl_usuarios')->get();
        
        // Agrupar productos
        $todosProductos = $detallesVenta->groupBy('nombre_producto')->map(function($grupo) {
            return [
                'nombre' => $grupo->first()->nombre_producto,
                'cantidad_total' => $grupo->sum('iCantidad'),
                'ventas_totales' => $grupo->sum('dSubtotal'),
                'num_ventas' => $grupo->count()
            ];
        })->sortByDesc('cantidad_total')->values();

        // Agrupar ventas por estado
        $ventasPorEstado = $detallesVenta->groupBy('vEstado')->map(function($grupo) {
            return [
                'estado' => $grupo->first()->vEstado,
                'total_ventas' => $grupo->sum('dSubtotal'),
                'cantidad_productos' => $grupo->sum('iCantidad'),
                'num_transacciones' => $grupo->count()
            ];
        })->sortByDesc('total_ventas')->values();
        
        // Agrupar ventas por mes
        $ventasPorMes = $detallesVenta->groupBy(function($item) {
            return $item->año_venta . '-' . str_pad($item->mes_venta, 2, '0', STR_PAD_LEFT);
        })->map(function($grupo, $key) {
            $parts = explode('-', $key);
            $año = $parts[0];
            $mes = (int)$parts[1];
            
            $mesesEspanol = [
                1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
            ];
            
            return [
                'año' => $año,
                'mes' => $mes,
                'nombre_mes' => $mesesEspanol[$mes] . ' ' . $año,
                'total_ventas' => $grupo->sum('dSubtotal'),
                'cantidad_productos' => $grupo->sum('iCantidad'),
                'num_ventas' => $grupo->count()
            ];
        })->sortBy(function($item) {
            return $item['año'] . str_pad($item['mes'], 2, '0', STR_PAD_LEFT);
        })->values();
        
        // Calcular rangos para gráfica de estados
        if($ventasPorEstado->count() > 0) {
            $ventasTotales = $ventasPorEstado->pluck('total_ventas');
            $maxVenta = $ventasTotales->max();
            $minVenta = $ventasTotales->min();
            $rango = $maxVenta - $minVenta;
            $umbralAlto = $minVenta + ($rango * 0.66);
            $umbralMedio = $minVenta + ($rango * 0.33);
        }
    @endphp

    <!-- Gráficas -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
        <!-- Gráfica de Productos -->
        <div class="chart-container" id="productosChartContainer">
            <div class="chart-title">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-chart-bar" style="color: #667eea;"></i>
                    <span>Productos Más Vendidos (Unidades)</span>
                </div>
                <button class="collapse-btn" onclick="toggleChart('productos')">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
            <div class="chart-content" id="productosChartContent">
                <canvas id="productosChart" style="max-height: 400px;"></canvas>
            </div>
        </div>

        <!-- Gráfica de Ingresos por Producto -->
        <div class="chart-container" id="ingresosChartContainer">
            <div class="chart-title">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-dollar-sign" style="color: #48bb78;"></i>
                    <span>Ingresos por Producto</span>
                </div>
                <button class="collapse-btn" onclick="toggleChart('ingresos')">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
            <div class="chart-content" id="ingresosChartContent">
                <canvas id="ingresosChart" style="max-height: 400px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Segunda fila de gráficas -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
        <!-- Gráfica de Ventas por Estado -->
        <div class="chart-container" id="estadosChartContainer">
            <div class="chart-title">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-map-marked-alt" style="color: #f59e0b;"></i>
                    <span>Ventas por Estado</span>
                </div>
                <button class="collapse-btn" onclick="toggleChart('estados')">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
            <div class="chart-content" id="estadosChartContent">
                <canvas id="estadosChart" style="max-height: 400px;"></canvas>
            </div>
        </div>

        <!-- Gráfica de Ventas por Mes -->
        <div class="chart-container" id="mesesChartContainer">
            <div class="chart-title">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-calendar-alt" style="color: #8b5cf6;"></i>
                    <span>Ventas por Mes</span>
                </div>
                <button class="collapse-btn" onclick="toggleChart('meses')">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
            <div class="chart-content" id="mesesChartContent">
                <canvas id="mesesChart" style="max-height: 400px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Tabla de Detalles con DataTables -->
    <div id="tabla-section" style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="padding: 20px; border-bottom: 2px solid #e2e8f0;">
            <h3 style="margin: 0; color: #2d3748; font-size: 1.3rem;">
                <i class="fas fa-table"></i> Detalle de Ventas
            </h3>
        </div>
        
        <table id="ventasTable" class="table table-striped table-hover" style="width:100%">
            <thead>
                <tr>
                    <th>ID Venta</th>
                    <th>Cliente</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Ciudad</th>
                    <th>Estado</th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unit.</th>
                    <th>Subtotal</th>
                    <th>Total Venta</th>
                    <th>Fecha</th>
                    <th>Estado de Pago</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th><input type="text" class="column-filter" placeholder="ID Venta" /></th>
                    <th><input type="text" class="column-filter" placeholder="Cliente" /></th>
                    <th><input type="text" class="column-filter" placeholder="Email" /></th>
                    <th><input type="text" class="column-filter" placeholder="Teléfono" /></th>
                    <th><input type="text" class="column-filter" placeholder="Ciudad" /></th>
                    <th><input type="text" class="column-filter" placeholder="Estado" /></th>
                    <th><input type="text" class="column-filter" placeholder="Producto" /></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th><input type="text" class="column-filter" placeholder="Fecha" /></th>
                    <th>
                        <select class="column-filter">
                            <option value="">Todos</option>
                            <option value="completada">Completada</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="cancelada">Cancelada</option>
                            <option value="en proceso">En Proceso</option>
                        </select>
                    </th>
                </tr>
            </tfoot>
            <tbody>
                @foreach($detallesVenta as $detalle)
                @php
                    $estadoPago = obtenerEstadoReal($detalle);
                    $claseEstado = obtenerClaseEstado($estadoPago);
                    $textoEstado = obtenerTextoEstado($estadoPago);
                @endphp
                <tr>
                    <td><strong>#{{ $detalle->id_venta }}</strong></td>
                    <td><strong>{{ $detalle->usuario_nombre }} {{ $detalle->usuario_apellido1 }} {{ $detalle->usuario_apellido2 }}</strong></td>
                    <td><small>{{ $detalle->usuario_email }}</small></td>
                    <td><small>{{ $detalle->usuario_telefono }}</small></td>
                    <td>{{ $detalle->vCiudad }}</td>
                    <td style="text-transform: capitalize;">{{ ucfirst(strtolower($detalle->vEstado)) }}</td>
                    <td>
                        <div style="max-width: 300px;">
                            {{ $detalle->nombre_producto }}
                            @if($detalle->num_productos > 1)
                                <br><small style="color: #666; font-style: italic;">({{ $detalle->num_productos }} productos)</small>
                            @endif
                        </div>
                    </td>
                    <td style="text-align: center;"><strong>{{ $detalle->iCantidad }}</strong></td>
                    <td style="text-align: right;" data-order="{{ $detalle->dPrecio_unitario }}">${{ number_format($detalle->dPrecio_unitario, 2) }}</td>
                    <td style="text-align: right;" data-order="{{ $detalle->dSubtotal }}">${{ number_format($detalle->dSubtotal, 2) }}</td>
                    <td style="text-align: right; background-color: #f0f0f0; font-weight: bold;" data-order="{{ $detalle->total_venta }}">${{ number_format($detalle->total_venta, 2) }}</td>
                    <td data-order="{{ isset($detalle->fecha_venta) ? \Carbon\Carbon::parse($detalle->fecha_venta)->format('Y-m-d') : '' }}">
                        <small style="color: #666;">
                            @if(isset($detalle->fecha_venta))
                                {{ \Carbon\Carbon::parse($detalle->fecha_venta)->format('d/m/Y') }}
                            @elseif(isset($detalle->año_venta) && isset($detalle->mes_venta) && isset($detalle->dia_venta))
                                {{ str_pad($detalle->dia_venta, 2, '0', STR_PAD_LEFT) }}/{{ str_pad($detalle->mes_venta, 2, '0', STR_PAD_LEFT) }}/{{ $detalle->año_venta }}
                            @else
                                N/A
                            @endif
                        </small>
                    </td>
                    <td>
                        <span class="estado-pago {{ $claseEstado }}">{{ $textoEstado }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal de confirmación -->
<div id="confirmationModal" class="confirmation-modal">
    <div class="modal-content">
        <h3 style="margin: 0 0 15px 0; color: #2d3748;">¿Confirmar eliminación?</h3>
        <p style="color: #4b5563; margin-bottom: 20px;">¿Estás seguro de que deseas eliminar este detalle de venta? Esta acción no se puede deshacer.</p>
        <div class="modal-actions">
            <button id="confirmDeleteBtn" class="btn-confirm">Sí, eliminar</button>
            <button id="cancelDeleteBtn" class="btn-cancel">Cancelar</button>
        </div>
    </div>
</div>

<!-- MODALES DE DETALLES -->
<div id="modal-usuarios" class="detail-modal">
    <div class="detail-modal-content">
        <div class="detail-modal-header">
            <h2>Detalles de Usuarios Registrados</h2>
            <button class="close-modal" onclick="closeDetailModal('usuarios')">×</button>
        </div>
        <div class="detail-modal-body">
            @php
                $todosLosUsuarios = \DB::table('tbl_usuarios')
                    ->select('id_usuario', 'vNombre', 'vApaterno', 'vAmaterno', 'vEmail', 'eRol', 'tFecha_registro')
                    ->orderBy('id_usuario', 'asc')
                    ->get();
            @endphp
            
            <div class="stat-summary">
                <div class="stat-summary-item">
                    <div class="label">Total de Usuarios</div>
                    <div class="value">{{ $todosLosUsuarios->count() }}</div>
                </div>
                <div class="stat-summary-item">
                    <div class="label">Clientes</div>
                    <div class="value">{{ $todosLosUsuarios->where('eRol', 'cliente')->count() }}</div>
                </div>
                <div class="stat-summary-item">
                    <div class="label">Administradores</div>
                    <div class="value">{{ $todosLosUsuarios->whereIn('eRol', ['admin', 'superadmin'])->count() }}</div>
                </div>
            </div>
            
            <h3>Listado de Usuarios</h3>
            @if($todosLosUsuarios->count() > 0)
            <table class="detail-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre Completo</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Fecha de Registro</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($todosLosUsuarios as $usuario)
                    <tr>
                        <td>{{ $usuario->id_usuario }}</td>
                        <td><strong>{{ $usuario->vNombre }} {{ $usuario->vApaterno }} {{ $usuario->vAmaterno }}</strong></td>
                        <td>{{ $usuario->vEmail }}</td>
                        <td>
                            <span style="padding: 5px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 600; background-color: {{ $usuario->eRol == 'cliente' ? '#dbeafe' : '#fef3c7' }}; color: {{ $usuario->eRol == 'cliente' ? '#1e40af' : '#92400e' }};">
                                @if($usuario->eRol == 'cliente') Cliente
                                @elseif($usuario->eRol == 'admin') Admin
                                @elseif($usuario->eRol == 'superadmin') Super Admin
                                @else {{ ucfirst($usuario->eRol) }}
                                @endif
                            </span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($usuario->tFecha_registro)->format('d/m/Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="no-data">
                <i class="fas fa-users" style="font-size: 3rem; margin-bottom: 15px;"></i>
                <p>No hay usuarios registrados</p>
            </div>
            @endif
        </div>
    </div>
</div>

<div id="modal-detalles" class="detail-modal">
    <div class="detail-modal-content">
        <div class="detail-modal-header">
            <h2>Detalles de Ventas</h2>
            <button class="close-modal" onclick="closeDetailModal('detalles')">×</button>
        </div>
        <div class="detail-modal-body">
            <div class="stat-summary">
                <div class="stat-summary-item">
                    <div class="label">Total de Detalles</div>
                    <div class="value">{{ $detallesVenta->count() }}</div>
                </div>
                <div class="stat-summary-item">
                    <div class="label">Ventas Únicas</div>
                    <div class="value">{{ $detallesVenta->unique('id_venta')->count() }}</div>
                </div>
                <div class="stat-summary-item">
                    <div class="label">Productos Diferentes</div>
                    <div class="value">{{ $detallesVenta->unique('nombre_producto')->count() }}</div>
                </div>
            </div>
            
            <h3>Resumen de Ventas</h3>
            <table class="detail-table">
                <thead>
                    <tr>
                        <th>ID Venta</th>
                        <th>Cliente</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($detallesVenta as $detalle)
                    <tr>
                        <td>#{{ $detalle->id_venta }}</td>
                        <td>{{ $detalle->usuario_nombre }} {{ $detalle->usuario_apellido1 }}</td>
                        <td>{{ $detalle->nombre_producto }}</td>
                        <td>{{ $detalle->iCantidad }}</td>
                        <td>${{ number_format($detalle->dSubtotal, 2) }}</td>
                        <td>
                            @if(isset($detalle->fecha_venta))
                                {{ \Carbon\Carbon::parse($detalle->fecha_venta)->format('d/m/Y') }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            @php
                                $estado = obtenerEstadoReal($detalle);
                                $clase = obtenerClaseEstado($estado);
                                $texto = obtenerTextoEstado($estado);
                            @endphp
                            <span class="estado-pago {{ $clase }}">{{ $texto }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="modal-productos" class="detail-modal">
    <div class="detail-modal-content">
        <div class="detail-modal-header">
            <h2>Detalles de Productos Vendidos</h2>
            <button class="close-modal" onclick="closeDetailModal('productos')">×</button>
        </div>
        <div class="detail-modal-body">
            <div class="stat-summary">
                <div class="stat-summary-item">
                    <div class="label">Total Productos</div>
                    <div class="value">{{ $detallesVenta->sum('iCantidad') }}</div>
                </div>
                <div class="stat-summary-item">
                    <div class="label">Productos Diferentes</div>
                    <div class="value">{{ $todosProductos->count() }}</div>
                </div>
                <div class="stat-summary-item">
                    <div class="label">Promedio por Venta</div>
                    <div class="value">{{ number_format($detallesVenta->avg('iCantidad'), 1) }}</div>
                </div>
            </div>
            
            <h3>Productos Más Vendidos</h3>
            <table class="detail-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad Total</th>
                        <th>Ventas Totales</th>
                        <th>Número de Ventas</th>
                        <th>Promedio por Venta</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($todosProductos as $producto)
                    <tr>
                        <td>{{ $producto['nombre'] }}</td>
                        <td>{{ $producto['cantidad_total'] }}</td>
                        <td>${{ number_format($producto['ventas_totales'], 2) }}</td>
                        <td>{{ $producto['num_ventas'] }}</td>
                        <td>{{ number_format($producto['cantidad_total'] / $producto['num_ventas'], 1) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="modal-ingresos" class="detail-modal">
    <div class="detail-modal-content">
        <div class="detail-modal-header">
            <h2>Detalles de Ingresos Totales</h2>
            <button class="close-modal" onclick="closeDetailModal('ingresos')">×</button>
        </div>
        <div class="detail-modal-body">
            @php
                $totalIngresos = $detallesVenta->sum('dSubtotal');
                $promedioVenta = $detallesVenta->count() > 0 ? $totalIngresos / $detallesVenta->unique('id_venta')->count() : 0;
                $ventaMasAlta = $detallesVenta->max('dSubtotal');
            @endphp
            
            <div class="stat-summary">
                <div class="stat-summary-item">
                    <div class="label">Ingresos Totales</div>
                    <div class="value">${{ number_format($totalIngresos, 2) }}</div>
                </div>
                <div class="stat-summary-item">
                    <div class="label">Promedio por Venta</div>
                    <div class="value">${{ number_format($promedioVenta, 2) }}</div>
                </div>
                <div class="stat-summary-item">
                    <div class="label">Venta Más Alta</div>
                    <div class="value">${{ number_format($ventaMasAlta, 2) }}</div>
                </div>
            </div>
            
            <h3>Desglose de Ingresos</h3>
            <table class="detail-table">
                <thead>
                    <tr>
                        <th>ID Venta</th>
                        <th>Cliente</th>
                        <th>Productos</th>
                        <th>Cantidad Total</th>
                        <th>Subtotal</th>
                        <th>Total Venta</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($detallesVenta->groupBy('id_venta') as $idVenta => $detalles)
                    @php
                        $primeraVenta = $detalles->first();
                        $cantidadTotal = $detalles->sum('iCantidad');
                        $subtotalTotal = $detalles->sum('dSubtotal');
                    @endphp
                    <tr>
                        <td>#{{ $idVenta }}</td>
                        <td>{{ $primeraVenta->usuario_nombre }} {{ $primeraVenta->usuario_apellido1 }}</td>
                        <td>{{ $detalles->count() }} producto(s)</td>
                        <td>{{ $cantidadTotal }}</td>
                        <td>${{ number_format($subtotalTotal, 2) }}</td>
                        <td>${{ number_format($primeraVenta->total_venta, 2) }}</td>
                        <td>
                            @if(isset($primeraVenta->fecha_venta))
                                {{ \Carbon\Carbon::parse($primeraVenta->fecha_venta)->format('d/m/Y') }}
                            @else
                                N/A
                            @endif
                        </td>
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

<!-- Botones de exportación -->
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
// Inicializar DataTable
$(document).ready(function() {
    var table = $('#ventasTable').DataTable({
        language: {
            "decimal": "",
            "emptyTable": "No hay datos disponibles en la tabla",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
            "infoEmpty": "Mostrando 0 a 0 de 0 registros",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": "Mostrar _MENU_ registros",
            "loadingRecords": "Cargando...",
            "processing": "Procesando...",
            "search": "Buscar:",
            "zeroRecords": "No se encontraron registros coincidentes",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            },
            "aria": {
                "sortAscending": ": activar para ordenar la columna ascendente",
                "sortDescending": ": activar para ordenar la columna descendente"
            },
            "buttons": {
                "copy": "Copiar",
                "excel": "Excel",
                "pdf": "PDF",
                "print": "Imprimir"
            }
        },
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
        responsive: true,
        order: [[0, 'desc']],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'copy',
                text: '<i class="fas fa-copy"></i> Copiar',
                className: 'dt-button',
                exportOptions: { columns: ':visible' }
            },
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'dt-button',
                exportOptions: { columns: ':visible' }
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'dt-button',
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: { columns: ':visible' }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Imprimir',
                className: 'dt-button',
                exportOptions: { columns: ':visible' }
            }
        ],
        columnDefs: [
            { targets: [8, 9, 10], className: 'dt-right' },
            { targets: 7, className: 'dt-center' },
            { targets: 12, className: 'dt-center' }
        ],
        drawCallback: function() {
            var api = this.api();
            var subtotal = api.column(9, {page:'current'}).data()
                .reduce(function(a, b) {
                    var val = parseFloat($(b).attr('data-order')) || 0;
                    return a + val;
                }, 0);
            $('#totalSubtotal').text('$' + subtotal.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
        }
    });

    $('#ventasTable tfoot th').each(function(i) {
        var column = table.column(i);
        var input = $(this).find('input, select');
        input.on('keyup change', function() {
            if (column.search() !== this.value) {
                column.search(this.value).draw();
            }
        });
    });
});

// Función para colapsar/expandir gráficas
function toggleChart(chartType) {
    const container = document.getElementById(chartType + 'ChartContainer');
    const button = container.querySelector('.collapse-btn i');
    
    if (container.classList.contains('chart-collapsed')) {
        container.classList.remove('chart-collapsed');
        button.classList.remove('fa-chevron-up');
        button.classList.add('fa-chevron-down');
        setTimeout(() => {
            if (window.charts && window.charts[chartType]) {
                window.charts[chartType].resize();
            }
        }, 300);
    } else {
        container.classList.add('chart-collapsed');
        button.classList.remove('fa-chevron-down');
        button.classList.add('fa-chevron-up');
    }
}

// Configuración de colores
const colorsOriginales = [
    'rgba(102, 126, 234, 0.8)', 'rgba(118, 75, 162, 0.8)',
    'rgba(237, 100, 166, 0.8)', 'rgba(255, 154, 158, 0.8)',
    'rgba(250, 208, 196, 0.8)', 'rgba(52, 211, 153, 0.8)',
    'rgba(251, 191, 36, 0.8)', 'rgba(248, 113, 113, 0.8)'
];

const productosData = @json($todosProductos);
const ventasPorMesData = @json($ventasPorMes);
const estadosData = @json($ventasPorEstado);

window.charts = {};

// Gráfica de Productos
const ctxProductos = document.getElementById('productosChart').getContext('2d');
window.charts.productos = new Chart(ctxProductos, {
    type: 'bar',
    data: {
        labels: productosData.map(p => p.nombre.length > 20 ? p.nombre.substring(0, 20) + '...' : p.nombre),
        datasets: [{
            label: 'Unidades Vendidas',
            data: productosData.map(p => p.cantidad_total),
            backgroundColor: colorsOriginales,
            borderColor: colorsOriginales.map(c => c.replace('0.8', '1')),
            borderWidth: 2,
            borderRadius: 8,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.parsed.y + ' unidades vendidas';
                    }
                }
            }
        },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } }
        }
    }
});

// Gráfica de Ingresos
const ctxIngresos = document.getElementById('ingresosChart').getContext('2d');
window.charts.ingresos = new Chart(ctxIngresos, {
    type: 'doughnut',
    data: {
        labels: productosData.map(p => p.nombre),
        datasets: [{
            data: productosData.map(p => p.ventas_totales),
            backgroundColor: colorsOriginales,
            borderWidth: 3,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'right',
                labels: { boxWidth: 15, padding: 15, font: { size: 11 } }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.label + ': $' + context.parsed.toFixed(2);
                    }
                }
            }
        }
    }
});

// Gráfica de Ventas por Estado
const ctxEstados = document.getElementById('estadosChart').getContext('2d');
const ventasValues = estadosData.map(e => e.total_ventas);
const maxVenta = Math.max(...ventasValues);
const minVenta = Math.min(...ventasValues);
const rango = maxVenta - minVenta;
const umbralAlto = minVenta + (rango * 0.66);
const umbralMedio = minVenta + (rango * 0.33);

const coloresEstados = estadosData.map(estado => {
    if (estado.total_ventas >= umbralAlto) return 'rgba(34, 197, 94, 0.8)';
    else if (estado.total_ventas >= umbralMedio) return 'rgba(245, 158, 11, 0.8)';
    else return 'rgba(239, 68, 68, 0.8)';
});

window.charts.estados = new Chart(ctxEstados, {
    type: 'bar',
    data: {
        labels: estadosData.map(e => e.estado),
        datasets: [{
            label: 'Ventas Totales ($)',
            data: estadosData.map(e => e.total_ventas),
            backgroundColor: coloresEstados,
            borderColor: coloresEstados.map(c => c.replace('0.8', '1')),
            borderWidth: 2,
            borderRadius: 8,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: true, position: 'top' },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const estado = estadosData[context.dataIndex];
                        const nivel = estado.total_ventas >= umbralAlto ? 'Alta' : 
                                     estado.total_ventas >= umbralMedio ? 'Media' : 'Baja';
                        return [
                            'Nivel: ' + nivel,
                            'Ventas: $' + context.parsed.y.toFixed(2),
                            'Productos: ' + estado.cantidad_productos + ' unidades',
                            'Transacciones: ' + estado.num_transacciones
                        ];
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value.toFixed(2);
                    }
                }
            }
        }
    }
});

// Gráfica de Ventas por Mes
const ctxMeses = document.getElementById('mesesChart').getContext('2d');
window.charts.meses = new Chart(ctxMeses, {
    type: 'line',
    data: {
        labels: ventasPorMesData.map(m => m.nombre_mes),
        datasets: [{
            label: 'Ventas Totales ($)',
            data: ventasPorMesData.map(m => m.total_ventas),
            backgroundColor: 'rgba(74, 246, 36, 0.2)',
            borderColor: 'rgb(18, 240, 47)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: 'rgb(144, 207, 171)',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 6,
            pointHoverRadius: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: true, position: 'top' },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const mes = ventasPorMesData[context.dataIndex];
                        return [
                            'Ventas: $' + context.parsed.y.toFixed(2),
                            'Productos: ' + mes.cantidad_productos + ' unidades',
                            'Transacciones: ' + mes.num_ventas
                        ];
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value.toFixed(2);
                    }
                },
                grid: { color: 'rgba(0, 0, 0, 0.05)' }
            },
            x: {
                grid: { color: 'rgba(0, 0, 0, 0.05)' }
            }
        }
    }
});

// Funciones para modales
function openDetailModal(type) {
    const modal = document.getElementById('modal-' + type);
    if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
}

function closeDetailModal(type) {
    const modal = document.getElementById('modal-' + type);
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = 'auto';
    }
}

document.querySelectorAll('.detail-modal').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('show');
            document.body.style.overflow = 'auto';
        }
    });
});
</script>

@endsection