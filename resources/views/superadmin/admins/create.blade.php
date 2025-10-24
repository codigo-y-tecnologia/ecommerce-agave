@extends('layouts.app')

@section('title', 'Crear nuevo administrador')

@section('content')
<div class="container py-4">
    <h2 class="mb-4 text-center">🧑‍💼 Crear nuevo administrador</h2>

    <form action="{{ route('superadmin.admins.store') }}" method="POST" class="card shadow-sm p-4">
        @csrf
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="vNombre" class="form-label">Nombre</label>
                <input type="text" name="vNombre" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label for="vApaterno" class="form-label">Apellido paterno</label>
                <input type="text" name="vApaterno" class="form-control">
            </div>
            <div class="col-md-4">
                <label for="vAmaterno" class="form-label">Apellido materno</label>
                <input type="text" name="vAmaterno" class="form-control">
            </div>
        </div>

        <div class="mb-3">
            <label for="vEmail" class="form-label">Correo electrónico</label>
            <input type="email" name="vEmail" class="form-control" required>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-primary">Crear administrador</button>
        </div>
    </form>
</div>
@endsection
