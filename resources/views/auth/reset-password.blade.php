<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablecer Contraseña</title>
    @vite(['resources/css/styles.css', 'resources/js/usuarios/reset-password.js'])
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">

<div class="card shadow p-4" style="max-width: 400px; width: 100%; border-radius: 16px;">
    <h4 class="text-center text-primary mb-3">Restablecer Contraseña</h4>

    <form id="resetPasswordForm" method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="mb-3">
            <label for="vEmail" class="form-label">Correo Electrónico</label>
            <input type="email" name="vEmail" id="vEmail" 
                   value="{{ old('vEmail', $email) }}" 
                   class="form-control @error('vEmail') is-invalid @enderror" required autofocus>
            @error('vEmail')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Nueva Contraseña</label>
            <input type="password" name="password" id="password" 
                   class="form-control @error('password') is-invalid @enderror" required>
                   <small id="passwordStrengthText" class="form-text"></small>
    <div id="passwordStrengthBar" class="progress mt-1" style="height: 6px;">
        <div class="progress-bar" role="progressbar"></div>
    </div>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
            <input type="password" name="password_confirmation" id="password_confirmation" 
                   class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success w-100">Restablecer Contraseña</button>
    </form>
</div>

</body>
</html>