@extends('layouts.checkout')

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
        $desglose = [];
        $totalPorcentaje = 0;

        foreach ($producto->impuestos as $imp) {
            if ($imp->bActivo) {
                $totalPorcentaje += $imp->dPorcentaje;
                $desglose[] = "{$imp->eTipo} ({$imp->dPorcentaje}%)";
            }
        }

        // 🔹 Precio base (sin impuestos)
        $precioSinImpuestos = $detalle->precio_unitario / (1 + ($totalPorcentaje / 100));

        // 🔹 Impuestos por unidad y total
        $impuestosUnitarios = $detalle->precio_unitario - $precioSinImpuestos;
        $impuestosTotales = $impuestosUnitarios * $detalle->cantidad;
    @endphp

    <tr>
        <td>{{ $producto->vNombre }}</td>
        <td class="text-center">{{ $detalle->cantidad }}</td>

        {{-- Precio unitario sin impuestos --}}
        <td class="text-end">${{ number_format($precioSinImpuestos, 2) }}</td>

        {{-- Impuestos --}}
        <td class="text-end">
            @if(count($desglose) > 0)
                <small class="text-muted d-block">{{ implode(', ', $desglose) }}</small>
                ${{ number_format($impuestosTotales, 2) }}
            @else
                <span class="text-muted">—</span>
            @endif
        </td>

        {{-- Subtotal con impuestos --}}
        <td class="text-end fw-bold">${{ number_format($detalle->cantidad * $detalle->precio_unitario, 2) }}</td>
    </tr>
@endforeach
                    </tbody>
                </table>
            </div>

            {{-- 💰 Totales --}}
            <div class="mt-4">
                <p class="text-end mb-1"><strong>Subtotal:</strong> ${{ number_format($subtotal, 2) }}</p>
                <p class="text-end mb-1"><strong>Impuestos:</strong> ${{ number_format($totalImpuestos, 2) }}</p>

                <p class="text-end mb-1" id="envio-linea">
        <strong>Envío:</strong>
        @if ($envio == 0)
        <span class="text-success">Gratis 🚚</span>
        @else
            ${{ number_format($envio, 2) }}
        @endif
    </p>

                {{-- 💸 Cupón --}}
                <div class="d-flex justify-content-end mt-3">
                    <input type="text" id="codigo_cupon" class="form-control w-auto me-2" placeholder="Código de cupón" value="{{ $codigoCupon ?? '' }}">
                    <button id="btn-aplicar-cupon" class="btn btn-outline-primary">Aplicar</button>
                </div>

                {{-- 💸 Mensaje de cupón aplicado --}}
@if(!empty($codigoCupon))
    <p class="text-end mt-3 text-success fw-bold" id="mensaje-cupon">
        @if(($codigoCupon) === 'ENVIOGRATIS')
            Cupón aplicado correctamente: {{ $codigoCupon }} — Envío gratis activado 🚚
        @else
            Cupón aplicado correctamente: {{ $codigoCupon }} — Descuento: ${{ number_format($descuento, 2) }}
        @endif
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
            <div class="input-group">
                <select name="id_direccion" id="id_direccion" class="form-select" required>
                    <option value="">-- Selecciona una dirección --</option>
                    @foreach($direcciones as $dir)
                        <option value="{{ $dir->id_direccion }}">
                            {{ $dir->vCalle }} {{ $dir->vNumero_exterior }}, {{ $dir->vColonia }}, {{ $dir->vCiudad }}
                        </option>
                    @endforeach
                </select>
                <button type="button" class="btn btn-outline-primary" id="btn-editar-direccion">✏️ Editar</button>
                <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalDireccion" id="btn-nueva-direccion">
                    ➕ Nueva
                </button>
            </div>
        </div>

        <div class="d-flex justify-content-end mt-3">
            <button type="submit" class="btn btn-success px-4">Confirmar pedido</button>
        </div>
    </form>
