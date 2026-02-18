<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Establecer contraseña</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex align-items-center justify-content-center min-vh-100">
    <div class="row w-100">
        <div class="col-md-6 col-lg-5 mx-auto">

            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-5">

                    <div class="text-center mb-4">
                        <h2 class="fw-bold">Establecer contraseña</h2>
                        <p class="text-muted small mb-0">
                            Para activar tu cuenta, establece una contraseña segura.
                        </p>
                    </div>

                    <div class="alert alert-info small">
                        La contraseña debe tener al menos <strong>8 caracteres</strong>,
                        incluir <strong>una mayúscula</strong>, <strong>una minúscula</strong>
                        y <strong>un número</strong>.
                    </div>

                    <form method="POST" action="{{ route('guardar.password', $token) }}">
                        @csrf

                        <div class="form-floating mb-3">
                            <input type="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   id="password"
                                   name="password"
                                   placeholder="Nueva contraseña"
                                   required>
                            <label for="password">Nueva contraseña</label>

                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-floating mb-4">
                            <input type="password"
                                   class="form-control"
                                   id="password_confirmation"
                                   name="password_confirmation"
                                   placeholder="Confirmar contraseña"
                                   required>
                            <label for="password_confirmation">Confirmar contraseña</label>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg rounded-3">
                                Guardar contraseña
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
