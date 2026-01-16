@extends('layouts.admins')

@section('content')

@include('superadmin.partials.alerts')

<a href="{{ route('roles.permisos') }}"
   class="btn btn-secondary mb-3">
    ← Volver
</a>

<h2>🧾 Permisos</h2>

<a href="{{ route('permissions.create') }}"
   class="btn btn-primary mb-3">
    Nuevo permiso
</a>

{{-- Buscador --}}
<form method="GET" action="{{ route('permissions.index') }}" class="mb-3">
    <div class="input-group">
        <input type="text"
               name="search"
               class="form-control"
               placeholder="Buscar permiso..."
               value="{{ request('search') }}">

        <button class="btn btn-outline-secondary">
            🔍 Buscar
        </button>
    </div>
</form>

<table class="table table-bordered align-middle">
    <thead>
        <tr>
            <th>Permiso</th>
            <th>Usado por roles</th>
            <th width="220">Acciones</th>
        </tr>
    </thead>

    <tbody>
    @forelse($permissions as $permission)
        <tr>
            <td>{{ $permission->name }}</td>

            {{-- Roles que usan el permiso --}}
            <td>
                @if ($permission->roles->isEmpty())
                    <span class="text-muted">No asignado</span>
                @else
                    @foreach($permission->roles as $role)
                        <span class="badge bg-secondary me-1">
                            {{ $role->name }}
                        </span>
                    @endforeach
                @endif
            </td>

            {{-- Acciones --}}
            <td>
                <a href="{{ route('permissions.edit', $permission) }}"
                   class="btn btn-sm btn-warning">
                    Editar
                </a>

                @if ($permission->roles->isEmpty())
                    <form action="{{ route('permissions.destroy', $permission) }}"
                          method="POST"
                          class="d-inline"
                          onsubmit="return confirm('¿Eliminar permiso?');">
                        @csrf
                        @method('DELETE')

                        <button class="btn btn-sm btn-danger">
                            Eliminar
                        </button>
                    </form>
                @else
                    <button class="btn btn-sm btn-secondary" disabled>
                        En uso
                    </button>
                @endif
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="3" class="text-center text-muted">
                No se encontraron permisos
            </td>
        </tr>
    @endforelse
    </tbody>
</table>

{{-- Paginación --}}
<div class="d-flex justify-content-center">
    {{ $permissions->links() }}
</div>

@endsection
