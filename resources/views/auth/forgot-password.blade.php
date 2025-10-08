<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Recuperar Contraseña</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">

<div class="card shadow p-4" style="max-width: 400px; width: 100%; border-radius: 16px;">
  <h4 class="text-center text-primary mb-3">¿Olvidaste tu contraseña?</h4>
  <p class="text-muted small text-center mb-4">Introduce tu correo y te enviaremos un enlace para restablecerla.</p>

  @if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <form method="POST" action="{{ route('password.email') }}">
    @csrf
    <div class="mb-3">
      <label for="vEmail" class="form-label">Correo Electrónico</label>
      <input type="email" name="vEmail" id="vEmail" class="form-control @error('email') is-invalid @enderror" required autofocus>
      @error('email')
      <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>
    <button type="submit" class="btn btn-primary w-100">Enviar enlace</button>
  </form>

  <div class="text-center mt-3">
    <a href="{{ route('login') }}" class="text-decoration-none small">← Volver al inicio de sesión</a>
  </div>
</div>

</body>
</html>
