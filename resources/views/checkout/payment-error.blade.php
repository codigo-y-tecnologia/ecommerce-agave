@extends('layouts.app')

@section('title', 'Error en el pago')

@section('content')
<div class="container mt-5">

    <div class="text-center mb-4">
        <h2 class="fw-bold text-danger">Hubo un problema con tu pago</h2>
        <p class="text-muted">{{ $mensaje }}</p>
    </div>

    <div class="card shadow-sm p-4">
        <h5 class="fw-bold mb-3">¿Qué puedes hacer?</h5>

        <ul class="list-unstyled">
            <li class="mb-2">• Verifica tu conexión a internet.</li>
            <li class="mb-2">• Intenta con otra tarjeta o método de pago.</li>
            <li class="mb-2">• Si crees que tu pago sí se realizó, revisa tu correo o contáctanos.</li>
        </ul>

        <div class="text-center mt-4">
            <a href="{{ route('checkout.index') }}" class="btn btn-primary px-4">
                Volver al checkout
            </a>

            <a href="{{ route('soporte.form') }}" class="btn btn-outline-primary btn-lg px-5">
        Contactar soporte
    </a>
        </div>
    </div>

</div>
@endsection
