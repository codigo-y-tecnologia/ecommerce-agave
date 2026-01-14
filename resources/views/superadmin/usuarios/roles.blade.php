@extends('layouts.admins')

@section('title', 'Asignar rol')

@section('content')
<div class="container">
    <h2 class="mb-4">👤 Asignar rol a usuario</h2>

    @include('superadmin.partials.alerts')

    <div class="card">
        <div class="card-body">

            <p><strong>Usuario:</strong> {{ $usuario->vNombre }}</p>
            <p><strong>Email:</strong> {{ $usuario->vEmail }}</p>

            <form method="POST"
                  action="{{ route('usuarios.roles.update', $usuario) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Rol</label>

                    <select name="role"
                            class="form-select"
                            required>

                        @foreach($roles as $role)
                            <option value="{{ $role->name }}"
                                {{ $usuario->hasRole($role->name) ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach

                    </select>
                </div>

                <button class="btn btn-primary">
                    Guardar cambios
                </button>

                <a href="{{ route('roles.permisos') }}"
                   class="btn btn-secondary ms-2">
                    Cancelar
                </a>
            </form>

        </div>
    </div>
</div>
@endsection
