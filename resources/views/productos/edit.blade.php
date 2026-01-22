@extends('layouts.app')

@section('title', 'Editar Producto - ' . $producto->vNombre)
@section('content')
<div class="container">
    <h1><i class="fas fa-edit me-2"></i>Editar Producto</h1>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('productos.update', $producto->id_producto) }}" method="POST" enctype="multipart/form-data" id="productoForm">
        @csrf
        @method('PUT')

        <!-- IMÁGENES ACTUALES CON GESTIÓN -->
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white d-flex align-items-center justify-content-between">
                <div>
                    <i class="fas fa-images me-2"></i>
                    <h5 class="mb-0">Imágenes del Producto ({{ count($producto->imagenes) }}/8)</h5>
                </div>
                <span class="badge bg-light text-dark">
                    Espacio disponible: {{ 8 - count($producto->imagenes) }} imágenes
                </span>
            </div>
            <div class="card-body">
                @php
                    $nombresArchivos = $producto->getNombresArchivosImagenes();
                @endphp
                
                @if(count($nombresArchivos) > 0)
                    <div class="alert alert-info d-flex align-items-center">
                        <i class="fas fa-info-circle me-2"></i>
                        <div>
                            <strong>Importante:</strong> Marca las imágenes que deseas eliminar. 
                            <span class="text-danger">Esta acción no se puede deshacer.</span>
                        </div>
                    </div>
                    
                    <div class="row" id="imagenes-actuales">
                        @foreach($nombresArchivos as $imagen)
                            <div class="col-6 col-md-3 mb-3">
                                <div class="card h-100 border">
                                    <div class="card-img-container position-relative">
                                        <img src="{{ $imagen['url'] }}" 
                                             class="card-img-top" 
                                             style="height: 180px; object-fit: contain; background: #f8f9fa;"
                                             alt="Imagen del producto">
                                        
                                        <!-- Checkbox para eliminar -->
                                        <div class="position-absolute top-0 end-0 m-2">
                                            <input type="checkbox" 
                                                   name="imagenes_a_eliminar[]"
                                                   value="{{ $imagen['nombre'] }}"
                                                   id="eliminar_{{ $imagen['nombre'] }}"
                                                   class="form-check-input eliminar-imagen-checkbox"
                                                   style="transform: scale(1.3);">
                                        </div>
                                    </div>
                                    <div class="card-body p-2 text-center">
                                        <small class="text-muted d-block" style="font-size: 12px;">
                                            {{ $imagen['nombre'] }}
                                        </small>
                                        <label for="eliminar_{{ $imagen['nombre'] }}" 
                                               class="small text-danger mt-1 cursor-pointer">
                                            <i class="fas fa-trash-alt me-1"></i>Eliminar
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-images fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No hay imágenes cargadas para este producto</p>
                    </div>
                @endif

                <!-- Agregar nuevas imágenes -->
                <div class="mt-4">
                    <h6 class="fw-bold mb-3">Agregar nuevas imágenes</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="imagenes" class="form-label fw-bold">
                                    Seleccionar imágenes
                                </label>
                                <input type="file" 
                                       name="imagenes[]" 
                                       id="imagenes" 
                                       class="form-control @error('imagenes') is-invalid @enderror" 
                                       multiple 
                                       accept="image/*"
                                       onchange="previewNewImages(event)">
                                @error('imagenes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @error('imagenes.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Formatos permitidos: JPG, JPEG, PNG, GIF, WEBP, JFIF, SVG. 
                                    Máximo 5MB por imagen.
                                    <br>Puedes seleccionar hasta {{ 8 - count($producto->imagenes) }} imágenes más.
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <div id="preview-new-container" class="row mt-2"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
                                SKU <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="vCodigo_barras" id="vCodigo_barras" 
                                   class="form-control @error('vCodigo_barras') is-invalid @enderror"
                                   value="{{ old('vCodigo_barras', $producto->vCodigo_barras) }}" 
                                   maxlength="15" 
                                   required 
                                   oninput="validarSKU(this)"
                                   pattern="[A-Za-z0-9]+"
                                   title="Solo letras y números (máximo 15 caracteres)"
                                   inputmode="text">
                            @error('vCodigo_barras')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">15 caracteres máximo, solo letras y números</small>
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
                                       value="{{ old('dPrecio_compra', $producto->dPrecio_compra ? number_format($producto->dPrecio_compra, 2, '.', '') : '') }}" 
                                       oninput="validarPrecio(this)"
                                       placeholder="0.00"
                                       title="Máximo: 9,999,999.99 (7 dígitos enteros, 2 decimales)">
                                @error('dPrecio_compra')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">
                                Máximo: 9,999,999.99 - Mínimo: 0.00
                            </small>
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
                                       value="{{ old('dPrecio_venta', number_format($producto->dPrecio_venta, 2, '.', '')) }}" 
                                       required 
                                       oninput="validarPrecio(this)"
                                       placeholder="0.00"
                                       title="Máximo: 9,999,999.99 (7 dígitos enteros, 2 decimales)">
                                @error('dPrecio_venta')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">
                                Máximo: 9,999,999.99 - Mínimo: 0.00
                            </small>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="iStock" class="form-label fw-bold">
                                Stock <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="iStock" id="iStock" 
                                   class="form-control @error('iStock') is-invalid @enderror"
                                   value="{{ old('iStock', $producto->iStock) }}" 
                                   required min="0" max="9999" step="1"
                                   oninput="validarStock(this)"
                                   pattern="[0-9]*"
                                   inputmode="numeric">
                            @error('iStock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Solo números enteros. Rango: 0 - 9,999</small>
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
                                @php
                                    function mostrarCategoriasJerarquicamenteEdit($categorias, $nivel = 0, $oldValue = null, $productoCategoria = null)
                                    {
                                        foreach($categorias as $categoria) {
                                            $prefijo = str_repeat('&nbsp;&nbsp;&nbsp;', $nivel);
                                            $icono = '';
                                            
                                            if ($nivel == 0) {
                                                $icono = '🏠 ';
                                            } elseif ($nivel == 1) {
                                                $icono = '↳ ';
                                            } elseif ($nivel >= 2) {
                                                $icono = str_repeat('↳&nbsp;', $nivel);
                                            }
                                            
                                            // Determinar si está seleccionado (prioridad: old value, luego producto actual)
                                            $selected = false;
                                            if ($oldValue !== null) {
                                                $selected = ($oldValue == $categoria->id_categoria);
                                            } elseif ($productoCategoria !== null) {
                                                $selected = ($productoCategoria == $categoria->id_categoria);
                                            }
                                            
                                            echo '<option value="' . $categoria->id_categoria . '" ' . 
                                                 ($selected ? 'selected' : '') . '>' .
                                                 $prefijo . $icono . htmlspecialchars($categoria->vNombre) . 
                                                 '</option>';
                                            
                                            if ($categoria->hijos && $categoria->hijos->count() > 0) {
                                                mostrarCategoriasJerarquicamenteEdit($categoria->hijos, $nivel + 1, $oldValue, $productoCategoria);
                                            }
                                        }
                                    }
                                    
                                    // Pasar el valor old si existe, o el valor actual del producto
                                    $oldCategoria = old('id_categoria');
                                    $productoCategoria = $producto->id_categoria;
                                    $categoriasRaiz = $categorias->where('id_categoria_padre', null)->where('bActivo', true);
                                @endphp
                                
                                @php
                                    mostrarCategoriasJerarquicamenteEdit($categoriasRaiz, 0, $oldCategoria, $productoCategoria);
                                @endphp
                            </select>
                            @error('id_categoria')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Selecciona la categoría principal o subcategoría para este producto
                            </small>
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

        <!-- ATRIBUTOS -->
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

        <!-- DESCRIPCIÓN Y ETIQUETAS -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white d-flex align-items-center">
                <i class="fas fa-file-alt me-2"></i>
                <h5 class="mb-0">Descripción y Etiquetas</h5>
            </div>
            <div class="card-body">
                <div class="row">
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
                    
                    <div class="col-md-6">
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
                    </div>
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
// Objeto para guardar valores originales
const valoresOriginalesEdit = {};

// Guardar valor original cuando el campo recibe focus
function guardarValorOriginal(input) {
    valoresOriginalesEdit[input.id] = input.value;
}

// Validar precio - SIN COMAS, solo números y punto decimal, limitado a 7 dígitos enteros
function validarPrecio(input) {
    let value = input.value;
    const cursorPos = input.selectionStart;
    
    if (value === '') {
        input.classList.remove('is-invalid');
        return;
    }
    
    // Eliminar todo excepto números y un punto decimal
    value = value.replace(/[^0-9.]/g, '');
    
    // Verificar que no haya más de un punto decimal
    const puntos = value.split('.').length - 1;
    if (puntos > 1) {
        // Mantener solo el primer punto decimal
        const partes = value.split('.');
        value = partes[0] + '.' + partes.slice(1).join('');
    }
    
    // Eliminar múltiples puntos seguidos
    value = value.replace(/\.{2,}/g, '.');
    
    // Si comienza con punto, agregar 0 al inicio
    if (value.startsWith('.')) {
        value = '0' + value;
    }
    
    // Limitar a máximo 7 dígitos enteros (9,999,999.99)
    const partesNumero = value.split('.');
    const parteEntera = partesNumero[0];
    
    if (parteEntera.length > 7) {
        // Limitar a 7 dígitos enteros
        value = parteEntera.substring(0, 7) + (partesNumero[1] ? '.' + partesNumero[1] : '');
    }
    
    // Limitar decimales a máximo 2
    if (value.includes('.')) {
        const partes = value.split('.');
        if (partes[1].length > 2) {
            partes[1] = partes[1].substring(0, 2);
            value = partes[0] + '.' + partes[1];
        }
    }
    
    // Solo actualizar si el valor cambió
    if (input.value !== value) {
        const oldValue = input.value;
        input.value = value;
        
        const cursorDiff = value.length - oldValue.length;
        const newCursorPos = Math.max(0, Math.min(value.length, cursorPos + cursorDiff));
        setTimeout(() => {
            input.setSelectionRange(newCursorPos, newCursorPos);
        }, 0);
    }
    
    input.classList.remove('is-invalid');
    
    // Mostrar error si el número es muy grande
    if (value) {
        const numero = parseFloat(value);
        if (!isNaN(numero) && numero > 9999999.99) {
            input.classList.add('is-invalid');
            mostrarErrorPrecio(input, 'El precio máximo es 9,999,999.99');
        }
    }
}

// Función para mostrar error de precio
function mostrarErrorPrecio(input, mensaje) {
    // Remover error anterior si existe
    const errorId = `error-${input.id}`;
    const errorElement = document.getElementById(errorId);
    if (errorElement) {
        errorElement.remove();
    }
    
    // Crear elemento de error
    const errorDiv = document.createElement('div');
    errorDiv.className = 'invalid-feedback d-block precio-error';
    errorDiv.textContent = mensaje;
    errorDiv.id = errorId;
    
    // Insertar después del input
    input.parentNode.appendChild(errorDiv);
}

// Validar stock (solo números enteros con límite)
function validarStock(input) {
    // Remover cualquier caracter que no sea número
    input.value = input.value.replace(/[^0-9]/g, '');
    
    // Validar que sea mayor o igual a 0 y menor o igual a 9999
    if (input.value && parseInt(input.value) < 0) {
        input.value = '0';
    } else if (input.value && parseInt(input.value) > 9999) {
        input.value = '9999';
    }
    
    // Remover ceros a la izquierda
    if (input.value.length > 1 && input.value.startsWith('0')) {
        input.value = input.value.replace(/^0+/, '');
    }
    
    // Si está vacío, poner 0
    if (input.value === '') {
        input.value = '0';
    }
    
    input.classList.remove('is-invalid');
}

// Función para validar SKU (15 caracteres máximo, solo letras y números)
function validarSKU(input) {
    // Permitir solo letras y números
    input.value = input.value.replace(/[^A-Za-z0-9]/g, '');
    
    // Limitar a 15 caracteres
    if (input.value.length > 15) {
        input.value = input.value.substring(0, 15);
    }
    
    // Convertir a mayúsculas automáticamente
    input.value = input.value.toUpperCase();
    
    input.classList.remove('is-invalid');
}

// Preview de nuevas imágenes
function previewNewImages(event) {
    const previewContainer = document.getElementById('preview-new-container');
    previewContainer.innerHTML = '';
    
    const files = event.target.files;
    const maxFiles = 8 - {{ count($producto->imagenes) }};
    const imagenesAEliminar = document.querySelectorAll('.eliminar-imagen-checkbox:checked').length;
    const espacioReal = maxFiles + imagenesAEliminar;
    
    if (files.length > espacioReal) {
        alert('Solo puedes seleccionar máximo ' + espacioReal + ' imágenes más.');
        event.target.value = '';
        return;
    }
    
    for (let i = 0; i < files.length && i < espacioReal; i++) {
        const file = files[i];
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const col = document.createElement('div');
            col.className = 'col-6 col-md-3 mb-2';
            col.innerHTML = `
                <div class="card border">
                    <img src="${e.target.result}" 
                         class="card-img-top" 
                         style="height: 120px; object-fit: cover;"
                         alt="Previsualización">
                    <div class="card-body p-2 text-center">
                        <small class="text-muted d-block" style="font-size: 11px;">
                            ${file.name.length > 15 ? file.name.substring(0, 15) + '...' : file.name}
                        </small>
                        <small class="text-muted d-block">
                            ${(file.size / 1024).toFixed(2)} KB
                        </small>
                    </div>
                </div>
            `;
            previewContainer.appendChild(col);
        }
        
        reader.readAsDataURL(file);
    }
}

// Mostrar confirmación al marcar imágenes para eliminar
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.eliminar-imagen-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const card = this.closest('.card');
            if (this.checked) {
                card.style.borderColor = '#dc3545';
                card.style.boxShadow = '0 0 0 3px rgba(220, 53, 69, 0.3)';
            } else {
                card.style.borderColor = '';
                card.style.boxShadow = '';
            }
            
            updateImageCount();
        });
    });
    
    // Actualizar contador de imágenes disponibles
    function updateImageCount() {
        const imagenesAEliminar = document.querySelectorAll('.eliminar-imagen-checkbox:checked').length;
        const imagenesActuales = {{ count($producto->imagenes) }};
        const espacioDisponible = 8 - (imagenesActuales - imagenesAEliminar);
        
        const fileInput = document.getElementById('imagenes');
        if (fileInput) {
            fileInput.setAttribute('data-max-files', espacioDisponible);
        }
    }
    
    updateImageCount();
});

