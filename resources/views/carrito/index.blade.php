@extends('layouts.app')

@section('title', 'Mi Carrito de Compras')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">🛒 Mi Carrito de Compras</h2>

    <!-- Mostrar mensaje si el carrito está vacío -->
    @if(session('carrito_vacio'))
        <div class="alert alert-info">
            {{ session('carrito_vacio') }}
        </div>
    @endif

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

    @if(session('warning'))
    <div class="alert alert-warning">
        {{ session('warning') }}
    </div>
@endif

@if(!empty($warning))
    <div class="alert alert-warning">
        <ul class="mb-0">
            @foreach($warning as $mensaje)
                <li>{{ $mensaje }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(!empty($carrito_vacio))
    <div class="alert alert-info">
        {{ $carrito_vacio }}
    </div>
@endif

@if(session('info'))
    <div class="alert alert-info">{{ session('info') }}</div>
@endif

    <!-- Tabla de productos en el carrito -->
    <table class="table table-bordered">
    <thead class="table-light">
        <tr>
            <th>Producto</th>
            <th>Precio Unitario</th>
            <th>Cantidad</th>
            <th>Subtotal</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse($detalles as $detalle)
        <tr>
            <!-- Producto -->
            <td>{{ $detalle->producto->vNombre }}</td>

            <!-- Precio unitario -->
            <td>
    ${{ number_format($detalle->producto->precio_con_impuestos, 2) }}
</td>

            <!-- Cantidad con formulario de actualización -->
            <td>
                <form action="{{ route('carrito.update', $detalle->id_detalle_carrito) }}" method="POST" class="d-flex align-items-center">
                    @csrf
                    @method('PUT')
                    <input type="number" name="cantidad" value="{{ $detalle->iCantidad }}" min="1" class="form-control w-50 me-2">
                    <button type="submit" class="btn btn-sm btn-primary">Actualizar</button>
                </form>
            </td>

            <!-- Subtotal -->
            <td>
    ${{ number_format($detalle->producto->precio_con_impuestos * $detalle->iCantidad, 2) }}
</td>

            <!-- Acciones -->
            <td>
                <form action="{{ route('carrito.destroy', $detalle->id_detalle_carrito) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center">Tu carrito está vacío.</td>
        </tr>
        @endforelse
    </tbody>
</table>

    <!-- Resumen -->
    @if(isset($total) && $total > 0)
    <div class="text-end">
        @php
    $total = $detalles->sum(fn($d) => $d->producto->precio_con_impuestos * $d->iCantidad);
@endphp
<h4>Total: ${{ number_format($total, 2) }}</h4>
        <a href="{{ route('checkout.index') }}" class="btn btn-success">Finalizar Compra</a>
    </div>
    @endif
</div>

<!-- Bootstrap JS (opcional, solo si usarás cosas como modal o dropdowns) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@endsection