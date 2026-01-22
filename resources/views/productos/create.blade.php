@extends('layouts.app')

@section('title', 'Registrar Nuevo Producto')
@section('content')
<div class="container">
    <h1><i class="fas fa-plus-circle me-2"></i>Registrar Producto</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data" id="productoForm">
        @csrf

        <!-- INFORMACIÓN BÁSICA DEL PRODUCTO -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información Básica</h5>
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
                                   value="{{ old('vCodigo_barras') }}" 
                                   maxlength="15" 
                                   required
                                   oninput="validarSKU(this)"
                                   pattern="[A-Za-z0-9]+"
                                   title="Solo letras y números (máximo 15 caracteres)">
                            @error('vCodigo_barras')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Ej: AGAVE001, MEZCAL2024 (15 caracteres máximo, solo letras y números)</small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="vNombre" class="form-label fw-bold">
                                Nombre del producto <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="vNombre" id="vNombre" 
                                   class="form-control @error('vNombre') is-invalid @enderror" 
                                   value="{{ old('vNombre') }}" 
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
                                <input type="text" 
                                       name="dPrecio_compra" 
                                       id="dPrecio_compra" 
                                       class="form-control @error('dPrecio_compra') is-invalid @enderror"
                                       value="{{ old('dPrecio_compra') }}" 
                                       oninput="validarPrecio(this)"
                                       placeholder="0.00"
                                       title="Máximo: 9,999,999.99 (7 dígitos enteros, 2 decimales)">
                                @error('dPrecio_compra')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">
                                Máximo: 9,999,999.99 (7 dígitos enteros)
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
                                <input type="text" 
                                       name="dPrecio_venta" 
                                       id="dPrecio_venta" 
                                       class="form-control @error('dPrecio_venta') is-invalid @enderror"
                                       value="{{ old('dPrecio_venta') }}" 
                                       required 
                                       oninput="validarPrecio(this)"
                                       placeholder="0.00"
                                       title="Máximo: 9,999,999.99 (7 dígitos enteros, 2 decimales)">
                                @error('dPrecio_venta')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">
                                Máximo: 9,999,999.99 (7 dígitos enteros)
                            </small>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="iStock" class="form-label fw-bold">
                                Stock inicial <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="iStock" id="iStock" 
                                   class="form-control @error('iStock') is-invalid @enderror"
                                   value="{{ old('iStock', 0) }}" 
                                   required 
                                   oninput="validarStock(this)"
                                   pattern="[0-9]{1,4}"
                                   title="Máximo 4 dígitos (0-9999)"
                                   inputmode="numeric"
                                   min="0"
                                   max="9999">
                            @error('iStock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Máximo 4 dígitos (0-9999)</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CATEGORÍA Y MARCA -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-tags me-2"></i>Categorización</h5>
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
                                    function mostrarCategoriasJerarquicamente($categorias, $nivel = 0, $oldValue = null)
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
                                            
                                            $selected = ($oldValue == $categoria->id_categoria) ? 'selected' : '';
                                            
                                            echo '<option value="' . $categoria->id_categoria . '" ' . $selected . '>' .
                                                 $prefijo . $icono . htmlspecialchars($categoria->vNombre) . 
                                                 '</option>';
                                            
                                            if ($categoria->hijos && $categoria->hijos->count() > 0) {
                                                mostrarCategoriasJerarquicamente($categoria->hijos, $nivel + 1, $oldValue);
                                            }
                                        }
                                    }
                                    
                                    // Pasar el valor old si existe
                                    $oldCategoria = old('id_categoria');
                                    $categoriasRaiz = $categorias->where('id_categoria_padre', null)->where('bActivo', true);
                                @endphp
                                
                                @php
                                    mostrarCategoriasJerarquicamente($categoriasRaiz, 0, $oldCategoria);
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
                                <option value="">Seleccionar marca</option>
                                @foreach ($marcas as $marca)
                                    <option value="{{ $marca->id_marca }}" 
                                        {{ old('id_marca') == $marca->id_marca ? 'selected' : '' }}>
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

        <!-- ATRIBUTOS (OPCIONAL) -->
        @if($atributos && $atributos->count() > 0)
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="fas fa-tags me-2"></i>Atributos (Opcional)</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Los atributos son opcionales. Puedes asignarlos después de crear el producto.
                </div>
                
                <div class="row">
                    @foreach($atributos as $atributo)
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 border">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 fw-bold">{{ $atributo->vNombre }}</h6>
                                    @if($atributo->tDescripcion)
                                        <p class="small text-muted mb-0 mt-1">{{ $atributo->tDescripcion }}</p>
                                    @endif
                                </div>
                                <div class="card-body">
                                    @if($atributo->valoresActivos && $atributo->valoresActivos->count() > 0)
                                        @foreach($atributo->valoresActivos as $valor)
                                            <div class="form-check mb-2">
                                                <input type="checkbox" 
                                                       class="form-check-input" 
                                                       name="atributos[{{ $atributo->id_atributo }}][]"
                                                       value="{{ $valor->id_atributo_valor }}"
                                                       id="atributo_{{ $atributo->id_atributo }}_valor_{{ $valor->id_atributo_valor }}">
                                                <label class="form-check-label" for="atributo_{{ $atributo->id_atributo }}_valor_{{ $valor->id_atributo_valor }}">
                                                    {{ $valor->vValor }}
                                                    @if($valor->dPrecio_extra > 0)
                                                        <small class="text-success">(+${{ number_format($valor->dPrecio_extra, 2) }})</small>
                                                    @endif
                                                </label>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="alert alert-warning py-2 mb-0">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            No hay valores disponibles
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- IMÁGENES Y DESCRIPCIÓN -->
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="fas fa-images me-2"></i>Imágenes y Descripción</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="imagenes" class="form-label fw-bold">
                                Imágenes del producto (Máximo 8)
                            </label>
                            <input type="file" name="imagenes[]" id="imagenes" 
                                   class="form-control @error('imagenes') is-invalid @enderror" 
                                   multiple accept="image/*"
                                   onchange="handleImageSelection(event)">
                            @error('imagenes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Formatos: JPG, JPEG, PNG, GIF, WEBP, JFIF, SVG. Máximo 5MB por imagen.
                                Puedes seleccionar hasta 8 imágenes.
                            </small>
                            
                            <!-- Contenedor para mostrar las imágenes seleccionadas -->
                            <div class="mt-3">
                                <h6 class="fw-bold mb-2">Imágenes seleccionadas:</h6>
                                <div id="selected-images-container" class="row mb-3">
                                    <!-- Las imágenes seleccionadas aparecerán aquí -->
                                </div>
                                
                                <div class="alert alert-info py-2">
                                    <i class="fas fa-info-circle me-1"></i>
                                    <small>Las imágenes seleccionadas se mantendrán aunque abras el selector nuevamente. 
                                    Para eliminar una imagen, haz clic en la "X" en la esquina superior derecha.</small>
                                </div>
                            </div>
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
                                      rows="3">{{ old('tDescripcion_corta') }}</textarea>
                            @error('tDescripcion_corta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="form-group mb-3">
                    <label for="tDescripcion_larga" class="form-label fw-bold">
                        Descripción detallada
                    </label>
                    <textarea name="tDescripcion_larga" id="tDescripcion_larga" 
                              class="form-control @error('tDescripcion_larga') is-invalid @enderror" 
                              rows="5">{{ old('tDescripcion_larga') }}</textarea>
                    @error('tDescripcion_larga')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group mb-3">
                    <label class="form-label fw-bold">Etiquetas (Opcional)</label>
                    <div class="row">
                        @foreach ($etiquetas as $etiqueta)
                            <div class="col-md-3 col-6 mb-2">
                                <div class="form-check">
                                    <input type="checkbox" 
                                           name="etiquetas[]" 
                                           value="{{ $etiqueta->id_etiqueta }}" 
                                           class="form-check-input"
                                           {{ is_array(old('etiquetas')) && in_array($etiqueta->id_etiqueta, old('etiquetas')) ? 'checked' : '' }}
                                           id="etiqueta_{{ $etiqueta->id_etiqueta }}">
                                    <label class="form-check-label" for="etiqueta_{{ $etiqueta->id_etiqueta }}">
                                        {{ $etiqueta->vNombre }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="form-group mb-3">
                    <div class="form-check form-switch">
                        <input type="checkbox" name="bActivo" id="bActivo" 
                               class="form-check-input" value="1" 
                               {{ old('bActivo', true) ? 'checked' : '' }}>
                        <label for="bActivo" class="form-check-label fw-bold">
                            Producto activo
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- BOTONES DE ACCIÓN -->
        <div class="d-flex gap-2 mb-4">
            <button type="submit" class="btn btn-success btn-lg px-4">
                <i class="fas fa-save me-2"></i> Guardar Producto
            </button>
            <a href="{{ route('productos.index') }}" class="btn btn-secondary btn-lg px-4">
                <i class="fas fa-times me-2"></i> Cancelar
            </a>
        </div>
    </form>
</div>

<script>
// Objeto para guardar valores originales
const valoresOriginales = {};

// Guardar valor original cuando el campo recibe focus
function guardarValorOriginal(input) {
    valoresOriginales[input.id] = input.value;
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

// Validar stock (máximo 4 dígitos)
function validarStock(input) {
    // Remover cualquier caracter que no sea número
    input.value = input.value.replace(/[^0-9]/g, '');
    
    // Limitar a máximo 4 dígitos
    if (input.value.length > 4) {
        input.value = input.value.substring(0, 4);
    }
    
    // Validar que sea mayor o igual a 0
    if (input.value && parseInt(input.value) < 0) {
        input.value = '0';
    }
    
    // Remover ceros a la izquierda (excepto si es solo "0")
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

// Variable global para almacenar las imágenes seleccionadas
let selectedImages = [];
let imageCounter = 0;

// Manejar la selección de imágenes
function handleImageSelection(event) {
    const files = event.target.files;
    const maxFiles = 8;
    
    // Verificar si excede el límite
    if (selectedImages.length + files.length > maxFiles) {
        alert(`Solo puedes seleccionar máximo ${maxFiles} imágenes. Ya tienes ${selectedImages.length} seleccionadas.`);
        event.target.value = '';
        return;
    }
    
    // Agregar nuevas imágenes al array
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        
        // Verificar que no sea una imagen duplicada
        if (!isImageDuplicate(file)) {
            const imageId = 'img_' + Date.now() + '_' + imageCounter++;
            selectedImages.push({
                id: imageId,
                file: file,
                preview: URL.createObjectURL(file)
            });
        }
    }
    
    // Actualizar la visualización
    renderSelectedImages();
    
    // Limpiar el input para permitir nuevas selecciones
    event.target.value = '';
}

// Verificar si la imagen ya fue seleccionada
function isImageDuplicate(newFile) {
    return selectedImages.some(img => 
        img.file.name === newFile.name && 
        img.file.size === newFile.size && 
        img.file.lastModified === newFile.lastModified
    );
}

// Eliminar una imagen seleccionada
function removeSelectedImage(imageId) {
    selectedImages = selectedImages.filter(img => img.id !== imageId);
    renderSelectedImages();
    updateFileInput();
}

// Renderizar las imágenes seleccionadas
function renderSelectedImages() {
    const container = document.getElementById('selected-images-container');
    container.innerHTML = '';
    
    if (selectedImages.length === 0) {
        container.innerHTML = `
            <div class="col-12 text-center py-3">
                <i class="fas fa-images fa-2x text-muted mb-2"></i>
                <p class="text-muted small mb-0">No hay imágenes seleccionadas</p>
            </div>
        `;
        return;
    }
    
    // Mostrar contador
    const counterInfo = document.createElement('div');
    counterInfo.className = 'col-12 mb-2';
    counterInfo.innerHTML = `
        <div class="alert alert-secondary py-2 mb-0">
            <i class="fas fa-camera me-1"></i>
            <strong>${selectedImages.length}</strong> de 8 imágenes seleccionadas
        </div>
    `;
    container.appendChild(counterInfo);
    
    // Mostrar cada imagen
    selectedImages.forEach((image, index) => {
        const col = document.createElement('div');
        col.className = 'col-6 col-md-3 mb-3';
        col.innerHTML = `
            <div class="card border position-relative">
                <!-- Botón para eliminar -->
                <button type="button" 
                        class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1"
                        onclick="removeSelectedImage('${image.id}')"
                        style="width: 28px; height: 28px; padding: 0; border-radius: 50%; z-index: 10;">
                    <i class="fas fa-times"></i>
                </button>
                
                <!-- Imagen -->
                <img src="${image.preview}" 
                     class="card-img-top" 
                     style="height: 120px; object-fit: contain; background: #f8f9fa; padding: 8px;"
                     alt="Imagen ${index + 1}">
                
                <div class="card-body p-2 text-center">
                    <small class="text-muted d-block" style="font-size: 11px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                        ${image.file.name.length > 15 ? image.file.name.substring(0, 15) + '...' : image.file.name}
                    </small>
                    <small class="text-muted d-block">
                        ${(image.file.size / 1024).toFixed(2)} KB
                    </small>
                </div>
            </div>
        `;
        container.appendChild(col);
    });
}

// Actualizar el input file con las imágenes seleccionadas
function updateFileInput() {
    const dataTransfer = new DataTransfer();
    
    // Agregar todos los archivos al DataTransfer
    selectedImages.forEach(image => {
        dataTransfer.items.add(image.file);
    });
    
    // Asignar los archivos al input
    const fileInput = document.getElementById('imagenes');
    fileInput.files = dataTransfer.files;
}

// Validación de precio en el submit - SIN COMAS
function validarPrecioEnSubmit(input, esRequerido = false) {
    const valorActual = input.value.trim();
    
    if (esRequerido && valorActual === '') {
        return { 
            esValido: false, 
            mensaje: 'Este campo es obligatorio' 
        };
    }
    
    if (valorActual === '') {
        return { esValido: true };
    }
    
    // Verificar que solo tenga números y punto decimal
    const regexValido = /^[0-9]*\.?[0-9]*$/;
    if (!regexValido.test(valorActual)) {
        return { 
            esValido: false, 
            mensaje: 'Solo números y punto decimal permitidos' 
        };
    }
    
    const numero = parseFloat(valorActual);
    
    // Verificar si es un número válido
    if (isNaN(numero)) {
        return { 
            esValido: false, 
            mensaje: 'Ingresa un precio válido' 
        };
    }
    
    // Validar que no sea negativo
    if (numero < 0) {
        return { 
            esValido: false, 
            mensaje: 'El precio no puede ser negativo' 
        };
    }
    
    // Validar límite máximo - 7 dígitos enteros (9,999,999.99)
    if (numero > 9999999.99) {
        return { 
            esValido: false, 
            mensaje: 'El precio máximo es 9,999,999.99' 
        };
    }
    
    // Validar formato - no permitir múltiples puntos decimales
    const puntosDecimales = valorActual.split('.').length - 1;
    if (puntosDecimales > 1) {
        return { 
            esValido: false, 
            mensaje: 'Formato de precio inválido (solo un punto decimal permitido)' 
        };
    }
    
    return { esValido: true };
}

// =====================================================
// VALIDACIÓN SIMPLIFICADA - SOLO PARA MEJORAR UX
// =====================================================
document.getElementById('productoForm').addEventListener('submit', function(e) {
    // Solo hacer validaciones básicas en tiempo real
    // Las validaciones reales las hará Laravel en el servidor
    
    // 1. Verificar que los campos obligatorios tengan algún valor
    let erroresCriticos = false;
    
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
            
            // Crear mensaje de error si no existe
            if (!elemento.nextElementSibling || !elemento.nextElementSibling.classList.contains('invalid-feedback')) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback';
                errorDiv.textContent = `El campo "${campo.nombre}" es obligatorio`;
                elemento.parentNode.appendChild(errorDiv);
            }
        }
    });
    
    // 2. Validar que el stock sea un número válido
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
    
    // 3. Validar formato de precios (solo números y punto decimal)
    const precioVenta = document.getElementById('dPrecio_venta');
    const precioCompra = document.getElementById('dPrecio_compra');
    
    // Validar precio de venta
    if (precioVenta.value.trim()) {
        const regexPrecio = /^[0-9]*\.?[0-9]*$/;
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
            // Validar que no sea demasiado grande
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
    
    // Validar precio de compra si tiene valor
    if (precioCompra.value.trim()) {
        const regexPrecio = /^[0-9]*\.?[0-9]*$/;
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
            // Validar que no sea demasiado grande
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
    
    // 4. Verificar imágenes (máximo 8)
    if (selectedImages.length > 8) {
        alert('Solo puedes subir máximo 8 imágenes.');
        e.preventDefault();
        return false;
    }
    
    // 5. Actualizar el input file antes de enviar
    updateFileInput();
    
    // 6. Si hay errores críticos, prevenir envío
    if (erroresCriticos) {
        e.preventDefault();
        
        // Enfocar el primer campo con error
        const primerError = document.querySelector('.is-invalid');
        if (primerError) {
            primerError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            primerError.focus();
        }
        
        return false;
    }
    
    // Si todo está bien, permitir el envío
    return true;
});

// Inicializar el contenedor de imágenes seleccionadas
document.addEventListener('DOMContentLoaded', function() {
    renderSelectedImages();
    
    // Limpiar clases de error al escribir en los campos
    document.querySelectorAll('input, select, textarea').forEach(elemento => {
        elemento.addEventListener('input', function() {
            this.classList.remove('is-invalid');
            
            // Remover mensaje de error si existe
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
    border: 1px solid #dee2e6;
}

.card-header {
    border-bottom: 1px solid rgba(0,0,0,0.125);
}

#selected-images-container .card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

#selected-images-container .btn-danger {
    transition: all 0.3s ease;
}

#selected-images-container .btn-danger:hover {
    transform: scale(1.1);
    background-color: #c82333;
    border-color: #bd2130;
}

/* Estilos para la jerarquía de categorías */
select option {
    white-space: pre;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

select option[value=""] {
    color: #95a5a6;
    font-style: italic;
}

/* Responsive */
@media (max-width: 768px) {
    #selected-images-container .card-img-top {
        height: 100px !important;
    }
}

@media (max-width: 576px) {
    #selected-images-container .col-6 {
        width: 50% !important;
    }
}

/* Mejorar la experiencia del input de precio */
input[name="dPrecio_venta"],
input[name="dPrecio_compra"] {
    font-family: 'Courier New', monospace;
    font-size: 1.1em;
    letter-spacing: 0.5px;
    text-align: right;
}

/* Estilos para errores de precio */
.precio-error {
    margin-top: 5px;
    font-size: 0.875em;
    color: #dc3545;
}
</style>
@endsection