@extends('layouts.checkout')

@section('title', 'Checkout - Confirmar Pedido')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4 text-center fw-bold">🧾 Finalizar Compra</h2>

    {{-- ✅ Mensajes --}}
    @if(session('warning'))
        <div class="alert alert-warning">{{ session('warning') }}</div>
    @endif
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($direcciones->count() > 0)
    <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
        @csrf
        
        {{-- ========================================= --}}
        {{-- 1. DIRECCIÓN DE ENVÍO --}}
        {{-- ========================================= --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">📍 Dirección de Envío</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="id_direccion" class="form-label fw-bold">Selecciona una dirección guardada:</label>
                    <div class="input-group">
                        <select name="id_direccion" id="id_direccion" class="form-select" required>
                            <option value="">-- Selecciona una dirección --</option>
                            @foreach($direcciones as $dir)
                                <option value="{{ $dir->id_direccion }}"
                                    @if(isset($direccionPrincipal) && $direccionPrincipal->id_direccion === $dir->id_direccion)
                                        selected
                                    @endif
                                >
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
            </div>
        </div>

        {{-- ========================================= --}}
        {{-- 2. DIRECCIÓN DE FACTURACIÓN --}}
        {{-- ========================================= --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">🧾 Dirección de Facturación</h5>
            </div>
            <div class="card-body">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="misma_direccion_facturacion" checked>
                    <label class="form-check-label" for="misma_direccion_facturacion">
                        Usar la misma dirección para facturación
                    </label>
                </div>
                
                {{-- Contenedor para dirección de facturación diferente --}}
                <div id="direccion-facturacion-container" class="mt-3" style="display: none;">
                    <label for="id_direccion_facturacion" class="form-label fw-bold">Dirección de facturación:</label>
                    <select name="id_direccion_facturacion" id="id_direccion_facturacion" class="form-select">
                        <option value="">-- Selecciona una dirección de facturación --</option>
                        @foreach($direcciones as $dir)
                            <option value="{{ $dir->id_direccion }}">
                                {{ $dir->vCalle }} {{ $dir->vNumero_exterior }}, {{ $dir->vColonia }}, {{ $dir->vCiudad }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- ========================================= --}}
        {{-- 3. MÉTODO DE PAGO --}}
        {{-- ========================================= --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">💳 Método de Pago</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check mb-3">
                            <input class="form-check-input metodo-pago" type="radio" name="metodo_pago" id="stripe_pago" value="stripe" checked>
                            <label class="form-check-label fw-bold" for="stripe_pago">
                                <i class="bi bi-credit-card-2-front me-2"></i> Tarjeta de Crédito/Débito (Stripe)
                            </label>
                            <p class="text-muted small mb-0">Paga de forma segura con tu tarjeta</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check mb-3">
                            <input class="form-check-input metodo-pago" type="radio" name="metodo_pago" id="paypal_pago" value="paypal">
                            <label class="form-check-label fw-bold" for="paypal_pago">
                                <i class="bi bi-paypal me-2"></i> PayPal
                            </label>
                            <p class="text-muted small mb-0">Paga a través de tu cuenta PayPal</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================= --}}
        {{-- 4. NOTA DEL PEDIDO --}}
        {{-- ========================================= --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">📝 Nota del Pedido</h5>
            </div>
            <div class="card-body">
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="agregar_nota">
                    <label class="form-check-label" for="agregar_nota">
                        Agregar nota a su orden
                    </label>
                </div>
                
                <div id="nota-container" class="mt-2" style="display: none;">
                    <textarea name="nota_pedido" id="nota_pedido" class="form-control" rows="4" 
                              placeholder="Ejemplo: Llamar antes de entregar, dejar el paquete en la puerta, instrucciones especiales, etc."></textarea>
                    <small class="text-muted">Esta nota se incluirá con tu pedido.</small>
                </div>
            </div>
        </div>

        {{-- ========================================= --}}
        {{-- 5. RESUMEN DEL PEDIDO --}}
        {{-- ========================================= --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">🧾 Resumen de tu Pedido</h5>
            </div>
            <div class="card-body">
                {{-- 🛒 Tabla de productos --}}
                <div class="table-responsive mb-4">
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

                                    $precioSinImpuestos = $detalle->precio_unitario / (1 + ($totalPorcentaje / 100));
                                    $impuestosUnitarios = $detalle->precio_unitario - $precioSinImpuestos;
                                    $impuestosTotales = $impuestosUnitarios * $detalle->cantidad;
                                @endphp
                                <tr>
                                    <td>{{ $producto->vNombre }}</td>
                                    <td class="text-center">{{ $detalle->cantidad }}</td>
                                    <td class="text-end">${{ number_format($precioSinImpuestos, 2) }}</td>
                                    <td class="text-end">
                                        @if(count($desglose) > 0)
                                            <small class="text-muted d-block">{{ implode(', ', $desglose) }}</small>
                                            ${{ number_format($impuestosTotales, 2) }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-end fw-bold">${{ number_format($detalle->cantidad * $detalle->precio_unitario, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- 💰 Totales --}}
                <div class="border-top pt-3">
                    <div class="row justify-content-end">
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between mb-2">
                                <span><strong>Subtotal:</strong></span>
                                <span>${{ number_format($subtotal, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span><strong>Impuestos:</strong></span>
                                <span>${{ number_format($totalImpuestos, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2" id="envio-linea">
                                <span><strong>Envío:</strong></span>
                                <span>
                                    @if ($envio == 0)
                                        <span class="text-success">Gratis 🚚</span>
                                    @else
                                        ${{ number_format($envio, 2) }}
                                    @endif
                                </span>
                            </div>

                            {{-- 💸 Cupón --}}
                            <div class="d-flex justify-content-between align-items-center mt-3 mb-2">
                                <input type="text" id="codigo_cupon" class="form-control me-2" placeholder="Código de cupón" value="{{ $codigoCupon ?? '' }}">
                                <button id="btn-aplicar-cupon" class="btn btn-outline-primary">Aplicar</button>
                            </div>

                            {{-- Mensaje de cupón aplicado --}}
                            <div id="mensaje-cupon-container">
                                @if(!empty($codigoCupon))
                                    <p class="text-success fw-bold mb-2" id="mensaje-cupon">
                                        @if($codigoCupon === 'ENVIOGRATIS')
                                            Cupón aplicado correctamente: {{ $codigoCupon }} — Envío gratis activado 🚚
                                        @else
                                            Cupón aplicado correctamente: {{ $codigoCupon }} — Descuento: ${{ number_format($descuento, 2) }}
                                        @endif
                                    </p>
                                @else
                                    <p class="text-success fw-bold mb-2" id="mensaje-cupon" style="display: none;"></p>
                                @endif
                            </div>

                            <hr>
                            <div class="d-flex justify-content-between fs-5 fw-bold">
                                <span>Total Final:</span>
                                <span id="total-final">${{ number_format($totalFinal, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================= --}}
        {{-- 6. BOTONES DE PAGO --}}
        {{-- ========================================= --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body text-center">
                <h5 class="card-title mb-3">Finalizar Compra</h5>
                
                {{-- Botón Stripe --}}
                <button id="btn-stripe" type="button" class="btn btn-primary btn-lg px-5 py-3 d-none" 
                        style="font-size: 1.1rem; font-weight: 600;">
                    <i class="bi bi-credit-card-2-front me-2"></i>
                    Pagar con Tarjeta - $<span id="stripe-total">{{ number_format($totalFinal, 2) }}</span>
                </button>

                {{-- Botón PayPal --}}
                <div id="paypal-button-container" class="d-none"></div>

                <p class="text-muted mt-3">
                    Al completar tu compra, aceptas nuestros <a href="#">Términos y Condiciones</a>
                </p>
            </div>
        </div>
    </form>
    @else
        {{-- Mensaje cuando no hay direcciones --}}
        <div class="alert alert-warning">
            <p class="mb-3">No tienes direcciones guardadas. Debes agregar una dirección para continuar con la compra.</p>
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalDireccion" id="btn-nueva-direccion">
                + Agregar nueva dirección
            </button>
        </div>
    @endif
</div>

{{-- 🏠 Modal para agregar nueva dirección --}}
<div class="modal fade" id="modalDireccion" tabindex="-1" aria-labelledby="modalDireccionLabel" aria-hidden="true">
    {{-- El contenido del modal permanece igual --}}
    <div class="modal-dialog modal-lg">
        <form id="form-nueva-direccion" class="modal-content">
            @csrf
            <input type="hidden" id="id_direccion_editar" name="id_direccion_editar">
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

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Stripe JS -->
<script src="https://js.stripe.com/v3/"></script>
<!-- PayPal JS -->
<script src="https://www.paypal.com/sdk/js?client-id={{ env('PAYPAL_CLIENT_ID') }}&currency=MXN&disable-funding=card"></script>

{{-- 💻 Script AJAX --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    // =========================================
    // VARIABLES GLOBALES
    // =========================================
    let notaTexto = ''; // Variable para guardar el texto de la nota

    // =========================================
    // 1. MANEJO DE DIRECCIÓN DE FACTURACIÓN
    // =========================================
    const mismaDireccionCheckbox = document.getElementById('misma_direccion_facturacion');
    const direccionFacturacionContainer = document.getElementById('direccion-facturacion-container');

    mismaDireccionCheckbox?.addEventListener('change', function() {
        if (this.checked) {
            direccionFacturacionContainer.style.display = 'none';
        } else {
            direccionFacturacionContainer.style.display = 'block';
        }
    });

    // =========================================
    // 2. MANEJO DE NOTA DEL PEDIDO
    // =========================================
    const agregarNotaCheckbox = document.getElementById('agregar_nota');
    const notaContainer = document.getElementById('nota-container');
    const notaTextarea = document.getElementById('nota_pedido');

    agregarNotaCheckbox?.addEventListener('change', function() {
        if (this.checked) {
            notaContainer.style.display = 'block';
            // Restaurar el texto si existe
            if (notaTexto) {
                notaTextarea.value = notaTexto;
            }
        } else {
            // Guardar el texto antes de ocultar
            notaTexto = notaTextarea.value;
            notaContainer.style.display = 'none';
        }
    });

    // =========================================
    // 3. MANEJO DE MÉTODO DE PAGO
    // =========================================
    const metodoPagoRadios = document.querySelectorAll('.metodo-pago');
    const btnStripe = document.getElementById('btn-stripe');
    const paypalContainer = document.getElementById('paypal-button-container');

    function actualizarBotonesPago() {
        const metodoSeleccionado = document.querySelector('input[name="metodo_pago"]:checked').value;
        
        // Ocultar todos los botones primero
        btnStripe.classList.add('d-none');
        paypalContainer.classList.add('d-none');

        // Mostrar solo el botón seleccionado
        if (metodoSeleccionado === 'stripe') {
            btnStripe.classList.remove('d-none');
        } else if (metodoSeleccionado === 'paypal') {
            paypalContainer.classList.remove('d-none');
        }
    }

    // Event listeners para los radio buttons
    metodoPagoRadios.forEach(radio => {
        radio.addEventListener('change', actualizarBotonesPago);
    });

    // Inicializar botones al cargar la página
    actualizarBotonesPago();

    // =========================================
    // 4. CUPÓN (código existente)
    // =========================================
    document.getElementById('btn-aplicar-cupon')?.addEventListener('click', async (e) => {
        e.preventDefault();

        const codigo = document.getElementById('codigo_cupon').value.trim();
        const mensaje = document.getElementById('mensaje-cupon');
        const totalFinal = document.getElementById('total-final');
        const stripeTotal = document.getElementById('stripe-total');
        const envioElemento = document.getElementById('envio-linea');

        function formatCurrency(value) {
            return '$' + value.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        if (!codigo) {
            Swal.fire({
                icon: "warning",
                title: "Código vacío",
                text: "Por favor ingresa un código de cupón.",
                confirmButtonText: "Entendido"
            });
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
                mensaje.style.display = 'block';
                mensaje.classList.remove('text-danger');
                mensaje.classList.add('text-success');
                
                totalFinal.textContent = formatCurrency(data.totalFinal);
                stripeTotal.textContent = data.totalFinal.toFixed(2);

                // Actualizar Envío dinámicamente
                if (typeof data.envio !== 'undefined' && envioElemento) {
                    const envioValor = data.envio == 0
                        ? '<span class="text-success">Gratis 🚚</span>'
                        : formatCurrency(data.envio);
                    envioElemento.innerHTML = `<span><strong>Envío:</strong></span><span>${envioValor}</span>`;
                }
            } else {
                Swal.fire({
                    icon: "warning",
                    title: "Cupón no disponible",
                    text: data.message,
                    confirmButtonText: "Entendido"
                });

                mensaje.textContent = data.message;
                mensaje.style.display = 'block';
                mensaje.classList.remove('text-success');
                mensaje.classList.add('text-danger');
            }

        } catch (error) {
            console.error('Error al aplicar cupón:', error);
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "Ocurrió un error al aplicar el cupón.",
                confirmButtonText: "Entendido"
            });
        }
    });

    // =========================================
    // 5. VALIDACIÓN DE DIRECCIÓN
    // =========================================
    function validarDireccion() {
        const selectDireccion = document.getElementById('id_direccion');
        const valor = selectDireccion.value;

        if (!valor || valor === "" || parseInt(valor) <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Selecciona una dirección',
                text: 'Debes elegir una dirección de envío antes de continuar.',
            });
            return false;
        }

        return true;
    }

    // =========================================
    // 6. STRIPE
    // =========================================
    document.getElementById('btn-stripe')?.addEventListener('click', async (e) => {
        e.preventDefault();

        if (!validarDireccion()) {
            return;
        }

        // Obtener datos del formulario
        const idDireccion = document.getElementById('id_direccion').value;
        const mismaDireccion = document.getElementById('misma_direccion_facturacion').checked;
        const idDireccionFacturacion = mismaDireccion ? idDireccion : document.getElementById('id_direccion_facturacion').value;
        const nota = document.getElementById('nota_pedido').value;

        try {
            const res = await fetch("{{ route('payment.stripe.session') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    id_direccion: idDireccion,
                    id_direccion_facturacion: idDireccionFacturacion,
                    nota: nota
                })
            });

            const data = await res.json();

            if (!data.success) {
                Swal.fire("Error", data.message || "Error al crear la sesión de pago.", "error");
                return;
            }

            window.location.href = data.url;

        } catch (err) {
            console.error(err);
            Swal.fire("Error", "No se pudo iniciar el pago con Stripe.", "error");
        }
    });

    // =========================================
    // 7. PAYPAL
    // =========================================
    if (typeof paypal !== 'undefined') {
        paypal.Buttons({
            onClick: function(data, actions) {
                if (!validarDireccion()) {
                    return Promise.reject();
                }
                return Promise.resolve();
            },

            createOrder: function(data, actions) {
                // Obtener datos del formulario
                const idDireccion = document.getElementById('id_direccion').value;
                const mismaDireccion = document.getElementById('misma_direccion_facturacion').checked;
                const idDireccionFacturacion = mismaDireccion ? idDireccion : document.getElementById('id_direccion_facturacion').value;
                const nota = document.getElementById('nota_pedido').value;

                return fetch("{{ route('payment.paypal.create') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        id_direccion: idDireccion,
                        id_direccion_facturacion: idDireccionFacturacion,
                        nota: nota
                    })
                })
                .then(res => res.json())
                .then(json => {
                    if (!json.success) {
                        Swal.fire("Error", json.message || "Error creando orden PayPal", "error");
                        throw new Error('Error creando orden PayPal');
                    }
                    return json.orderID;
                });
            },

            onApprove: function(data, actions) {
                return fetch("{{ route('payment.paypal.capture') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ orderID: data.orderID })
                })
                .then(res => res.json())
                .then(json => {
                    if (json.success) {
                        window.location.href = "{{ route('home') }}?paid=1&method=paypal";
                    } else {
                        Swal.fire("Error", "Error capturando pago en PayPal", "error");
                    }
                });
            },

            onCancel: function () {
                Swal.fire("Cancelado", "Pago cancelado.", "info");
            },

            onError: function (err) {
                console.error('PayPal error:', err);
                Swal.fire("Error", "Error en PayPal", "error");
            }
        }).render('#paypal-button-container');
    }

    // =========================================
    // 8. MANEJO DE DIRECCIONES (código existente)
    // =========================================
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

        if (id) {
            formData.append('_method', 'PUT');
        }

        const url = id
            ? `/checkout/actualizar-direccion/${id}`
            : `{{ route('checkout.crearDireccion') }}`;

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: formData
            });

            const data = await res.json();

            if (data.success) {
                modal.hide();
                Swal.fire({
                    icon: 'success',
                    title: id ? 'Dirección actualizada' : 'Dirección creada',
                    text: id ? 'Dirección actualizada correctamente.' : 'Dirección creada correctamente.',
                    confirmButtonText: 'Entendido'
                });

                if (!id) {
                    const opt = document.createElement('option');
                    opt.value = data.direccion.id_direccion;
                    opt.textContent = `${data.direccion.vCalle} ${data.direccion.vNumero_exterior}, ${data.direccion.vColonia}, ${data.direccion.vCiudad}`;
                    idInput.appendChild(opt);
                    idInput.value = data.direccion.id_direccion;
                    
                    // También agregar a la lista de facturación
                    const optFacturacion = document.createElement('option');
                    optFacturacion.value = data.direccion.id_direccion;
                    optFacturacion.textContent = `${data.direccion.vCalle} ${data.direccion.vNumero_exterior}, ${data.direccion.vColonia}, ${data.direccion.vCiudad}`;
                    document.getElementById('id_direccion_facturacion').appendChild(optFacturacion);
                } else {
                    const selected = idInput.querySelector(`option[value="${id}"]`);
                    selected.textContent = `${data.direccion.vCalle} ${data.direccion.vNumero_exterior}, ${data.direccion.vColonia}, ${data.direccion.vCiudad}`;
                    
                    // Actualizar también en facturación si existe
                    const selectedFacturacion = document.querySelector(`#id_direccion_facturacion option[value="${id}"]`);
                    if (selectedFacturacion) {
                        selectedFacturacion.textContent = `${data.direccion.vCalle} ${data.direccion.vNumero_exterior}, ${data.direccion.vColonia}, ${data.direccion.vCiudad}`;
                    }
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al guardar la dirección.',
                    confirmButtonText: 'Entendido'
                });
            }
        } catch (err) {
            console.error(err);
            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: 'Error de conexión al guardar la dirección.',
                confirmButtonText: 'Entendido'
            });
        }
    });
});
</script>

<style>
.metodo-pago:checked + label {
    color: #0d6efd;
    font-weight: bold;
}
.paypal-buttons iframe {
    transform: scale(1.05);
}
.card-header {
    border-bottom: 2px solid #e9ecef;
}
</style>

@endsection