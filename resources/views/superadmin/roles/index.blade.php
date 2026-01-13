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

<table class="table table-bordered">
    <tr>
        <th>Rol</th>
        <th>Acciones</th>
    </tr>

    @forelse($roles as $role)
    <tr>
        <td>{{ $role->name }}</td>
        <td>
            <a href="{{ route('roles.permissions.edit', $role) }}"
               class="btn btn-info btn-sm">
                Gestionar permisos
            </a>
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
