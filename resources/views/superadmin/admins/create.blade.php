@extends('layouts.app')

@section('title', 'Crear Administrador')

@push('scripts')
    <script src="{{ asset('js/superadmin/crear-admin.js') }}" defer></script>
@endpush

@section('content')
<div class="container py-4">
    <h2 class="mb-4 text-center">🧑‍💼 Crear nuevo administrador</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" id="createAdminForm" action="{{ route('superadmin.admins.store') }}" class="card shadow-sm p-4">
        @csrf
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Nombre</label>
                <input type="text" name="vNombre" class="form-control" value="{{ old('vNombre') }}" maxlength="60" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Apellido paterno</label>
                <input type="text" name="vApaterno" class="form-control" value="{{ old('vApaterno') }}"maxlength="50" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Apellido materno</label>
                <input type="text" name="vAmaterno" class="form-control" value="{{ old('vAmaterno') }}" maxlength="50" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Correo electrónico</label>
            <input type="email" id="vEmail" name="vEmail" class="form-control" value="{{ old('vEmail') }}" maxlength="100" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Fecha de nacimiento</label>
            <input type="date" name="dFecha_nacimiento" class="form-control" value="{{ old('dFecha_nacimiento') }}" required>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-primary">Crear administrador</button>
        </div>
    </form>
</div>
@endsection
