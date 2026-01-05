@extends('layouts.admins')

@section('title', 'Reportes')

@section('content')
<div class="container mt-5">
    <h2 class="fw-bold mb-4 text-center">📊 Reportes</h2>

    <div class="list-group shadow-sm">
        <a href="{{ route('ventas.index') }}" class="list-group-item list-group-item-action">
            📈 Ventas
        </a>
        <a href="{{ route('detalle_venta.index') }}" class="list-group-item list-group-item-action">
            🔍 Detalle de Ventas
        </a>
    </div>
</div>
@endsection
