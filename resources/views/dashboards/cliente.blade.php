@php
use Illuminate\Support\Str;
@endphp

@extends('layouts.app')

@section('title', 'Panel del Cliente')

@section('content')
<div class="container my-4">

    {{-- 👋 Mensaje de bienvenida --}}
    @auth
        <h1 class="mb-3">Bienvenido, {{ Auth::user()->vNombre }} 👋</h1>
        <p class="text-muted">
            Desde aquí puedes gestionar tus pedidos, direcciones y métodos de pago.
        </p>
    @endauth

    @guest
        <h1 class="mb-3">Bienvenido a la tienda en línea 🛍️</h1>
        <p class="text-muted">
            Explora nuestros productos y realiza tus compras de manera fácil y segura.
        </p>
    @endguest


    {{-- 🛒 Sección de productos --}}
    <hr class="my-4">
    <h2 class="mb-4">Explora nuestros productos</h2>

    @if(isset($productos) && $productos->isNotEmpty())
        <div class="row">
            @foreach($productos as $producto)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $producto->vNombre }}</h5>
                            <p class="card-text text-muted mb-2">
                                Precio: ${{ number_format($producto->dPrecio_venta, 2) }}
                            </p>

                            {{-- Descripción opcional --}}
                            @if(!empty($producto->vDescripcion))
                                <p class="small text-secondary flex-grow-1">
                                    {{ Str::limit($producto->vDescripcion, 80) }}
                                </p>
                            @endif

                            {{-- Botón para agregar al carrito --}}
                            <form action="{{ route('carrito.store', $producto->id_producto) }}" method="POST" class="mt-auto">
                                @csrf
                                <div class="d-flex align-items-center">
                                    <input type="number" name="cantidad" value="1" min="1" class="form-control w-25 me-2" required>
                                    <button type="submit" class="btn btn-success btn-sm">
                                        🛒 Agregar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info text-center">
            No hay productos disponibles por ahora. ¡Vuelve pronto! 😊
        </div>
    @endif
</div>
@endsection
