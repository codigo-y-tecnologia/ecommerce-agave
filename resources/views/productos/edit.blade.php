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

    <!-- Modal de confirmación para eliminar imágenes -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Eliminación
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <i class="fas fa-trash-alt fa-2x text-danger"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="text-danger mb-1">¿Eliminar imagen?</h5>
                            <p class="mb-0" id="modalMessage">Esta acción no se puede deshacer.</p>
                        </div>
                    </div>
                    <div class="text-center" id="imagePreviewContainer">
                        <!-- Vista previa de la imagen a eliminar -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="fas fa-trash me-1"></i> Sí, Eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('productos.update', $producto->id_producto) }}" method="POST" enctype="multipart/form-data" id="productoForm">
        @csrf
        @method('PUT')

        <!-- IMÁGENES DEL PRODUCTO - CORREGIDO -->
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <div>
                    <i class="fas fa-images me-2"></i>
                    <h5 class="mb-0">Imágenes del Producto (Máximo 8)</h5>
                </div>
            </div>
            <div class="card-body">
                @php
                    $nombresArchivos = $producto->getNombresArchivosImagenes();
                    $imagenesExistentes = count($nombresArchivos);
                @endphp
                
                <!-- CONTENEDOR DE IMÁGENES EXISTENTES - CORREGIDO -->
                <div class="row mb-4" id="imagenes-existentes-container">
                    @if($imagenesExistentes > 0)
                        <div class="col-12 mb-3">
                            <h6 class="fw-bold">
                                <i class="fas fa-images me-2"></i>Imágenes actuales
                                <span class="badge bg-primary">{{ $imagenesExistentes }} / 8</span>
                            </h6>
                        </div>
                        
                        @foreach($nombresArchivos as $index => $imagen)
                            <div class="col-6 col-md-4 col-lg-3 mb-3 image-item" id="image-container-{{ $imagen['nombre'] }}">
                                <div class="card h-100 border position-relative">
                                    <img src="{{ $imagen['url'] }}" 
                                         class="card-img-top existing-image" 
                                         style="height: 180px; object-fit: contain; background: #f8f9fa; padding: 10px;"
                                         alt="Imagen {{ $index + 1 }}"
                                         data-image-name="{{ $imagen['nombre'] }}"
                                         data-image-url="{{ $imagen['url'] }}">
                                    
                                    <!-- Botón para eliminar -->
                                    <button type="button" 
                                            class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 delete-existing-btn"
                                            style="width: 32px; height: 32px; padding: 0; border-radius: 50%; z-index: 10;"
                                            data-image-name="{{ $imagen['nombre'] }}"
                                            data-image-url="{{ $imagen['url'] }}"
                                            title="Eliminar esta imagen">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    
                                    <!-- Campo oculto para marcar la imagen a eliminar -->
                                    <input type="hidden" 
                                           name="imagenes_a_eliminar[]" 
                                           value="{{ $imagen['nombre'] }}"
                                           id="hidden-eliminar-{{ $imagen['nombre'] }}"
                                           class="hidden-eliminar-input"
                                           disabled>
                                    
                                    <!-- Badge de número -->
                                    <div class="position-absolute top-0 start-0 m-2">
                                        <span class="badge bg-primary">#{{ $index + 1 }}</span>
                                    </div>
                                    
                                    <div class="card-body p-2 text-center">
                                        <small class="text-muted d-block" style="font-size: 12px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            {{ $imagen['nombre'] }}
                                        </small>
                                        <span class="badge bg-success mt-1" id="badge-{{ $imagen['nombre'] }}">
                                            <i class="fas fa-check-circle me-1"></i> Actual
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-12 text-center py-4">
                            <i class="fas fa-image fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No hay imágenes disponibles</p>
                        </div>
                    @endif
                </div>
                
                <!-- CONTADOR DE IMÁGENES -->
                <div class="alert alert-info mb-4" id="contador-imagenes">
                    <i class="fas fa-info-circle me-2"></i>
                    <span id="contador-texto">
                        @if($imagenesExistentes > 0)
                            Tienes <strong>{{ $imagenesExistentes }}</strong> imágenes. Puedes eliminar imágenes existentes y agregar nuevas manteniendo un máximo de 8 imágenes en total.
                        @else
                            No tienes imágenes. Puedes agregar hasta 8 imágenes.
                        @endif
                    </span>
                </div>
                
                <!-- AGREGAR NUEVAS IMÁGENES -->
                <div class="mt-4">
                    <h6 class="fw-bold mb-3">
                        <i class="fas fa-plus-circle me-2"></i>Agregar nuevas imágenes
                        <span id="contador-nuevas" class="badge bg-success ms-2">0 nuevas</span>
                    </h6>
                    <div class="row">
                        <div class="col-md-12">
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
                                       onchange="manejarNuevasImagenes(event)">
                                @error('imagenes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Formatos permitidos: JPG, JPEG, PNG, GIF, WEBP, JFIF, SVG. 
                                    Máximo 5MB por imagen. Puedes seleccionar múltiples imágenes.
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- CONTENEDOR PARA PREVISUALIZAR NUEVAS IMÁGENES -->
                    <div class="row mb-4" id="nuevas-imagenes-container">
                        <!-- Las nuevas imágenes seleccionadas aparecerán aquí -->
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
                                   title="Solo letras y números (máximo 15 caracteres)">
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
                            <input type="text" name="iStock" id="iStock" 
                                   class="form-control @error('iStock') is-invalid @enderror"
                                   value="{{ old('iStock', $producto->iStock) }}" 
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
                                <option value="">Seleccionar marca</option>
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

        <!-- DIMENSIONES Y PESO - SECCIÓN NUEVA -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-ruler-combined me-2"></i>Dimensiones y Peso (Opcional)</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label for="dPeso" class="form-label fw-bold">
                                Peso (kg)
                            </label>
                            <div class="input-group">
                                <input type="text" 
                                       name="dPeso" 
                                       id="dPeso" 
                                       class="form-control @error('dPeso') is-invalid @enderror"
                                       value="{{ old('dPeso', $producto->dPeso ? number_format($producto->dPeso, 3, '.', '') : '') }}" 
                                       oninput="validarPeso(this)"
                                       placeholder="0.000"
                                       title="Peso en kilogramos (ej: 1.250)">
                                <span class="input-group-text">kg</span>
                            </div>
                            @error('dPeso')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Ej: 1.250 (máximo 999.999 kg)</small>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label for="dLargo_cm" class="form-label fw-bold">
                                Largo (cm)
                            </label>
                            <div class="input-group">
                                <input type="text" 
                                       name="dLargo_cm" 
                                       id="dLargo_cm" 
                                       class="form-control @error('dLargo_cm') is-invalid @enderror"
                                       value="{{ old('dLargo_cm', $producto->dLargo_cm ? number_format($producto->dLargo_cm, 2, '.', '') : '') }}" 
                                       oninput="validarDimension(this)"
                                       placeholder="0.00"
                                       title="Largo en centímetros">
                                <span class="input-group-text">cm</span>
                            </div>
                            @error('dLargo_cm')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Ej: 30.50 cm</small>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label for="dAncho_cm" class="form-label fw-bold">
                                Ancho (cm)
                            </label>
                            <div class="input-group">
                                <input type="text" 
                                       name="dAncho_cm" 
                                       id="dAncho_cm" 
                                       class="form-control @error('dAncho_cm') is-invalid @enderror"
                                       value="{{ old('dAncho_cm', $producto->dAncho_cm ? number_format($producto->dAncho_cm, 2, '.', '') : '') }}" 
                                       oninput="validarDimension(this)"
                                       placeholder="0.00"
                                       title="Ancho en centímetros">
                                <span class="input-group-text">cm</span>
                            </div>
                            @error('dAncho_cm')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Ej: 20.30 cm</small>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label for="dAlto_cm" class="form-label fw-bold">
                                Alto (cm)
                            </label>
                            <div class="input-group">
                                <input type="text" 
                                       name="dAlto_cm" 
                                       id="dAlto_cm" 
                                       class="form-control @error('dAlto_cm') is-invalid @enderror"
                                       value="{{ old('dAlto_cm', $producto->dAlto_cm ? number_format($producto->dAlto_cm, 2, '.', '') : '') }}" 
                                       oninput="validarDimension(this)"
                                       placeholder="0.00"
                                       title="Alto en centímetros">
                                <span class="input-group-text">cm</span>
                            </div>
                            @error('dAlto_cm')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Ej: 15.25 cm</small>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="vClase_envio" class="form-label fw-bold">
                                Clase de envío
                            </label>
                            <select name="vClase_envio" id="vClase_envio" 
                                    class="form-select @error('vClase_envio') is-invalid @enderror">
                                <option value="">Seleccionar clase...</option>
                                <option value="estandar" {{ old('vClase_envio', $producto->vClase_envio) == 'estandar' ? 'selected' : '' }}>Estándar</option>
                                <option value="express" {{ old('vClase_envio', $producto->vClase_envio) == 'express' ? 'selected' : '' }}>Express</option>
                                <option value="fragil" {{ old('vClase_envio', $producto->vClase_envio) == 'fragil' ? 'selected' : '' }}>Frágil</option>
                                <option value="grandes_dimensiones" {{ old('vClase_envio', $producto->vClase_envio) == 'grandes_dimensiones' ? 'selected' : '' }}>Grandes dimensiones</option>
                            </select>
                            @error('vClase_envio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Define la categoría de envío del producto</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- OFERTA ESPECIAL - SECCIÓN CORREGIDA (CON FORMATO DE FECHA CORRECTO) -->
        <div class="card mb-4">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="fas fa-percentage me-2"></i>Oferta Especial (Opcional)</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Configura una oferta temporal con precio especial y fechas específicas.
                </div>
                
                <div class="form-group mb-3">
                    <div class="form-check form-switch">
                        <input type="hidden" name="bTiene_oferta" value="0">
                        <input type="checkbox" name="bTiene_oferta" id="bTiene_oferta" 
                               class="form-check-input"
                               onchange="toggleOfertaForm()"
                               value="1"
                               {{ old('bTiene_oferta', $producto->bTiene_oferta ? '1' : '0') == '1' ? 'checked' : '' }}>
                        <label for="bTiene_oferta" class="form-check-label fw-bold">
                            Activar oferta especial
                        </label>
                    </div>
                </div>
                
                <div id="oferta-form" style="display: {{ old('bTiene_oferta', $producto->bTiene_oferta ? '1' : '0') == '1' ? 'block' : 'none' }};">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="dPrecio_oferta" class="form-label fw-bold">
                                    Precio de oferta <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="text" 
                                           name="dPrecio_oferta" 
                                           id="dPrecio_oferta" 
                                           class="form-control @error('dPrecio_oferta') is-invalid @enderror"
                                           value="{{ old('dPrecio_oferta', $producto->dPrecio_oferta ? number_format($producto->dPrecio_oferta, 2, '.', '') : '') }}" 
                                           oninput="validarPrecioOferta()"
                                           placeholder="0.00"
                                           title="Precio de oferta especial (debe ser menor que el precio de venta)">
                                    @error('dPrecio_oferta')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">
                                    Precio especial durante el periodo de oferta (debe ser menor que el precio de venta)
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="vMotivo_oferta" class="form-label fw-bold">
                                    Motivo de la oferta
                                </label>
                                <input type="text" 
                                       name="vMotivo_oferta" 
                                       id="vMotivo_oferta" 
                                       class="form-control @error('vMotivo_oferta') is-invalid @enderror"
                                       value="{{ old('vMotivo_oferta', $producto->vMotivo_oferta) }}" 
                                       maxlength="255"
                                       placeholder="Ej: Temporada navideña, Liquidación, etc.">
                                @error('vMotivo_oferta')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="dFecha_inicio_oferta" class="form-label fw-bold">
                                    Fecha de inicio <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       name="dFecha_inicio_oferta" 
                                       id="dFecha_inicio_oferta" 
                                       class="form-control @error('dFecha_inicio_oferta') is-invalid @enderror"
                                       value="{{ old('dFecha_inicio_oferta', $producto->dFecha_inicio_oferta ? \Carbon\Carbon::parse($producto->dFecha_inicio_oferta)->format('Y-m-d') : '') }}">
                                @error('dFecha_inicio_oferta')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Fecha de inicio de la oferta
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="dFecha_fin_oferta" class="form-label fw-bold">
                                    Fecha de fin <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       name="dFecha_fin_oferta" 
                                       id="dFecha_fin_oferta" 
                                       class="form-control @error('dFecha_fin_oferta') is-invalid @enderror"
                                       value="{{ old('dFecha_fin_oferta', $producto->dFecha_fin_oferta ? \Carbon\Carbon::parse($producto->dFecha_fin_oferta)->format('Y-m-d') : '') }}">
                                @error('dFecha_fin_oferta')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Fecha de finalización de la oferta
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        La oferta solo estará activa durante el periodo especificado.
                        Después de la fecha de fin, el producto volverá a su precio normal.
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
                
                @php
                    // Obtener atributos actuales del producto
                    $atributosSeleccionados = [];
                    foreach ($producto->valoresAtributos as $valor) {
                        $atributosSeleccionados[$valor->id_atributo][] = $valor->id_atributo_valor;
                    }
                @endphp
                
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
                                                       id="atributo_{{ $atributo->id_atributo }}_valor_{{ $valor->id_atributo_valor }}"
                                                       {{ isset($atributosSeleccionados[$atributo->id_atributo]) && in_array($valor->id_atributo_valor, $atributosSeleccionados[$atributo->id_atributo]) ? 'checked' : '' }}>
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

        <!-- DESCRIPCIÓN Y ETIQUETAS -->
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white d-flex align-items-center">
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
// ==================== VARIABLES GLOBALES PARA IMÁGENES ====================

// Array para almacenar imágenes nuevas seleccionadas
let nuevasImagenes = [];
let nuevasImagenesCounter = 0;
let imagenesAEliminar = []; // Array para almacenar nombres de imágenes a eliminar
let currentImageToDelete = null; // Variable para almacenar la imagen a eliminar

// ==================== FUNCIONES PARA MANEJAR IMÁGENES ====================

// Manejar la selección de nuevas imágenes
function manejarNuevasImagenes(event) {
    const files = event.target.files;
    const contenedor = document.getElementById('nuevas-imagenes-container');
    
    // Calcular imágenes totales después de cambios
    const imagenesActuales = {{ $imagenesExistentes }} - imagenesAEliminar.length;
    const espacioDisponible = 8 - (imagenesActuales + nuevasImagenes.length);
    
    if (files.length > espacioDisponible) {
        alert(`Solo puedes agregar ${espacioDisponible} imágenes más. Ya tienes ${imagenesActuales} imágenes existentes y ${nuevasImagenes.length} nuevas seleccionadas.`);
        event.target.value = '';
        return;
    }
    
    // Agregar nuevas imágenes
    let imagenesAgregadas = 0;
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        
        // Verificar si ya fue seleccionada entre las nuevas imágenes
        if (!esImagenDuplicada(file)) {
            // Verificar si el archivo ya existe entre las imágenes actuales del producto
            if (imagenYaExisteEnProducto(file)) {
                alert(`La imagen "${file.name}" ya existe en este producto.`);
                continue;
            }
            
            const reader = new FileReader();
            const imageId = 'nueva_img_' + Date.now() + '_' + nuevasImagenesCounter++;
            
            reader.onload = function(e) {
                // Agregar al array
                nuevasImagenes.push({
                    id: imageId,
                    file: file,
                    preview: e.target.result,
                    nombre: file.name,
                    size: file.size,
                    lastModified: file.lastModified
                });
                
                // Crear elemento para mostrar
                const col = document.createElement('div');
                col.className = 'col-6 col-md-4 col-lg-3 mb-3';
                col.id = `nueva-container-${imageId}`;
                col.innerHTML = `
                    <div class="card h-100 border position-relative nueva-imagen">
                        <!-- Imagen -->
                        <img src="${e.target.result}" 
                             class="card-img-top" 
                             style="height: 180px; object-fit: contain; background: #f8f9fa; padding: 10px;"
                             alt="Nueva imagen">
                        
                        <!-- Botón para eliminar -->
                        <button type="button" 
                                class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2"
                                style="width: 32px; height: 32px; padding: 0; border-radius: 50%; z-index: 10;"
                                onclick="mostrarConfirmacionEliminarNueva('${imageId}', '${file.name}')"
                                title="Eliminar esta imagen">
                            <i class="fas fa-times"></i>
                        </button>
                        
                        <div class="card-body p-2 text-center">
                            <small class="text-muted d-block" style="font-size: 12px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                ${file.name.length > 20 ? file.name.substring(0, 20) + '...' : file.name}
                            </small>
                            <small class="text-muted d-block">
                                ${(file.size / 1024).toFixed(2)} KB
                            </small>
                            <span class="badge bg-success mt-1">
                                Nueva
                            </span>
                        </div>
                    </div>
                `;
                
                // Agregar al contenedor
                contenedor.appendChild(col);
                imagenesAgregadas++;
            };
            
            reader.readAsDataURL(file);
        } else {
            alert(`La imagen "${file.name}" ya fue seleccionada.`);
        }
    }
    
    if (imagenesAgregadas > 0) {
        // Actualizar contador
        actualizarContadorImagenes();
        
        // Actualizar DataTransfer
        actualizarDataTransfer();
    }
    
    // Limpiar input para permitir nuevas selecciones
    event.target.value = '';
}

// Verificar si la imagen ya fue seleccionada entre las nuevas imágenes
function esImagenDuplicada(newFile) {
    return nuevasImagenes.some(img => 
        img.file.name === newFile.name && 
        img.file.size === newFile.size && 
        img.file.lastModified === newFile.lastModified
    );
}

// Verificar si la imagen ya existe en el producto actual (imágenes existentes no marcadas para eliminar)
function imagenYaExisteEnProducto(file) {
    // Obtener todos los nombres de imágenes existentes que NO están marcadas para eliminar
    const nombresImagenesExistentes = [];
    
    // Obtener nombres de imágenes existentes desde los datos PHP
    @foreach($nombresArchivos as $imagen)
        if (!imagenesAEliminar.includes('{{ $imagen['nombre'] }}')) {
            nombresImagenesExistentes.push('{{ $imagen['nombre'] }}');
        }
    @endforeach
    
    // Verificar si el nombre del archivo ya existe
    return nombresImagenesExistentes.some(nombreExistente => {
        // Comparamos solo el nombre del archivo
        return nombreExistente.toLowerCase() === file.name.toLowerCase();
    });
}

// Mostrar modal de confirmación para eliminar imagen existente
function mostrarConfirmacionEliminarExistente(imageName, imageUrl) {
    currentImageToDelete = {
        type: 'existing',
        name: imageName,
        url: imageUrl
    };
    
    // Actualizar mensaje del modal
    document.getElementById('modalMessage').textContent = `¿Estás seguro de que deseas eliminar la imagen "${imageName}"? Esta acción no se puede deshacer.`;
    
    // Mostrar vista previa de la imagen
    const previewContainer = document.getElementById('imagePreviewContainer');
    previewContainer.innerHTML = `
        <div class="text-center mt-3">
            <img src="${imageUrl}" 
                 alt="Vista previa" 
                 class="img-fluid rounded" 
                 style="max-height: 150px; max-width: 100%;">
            <p class="mt-2 text-muted"><small>${imageName}</small></p>
        </div>
    `;
    
    // Mostrar el modal
    const modal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
    modal.show();
}

// Mostrar modal de confirmación para eliminar imagen nueva
function mostrarConfirmacionEliminarNueva(imageId, fileName) {
    // Buscar la imagen en el array de nuevas imágenes
    const imagen = nuevasImagenes.find(img => img.id === imageId);
    if (!imagen) return;
    
    currentImageToDelete = {
        type: 'new',
        id: imageId,
        name: fileName,
        preview: imagen.preview
    };
    
    // Actualizar mensaje del modal
    document.getElementById('modalMessage').textContent = `¿Estás seguro de que deseas eliminar la imagen "${fileName}"? Esta acción no se puede deshacer.`;
    
    // Mostrar vista previa de la imagen
    const previewContainer = document.getElementById('imagePreviewContainer');
    previewContainer.innerHTML = `
        <div class="text-center mt-3">
            <img src="${imagen.preview}" 
                 alt="Vista previa" 
                 class="img-fluid rounded" 
                 style="max-height: 150px; max-width: 100%;">
            <p class="mt-2 text-muted"><small>${fileName}</small></p>
        </div>
    `;
    
    // Mostrar el modal
    const modal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
    modal.show();
}

// Función para manejar la eliminación confirmada
function confirmarEliminacion() {
    if (!currentImageToDelete) return;
    
    if (currentImageToDelete.type === 'existing') {
        // Eliminar imagen existente
        eliminarImagenExistenteConfirmada(currentImageToDelete.name);
    } else if (currentImageToDelete.type === 'new') {
        // Eliminar imagen nueva
        eliminarNuevaImagenConfirmada(currentImageToDelete.id);
    }
    
    // Cerrar el modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('confirmDeleteModal'));
    modal.hide();
    
    // Limpiar la variable
    currentImageToDelete = null;
}

// Eliminar una imagen existente (después de confirmación)
function eliminarImagenExistenteConfirmada(nombreImagen) {
    // Agregar a la lista de imágenes a eliminar
    if (!imagenesAEliminar.includes(nombreImagen)) {
        imagenesAEliminar.push(nombreImagen);
    }
    
    const hiddenInput = document.getElementById(`hidden-eliminar-${nombreImagen}`);
    const container = document.getElementById(`image-container-${nombreImagen}`);
    
    if (hiddenInput) {
        hiddenInput.disabled = false;
    }
    
    if (container) {
        // Aplicar animación de eliminación
        container.style.opacity = '0.5';
        container.style.transform = 'scale(0.95)';
        container.style.transition = 'all 0.3s ease';
        
        // Después de la animación, remover del DOM
        setTimeout(() => {
            container.style.display = 'none';
            
            // Actualizar contador
            actualizarContadorImagenes();
        }, 300);
    }
}

// Eliminar una imagen nueva seleccionada (después de confirmación)
function eliminarNuevaImagenConfirmada(imageId) {
    // Remover del array
    nuevasImagenes = nuevasImagenes.filter(img => img.id !== imageId);
    
    // Remover del DOM
    const container = document.getElementById(`nueva-container-${imageId}`);
    if (container) {
        // Aplicar animación de eliminación
        container.style.opacity = '0.5';
        container.style.transform = 'scale(0.95)';
        container.style.transition = 'all 0.3s ease';
        
        // Después de la animación, remover del DOM
        setTimeout(() => {
            container.remove();
            
            // Actualizar contador
            actualizarContadorImagenes();
            
            // Actualizar DataTransfer
            actualizarDataTransfer();
        }, 300);
    }
}

// Actualizar DataTransfer para mantener archivos
function actualizarDataTransfer() {
    const dataTransfer = new DataTransfer();
    
    // Agregar todas las nuevas imágenes al DataTransfer
    nuevasImagenes.forEach(imagen => {
        dataTransfer.items.add(imagen.file);
    });
    
    // Actualizar input file
    const fileInput = document.getElementById('imagenes');
    fileInput.files = dataTransfer.files;
}

// Actualizar contador de imágenes
function actualizarContadorImagenes() {
    const imagenesActuales = {{ $imagenesExistentes }} - imagenesAEliminar.length;
    const totalImagenes = imagenesActuales + nuevasImagenes.length;
    
    // Actualizar contador de nuevas imágenes
    const contadorNuevas = document.getElementById('contador-nuevas');
    if (contadorNuevas) {
        contadorNuevas.textContent = `${nuevasImagenes.length} nuevas`;
    }
    
    // Actualizar texto informativo
    const contadorTexto = document.getElementById('contador-texto');
    if (contadorTexto) {
        if (totalImagenes > 8) {
            contadorTexto.innerHTML = `<span class="text-danger"><i class="fas fa-exclamation-triangle me-1"></i>Excedes el límite de 8 imágenes. Tienes ${totalImagenes} imágenes (${imagenesActuales} existentes + ${nuevasImagenes.length} nuevas).</span>`;
        } else {
            contadorTexto.innerHTML = `
                Tienes <strong>${totalImagenes} de 8</strong> imágenes: 
                <span class="text-primary">${imagenesActuales} existentes</span> 
                <span class="text-success">${nuevasImagenes.length} nuevas</span>.
                ${imagenesAEliminar.length > 0 ? `<span class="text-danger">(${imagenesAEliminar.length} marcadas para eliminar)</span>` : ''}
            `;
        }
    }
}

// ==================== FUNCIONES DE VALIDACIÓN ====================

// Validar precio
function validarPrecio(input) {
    let value = input.value;
    const cursorPos = input.selectionStart;
    
    if (value === '') {
        input.classList.remove('is-invalid');
        return;
    }
    
    value = value.replace(/[^0-9.]/g, '');
    
    const puntos = value.split('.').length - 1;
    if (puntos > 1) {
        const partes = value.split('.');
        value = partes[0] + '.' + partes.slice(1).join('');
    }
    
    value = value.replace(/\.{2,}/g, '.');
    
    if (value.startsWith('.')) {
        value = '0' + value;
    }
    
    const partesNumero = value.split('.');
    const parteEntera = partesNumero[0];
    
    if (parteEntera.length > 7) {
        value = parteEntera.substring(0, 7) + (partesNumero[1] ? '.' + partesNumero[1] : '');
    }
    
    if (value.includes('.')) {
        const partes = value.split('.');
        if (partes[1].length > 2) {
            partes[1] = partes[1].substring(0, 2);
            value = partes[0] + '.' + partes[1];
        }
    }
    
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
    
    if (value) {
        const numero = parseFloat(value);
        if (!isNaN(numero) && numero > 9999999.99) {
            input.classList.add('is-invalid');
            mostrarErrorPrecio(input, 'El precio máximo es 9,999,999.99');
        }
    }
}

function mostrarErrorPrecio(input, mensaje) {
    const errorId = `error-${input.id}`;
    const errorElement = document.getElementById(errorId);
    if (errorElement) {
        errorElement.remove();
    }
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'invalid-feedback d-block precio-error';
    errorDiv.textContent = mensaje;
    errorDiv.id = errorId;
    
    input.parentNode.appendChild(errorDiv);
}

// Validar stock
function validarStock(input) {
    input.value = input.value.replace(/[^0-9]/g, '');
    
    if (input.value.length > 4) {
        input.value = input.value.substring(0, 4);
    }
    
    if (input.value && parseInt(input.value) < 0) {
        input.value = '0';
    }
    
    if (input.value.length > 1 && input.value.startsWith('0')) {
        input.value = input.value.replace(/^0+/, '');
    }
    
    if (input.value === '') {
        input.value = '0';
    }
    
    input.classList.remove('is-invalid');
}

// Validar SKU
function validarSKU(input) {
    input.value = input.value.replace(/[^A-Za-z0-9]/g, '');
    
    if (input.value.length > 15) {
        input.value = input.value.substring(0, 15);
    }
    
    input.value = input.value.toUpperCase();
    
    input.classList.remove('is-invalid');
}

// Validar peso
function validarPeso(input) {
    let value = input.value;
    const cursorPos = input.selectionStart;
    
    if (value === '') {
        input.classList.remove('is-invalid');
        return;
    }
    
    value = value.replace(/[^0-9.]/g, '');
    
    const puntos = value.split('.').length - 1;
    if (puntos > 1) {
        const partes = value.split('.');
        value = partes[0] + '.' + partes.slice(1).join('');
    }
    
    value = value.replace(/\.{2,}/g, '.');
    
    if (value.startsWith('.')) {
        value = '0' + value;
    }
    
    const partesNumero = value.split('.');
    const parteEntera = partesNumero[0];
    
    if (parteEntera.length > 3) {
        value = parteEntera.substring(0, 3) + (partesNumero[1] ? '.' + partesNumero[1] : '');
    }
    
    if (value.includes('.')) {
        const partes = value.split('.');
        if (partes[1].length > 3) {
            partes[1] = partes[1].substring(0, 3);
            value = partes[0] + '.' + partes[1];
        }
    }
    
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
    
    if (value) {
        const numero = parseFloat(value);
        if (!isNaN(numero) && numero > 999.999) {
            input.classList.add('is-invalid');
            mostrarErrorGeneral(input, 'El peso máximo es 999.999 kg');
        }
    }
}

// Validar dimensiones
function validarDimension(input) {
    let value = input.value;
    const cursorPos = input.selectionStart;
    
    if (value === '') {
        input.classList.remove('is-invalid');
        return;
    }
    
    value = value.replace(/[^0-9.]/g, '');
    
    const puntos = value.split('.').length - 1;
    if (puntos > 1) {
        const partes = value.split('.');
        value = partes[0] + '.' + partes.slice(1).join('');
    }
    
    value = value.replace(/\.{2,}/g, '.');
    
    if (value.startsWith('.')) {
        value = '0' + value;
    }
    
    const partesNumero = value.split('.');
    const parteEntera = partesNumero[0];
    
    if (parteEntera.length > 3) {
        value = parteEntera.substring(0, 3) + (partesNumero[1] ? '.' + partesNumero[1] : '');
    }
    
    if (value.includes('.')) {
        const partes = value.split('.');
        if (partes[1].length > 2) {
            partes[1] = partes[1].substring(0, 2);
            value = partes[0] + '.' + partes[1];
        }
    }
    
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
    
    if (value) {
        const numero = parseFloat(value);
        if (!isNaN(numero) && numero > 999.99) {
            input.classList.add('is-invalid');
            mostrarErrorGeneral(input, 'La dimensión máxima es 999.99 cm');
        }
    }
}

function mostrarErrorGeneral(input, mensaje) {
    const errorId = `error-${input.id}`;
    const errorElement = document.getElementById(errorId);
    
    if (errorElement) {
        errorElement.remove();
    }
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'invalid-feedback d-block';
    errorDiv.textContent = mensaje;
    errorDiv.id = errorId;
    
    input.parentNode.appendChild(errorDiv);
}

// ==================== FUNCIONES PARA OFERTA ====================

// Mostrar/ocultar formulario de oferta
function toggleOfertaForm() {
    const ofertaCheckbox = document.getElementById('bTiene_oferta');
    const ofertaForm = document.getElementById('oferta-form');
    
    if (ofertaCheckbox.checked) {
        ofertaForm.style.display = 'block';
    } else {
        ofertaForm.style.display = 'none';
    }
    
    // Validar precio de oferta al cambiar
    validarPrecioOferta();
}

// ==================== VALIDACIÓN DE PRECIO DE OFERTA ====================

// Validar precio de oferta en tiempo real
function validarPrecioOferta() {
    const precioVentaInput = document.getElementById('dPrecio_venta');
    const precioOfertaInput = document.getElementById('dPrecio_oferta');
    const tieneOfertaCheckbox = document.getElementById('bTiene_oferta');
    
    if (!precioVentaInput || !precioOfertaInput || !tieneOfertaCheckbox) {
        return true;
    }
    
    const precioVenta = parseFloat(precioVentaInput.value) || 0;
    const precioOferta = parseFloat(precioOfertaInput.value) || 0;
    const tieneOferta = tieneOfertaCheckbox.checked;
    
    if (tieneOferta && precioOferta > 0) {
        if (precioOferta >= precioVenta) {
            mostrarErrorOferta('El precio de oferta debe ser menor que el precio de venta.');
            return false;
        } else {
            limpiarErrorOferta();
            return true;
        }
    }
    
    limpiarErrorOferta();
    return true;
}

function mostrarErrorOferta(mensaje) {
    const inputOferta = document.getElementById('dPrecio_oferta');
    const errorId = 'error-precio-oferta';
    
    // Remover error anterior si existe
    const errorElement = document.getElementById(errorId);
    if (errorElement) {
        errorElement.remove();
    }
    
    // Crear elemento de error
    const errorDiv = document.createElement('div');
    errorDiv.className = 'invalid-feedback d-block text-danger mt-1';
    errorDiv.id = errorId;
    errorDiv.innerHTML = `<i class="fas fa-exclamation-triangle me-1"></i> ${mensaje}`;
    
    // Insertar después del input
    const inputGroup = inputOferta.closest('.input-group') || inputOferta.parentNode;
    inputGroup.appendChild(errorDiv);
    inputOferta.classList.add('is-invalid');
}

function limpiarErrorOferta() {
    const inputOferta = document.getElementById('dPrecio_oferta');
    const errorId = 'error-precio-oferta';
    
    const errorElement = document.getElementById(errorId);
    if (errorElement) {
        errorElement.remove();
    }
    
    inputOferta.classList.remove('is-invalid');
}

// Validación de fechas de oferta
function validarFechasOferta() {
    const fechaInicio = document.getElementById('dFecha_inicio_oferta');
    const fechaFin = document.getElementById('dFecha_fin_oferta');
    
    if (fechaInicio.value && fechaFin.value) {
        const inicio = new Date(fechaInicio.value);
        const fin = new Date(fechaFin.value);
        
        if (fin < inicio) {
            fechaFin.setCustomValidity('La fecha de fin debe ser posterior a la fecha de inicio');
            return false;
        }
    }
    
    fechaFin.setCustomValidity('');
    return true;
}

// ==================== VALIDACIÓN DEL FORMULARIO ====================

document.getElementById('productoForm').addEventListener('submit', function(e) {
    let erroresCriticos = false;
    
    // Validar campos obligatorios
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
    
    // Validar stock
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
    
    // Validar imágenes (máximo 8)
    const imagenesActuales = {{ $imagenesExistentes }} - imagenesAEliminar.length;
    const totalImagenes = imagenesActuales + nuevasImagenes.length;
    
    if (totalImagenes > 8) {
        e.preventDefault();
        alert(`Máximo 8 imágenes permitidas. Tienes ${totalImagenes} imágenes (${imagenesActuales} existentes + ${nuevasImagenes.length} nuevas).`);
        return false;
    }
    
    // Validar precio de oferta si está activa
    const tieneOfertaCheckbox = document.getElementById('bTiene_oferta');
    const precioOfertaInput = document.getElementById('dPrecio_oferta');
    const precioVentaInput = document.getElementById('dPrecio_venta');
    
    if (tieneOfertaCheckbox && precioOfertaInput && precioVentaInput) {
        const tieneOferta = tieneOfertaCheckbox.checked;
        const precioOferta = parseFloat(precioOfertaInput.value) || 0;
        const precioVenta = parseFloat(precioVentaInput.value) || 0;
        
        if (tieneOferta) {
            // Validar que precio de oferta tenga valor
            if (precioOferta <= 0) {
                precioOfertaInput.classList.add('is-invalid');
                erroresCriticos = true;
                
                if (!precioOfertaInput.nextElementSibling || !precioOfertaInput.nextElementSibling.classList.contains('invalid-feedback')) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback';
                    errorDiv.textContent = 'El precio de oferta es obligatorio cuando se activa la oferta';
                    precioOfertaInput.parentNode.appendChild(errorDiv);
                }
            }
            
            // Validar que precio de oferta sea menor que precio de venta
            if (precioOferta > 0 && precioOferta >= precioVenta) {
                precioOfertaInput.classList.add('is-invalid');
                erroresCriticos = true;
                
                if (!document.getElementById('error-precio-oferta')) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback d-block text-danger mt-1';
                    errorDiv.id = 'error-precio-oferta';
                    errorDiv.innerHTML = `<i class="fas fa-exclamation-triangle me-1"></i> El precio de oferta debe ser menor que el precio de venta`;
                    precioOfertaInput.parentNode.appendChild(errorDiv);
                }
            }
            
            // Validar fechas de oferta
            const fechaInicio = document.getElementById('dFecha_inicio_oferta');
            const fechaFin = document.getElementById('dFecha_fin_oferta');
            
            // Validar que las fechas tengan valor
            if (!fechaInicio.value) {
                fechaInicio.classList.add('is-invalid');
                erroresCriticos = true;
                
                if (!fechaInicio.nextElementSibling || !fechaInicio.nextElementSibling.classList.contains('invalid-feedback')) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback';
                    errorDiv.textContent = 'La fecha de inicio es obligatoria cuando se activa la oferta';
                    fechaInicio.parentNode.appendChild(errorDiv);
                }
            }
            
            if (!fechaFin.value) {
                fechaFin.classList.add('is-invalid');
                erroresCriticos = true;
                
                if (!fechaFin.nextElementSibling || !fechaFin.nextElementSibling.classList.contains('invalid-feedback')) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback';
                    errorDiv.textContent = 'La fecha de fin es obligatoria cuando se activa la oferta';
                    fechaFin.parentNode.appendChild(errorDiv);
                }
            }
            
            if (fechaInicio.value && fechaFin.value) {
                const inicio = new Date(fechaInicio.value);
                const fin = new Date(fechaFin.value);
                
                // Permitir fechas pasadas, solo validar que fin sea mayor que inicio
                if (fin < inicio) {
                    fechaFin.classList.add('is-invalid');
                    erroresCriticos = true;
                    
                    if (!fechaFin.nextElementSibling || !fechaFin.nextElementSibling.classList.contains('invalid-feedback')) {
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'invalid-feedback';
                        errorDiv.textContent = 'La fecha de fin debe ser posterior a la fecha de inicio';
                        fechaFin.parentNode.appendChild(errorDiv);
                    }
                }
            }
        }
    }
    
    // Actualizar DataTransfer antes de enviar
    actualizarDataTransfer();
    
    if (erroresCriticos) {
        e.preventDefault();
        
        const primerError = document.querySelector('.is-invalid');
        if (primerError) {
            primerError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            primerError.focus();
        }
        
        return false;
    }
    
    return true;
});

