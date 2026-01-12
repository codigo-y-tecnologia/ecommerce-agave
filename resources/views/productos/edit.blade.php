@extends('layouts.app')

@section('title', 'Editar Producto - ' . $producto->vNombre)
@section('content')
<div class="container">
    <h1><i class="fas fa-edit me-2"></i>Editar Producto</h1>

    <form action="{{ route('productos.update', $producto->id_producto) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- INFORMACIÓN BÁSICA DEL PRODUCTO -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white d-flex align-items-center">
                <i class="fas fa-info-circle me-2"></i>
                <h5 class="mb-0">Información Básica</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="vCodigo_barras" class="form-label fw-bold">
                                Código de barras <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="vCodigo_barras" id="vCodigo_barras" 
                                   class="form-control @error('vCodigo_barras') is-invalid @enderror"
                                   value="{{ old('vCodigo_barras', $producto->vCodigo_barras) }}" 
                                   maxlength="20" 
                                   required 
                                   oninput="soloNumeros(this)">
                            @error('vCodigo_barras')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="vNombre" class="form-label fw-bold">
                                Nombre del producto <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="vNombre" id="vNombre" 
                                   class="form-control @error('vNombre') is-invalid @enderror"
                                   value="{{ old('vNombre', $producto->vNombre) }}" 
                                   maxlength="100" 
                                   required>
                            @error('vNombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="dPrecio_compra" class="form-label fw-bold">
                                Precio de compra
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="text" name="dPrecio_compra" id="dPrecio_compra" 
                                       class="form-control @error('dPrecio_compra') is-invalid @enderror"
                                       value="{{ old('dPrecio_compra', $producto->dPrecio_compra) }}" 
                                       oninput="soloNumerosYDecimal(this)">
                                @error('dPrecio_compra')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="dPrecio_venta" class="form-label fw-bold">
                                Precio de venta <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="text" name="dPrecio_venta" id="dPrecio_venta" 
                                       class="form-control @error('dPrecio_venta') is-invalid @enderror"
                                       value="{{ old('dPrecio_venta', $producto->dPrecio_venta) }}" 
                                       required 
                                       oninput="soloNumerosYDecimal(this)">
                                @error('dPrecio_venta')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="iStock" class="form-label fw-bold">
                                Stock <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="iStock" id="iStock" 
                                   class="form-control @error('iStock') is-invalid @enderror"
                                   value="{{ old('iStock', $producto->iStock) }}" 
                                   required 
                                   oninput="soloNumeros(this)">
                            @error('iStock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CATEGORÍA Y MARCA -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white d-flex align-items-center">
                <i class="fas fa-tags me-2"></i>
                <h5 class="mb-0">Categorización</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="id_categoria" class="form-label fw-bold">
                                Categoría <span class="text-danger">*</span>
                            </label>
                            <select name="id_categoria" id="id_categoria" 
                                    class="form-select @error('id_categoria') is-invalid @enderror" 
                                    required>
                                <option value="">Seleccionar categoría</option>
                                @foreach ($categorias as $categoria)
                                    <option value="{{ $categoria->id_categoria }}" 
                                        {{ $categoria->id_categoria == old('id_categoria', $producto->id_categoria) ? 'selected' : '' }}>
                                        {{ $categoria->vNombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_categoria')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="id_marca" class="form-label fw-bold">
                                Marca <span class="text-danger">*</span>
                            </label>
                            <select name="id_marca" id="id_marca" 
                                    class="form-select @error('id_marca') is-invalid @enderror" 
                                    required>
                                @foreach ($marcas as $marca)
                                    <option value="{{ $marca->id_marca }}"
                                        {{ $marca->id_marca == old('id_marca', $producto->id_marca) ? 'selected' : '' }}>
                                        {{ $marca->vNombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_marca')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ASIGNAR ATRIBUTOS -->
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark d-flex align-items-center">
                <i class="fas fa-tags me-2"></i>
                <h5 class="mb-0">Asignar Atributos y Valores</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info d-flex align-items-center">
                    <i class="fas fa-info-circle me-2"></i>
                    <div>
                        Selecciona los atributos y valores que tendrá este producto. 
                        <br>Los atributos seleccionados se usarán para crear valoraciones específicas.
                    </div>
                </div>
                
                @if($atributos && $atributos->count() > 0)
                    @php
                        $atributosSeleccionados = [];
                        foreach ($producto->valoresAtributos as $valor) {
                            $atributosSeleccionados[$valor->id_atributo][] = $valor->id_atributo_valor;
                        }
                    @endphp
                    
                    <div class="row">
                        @foreach($atributos as $atributo)
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 border">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 fw-bold">{{ $atributo->vNombre }}</h6>
                                        <div class="form-check form-switch">
                                            @php
                                                $tieneValores = isset($atributosSeleccionados[$atributo->id_atributo]) && 
                                                               count($atributosSeleccionados[$atributo->id_atributo]) > 0;
                                            @endphp
                                            <input type="checkbox" 
                                                   class="form-check-input atributo-maestro-checkbox" 
                                                   data-atributo-id="{{ $atributo->id_atributo }}"
                                                   id="atributo_maestro_{{ $atributo->id_atributo }}"
                                                   {{ $tieneValores ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="atributo_maestro_{{ $atributo->id_atributo }}">
                                                Seleccionar
                                            </label>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        @if($atributo->tDescripcion)
                                            <p class="small text-muted mb-2">{{ $atributo->tDescripcion }}</p>
                                        @endif
                                        
                                        <div class="form-group valores-container" id="valores-atributo-{{ $atributo->id_atributo }}" 
                                             style="{{ $tieneValores ? 'display: block;' : 'display: none;' }}">
                                            <label class="small fw-bold mb-2">Seleccionar valores:</label>
                                            <div class="row">
                                                @if($atributo->valoresActivos && $atributo->valoresActivos->count() > 0)
                                                    @foreach($atributo->valoresActivos as $valor)
                                                        @php
                                                            $seleccionado = isset($atributosSeleccionados[$atributo->id_atributo]) && 
                                                                            in_array($valor->id_atributo_valor, $atributosSeleccionados[$atributo->id_atributo]);
                                                        @endphp
                                                        <div class="col-6 mb-2">
                                                            <div class="form-check">
                                                                <input type="checkbox" 
                                                                       class="form-check-input atributo-valor-checkbox" 
                                                                       name="atributos[{{ $atributo->id_atributo }}][]"
                                                                       value="{{ $valor->id_atributo_valor }}"
                                                                       id="atributo_{{ $atributo->id_atributo }}_valor_{{ $valor->id_atributo_valor }}"
                                                                       {{ $seleccionado ? 'checked' : '' }}>
                                                                <label class="form-check-label small" for="atributo_{{ $atributo->id_atributo }}_valor_{{ $valor->id_atributo_valor }}">
                                                                    {{ $valor->vValor }}
                                                                    @if($valor->dPrecio_extra > 0)
                                                                        <small class="text-success">(+${{ number_format($valor->dPrecio_extra, 2) }})</small>
                                                                    @endif
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="col-12">
                                                        <div class="alert alert-warning py-2 mb-0">
                                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                                            No hay valores disponibles para este atributo
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-tags fa-3x text-muted mb-2"></i>
                        <h5 class="text-muted">No hay atributos disponibles</h5>
                        <p class="text-muted mb-3">Crea atributos primero para poder asignarlos a los productos</p>
                        <a href="{{ route('atributos.create') }}" class="btn btn-warning">
                            <i class="fas fa-plus me-1"></i> Crear Atributo
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- IMÁGENES Y DESCRIPCIÓN -->
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white d-flex align-items-center">
                <i class="fas fa-images me-2"></i>
                <h5 class="mb-0">Imágenes y Descripción</h5>
            </div>
            <div class="card-body">
                <!-- Imágenes existentes -->
                <div class="form-group mb-4">
                    <label class="form-label fw-bold">Imágenes actuales ({{ count($producto->imagenes) }}/6)</label>
                    <p class="text-muted small">
                        <i class="fas fa-folder me-1"></i>
                        Carpeta: products/{{ $producto->id_producto }}/
                    </p>
                    @if(count($producto->imagenes) > 0)
                        <div class="row">
                            @foreach($producto->imagenes as $index => $imagen)
                                <div class="col-4 col-md-3 mb-2">
                                    <div class="card border">
                                        <img src="{{ $imagen }}" 
                                             class="card-img-top" 
                                             style="height: 100px; object-fit: cover;"
                                             alt="Imagen {{ $index + 1 }}">
                                        <div class="card-body p-2 text-center">
                                            <small class="text-muted">Imagen {{ $index + 1 }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No hay imágenes cargadas</p>
                    @endif
                </div>

                <!-- Agregar nuevas imágenes -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="imagenes" class="form-label fw-bold">
                                Agregar nuevas imágenes
                            </label>
                            <input type="file" name="imagenes[]" id="imagenes" 
                                   class="form-control @error('imagenes') is-invalid @enderror" 
                                   multiple accept="image/*">
                            @error('imagenes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @error('imagenes.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Formatos: JPG, JPEG, PNG, GIF, WEBP. Máximo 2MB por imagen.
                                <br>Espacio disponible para {{ 6 - count($producto->imagenes) }} imágenes más.
                            </div>
                            <div id="preview-container" class="mt-2 row"></div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="tDescripcion_corta" class="form-label fw-bold">
                                Descripción corta
                            </label>
                            <textarea name="tDescripcion_corta" id="tDescripcion_corta" 
                                      class="form-control @error('tDescripcion_corta') is-invalid @enderror" 
                                      maxlength="255" 
                                      rows="3">{{ old('tDescripcion_corta', $producto->tDescripcion_corta) }}</textarea>
                            @error('tDescripcion_corta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Máximo 255 caracteres</small>
                        </div>
                    </div>
                </div>
                
                <div class="form-group mb-3">
                    <label for="tDescripcion_larga" class="form-label fw-bold">
                        Descripción detallada
                    </label>
                    <textarea name="tDescripcion_larga" id="tDescripcion_larga" 
                              class="form-control @error('tDescripcion_larga') is-invalid @enderror" 
                              rows="5">{{ old('tDescripcion_larga', $producto->tDescripcion_larga) }}</textarea>
                    @error('tDescripcion_larga')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group mb-3">
                    <label class="form-label fw-bold">Etiquetas</label>
                    <div class="row">
                        @foreach ($etiquetas as $etiqueta)
                            <div class="col-md-3 col-6 mb-2">
                                <div class="form-check">
                                    <input type="checkbox" 
                                           name="etiquetas[]" 
                                           value="{{ $etiqueta->id_etiqueta }}" 
                                           class="form-check-input"
                                           {{ in_array($etiqueta->id_etiqueta, old('etiquetas', $producto->etiquetas->pluck('id_etiqueta')->toArray())) ? 'checked' : '' }}
                                           id="etiqueta_{{ $etiqueta->id_etiqueta }}">
                                    <label class="form-check-label" for="etiqueta_{{ $etiqueta->id_etiqueta }}">
                                        {{ $etiqueta->vNombre }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('etiquetas')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group mb-3">
                    <div class="form-check form-switch">
                        <input type="checkbox" name="bActivo" id="bActivo" 
                               class="form-check-input" value="1" 
                               {{ old('bActivo', $producto->bActivo) ? 'checked' : '' }}>
                        <label for="bActivo" class="form-check-label fw-bold">
                            Producto activo
                        </label>
                        <small class="form-text text-muted d-block">
                            Si está desactivado, el producto no se mostrará en la tienda
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- BOTONES DE ACCIÓN -->
        <div class="d-flex gap-2 mb-4">
            <button type="submit" class="btn btn-primary btn-lg px-4">
                <i class="fas fa-save me-2"></i> Actualizar Producto
            </button>
            <a href="{{ route('productos.index') }}" class="btn btn-secondary btn-lg px-4">
                <i class="fas fa-times me-2"></i> Cancelar
            </a>
            <a href="{{ route('productos.show', $producto) }}" class="btn btn-info btn-lg px-4">
                <i class="fas fa-eye me-2"></i> Ver Detalle
            </a>
        </div>
    </form>
</div>

<script>
// Solo números (para código de barras y stock)
function soloNumeros(input) {
    input.value = input.value.replace(/[^0-9]/g, '');
    input.classList.remove('is-invalid');
}

// Números y punto decimal (para precios)
function soloNumerosYDecimal(input) {
    input.value = input.value.replace(/[^0-9.]/g, '');
    
    let puntos = input.value.split('.').length - 1;
    if (puntos > 1) {
        input.value = input.value.slice(0, -1);
    }
    
    if (input.value.includes('.')) {
        let partes = input.value.split('.');
        if (partes[1].length > 2) {
            partes[1] = partes[1].substring(0, 2);
            input.value = partes[0] + '.' + partes[1];
        }
    }
    
    input.classList.remove('is-invalid');
}

// Preview de nuevas imágenes
document.getElementById('imagenes').addEventListener('change', function(e) {
    const previewContainer = document.getElementById('preview-container');
    previewContainer.innerHTML = '';
    
    const files = e.target.files;
    const maxFiles = 6 - {{ count($producto->imagenes) }};
    
    if (files.length > maxFiles) {
        alert('Solo puedes seleccionar máximo ' + maxFiles + ' imágenes más.');
        this.value = '';
        return;
    }
    
    for (let i = 0; i < files.length && i < maxFiles; i++) {
        const file = files[i];
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const col = document.createElement('div');
            col.className = 'col-4 col-md-3 mb-2';
            col.innerHTML = `
                <div class="card border">
                    <img src="${e.target.result}" 
                         class="card-img-top" 
                         style="height: 100px; object-fit: cover;"
                         alt="Previsualización">
                    <div class="card-body p-2 text-center">
                        <small class="text-muted">${file.name.length > 15 ? file.name.substring(0, 15) + '...' : file.name}</small>
                    </div>
                </div>
            `;
            previewContainer.appendChild(col);
        }
        
        reader.readAsDataURL(file);
    }
});

// Mostrar/ocultar valores cuando se selecciona un atributo maestro
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.atributo-maestro-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const valoresContainer = document.getElementById(`valores-atributo-${this.dataset.atributoId}`);
            if (this.checked) {
                valoresContainer.style.display = 'block';
                // Agregar animación
                valoresContainer.style.opacity = '0';
                valoresContainer.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    valoresContainer.style.opacity = '1';
                    valoresContainer.style.transform = 'translateY(0)';
                    valoresContainer.style.transition = 'all 0.3s ease';
                }, 10);
            } else {
                valoresContainer.style.display = 'none';
                // Desmarcar todos los valores de este atributo
                valoresContainer.querySelectorAll('.atributo-valor-checkbox').forEach(vc => {
                    vc.checked = false;
                });
            }
        });
    });
});

// Remover error cuando se escribe
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('input, select, textarea').forEach(input => {
        input.addEventListener('input', function() {
            this.classList.remove('is-invalid');
        });
        
        input.addEventListener('change', function() {
            this.classList.remove('is-invalid');
        });
    });
});
</script>

<style>
.card {
    transition: all 0.3s ease;
    border: 1px solid #dee2e6;
}

.card:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.card-header {
    border-bottom: 1px solid rgba(0,0,0,0.125);
}

.form-check-input:checked {
    background-color: #198754;
    border-color: #198754;
}

.form-switch .form-check-input:checked {
    background-color: #198754;
    border-color: #198754;
}

#preview-container img {
    transition: all 0.3s ease;
}

#preview-container img:hover {
    transform: scale(1.05);
}

.valores-container {
    transition: all 0.3s ease;
}

.input-group-text {
    background-color: #f8f9fa;
    border-right: 0;
}

.form-control:focus + .input-group-text {
    border-color: #86b7fe;
    background-color: #f8f9fa;
}
</style>
@endsection