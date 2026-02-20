@extends('layouts.admins')

@section('title', 'Crear permiso')

@section('content')
<div class="container">
    <h2 class="mb-4">➕ Crear nuevo permiso</h2>

    <form action="{{ route('permissions.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Nombre del permiso</label>
            <input type="text"
                   name="name"
                   value="{{ old('name') }}"
                   class="form-control @error('name') is-invalid @enderror"
                   placeholder="ej: manage orders"
                   required>

            @error('name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <button class="btn btn-success">
            Guardar permiso
        </button>

        <a href="{{ route('permissions.index') }}"
           class="btn btn-secondary ms-2">
            Cancelar
        </a>
    </form>
</div>
@endsection
