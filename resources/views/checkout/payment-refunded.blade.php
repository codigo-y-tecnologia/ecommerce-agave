@extends('layouts.app')

@section('title', 'Pago reembolsado')

@section('content')
<div class="container mt-5 text-center">

    <h2 class="fw-bold text-danger mb-3">⚠️ Pago reembolsado</h2>

    <p class="text-muted">
        Uno o más productos se quedaron sin stock.<br>
        Tu pago fue reembolsado automáticamente.
    </p>

    <a href="{{ route('home') }}" class="btn btn-primary mt-4">
        Volver a la tienda
    </a>

</div>
@endsection
