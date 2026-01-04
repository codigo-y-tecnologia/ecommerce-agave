@extends('layouts.app')

@section('title', 'Detalle del pedido')

@section('content')

<a href="{{ route('pedidos.index') }}" class="btn btn-link mb-3">
    ← Volver a mis compras
</a>

<h2 class="fw-bold mb-3">
    Pedido #{{ $pedido->id_pedido }}
</h2>

<div class="d-flex justify-content-between align-items-center mb-4">
    <p class="mb-0">
        Estado:
        <span class="badge bg-{{ estadoPedidoColor($pedido->eEstado) }}">
            {{ estadoPedidoTexto($pedido->eEstado) }}
        </span>
    </p>

    <div class="d-flex gap-2">

    @php
$estadoEnvio = optional($pedido->envio)->eEstado;
$solicitud = $pedido->ultimaSolicitudPostventa;
$bloquearPostventa = $solicitud
    && in_array($solicitud->eEstado, ['pendiente', 'rechazada'])
    && (
        ($solicitud->eTipo === 'cancelacion' && $estadoEnvio === \App\Models\Envio::ESTADO_PENDIENTE)
        || ($solicitud->eTipo === 'devolucion' && $estadoEnvio === \App\Models\Envio::ESTADO_ENTREGADO)
    );
@endphp

    {{-- CANCELAR: solo si aún NO ha sido enviado --}}
    @if(!$bloquearPostventa && $pedido->eEstado === 'pagado' && $estadoEnvio === \App\Models\Envio::ESTADO_PENDIENTE)
<button class="btn btn-outline-danger btn-sm"
    onclick="solicitarPostventa(
        '{{ route('postventa.cancelar', $pedido) }}',
        'Cancelar compra',
        'Describe el motivo de la cancelación'
    )">
    Cancelar compra
</button>
@endif

    {{-- DEVOLVER: solo si ya fue entregado --}}
    @if(!$bloquearPostventa && $estadoEnvio === \App\Models\Envio::ESTADO_ENTREGADO && $pedido->eEstado !== 'devuelto')
<button class="btn btn-outline-warning btn-sm"
    onclick="solicitarPostventa(
        '{{ route('postventa.devolver', $pedido) }}',
        'Solicitar devolución',
        'Describe el motivo de la devolución'
    )">
    Devolver productos
</button>
@endif
</div>

    @if($pedido->venta && $pedido->venta->eEstado === 'completada')
    <a href="{{ route('pedidos.factura', $pedido->id_pedido) }}"
       class="btn btn-outline-secondary btn-sm">
        Descargar factura PDF
    </a>
@endif
</div>

@php
$solicitud = $pedido->ultimaSolicitudPostventa;
@endphp

@if($solicitud)
<div class="alert 
    @if($solicitud->eEstado === 'pendiente') alert-warning
    @elseif($solicitud->eEstado === 'rechazada' &&
    !(
        $solicitud->eTipo === 'cancelacion' &&
        optional($pedido->envio)->eEstado === \App\Models\Envio::ESTADO_ENTREGADO
    )) alert-danger 
    @elseif($solicitud->eEstado === 'reembolsada') alert-success
    @endif
">

@if($solicitud->eEstado === 'pendiente')
Tu solicitud de {{ $solicitud->eTipo }} está en revisión.
@endif

@if($solicitud->eEstado === 'rechazada' &&
    !(
        $solicitud->eTipo === 'cancelacion' &&
        optional($pedido->envio)->eEstado === \App\Models\Envio::ESTADO_ENTREGADO
    ))
Tu solicitud de {{ $solicitud->eTipo }} fue rechazada.<br>
<strong>Motivo:</strong> {{ $solicitud->tRespuesta_admin }}
@endif

@if($solicitud->eEstado === 'reembolsada')
Tu solicitud fue aprobada y el reembolso fue procesado.
@endif
</div>
@endif

{{-- ===============================
     PRODUCTOS
================================ --}}
<div class="card mb-4 shadow-sm">
    <div class="card-header fw-bold">
        Productos
    </div>

    <div class="card-body p-0">
        @if($pedido->detalles && $pedido->detalles->count())
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                <tr>
                    <th></th>
                    <th>Producto</th>
                    <th class="text-center">Cantidad</th>
                    <th class="text-end">Precio unitario</th>
                    <th class="text-end">Total</th>
                </tr>
                </thead>
                <tbody>
@foreach ($pedido->detalles as $det)

    @php
        $producto = optional($det->producto);
        $imagenes = $producto->imagenes ?? [];
    @endphp

    <tr>
        {{-- IMAGEN --}}
        <td style="width:90px">
            @if(!empty($imagenes) && count($imagenes) > 0)
                <img src="{{ $imagenes[0] }}"
                     alt="{{ $producto->vNombre }}"
                     class="rounded"
                     style="width:70px; height:70px; object-fit:cover;">
            @else
                <div class="bg-light d-flex align-items-center justify-content-center rounded"
                     style="width:70px; height:70px;">
                    <span class="text-muted small">Sin imagen</span>
                </div>
            @endif
        </td>

        {{-- NOMBRE --}}
        <td>
            @if($producto->id_producto)
                <a href="{{ route('productos.show.public', $producto->id_producto) }}"
                   class="fw-semibold text-decoration-none">
                    {{ $producto->vNombre }}
                </a>
            @else
                <span class="text-muted">Producto no disponible</span>
            @endif
        </td>

        {{-- CANTIDAD --}}
        <td class="text-center">
            {{ $det->iCantidad }}
        </td>

        {{-- PRECIO UNITARIO --}}
        <td class="text-end">
            ${{ number_format($det->dPrecio_unitario, 2) }}
        </td>

        {{-- TOTAL --}}
        <td class="text-end fw-semibold">
            ${{ number_format($det->iCantidad * $det->dPrecio_unitario, 2) }}
        </td>
    </tr>
