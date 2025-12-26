@extends('layouts.admins')

@section('title', 'Panel de Administración')

@section('content')
<div class="text-center mb-5">
    <h2>👨‍💼 Bienvenido, {{ Auth::user()->vNombre }}</h2>
    <p class="text-muted">Desde aquí puedes gestionar el catálogo, usuarios y operaciones del ecommerce.</p>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card border-warning shadow-sm">
            <div class="card-body text-center">
                <h5>👥 Usuarios Registrados</h5>
                <p>Consulta, edita o elimina usuarios.</p>
                <a href="{{ route('admin.usuarios') }}" class="btn btn-warning w-100">Ver usuarios</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-success shadow-sm">
            <div class="card-body text-center">
                <h5>💸 Cupones</h5>
                <p>Gestiona cupones de descuento.</p>
                <a href="{{ route('cupones.index') }}" class="btn btn-success w-100">Administrar cupones</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-secondary shadow-sm">
            <div class="card-body text-center">
                <h5>💰 Impuestos</h5>
                <p>Agrega o modifica las tasas de impuestos.</p>
                <a href="{{ route('impuestos.index') }}" class="btn btn-secondary w-100">Ver impuestos</a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-info shadow-sm">
            <div class="card-body text-center">
                <h5>🛍️ Productos</h5>
                <p>Gestiona los productos del catálogo.</p>
                <a href="{{ route('productos.index') }}" class="btn btn-info w-100">Administrar productos</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-primary shadow-sm">
            <div class="card-body text-center">
                <h5>📦 Pedidos</h5>
                <p>Consulta y actualiza el estado de los pedidos.</p>
                <a href="{{ route('admin.pedidos.index') }}" class="btn btn-primary w-100">Ver pedidos</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-danger shadow-sm">
            <div class="card-body text-center">
                <h5>📊 Reportes</h5>
                <p>Genera reportes de ventas, usuarios y más.</p>
                <a href="{{ route('reportes.index') }}" class="btn btn-danger w-100">Ver reportes</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
    <div class="card border-dark shadow-sm">
        <div class="card-body text-center">
            <h5>🔄 Reembolsos</h5>
            <p>Gestiona solicitudes y estados de reembolsos.</p>
            <a href="{{ route('reembolsos.index') }}" class="btn btn-dark w-100">
                Administrar reembolsos
            </a>
        </div>
    </div>
</div>
</div>
@endsection
