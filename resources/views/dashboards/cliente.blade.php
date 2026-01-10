@extends('layouts.app')
@section('title', 'Panel del Cliente')

@section('content')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('warning'))
    <div class="alert alert-warning">{{ session('warning') }}</div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('info'))
    <div class="alert alert-info">{{ session('info') }}</div>
@endif

@auth
<h1 class="mb-4">Bienvenido {{ Auth::user()->vNombre }}</h1>
<p>Desde aquí puedes gestionar tus pedidos, direcciones y métodos de pago.</p>
@endauth

<div class="row g-4">
    <div class="col-md-4">
        <div class="card border-success shadow-sm">
            <div class="card-body">
                <h5 class="card-title">🛒 Mi Carrito</h5>
                <p class="card-text">Consulta o modifica los productos agregados a tu carrito.</p>
                <a href="#" class="btn btn-success w-100">Ir al carrito</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-info shadow-sm">
            <div class="card-body">
                <h5 class="card-title">📦 Mis Pedidos</h5>
                <p class="card-text">Consulta el estado de tus pedidos realizados.</p>
                <a href="#" class="btn btn-info w-100">Ver pedidos</a>
            </div>
        </div>
    </div>
</div>
@endsection
