@extends('layouts.app')

@section('title', 'Usuarios Registrados')

@section('content')
<div class="container">
    <h2 class="mb-4 text-center">👥 Usuarios Registrados</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive shadow-sm">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Fecha Registro</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($usuarios as $usuario)
                <tr>
                    <td>{{ $usuario->id_usuario }}</td>
                    <td>{{ $usuario->vNombre }} {{ $usuario->vApaterno }}</td>
                    <td>{{ $usuario->vEmail }}</td>
                    <td>
                        @if($usuario->eRol === 'admin')
                            <span class="badge bg-warning text-dark">Admin</span>
                        @elseif($usuario->eRol === 'superadmin')
                            <span class="badge bg-danger">Superadmin</span>
                        @else
                            <span class="badge bg-success">Cliente</span>
                        @endif
                    </td>
                    <td>{{ $usuario->tFecha_registro ?? '—' }}</td>
                    <td class="text-center">
                        <a href="{{ route('admin.usuarios.edit', $usuario->id_usuario) }}" class="btn btn-sm btn-primary">✏️ Editar</a>
                        <form action="{{ route('admin.usuarios.destroy', $usuario->id_usuario) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este usuario?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">🗑 Eliminar</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
