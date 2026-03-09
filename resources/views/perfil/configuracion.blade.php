@extends('layouts.app')

@section('title', 'Configuración de Cuenta')

@section('content')
<div class="container mt-5 mb-5">
    <h2 class="fw-bold text-center mb-4">⚙️ Configuración de Cuenta</h2>

<!-- Mensajes de estado -->
@if(session('warning'))
    <div class="alert alert-warning">{{ session('warning') }}</div>
@endif

@if(session('info'))
    <div class="alert alert-info">{{ session('info') }}</div>
@endif

@if(auth()->user()->email_pending)
    <div class="alert alert-warning">
        Tienes un cambio de correo pendiente:
        <strong>{{ auth()->user()->email_pending }}</strong>
    </div>
@endif

    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Información Personal -->
            <div class="card shadow-sm mb-4 border-0 rounded-4">
                <div class="card-header bg-primary text-white fw-bold rounded-top-4">
                    Información Personal
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('perfil.actualizar') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="vNombre" value="{{ old('vNombre', $usuario->vNombre) }}" class="form-control rounded-pill" maxlength="60" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Apellido Paterno</label>
                            <input type="text" name="vApaterno" value="{{ old('vApaterno', $usuario->vApaterno) }}" class="form-control rounded-pill" maxlength="50" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Apellido Materno</label>
                            <input type="text" name="vAmaterno" value="{{ old('vAmaterno', $usuario->vAmaterno) }}" class="form-control rounded-pill" maxlength="50" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Correo electrónico</label>
                            <input type="email" name="vEmail" value="{{ old('vEmail', $usuario->vEmail) }}" class="form-control rounded-pill" required>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary px-4 rounded-pill">💾 Guardar cambios</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Cambiar Contraseña -->
            <div class="card shadow-sm mb-4 border-0 rounded-4">
                <div class="card-header bg-warning text-dark fw-bold rounded-top-4">
                    Cambiar Contraseña
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('perfil.cambiarPassword') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Contraseña actual</label>
                            <input type="password" name="password_actual" class="form-control rounded-pill" maxlength="150" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nueva contraseña</label>
                            <input type="password" name="password_nueva" class="form-control rounded-pill" maxlength="150" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Confirmar nueva contraseña</label>
                            <input type="password" name="password_nueva_confirmation" class="form-control rounded-pill" maxlength="150" required>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-warning text-dark px-4 rounded-pill">🔒 Actualizar contraseña</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Eliminar Cuenta -->
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-danger text-white fw-bold rounded-top-4">
                    Eliminar Cuenta
                </div>
                <div class="card-body">
                    <p class="text-muted">⚠️ Esta acción no se puede deshacer. Se eliminarán todos tus datos y direcciones guardadas.</p>

                    <form id="formEliminarCuenta" method="POST" action="{{ route('perfil.eliminar') }}">

                        @csrf
                        @method('DELETE')

                        <input type="password"
                            name="password"
                            id="passwordEliminar"
                            class="form-control rounded-pill mb-3"
                            placeholder="Confirma tu contraseña"
                            maxlength="150" required>

                        <button type="button" id="btnEliminarCuenta" class="btn btn-outline-danger rounded-pill w-100">
                            🗑️ Eliminar mi cuenta
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Mensaje de éxito
    @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: '{{ session('success') }}',
            confirmButtonText: 'Aceptar',
            confirmButtonColor: '#3085d6',
            background: '#fefefe',
            iconColor: '#00b894',
        });
    @endif

    // Mensajes de error
    @if ($errors->any())
        let errores = `{!! implode('<br>', $errors->all()) !!}`;
        Swal.fire({
            icon: 'error',
            title: 'Errores encontrados',
            html: errores,
            confirmButtonText: 'Revisar',
            confirmButtonColor: '#d33',
            background: '#fff8f8',
            iconColor: '#e74c3c',
        });
    @endif

    // 🗑️ Confirmación de eliminación
    const btnEliminar = document.getElementById('btnEliminarCuenta');
    const formEliminar = document.getElementById('formEliminarCuenta');
    const passwordInput = document.getElementById('passwordEliminar');

    if (btnEliminar) {
        btnEliminar.addEventListener('click', (e) => {
            e.preventDefault();

            if (!passwordInput.value.trim()) {
        Swal.fire({
            icon: 'error',
            title: 'Contraseña requerida',
            text: 'Debes confirmar tu contraseña para eliminar tu cuenta.',
            confirmButtonColor: '#d33',
        });
        return;
    }

            Swal.fire({
                title: '¿Eliminar tu cuenta?',
                text: 'Esta acción no se puede deshacer. Se eliminarán todos tus datos.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminarla',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                background: '#fff',
                iconColor: '#f39c12',
            }).then((result) => {
                if (result.isConfirmed) {
                    formEliminar.submit();
                }
            });
        });
    }
});
</script>
@endsection
