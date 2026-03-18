<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Deseos - Ecommerce Agave</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 400px;
            width: 90%;
        }

        .icon {
            font-size: 64px;
            color: #ff6b6b;
            margin-bottom: 20px;
        }

        h1 {
            color: #333;
            margin-bottom: 15px;
            font-size: 24px;
        }

        p {
            color: #666;
            margin-bottom: 25px;
            line-height: 1.5;
        }

        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background: #0056b3;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #545b62;
        }

        .btn-outline {
            background: transparent;
            color: #007bff;
            border: 2px solid #007bff;
        }

        .btn-outline:hover {
            background: #007bff;
            color: white;
        }

        @media (min-width: 480px) {
            .btn-group {
                flex-direction: row;
                justify-content: center;
            }
            
            .btn {
                min-width: 120px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">❤️</div>
        <h1>¡Hola! Para agregar favoritos, ingresa a tu cuenta</h1>
        <p>Guarda tus productos favoritos y recibe notificaciones cuando estén en descuento o se estén agotando.</p>
        
        <div class="btn-group">
            <a href="{{ route('usuarios.create') }}" class="btn btn-primary">Crear cuenta</a>
            <a href="{{ route('login') }}" class="btn btn-outline">Ingresar</a>
            <a href="{{ route('inicio') }}" class="btn btn-secondary">Seguir comprando</a>
        </div>
    </div>
</body>
</html>