<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Cuenta creada</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6">

    <h2>¡Hola {{ $usuario->vNombre }}! 👋</h2>

    <p>
        Gracias por tu compra en <strong>{{ config('app.name') }}</strong>.
    </p>

    <p>
        Hemos creado automáticamente una cuenta para ti para que puedas:
    </p>

    <ul>
        <li>Ver tus pedidos</li>
        <li>Guardar direcciones</li>
        <li>Comprar más rápido</li>
    </ul>

    <p>
        Para activar tu cuenta y establecer tu contraseña, haz clic en el botón:
    </p>

    <p style="margin: 30px 0;">
        <a href="{{ url('/activar-cuenta/' . $token) }}"
           style="background:#111827;color:#fff;padding:12px 20px;
                  text-decoration:none;border-radius:6px;">
            Establecer contraseña
        </a>
    </p>

    <p style="font-size: 14px; color: #555;">
        Si tú no realizaste esta compra, puedes ignorar este correo.
    </p>

    <p>
        — {{ config('app.name') }}
    </p>

</body>
</html>