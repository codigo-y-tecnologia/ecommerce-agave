@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">🧾 Resumen de tu pedido</h2>

    {{-- Mensajes --}}
    @if(session('warning')) <div class="alert alert-warning">{{ session('warning') }}</div> @endif

    <form action="{{ route('checkout.store') }}" method="POST" class="card p-4 shadow-sm">
        @csrf

        {{-- 🛒 Resumen de productos --}}
        <table class="table table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio unitario</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($carrito->detalles as $detalle)
                    <tr>
                        <td>{{ $detalle->producto->vNombre }}</td>
                        <td>{{ $detalle->cantidad }}</td>
                        <td>${{ number_format($detalle->precio_unitario, 2) }}</td>
                        <td>${{ number_format($detalle->cantidad * $detalle->precio_unitario, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- 🎟 Cupón --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Código de cupón (opcional)</label>
            <input type="text" name="codigo_cupon" class="form-control" placeholder="AGAVE2025">
        </div>

        {{-- 🚚 Dirección de envío --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Dirección de envío</label>
            <select name="id_direccion" class="form-select" required>
                <option value="">Selecciona una dirección...</option>
                @foreach ($direcciones as $direccion)
                    <option value="{{ $direccion->id_direccion }}">
                        {{ $direccion->vCalle }} {{ $direccion->vNumero_exterior }}, {{ $direccion->vCiudad }}
                    </option>
                @endforeach
            </select>
            <a href="#" class="small d-block mt-1">+ Agregar nueva dirección</a>
        </div>

        {{-- 💳 Método de pago --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Método de pago</label>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="metodo_pago" value="paypal" required>
                <label class="form-check-label">PayPal</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="metodo_pago" value="stripe">
                <label class="form-check-label">Tarjeta (Stripe)</label>
            </div>
        </div>

        {{-- 💰 Total --}}
        <div class="text-end">
            <h4>Total a pagar: <strong>${{ number_format($subtotal, 2) }}</strong></h4>
        </div>

        <p class="text-end text-muted">
  Subtotal sin impuestos: ${{ number_format($subtotal, 2) }}<br>
  IVA (16%): ${{ number_format($subtotal * 0.16, 2) }}<br>
  <strong>Total con impuestos: ${{ number_format($subtotal * 1.16, 2) }}</strong>
</p>

        {{-- 🔘 Botones --}}
        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('carrito.index') }}" class="btn btn-secondary">Volver al carrito</a>
            <button type="submit" class="btn btn-success">Realizar pedido</button>
        </div>
    </form>
</div>
@endsection
