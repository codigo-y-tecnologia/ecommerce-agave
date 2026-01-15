@extends('layouts.admins')

@section('content')

@include('superadmin.partials.alerts')

<h2>👥 Usuarios</h2>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Email</th>
            <th>Rol actual</th>
            <th width="180">Acciones</th>
        </tr>
    </thead>

    <tbody>
    @forelse($usuarios as $usuario)
        <tr>
            <td>{{ $usuario->vNombre }}</td>
            <td>{{ $usuario->vEmail }}</td>

            <td>
                {{ $usuario->roles->pluck('name')->implode(', ') ?: 'Sin rol' }}
            </td>

            <td>
                <a href="{{ route('usuarios.roles.edit', $usuario) }}"
                   class="btn btn-sm btn-info">
                    Asignar rol
                </a>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="4" class="text-center text-muted">
                No hay usuarios registrados
            </td>
        </tr>
    @endforelse
    </tbody>
</table>

{{-- Paginación --}}
{{ $usuarios->links() }}

@endsection