// Validación de formulario SIMPLIFICADA - solo validaciones críticas
document.getElementById('productoForm').addEventListener('submit', function(e) {
    let erroresCriticos = false;
    
    // 1. Validar campos obligatorios
    const camposObligatorios = [
        {id: 'vCodigo_barras', nombre: 'SKU'},
        {id: 'vNombre', nombre: 'Nombre'},
        {id: 'dPrecio_venta', nombre: 'Precio de venta'},
        {id: 'iStock', nombre: 'Stock'},
        {id: 'id_categoria', nombre: 'Categoría'},
        {id: 'id_marca', nombre: 'Marca'}
    ];
    
    camposObligatorios.forEach(campo => {
        const elemento = document.getElementById(campo.id);
        if (!elemento.value.trim()) {
            elemento.classList.add('is-invalid');
            erroresCriticos = true;
            
            if (!elemento.nextElementSibling || !elemento.nextElementSibling.classList.contains('invalid-feedback')) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback';
                errorDiv.textContent = `El campo "${campo.nombre}" es obligatorio`;
                elemento.parentNode.appendChild(errorDiv);
            }
        }
    });
    
    // 2. Validar stock
    const stockInput = document.getElementById('iStock');
    const stockValue = stockInput.value.trim();
    if (stockValue) {
        const stockNum = parseInt(stockValue);
        if (isNaN(stockNum) || stockNum < 0 || stockNum > 9999) {
            stockInput.classList.add('is-invalid');
            erroresCriticos = true;
            
            if (!stockInput.nextElementSibling || !stockInput.nextElementSibling.classList.contains('invalid-feedback')) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback';
                errorDiv.textContent = 'El stock debe ser un número entre 0 y 9999';
                stockInput.parentNode.appendChild(errorDiv);
            }
        }
    }
    
    // 3. Validar precios
    const precioVenta = document.getElementById('dPrecio_venta');
    const precioCompra = document.getElementById('dPrecio_compra');
    const regexPrecio = /^[0-9]*\.?[0-9]*$/;
    
    // Validar precio de venta
    if (precioVenta.value.trim()) {
        if (!regexPrecio.test(precioVenta.value.trim())) {
            precioVenta.classList.add('is-invalid');
            erroresCriticos = true;
            
            if (!precioVenta.nextElementSibling || !precioVenta.nextElementSibling.classList.contains('invalid-feedback')) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback';
                errorDiv.textContent = 'Solo números y punto decimal permitidos';
                precioVenta.parentNode.appendChild(errorDiv);
            }
        } else {
            const numero = parseFloat(precioVenta.value.trim());
            if (!isNaN(numero) && numero > 9999999.99) {
                precioVenta.classList.add('is-invalid');
                erroresCriticos = true;
                
                if (!precioVenta.nextElementSibling || !precioVenta.nextElementSibling.classList.contains('invalid-feedback')) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback';
                    errorDiv.textContent = 'El precio máximo es 9,999,999.99';
                    precioVenta.parentNode.appendChild(errorDiv);
                }
            }
        }
    }
    
    // Validar precio de compra
    if (precioCompra.value.trim()) {
        if (!regexPrecio.test(precioCompra.value.trim())) {
            precioCompra.classList.add('is-invalid');
            erroresCriticos = true;
            
            if (!precioCompra.nextElementSibling || !precioCompra.nextElementSibling.classList.contains('invalid-feedback')) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback';
                errorDiv.textContent = 'Solo números y punto decimal permitidos';
                precioCompra.parentNode.appendChild(errorDiv);
            }
        } else {
            const numero = parseFloat(precioCompra.value.trim());
            if (!isNaN(numero) && numero > 9999999.99) {
                precioCompra.classList.add('is-invalid');
                erroresCriticos = true;
                
                if (!precioCompra.nextElementSibling || !precioCompra.nextElementSibling.classList.contains('invalid-feedback')) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback';
                    errorDiv.textContent = 'El precio máximo es 9,999,999.99';
                    precioCompra.parentNode.appendChild(errorDiv);
                }
            }
        }
    }
    
    // 4. Validar imágenes
    const imagenesAEliminar = document.querySelectorAll('.eliminar-imagen-checkbox:checked').length;
    const nuevasImagenes = document.getElementById('imagenes').files.length;
    const imagenesActuales = {{ count($producto->imagenes) }};
    const totalImagenes = imagenesActuales - imagenesAEliminar + nuevasImagenes;
    
    if (totalImagenes > 8) {
        e.preventDefault();
        alert('Error: Excediste el límite de 8 imágenes. Total calculado: ' + totalImagenes + ' imágenes.');
        return false;
    }
    
    // 5. Si hay errores críticos, prevenir envío
    if (erroresCriticos) {
        e.preventDefault();
        
        const primerError = document.querySelector('.is-invalid');
        if (primerError) {
            primerError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            primerError.focus();
        }
        
        return false;
    }
    
    // 6. Confirmar eliminación de imágenes
    if (imagenesAEliminar > 0) {
        if (!confirm(`¿Estás seguro de que deseas eliminar ${imagenesAEliminar} imagen(es)? Esta acción no se puede deshacer.`)) {
            e.preventDefault();
            return false;
        }
    }
    
    return true;
});

