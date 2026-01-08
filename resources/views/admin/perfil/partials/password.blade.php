<form method="POST" action="{{ route('admin.perfil.password') }}">
    @csrf
    @method('PUT')

    <h5>Seguridad – Cambiar contraseña</h5>

    <input type="password" name="current_password" class="form-control mb-2" placeholder="Contraseña actual" maxlength="100" required>
    <input type="password" name="password" class="form-control mb-2" placeholder="Nueva contraseña" maxlength="100" required>
    <input type="password" name="password_confirmation" class="form-control mb-2" placeholder="Confirmar contraseña" maxlength="100" required>

    <button class="btn btn-warning">Actualizar contraseña</button>
</form>
