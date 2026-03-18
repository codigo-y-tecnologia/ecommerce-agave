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
        <a href="{{ route('busqueda.resultados', ['en_descuento' => '1']) }}" class="btn-banner descuento mb-3" id="banner-descuento">🔥 Ver Descuentos</a>
    @endauth

    @guest
    <!-- Banner de bienvenida -->
    <div class="banner-inicio">
        <h1 class="mb-3">Bienvenido a la tienda en línea 🛍️</h1>
        <p class="text-muted">
            Explora nuestros productos y realiza tus compras de manera fácil y segura.
        </p>
        <a href="{{ route('busqueda.resultados') }}" class="btn-banner">Explorar Productos</a>
        <a href="{{ route('busqueda.resultados', ['en_descuento' => '1']) }}" class="btn-banner descuento" id="banner-descuento">🔥 Ver Descuentos</a>
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

    <!-- SECCIÓN 1: PRODUCTOS EN DESCUENTO -->
    @if(isset($productosDescuento) && $productosDescuento->count() > 0)
    <div class="seccion-destacados" style="margin-top: 30px;">
        <h2 class="titulo-seccion descuento">
            🔥 Productos con Descuento
        </h2>
        
        <div class="productos-grid">
            @foreach($productosDescuento as $item)
                @php
                    $esVariacion = isset($item->id_variacion);
                    
                    if ($esVariacion) {
                        $tieneDescuento = $item->tieneDescuentoActivo();
                        $precioOriginal = $item->dPrecio;
                        $precioDescuento = $item->dPrecio_oferta;
                        $stock = $item->iStock;
                        $nombreProducto = $item->productoPadre->vNombre;
                        $nombreCompleto = $nombreProducto . ' - ' . $item->getAtributosTexto();
                        $imagen = $item->primera_imagen;
                        $url = route('productos.show.public', [$item->productoPadre->id_producto, 'variacion' => $item->id_variacion]);
                        $productoId = $item->productoPadre->id_producto;
                        $variacionId = $item->id_variacion;
                        $esFavorito = $item->esFavorito();
                        $atributosTexto = $item->getAtributosTexto();
                        $atributosCompletos = $item->getAtributosCompletosTexto();
                        $marca = $item->productoPadre->marca->vNombre ?? 'Marca genérica';
                        $sku = $item->vSKU;
                        $motivoOferta = $item->vMotivo_oferta ?? '';
                        $fechaInicio = $item->dFecha_inicio_oferta ? \Carbon\Carbon::parse($item->dFecha_inicio_oferta)->format('d/m') : '';
                        $fechaFin = $item->dFecha_fin_oferta ? \Carbon\Carbon::parse($item->dFecha_fin_oferta)->format('d/m') : '';
                    } else {
                        $tieneDescuento = $item->tieneDescuentoActivo();
                        $precioOriginal = $item->dPrecio_venta;
                        $precioDescuento = $item->dPrecio_oferta;
                        $stock = $item->iStock;
                        $nombreProducto = $item->vNombre;
                        $nombreCompleto = $nombreProducto;
                        $imagen = $item->primera_imagen;
                        $url = route('productos.show.public', $item->id_producto);
                        $productoId = $item->id_producto;
                        $variacionId = null;
                        $esFavorito = $item->esFavorito();
                        $atributosTexto = '';
                        $atributosCompletos = '';
                        $marca = $item->marca->vNombre ?? 'Marca genérica';
                        $sku = $item->vCodigo_barras;
                        $motivoOferta = $item->vMotivo_oferta ?? '';
                        $fechaInicio = $item->dFecha_inicio_oferta ? \Carbon\Carbon::parse($item->dFecha_inicio_oferta)->format('d/m') : '';
                        $fechaFin = $item->dFecha_fin_oferta ? \Carbon\Carbon::parse($item->dFecha_fin_oferta)->format('d/m') : '';
                    }
                    
                    $precioActual = $tieneDescuento ? $precioDescuento : $precioOriginal;
                    $porcentajeDescuento = $tieneDescuento ? round((($precioOriginal - $precioDescuento) / $precioOriginal) * 100) : 0;
                    
                    $envioGratis = $precioActual >= config('tienda.envio_gratis_desde');
                    $costoEnvio = config('tienda.costo_de_envio');
                    
                    $estaBajoStock = $stock > 0 && $stock <= 10;
                @endphp
                
                <div class="producto-card" onclick="window.location.href='{{ $url }}'">
                    <div class="producto-imagen-container">
                        <!-- Botón de corazón para favoritos -->
                        <button class="corazon-favorito {{ $esFavorito ? 'activo' : 'inactivo' }}" 
                                data-producto="{{ $productoId }}"
                                data-variacion="{{ $variacionId ?? '' }}"
                                data-tipo="{{ $esVariacion ? 'variacion' : 'producto' }}"
                                onclick="event.stopPropagation(); toggleFavorito(this, {{ $productoId }}, {{ $variacionId ?? 'null' }})"
                                title="{{ $esFavorito ? 'Quitar de favoritos' : 'Agregar a favoritos' }}">
                            {{ $esFavorito ? '❤️' : '🤍' }}
                        </button>

                        @if($tieneDescuento && $porcentajeDescuento > 0)
                            <div class="badge-descuento-rojo" title="{{ $motivoOferta ?: 'Descuento especial' }}">
                                -{{ $porcentajeDescuento }}% OFF
                                @if($motivoOferta)
                                    <br><small style="font-size: 8px;">{{ Str::limit($motivoOferta, 15) }}</small>
                                @endif
                            </div>
                        @elseif($estaBajoStock)
                            <div class="badge-stock-bajo">
                                ¡Últimas!
                            </div>
                        @endif

                        @if($esVariacion && !empty($atributosTexto))
                            <div class="badge-variacion" title="{{ $atributosCompletos }}">
                                {{ $atributosTexto }}
                            </div>
                        @endif

                        @if($imagen)
                            <img src="{{ $imagen }}" alt="{{ $nombreProducto }}" class="producto-imagen" loading="lazy">
                        @else
                            <div class="no-imagen">
                                <span>🛒 Sin imagen</span>
                            </div>
                        @endif
                    </div>
                    
                    <div class="producto-info">
                        <h3 title="{{ $nombreCompleto }}">{{ Str::limit($nombreCompleto, 50) }}</h3>

                        <div class="precio-container">
                            @if($tieneDescuento && $porcentajeDescuento > 0)
                                <span class="precio-original">${{ number_format($precioOriginal, 2) }}</span>
                                <div style="display: flex; align-items: center; flex-wrap: wrap;">
                                    <span class="precio-actual">${{ number_format($precioActual, 2) }} <small>sin interés</small></span>
                                    <span class="descuento-badge">{{ $porcentajeDescuento }}% OFF</span>
                                </div>
                                
                                @if($motivoOferta && $esVariacion)
                                    <div class="motivo-descuento" title="{{ $motivoOferta }}">
                                        <i class="fas fa-tag"></i> {{ Str::limit($motivoOferta, 25) }}
                                    </div>
                                @endif
                                
                                @if($fechaInicio && $fechaFin)
                                    <div class="periodo-descuento">
                                        <i class="fas fa-calendar-alt"></i> {{ $fechaInicio }} - {{ $fechaFin }}
                                    </div>
                                @endif
                            @else
                                <span class="precio-actual">${{ number_format($precioActual, 2) }} <small>sin interés</small></span>
                            @endif
                        </div>

                        <div class="envio-info">
                            @if($envioGratis)
                                <span class="envio-gratis">
                                    <span>🚚</span> Envío gratis
                                </span>
                            @else
                                <span class="envio-pago">
                                    <span>📦</span> + ${{ number_format($costoEnvio, 2) }} envío
                                </span>
                            @endif
                        </div>

                        <div class="stock-info {{ $stock > 10 ? 'stock-bueno' : ($stock > 0 ? 'stock-bajo' : 'sin-stock') }}">
                            @if($stock > 10)
                                ✅ En stock ({{ $stock }} disponibles)
                            @elseif($stock > 0)
                                ⚠️ Solo {{ $stock }} unidades
                            @else
                                ❌ Sin stock
                            @endif
                        </div>

                        <p style="font-size: 13px; color: #666; margin-bottom: 5px;">
                            {{ $marca }} | SKU: {{ $sku }}
                        </p>

                        <div class="ver-detalle">
                            <a href="{{ $url }}" onclick="event.stopPropagation();">Ver más</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div style="text-align: center; margin-top: 20px; margin-bottom: 30px;">
            <a href="{{ route('busqueda.resultados', ['en_descuento' => '1']) }}" class="btn btn-descuento">Ver Todos los Descuentos</a>
        </div>
    </div>
    @endif

    <!-- SECCIÓN 2: PRODUCTOS DESTACADOS -->
    <div class="seccion-destacados">
        <h2 class="titulo-seccion">Productos Destacados</h2>
        
        @if(isset($productosDestacados) && $productosDestacados->count() > 0)
            <div class="productos-grid">
                @foreach($productosDestacados as $item)
                    @php
                        $esVariacion = isset($item->id_variacion);
                        
                        if ($esVariacion) {
                            $tieneDescuento = $item->tieneDescuentoActivo();
                            $precioOriginal = $item->dPrecio;
                            $precioDescuento = $item->dPrecio_oferta;
                            $stock = $item->iStock;
                            $nombreProducto = $item->productoPadre->vNombre;
                            $nombreCompleto = $nombreProducto . ' - ' . $item->getAtributosTexto();
                            $imagen = $item->primera_imagen;
                            $url = route('productos.show.public', [$item->productoPadre->id_producto, 'variacion' => $item->id_variacion]);
                            $productoId = $item->productoPadre->id_producto;
                            $variacionId = $item->id_variacion;
                            $esFavorito = $item->esFavorito();
                            $atributosTexto = $item->getAtributosTexto();
                            $atributosCompletos = $item->getAtributosCompletosTexto();
                            $marca = $item->productoPadre->marca->vNombre ?? 'Marca genérica';
                            $sku = $item->vSKU;
                            $motivoOferta = $item->vMotivo_oferta ?? '';
                            $fechaInicio = $item->dFecha_inicio_oferta ? \Carbon\Carbon::parse($item->dFecha_inicio_oferta)->format('d/m') : '';
                            $fechaFin = $item->dFecha_fin_oferta ? \Carbon\Carbon::parse($item->dFecha_fin_oferta)->format('d/m') : '';
                        } else {
                            $tieneDescuento = $item->tieneDescuentoActivo();
                            $precioOriginal = $item->dPrecio_venta;
                            $precioDescuento = $item->dPrecio_oferta;
                            $stock = $item->iStock;
                            $nombreProducto = $item->vNombre;
                            $nombreCompleto = $nombreProducto;
                            $imagen = $item->primera_imagen;
                            $url = route('productos.show.public', $item->id_producto);
                            $productoId = $item->id_producto;
                            $variacionId = null;
                            $esFavorito = $item->esFavorito();
                            $atributosTexto = '';
                            $atributosCompletos = '';
                            $marca = $item->marca->vNombre ?? 'Marca genérica';
                            $sku = $item->vCodigo_barras;
                            $motivoOferta = $item->vMotivo_oferta ?? '';
                            $fechaInicio = $item->dFecha_inicio_oferta ? \Carbon\Carbon::parse($item->dFecha_inicio_oferta)->format('d/m') : '';
                            $fechaFin = $item->dFecha_fin_oferta ? \Carbon\Carbon::parse($item->dFecha_fin_oferta)->format('d/m') : '';
                        }
                        
                        $precioActual = $tieneDescuento ? $precioDescuento : $precioOriginal;
                        $porcentajeDescuento = $tieneDescuento ? round((($precioOriginal - $precioDescuento) / $precioOriginal) * 100) : 0;
                        
                        $envioGratis = $precioActual >= config('tienda.envio_gratis_desde');
                        $costoEnvio = config('tienda.costo_de_envio');
                        
                        $estaBajoStock = $stock > 0 && $stock <= 10;
                    @endphp
                    
                    <div class="producto-card" onclick="window.location.href='{{ $url }}'">
                        <div class="producto-imagen-container">
                            <button class="corazon-favorito {{ $esFavorito ? 'activo' : 'inactivo' }}" 
                                    data-producto="{{ $productoId }}"
                                    data-variacion="{{ $variacionId ?? '' }}"
                                    data-tipo="{{ $esVariacion ? 'variacion' : 'producto' }}"
                                    onclick="event.stopPropagation(); toggleFavorito(this, {{ $productoId }}, {{ $variacionId ?? 'null' }})"
                                    title="{{ $esFavorito ? 'Quitar de favoritos' : 'Agregar a favoritos' }}">
                                {{ $esFavorito ? '❤️' : '🤍' }}
                            </button>

                            @if($tieneDescuento && $porcentajeDescuento > 0)
                                <div class="badge-descuento-rojo" title="{{ $motivoOferta ?: 'Descuento especial' }}">
                                    -{{ $porcentajeDescuento }}%
                                    @if($motivoOferta)
                                        <br><small style="font-size: 8px;">{{ Str::limit($motivoOferta, 15) }}</small>
                                    @endif
                                </div>
                            @elseif($estaBajoStock)
                                <div class="badge-stock-bajo">
                                    ¡Últimas!
                                </div>
                            @endif

                            @if($esVariacion && !empty($atributosTexto))
                                <div class="badge-variacion" title="{{ $atributosCompletos }}">
                                    {{ $atributosTexto }}
                                </div>
                            @endif

                            @if($imagen)
                                <img src="{{ $imagen }}" alt="{{ $nombreProducto }}" class="producto-imagen" loading="lazy">
                            @else
                                <div class="no-imagen">
                                    <span>🛒 Sin imagen</span>
                                </div>
                            @endif
                        </div>
                        
                        <div class="producto-info">
                            <h3 title="{{ $nombreCompleto }}">{{ Str::limit($nombreCompleto, 50) }}</h3>

                            <div class="precio-container">
                                @if($tieneDescuento && $porcentajeDescuento > 0)
                                    <span class="precio-original">${{ number_format($precioOriginal, 2) }}</span>
                                    <div style="display: flex; align-items: center; flex-wrap: wrap;">
                                        <span class="precio-actual">${{ number_format($precioActual, 2) }} <small>sin interés</small></span>
                                        <span class="descuento-badge">{{ $porcentajeDescuento }}% OFF</span>
                                    </div>
                                    
                                    @if($motivoOferta && $esVariacion)
                                        <div class="motivo-descuento" title="{{ $motivoOferta }}">
                                            <i class="fas fa-tag"></i> {{ Str::limit($motivoOferta, 25) }}
                                        </div>
                                    @endif
                                    
                                    @if($fechaInicio && $fechaFin)
                                        <div class="periodo-descuento">
                                            <i class="fas fa-calendar-alt"></i> {{ $fechaInicio }} - {{ $fechaFin }}
                                        </div>
                                    @endif
                                @else
                                    <span class="precio-actual">${{ number_format($precioActual, 2) }} <small>sin interés</small></span>
                                @endif
                            </div>

                            <div class="envio-info">
                                @if($envioGratis)
                                    <span class="envio-gratis">
                                        <span>🚚</span> Envío gratis
                                    </span>
                                @else
                                    <span class="envio-pago">
                                        <span>📦</span> + ${{ number_format($costoEnvio, 2) }} envío
                                    </span>
                                @endif
                            </div>

                            <div class="stock-info {{ $stock > 10 ? 'stock-bueno' : ($stock > 0 ? 'stock-bajo' : 'sin-stock') }}">
                                @if($stock > 10)
                                    ✅ En stock ({{ $stock }} disponibles)
                                @elseif($stock > 0)
                                    ⚠️ Solo {{ $stock }} unidades
                                @else
                                    ❌ Sin stock
                                @endif
                            </div>

                            <p style="font-size: 13px; color: #666; margin-bottom: 5px;">
                                {{ $marca }} | SKU: {{ $sku }}
                            </p>

                            <div class="ver-detalle">
                                <a href="{{ $url }}" onclick="event.stopPropagation();">Ver más</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- SECCIÓN 3: PRODUCTOS RECOMENDADOS -->
        @if(isset($productosRecomendados) && $productosRecomendados->count() > 0)
        <h2 class="titulo-seccion" style="margin-top: 40px;">✨ Productos Recomendados</h2>
        
        <div class="productos-grid">
            @foreach($productosRecomendados as $item)
                @php
                    $esVariacion = isset($item->id_variacion);
                    
                    if ($esVariacion) {
                        $tieneDescuento = $item->tieneDescuentoActivo();
                        $precioOriginal = $item->dPrecio;
                        $precioDescuento = $item->dPrecio_oferta;
                        $stock = $item->iStock;
                        $nombreProducto = $item->productoPadre->vNombre;
                        $nombreCompleto = $nombreProducto . ' - ' . $item->getAtributosTexto();
                        $imagen = $item->primera_imagen;
                        $url = route('productos.show.public', [$item->productoPadre->id_producto, 'variacion' => $item->id_variacion]);
                        $productoId = $item->productoPadre->id_producto;
                        $variacionId = $item->id_variacion;
                        $esFavorito = $item->esFavorito();
                        $atributosTexto = $item->getAtributosTexto();
                        $atributosCompletos = $item->getAtributosCompletosTexto();
                        $marca = $item->productoPadre->marca->vNombre ?? 'Marca genérica';
                        $sku = $item->vSKU;
                        $motivoOferta = $item->vMotivo_oferta ?? '';
                        $fechaInicio = $item->dFecha_inicio_oferta ? \Carbon\Carbon::parse($item->dFecha_inicio_oferta)->format('d/m') : '';
                        $fechaFin = $item->dFecha_fin_oferta ? \Carbon\Carbon::parse($item->dFecha_fin_oferta)->format('d/m') : '';
                    } else {
                        $tieneDescuento = $item->tieneDescuentoActivo();
                        $precioOriginal = $item->dPrecio_venta;
                        $precioDescuento = $item->dPrecio_oferta;
                        $stock = $item->iStock;
                        $nombreProducto = $item->vNombre;
                        $nombreCompleto = $nombreProducto;
                        $imagen = $item->primera_imagen;
                        $url = route('productos.show.public', $item->id_producto);
                        $productoId = $item->id_producto;
                        $variacionId = null;
                        $esFavorito = $item->esFavorito();
                        $atributosTexto = '';
                        $atributosCompletos = '';
                        $marca = $item->marca->vNombre ?? 'Marca genérica';
                        $sku = $item->vCodigo_barras;
                        $motivoOferta = $item->vMotivo_oferta ?? '';
                        $fechaInicio = $item->dFecha_inicio_oferta ? \Carbon\Carbon::parse($item->dFecha_inicio_oferta)->format('d/m') : '';
                        $fechaFin = $item->dFecha_fin_oferta ? \Carbon\Carbon::parse($item->dFecha_fin_oferta)->format('d/m') : '';
                    }
                    
                    $precioActual = $tieneDescuento ? $precioDescuento : $precioOriginal;
                    $porcentajeDescuento = $tieneDescuento ? round((($precioOriginal - $precioDescuento) / $precioOriginal) * 100) : 0;
                    
                    $envioGratis = $precioActual >= config('tienda.envio_gratis_desde');
                    $costoEnvio = config('tienda.costo_de_envio');
                @endphp
                
                <div class="producto-card" onclick="window.location.href='{{ $url }}'">
                    <div class="producto-imagen-container">
                        <div class="badge-recomendado">
                            ✨ Recomendado
                        </div>

                        <button class="corazon-favorito {{ $esFavorito ? 'activo' : 'inactivo' }}" 
                                data-producto="{{ $productoId }}"
                                data-variacion="{{ $variacionId ?? '' }}"
                                data-tipo="{{ $esVariacion ? 'variacion' : 'producto' }}"
                                onclick="event.stopPropagation(); toggleFavorito(this, {{ $productoId }}, {{ $variacionId ?? 'null' }})"
                                title="{{ $esFavorito ? 'Quitar de favoritos' : 'Agregar a favoritos' }}">
                            {{ $esFavorito ? '❤️' : '🤍' }}
                        </button>

                        @if($tieneDescuento && $porcentajeDescuento > 0)
                            <div class="badge-descuento-rojo" title="{{ $motivoOferta ?: 'Descuento especial' }}">
                                -{{ $porcentajeDescuento }}%
                            </div>
                        @endif

                        @if($esVariacion && !empty($atributosTexto))
                            <div class="badge-variacion" title="{{ $atributosCompletos }}">
                                {{ $atributosTexto }}
                            </div>
                        @endif

                        @if($imagen)
                            <img src="{{ $imagen }}" alt="{{ $nombreProducto }}" class="producto-imagen" loading="lazy">
                        @else
                            <div class="no-imagen">
                                <span>🛒 Sin imagen</span>
                            </div>
                        @endif
                    </div>
                    
                    <div class="producto-info">
                        <h3 title="{{ $nombreCompleto }}">{{ Str::limit($nombreCompleto, 50) }}</h3>

                        <div class="precio-container">
                            @if($tieneDescuento && $porcentajeDescuento > 0)
                                <span class="precio-original">${{ number_format($precioOriginal, 2) }}</span>
                                <div style="display: flex; align-items: center; flex-wrap: wrap;">
                                    <span class="precio-actual">${{ number_format($precioActual, 2) }} <small>sin interés</small></span>
                                    <span class="descuento-badge">{{ $porcentajeDescuento }}% OFF</span>
                                </div>
                                
                                @if($motivoOferta && $esVariacion)
                                    <div class="motivo-descuento" title="{{ $motivoOferta }}">
                                        <i class="fas fa-tag"></i> {{ Str::limit($motivoOferta, 25) }}
                                    </div>
                                @endif
                                
                                @if($fechaInicio && $fechaFin)
                                    <div class="periodo-descuento">
                                        <i class="fas fa-calendar-alt"></i> {{ $fechaInicio }} - {{ $fechaFin }}
                                    </div>
                                @endif
                            @else
                                <span class="precio-actual">${{ number_format($precioActual, 2) }} <small>sin interés</small></span>
                            @endif
                        </div>

                        <div class="envio-info">
                            @if($envioGratis)
                                <span class="envio-gratis"><span>🚚</span> Envío gratis</span>
                            @else
                                <span class="envio-pago"><span>📦</span> + ${{ number_format($costoEnvio, 2) }} envío</span>
                            @endif
                        </div>

                        <div class="stock-info {{ $stock > 10 ? 'stock-bueno' : ($stock > 0 ? 'stock-bajo' : 'sin-stock') }}">
                            @if($stock > 0)
                                ✅ Disponible ({{ $stock }} unidades)
                            @else
                                ❌ Sin stock
                            @endif
                        </div>

                        <p style="font-size: 13px; color: #666; margin-bottom: 5px;">
                            {{ $marca }} | SKU: {{ $sku }}
                        </p>

                        <div class="ver-detalle">
                            <a href="{{ $url }}" onclick="event.stopPropagation();">Ver más</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @endif

        <!-- SECCIÓN 4: TODOS LOS PRODUCTOS Y VARIACIONES -->
        @if(isset($todosLosItems) && $todosLosItems->count() > 0)
        <h2 class="titulo-seccion" style="margin-top: 40px;">📦 Todos Nuestros Productos</h2>
        
        <div class="productos-grid">
            @foreach($todosLosItems as $item)
                @php
                    $esVariacion = isset($item->id_variacion);
                    
                    if ($esVariacion) {
                        $tieneDescuento = $item->tieneDescuentoActivo();
                        $precioOriginal = $item->dPrecio;
                        $precioDescuento = $item->dPrecio_oferta;
                        $stock = $item->iStock;
                        $nombreProducto = $item->productoPadre->vNombre;
                        $nombreCompleto = $nombreProducto . ' - ' . $item->getAtributosTexto();
                        $imagen = $item->primera_imagen;
                        $url = route('productos.show.public', [$item->productoPadre->id_producto, 'variacion' => $item->id_variacion]);
                        $productoId = $item->productoPadre->id_producto;
                        $variacionId = $item->id_variacion;
                        $esFavorito = $item->esFavorito();
                        $atributosTexto = $item->getAtributosTexto();
                        $atributosCompletos = $item->getAtributosCompletosTexto();
                        $marca = $item->productoPadre->marca->vNombre ?? 'Marca genérica';
                        $sku = $item->vSKU;
                        $motivoOferta = $item->vMotivo_oferta ?? '';
                        $fechaInicio = $item->dFecha_inicio_oferta ? \Carbon\Carbon::parse($item->dFecha_inicio_oferta)->format('d/m') : '';
                        $fechaFin = $item->dFecha_fin_oferta ? \Carbon\Carbon::parse($item->dFecha_fin_oferta)->format('d/m') : '';
                    } else {
                        $tieneDescuento = $item->tieneDescuentoActivo();
                        $precioOriginal = $item->dPrecio_venta;
                        $precioDescuento = $item->dPrecio_oferta;
                        $stock = $item->iStock;
                        $nombreProducto = $item->vNombre;
                        $nombreCompleto = $nombreProducto;
                        $imagen = $item->primera_imagen;
                        $url = route('productos.show.public', $item->id_producto);
                        $productoId = $item->id_producto;
                        $variacionId = null;
                        $esFavorito = $item->esFavorito();
                        $atributosTexto = '';
                        $atributosCompletos = '';
                        $marca = $item->marca->vNombre ?? 'Marca genérica';
                        $sku = $item->vCodigo_barras;
                        $motivoOferta = $item->vMotivo_oferta ?? '';
                        $fechaInicio = $item->dFecha_inicio_oferta ? \Carbon\Carbon::parse($item->dFecha_inicio_oferta)->format('d/m') : '';
                        $fechaFin = $item->dFecha_fin_oferta ? \Carbon\Carbon::parse($item->dFecha_fin_oferta)->format('d/m') : '';
                    }
                    
                    $precioActual = $tieneDescuento ? $precioDescuento : $precioOriginal;
                    $porcentajeDescuento = $tieneDescuento ? round((($precioOriginal - $precioDescuento) / $precioOriginal) * 100) : 0;
                    
                    $envioGratis = $precioActual >= config('tienda.envio_gratis_desde');
                    $costoEnvio = config('tienda.costo_de_envio');
                    
                    $estaBajoStock = $stock > 0 && $stock <= 10;
                @endphp
                
                <div class="producto-card" onclick="window.location.href='{{ $url }}'">
                    <div class="producto-imagen-container">
                        <button class="corazon-favorito {{ $esFavorito ? 'activo' : 'inactivo' }}" 
                                data-producto="{{ $productoId }}"
                                data-variacion="{{ $variacionId ?? '' }}"
                                data-tipo="{{ $esVariacion ? 'variacion' : 'producto' }}"
                                onclick="event.stopPropagation(); toggleFavorito(this, {{ $productoId }}, {{ $variacionId ?? 'null' }})"
                                title="{{ $esFavorito ? 'Quitar de favoritos' : 'Agregar a favoritos' }}">
                            {{ $esFavorito ? '❤️' : '🤍' }}
                        </button>

                        @if($tieneDescuento && $porcentajeDescuento > 0)
                            <div class="badge-descuento-rojo" title="{{ $motivoOferta ?: 'Descuento especial' }}">
                                -{{ $porcentajeDescuento }}% OFF
                                @if($motivoOferta)
                                    <br><small style="font-size: 8px;">{{ Str::limit($motivoOferta, 15) }}</small>
                                @endif
                            </div>
                        @elseif($estaBajoStock)
                            <div class="badge-stock-bajo">
                                ¡Últimas!
                            </div>
                        @endif

                        @if($esVariacion && !empty($atributosTexto))
                            <div class="badge-variacion" title="{{ $atributosCompletos }}">
                                {{ $atributosTexto }}
                            </div>
                        @endif

                        @if($imagen)
                            <img src="{{ $imagen }}" alt="{{ $nombreProducto }}" class="producto-imagen" loading="lazy">
                        @else
                            <div class="no-imagen">
                                <span>🛒 Sin imagen</span>
                            </div>
                        @endif
                    </div>
                    
                    <div class="producto-info">
                        <h3 title="{{ $nombreCompleto }}">{{ Str::limit($nombreCompleto, 50) }}</h3>

                        <div class="precio-container">
                            @if($tieneDescuento && $porcentajeDescuento > 0)
                                <span class="precio-original">${{ number_format($precioOriginal, 2) }}</span>
                                <div style="display: flex; align-items: center; flex-wrap: wrap;">
                                    <span class="precio-actual">${{ number_format($precioActual, 2) }} <small>sin interés</small></span>
                                    <span class="descuento-badge">{{ $porcentajeDescuento }}% OFF</span>
                                </div>
                                
                                @if($motivoOferta && $esVariacion)
                                    <div class="motivo-descuento" title="{{ $motivoOferta }}">
                                        <i class="fas fa-tag"></i> {{ Str::limit($motivoOferta, 25) }}
                                    </div>
                                @endif
                                
                                @if($fechaInicio && $fechaFin)
                                    <div class="periodo-descuento">
                                        <i class="fas fa-calendar-alt"></i> {{ $fechaInicio }} - {{ $fechaFin }}
                                    </div>
                                @endif
                            @else
                                <span class="precio-actual">${{ number_format($precioActual, 2) }} <small>sin interés</small></span>
                            @endif
                        </div>

                        <div class="envio-info">
                            @if($envioGratis)
                                <span class="envio-gratis">
                                    <span>🚚</span> Envío gratis
                                </span>
                            @else
                                <span class="envio-pago">
                                    <span>📦</span> + ${{ number_format($costoEnvio, 2) }} envío
                                </span>
                            @endif
                        </div>

                        <div class="stock-info {{ $stock > 10 ? 'stock-bueno' : ($stock > 0 ? 'stock-bajo' : 'sin-stock') }}">
                            @if($stock > 10)
                                ✅ En stock ({{ $stock }} disponibles)
                            @elseif($stock > 0)
                                ⚠️ Solo {{ $stock }} unidades
                            @else
                                ❌ Sin stock
                            @endif
                        </div>

                        <p style="font-size: 13px; color: #666; margin-bottom: 5px;">
                            {{ $marca }} | SKU: {{ $sku }}
                        </p>

                        <div class="ver-detalle">
                            <a href="{{ $url }}" onclick="event.stopPropagation();">Ver más</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- PAGINACIÓN -->
        <div class="paginacion">
            {{ $todosLosItems->links() }}
        </div>

        <div style="text-align: center; margin-top: 20px; margin-bottom: 20px;">
            <a href="{{ route('busqueda.resultados') }}" class="boton">Ver Todos los Productos</a>
        </div>
        @endif
    </div>