// Estilo para cursor pointer en etiquetas
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('label.cursor-pointer').forEach(label => {
        label.style.cursor = 'pointer';
    });
    
    // Limpiar errores al escribir
    document.querySelectorAll('input, select, textarea').forEach(elemento => {
        elemento.addEventListener('input', function() {
            this.classList.remove('is-invalid');
            
            const errorFeedback = this.nextElementSibling;
            if (errorFeedback && errorFeedback.classList.contains('invalid-feedback')) {
                errorFeedback.remove();
            }
        });
    });
});
</script>

<style>
.card {
    transition: all 0.3s ease;
    border: 1px solid #dee2e6;
}

.card-header {
    border-bottom: 1px solid rgba(0,0,0,0.125);
}

.card-img-container {
    position: relative;
    overflow: hidden;
}

.card-img-container:hover img {
    transform: scale(1.05);
}

.card-img-container img {
    transition: transform 0.3s ease;
}

.form-check-input:checked {
    background-color: #dc3545;
    border-color: #dc3545;
}

/* Estilos para etiquetas con cursor */
.cursor-pointer {
    cursor: pointer;
}

/* Resaltar cuando se marca para eliminar */
.eliminar-imagen-checkbox:checked + .card-img-container {
    opacity: 0.7;
}

/* Responsive */
@media (max-width: 768px) {
    .card-img-top {
        height: 150px !important;
    }
}

/* Estilos para errores de precio */
.precio-error {
    margin-top: 5px;
    font-size: 0.875em;
    color: #dc3545;
}

.input-group .is-invalid {
    z-index: 3;
}

.input-group .is-invalid ~ .invalid-feedback {
    display: block;
}

/* Mejorar la experiencia del input de precio */
input[name="dPrecio_venta"],
input[name="dPrecio_compra"] {
    font-family: 'Courier New', monospace;
    font-size: 1.1em;
    letter-spacing: 0.5px;
    text-align: right;
}
</style>
@endsection