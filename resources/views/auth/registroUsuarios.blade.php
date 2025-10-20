<!-- resources/views/usuarios/create.blade.php -->

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.min.css">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="container mt-5">

    <h2>Formulario de registro</h2>
    <p><small>* Campos obligatorios</small></p>

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
            <label for="vNombre" class="form-label">Nombre *</label>
            <input type="text" id="vNombre" name="vNombre" class="form-control" value="{{ old('vNombre') }}" maxlength="60" required>
        </div>

        <div class="mb-3">
            <label for="vApaterno" class="form-label">Apellido Paterno *</label>
            <input type="text" id="vApaterno" name="vApaterno" class="form-control" value="{{ old('vApaterno') }}"maxlength="50" required>
        </div>

        <div class="mb-3">
            <label for="vAmaterno" class="form-label">Apellido Materno *</label>
            <input type="text" id="vAmaterno" name="vAmaterno" class="form-control" value="{{ old('vAmaterno') }}" maxlength="50" required>
        </div>

        <div class="mb-3">
            <label for="vEmail" class="form-label">Correo Electrónico *</label>
            <input type="email" id="vEmail" name="vEmail" class="form-control" value="{{ old('vEmail') }}" maxlength="100" required>
        </div>

        <div class="mb-3">
            <label for="vPassword" class="form-label">Contraseña *</label>
            <input type="password" id="vPassword" name="vPassword" class="form-control" value="{{ old('vPassword') }}" maxlength="150" required>
            <small id="passwordStrengthText" class="form-text"></small>
    <div id="passwordStrengthBar" class="progress mt-1" style="height: 6px;">
        <div class="progress-bar" role="progressbar"></div>
    </div>
        </div>

        <div class="mb-3">
    <label for="vPassword_confirmation" class="form-label">Confirmar Contraseña *</label>
    <input type="password" id="vPassword_confirmation" name="vPassword_confirmation" class="form-control" value="{{ old('vPassword_confirmation') }}" maxlength="150" required>
</div>

        <div class="mb-3">
            <label for="dFecha_nacimiento" class="form-label">Fecha de Nacimiento *</label>
            <input type="date" id="dFecha_nacimiento" name="dFecha_nacimiento" class="form-control" value="{{ old('dFecha_nacimiento') }}" required>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" id="terminos" name="terminos" id="terminos" class="form-check-input" value="1" {{ old('terminos') ? 'checked' : '' }} required>
            <label class="form-check-label" for="terminos">
                Acepto los términos y condiciones y confirmo que soy mayor de edad.
            </label>
        </div>

        <button type="submit" class="btn btn-success">Registrar</button>
    </form>
  <script src="{{ asset('js/usuarios/registrar-usuario.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.all.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
