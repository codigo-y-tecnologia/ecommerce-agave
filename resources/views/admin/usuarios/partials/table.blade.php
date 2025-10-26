<div class="table-responsive shadow-sm rounded">
    <table class="table table-striped table-hover align-middle mb-0">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nombre completo</th>
                <th>Correo</th>
                <th>Fecha de registro</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($usuarios as $usuario)
                <tr>
                    <td>{{ $usuario->id_usuario }}</td>
                    <td>{{ $usuario->vNombre }} {{ $usuario->vApaterno }} {{ $usuario->vAmaterno }}</td>
                    <td>{{ $usuario->vEmail }}</td>
                    <td>{{ $usuario->tFecha_registro ?? '—' }}</td>
                    <td class="text-center">
                        <a href="{{ route('admin.usuarios.edit', $usuario->id_usuario) }}" class="btn btn-sm btn-primary">✏️ Editar</a>
                        <form action="{{ route('admin.usuarios.destroy', $usuario->id_usuario) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este cliente?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">🗑 Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">No hay clientes registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- 🔹 Paginación dinámica --}}
<div class="d-flex justify-content-center mt-4">
    {{ $usuarios->links('pagination::bootstrap-5') }}
</div>
