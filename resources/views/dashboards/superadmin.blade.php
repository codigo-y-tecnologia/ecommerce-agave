@extends('layouts.app')
@section('title', 'Panel del Superadmin')

@section('content')
<h1 class="mb-4">Panel del Superadministrador</h1>

<div class="alert alert-dark">
    <strong>⚙️ Funciones críticas:</strong> supervisa la infraestructura, usuarios administrativos y seguridad del sistema.
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card border-danger shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Gestión de administradores</h5>
                <p class="card-text">Agrega o quita administradores del sistema.</p>
                <a href="#" class="btn btn-danger w-100">Administrar</a>
            </div>
        </div>
    </div>
</div>
@endsection
