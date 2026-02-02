@extends('layouts.checkout')

@section('title', 'Checkout - Confirmar Pedido')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4 text-center fw-bold">🧾 Finalizar Compra</h2>

    {{-- ✅ Mensajes --}}
    @if(request('paid') === '0')
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Pago no completado',
            text: 'Tu pago no pudo realizarse. Puedes intentar nuevamente con otro método o tarjeta.',
        });
    </script>
@endif
    
@include('superadmin.partials.alerts')

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
                    <label for="id_direccion" class="form-label fw-bold">Selecciona una dirección de envío:</label>
                    <div class="input-group">
                        <select name="id_direccion" id="id_direccion" class="form-select" required>
                            <option value="">-- Selecciona una dirección --</option>
                            @foreach($direcciones as $dir)
                            @php
            // Para usuarios invitados, usar id_direccion_guest
            $idValue = Auth::check() ? $dir->id_direccion : $dir->id_direccion_guest;
        @endphp
                                <option value="{{ $idValue }}"
                                    @if(isset($direccionPrincipal) && 
                (Auth::check() && $direccionPrincipal->id_direccion === $dir->id_direccion) ||
                (!Auth::check() && $direccionPrincipal->id_direccion_guest === $dir->id_direccion_guest))
                selected
            @endif>
                                    {{ $dir->vCalle }} {{ $dir->vNumero_exterior }}, {{ $dir->vColonia }}, {{ $dir->vCiudad }}
                                </option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-outline-primary" id="btn-editar-direccion">✏️ Editar</button>
                        <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalDireccion" id="btn-nueva-direccion">
                            ➕ Nueva
                        </button>
                    </div>
                    @if($direcciones->count() == 0)
                        <div class="text-warning mt-2">
                            <small>⚠️ No tienes direcciones guardadas. Debes agregar una dirección antes de pagar.</small>
                        </div>
                    @endif
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
            @php
                // Usar el mismo ID que en el select de envío
                $idValue = Auth::check() ? $dir->id_direccion : $dir->id_direccion_guest;
            @endphp
            <option value="{{ $idValue }}">
                {{ $dir->vCalle }} {{ $dir->vNumero_exterior }}, {{ $dir->vColonia }}, {{ $dir->vCiudad }}
            </option>
        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- ========================================= --}}
        {{-- 3. INFORMACIÓN DE CONTACTO (USUARIOS NO REGISTRADOS) --}}
        {{-- ========================================= --}}
        @guest
<div class="card shadow-sm mb-4">
    <div class="card-header bg-light">
        <h5 class="card-title mb-0">📧 Información de contacto</h5>
    </div>
    <div class="card-body">
        <p class="text-muted">
            Utilizaremos este correo electrónico para enviarte detalles y actualizaciones sobre tu pedido.
        </p>

        <div class="mb-3">
            <label class="form-label fw-bold">Correo electrónico <span class="text-danger">*</span></label>
            <input type="email"
                   name="vEmail"
                   id="vEmail"
                   class="form-control"
                   placeholder="ejemplo@correo.com"
                   maxlength="100"
                   required>
        </div>
    </div>
