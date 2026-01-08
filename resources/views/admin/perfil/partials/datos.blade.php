<form method="POST" action="{{ route('admin.perfil.datos') }}">
    @csrf
    @method('PUT')

    <h5>Datos personales</h5>

    <input type="text" name="vNombre" value="{{ auth()->user()->vNombre }}" class="form-control mb-2">
    <input type="text" name="vApaterno" value="{{ auth()->user()->vApaterno }}" class="form-control mb-2">
    <input type="text" name="vAmaterno" value="{{ auth()->user()->vAmaterno }}" class="form-control mb-2">
    <input type="email" name="vEmail" value="{{ auth()->user()->vEmail }}" class="form-control mb-2">

    <button class="btn btn-primary">Guardar cambios</button>
</form>
