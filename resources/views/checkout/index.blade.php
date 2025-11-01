@extends('layouts.app')

@section('title', 'Checkout - Confirmar Pedido')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4 text-center fw-bold">🧾 Resumen de tu pedido</h2>

    {{-- ✅ Mensajes --}}
    @if(session('warning'))
        <div class="alert alert-warning">{{ session('warning') }}</div>
    @endif
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- 🛒 Tabla de productos --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title">Productos en tu carrito</h5>
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-end">Precio Unitario</th>
                            <th class="text-end">Impuestos</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($carrito->detalles as $detalle)
                            @php
                                $producto = $detalle->producto;
                                $subtotalProducto = $detalle->cantidad * $detalle->precio_unitario;
                                $impuestosProducto = 0;
                                $desglose = [];

                                foreach ($producto->impuestos as $imp) {
                                    if ($imp->bActivo) {
                                        $monto = $subtotalProducto * ($imp->dPorcentaje / 100);
                                        $impuestosProducto += $monto;
                                        $desglose[] = "{$imp->eTipo} ({$imp->dPorcentaje}%)";
                                    }
                                }
                            @endphp
                            <tr>
                                <td>{{ $producto->vNombre }}</td>
                                <td class="text-center">{{ $detalle->cantidad }}</td>
                                <td class="text-end">${{ number_format($detalle->precio_unitario, 2) }}</td>
                                <td class="text-end">
                                    @if(count($desglose) > 0)
                                        <small class="text-muted d-block">{{ implode(', ', $desglose) }}</small>
                                        ${{ number_format($impuestosProducto, 2) }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-end fw-bold">${{ number_format($subtotalProducto + $impuestosProducto, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- 💰 Totales --}}
            <div class="mt-4">
                <p class="text-end mb-1"><strong>Subtotal:</strong> ${{ number_format($subtotal, 2) }}</p>
                <p class="text-end mb-1"><strong>Impuestos:</strong> ${{ number_format($totalImpuestos, 2) }}</p>

                {{-- 💸 Cupón --}}
                <div class="d-flex justify-content-end mt-3">
                    <input type="text" id="codigo_cupon" class="form-control w-auto me-2" placeholder="Código de cupón" value="{{ $codigoCupon ?? '' }}">
                    <button id="btn-aplicar-cupon" class="btn btn-outline-primary">Aplicar</button>
                </div>

                {{-- 💸 Mensaje de cupón aplicado --}}
@if(!empty($codigoCupon))
    <p class="text-end mt-3 text-success fw-bold" id="mensaje-cupon">
        Cupón aplicado: <span class="text-uppercase">{{ $codigoCupon }}</span>
        — Descuento: ${{ number_format($descuento, 2) }}
    </p>
@else
    <p class="text-end mt-3 text-success fw-bold" id="mensaje-cupon"></p>
@endif

                <hr>
                <p class="text-end fs-5 fw-bold">Total Final: <span id="total-final">${{ number_format($totalFinal, 2) }}</span></p>
            </div>
        </div>
    </div>

    {{-- 🚚 Dirección de envío --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title">Dirección de envío</h5>

            @if($direcciones->count() > 0)
                <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
                    @csrf
                    <div class="mb-3">
                        <label for="id_direccion" class="form-label fw-bold">Selecciona una dirección guardada:</label>
                        <select name="id_direccion" id="id_direccion" class="form-select" required>
                            <option value="">-- Selecciona una dirección --</option>
                            @foreach($direcciones as $dir)
                                <option value="{{ $dir->id_direccion }}">
                                    {{ $dir->vCalle }} {{ $dir->vNumero_exterior }}, {{ $dir->vColonia }}, {{ $dir->vCiudad }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-success px-4">Confirmar pedido</button>
                    </div>
                </form>
            @else
                <p class="text-muted">No tienes direcciones guardadas.</p>
                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalDireccion">
                    + Agregar nueva dirección
                </button>
            @endif
        </div>
    </div>
</div>

{{-- 🏠 Modal para agregar nueva dirección --}}
<div class="modal fade" id="modalDireccion" tabindex="-1" aria-labelledby="modalDireccionLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="form-nueva-direccion" class="modal-content">
            @csrf
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalDireccionLabel">Agregar nueva dirección</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="row g-3">
                    {{-- Teléfono --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold">📞 Teléfono de contacto</label>
                        <input type="text" name="vTelefono_contacto" class="form-control" required>
                    </div>

                    {{-- Calle y números --}}
                    <div class="col-md-8">
                        <label class="form-label fw-bold">🏠 Calle</label>
                        <input type="text" name="vCalle" class="form-control" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Número exterior</label>
                        <input type="text" name="vNumero_exterior" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Número interior</label>
                        <input type="text" name="vNumero_interior" class="form-control">
                    </div>

                    {{-- Colonia y CP --}}
                    <div class="col-md-4">
                        <label class="form-label">Colonia</label>
                        <input type="text" name="vColonia" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Código postal</label>
                        <input type="text" name="vCodigo_postal" class="form-control">
                    </div>

                    {{-- Ciudad y estado --}}
                    <div class="col-md-4">
                        <label class="form-label">Ciudad</label>
                        <input type="text" name="vCiudad" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Estado</label>
                        <input type="text" name="vEstado" class="form-control">
                    </div>

                    {{-- Entre calles --}}
                    <div class="col-md-6">
                        <label class="form-label">Entre calle 1</label>
                        <input type="text" name="vEntre_calle_1" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Entre calle 2</label>
                        <input type="text" name="vEntre_calle_2" class="form-control">
                    </div>

                    {{-- Referencias --}}
                    <div class="col-12">
                        <label class="form-label">Referencias adicionales</label>
                        <textarea name="tReferencias" class="form-control" rows="2" placeholder="Ejemplo: Frente al parque o portón azul"></textarea>
                    </div>

                    {{-- Dirección principal --}}
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bDireccion_principal" value="1" id="checkPrincipal">
                            <label class="form-check-label" for="checkPrincipal">
                                Establecer como dirección principal
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar dirección</button>
            </div>
        </form>
    </div>
</div>

{{-- 💻 Script AJAX --}}
<script>
document.addEventListener('DOMContentLoaded', () => {

    // Aplicar cupón dinámicamente
document.getElementById('btn-aplicar-cupon')?.addEventListener('click', async () => {
    const codigo = document.getElementById('codigo_cupon').value;
    const mensaje = document.getElementById('mensaje-cupon');
    const totalFinal = document.getElementById('total-final');

    const res = await fetch("{{ route('cupon.aplicar') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ codigo })
    });

    const data = await res.json();

    if (data.success) {
        mensaje.innerHTML = `
            Cupón aplicado: <span class="text-uppercase">${data.codigo}</span>
            — Descuento: $${data.descuento.toFixed(2)}
        `;
        mensaje.classList.remove('text-danger');
        mensaje.classList.add('text-success');
        totalFinal.textContent = `$${data.totalFinal.toFixed(2)}`;
    } else {
        mensaje.textContent = data.message;
        mensaje.classList.remove('text-success');
        mensaje.classList.add('text-danger');
    }
});
});
</script>
@endsection
