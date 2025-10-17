@extends('layouts.app')
@section('title', 'Panel de Administración')

@section('content')
<h1 class="mb-4">Panel del Administrador</h1>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card border-primary shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Usuarios</h5>
                <p class="card-text">Gestiona los usuarios registrados en el sistema.</p>
                <a href="#" class="btn btn-primary w-100">Ver usuarios</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-warning shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Cupones</h5>
                <p class="card-text">Crea, edita o elimina cupones promocionales.</p>
                <a href="#" class="btn btn-warning w-100">Gestionar cupones</a>
            </div>
        </div>
    </div>
</div>
@endsection
