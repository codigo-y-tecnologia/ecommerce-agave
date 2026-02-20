<form method="POST" action="{{ route('superadmin.perfil.logoutOthers') }}">
    @csrf

    <h5>Seguridad de sesión</h5>
    <p class="text-muted">
        Cierra todas las sesiones activas excepto esta.
    </p>

    <input type="password" name="password" class="form-control mb-2" placeholder="Confirma tu contraseña" maxlength="100" required>

    <button class="btn btn-danger">Cerrar otras sesiones</button>
</form>