// ==================== INICIALIZACIÓN ====================

document.addEventListener('DOMContentLoaded', function() {
    // Configurar botón de confirmación del modal
    document.getElementById('confirmDeleteBtn').addEventListener('click', confirmarEliminacion);
    
    // Configurar botones de eliminar imágenes existentes para usar el modal
    document.querySelectorAll('.delete-existing-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const imageName = this.getAttribute('data-image-name');
            const imageUrl = this.getAttribute('data-image-url');
            mostrarConfirmacionEliminarExistente(imageName, imageUrl);
        });
    });
    
    // Limpiar errores al escribir
    document.querySelectorAll('input, select, textarea').forEach(elemento => {
        elemento.addEventListener('input', function() {
            this.classList.remove('is-invalid');
            
            const errorFeedback = this.nextElementSibling;
            if (errorFeedback && errorFeedback.classList.contains('invalid-feedback')) {
                errorFeedback.remove();
            }
            
            // Si es precio de venta, validar precio de oferta
            if (this.id === 'dPrecio_venta') {
                validarPrecioOferta();
            }
        });
    });
    
    // Inicializar contador de imágenes
    actualizarContadorImagenes();
    
    // Inicializar toggle de oferta
    toggleOfertaForm();
    
    // Event listeners para validar precio de oferta en tiempo real
    const precioVentaInput = document.getElementById('dPrecio_venta');
    const precioOfertaInput = document.getElementById('dPrecio_oferta');
    const tieneOfertaCheckbox = document.getElementById('bTiene_oferta');
    
    if (precioVentaInput && precioOfertaInput && tieneOfertaCheckbox) {
        precioVentaInput.addEventListener('input', validarPrecioOferta);
        precioOfertaInput.addEventListener('input', validarPrecioOferta);
        tieneOfertaCheckbox.addEventListener('change', validarPrecioOferta);
    }
    
    // Event listeners para validar fechas de oferta
    const fechaInicio = document.getElementById('dFecha_inicio_oferta');
    const fechaFin = document.getElementById('dFecha_fin_oferta');
    
    if (fechaInicio) {
        fechaInicio.addEventListener('change', function() {
            if (fechaFin.value && new Date(fechaFin.value) < new Date(this.value)) {
                fechaFin.value = this.value;
            }
            validarFechasOferta();
        });
    }
    
    if (fechaFin) {
        fechaFin.addEventListener('change', validarFechasOferta);
    }
    
    // **IMPORTANTE: NO se establece fecha mínima para permitir fechas pasadas**
    // Esto permite mantener las fechas originales de la oferta
});
</script>