</div>
@endguest

        {{-- ========================================= --}}
        {{-- 4. MÉTODO DE PAGO --}}
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
                                <i class="bi bi-credit-card-2-front me-2"></i> Tarjeta de Crédito/Débito
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
        {{-- 5. NOTA DEL PEDIDO --}}
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
        {{-- 6. RESUMEN DEL PEDIDO --}}
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
                            $cantidad = $detalle->cantidad;

                            // Precio base del producto sin impuestos
                            $precio_base = $producto->dPrecio_venta;

                            // Obtener impuestos activos
                            $impuestos = $producto->impuestos->where('bActivo', 1);

                            $ieps = 0;
                            $iva = 0;

                            $desglose = [];

                            // Calcular IEPS primero
                            foreach ($impuestos as $imp) {
                                if ($imp->eTipo === 'IEPS') {
                                    $ieps = $precio_base * ($imp->dPorcentaje / 100);
                                }
                            }

                            // Calcular IVA después (sobre base + IEPS)
                            foreach ($impuestos as $imp) {
                                if ($imp->eTipo === 'IVA') {
                                    $iva = ($precio_base + $ieps) * ($imp->dPorcentaje / 100);
                                }
                            }

                            // Crear desglose visual
                            foreach ($impuestos as $imp) {
                                $desglose[] = "{$imp->eTipo} ({$imp->dPorcentaje}%)";
                            }

                            // Impuestos unitarios
                            $impuestos_unitarios = $ieps + $iva;

                            // Precio final unitario
                            $precio_final_unitario = $precio_base + $ieps + $iva;

                            // Totales multiplicados por cantidad
                            $impuestosTotales = $impuestos_unitarios * $cantidad;
                            $subtotalProducto = $precio_final_unitario * $cantidad;

                        @endphp
                        <tr>
                            <td>{{ $producto->vNombre }}</td>

                            <td class="text-center">{{ $cantidad }}</td>

                            {{-- Precio unitario sin impuestos --}}
                            <td class="text-end">
                                ${{ number_format($precio_base, 2) }}
                            </td>

                            {{-- Impuestos (IEPS + IVA) --}}
                            <td class="text-end">
                                <small class="text-muted d-block">{{ implode(', ', $desglose) }}</small>
                                ${{ number_format($impuestosTotales, 2) }}
                            </td>

                            {{-- Subtotal final (precio base + ieps + iva) * cantidad --}}
                            <td class="text-end fw-bold">
                                ${{ number_format($subtotalProducto, 2) }}
                            </td>
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
        {{-- 7. BOTONES DE PAGO --}}
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
</div>

{{-- 🏠 Modal para agregar nueva dirección --}}
<div class="modal fade" id="modalDireccion" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalDireccionLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="form-nueva-direccion" class="modal-content" novalidate>
            @csrf
            <input type="hidden" id="id_direccion_editar" name="id_direccion_editar">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalDireccionLabel">Agregar nueva dirección</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                {{-- Mensajes de error --}}
                <div id="modal-errors" class="alert alert-danger d-none"></div>

                {{-- Campos del formulario --}}

                {{-- Nombre y apellidos (solo para usuarios no registrados) --}}
                @guest
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <label class="form-label fw-bold">Nombre <span class="text-danger">*</span></label>
        <input type="text" name="vNombre" id="vNombre" class="form-control" maxlength="60" required>
    </div>
    <div class="col-md-4">
        <label class="form-label fw-bold">Apellido paterno<span class="text-danger">*</span></label>
        <input type="text" name="vApaterno" id="vApaterno" class="form-control" maxlength="50" required>
    </div>
    <div class="col-md-4">
        <label class="form-label fw-bold">Apellido materno<span class="text-danger">*</span></label>
        <input type="text" name="vAmaterno" id="vAmaterno" class="form-control" maxlength="50" required>
    </div>
