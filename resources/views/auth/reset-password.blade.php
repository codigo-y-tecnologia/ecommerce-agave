<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Restablecer Contraseña</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">

<div class="card shadow p-4" style="max-width: 400px; width: 100%; border-radius: 16px;">
  <h4 class="text-center text-primary mb-3">Restablecer Contraseña</h4>

  <form method="POST" action="{{ route('password.update') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <div class="mb-3">
      <label for="vEmail" class="form-label">Correo Electrónico</label>
      <input type="email" name="vEmail" id="vEmail" value="{{ old('email', $email) }}" class="form-control @error('email') is-invalid @enderror" required autofocus>
      @error('email')
      <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="mb-3">
      <label for="password" class="form-label">Nueva Contraseña</label>
      <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required>
      @error('password')
      <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="mb-4">
      <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
      <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-success w-100">Restablecer Contraseña</button>
  </form>
</div>

</body>
</html>
