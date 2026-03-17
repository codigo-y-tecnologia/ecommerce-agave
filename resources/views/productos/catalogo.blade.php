<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Productos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 15px 0;
            border-bottom: 1px solid #dee2e6;
            margin-bottom: 30px;
        }
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        .back-btn {
            color: #495057;
            text-decoration: none;
            font-weight: bold;
            padding: 8px 16px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            background: white;
        }
        .back-btn:hover {
            color: #007bff;
            border-color: #007bff;
        }
        .productos-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .producto-card {
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
            text-align: left;
            border: 1px solid #dee2e6;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .producto-card:hover {
            transform: translateY(-2px);
        }
        .producto-imagen {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        .no-imagen {
            width: 100%;
            height: 200px;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            margin-bottom: 10px;
            color: #6c757d;
        }
        .producto-card h3 {
            margin-top: 0;
            margin-bottom: 8px;
            color: #333;
        }
        .producto-precio {
            font-weight: bold;
            color: #28a745;
            margin-bottom: 5px;
            font-size: 1.2rem;
        }
        .badge {
            display: inline-block;
            padding: 3px 6px;
            font-size: 12px;
            background-color: #6c757d;
            color: white;
            border-radius: 4px;
            margin-right: 3px;
        }
        .ver-detalle {
            margin-top: 10px;
            text-align: center;
        }
        .ver-detalle a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }
        .ver-detalle a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <a href="javascript:history.back()" class="back-btn">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="container">
        <h1>Catálogo de Productos</h1>
        
        @if ($productos->count() > 0)
            <div class="productos-container">
                @foreach ($productos as $producto)
                    <div class="producto-card" onclick="window.location.href='{{ route('productos.show.public', $producto->id_producto) }}'">
                        @if(count($producto->imagenes) > 0)
                            <img src="{{ $producto->imagenes[0] }}" alt="{{ $producto->vNombre }}" class="producto-imagen">
                        @else
                            <div class="no-imagen">
                                <span>Sin imagen</span>
                            </div>
                        @endif
                        
                        <h3>{{ $producto->vNombre }}</h3>
                        <p class="producto-precio">${{ number_format($producto->dPrecio_venta, 2) }}</p>
                        <p><strong>Stock:</strong> {{ $producto->iStock }}</p>
                        <p><strong>Categoría:</strong> {{ $producto->categoria->vNombre ?? 'N/A' }}</p>
                        <p><strong>Marca:</strong> {{ $producto->marca->vNombre ?? 'N/A' }}</p>
                        @if ($producto->etiquetas->count() > 0)
                            <p>
                                @foreach ($producto->etiquetas as $etiqueta)
                                    <span class="badge">{{ $etiqueta->vNombre }}</span>
                                @endforeach
                            </p>
                        @endif
                        
                        <div class="ver-detalle">
                            <a href="{{ route('productos.show.public', $producto->id_producto) }}">Ver detalle del producto</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p>No hay productos disponibles.</p>
        @endif
    </div>
</body>
</html>