</main>

<script>
         // Configurar CSRF token para todas las peticiones fetch
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        let currentToast = null;
        let toastTimeout = null;

        function removeNotification() {
            if (currentToast) {
                currentToast.classList.remove('show');
                setTimeout(() => {
                    if (currentToast && currentToast.parentNode) {
                        currentToast.parentNode.removeChild(currentToast);
                    }
                    currentToast = null;
                }, 300);
            }
            if (toastTimeout) {
                clearTimeout(toastTimeout);
                toastTimeout = null;
            }
        }

        function showNotification(message, type = 'success') {
            removeNotification();
            
            const toast = document.createElement('div');
            toast.className = `toast-single ${type}`;
            
            let icon = '✅';
            if (type === 'error') icon = '❌';
            if (type === 'info') icon = 'ℹ️';
            
            const cleanMessage = message.replace('✅', '').replace('❌', '').trim();
            
            toast.innerHTML = `
                <span class="toast-icon">${icon}</span>
                <span class="toast-message">${cleanMessage}</span>
            `;
            
            document.body.appendChild(toast);
            currentToast = toast;
            
            setTimeout(() => toast.classList.add('show'), 10);
            
            toastTimeout = setTimeout(() => {
                if (toast.classList.contains('show')) {
                    toast.classList.remove('show');
                    setTimeout(() => {
                        if (toast.parentNode) {
                            toast.parentNode.removeChild(toast);
                        }
                        if (currentToast === toast) {
                            currentToast = null;
                        }
                        toastTimeout = null;
                    }, 400);
                }
            }, 3000);
        }

        function toggleFavorito(button, productoId, variacionId = null) {
            if (button.disabled) return;
            
            const estabaActivo = button.classList.contains('activo');
            
            button.disabled = true;
            button.classList.add('loading');
            button.innerHTML = '⏳';
            
            @auth
                // Usuario autenticado - ruta directa
                const url = variacionId 
                    ? `/favoritos/toggle-variacion/${variacionId}`
                    : `/favoritos/toggle-producto/${productoId}`;
                    
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(async response => {
                    if (!response.ok) {
                        if (response.status === 401) {
                            const redirectUrl = new URL('{{ route("login") }}');
                            redirectUrl.searchParams.set('from_favoritos', 'true');
                            redirectUrl.searchParams.set('redirect', window.location.href);
                            redirectUrl.searchParams.set('producto', productoId);
                            if (variacionId) {
                                redirectUrl.searchParams.set('variacion', variacionId);
                            }
                            window.location.href = redirectUrl.toString();
                            return null;
                        }
                        const errorData = await response.json().catch(() => ({}));
                        throw new Error(errorData.message || `HTTP ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data) return;
                    
                    if (data.success) {
                        if (data.action === 'added') {
                            button.classList.remove('inactivo');
                            button.classList.add('activo');
                            button.innerHTML = '❤️';
                            button.setAttribute('title', 'Quitar de favoritos');
                            
                            let tipoTexto = data.tipo === 'variacion' ? 'Variación' : 'Producto';
                            showNotification(`✅ ${tipoTexto} agregado a favoritos`, 'success');
                            
                            localStorage.setItem('last_favorito_action', 'added');
                            localStorage.setItem('last_favorito_id', variacionId || productoId);
                            localStorage.setItem('last_favorito_tipo', data.tipo);
                            localStorage.setItem('last_favorito_time', Date.now());
                        } else {
                            button.classList.remove('activo');
                            button.classList.add('inactivo');
                            button.innerHTML = '🤍';
                            button.setAttribute('title', 'Agregar a favoritos');
                            
                            let tipoTexto = data.tipo === 'variacion' ? 'Variación' : 'Producto';
                            showNotification(`❌ ${tipoTexto} eliminado de favoritos`, 'error');
                            
                            localStorage.setItem('last_favorito_action', 'removed');
                            localStorage.setItem('last_favorito_id', variacionId || productoId);
                            localStorage.setItem('last_favorito_tipo', data.tipo);
                            localStorage.setItem('last_favorito_time', Date.now());
                        }
                    } else {
                        // Revertir cambios si hubo error
                        revertirEstadoFavorito(button, estabaActivo);
                        showNotification('❌ ' + (data.message || 'Error al gestionar favoritos'), 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    revertirEstadoFavorito(button, estabaActivo);
                    showNotification('❌ ' + error.message, 'error');
                })
                .finally(() => {
                    setTimeout(() => {
                        button.disabled = false;
                        button.classList.remove('loading');
                    }, 500);
                });
            @else
                // Invitado - verificar estado actual primero
                const checkUrl = variacionId 
                    ? `/favoritos-invitado/check/${productoId}/${variacionId}`
                    : `/favoritos-invitado/check/${productoId}`;
                
                // Verificar estado actual
                fetch(checkUrl, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error al verificar estado');
                    }
                    return response.json();
                })
                .then(checkData => {
                    if (checkData.success) {
                        // Proceder con el toggle
                        const toggleUrl = variacionId 
                            ? `/favoritos-invitado/toggle-variacion/${variacionId}`
                            : `/favoritos-invitado/toggle-producto/${productoId}`;
                        
                        return fetch(toggleUrl, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        });
                    } else {
                        throw new Error('Error al verificar estado');
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw new Error(err.message || `HTTP ${response.status}`); });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        if (data.action === 'added') {
                            button.classList.remove('inactivo');
                            button.classList.add('activo');
                            button.innerHTML = '❤️';
                            button.setAttribute('title', 'Quitar de favoritos');
                            
                            let tipoTexto = data.tipo === 'variacion' ? 'Variación' : 'Producto';
                            showNotification(`✅ ${tipoTexto} agregado a favoritos`, 'success');
                            
                            localStorage.setItem('last_favorito_action', 'added');
                            localStorage.setItem('last_favorito_id', variacionId || productoId);
                            localStorage.setItem('last_favorito_tipo', data.tipo);
                            localStorage.setItem('last_favorito_time', Date.now());
                        } else {
                            button.classList.remove('activo');
                            button.classList.add('inactivo');
                            button.innerHTML = '🤍';
                            button.setAttribute('title', 'Agregar a favoritos');
                            
                            let tipoTexto = data.tipo === 'variacion' ? 'Variación' : 'Producto';
                            showNotification(`❌ ${tipoTexto} eliminado de favoritos`, 'error');
                            
                            localStorage.setItem('last_favorito_action', 'removed');
                            localStorage.setItem('last_favorito_id', variacionId || productoId);
                            localStorage.setItem('last_favorito_tipo', data.tipo);
                            localStorage.setItem('last_favorito_time', Date.now());
                        }
                    } else {
                        revertirEstadoFavorito(button, estabaActivo);
                        showNotification('❌ ' + (data.message || 'Error al gestionar favoritos'), 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    revertirEstadoFavorito(button, estabaActivo);
                    showNotification('❌ ' + error.message, 'error');
                })
                .finally(() => {
                    setTimeout(() => {
                        button.disabled = false;
                        button.classList.remove('loading');
                    }, 500);
                });
            @endauth
        }

        function revertirEstadoFavorito(button, estabaActivo) {
            if (estabaActivo) {
                button.classList.add('activo');
                button.classList.remove('inactivo');
                button.innerHTML = '❤️';
            } else {
                button.classList.remove('activo');
                button.classList.add('inactivo');
                button.innerHTML = '🤍';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Prevenir propagación en botones
            const buttons = document.querySelectorAll('.producto-card button, .producto-card a');
            buttons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            });

            // Links de descuento
            const linkDescuento = document.getElementById('link-descuento');
            if (linkDescuento) {
                linkDescuento.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.location.href = this.href;
                });
            }

            const bannerDescuento = document.getElementById('banner-descuento');
            if (bannerDescuento) {
                bannerDescuento.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.location.href = this.href;
                });
            }

            // Auto-focus en búsqueda para desktop
            if (window.innerWidth > 768) {
                const searchInput = document.querySelector('.barra-busqueda-principal input[type="text"]');
                if (searchInput) searchInput.focus();
            }

            // Verificar acciones recientes de localStorage
            const lastAction = localStorage.getItem('last_favorito_action');
            const lastId = localStorage.getItem('last_favorito_id');
            const lastTipo = localStorage.getItem('last_favorito_tipo');
            const lastTime = localStorage.getItem('last_favorito_time');
            
            if (lastAction && lastId && lastTime && (Date.now() - lastTime) < 5000) {
                // Buscar el botón correspondiente
                let selector;
                if (lastTipo === 'variacion') {
                    selector = `[data-variacion="${lastId}"]`;
                } else {
                    selector = `[data-producto="${lastId}"][data-variacion=""]`;
                }
                
                const button = document.querySelector(selector);
                if (button) {
                    if (lastAction === 'removed') {
                        button.classList.remove('activo');
                        button.classList.add('inactivo');
                        button.innerHTML = '🤍';
                        button.setAttribute('title', 'Agregar a favoritos');
                    } else if (lastAction === 'added') {
                        button.classList.remove('inactivo');
                        button.classList.add('activo');
                        button.innerHTML = '❤️';
                        button.setAttribute('title', 'Quitar de favoritos');
                    }
                }
            }
            
            // Limpiar localStorage después de 5 segundos
            setTimeout(() => {
                localStorage.removeItem('last_favorito_action');
                localStorage.removeItem('last_favorito_id');
                localStorage.removeItem('last_favorito_tipo');
                localStorage.removeItem('last_favorito_time');
            }, 5000);
        });
    </script>
@endsection
