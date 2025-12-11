@extends('layouts.app')

@section('title', 'No pudimos confirmar tu pedido')

@section('content')

<div class="container py-5" style="max-width: 720px;">

    <!-- Header estilo Amazon -->
    <div class="text-center mb-4">
        <h2 class="fw-bold">No pudimos confirmar tu pedido</h2>
        <p class="text-muted">
            Ha ocurrido un problema al validar la confirmación del pago.
        </p>
    </div>

    <!-- Tarjeta tipo WooCommerce / Shopify -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">

            <h5 class="fw-bold mb-3">¿Qué significa esto?</h5>
            <p class="text-muted">
                Parece que tu pago se procesó, pero no pudimos recibir la confirmación final 
                desde nuestro sistema. Esto suele ocurrir cuando:
            </p>

            <ul class="text-muted">
                <li>La conexión a internet se interrumpió justo después de pagar.</li>
                <li>El proveedor de pago tardó demasiado en enviarnos la confirmación.</li>
                <li>Cerraste la ventana antes de completar la redirección.</li>
                <li>El navegador bloqueó la página de retorno.</li>
            </ul>

            <hr>

            <h5 class="fw-bold mb-3">¿Tu pago se realizó?</h5>
            <p class="text-muted">
                Si completaste el pago, es muy probable que <strong>sí se haya procesado correctamente</strong>.
                Nuestro equipo puede verificarlo manualmente revisando tu transacción.
            </p>

            <!-- Botón estilo Shopify -->
            <div class="mt-4 d-grid gap-3">

                <a href="{{ route('checkout.index') }}" 
                   class="btn btn-primary btn-lg">
                    Volver al checkout
                </a>

                <a href="{{ route('soporte.form') }}" 
                   class="btn btn-outline-secondary btn-lg">
                    Contactar a soporte
                </a>
            </div>

        </div>
    </div>

    <div class="text-center mt-4 text-muted">
        <small>
            Si pagaste, no te preocupes: tu transacción está protegida.
            Nuestro equipo puede ayudarte a confirmar tu pedido.
        </small>
    </div>

</div>

@endsection