@endforeach
</tbody>
            </table>
        @else
            <p class="text-muted p-3 mb-0">
                No hay productos registrados en este pedido.
            </p>
        @endif
    </div>
</div>

{{-- ===============================
     RESUMEN DEL PEDIDO
================================ --}}
<div class="row mb-4">

    {{-- DIRECCIÓN --}}
    <div class="col-md-6">
        <div class="card shadow-sm mb-3">
            <div class="card-header fw-bold">
                Dirección de envío
            </div>
            <div class="card-body">
                @if($pedido->direccion)
                    <p class="mb-1">
                        {{ $pedido->direccion->vCalle }}
                        {{ $pedido->direccion->vNumero ?? '' }}
                    </p>
                    <p class="mb-1">
                        {{ $pedido->direccion->vColonia }},
                        {{ $pedido->direccion->vCiudad }}
                    </p>
                    <p class="mb-0">
                        {{ $pedido->direccion->vEstado }},
                        {{ $pedido->direccion->vCodigo_postal }}
                    </p>
                @else
                    <p class="text-muted mb-0">
                        Dirección no disponible
                    </p>
                @endif
            </div>
        </div>

        {{-- PAGO --}}
        <div class="card shadow-sm">
            <div class="card-header fw-bold">
                Pago
            </div>
            <div class="card-body">
                @if($pedido->pago)
                    <p class="mb-1">
                        Método:
                        <strong>{{ strtoupper($pedido->pago->eMetodo_pago) }}</strong>
                    </p>

                    <p class="mb-1">
                        Referencia:
                        <span class="text-muted">
                            {{ $pedido->pago->vReferencia ?? '—' }}
                        </span>
                    </p>

                    <p class="mb-0">
                        Fecha:
                        @if($pedido->pago->tFecha_pago)
                            {{ optional($pedido->pago->tFecha_pago)->format('d/m/Y H:i') }}
                        @else
                            <span class="text-muted">Pendiente</span>
                        @endif
                    </p>
                @else
                    <p class="text-muted mb-0">
                        Información de pago no disponible
                    </p>
                @endif
            </div>
        </div>
    </div>

    {{-- ===============================
         TOTALES
    ================================ --}}
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header fw-bold">
                Resumen
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal</span>
                    <span>${{ number_format($subtotal ?? 0, 2) }}</span>
                </div>

                {{-- ENVÍO --}}
                <div class="d-flex justify-content-between mb-2">
                <span>Envío</span>
                <span>
                    @php
                        $envio = optional($pedido->venta)->dCosto_envio ?? 0;
                    @endphp

                    @if($envio == 0)
                        <span class="text-success fw-semibold">Gratis</span>
                    @else
                        ${{ number_format($envio, 2) }}
                    @endif
                </span>
            </div>

                {{-- DESCUENTO --}}
                @if(optional($pedido->venta)->dDescuento > 0)
                    <div class="d-flex justify-content-between mb-2 text-success">
                        <span>Descuento</span>
                        <span>
                            - ${{ number_format($pedido->venta->dDescuento, 2) }}
                        </span>
                    </div>
                @endif

                <hr>

                <div class="d-flex justify-content-between fw-bold fs-5">
                    <span>Total</span>
                    <span>${{ number_format($pedido->dTotal, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function solicitarPostventa(url, titulo, placeholder) {
    Swal.fire({
        title: titulo,
        input: 'textarea',
        inputPlaceholder: placeholder,
        inputAttributes: {
            maxlength: 255,
            rows: 4
        },
        showCancelButton: true,
        confirmButtonText: 'Enviar solicitud',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#6f42c1',

        /* 🔒 UX PRO */
        showLoaderOnConfirm: true,
        allowOutsideClick: () => !Swal.isLoading(),

        preConfirm: (motivo) => {
            if (!motivo || motivo.length < 5) {
                Swal.showValidationMessage(
                    'El motivo debe tener al menos 5 caracteres'
                );
                return false;
            }

            return fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ motivo })
            })
            .then(async response => {
                const data = await response.json();

                if (!response.ok) {
                    throw data;
                }

                return data;
            })
            .catch(error => {
                Swal.showValidationMessage(
                    error.message ?? 'Ya existe una solicitud para este pedido'
                );
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                icon: 'success',
                title: 'Solicitud enviada',
                text: result.value.message,
                confirmButtonColor: '#198754'
            }).then(() => {
                location.reload();
            });
        }
    });
}
</script>

@endsection
