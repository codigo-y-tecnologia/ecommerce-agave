<!-- resources/views/usuarios/create.blade.php -->

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">

    <h2>Registrar nuevo usuario</h2>

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
    <form action="{{ route('usuarios.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="vNombre" class="form-label">Nombre</label>
            <input type="text" name="vNombre" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="vApaterno" class="form-label">Apellido Paterno</label>
            <input type="text" name="vApaterno" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="vAmaterno" class="form-label">Apellido Materno</label>
            <input type="text" name="vAmaterno" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="vEmail" class="form-label">Correo Electrónico</label>
            <input type="email" name="vEmail" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="vPassword" class="form-label">Contraseña</label>
            <input type="password" name="vPassword" class="form-control" required>
        </div>

        <div class="mb-3">
    <label for="vPassword_confirmation" class="form-label">Confirmar Contraseña</label>
    <input type="password" name="vPassword_confirmation" class="form-control" required>
</div>

        <div class="mb-3">
            <label for="dFecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
            <input type="date" name="dFecha_nacimiento" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="eRol" class="form-label">Rol</label>
            <select name="eRol" class="form-select">
                <option value="cliente">Cliente</option>
                <option value="admin">Administrador</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Registrar</button>
    </form>

</body>
</html>
