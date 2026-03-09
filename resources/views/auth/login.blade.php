<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar Sesión</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.min.css">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">

  <div class="card shadow-lg p-4" style="width: 100%; max-width: 420px; border-radius: 16px;">
    <div class="text-center mb-4">
      <h3 class="fw-bold text-primary">Iniciar Sesión</h3>
      <p class="text-muted small">Accede a tu cuenta para continuar</p>
    </div>

     <!-- Mensaje de éxito general -->
    @if (session('success'))
        <div class="alert alert-success">
            <strong>{{ session('success') }}</strong>
            @if (session('verification_message'))
                <br><small>{{ session('verification_message') }}</small>
            @endif
        </div>
    @endif

    <!-- Mensaje específico de verificación -->
    @if (session('verification_sent'))
        <div class="alert alert-info">
            <i class="bi bi-envelope-check"></i>
            <strong>¡Email de verificación enviado!</strong>
            <br>
            {{ session('verification_sent') }}
        </div>
    @endif

    <!-- Mensaje de verificación exitosa -->
    @if (session('verification_success'))
        <div class="alert alert-success">
            <i class="bi bi-check-circle"></i>
            <strong>¡Email verificado!</strong>
            <br>
            {{ session('verification_success') }}
        </div>
    @endif

    @if ($errors->any())
      <div class="alert alert-danger p-2">
        <ul class="mb-0 small">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @if (session('show_set_password'))
    <form method="POST" action="{{ route('cuenta.reenviar-password') }}" class="mt-2">
        @csrf
        <input type="hidden" name="vEmail" value="{{ old('vEmail') }}">
        <button type="submit" class="btn btn-warning btn-sm w-100">
            Reenviar correo para establecer mi contraseña
        </button>
    </form>
@endif

    <form id="loginForm" action="{{ route('login') }}" method="POST">
      @csrf

      <!-- Email -->
      <div class="mb-3">
        <label for="vEmail" class="form-label fw-semibold">Correo Electrónico</label>
        <input 
          type="email" 
          name="vEmail" 
          id="vEmail" 
          class="form-control @error('vEmail') is-invalid @enderror" 
          placeholder="ejemplo@correo.com" 
          required
          maxlength="100"
          value="{{ old('vEmail') }}"
        >
        @error('vEmail')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <!-- Contraseña -->
      <div class="mb-2">
        <label for="vPassword" class="form-label fw-semibold">Contraseña</label>
        <input 
          type="password" 
          name="vPassword" 
          id="vPassword" 
          class="form-control @error('vPassword') is-invalid @enderror" 
          placeholder="••••••••" 
          required
          maxlength="150"
        >
        @error('vPassword')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <!-- Enlace: Olvidé mi contraseña -->
      <div class="text-end mb-3">
        <a href="{{ route('password.request') }}" class="text-decoration-none small text-primary fw-semibold">
          ¿Olvidaste tu contraseña?
        </a>
      </div>

      <!-- Recordarme -->
      <div class="form-check mb-3">
        <input 
          class="form-check-input" 
          type="checkbox" 
          name="remember" 
          id="remember"
          checked
        >
        <label class="form-check-label small text-muted" for="remember">
          Recordarme
        </label>
      </div>

      <!-- Botón Ingresar -->
      <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
        Ingresar
      </button>
    </form>

    <div class="text-center mt-3">
    <a href="{{ route('verification.resend-form') }}" class="text-decoration-none small">
        ¿No recibiste el email de verificación?
    </a>
</div>

    <div class="text-center mt-3">
      <p class="small mb-0">¿No tienes una cuenta?
        <a href="{{ route('usuarios.create') }}" class="text-decoration-none fw-semibold text-primary">Regístrate aquí</a>
      </p>
    </div>
  </div>
  <script src="{{ asset('js/usuarios/login.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.all.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
