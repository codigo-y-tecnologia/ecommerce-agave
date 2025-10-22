@extends('layouts.app')

@section('title', 'Editar Cliente')

@push('scripts')
    @vite(['resources/js/admin/editar-usuario.js'])
@endpush

@section('content')
<div class="container">
    <h2 class="mb-4 text-center">✏️ Editar Cliente</h2>

    <!-- Mostrar errores de validación -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" id="editClientForm" action="{{ route('admin.usuarios.update', $usuario->id_usuario) }}" class="shadow-sm p-4 bg-white rounded">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="vNombre" class="form-label">Nombre</label>
            <input type="text" name="vNombre" id="vNombre" value="{{ old('vNombre', $usuario->vNombre) }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="vApaterno" class="form-label">Apellido Paterno</label>
            <input type="text" name="vApaterno" id="vApaterno" value="{{ old('vApaterno', $usuario->vApaterno) }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="vAmaterno" class="form-label">Apellido Materno</label>
            <input type="text" name="vAmaterno" id="vAmaterno" value="{{ old('vAmaterno', $usuario->vAmaterno) }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="vEmail" class="form-label">Correo electrónico</label>
            <input type="email" name="vEmail" id="vEmail" value="{{ old('vEmail', $usuario->vEmail) }}" class="form-control" required>
        </div>

        <div class="text-end">
            <a href="{{ route('admin.usuarios') }}" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </div>
    </form>
</div>
@endsection
