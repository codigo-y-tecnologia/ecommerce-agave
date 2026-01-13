@extends('layouts.admins')

@section('content')

@include('superadmin.partials.alerts')

<h2>🧾 Roles del sistema</h2>

<a href="{{ route('roles.create') }}"
   class="btn btn-primary mb-3 mt-2">
    Nuevo rol
</a>

{{-- Buscador --}}
<form method="GET" class="mb-3">
    <input type="text"
           name="search"
           class="form-control"
           placeholder="Buscar rol..."
           value="{{ request('search') }}">
</form>

{{-- Tabla de roles --}}
<table class="table table-bordered align-middle">
    <tr>
        <th>Rol</th>
        <th style="width: 280px">Acciones</th>
    </tr>

    @forelse($roles as $role)
    <tr>
        <td>{{ $role->name }}</td>
        <td>
            <div class="d-flex gap-1 flex-wrap">

                {{-- Gestionar permisos --}}
                <a href="{{ route('roles.permissions.edit', $role) }}"
                   class="btn btn-info btn-sm">
                    Permisos
                </a>

                {{-- Editar rol --}}
                <a href="{{ route('roles.edit', $role) }}"
                   class="btn btn-warning btn-sm">
                    Editar
                </a>

                {{-- Eliminar rol (protegido) --}}
                @if ($role->name !== 'superadmin')
                    <form action="{{ route('roles.destroy', $role) }}"
                          method="POST"
                          onsubmit="return confirm('¿Seguro que deseas eliminar este rol?');">
                        @csrf
                        @method('DELETE')

                        <button class="btn btn-danger btn-sm">
                            Eliminar
                        </button>
                    </form>
                @endif

            </div>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="2" class="text-center text-muted">
            No hay roles registrados.
        </td>
    </tr>
    @endforelse
</table>

{{-- Paginación --}}
{{ $roles->links() }}
@endsection
