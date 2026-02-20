@extends('layouts.admins')

@section('title', 'Configuración de la tienda')

@section('content')
<div class="container py-4">

    <h2 class="mb-4 text-center">⚙️ Configuración de la tienda</h2>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- 👤 Clientes y Checkout --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">👤 Clientes y Checkout</h5>
        </div>
        <div class="card-body">

            <form method="POST" action="{{ route('admin.settings.auto-register') }}">
                @csrf

                <div class="form-check form-switch">
                    <input class="form-check-input"
                           type="checkbox"
                           name="value"
                           onchange="this.form.submit()"
                           {{ \App\Models\Setting::getValue('auto_register_guest_after_purchase') ? 'checked' : '' }}>

                    <label class="form-check-label fw-bold">
                        Registrar automáticamente a invitados después del pago
                    </label>
                </div>

                <small class="text-muted d-block mt-2">
                    Si está activado, los clientes que compren como invitados
                    se registrarán automáticamente usando su correo después del pago.
                    Si está desactivado, la compra se completará sin crear una cuenta.
                </small>

            </form>

        </div>
    </div>

    {{-- 📦 Pedidos y Reembolsos --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">📦 Pedidos y Reembolsos</h5>
        </div>
        <div class="card-body">

            <form method="POST" action="{{ route('admin.settings.allow-returns') }}">
                @csrf

                <div class="form-check form-switch">
                    <input class="form-check-input"
                           type="checkbox"
                           name="value"
                           onchange="this.form.submit()"
                           {{ \App\Models\Setting::getValue('allow_order_returns') ? 'checked' : '' }}>

                    <label class="form-check-label fw-bold">
                        Permitir devoluciones y cancelaciones de pedidos
                    </label>
                </div>

                <small class="text-muted d-block mt-2">
                    Al desactivar esta opción, los clientes no podrán solicitar
                    devoluciones ni cancelaciones después de completar un pedido.
                    Esta acción afecta únicamente a pedidos nuevos.
                </small>

            </form>

        </div>
    </div>

</div>
@endsection
