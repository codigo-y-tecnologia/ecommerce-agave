<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Lista de Deseos - Ecommerce Agave</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        header {
            background-color: #f8f9fa;
            padding: 15px 0;
            text-align: center;
            border-bottom: 1px solid #dee2e6;
        }

        nav.navbar {
            background-color: #e9ecef;
            padding: 10px 0;
        }

        nav.navbar ul {
            list-style: none;
            display: flex;
            justify-content: center;
            gap: 25px;
            padding: 0;
            margin: 0;
        }

        nav.navbar ul li {
            display: inline;
        }

        nav.navbar ul li a {
            color: #495057;
            text-decoration: none;
            font-weight: bold;
        }

        nav.navbar ul li a:hover {
            text-decoration: underline;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }

        .page-title {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        .favoritos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .favorito-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
        }

        .favorito-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .favorito-imagen {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .no-imagen {
            width: 100%;
            height: 200px;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
        }

        .favorito-content {
            padding: 15px;
        }

        .favorito-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #333;
            line-height: 1.3;
        }

        .favorito-precio {
            font-size: 18px;
            font-weight: bold;
            color: #28a745;
            margin-bottom: 8px;
        }

        .favorito-descuento {
            color: #dc3545;
            font-size: 14px;
            font-weight: bold;
            margin-left: 8px;
        }

        .precio-original {
            text-decoration: line-through;
            color: #6c757d;
            font-size: 14px;
            margin-right: 8px;
        }

        .favorito-stock {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }

        .stock-bajo {
            color: #dc3545;
            font-weight: bold;
        }

        .favorito-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            font-size: 14px;
            transition: all 0.3s ease;
            flex: 1;
        }

        .btn-primary {
            background: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background: #0056b3;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .empty-state .icon {
            font-size: 64px;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            margin-bottom: 15px;
            color: #333;
        }

        .badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #dc3545;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }

        .badge-descuento {
            background: #28a745;
        }

        .badge-stock {
            background: #ffc107;
            color: #000;
        }

        .notificaciones {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .notificacion-item {
            padding: 10px;
            margin-bottom: 10px;
            background: white;
            border-radius: 4px;
            border-left: 4px solid #007bff;
        }

        .notificacion-item.stock {
            border-left-color: #dc3545;
        }

        .notificacion-item.descuento {
            border-left-color: #28a745;
        }

        @media (max-width: 768px) {
            .favoritos-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }

            .favorito-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Ecommerce Agave</h1>
    </header>

    <nav class="navbar">
        <ul>
            <li><a href="{{ route('inicio') }}">Inicio</a></li>
            <li><a href="{{ route('busqueda.resultados') }}">Buscar Productos</a></li>
            <li><strong>❤️ Mi Lista de Deseos</strong></li>
            @auth('web')
                <li><a href="#">Mi Carrito</a></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                        @csrf
                        <button type="submit" style="background: none; border: none; color: #495057; cursor: pointer; font-weight: bold;">Cerrar Sesión</button>
                    </form>
                </li>
            @endauth
        </ul>
    </nav>

    <div class="container">
        <h1 class="page-title">Mi Lista de Deseos ❤️</h1>

        <!-- Notificaciones -->
        @php
            $notificaciones = app(App\Http\Controllers\FavoritoController::class)->verificarNotificaciones();
        @endphp

        @if(count($notificaciones) > 0)
            <div class="notificaciones">
                <h3>📢 Notificaciones importantes</h3>
                @foreach($notificaciones as $notificacion)
                    <div class="notificacion-item {{ $notificacion['tipo'] }}">
                        <strong>{{ $notificacion['mensaje'] }}</strong>
                        <br>
                        <a href="{{ route('productos.show.public', $notificacion['producto']->id_producto) }}" class="btn btn-primary" style="padding: 5px 10px; margin-top: 5px; font-size: 12px;">
                            Ver Producto
                        </a>
                    </div>
                @endforeach
            </div>
        @endif

        @if($favoritos->count() > 0)
            <div class="favoritos-grid">
                @foreach($favoritos as $favorito)
                    @php
                        $producto = $favorito->producto;
                        $tieneDescuento = $producto->tieneDescuento();
                        $estaBajoStock = $producto->estaBajoEnStock();
                    @endphp
                    
                    <div class="favorito-card">
                        @if($tieneDescuento)
                            <div class="badge badge-descuento">{{ $producto->porcentajeDescuento() }}% OFF</div>
                        @elseif($estaBajoStock)
                            <div class="badge badge-stock">¡Últimas unidades!</div>
                        @endif

                        @if(count($producto->imagenes) > 0)
                            <img src="{{ $producto->imagenes[0] }}" alt="{{ $producto->vNombre }}" class="favorito-imagen">
                        @else
                            <div class="no-imagen">
                                <span>Sin imagen</span>
                            </div>
                        @endif

                        <div class="favorito-content">
                            <h3 class="favorito-title">{{ $producto->vNombre }}</h3>
                            <div class="favorito-precio">
                                @if($tieneDescuento)
                                    <span class="precio-original">${{ number_format($producto->dPrecio_compra, 2) }}</span>
                                @endif
                                ${{ number_format($producto->dPrecio_venta, 2) }}
                                @if($tieneDescuento)
                                    <span class="favorito-descuento">
                                        -{{ $producto->porcentajeDescuento() }}%
                                    </span>
                                @endif
                            </div>
                            <div class="favorito-stock {{ $estaBajoStock ? 'stock-bajo' : '' }}">
                                Stock: {{ $producto->iStock }} unidades
                                @if($estaBajoStock)
                                    ⚠️ Se está agotando
                                @endif
                            </div>

                            <div class="favorito-actions">
                                <a href="{{ route('productos.show.public', $producto->id_producto) }}" class="btn btn-primary">
                                    Ver Producto
                                </a>
                                <button class="btn btn-danger btn-eliminar-favorito" data-producto="{{ $producto->id_producto }}">
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <div class="icon">❤️</div>
                <h3>Tu lista de deseos está vacía</h3>
                <p>Agrega productos que te gusten para verlos aquí y recibir notificaciones especiales.</p>
                <a href="{{ route('busqueda.resultados') }}" class="btn btn-primary" style="display: inline-block; width: auto;">
                    Explorar Productos
                </a>
            </div>
        @endif
    </div>

    <script>
        // Eliminar de favoritos
        document.querySelectorAll('.btn-eliminar-favorito').forEach(button => {
            button.addEventListener('click', function() {
                const productoId = this.getAttribute('data-producto');
                const card = this.closest('.favorito-card');
                
                if (confirm('¿Estás seguro de que quieres eliminar este producto de tu lista de deseos?')) {
                    fetch(`/favoritos/${productoId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            card.style.opacity = '0';
                            setTimeout(() => {
                                card.remove();
                                
                                // Si no quedan favoritos, mostrar estado vacío
                                if (document.querySelectorAll('.favorito-card').length === 0) {
                                    location.reload();
                                }
                            }, 300);
                        } else {
                            alert('Error al eliminar el producto: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al eliminar el producto');
                    });
                }
            });
        });
    </script>
</body>
</html>