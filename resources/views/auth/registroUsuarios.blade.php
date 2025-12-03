<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario - Ecommerce Agave</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="text-center">Registrar nuevo usuario</h2>
                </div>
                <div class="card-body">

                    <!-- Mostrar errores de validación -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Formulario -->
                    <form action="{{ route('usuarios.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="vNombre" class="form-label">Nombre *</label>
                                <input type="text" 
                                       name="vNombre" 
                                       class="form-control @error('vNombre') is-invalid @enderror" 
                                       value="{{ old('vNombre') }}" 
                                       required>
                                @error('vNombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="vApaterno" class="form-label">Apellido Paterno *</label>
                                <input type="text" 
                                       name="vApaterno" 
                                       class="form-control @error('vApaterno') is-invalid @enderror" 
                                       value="{{ old('vApaterno') }}" 
                                       required>
                                @error('vApaterno')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="vAmaterno" class="form-label">Apellido Materno *</label>
                            <input type="text" 
                                   name="vAmaterno" 
                                   class="form-control @error('vAmaterno') is-invalid @enderror" 
                                   value="{{ old('vAmaterno') }}" 
                                   required>
                            @error('vAmaterno')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="vEmail" class="form-label">Correo Electrónico *</label>
                            <input type="email" 
                                   name="vEmail" 
                                   class="form-control @error('vEmail') is-invalid @enderror" 
                                   value="{{ old('vEmail') }}" 
                                   required>
                            @error('vEmail')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="vPassword" class="form-label">Contraseña *</label>
                                <input type="password" 
                                       name="vPassword" 
                                       class="form-control @error('vPassword') is-invalid @enderror" 
                                       required>
                                @error('vPassword')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="vPassword_confirmation" class="form-label">Confirmar Contraseña *</label>
                                <input type="password" 
                                       name="vPassword_confirmation" 
                                       class="form-control" 
                                       required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="dFecha_nacimiento" class="form-label">Fecha de Nacimiento *</label>
                            <input type="date" 
                                   name="dFecha_nacimiento" 
                                   class="form-control @error('dFecha_nacimiento') is-invalid @enderror" 
                                   value="{{ old('dFecha_nacimiento') }}" 
                                   required>
                            @error('dFecha_nacimiento')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="eRol" class="form-label">Rol</label>
                            <select name="eRol" class="form-select @error('eRol') is-invalid @enderror">
                                <option value="cliente" {{ old('eRol') == 'cliente' ? 'selected' : '' }}>Cliente</option>
                                <option value="admin" {{ old('eRol') == 'admin' ? 'selected' : '' }}>Administrador</option>
                            </select>
                            @error('eRol')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">Registrarse</button>
                            <a href="{{ route('login') }}" class="btn btn-outline-secondary">¿Ya tienes cuenta? Inicia Sesión</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
</html>