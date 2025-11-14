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
    {{-- 🛒 Sección de productos --}}
    <hr class="my-4">
    <h2 class="mb-4">Explora nuestros productos</h2>

    <!-- Barra de búsqueda -->
        <div class="barra-busqueda-inicio">
            <form action="{{ route('busqueda.resultados') }}" method="GET">
                <input type="text" name="q" placeholder="Buscar productos (agave, mezcal, espadín, ancestral...)">
                <button type="submit">Buscar</button>
            </form>
        </div>

        @if ($productos->count() > 0)
            <p>Encontramos <strong>{{ $productos->count() }}</strong> productos disponibles</p>
            
            <div class="productos-container">
                @foreach ($productos as $producto)
                    <div class="producto-card" onclick="window.location.href='{{ route('productos.show.public', $producto->id_producto) }}'">
                        {{-- Mostrar primera imagen si existe --}}
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
                                <strong>Etiquetas:</strong>
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

            <!-- Sugerencia de búsqueda -->
            <div style="margin-top: 30px; padding: 15px; background: #e7f3ff; border-radius: 8px; border: 1px solid #b3d9ff;">
                <h3>¿No encuentras lo que buscas?</h3>
                <p>Usa nuestra barra de búsqueda para encontrar productos específicos por nombre, categoría o marca.</p>
                <p>Por ejemplo, busca: 
                    <a href="{{ route('busqueda.resultados') }}?q=agave" style="color: #007bff; text-decoration: none; margin: 0 5px;">"agave"</a>, 
                    <a href="{{ route('busqueda.resultados') }}?q=mezcal" style="color: #007bff; text-decoration: none; margin: 0 5px;">"mezcal"</a>, 
                    <a href="{{ route('busqueda.resultados') }}?q=ancestral" style="color: #007bff; text-decoration: none; margin: 0 5px;">"ancestral"</a>
                </p>
            </div>

        @else
            <div style="padding: 40px; background: #fff; border-radius: 8px; border: 1px solid #dee2e6;">
                <h3>No hay productos registrados aún</h3>
                <p>Próximamente tendremos disponibles nuestros productos de agave.</p>
        </div>
    @endif
</div>
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
            window.location.href = "{{ route('busqueda.resultados') }}?q=" + encodeURIComponent(termino);
        }
    </script>
@endsection
