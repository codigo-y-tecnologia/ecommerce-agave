@extends('layouts.app')

@section('title', 'Mi Perfil')

@section('content')
<div class="container mt-5">
    <h2 class="fw-bold mb-4 text-center">👤 Mi Perfil</h2>

    <div class="list-group shadow-sm">
        <a href="{{ route('direcciones.index') }}" class="list-group-item list-group-item-action">
            📍 Mis direcciones de envío
        </a>
        <a href="{{ route('perfil.configuracion') }}" class="list-group-item list-group-item-action">
            ⚙️ Configuración de cuenta
        </a>
        <a href="#" class="list-group-item list-group-item-action disabled">
            🧾 Historial de pedidos (próximamente)
        </a>
    </div>
</div>
@endsection
