<form method="POST" action="{{ route('superadmin.perfil.datos') }}">
    @csrf
    @method('PUT')

    <h5>Datos personales</h5>

    <input type="text" name="vNombre" value="{{ auth()->user()->vNombre }}" class="form-control mb-2" maxlength="60" required>
    <input type="text" name="vApaterno" value="{{ auth()->user()->vApaterno }}" class="form-control mb-2" maxlength="50" required>
    <input type="text" name="vAmaterno" value="{{ auth()->user()->vAmaterno }}" class="form-control mb-2" maxlength="50" required>
    <input type="email" name="vEmail" value="{{ auth()->user()->vEmail }}" class="form-control mb-2" maxlength="100" required>

    <button class="btn btn-primary">Guardar cambios</button>
</form>
