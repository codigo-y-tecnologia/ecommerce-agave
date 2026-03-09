@extends('layouts.admins')

@section('content')
<h2>✏️ Editar permiso</h2>

@include('superadmin.partials.alerts')

<form method="POST" action="{{ route('permissions.update', $permission) }}">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label>Nombre del permiso</label>
        <input type="text" name="name"
               class="form-control"
               value="{{ old('name', $permission->name) }}" required>
    </div>

    <button class="btn btn-warning">Actualizar</button>
    <a href="{{ route('permissions.index') }}" class="btn btn-secondary">Cancelar</a>
</form>
@endsection