@else
    <p class="text-muted">No tienes direcciones guardadas.</p>
    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalDireccion" id="btn-nueva-direccion">
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
            <input type="hidden" id="id_direccion_editar" name="id_direccion_editar"> {{-- 🔹 para modo edición --}}
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
    document.getElementById('btn-aplicar-cupon')?.addEventListener('click', async (e) => {
        e.preventDefault(); // evita recargar la página

        const codigo = document.getElementById('codigo_cupon').value.trim();
        const mensaje = document.getElementById('mensaje-cupon');
        const totalFinal = document.getElementById('total-final');
        const envioElemento = document.getElementById('envio-linea');

        function formatCurrency(value) {
    return '$' + value.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

        if (!codigo) {
            mensaje.textContent = "Por favor ingresa un código de cupón.";
            mensaje.classList.add('text-danger');
            return;
        }

        try {
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
                mensaje.textContent = data.message;
                mensaje.classList.remove('text-danger');
                mensaje.classList.add('text-success');
                totalFinal.textContent = formatCurrency(data.totalFinal);

                // Actualizar Envío dinámicamente
                if (typeof data.envio !== 'undefined' && envioElemento) {
                    const envioValor = data.envio == 0
                        ? '<span class="text-success">Gratis 🚚</span>'
                        : formatCurrency(data.envio);
                        envioElemento.innerHTML = `<strong>Envío:</strong> ${envioValor}`;
                }
            } else {
                mensaje.textContent = data.message;
                mensaje.classList.remove('text-success');
                mensaje.classList.add('text-danger');
            }

        } catch (error) {
            console.error('Error al aplicar cupón:', error);
            mensaje.textContent = "Ocurrió un error al aplicar el cupón.";
            mensaje.classList.add('text-danger');
        }
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = new bootstrap.Modal(document.getElementById('modalDireccion'));
    const formDireccion = document.getElementById('form-nueva-direccion');
    const idInput = document.getElementById('id_direccion');
    const idEditar = document.getElementById('id_direccion_editar');
    const modalTitle = document.getElementById('modalDireccionLabel');

    // 🔹 Botón NUEVA DIRECCIÓN
    document.getElementById('btn-nueva-direccion')?.addEventListener('click', () => {
        formDireccion.reset();
        idEditar.value = '';
        modalTitle.textContent = 'Agregar nueva dirección';
    });

    // 🔹 Botón EDITAR DIRECCIÓN
    document.getElementById('btn-editar-direccion')?.addEventListener('click', async () => {
        const id = idInput.value;
        if (!id) {
            alert('Por favor selecciona una dirección para editar.');
            return;
        }

        try {
            const res = await fetch(`/api/direccion/${id}`);
            const data = await res.json();

            if (data.success) {
                const d = data.direccion;
                for (const key in d) {
                    if (formDireccion.elements[key]) {
                        formDireccion.elements[key].value = d[key];
                    }
                }

                const checkPrincipal = document.getElementById('checkPrincipal');
                checkPrincipal.checked = (parseInt(d.bDireccion_principal) === 1);

                idEditar.value = id;
                modalTitle.textContent = 'Editar dirección';
                modal.show();
            } else {
                alert('No se pudo cargar la dirección.');
            }
        } catch (err) {
            console.error(err);
            alert('Error al obtener la dirección.');
        }
    });

    // 🔹 GUARDAR (crear o actualizar)
    formDireccion.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(formDireccion);
    const id = idEditar.value;

    // 🔹 Forzar Laravel a reconocer PUT
    if (id) {
        formData.append('_method', 'PUT');
    }

    const url = id
        ? `/checkout/actualizar-direccion/${id}`
        : `{{ route('checkout.crearDireccion') }}`;

    try {
        const res = await fetch(url, {
            method: 'POST', // <-- SIEMPRE POST
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: formData
        });

        const data = await res.json();

        if (data.success) {
            modal.hide();
            alert(id ? 'Dirección actualizada correctamente.' : 'Dirección creada correctamente.');

            if (!id) {
                const opt = document.createElement('option');
                opt.value = data.direccion.id_direccion;
                opt.textContent = `${data.direccion.vCalle} ${data.direccion.vNumero_exterior}, ${data.direccion.vColonia}, ${data.direccion.vCiudad}`;
                idInput.appendChild(opt);
                idInput.value = data.direccion.id_direccion;
            } else {
                const selected = idInput.querySelector(`option[value="${id}"]`);
                selected.textContent = `${data.direccion.vCalle} ${data.direccion.vNumero_exterior}, ${data.direccion.vColonia}, ${data.direccion.vCiudad}`;
            }
        } else {
            alert('❌ Error al guardar la dirección.');
        }
    } catch (err) {
        console.error(err);
        alert('❌ Error de conexión al guardar la dirección.');
    }
});
});
</script>

@endsection