<style>
/* ==================== ESTILOS GENERALES ==================== */
.card {
    transition: all 0.3s ease;
    border: 1px solid #dee2e6;
}

.card-header {
    border-bottom: 1px solid rgba(0,0,0,0.125);
}

/* ==================== ESTILOS PARA IMÁGENES ==================== */
.card-img-top {
    transition: transform 0.3s ease;
}

.card:hover .card-img-top {
    transform: scale(1.05);
}

.eliminar-imagen-btn {
    transition: all 0.3s ease;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

.eliminar-imagen-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
}

/* Estilos para botones de eliminar */
.btn-danger.btn-sm {
    transition: all 0.3s ease;
}

.btn-danger.btn-sm:hover {
    transform: scale(1.1);
    background-color: #c82333;
    border-color: #bd2130;
}

/* Estilos para errores */
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

/* Inputs especiales */
input[name="dPrecio_venta"],
input[name="dPrecio_compra"],
input[name="dPeso"],
input[name="dLargo_cm"],
input[name="dAncho_cm"],
input[name="dAlto_cm"],
input[name="dPrecio_oferta"] {
    font-family: 'Courier New', monospace;
    font-size: 1.1em;
    letter-spacing: 0.5px;
    text-align: right;
}

.input-group-text {
    background-color: #f8f9fa;
    border: 1px solid #ced4da;
}

/* Estilos para sección de oferta */
#oferta-form {
    transition: all 0.3s ease;
}

.card-header.bg-danger {
    background: linear-gradient(135deg, #dc3545, #c82333);
}

/* Animaciones para imágenes eliminadas */
@keyframes fadeOut {
    from { opacity: 1; transform: scale(1); }
    to { opacity: 0; transform: scale(0.95); }
}

.image-item.eliminando {
    animation: fadeOut 0.3s ease forwards;
}

/* Responsive */
@media (max-width: 768px) {
    .card-img-top {
        height: 150px !important;
    }
}

@media (max-width: 576px) {
    .card-img-top {
        height: 130px !important;
    }
}

/* Estilos para imágenes marcadas para eliminar */
[id^="image-container-"] {
    transition: all 0.3s ease;
}

/* Estilos para el badge de imágenes */
.badge {
    font-size: 12px;
    padding: 4px 8px;
}

/* Estilos para el modal de confirmación */
#confirmDeleteModal .modal-header {
    background: linear-gradient(135deg, #dc3545, #c82333);
}

#confirmDeleteModal .modal-body {
    padding: 25px;
}
</style>
@endsection