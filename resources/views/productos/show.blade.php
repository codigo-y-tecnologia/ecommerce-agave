<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $producto->vNombre }} - Detalles del Producto</title>
</head>
<body>
    <div>
        <a href="javascript:history.back()">← Volver</a>
        <h1>Detalles del Producto</h1>
    </div>

    <div>
        <div>
            <div>
                <!-- Columna de imágenes -->
                <div>
                    @if(count($producto->imagenes) > 0)
                        <div>
                            <div>
                                <img id="mainImage" src="{{ $producto->imagenes[0] }}" 
                                     alt="{{ $producto->vNombre }}" style="max-width: 100%; height: auto;">
                                
                                @if(count($producto->imagenes) > 1)
                                    <div>
                                        <span id="currentImage">1</span> / <span id="totalImages">{{ count($producto->imagenes) }}</span>
                                    </div>
                                    <div>
                                        <button onclick="changeImage(-1)">←</button>
                                        <button onclick="changeImage(1)">→</button>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Miniaturas -->
                        @if(count($producto->imagenes) > 1)
                            <div>
                                @foreach($producto->imagenes as $index => $imagen)
                                    <img src="{{ $imagen }}" 
                                         alt="{{ $producto->vNombre }} - Imagen {{ $index + 1 }}"
                                         style="width: 80px; height: 80px; object-fit: cover; cursor: pointer; border: 2px solid {{ $index === 0 ? '#007bff' : 'transparent' }};"
                                         onclick="selectImage({{ $index }})">
                                @endforeach
                            </div>
                        @endif
                    @else
                        <div>
                            <p>No hay imágenes disponibles</p>
                        </div>
                    @endif
                </div>

                <!-- Columna de información -->
                <div>
                    <h1>{{ $producto->vNombre }}</h1>
                    
                    <div>
                        <strong>Precio: ${{ number_format($producto->dPrecio_venta, 2) }}</strong>
                    </div>

                    <div>
                        @if($producto->iStock > 10)
                            ✅ En stock ({{ $producto->iStock }} unidades)
                        @elseif($producto->iStock > 0)
                            ⚠️ Stock bajo ({{ $producto->iStock }} unidades)
                        @else
                            ❌ Sin stock
                        @endif
                    </div>

                    <div>
                        <div>
                            <strong>Código:</strong> {{ $producto->vCodigo_barras }}
                        </div>
                        <div>
                            <strong>Categoría:</strong> {{ $producto->categoria->vNombre ?? 'N/A' }}
                        </div>
                        <div>
                            <strong>Marca:</strong> {{ $producto->marca->vNombre ?? 'N/A' }}
                        </div>
                    </div>

                    @if($producto->etiquetas->count() > 0)
                        <div>
                            <strong>Etiquetas:</strong><br>
                            @foreach($producto->etiquetas as $etiqueta)
                                <span>{{ $etiqueta->vNombre }}</span>
                            @endforeach
                        </div>
                    @endif

                    @if($producto->tDescripcion_corta)
                        <div>
                            <h3>Descripción</h3>
                            <p>{{ $producto->tDescripcion_corta }}</p>
                        </div>
                    @endif

                    @if($producto->tDescripcion_larga)
                        <div>
                            <h3>Información detallada</h3>
                            <p>{{ $producto->tDescripcion_larga }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentImageIndex = 0;
        const totalImages = {{ count($producto->imagenes) }};
        const images = @json($producto->imagenes);

        function changeImage(direction) {
            currentImageIndex += direction;
            
            if (currentImageIndex < 0) {
                currentImageIndex = totalImages - 1;
            } else if (currentImageIndex >= totalImages) {
                currentImageIndex = 0;
            }
            
            updateMainImage();
        }

        function selectImage(index) {
            currentImageIndex = index;
            updateMainImage();
        }

        function updateMainImage() {
            // Update main image
            document.getElementById('mainImage').src = images[currentImageIndex];
            
            // Update counter
            document.getElementById('currentImage').textContent = currentImageIndex + 1;
            
            // Update thumbnails
            document.querySelectorAll('img[style*="cursor: pointer"]').forEach((thumb, index) => {
                if (index === currentImageIndex) {
                    thumb.style.border = '2px solid #007bff';
                } else {
                    thumb.style.border = '2px solid transparent';
                }
            });
        }

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft') {
                changeImage(-1);
            } else if (e.key === 'ArrowRight') {
                changeImage(1);
            }
        });
    </script>
</body>
</html>