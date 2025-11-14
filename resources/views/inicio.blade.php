<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Inicio - Ecommerce Agave</title>
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
        main {
            padding: 30px;
            text-align: center;
        }
        button.logout-btn {
            background: none; 
            color: #495057; 
            border: none; 
            cursor: pointer;
            font-weight: bold;
            font-size: inherit;
        }
        button.logout-btn:hover {
            text-decoration: underline;
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
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .producto-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .producto-imagen {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        .no-imagen {
            width: 100%;
            height: 150px;
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
            font-size: 16px;
            line-height: 1.3;
        }
        .producto-precio {
            font-weight: bold;
            color: #28a745;
            margin-bottom: 5px;
            font-size: 18px;
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

        /* Estilos para la barra de búsqueda */
        .barra-busqueda-inicio {
            text-align: center;
            margin: 20px 0;
            padding: 0 20px;
        }
        .barra-busqueda-inicio form {
            display: inline-block;
            max-width: 600px;
            width: 100%;
        }
        .barra-busqueda-inicio input[type="text"] {
            padding: 12px 20px;
            width: 70%;
            border: 2px solid #007bff;
            border-radius: 25px 0 0 25px;
            font-size: 16px;
            outline: none;
        }
        .barra-busqueda-inicio button {
            padding: 12px 25px;
            background: #007bff;
            color: white;
            border: 2px solid #007bff;
            border-radius: 0 25px 25px 0;
            font-size: 16px;
            cursor: pointer;
            margin-left: -5px;
        }
        .barra-busqueda-inicio button:hover {
            background: #0056b3;
            border-color: #0056b3;
        }

        /* Estilos para información de auth */
        .auth-info {
            text-align: center;
            font-size: 14px;
            color: #666;
            margin: 10px 0;
            padding: 10px;
            background: #fff;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }

        /* Mensaje de bienvenida */
        .bienvenida {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            margin-bottom: 20px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .barra-busqueda-inicio input[type="text"] {
                width: 60%;
            }
            
            .productos-container {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
            
            nav.navbar ul {
                flex-direction: column;
                gap: 10px;
            }
        }

        @media (max-width: 480px) {
            .barra-busqueda-inicio input[type="text"] {
                width: 100%;
                border-radius: 25px;
                margin-bottom: 10px;
            }
            
            .barra-busqueda-inicio button {
                width: 100%;
                border-radius: 25px;
                margin-left: 0;
            }
            
            .barra-busqueda-inicio form {
                display: flex;
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <header>
          @auth
          <p>Hola, {{ Auth::user()->vNombre }}. Tu rol es: {{ Auth::user()->eRol }}</p>

          @if(Auth::user()->eRol === 'cliente')
           {{-- Panel de cliente --}}
         <nav class="navbar">
    <ul>
            <li><a href="{{ route('carrito.index') }}">🛒 Mi Carrito</a></li>
            <li><a href="#">Mis Pedidos</a></li>
        </ul>
    </nav>

    @elseif(Auth::user()->eRol === 'admin')
        {{-- Panel de administrador --}}
        <nav class="navbar">
            <ul>
                <li><a href="#">Gestionar Usuarios</a></li>
                <li><a href="#">Ver Carritos de Clientes</a></li>
                <li><a href="#">Reportes</a></li>
            </ul>
        </nav>
    @elseif(Auth::user()->eRol === 'superadmin')
        {{-- Panel de superadmin --}}
        <nav class="navbar">
            <ul>
                <li><a href="#">⚙ Configuración General</a></li>
                <li><a href="#">Gestión Avanzada de Usuarios</a></li>
                <li><a href="#">Monitoreo del Sistema</a></li>
            </ul>
        </nav>
    @endif

    {{-- Botón de cerrar sesión --}}
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Cerrar sesión</button>
    </form>
    @endauth

        @guest
            <p>No has iniciado sesión.</p>
            <li><a href="{{ route('login') }}">Ingresar</a></li>
            <li><a href="{{ route('usuarios.create') }}">Crear Cuenta</a></li>
        @endguest
    </header>
    
    <main>
        
        <h1>Bienvenido a la tienda en línea</h1>
        <p>Explora nuestros productos y realiza tus compras de manera fácil y segura.</p>

        <h2>Agregar productos al carrito</h2>

@if($productos->isEmpty())
    <p>No hay productos disponibles por ahora.</p>
@else
    <div class="row">
    @foreach($productos as $producto)
        <div class="col-md-4 mb-3">
            <div class="card p-3">
                <h5>{{ $producto->vNombre }}</h5>
                <p>Precio: ${{ number_format($producto->dPrecio_venta,2) }}</p>

                <form action="{{ route('carrito.store', $producto->id_producto) }}" method="POST" class="d-flex align-items-center">
                    @csrf
                    <input type="number" name="cantidad" value="1" min="1" class="form-control w-25 me-2">
                    <button type="submit" class="btn btn-success btn-sm">🛒 Agregar</button>
                </form>
            </div>
        </div>
    @endforeach
    </div>
@endif

    </main>

    <script>
        // Funcionalidad adicional para mejorar la experiencia
        document.addEventListener('DOMContentLoaded', function() {
            // Agregar confirmación al cerrar sesión
            const logoutForms = document.querySelectorAll('form[action*="logout"]');
            logoutForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (!confirm('¿Estás seguro de que deseas cerrar sesión?')) {
                        e.preventDefault();
                    }
                });
            });

            // Mejorar la interactividad de las tarjetas de producto
            const productCards = document.querySelectorAll('.producto-card');
            productCards.forEach(card => {
                // Efecto de hover mejorado
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });

                // Click en cualquier parte de la tarjeta
                card.addEventListener('click', function() {
                    const link = this.querySelector('.ver-detalle a');
                    if (link) {
                        window.location.href = link.href;
                    }
                });
            });

            // Focus en la barra de búsqueda al presionar Ctrl+K
            document.addEventListener('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    const searchInput = document.querySelector('.barra-busqueda-inicio input[type="text"]');
                    if (searchInput) {
                        searchInput.focus();
                    }
                }
            });

            // Mostrar atajo de teclado en la barra de búsqueda
            const searchInput = document.querySelector('.barra-busqueda-inicio input[type="text"]');
            if (searchInput) {
                // Agregar placeholder dinámico
                const placeholders = [
                    "Buscar productos (agave, mezcal, espadín...)",
                    "Buscar por nombre, categoría o marca...",
                    "Presiona Ctrl+K para buscar rápidamente"
                ];
                let currentPlaceholder = 0;
                
                setInterval(() => {
                    searchInput.placeholder = placeholders[currentPlaceholder];
                    currentPlaceholder = (currentPlaceholder + 1) % placeholders.length;
                }, 3000);
            }
        });

        // Función para búsqueda rápida
        function busquedaRapida(termino) {
            window.location.href = "#" + encodeURIComponent(termino);
        }
    </script>
</body>
</html>