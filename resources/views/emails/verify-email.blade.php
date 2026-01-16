<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Verifica tu correo</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 20px;">
    <div style="background-color: white; padding: 20px; border-radius: 10px; max-width: 600px; margin: auto;">
        <h2 style="color: #28a745;">¡Bienvenido a Ecommerce Agave, {{ $usuario->vNombre }}!</h2>
        <p>Gracias por registrarte. Antes de poder usar tu cuenta, necesitamos confirmar que este correo te pertenece.</p>

        <p>
            <a href="{{ url('/verificar-email/' . $token) }}"
               style="background-color: #28a745; color: white; padding: 10px 20px;
                      text-decoration: none; border-radius: 5px;">
                Verificar mi correo
            </a>
        </p>

        <p>Si no creaste una cuenta, puedes ignorar este mensaje.</p>

        <p style="font-size: 12px; color: gray;">Ecommerce Agave © {{ date('Y') }}</p>
    </div>
</body>
</html>
