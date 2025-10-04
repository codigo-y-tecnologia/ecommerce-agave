<!-- resources/views/usuarios/create.blade.php -->

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
    @vite(['resources/css/styles.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="container mt-5">

    <h2>Formulario de registro</h2>

    <!-- Mostrar errores de validación -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Formulario -->
    <form id="registroForm" action="{{ route('usuarios.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="vNombre" class="form-label">Nombre</label>
            <input type="text" name="vNombre" class="form-control" value="{{ old('vNombre') }}">
        </div>

        <div class="mb-3">
            <label for="vApaterno" class="form-label">Apellido Paterno</label>
            <input type="text" name="vApaterno" class="form-control" value="{{ old('vApaterno') }}">
        </div>

        <div class="mb-3">
            <label for="vAmaterno" class="form-label">Apellido Materno</label>
            <input type="text" name="vAmaterno" class="form-control" value="{{ old('vAmaterno') }}">
        </div>

        <div class="mb-3">
            <label for="vEmail" class="form-label">Correo Electrónico</label>
            <input type="email" name="vEmail" class="form-control" value="{{ old('vEmail') }}">
        </div>

        <div class="mb-3">
            <label for="vPassword" class="form-label">Contraseña</label>
            <input type="password" name="vPassword" class="form-control" value="{{ old('vPassword') }}">
        </div>

        <div class="mb-3">
    <label for="vPassword_confirmation" class="form-label">Confirmar Contraseña</label>
    <input type="password" name="vPassword_confirmation" class="form-control" value="{{ old('vPassword_confirmation') }}">
</div>

        <div class="mb-3">
            <label for="dFecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
            <input type="date" name="dFecha_nacimiento" class="form-control" value="{{ old('dFecha_nacimiento') }}" >
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" name="terminos" id="terminos" class="form-check-input" value="1" {{ old('terminos') ? 'checked' : '' }}>
            <label class="form-check-label" for="terminos">
                Acepto los términos y condiciones y confirmo que soy mayor de edad.
            </label>
        </div>

        <button type="submit" class="btn btn-success">Registrar</button>
    </form>

</body>
</html>