</div>
@endguest
                <div class="row g-3">
                    {{-- Teléfono con código de país --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold">📞 Teléfono de contacto <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <select name="codigo_pais" id="codigo_pais" class="form-select" style="max-width: 120px;" required>
                                <option value="+52">🇲🇽 +52</option>
                                <option value="+1">🇺🇸 +1</option>
                            </select>
                            <input type="text" name="vTelefono_contacto" id="vTelefono_contacto" class="form-control" 
                                   maxlength="15" pattern="[0-9]*" placeholder="Ej: 5512345678" required
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                   title="Solo se permiten números">
                        </div>
                        <div class="form-text">Solo números, máximo 15 dígitos (sin código de país)</div>
                    </div>

                    {{-- RFC (opcional) --}}
    <div class="col-md-6">
        <label class="form-label fw-bold">RFC</label>
        <input type="text"
               name="vRFC"
               id="vRFC"
               class="form-control"
               maxlength="13"
               placeholder="XAXX010101000"
               style="text-transform: uppercase;"
               oninput="this.value = this.value.toUpperCase()">
        <div class="form-text">Opcional para facturación</div>
    </div>

                    {{-- Calle --}}
                    <div class="col-md-8">
                        <label class="form-label fw-bold">🏠 Calle <span class="text-danger">*</span></label>
                        <input type="text" name="vCalle" class="form-control" maxlength="150" required
                               placeholder="Ej: Avenida Insurgentes">
                        <div class="form-text">Máximo 150 caracteres</div>
                    </div>

                    {{-- Número exterior --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Número exterior <span class="text-danger">*</span></label>
                        <input type="text" name="vNumero_exterior" class="form-control" maxlength="20" required
                               placeholder="Ej: 123">
                        <div class="form-text">Máximo 20 caracteres</div>
                    </div>

                    {{-- Número interior (OPCIONAL) --}}
                    <div class="col-md-4">
                        <label class="form-label">Número interior</label>
                        <input type="text" name="vNumero_interior" class="form-control" maxlength="20"
                               placeholder="Ej: 45">
                        <div class="form-text">Opcional, máximo 20 caracteres</div>
                    </div>

                    {{-- Colonia --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Colonia <span class="text-danger">*</span></label>
                        <input type="text" name="vColonia" class="form-control" maxlength="150" required
                               placeholder="Ej: Nápoles">
                        <div class="form-text">Máximo 150 caracteres</div>
                    </div>

                    {{-- Código postal --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Código postal <span class="text-danger">*</span></label>
                        <input type="text" name="vCodigo_postal" class="form-control" maxlength="10" 
                               pattern="[0-9]*" required
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                               placeholder="Ej: 03810"
                               title="Solo se permiten números">
                        <div class="form-text">Máximo 10 caracteres, solo números</div>
                    </div>

                    {{-- Ciudad --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Ciudad <span class="text-danger">*</span></label>
                        <input type="text" name="vCiudad" class="form-control" maxlength="80" required
                               placeholder="Ej: Ciudad de México">
                        <div class="form-text">Máximo 80 caracteres</div>
                    </div>

                    {{-- Estado --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Estado <span class="text-danger">*</span></label>
                        <input type="text" name="vEstado" class="form-control" maxlength="80" required
                               placeholder="Ej: Ciudad de México">
                        <div class="form-text">Máximo 80 caracteres</div>
                    </div>

                    {{-- Entre calles (OPCIONAL) --}}
                    <div class="col-md-6">
                        <label class="form-label">Entre calle 1</label>
                        <input type="text" name="vEntre_calle_1" class="form-control" maxlength="150"
                               placeholder="Ej: Avenida Chapultepec">
                        <div class="form-text">Opcional, máximo 150 caracteres</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Entre calle 2</label>
                        <input type="text" name="vEntre_calle_2" class="form-control" maxlength="150"
                               placeholder="Ej: Avenida Reforma">
                        <div class="form-text">Opcional, máximo 150 caracteres</div>
                    </div>

                    {{-- Referencias (OPCIONAL) --}}
                    <div class="col-12">
                        <label class="form-label">Referencias adicionales</label>
                        <textarea name="tReferencias" class="form-control" rows="3" 
                                  placeholder="Ejemplo: Frente al parque, casa con portón azul, entre calles..."
                                  maxlength="1000"></textarea>
                        <div class="form-text">Opcional, máximo 1000 caracteres</div>
                        <div class="text-end">
                            <small class="text-muted"><span id="contador-referencias">0</span>/1000 caracteres</small>
                        </div>
                    </div>

                    {{-- Dirección principal (OPCIONAL) --}}
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
                <button type="submit" class="btn btn-primary" id="btn-guardar-direccion">Guardar dirección</button>
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

<script>
    const direccionesGuardadas = {{ $direcciones->count() }};
</script>

<script>
    window.checkoutErrorUrl = "{{ route('checkout.error') }}";
</script>

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

    // VALIDAR EMAIL
    function validarEmail() {
        const emailInvitado = document.getElementById('vEmail')?.value.trim() ?? null;
        if (!emailInvitado) {
        Swal.fire('Correo requerido', 'Ingresa tu correo para continuar', 'warning');
        return false;
    }
        return true;
    }

    // =========================================
    // 5. VALIDACIÓN DE DIRECCIÓN MEJORADA
    // =========================================
    function validarDireccion() {
        const selectDireccion = document.getElementById('id_direccion');
        const valor = selectDireccion.value;

        if (direccionesGuardadas === 0) {
        if (!valor || valor === "" || parseInt(valor) <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Dirección de envío requerida',
                text: 'Debes seleccionar o agregar una dirección de envío antes de proceder con el pago.',
                confirmButtonText: 'Agregar Dirección',
                showCancelButton: true,
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Mostrar el modal de dirección
                    document.getElementById('btn-nueva-direccion').click();
                }
            });
            return false;
        }
    } else {
        // ➤ Caso 2: El usuario SÍ tiene direcciones pero NO seleccionó una
    if (!valor || valor === "" || valor === "0") {
        Swal.fire({
            icon: 'warning',
            title: 'Dirección requerida',
            text: 'Por favor selecciona una dirección para continuar.',
            confirmButtonText: 'Entendido'
        });
        return false;
    }
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

        if (!validarEmail()) {
            return;
        }

        // Validar dirección de facturación
    const usarMisma = document.getElementById('misma_direccion_facturacion').checked;
    const selectFact = document.getElementById('id_direccion_facturacion');

    if (!usarMisma) {
        if (!selectFact.value || selectFact.value === "0") {

            // Marcar select en rojo
            selectFact.classList.remove('is-valid');
            selectFact.classList.add('is-invalid');

            await Swal.fire({
                icon: "warning",
                title: "Dirección de facturación requerida",
                text: "Selecciona una dirección de facturación para continuar.",
            });

            return; // ⛔ IMPORTANTE → DETIENE STRIPE
        }
    }

        // Obtener datos del formulario
        const idDireccion = document.getElementById('id_direccion').value;
        const mismaDireccion = document.getElementById('misma_direccion_facturacion').checked;
        const idDireccionFacturacion = mismaDireccion ? idDireccion : document.getElementById('id_direccion_facturacion').value;
        const emailInvitado = document.getElementById('vEmail')?.value.trim() ?? null;
        const nota = document.getElementById('agregar_nota').checked 
    ? document.getElementById('nota_pedido').value 
    : null;

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
                    email_invitado: emailInvitado,
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
                const emailInvitado = document.getElementById('vEmail')?.value.trim() ?? null;
                const nota = document.getElementById('agregar_nota').checked 
                ? document.getElementById('nota_pedido').value 
                : null;

                return fetch("{{ route('payment.paypal.create') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        id_direccion: idDireccion,
                        id_direccion_facturacion: idDireccionFacturacion,
                        email_invitado: emailInvitado,
                        nota: nota
                    })
                })
                .then(res => res.json())
                .then(json => {
                    if (!json.success) {
                        Swal.fire({
                        icon: "error",
                        title: "No se puede continuar con el pago",
                        text: json.message || "No fue posible crear la orden de PayPal.",
                        confirmButtonText: "Entendido"
                    });
                        throw new Error(json.message || "No fue posible crear la orden de PayPal.");
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
                        window.location.href = json.redirect_url;
                    } else {
                        window.location.href = window.checkoutErrorUrl + 
        '?msg=' + encodeURIComponent(json.message ?? 'No se pudo completar el pago con PayPal.');
                    }
                });
            },

            onCancel: function () {
                Swal.fire("Cancelado", "Pago cancelado.", "info");
            },

            onError: function (err) {
                console.error('PayPal error:', err);
                window.location.href = window.checkoutErrorUrl + 
        '?msg=' + encodeURIComponent('Ocurrió un error al procesar el pago con PayPal.');
            }
        }).render('#paypal-button-container');
    }

    // =========================================
    // 8. MANEJO DE DIRECCIONES MEJORADO
    // =========================================
    const modal = new bootstrap.Modal(document.getElementById('modalDireccion'));
    const formDireccion = document.getElementById('form-nueva-direccion');
    const idInput = document.getElementById('id_direccion');
    const idEditar = document.getElementById('id_direccion_editar');
    const modalTitle = document.getElementById('modalDireccionLabel');
    const modalErrors = document.getElementById('modal-errors');
    const btnGuardar = document.getElementById('btn-guardar-direccion');
    const contadorReferencias = document.getElementById('contador-referencias');

    // Inicializar contador de referencias
    const textareaReferencias = formDireccion.querySelector('textarea[name="tReferencias"]');
    if (textareaReferencias && contadorReferencias) {
        textareaReferencias.addEventListener('input', function() {
            contadorReferencias.textContent = this.value.length;
            if (this.value.length > 900) {
                contadorReferencias.classList.add('text-warning');
            } else {
                contadorReferencias.classList.remove('text-warning');
            }
            if (this.value.length >= 1000) {
                contadorReferencias.classList.add('text-danger');
            } else {
                contadorReferencias.classList.remove('text-danger');
            }
        });
        
        // Inicializar contador
        contadorReferencias.textContent = textareaReferencias.value.length;
    }

    // Validación en tiempo real para campos numéricos
    document.querySelectorAll('input[pattern="[0-9]*"]').forEach(input => {
        input.addEventListener('input', function() {
            const valorOriginal = this.value;
            this.value = this.value.replace(/[^0-9]/g, '');
            
            // Si se eliminaron caracteres, mostrar advertencia
            if (valorOriginal !== this.value) {
                this.classList.add('is-invalid');
                setTimeout(() => this.classList.remove('is-invalid'), 2000);
            }
        });
    });

    // Validación de longitud máxima en tiempo real
    document.querySelectorAll('input[maxlength], textarea[maxlength]').forEach(input => {
        input.addEventListener('input', function() {
            if (this.value.length > this.maxLength) {
                this.value = this.value.slice(0, this.maxLength);
                this.classList.add('is-invalid');
                setTimeout(() => this.classList.remove('is-invalid'), 2000);
            }
        });
    });

    // Validación de campos requeridos en tiempo real
    document.querySelectorAll('[required]').forEach(input => {
        input.addEventListener('blur', function() {
            if (!this.value.trim()) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        });
    });

    // 🔹 Botón NUEVA DIRECCIÓN
    document.getElementById('btn-nueva-direccion')?.addEventListener('click', () => {
        formDireccion.reset();
        idEditar.value = '';
        modalTitle.textContent = 'Agregar nueva dirección';
        // Limpiar errores y estados de validación
        modalErrors.classList.add('d-none');
        modalErrors.innerHTML = '';
        formDireccion.querySelectorAll('.is-invalid, .is-valid').forEach(el => {
            el.classList.remove('is-invalid', 'is-valid');
        });
        // Reiniciar contador de referencias
        if (contadorReferencias) {
            contadorReferencias.textContent = '0';
        }
    });

    // 🔹 Botón EDITAR DIRECCIÓN
    document.getElementById('btn-editar-direccion')?.addEventListener('click', async () => {
        const id = idInput.value;
        if (!id) {
            Swal.fire({
                icon: 'warning',
                title: 'Selecciona una dirección',
                text: 'Por favor selecciona una dirección para editar.',
                confirmButtonText: 'Entendido'
            });
            return;
        }

        try {

            // Determinar si es usuario invitado (guest)
            const isGuest = {{ Auth::guest() ? 'true' : 'false' }};

            // Construir la URL de la API según el tipo de usuario
            let apiUrl;
        if (isGuest) {
            // Usuario invitado: usar endpoint para direcciones guest
            apiUrl = `/checkout/direccion-guest/${id}`;
        } else {
            // Usuario logueado: usar endpoint normal
            apiUrl = `/api/direccion/${id}`;
        }
            const res = await fetch(apiUrl);
            //const res = await fetch(`/api/direccion/${id}`);
            const data = await res.json();

            if (data.success) {
                const d = data.direccion;
                
                // Limpiar errores y estados de validación
                modalErrors.classList.add('d-none');
                modalErrors.innerHTML = '';
                formDireccion.querySelectorAll('.is-invalid, .is-valid').forEach(el => {
                    el.classList.remove('is-invalid', 'is-valid');
                });

                // Llenar formulario con datos
                for (const key in d) {
                    if (formDireccion.elements[key]) {
                        formDireccion.elements[key].value = d[key] || '';
                    }
                }

                // Manejar teléfono (separar código de país)
                if (d.vTelefono_contacto) {
                    const telefonoCompleto = d.vTelefono_contacto;
                    // Asumimos que el formato es "+52 5512345678"
                    const partes = telefonoCompleto.split(' ');
                    if (partes.length >= 2) {
                        document.getElementById('codigo_pais').value = partes[0];
                        document.getElementById('vTelefono_contacto').value = partes.slice(1).join(' ');
                    } else {
                        // Si no tiene código, asumimos México
                        document.getElementById('codigo_pais').value = '+52';
                        document.getElementById('vTelefono_contacto').value = telefonoCompleto;
                    }
                }

                // Checkbox de dirección principal
                const checkPrincipal = document.getElementById('checkPrincipal');
                checkPrincipal.checked = (parseInt(d.bDireccion_principal) === 1);

                // Actualizar contador de referencias
                if (textareaReferencias && contadorReferencias) {
                    contadorReferencias.textContent = textareaReferencias.value.length;
                }

                // Configurar modal para edición
                idEditar.value = id;
                modalTitle.textContent = 'Editar dirección';
                
                // Mostrar modal
                modal.show();

            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'No se pudo cargar la dirección',
                    confirmButtonText: 'Entendido'
                });
            }
        } catch (err) {
            console.error('Error al obtener la dirección:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al obtener la dirección.',
                confirmButtonText: 'Entendido'
            });
        }
    });

    // 🔹 GUARDAR (crear o actualizar) con validaciones
    formDireccion.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        // Ocultar errores anteriores
        modalErrors.classList.add('d-none');
        modalErrors.innerHTML = '';

        // Validar campos requeridos
        const camposRequeridos = formDireccion.querySelectorAll('[required]');
        let errores = [];
        let hayErrores = false;

        camposRequeridos.forEach(campo => {
            campo.classList.remove('is-invalid', 'is-valid');
            
            if (!campo.value.trim()) {
                campo.classList.add('is-invalid');
                const label = formDireccion.querySelector(`label[for="${campo.id}"]`) || 
                             campo.closest('.form-group')?.querySelector('label') ||
                             campo.previousElementSibling;
                const nombreCampo = label ? label.textContent.replace('*', '').replace('📞', '').replace('🏠', '').trim() : 'Este campo';
                errores.push(`❌ <strong>${nombreCampo}</strong> es obligatorio`);
                hayErrores = true;
            } else {
                campo.classList.add('is-valid');
            }
        });

        // Validaciones específicas
        const telefono = document.getElementById('vTelefono_contacto').value;
        if (telefono && telefono.length < 10) {
            errores.push('❌ <strong>Teléfono</strong> debe tener al menos 10 dígitos');
            document.getElementById('vTelefono_contacto').classList.add('is-invalid');
            hayErrores = true;
        }

        const codigoPostal = document.querySelector('input[name="vCodigo_postal"]').value;
        if (codigoPostal && codigoPostal.length < 5) {
            errores.push('❌ <strong>Código postal</strong> debe tener al menos 5 dígitos');
            document.querySelector('input[name="vCodigo_postal"]').classList.add('is-invalid');
            hayErrores = true;
        }

        // Si hay errores, mostrarlos
        if (hayErrores) {
            modalErrors.innerHTML = '<h6 class="alert-heading">Por favor corrige los siguientes errores:</h6>' + 
                                   errores.join('<br>');
            modalErrors.classList.remove('d-none');
            
            // Hacer scroll hasta los errores
            modalErrors.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        // Deshabilitar botón para evitar múltiples envíos
        btnGuardar.disabled = true;
        btnGuardar.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Guardando...';

        await guardarDireccion(formDireccion, modalErrors, btnGuardar);
    });

    // Función para guardar dirección
    async function guardarDireccion(formDireccion, modalErrors, btnGuardar) {
        const formData = new FormData(formDireccion);
        const id = document.getElementById('id_direccion_editar').value;

        // Combinar código de país con teléfono (respetando máximo 20 caracteres)
        const codigoPais = document.getElementById('codigo_pais').value;
        const telefono = document.getElementById('vTelefono_contacto').value;
        const telefonoCompleto = codigoPais + ' ' + telefono;
        
        // Asegurar que no exceda 20 caracteres
        if (telefonoCompleto.length > 20) {
            // Si excede, truncar el número de teléfono
            const espacioDisponible = 20 - (codigoPais.length + 1); // +1 por el espacio
            const telefonoAjustado = telefono.slice(0, espacioDisponible);
            formData.set('vTelefono_contacto', codigoPais + ' ' + telefonoAjustado);
        } else {
            formData.set('vTelefono_contacto', telefonoCompleto);
        }

        // Forzar Laravel a reconocer PUT si es edición
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
                // Cerrar modal y mostrar éxito
                modal.hide();
                
                Swal.fire({
                    icon: 'success',
                    title: id ? '¡Dirección actualizada!' : '¡Dirección creada!',
                    text: id ? 'Tu dirección se ha actualizado correctamente.' : 'Tu dirección se ha guardado correctamente.',
                    confirmButtonText: 'Entendido',
                    timer: 3000,
                    timerProgressBar: true
                });

                // Actualizar la interfaz
                await actualizarInterfazDirecciones(data, id);

            } else {
                // Mostrar errores del backend
                if (data.errors) {
                    let erroresBackend = [];
                    for (const campo in data.errors) {
                        const nombreCampo = {
                            'vNombre': 'Nombre',
                            'vApaterno': 'Apellido paterno',
                            'vAmaterno': 'Apellido materno',
                            'vTelefono_contacto': 'Teléfono de contacto',
                            'vRFC': 'RFC',
                            'vCalle': 'Calle',
                            'vNumero_exterior': 'Número exterior',
                            'vColonia': 'Colonia',
                            'vCodigo_postal': 'Código postal',
                            'vCiudad': 'Ciudad',
                            'vEstado': 'Estado'
                        }[campo] || campo;
                        
                        erroresBackend.push(`❌ <strong>${nombreCampo}:</strong> ${data.errors[campo].join(', ')}`);
                    }
                    modalErrors.innerHTML = '<h6 class="alert-heading">Errores del servidor:</h6>' + 
                                           erroresBackend.join('<br>');
                } else {
                    modalErrors.innerHTML = `❌ ${data.message || 'Error al guardar la dirección'}`;
                }
                modalErrors.classList.remove('d-none');
                modalErrors.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }

        } catch (err) {
            console.error('Error de conexión:', err);
            modalErrors.innerHTML = '❌ Error de conexión. Verifica tu internet e intenta nuevamente.';
            modalErrors.classList.remove('d-none');
        } finally {
            // Rehabilitar botón
            btnGuardar.disabled = false;
            btnGuardar.innerHTML = 'Guardar dirección';
        }
    }

    // Función para actualizar la interfaz después de guardar
    async function actualizarInterfazDirecciones(data, id) {
        const idInput = document.getElementById('id_direccion');
        const idFacturacion = document.getElementById('id_direccion_facturacion');

        if (!id) {
            // Nueva dirección - agregar a ambos selects
            const nuevaOpcion = (value, text) => {
                const opt = document.createElement('option');
                opt.value = value;
                opt.textContent = text;
                return opt;
            };

            const textoDireccion = `${data.direccion.vCalle} ${data.direccion.vNumero_exterior}, ${data.direccion.vColonia}, ${data.direccion.vCiudad}`;
            
            idInput.appendChild(nuevaOpcion(data.direccion.id_direccion, textoDireccion));
            idInput.value = "";
            idInput.classList.remove("is-valid");
            idInput.classList.remove("is-invalid");
            
            idFacturacion.appendChild(nuevaOpcion(data.direccion.id_direccion, textoDireccion));

            // Ocultar mensaje de advertencia si existe
            const warningMessage = idInput.parentElement.querySelector('.text-warning');
            if (warningMessage) {
                warningMessage.style.display = 'none';
            }
        } else {
            // Edición - actualizar texto en ambos selects
            const textoActualizado = `${data.direccion.vCalle} ${data.direccion.vNumero_exterior}, ${data.direccion.vColonia}, ${data.direccion.vCiudad}`;
            
            const opcionEnvio = idInput.querySelector(`option[value="${id}"]`);
            if (opcionEnvio) opcionEnvio.textContent = textoActualizado;

            const opcionFacturacion = idFacturacion.querySelector(`option[value="${id}"]`);
            if (opcionFacturacion) opcionFacturacion.textContent = textoActualizado;
        }

        // 🔥 Ocultar el mensaje "No tienes direcciones guardadas" dinámicamente
const warningMessage = document.querySelector('#id_direccion')
    .closest('.mb-3')
    .querySelector('.text-warning');

if (warningMessage) {
    warningMessage.style.display = 'none';
}

// 🌟 Marcar el select de dirección como válido inmediatamente
const selectEnvio = document.getElementById('id_direccion');
selectEnvio.classList.remove('is-invalid');
selectEnvio.classList.add('is-valid');

// 🌟 Marcar el select de facturación como válido inmediatamente
const selectFact = document.getElementById('id_direccion_facturacion');
selectFact.classList.remove('is-invalid');
selectFact.classList.add('is-valid');

    }

    // VALIDAR SELECT DE LA DIRECCION EN TIEMPO REAL
    document.getElementById('id_direccion').addEventListener('change', function () {
    const select = this;

    if (select.value === "" || select.value === "0") {
        // Valor inválido → borde rojo e ícono warning
        select.classList.remove('is-valid');
        select.classList.add('is-invalid');
    } else {
        // Valor válido → borde verde y palomita
        select.classList.remove('is-invalid');
        select.classList.add('is-valid');
    }
});

// VALIDAR SELECT DE FACTURACIÓN EN TIEMPO REAL
document.getElementById('id_direccion_facturacion').addEventListener('change', function () {
    const select = this;

    if (select.value === "" || select.value === "0") {
        select.classList.remove('is-valid');
        select.classList.add('is-invalid');
    } else {
        select.classList.remove('is-invalid');
        select.classList.add('is-valid');
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
/* Estilos para validaciones visuales */
.is-valid {
    border-color: #198754 !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.is-invalid {
    border-color: #dc3545 !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

/* Contador de caracteres */
.text-warning {
    color: #ffc107 !important;
    font-weight: bold;
}

.text-danger {
    color: #dc3545 !important;
    font-weight: bold;
}

/* Campos obligatorios */
label .text-danger {
    font-size: 1.2em;
}

/* --- Contenedor del botón de PayPal --- */
#paypal-button-container {
    max-width: 330px; /* MISMO ANCHO APROX. QUE EL BOTÓN DE STRIPE */
    width: 100%;
    margin: 0 auto; /* Centrado */
    display: flex;
    justify-content: center;
}

/* --- Forzar que el iframe también respete el ancho --- */
#paypal-button-container iframe {
    width: 100% !important;
    max-width: 100% !important;
}

</style>

@endsection