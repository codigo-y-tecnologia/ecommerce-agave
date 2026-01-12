@extends('layouts.admins')

@section('content')

@include('superadmin.partials.alerts')

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

<table class="table table-bordered">
    <tr>
        <th>Permiso</th>
        <th width="180">Acciones</th>
    </tr>

    @forelse($permissions as $permission)
    <tr>
        <td>{{ $permission->name }}</td>
        <td>
            <a href="{{ route('permissions.edit', $permission) }}"
               class="btn btn-sm btn-warning">
                Editar
            </a>

            <form action="{{ route('permissions.destroy', $permission) }}"
                  method="POST"
                  class="d-inline"
                  onsubmit="return confirm('¿Eliminar permiso?')">
                @csrf
                @method('DELETE')

                <button class="btn btn-sm btn-danger">
                    Eliminar
                </button>
            </form>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="2" class="text-center text-muted">
            No se encontraron permisos
        </td>
    </tr>
    @endforelse
</table>

{{-- Paginación --}}
<div class="d-flex justify-content-center">
    {{ $permissions->links() }}
</div>

@endsection
