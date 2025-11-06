@extends('layouts.app')

@section('title', 'Mis Direcciones')

@section('content')
<div class="container mt-5">
    <h2 class="fw-bold mb-4 text-center">📍 Mis Direcciones</h2>

    <div class="d-flex justify-content-end mb-3">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalDireccion">➕ Nueva dirección</button>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            @if($direcciones->isEmpty())
                <p class="text-muted">No tienes direcciones guardadas.</p>
            @else
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Dirección</th>
                                <th>Ciudad</th>
                                <th>Estado</th>
                                <th>Principal</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($direcciones as $dir)
                                <tr>
                                    <td>{{ $dir->vCalle }} {{ $dir->vNumero_exterior }}, {{ $dir->vColonia }}</td>
                                    <td>{{ $dir->vCiudad }}</td>
                                    <td>{{ $dir->vEstado }}</td>
                                    <td>
                                        @if($dir->bDireccion_principal)
                                            <span class="badge bg-success">Principal</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary btn-editar" data-id="{{ $dir->id_direccion }}">✏️</button>
                                        <button class="btn btn-sm btn-outline-danger btn-eliminar" data-id="{{ $dir->id_direccion }}">🗑️</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- MODAL --}}
<div class="modal fade" id="modalDireccion" tabindex="-1" aria-labelledby="modalDireccionLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="formDireccion" class="modal-content">
            @csrf
            <input type="hidden" id="id_direccion">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalDireccionLabel">Agregar dirección</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="vTelefono_contacto" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Calle</label>
                        <input type="text" name="vCalle" class="form-control" required>
                    </div>
                    <div class="col-md-4"><label class="form-label">Número exterior</label><input type="text" name="vNumero_exterior" class="form-control"></div>
                    <div class="col-md-4"><label class="form-label">Colonia</label><input type="text" name="vColonia" class="form-control"></div>
                    <div class="col-md-4"><label class="form-label">Código postal</label><input type="text" name="vCodigo_postal" class="form-control"></div>
                    <div class="col-md-6"><label class="form-label">Ciudad</label><input type="text" name="vCiudad" class="form-control"></div>
                    <div class="col-md-6"><label class="form-label">Estado</label><input type="text" name="vEstado" class="form-control"></div>
                    <div class="col-12 form-check mt-2">
                        <input type="checkbox" class="form-check-input" id="checkPrincipal" name="bDireccion_principal">
                        <label for="checkPrincipal" class="form-check-label">Establecer como dirección principal</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

{{-- SCRIPTS --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('formDireccion');
    const modal = new bootstrap.Modal(document.getElementById('modalDireccion'));
    const idField = document.getElementById('id_direccion');
    const modalTitle = document.getElementById('modalDireccionLabel');

    // 🔹 Crear / actualizar
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = idField.value;
        const url = id ? `/perfil/direcciones/${id}` : `/perfil/direcciones`;
        const method = id ? 'PUT' : 'POST';

        const formData = new FormData(form);
        try {
            const res = await fetch(url, { method, headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}, body: formData });
            const data = await res.json();

            if (data.success) {
                modal.hide();
                alert('✅ Dirección guardada correctamente.');
                location.reload();
            } else {
                alert('❌ Error al guardar la dirección.');
            }
        } catch (err) {
            alert('⚠️ Error de conexión.');
        }
    });

    // 🔹 Editar
    document.querySelectorAll('.btn-editar').forEach(btn => {
        btn.addEventListener('click', async () => {
            const id = btn.dataset.id;
            const res = await fetch(`/api/direccion/${id}`);
            const data = await res.json();

            if (data.success) {
                const d = data.direccion;
                for (const key in d) {
                    if (form.elements[key]) form.elements[key].value = d[key];
                }
                idField.value = id;
                modalTitle.textContent = 'Editar dirección';
                modal.show();
            }
        });
    });

    // 🔹 Eliminar
    document.querySelectorAll('.btn-eliminar').forEach(btn => {
        btn.addEventListener('click', async () => {
            if (!confirm('¿Eliminar esta dirección?')) return;
            const id = btn.dataset.id;
            const res = await fetch(`/perfil/direcciones/${id}`, {
                method: 'DELETE',
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
            });
            const data = await res.json();
            if (data.success) location.reload();
            else alert('❌ Error al eliminar.');
        });
    });
});
</script>
@endsection
