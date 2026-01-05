<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Detalle de Venta #{{ $detalle->id_detalle_venta }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .title {
            font-size: 22px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 16px;
            color: #7f8c8d;
        }
        .info-box {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .info-label {
            font-weight: bold;
            color: #2c3e50;
            width: 150px;
            display: inline-block;
        }
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 30px;
        }
        .detail-table th {
            background-color: #2c3e50;
            color: white;
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        .detail-table td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        .detail-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .total-row {
            background-color: #2c3e50 !important;
            color: white;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #7f8c8d;
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo-text {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
        }
    </style>
</head>
<body>
    <div class="logo">
        <div class="logo-text">Ecommerce Agave</div>
        <div>Sistema de Ventas</div>
    </div>

    <div class="header">
        <div class="title">DETALLE DE VENTA</div>
        <div class="subtitle">Número: #{{ $detalle->id_detalle_venta }}</div>
        <div>Fecha de generación: {{ now()->format('d/m/Y H:i:s') }}</div>
    </div>

    <div class="info-box">
        <h3 style="margin-top: 0; color: #2c3e50;">Información General</h3>
        <div><span class="info-label">ID Detalle:</span> {{ $detalle->id_detalle_venta }}</div>
        <div><span class="info-label">ID Venta:</span> {{ $detalle->id_venta }}</div>
        <div><span class="info-label">ID Producto:</span> {{ $detalle->id_producto }}</div>
    </div>

    <h3 style="color: #2c3e50;">Detalles del Producto</h3>
    <table class="detail-table">
        <thead>
            <tr>
                <th>Descripción</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    Producto ID: {{ $detalle->id_producto }}
                    @if($detalle->producto)
                        <br><small>{{ $detalle->producto->nombre ?? 'N/A' }}</small>
                    @endif
                </td>
                <td>{{ $detalle->iCantidad }}</td>
                <td>${{ number_format($detalle->dPrecio_unitario, 2) }}</td>
                <td>${{ number_format($detalle->dSubtotal, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="3" style="text-align: right;"><strong>TOTAL:</strong></td>
                <td><strong>${{ number_format($detalle->dSubtotal, 2) }}</strong></td>
            </tr>
        </tbody>
    </table>

    @if($detalle->venta)
    <div class="info-box">
        <h3 style="margin-top: 0; color: #2c3e50;">Información de la Venta</h3>
        <div><span class="info-label">Venta ID:</span> {{ $detalle->venta->id_venta }}</div>
        @if($detalle->venta->fecha_venta)
            <div><span class="info-label">Fecha Venta:</span> {{ date('d/m/Y', strtotime($detalle->venta->fecha_venta)) }}</div>
        @endif
    </div>
    @endif

    <div class="footer">
        <div>Documento generado automáticamente por el Sistema Ecommerce Agave</div>
        <div>© {{ date('Y') }} - Todos los derechos reservados</div>
    </div>
</body>
</html>