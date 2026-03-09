@extends('layouts.admins')

@section('title', 'Crear Rol')

@section('content')
<div class="container">
    <h2 class="mb-4">➕ Crear nuevo rol</h2>

    <form method="POST" action="{{ route('roles.store') }}">
        @csrf

        {{-- Nombre del rol --}}
        <div class="mb-3">
            <label class="form-label">Nombre del rol</label>
            <input type="text"
                   name="name"
                   value="{{ old('name') }}"
                   class="form-control @error('name') is-invalid @enderror"
                   placeholder="ej: administrador"
                   required>

            @error('name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Permisos --}}
        <div class="mb-3">
            <label class="form-label">Permisos</label>

            @foreach($permissions as $permission)
                <div class="form-check">
                    <input class="form-check-input"
                           type="checkbox"
                           name="permissions[]"
                           value="{{ $permission->name }}"
                           id="perm_{{ $permission->id }}"
                           {{ in_array($permission->name, old('permissions', [])) ? 'checked' : '' }}>

                    <label class="form-check-label" for="perm_{{ $permission->id }}">
                        {{ $permission->name }}
                    </label>
                </div>
            @endforeach

            @error('permissions')
                <div class="text-danger mt-2">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Botones --}}
        <button class="btn btn-primary">
            Crear rol
        </button>

        <a href="{{ route('roles.index') }}"
           class="btn btn-secondary ms-2">
            Cancelar
        </a>
    </form>
</div>
@endsection
