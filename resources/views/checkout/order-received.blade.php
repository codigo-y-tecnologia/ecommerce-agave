@extends('layouts.app')

@section('title', 'Pedido recibido')

@section('content')
<div class="container mt-5">

    <h2 class="fw-bold text-center mb-4">🎉 ¡Pedido realizado con éxito!</h2>
    <p class="text-center text-muted">Gracias por tu compra. Aquí tienes los detalles de tu pedido.</p>

    <!-- ======================= -->
    <!-- INFORMACIÓN DEL PEDIDO -->
    <!-- ======================= -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Detalles del pedido</h4>

        <a href="{{ route('pedido.pdf', $pedido->id_pedido) }}"
           class="btn btn-outline-primary btn-sm">
            <i class="bi bi-file-earmark-pdf"></i> Descargar recibo
        </a>
    </div>

            <p><strong>Número de pedido:</strong> #{{ $pedido->id_pedido }}</p>
            <p><strong>Fecha:</strong> {{ $pedido->tFecha_pedido->format('d/m/Y H:i') }}</p>
            <p><strong>Método de pago:</strong> {{ strtoupper($payment_method) }}</p>
            <p><strong>Total:</strong>${{ number_format($totalFinal, 2) }}</p>
            @if ($pedido->tNota)
                <p><strong>Nota:</strong>{{ $pedido->tNota }}</p>
            @endif
        </div>
    </div>

    <!-- ======================= -->
    <!-- INFORMACIÓN DEL CLIENTE -->
    <!-- ======================= -->
    <div class="card mb-4">
        <div class="card-body">
            <h4 class="fw-bold mb-3">Datos del cliente</h4>

            <p><strong>Nombre:</strong> {{ $pedido->vNombre }} {{ $pedido->vApaterno }} {{ $pedido->vAmaterno }}</p>
            <p><strong>Correo:</strong> {{ $pedido->vEmail }}</p>

            <h5 class="fw-bold mt-4 mb-2">Dirección de envío</h5>
            <p>
                {{ $pedido->env_calle }} {{ $pedido->env_numero_exterior }}
                @if ($pedido->env_numero_interior)
                    Int. {{ $pedido->env_numero_interior }}
                @endif
                <br>
                {{ $pedido->env_colonia }}, {{ $pedido->env_ciudad }}, {{ $pedido->env_estado }}<br>
                CP {{ $pedido->env_codigo_postal }}<br>
                <strong>Tel:</strong> {{ $pedido->env_telefono_contacto }}
            </p>
        </div>
    </div>

    @if($pedido->vRFC)
    <div class="card mb-4">
        <div class="card-body">
            <h4 class="fw-bold mb-3">Datos de facturación</h4>

            <p><strong>RFC:</strong> {{ $pedido->vRFC }}</p>

            <p>
                {{ $pedido->fac_calle }} {{ $pedido->fac_numero_exterior }}
                @if($pedido->fac_numero_interior)
                    Int. {{ $pedido->fac_numero_interior }}
                @endif
                <br>

                {{ $pedido->fac_colonia }},
                {{ $pedido->fac_ciudad }},
                {{ $pedido->fac_estado }}<br>

                CP {{ $pedido->fac_codigo_postal }}<br>

                <strong>Tel:</strong> {{ $pedido->fac_telefono_contacto }}
            </p>
        </div>
    </div>
@endif

    <!-- ======================= -->
    <!-- PRODUCTOS -->
    <!-- ======================= -->
    <h4 class="fw-bold mb-3">Productos</h4>

    <table class="table table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th>Producto</th>
                <th class="text-center">Cant.</th>
                <th class="text-end">Precio (sin impuestos)</th>
                <th class="text-end">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pedido->detalles as $det)
                @php
                    $producto = $det->producto;
                    $precioBase = $producto->dPrecio_venta;
                    $subtotalProducto = $precioBase * $det->iCantidad;
                @endphp

                <tr>
                    <td>{{ $producto->vNombre }}</td>
                    <td class="text-center">{{ $det->iCantidad }}</td>
                    <td class="text-end">${{ number_format($precioBase, 2) }}</td>
                    <td class="text-end">${{ number_format($subtotalProducto, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- ======================= -->
    <!-- RESUMEN DE TOTALES -->
    <!-- ======================= -->
    <div class="card my-4">
        <div class="card-body">
            <h4 class="fw-bold mb-3">Resumen de totales</h4>

            <table class="table">
                <tbody>
                    <tr>
                        <th>Subtotal (sin impuestos):</th>
                        <td class="text-end">${{ number_format($subtotal, 2) }}</td>
                    </tr>

                    <!-- Impuestos por tipo -->
                    @foreach($impuestosPorTipo as $tipo => $monto)
                        <tr>
                            <th>{{ $tipo }}:</th>
                            <td class="text-end">${{ number_format($monto, 2) }}</td>
                        </tr>
                    @endforeach

                    <tr>
                        <th>Total Impuestos:</th>
                        <td class="text-end">${{ number_format($totalImpuestos, 2) }}</td>
                    </tr>

                    <tr>
                        <th>Subtotal con impuestos:</th>
                        <td class="text-end fw-bold">${{ number_format($subtotalConImpuestos, 2) }}</td>
                    </tr>

                    @if($descuento > 0)
                        <tr>
                            <th>Descuento 
                                @if($cupon) ({{ $cupon->vCodigo_cupon }}) @endif:
                            </th>
                            <td class="text-end text-success">
                                - ${{ number_format($descuento, 2) }}
                            </td>
                        </tr>
                    @endif

                    <tr>
                        <th>Envío:</th>
                        <td class="text-end">
                            @if($envio == 0)
                                <span class="text-success fw-bold">Gratis</span>
                            @else
                                ${{ number_format($envio, 2) }}
                            @endif
                        </td>
                    </tr>

                    <tr class="table-light">
                        <th class="fw-bold">Total Final:</th>
                        <td class="text-end fw-bold fs-5">
                            ${{ number_format($totalFinal, 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ======================= -->
    <!-- BOTÓN FINAL -->
    <!-- ======================= -->
    <div class="text-center mb-5">
        <a href="{{ route('home') }}" class="btn btn-success btn-lg px-5">
            Seguir comprando
        </a>
    </div>

</div>

@endsection
