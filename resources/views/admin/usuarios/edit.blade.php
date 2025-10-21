@extends('layouts.app')

@section('title', 'Editar Usuario')

@section('content')
<div class="container">
    <h2 class="mb-4 text-center">✏️ Editar Usuario</h2>

    <form method="POST" action="{{ route('admin.usuarios.update', $usuario->id_usuario) }}" class="shadow-sm p-4 bg-white rounded">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="vNombre" class="form-label">Nombre</label>
            <input type="text" name="vNombre" id="vNombre" value="{{ old('vNombre', $usuario->vNombre) }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="vEmail" class="form-label">Correo electrónico</label>
            <input type="email" name="vEmail" id="vEmail" value="{{ old('vEmail', $usuario->vEmail) }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="eRol" class="form-label">Rol</label>
            <select name="eRol" id="eRol" class="form-select" required>
                <option value="cliente" @selected($usuario->eRol === 'cliente')>Cliente</option>
                <option value="admin" @selected($usuario->eRol === 'admin')>Administrador</option>
                <option value="superadmin" @selected($usuario->eRol === 'superadmin')>Superadmin</option>
            </select>
        </div>

        <div class="text-end">
            <a href="{{ route('admin.usuarios') }}" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </div>
    </form>
</div>
@endsection
