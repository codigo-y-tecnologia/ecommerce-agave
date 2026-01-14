@extends('layouts.admins')

@section('title', 'Editar Rol')

@section('content')
<div class="container">
    <h2 class="mb-4">✏️ Editar rol</h2>

    @include('superadmin.partials.alerts')

    <form method="POST" action="{{ route('roles.update', $role) }}">
        @csrf
        @method('PUT')

        {{-- Nombre --}}
        <div class="mb-3">
            <label class="form-label">Nombre del rol</label>
            <input type="text"
                   name="name"
                   value="{{ old('name', $role->name) }}"
                   class="form-control @error('name') is-invalid @enderror"
                   required>

            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
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
                           {{ in_array(
                               $permission->name,
                               old('permissions', $role->permissions->pluck('name')->toArray())
                           ) ? 'checked' : '' }}>

                    <label class="form-check-label" for="perm_{{ $permission->id }}">
                        {{ $permission->name }}
                    </label>
                </div>
            @endforeach
        </div>

        <button class="btn btn-primary">Actualizar</button>
        <a href="{{ route('roles.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
    </form>
</div>
@endsection
