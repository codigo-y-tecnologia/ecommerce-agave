@extends('layouts.admins')

@section('title', 'Pedido #' . $pedido->id_pedido)

@section('content')

<a href="{{ route('admin.pedidos.index') }}" class="btn btn-link mb-3">
    ← Volver a pedidos
</a>

<h2 class="fw-bold mb-3">
    Pedido #{{ $pedido->id_pedido }}
</h2>

<div class="row mb-4">
    <div class="col-md-6">
        <p>
            <strong>Cliente:</strong>
            {{ data_get($pedido->usuario, 'vNombre') . ' ' . data_get($pedido->usuario, 'vApaterno') }}
        </p>

        <p>
            <strong>Email:</strong>
            {{ optional($pedido->usuario)->vEmail ?? '—' }}
        </p>

        <p>
            <strong>Estado pedido:</strong>
            <span class="badge bg-{{ estadoPedidoColor($pedido->eEstado) }}">
                {{ estadoPedidoTexto($pedido->eEstado) }}
            </span>
        </p>
    </div>

    <div class="col-md-6">
        <p>
            <strong>Método de pago:</strong>
            {{ optional($pedido->venta)->eMetodo_pago ?? '—' }}
        </p>

        <p>
            <strong>Estado pago:</strong>
            {{ optional($pedido->venta)->eEstado ?? '—' }}
        </p>

        <p>
            <strong>Total:</strong>
            ${{ number_format($pedido->dTotal, 2) }}
        </p>
    </div>
</div>

{{-- PRODUCTOS --}}
<div class="card shadow-sm mb-4">
    <div class="card-header fw-bold">
        Productos
    </div>

    <div class="card-body p-0">
        <table class="table mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Producto</th>
                    <th class="text-center">Cantidad</th>
                    <th class="text-end">Precio</th>
                    <th class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pedido->detalles as $det)
                    <tr>
                        <td>{{ optional($det->producto)->vNombre }}</td>
                        <td class="text-center">{{ $det->iCantidad }}</td>
                        <td class="text-end">${{ number_format($det->dPrecio_unitario, 2) }}</td>
                        <td class="text-end">
                            ${{ number_format($det->iCantidad * $det->dPrecio_unitario, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header fw-bold">
        Resumen del pedido
    </div>

    <div class="card-body">
        {{-- SUBTOTAL --}}
        <div class="d-flex justify-content-between mb-2">
            <span>Subtotal productos</span>
            <span>${{ number_format($subtotal, 2) }}</span>
        </div>

        {{-- ENVÍO --}}
        <div class="d-flex justify-content-between mb-2">
            <span>Envío</span>
            <span>
                @if($pedido->venta->dCosto_envio == 0)
                    <span class="text-success fw-semibold">Gratis</span>
                @else
                    ${{ number_format($pedido->venta->dCosto_envio, 2) }}
                @endif
            </span>
        </div>

        {{-- DESCUENTO --}}
        @if($pedido->venta->dDescuento > 0)
            <div class="d-flex justify-content-between mb-2 text-success">
                <span>Descuento aplicado</span>
                <span>
                    - ${{ number_format($pedido->venta->dDescuento, 2) }}
                </span>
            </div>
        @endif

        <hr>

        {{-- TOTAL --}}
        <div class="d-flex justify-content-between fw-bold fs-5">
            <span>Total pagado</span>
            <span>${{ number_format($pedido->dTotal, 2) }}</span>
        </div>
    </div>
</div>

{{-- ENVÍO --}}
<div class="card shadow-sm mt-4">
    <div class="card-header fw-bold">
        Envío
    </div>
    <div class="card-body">
        @if($pedido->envio)
            <p><strong>Transportista:</strong> {{ $pedido->envio->vTransportista }}</p>
            <p><strong>Guía:</strong> {{ $pedido->envio->vNumero_guia }}</p>
            <p>
                <strong>Estado:</strong>
                <span class="badge bg-info">
                    {{ ucfirst($pedido->envio->eEstado) }}
                </span>
            </p>
        @else
            <p class="text-muted">Aún no se ha generado el envío.</p>
        @endif
    </div>
</div>

<div class="card shadow-sm mt-4">
    <div class="card-header fw-bold">
        Acciones administrativas
    </div>

    <div class="card-body d-flex flex-wrap gap-2">

        {{-- CREAR ENVÍO --}}
        @if(!$pedido->envio && $pedido->eEstado === 'pagado')
            <button class="btn btn-outline-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#modalEnvio">
                Crear envío
            </button>
        @endif

        {{-- MARCAR ENVIADO --}}
        @if($pedido->envio && $pedido->envio->eEstado === 'pendiente')
            <form method="POST" action="{{ route('admin.pedidos.marcarEnviado', $pedido->id_pedido) }}">
                @csrf
                <button class="btn btn-outline-info">
                    Marcar como enviado
                </button>
            </form>
        @endif

        {{-- MARCAR ENTREGADO --}}
        @if($pedido->envio && $pedido->envio->eEstado === 'enviado')
            <form method="POST" action="{{ route('admin.pedidos.marcarEntregado', $pedido->id_pedido) }}">
                @csrf
                <button class="btn btn-outline-success">
                    Marcar como entregado
                </button>
            </form>
        @endif

        {{-- CANCELAR PEDIDO --}}
        @if($pedido->eEstado === 'pagado' && !$pedido->envio)
    <button
    class="btn btn-outline-danger btn-sm"
    onclick="cancelarPedido({{ $pedido->id_pedido }})">
    Cancelar y reembolsar
</button>
        @endif

    </div>
</div>

<div class="modal fade" id="modalEnvio" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.envios.store', $pedido->id_pedido) }}">
            @csrf

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Crear envío</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label>Transportista</label>
                        <input type="text" name="vTransportista" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Número de guía</label>
                        <input type="text" name="vNumero_guia" class="form-control" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Guardar envío</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function cancelarPedido(pedidoId) {
    Swal.fire({
        title: 'Cancelar pedido',
        html: `
            <p class="text-start mb-2">Indica el motivo del reembolso:</p>
            <textarea id="motivo" class="swal2-textarea" placeholder="Ej. Producto sin stock, error de pago..."></textarea>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, cancelar y reembolsar',
        cancelButtonText: 'No',
        preConfirm: () => {
            const motivo = document.getElementById('motivo').value.trim();
            if (!motivo) {
                Swal.showValidationMessage('El motivo es obligatorio');
            }
            return motivo;
        }
    }).then((result) => {
        if (result.isConfirmed) {

            Swal.fire({
                title: 'Procesando reembolso...',
                text: 'Por favor espera',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(`/admin/pedidos/${pedidoId}/cancelar`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    motivo: result.value
                })
            })
            .then(res => res.json())
            .then(data => {
                Swal.fire({
                    icon: 'success',
                    title: 'Reembolso exitoso',
                    text: data.message,
                }).then(() => location.reload());
            })
            .catch(() => {
                Swal.fire(
                    'Error',
                    'Ocurrió un error al procesar el reembolso',
                    'error'
                );
            });
        }
    });
}
</script>

@endsection
