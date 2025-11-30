@php
use Illuminate\Support\Str;
@endphp

@extends('layouts.app')

@push('styles')
    @vite(['resources/css/dashboard-cliente.css'])
@endpush

@section('title', 'Panel del Cliente')

@section('content')

    {{-- 👋 Mensaje de bienvenida --}}
    @auth
        <h1 class="mb-3">Bienvenido, {{ Auth::user()->vNombre }} 👋</h1>
        <p class="text-muted">
            Desde aquí puedes gestionar tus pedidos, direcciones y métodos de pago.
        </p>
    @endauth

    @guest
        <h1 class="mb-3">Bienvenido a la tienda en línea 🛍️</h1>
        <p class="text-muted">
            Explora nuestros productos y realiza tus compras de manera fácil y segura.
        </p>
    @endguest

    <main>

        <!-- Mostrar mensajes de éxito -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info">
            {{ session('info') }}
        </div>
    @endif

    <hr class="my-4">
    <!-- Banner de bienvenida -->
    <div class="banner-inicio">
        <h2>Bienvenido a Ecommerce Agave</h2>
        <p>Descubre nuestra exclusiva selección de productos de agave y mezcal</p>
        <a href="{{ route('busqueda.resultados') }}" class="btn-banner">Explorar Productos</a>
    </div>

    <!-- Barra de búsqueda -->
        <div class="barra-busqueda-principal">
            <form action="{{ route('busqueda.resultados') }}" method="GET">
                <input type="text" name="q" placeholder="Buscar productos (agave, mezcal, espadín...)" 
                       value="{{ request('q') }}" autocomplete="off">
                <button type="submit">Buscar</button>
            </form>
        </div>

        {{-- 🛒 Sección de productos --}}
        <!-- Sección de productos destacados -->
    <div class="seccion-destacados">
        <h2 class="titulo-seccion">Productos Destacados</h2>
        
        @if(isset($productos) && $productos->count() > 0)
            <div class="productos-grid">
                @foreach($productos as $producto)
                    @php
                        $estaBajoStock = $producto->estaBajoEnStock();
                        $esFavorito = $producto->esFavorito();
                    @endphp
                    
                    <div class="producto-card" onclick="window.location.href='{{ route('productos.show.public', $producto->id_producto) }}'">
                        
                        <div class="producto-imagen-container">
                            <!-- BOTÓN DEL CORAZÓN - DENTRO DEL CONTENEDOR DE IMAGEN -->
                            <button class="corazon-favorito {{ $esFavorito ? 'activo' : 'inactivo' }}" 
                                    data-producto="{{ $producto->id_producto }}"
                                    data-es-favorito="{{ $esFavorito ? 'true' : 'false' }}"
                                    onclick="event.stopPropagation(); toggleFavorito(this, {{ $producto->id_producto }})">
                                <!-- El contenido se maneja con CSS -->
                            </button>

                            <!-- Solo badge de stock bajo si aplica -->
                            @if($estaBajoStock)
                                <div style="position: absolute; top: 10px; left: 10px; z-index: 99;">
                                    <span class="badge badge-stock">¡Últimas!</span>
                                </div>
                            @endif

                            @if(count($producto->imagenes) > 0)
                                <img src="{{ $producto->imagenes[0] }}" alt="{{ $producto->vNombre }}" class="producto-imagen">
                            @else
                                <div class="no-imagen">
                                    <span>🛒 Sin imagen</span>
                                </div>
                            @endif
                        </div>
                        
                        <div class="producto-info">
                            <h3>{{ $producto->vNombre }}</h3>

                            <!-- Precio - DATOS REALES -->
                            <div class="producto-precio">
                                ${{ number_format($producto->dPrecio_venta, 2) }}
                            </div>

                            <!-- Envío - INFORMACIÓN REAL -->
                            <div style="color: #666; font-size: 14px;">
                                📦 Envío gratis
                            </div>

                            <!-- Stock - DATOS REALES -->
                            <div class="stock-info {{ $producto->iStock > 10 ? 'stock-bueno' : ($producto->iStock > 0 ? 'stock-bajo' : 'sin-stock') }}">
                                @if($producto->iStock > 10)
                                    ✅ En stock
                                @elseif($producto->iStock > 0)
                                    ⚠️ Solo {{ $producto->iStock }} unidades
                                @else
                                    ❌ Sin stock
                                @endif
                            </div>

                            <div class="ver-detalle">
                                <a href="{{ route('productos.show.public', $producto->id_producto) }}" onclick="event.stopPropagation();">Ver detalle →</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="text-align: center; margin-top: 30px;">
                <a href="{{ route('busqueda.resultados') }}" class="btn">Ver Todos los Productos</a>
            </div>
        @else
            <div class="sin-resultados">
                <h3>No hay productos disponibles</h3>
                <p>Pronto agregaremos nuevos productos a nuestro catálogo.</p>
            </div>
        @endif
    </div>
</main>

<script>
        // Función para toggle favoritos en productos
        function toggleFavorito(button, productoId) {
            // Verificar primero si el usuario está autenticado
            @if(!Auth::check())
                // Si no está autenticado, redirigir directamente al login
                window.location.href = '{{ route("login") }}';
                return;
            @endif

            const esFavorito = button.getAttribute('data-es-favorito') === 'true';
            
            fetch(`/favoritos/toggle/${productoId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (response.status === 401) {
                    // No autenticado - redirigir al login
                    window.location.href = '{{ route("login") }}';
                    return null;
                }
                return response.json();
            })
            .then(data => {
                if (!data) return; // Si es null (401), ya redirigimos
                
                if (data.success) {
                    if (data.action === 'added') {
                        button.classList.remove('inactivo');
                        button.classList.add('activo');
                        button.setAttribute('data-es-favorito', 'true');
                        showNotification('✅ Producto agregado a favoritos');
                    } else {
                        button.classList.remove('activo');
                        button.classList.add('inactivo');
                        button.setAttribute('data-es-favorito', 'false');
                        showNotification('❌ Producto eliminado de favoritos');
                    }
                } else {
                    showNotification('❌ ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('❌ Error al gestionar favoritos');
            });
        }

        // Función para mostrar notificaciones
        function showNotification(message) {
            // Crear elemento de notificación
            const notification = document.createElement('div');
            notification.className = 'toast';
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            // Remover después de 3 segundos
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => {
                    if (document.body.contains(notification)) {
                        document.body.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }

        // Auto-focus en la barra de búsqueda al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('.barra-busqueda-principal input[type="text"]');
            if (searchInput) {
                searchInput.focus();
            }
        });
    </script>
@endsection
