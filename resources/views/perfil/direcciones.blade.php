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
            <div class="row g-3">

    {{-- 📞 Teléfono --}}
    <div class="col-md-4">
        <label class="form-label fw-bold">Teléfono de contacto</label>
        <input type="text" name="vTelefono_contacto" class="form-control" required>
    </div>

    {{-- 🏠 Calle --}}
    <div class="col-md-8">
        <label class="form-label fw-bold">Calle</label>
        <input type="text" name="vCalle" class="form-control" required>
    </div>

    {{-- 🔢 Números --}}
    <div class="col-md-4">
        <label class="form-label">Número exterior</label>
        <input type="text" name="vNumero_exterior" class="form-control">
    </div>

    <div class="col-md-4">
        <label class="form-label">Número interior</label>
        <input type="text" name="vNumero_interior" class="form-control">
    </div>

    {{-- 🏘 Colonia y CP --}}
    <div class="col-md-4">
        <label class="form-label">Colonia</label>
        <input type="text" name="vColonia" class="form-control">
    </div>

    <div class="col-md-4">
        <label class="form-label">Código postal</label>
        <input type="text" name="vCodigo_postal" class="form-control">
    </div>

    {{-- 🏙 Ciudad y estado --}}
    <div class="col-md-4">
        <label class="form-label">Ciudad</label>
        <input type="text" name="vCiudad" class="form-control">
    </div>

    <div class="col-md-4">
        <label class="form-label">Estado</label>
        <input type="text" name="vEstado" class="form-control">
    </div>

    {{-- 🚏 Entre calles --}}
    <div class="col-md-6">
        <label class="form-label">Entre calle 1</label>
        <input type="text" name="vEntre_calle_1" class="form-control">
    </div>

    <div class="col-md-6">
        <label class="form-label">Entre calle 2</label>
        <input type="text" name="vEntre_calle_2" class="form-control">
    </div>

    {{-- 📝 Referencias --}}
    <div class="col-12">
        <label class="form-label">Referencias adicionales</label>
        <textarea name="tReferencias" class="form-control" rows="2" placeholder="Ejemplo: Portón azul, frente a la tienda..."></textarea>
    </div>

    {{-- ⭐ Dirección principal --}}
    <div class="col-12">
        <div class="form-check mt-2">
            <input type="checkbox" class="form-check-input" id="checkPrincipal" name="bDireccion_principal" value="1">
            <label class="form-check-label" for="checkPrincipal">
                Establecer como dirección principal
            </label>
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
    const modalElement = document.getElementById('modalDireccion');
    const modal = new bootstrap.Modal(modalElement);
    const idField = document.getElementById('id_direccion');
    const modalTitle = document.getElementById('modalDireccionLabel');

    // 🔹 NUEVA DIRECCIÓN → limpiar formulario y mostrar modal vacío
    document.querySelector('[data-bs-target="#modalDireccion"]').addEventListener('click', () => {
        form.reset(); // limpia todos los campos
        idField.value = ''; // limpia el ID oculto
        modalTitle.textContent = 'Agregar dirección';
        // Asegurarse que el checkbox quede desmarcado
        const check = document.getElementById('checkPrincipal');
        if (check) check.checked = false;
    });

    // 🔹 CREAR / ACTUALIZAR
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const id = idField.value;
        const url = id ? `/perfil/direcciones/${id}` : `/perfil/direcciones`;
        const formData = new FormData(form);

        // Siempre usar POST (Laravel entiende _method)
        formData.append('_method', id ? 'PUT' : 'POST');

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: formData
            });

            const data = await res.json();

            if (data.success) {
                modal.hide();
                alert('✅ Dirección guardada correctamente.');
                location.reload();
            } else {
                alert('❌ Error al guardar la dirección.');
            }
        } catch (err) {
            console.error(err);
            alert('⚠️ Error de conexión.');
        }
    });

    // 🔹 EDITAR
    document.querySelectorAll('.btn-editar').forEach(btn => {
        btn.addEventListener('click', async () => {
            const id = btn.dataset.id;
            try {
                const res = await fetch(`/api/direccion/${id}`);
                const data = await res.json();

                if (data.success) {
                    const d = data.direccion;

                    // Limpiamos primero para evitar valores residuales
                    form.reset();
                    idField.value = id;

                    // Rellenar con los valores de la dirección
                    for (const key in d) {
                        if (form.elements[key]) {
                            // Si es checkbox
                            if (form.elements[key].type === 'checkbox') {
                                form.elements[key].checked = d[key] == 1;
                            } else {
                                form.elements[key].value = d[key] ?? '';
                            }
                        }
                    }

                    modalTitle.textContent = 'Editar dirección';
                    modal.show();
                } else {
                    alert('❌ No se pudo cargar la dirección.');
                }
            } catch (err) {
                console.error(err);
                alert('⚠️ Error al cargar los datos de la dirección.');
            }
        });
    });

    // 🔹 ELIMINAR
    document.querySelectorAll('.btn-eliminar').forEach(btn => {
        btn.addEventListener('click', async () => {
            if (!confirm('¿Eliminar esta dirección?')) return;
            const id = btn.dataset.id;
            try {
                const res = await fetch(`/perfil/direcciones/${id}`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: new URLSearchParams({ _method: 'DELETE' })
                });
                const data = await res.json();
                if (data.success) {
                    alert('🗑️ Dirección eliminada correctamente.');
                    location.reload();
                } else {
                    alert('❌ Error al eliminar.');
                }
            } catch (err) {
                console.error(err);
                alert('⚠️ Error de conexión.');
            }
        });
    });
});
</script>

@endsection
