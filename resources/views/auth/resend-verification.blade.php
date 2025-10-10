<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reenviar Verificación de Email</title>
    @vite(['resources/css/styles.css', 'resources/js/usuarios/resend-verification.js'])
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">

<div class="card shadow p-4" style="max-width: 400px; width: 100%; border-radius: 16px;">
    <h4 class="text-center text-primary mb-3">Reenviar Verificación de Email</h4>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <p class="text-muted small text-center mb-4">Ingresa tu correo electrónico para reenviar el enlace de verificación.</p>

    <form id="resendVerificationForm" method="POST" action="{{ route('verification.resend') }}">
        @csrf
        <div class="mb-3">
            <label for="vEmail" class="form-label">Correo Electrónico</label>
            <input type="email" name="vEmail" id="vEmail" 
                   class="form-control @error('vEmail') is-invalid @enderror" 
                   value="{{ old('vEmail') }}" required autofocus>
            @error('vEmail')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary w-100">Reenviar Enlace</button>
    </form>

    <div class="text-center mt-3">
        <a href="{{ route('login') }}" class="text-decoration-none small">← Volver al inicio de sesión</a>
    </div>
</div>

</body>
</html>