<table class="table table-striped align-middle">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Correo</th>
            <th>Rol</th>
            <th class="text-center">Acciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($usuarios as $u)
        <tr>
            <td>{{ $u->vNombre }} {{ $u->vApaterno }} {{ $u->vAmaterno }}</td>
            <td>{{ $u->vEmail }}</td>
            <td><span class="badge bg-{{ $u->eRol === 'superadmin' ? 'danger' : 'primary' }}">{{ $u->eRol }}</span></td>
            <td class="text-center">
                @if ($u->eRol === 'cliente')
                    <form action="{{ route('superadmin.admins.promote', $u->id_usuario) }}" method="POST" class="d-inline">@csrf
                        <button class="btn btn-sm btn-success">Promover a Admin</button>
                    </form>
                @elseif ($u->eRol === 'admin')
                    <form action="{{ route('superadmin.admins.demote', $u->id_usuario) }}" method="POST" class="d-inline">@csrf
                        <button class="btn btn-sm btn-warning">Degradar a Cliente</button>
                    </form>
                @endif

                @if ($u->eRol !== 'superadmin')
                    <form action="{{ route('superadmin.admins.destroy', $u->id_usuario) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este usuario?')">Eliminar</button>
                    </form>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="4" class="text-center text-muted">No se encontraron administradores.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="mt-3">
    {{ $usuarios->links('pagination::bootstrap-5') }}
</div>
