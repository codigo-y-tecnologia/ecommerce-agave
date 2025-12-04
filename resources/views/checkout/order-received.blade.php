@extends('layouts.app')

@section('title', 'Orden Recibida')

@section('content')
<div class="container py-5">

    <div class="text-center mb-5">
        <div class="display-4 text-success mb-3">
            <i class="bi bi-check-circle-fill"></i>
        </div>
        <h1 class="fw-bold">¡Orden Recibida!</h1>
        <p class="text-muted fs-5">Gracias por tu compra, {{ $pedido->usuario->vNombre }}.</p>
    </div>

    {{-- Información de la orden --}}
    <div class="row justify-content-center mb-4">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body">

                    <h4 class="fw-bold mb-3">Detalles del Pedido</h4>

                    <ul class="list-group mb-4">
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>ID del Pedido:</strong>
                            <span>#{{ $pedido->id_pedido }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between">
                            <strong>Fecha:</strong>
                            <span>{{ $pedido->tFecha_pedido->format('d/m/Y H:i') }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between">
                            <strong>Total:</strong>
                            <span class="fw-bold">${{ number_format($pedido->dTotal, 2) }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between">
                            <strong>Método de Pago:</strong>
                            <span class="text-capitalize">{{ $payment_method }}</span>
                        </li>

                        @if($nota_pedido)
                        <li class="list-group-item">
                            <strong>Nota del cliente:</strong>
                            <p class="mt-2">{{ $nota_pedido }}</p>
                        </li>
                        @endif
                    </ul>

                    {{-- Dirección --}}
                    <h4 class="fw-bold mb-3">Dirección de Envío</h4>
                    <p class="mb-4">
                        {{ $direccion->vCalle }} {{ $direccion->vNumero_exterior }},
                        {{ $direccion->vColonia }}, {{ $direccion->vCiudad }},
                        {{ $direccion->vEstado }}, C.P. {{ $direccion->vCodigo_postal }}<br>
                        <strong>Tel:</strong> {{ $direccion->vTelefono_contacto }}
                    </p>

                </div>
            </div>
        </div>
    </div>

    {{-- Tabla productos --}}
    <div class="row justify-content-center">
        <div class="col-md-10">
            <h3 class="fw-bold mb-3">Productos</h3>
            <div class="table-responsive shadow-sm">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Producto</th>
                            <th class="text-center">Cant.</th>
                            <th class="text-end">Precio</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pedido->detalles as $det)
                        <tr>
                            <td>{{ $det->producto->vNombre }}</td>
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
    </div>

    {{-- Botón --}}
    <div class="text-center mt-5">
        <a href="{{ route('home') }}" class="btn btn-success btn-lg px-5">
            Seguir comprando
        </a>
    </div>

</div>
@endsection
