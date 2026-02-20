<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmar nuevo correo</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f5f5f5; padding: 20px;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td style="text-align: center;">
                <table width="600" style="background-color: #ffffff; padding: 30px; border-radius: 6px;">
                    <tr>
                        <td>
                            <h2 style="color:#333;">Hola {{ $nombre }},</h2>

                            <p>
                                Has solicitado cambiar tu correo electrónico en tu cuenta.
                            </p>

                            <p>
                                Para confirmar el nuevo correo, haz clic en el siguiente botón:
                            </p>

                            <p style="text-align:center; margin: 30px 0;">
                                <a href="{{ $verificationUrl }}"
                                   style="background-color:#0d6efd; color:#ffffff; padding:12px 20px;
                                   text-decoration:none; border-radius:4px;">
                                    Confirmar nuevo correo
                                </a>
                            </p>

                            <p>
                                Si tú no solicitaste este cambio, ignora este correo.
                            </p>

                            <p style="color:#888; font-size:12px;">
                                Este enlace expirará por seguridad.
                            </p>

                            <hr>

                            <p style="font-size:12px; color:#aaa;">
                                © {{ date('Y') }} Ecommerce Agave
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
