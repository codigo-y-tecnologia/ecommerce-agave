<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Ecommerce Agave</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: #333;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 40px;
            position: relative;
        }

        /* Título principal */
        .main-title {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 10px;
            text-align: center;
        }

        .subtitle {
            color: #6b7280;
            font-size: 14px;
            text-align: center;
            margin-bottom: 30px;
            font-weight: 500;
        }

        /* Formulario */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 15px;
            color: #111827;
            background-color: white;
            transition: all 0.2s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: #7e22ce;
            box-shadow: 0 0 0 3px rgba(126, 34, 206, 0.1);
        }

        .form-input::placeholder {
            color: #9ca3af;
        }

        /* Checkbox Recordarme */
        .remember-checkbox {
            display: flex;
            align-items: center;
            margin-bottom: 24px;
        }

        .remember-checkbox input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin-right: 10px;
            accent-color: #7e22ce;
            cursor: pointer;
        }

        .remember-checkbox label {
            font-size: 14px;
            color: #4b5563;
            cursor: pointer;
            user-select: none;
        }

        /* Línea divisoria */
        .divider {
            height: 1px;
            background-color: #e5e7eb;
            margin: 24px 0;
            position: relative;
        }

        .divider::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to right, transparent, #e5e7eb, transparent);
        }

        /* Botón principal */
        .btn-primary {
            width: 100%;
            background-color: #7e22ce;
            color: white;
            border: none;
            padding: 14px 20px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease;
            margin-bottom: 20px;
        }

        .btn-primary:hover {
            background-color: #6b21a8;
        }

        .btn-primary:active {
            background-color: #5b1a96;
        }

        /* Enlaces */
        .links {
            text-align: center;
            margin-top: 20px;
        }

        .register-link {
            display: block;
            color: #7e22ce;
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            margin-bottom: 16px;
            transition: color 0.2s ease;
        }

        .register-link:hover {
            color: #6b21a8;
            text-decoration: underline;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .back-link:hover {
            color: #4b5563;
            text-decoration: underline;
        }

        .back-link i {
            margin-right: 6px;
            font-size: 12px;
        }

        /* Errores y mensajes */
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: flex-start;
            line-height: 1.4;
        }

        .alert-danger {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-info {
            background-color: #dbeafe;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }

        .alert i {
            margin-right: 8px;
            margin-top: 2px;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-container {
                padding: 30px 24px;
                max-width: 100%;
            }
            
            .main-title {
                font-size: 22px;
            }
        }

        /* Animación de carga */
        .btn-primary.loading {
            position: relative;
            color: transparent;
        }

        .btn-primary.loading::after {
            content: '';
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.8s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Títulos -->
        <h1 class="main-title">Iniciar Sesión</h1>
        <p class="subtitle">Accede a tu cuenta de Ecommerce Agave</p>

        <!-- Formulario -->
        <form method="POST" action="{{ route('login.submit') }}" class="login-form" id="loginForm">
            @csrf

            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> 
                    <div>
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Campo Correo Electrónico -->
            <div class="form-group">
                <label for="vEmail" class="form-label">Correo Electrónico</label>
                <input type="email" 
                       name="vEmail" 
                       id="vEmail" 
                       class="form-input" 
                       value="{{ old('vEmail') }}"
                       placeholder="ejemplo@correo.com"
                       required
                       autocomplete="email"
                       autofocus>
            </div>

            <!-- Campo Contraseña -->
            <div class="form-group">
                <label for="vPassword" class="form-label">Contraseña</label>
                <input type="password" 
                       name="vPassword" 
                       id="vPassword" 
                       class="form-input" 
                       placeholder="••••••••"
                       required
                       autocomplete="current-password">
            </div>

            <!-- Checkbox Recordarme -->
            <div class="remember-checkbox">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember">Recordarme</label>
            </div>

            <!-- Línea divisoria -->
            <div class="divider"></div>

            <!-- Botón de Inicio de Sesión -->
            <button type="submit" class="btn-primary" id="submitBtn">
                Iniciar Sesión
            </button>

            <!-- Enlace de registro -->
            <div class="links">
                <a href="{{ route('usuarios.create') }}" class="register-link">
                    ¿No tienes cuenta? Regístrate aquí
                </a>
                
                <!-- Enlace Volver al inicio -->
                <a href="{{ route('inicio.real') }}" class="back-link">
                    <i class="fas fa-arrow-left"></i> Volver al inicio
                </a>
            </div>
        </form>
    </div>

    <script>
        // Validación básica del formulario
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const email = document.getElementById('vEmail').value.trim();
            const password = document.getElementById('vPassword').value.trim();
            const submitBtn = document.getElementById('submitBtn');
            
            // Validar email
            if (!email) {
                e.preventDefault();
                alert('Por favor ingresa tu correo electrónico');
                document.getElementById('vEmail').focus();
                return;
            }
            
            // Validar formato de email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Por favor ingresa un correo electrónico válido');
                document.getElementById('vEmail').focus();
                return;
            }
            
            // Validar contraseña
            if (!password) {
                e.preventDefault();
                alert('Por favor ingresa tu contraseña');
                document.getElementById('vPassword').focus();
                return;
            }
            
            // Mostrar estado de carga
            submitBtn.disabled = true;
            submitBtn.classList.add('loading');
            submitBtn.textContent = '';
            
            // Simular envío (en producción esto sería real)
            setTimeout(() => {
                submitBtn.disabled = false;
                submitBtn.classList.remove('loading');
                submitBtn.textContent = 'Iniciar Sesión';
            }, 1500);
        });

        // Auto-enfocar el campo de email si está vacío
        document.addEventListener('DOMContentLoaded', function() {
            const emailField = document.getElementById('vEmail');
            if (emailField && !emailField.value) {
                emailField.focus();
            }
        });

        // Recordar credenciales usando localStorage
        document.addEventListener('DOMContentLoaded', function() {
            const rememberCheckbox = document.getElementById('remember');
            const emailField = document.getElementById('vEmail');
            const passwordField = document.getElementById('vPassword');
            
            // Cargar credenciales guardadas si existen
            const savedEmail = localStorage.getItem('agave_remember_email');
            const savedPassword = localStorage.getItem('agave_remember_password');
            
            if (savedEmail && savedPassword) {
                emailField.value = savedEmail;
                passwordField.value = savedPassword;
                rememberCheckbox.checked = true;
            }
            
            // Guardar credenciales cuando se marca el checkbox
            rememberCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    localStorage.setItem('agave_remember_email', emailField.value);
                    localStorage.setItem('agave_remember_password', passwordField.value);
                } else {
                    localStorage.removeItem('agave_remember_email');
                    localStorage.removeItem('agave_remember_password');
                }
            });
        });
    </script>
</body>
</html>