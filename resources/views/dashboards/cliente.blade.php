@php
use Illuminate\Support\Str;
@endphp

@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard-cliente.css') }}">
@endpush

@section('title', 'Panel del Cliente')

@section('content')

    {{-- 👋 Mensaje de bienvenida --}}
    @auth
        <h1 class="mb-3">Bienvenido, {{ Auth::user()->vNombre }} 👋</h1>
        <p class="text-muted">
            Desde aquí puedes gestionar tus pedidos, direcciones y métodos de pago.
        </p>
        <a href="{{ route('busqueda.resultados') }}" class="btn-banner">Explorar Productos</a>
    @endauth

    @guest
    <!-- Banner de bienvenida -->
    <div class="banner-inicio">
        <h1 class="mb-3">Bienvenido a la tienda en línea 🛍️</h1>
        <p class="text-muted">
            Explora nuestros productos y realiza tus compras de manera fácil y segura.
        </p>
        <a href="{{ route('busqueda.resultados') }}" class="btn-banner">Explorar Productos</a>
    </div>
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
                                    onclick="event.stopPropagation(); toggleFavorito(this, {{ $producto->id_producto }})"
                                    title="{{ $esFavorito ? 'Quitar de favoritos' : 'Agregar a favoritos' }}">
                                {{ $esFavorito ? '❤️' : '🤍' }}
                            </button>

                            <!-- Solo badge de stock bajo si aplica -->
                            @if($estaBajoStock)
                                <div style="position: absolute; top: 15px; left: 15px; z-index: 99;">
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
                            <div style="color: #666; font-size: 14px; margin-bottom: 5px;">
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
        // VARIABLE GLOBAL para controlar UNA sola notificación
        let singleToast = null;
        let singleToastTimeout = null;

        // Función para toggle favoritos en productos
        function toggleFavorito(button, productoId) {
            if (button.disabled) return;
            button.disabled = true;

            // Verificar si el usuario está autenticado
            @if(!Auth::check())
                window.location.href = '{{ route("login") }}?from_favoritos=true&redirect=' + encodeURIComponent(window.location.href);
                return;
            @endif

            const esFavorito = button.classList.contains('activo');
            
            // Animación simple
            button.style.transform = 'scale(0.9)';
            
            // 1. ELIMINAR NOTIFICACIÓN ANTERIOR
            removeSingleToast();
            
            fetch(`/favoritos/toggle/${productoId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (response.status === 401) {
                    window.location.href = '{{ route("login") }}?from_favoritos=true&redirect=' + encodeURIComponent(window.location.href);
                    return null;
                }
                return response.json();
            })
            .then(data => {
                if (!data) return;
                
                if (data.success) {
                    if (data.action === 'added') {
                        // Cambiar a estado activo
                        button.classList.remove('inactivo');
                        button.classList.add('activo');
                        button.innerHTML = '❤️';
                        
                        // 2. MOSTRAR SOLO UNA NOTIFICACIÓN - 3 SEGUNDOS
                        showSingleNotification('Producto agregado a favoritos ✅', 3000);
                        
                    } else {
                        // Cambiar a estado inactivo
                        button.classList.remove('activo');
                        button.classList.add('inactivo');
                        button.innerHTML = '🤍';
                        
                        // 3. MOSTRAR SOLO UNA NOTIFICACIÓN - 3 SEGUNDOS
                        showSingleNotification('Producto eliminado de favoritos ❌', 3000);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showSingleNotification('Error de conexión ❌', 3000);
            })
            .finally(() => {
                setTimeout(() => {
                    button.disabled = false;
                    button.style.transform = '';
                }, 300);
            });
        }

        // FUNCIÓN PARA ELIMINAR NOTIFICACIÓN ANTERIOR
        function removeSingleToast() {
            if (singleToast) {
                singleToast.classList.remove('show');
                setTimeout(() => {
                    if (singleToast && singleToast.parentNode) {
                        singleToast.parentNode.removeChild(singleToast);
                    }
                    singleToast = null;
                }, 300);
            }
            
            if (singleToastTimeout) {
                clearTimeout(singleToastTimeout);
                singleToastTimeout = null;
            }
            
            // Eliminar cualquier otro toast
            const allToasts = document.querySelectorAll('.toast-single');
            allToasts.forEach(toast => {
                toast.classList.remove('show');
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 300);
            });
        }

        // FUNCIÓN PARA MOSTRAR UNA SOLA NOTIFICACIÓN
        function showSingleNotification(message, duration = 3000) {
            // 1. Eliminar notificación anterior
            removeSingleToast();
            
            // 2. Crear nueva notificación
            const toast = document.createElement('div');
            toast.className = 'toast-single';
            
            // Determinar emoji basado en el mensaje
            const emoji = message.includes('✅') ? '✅' : '❌';
            const cleanMessage = message.replace('✅', '').replace('❌', '').trim();
            
            toast.innerHTML = `
                <span style="font-size: 20px;">${emoji}</span>
                <span>${cleanMessage}</span>
            `;
            
            document.body.appendChild(toast);
            singleToast = toast;
            
            // 3. Mostrar con animación
            setTimeout(() => {
                toast.classList.add('show');
            }, 10);
            
            // 4. Configurar para eliminar después del tiempo especificado (3 SEGUNDOS PARA AMBOS)
            singleToastTimeout = setTimeout(() => {
                if (toast.classList.contains('show')) {
                    toast.classList.remove('show');
                    setTimeout(() => {
                        if (toast.parentNode) {
                            toast.parentNode.removeChild(toast);
                        }
                        singleToast = null;
                        singleToastTimeout = null;
                    }, 400);
                }
            }, duration);
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Limpiar localStorage
            localStorage.removeItem('favorito_removed');
            localStorage.removeItem('favorito_removed_time');
            localStorage.removeItem('favorito_added');
            localStorage.removeItem('favorito_added_time');
            
            // Auto-focus en la barra de búsqueda
            const searchInput = document.querySelector('.barra-busqueda-principal input[type="text"]');
            if (searchInput) {
                searchInput.focus();
            }
        });
    </script>
@endsection
