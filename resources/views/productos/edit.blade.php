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

        <!-- IMÁGENES DEL PRODUCTO -->
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <div>
                    <i class="fas fa-images me-2"></i>
                    <h5 class="mb-0">Imágenes del Producto</h5>
                </div>
            </div>
            <div class="card-body">
                @php
                    $nombresArchivos = $producto->getNombresArchivosImagenes();
                @endphp
                
                <!-- CONTENEDOR DE TODAS LAS IMÁGENES (EXISTENTES + NUEVAS) -->
                <div class="row mb-4" id="all-images-container">
                    <!-- Imágenes existentes -->
                    @foreach($nombresArchivos as $index => $imagen)
                        <div class="col-6 col-md-4 col-lg-3 mb-3" id="image-container-{{ $imagen['nombre'] }}">
                            <div class="card h-100 border position-relative">
                                <!-- Imagen -->
                                <img src="{{ $imagen['url'] }}" 
                                     class="card-img-top" 
                                     style="height: 180px; object-fit: contain; background: #f8f9fa; padding: 10px;"
                                     alt="Imagen {{ $index + 1 }}">
                                
                                <!-- Botón para eliminar -->
                                <button type="button" 
                                        class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 eliminar-imagen-btn"
                                        data-image-name="{{ $imagen['nombre'] }}"
                                        style="width: 32px; height: 32px; padding: 0; border-radius: 50%; z-index: 10;"
                                        onclick="eliminarImagenExistente('{{ $imagen['nombre'] }}', this)"
                                        title="Eliminar esta imagen">
                                    <i class="fas fa-trash"></i>
                                </button>
                                
                                <!-- Input oculto para eliminar -->
                                <input type="hidden" 
                                       name="imagenes_a_eliminar[]"
                                       value="{{ $imagen['nombre'] }}"
                                       id="eliminar_{{ $imagen['nombre'] }}"
                                       class="d-none">
                                
                                <div class="card-body p-2 text-center">
                                    <small class="text-muted d-block" style="font-size: 12px;">
                                        {{ $imagen['nombre'] }}
                                    </small>
                                    <span class="badge bg-primary mt-1">
                                        Imagen {{ $index + 1 }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    
                    <!-- Aquí se agregarán las imágenes nuevas -->
                </div>
                
                <!-- AGREGAR NUEVAS IMÁGENES -->
                <div class="mt-4">
                    <h6 class="fw-bold mb-3"><i class="fas fa-plus-circle me-2"></i>Agregar nuevas imágenes</h6>
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
                                       onchange="agregarNuevasImagenes(event)">
                                @error('imagenes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @error('imagenes.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Formatos permitidos: JPG, JPEG, PNG, GIF, WEBP, JFIF, SVG. 
                                    Máximo 5MB por imagen.
                                </small>
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

        <!-- DIMENSIONES Y PESO -->
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
                    
                    <div class="col-md-6">
                        <div class="alert alert-secondary mt-4">
                            <h6 class="fw-bold mb-2"><i class="fas fa-info-circle me-2"></i>Información de cálculo:</h6>
                            <div id="volumen-info" class="small">
                                <div>Volumen: <span id="volumen-calculado">0.00</span> cm³</div>
                                <div>Peso volumétrico: <span id="peso-volumetrico">0.000</span> kg</div>
                                <div>Peso facturable: <span id="peso-facturable">0.000</span> kg</div>
                            </div>
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
// ==================== FUNCIONES PARA IMÁGENES ====================

// Array para almacenar imágenes nuevas
let nuevasImagenes = [];

// Agregar nuevas imágenes
function agregarNuevasImagenes(event) {
    const files = event.target.files;
    const contenedor = document.getElementById('all-images-container');
    
    // Verificar límite de 8 imágenes en total
    const totalImagenesActuales = nuevasImagenes.length + 
                                 document.querySelectorAll('#all-images-container .card:not(.nueva-imagen)').length;
    
    if (totalImagenesActuales + files.length > 8) {
        alert('Máximo 8 imágenes permitidas.');
        event.target.value = '';
        return;
    }
    
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const imageId = 'nueva_img_' + Date.now() + '_' + i;
            
            // Agregar al array
            nuevasImagenes.push({
                id: imageId,
                file: file,
                preview: e.target.result
            });
            
            // Crear elemento HTML
            const col = document.createElement('div');
            col.className = 'col-6 col-md-4 col-lg-3 mb-3';
            col.id = `image-container-${imageId}`;
            col.innerHTML = `
                <div class="card h-100 border position-relative nueva-imagen">
                    <!-- Imagen -->
                    <img src="${e.target.result}" 
                         class="card-img-top" 
                         style="height: 180px; object-fit: contain; background: #f8f9fa; padding: 10px;"
                         alt="Nueva imagen">
                    
                    <!-- Botón para eliminar -->
                    <button type="button" 
                            class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 eliminar-imagen-btn"
                            data-image-id="${imageId}"
                            style="width: 32px; height: 32px; padding: 0; border-radius: 50%; z-index: 10;"
                            onclick="eliminarNuevaImagen('${imageId}', this)"
                            title="Eliminar esta imagen">
                        <i class="fas fa-trash"></i>
                    </button>
                    
                    <div class="card-body p-2 text-center">
                        <small class="text-muted d-block" style="font-size: 12px;">
                            ${file.name.length > 20 ? file.name.substring(0, 20) + '...' : file.name}
                        </small>
                        <small class="text-muted d-block">
                            ${(file.size / 1024).toFixed(2)} KB
                        </small>
                        <span class="badge bg-success mt-1">
                            Nueva imagen
                        </span>
                    </div>
                </div>
            `;
            
            // Agregar al final del contenedor
            contenedor.appendChild(col);
            
            // Actualizar DataTransfer
            actualizarDataTransfer();
        }
        
        reader.readAsDataURL(file);
    }
    
    // Limpiar input
    event.target.value = '';
}

// Eliminar imagen existente - DEFINITIVAMENTE
function eliminarImagenExistente(imageName, button) {
    if (!confirm('¿Estás seguro de que deseas eliminar esta imagen? Esta acción no se puede deshacer.')) {
        return;
    }
    
    const container = document.getElementById(`image-container-${imageName}`);
    if (container) {
        // Activar el input hidden para eliminación
        const input = document.getElementById(`eliminar_${imageName}`);
        if (input) {
            input.type = 'hidden';
            input.name = 'imagenes_a_eliminar[]';
            input.value = imageName;
        }
        
        // Animación de eliminación
        container.style.opacity = '0.5';
        container.style.transform = 'scale(0.95)';
        
        setTimeout(() => {
            container.style.display = 'none';
        }, 300);
    }
}

// Eliminar nueva imagen - DEFINITIVAMENTE
function eliminarNuevaImagen(imageId, button) {
    if (!confirm('¿Estás seguro de que deseas eliminar esta imagen nueva?')) {
        return;
    }
    
    // Remover del array
    nuevasImagenes = nuevasImagenes.filter(img => img.id !== imageId);
    
    // Remover del DOM
    const container = document.getElementById(`image-container-${imageId}`);
    if (container) {
        container.style.opacity = '0';
        container.style.transform = 'scale(0.8)';
        
        setTimeout(() => {
            container.remove();
            
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
        calcularVolumen();
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
    
    calcularVolumen();
}

// Validar dimensiones
function validarDimension(input) {
    let value = input.value;
    const cursorPos = input.selectionStart;
    
    if (value === '') {
        input.classList.remove('is-invalid');
        calcularVolumen();
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
    
    calcularVolumen();
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

// Calcular volumen
function calcularVolumen() {
    const largo = parseFloat(document.getElementById('dLargo_cm').value) || 0;
    const ancho = parseFloat(document.getElementById('dAncho_cm').value) || 0;
    const alto = parseFloat(document.getElementById('dAlto_cm').value) || 0;
    const peso = parseFloat(document.getElementById('dPeso').value) || 0;
    
    const volumen = largo * ancho * alto;
    document.getElementById('volumen-calculado').textContent = volumen.toFixed(2);
    
    const pesoVolumetrico = volumen / 5000;
    document.getElementById('peso-volumetrico').textContent = pesoVolumetrico.toFixed(3);
    
    const pesoFacturable = Math.max(peso, pesoVolumetrico);
    document.getElementById('peso-facturable').textContent = pesoFacturable.toFixed(3);
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
    const imagenesAEliminar = document.querySelectorAll('input[name="imagenes_a_eliminar[]"]').length;
    const nuevasImagenesCount = nuevasImagenes.length;
    const imagenesExistentes = {{ count($producto->imagenes) }};
    const totalImagenes = imagenesExistentes - imagenesAEliminar + nuevasImagenesCount;
    
    if (totalImagenes > 8) {
        e.preventDefault();
        alert('Máximo 8 imágenes permitidas.');
        return false;
    }
    
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
    // Inicializar cálculo de volumen
    calcularVolumen();
    
    // Event listeners para calcular automáticamente
    ['dPeso', 'dLargo_cm', 'dAncho_cm', 'dAlto_cm'].forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            input.addEventListener('input', calcularVolumen);
        }
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
input[name="dAlto_cm"] {
    font-family: 'Courier New', monospace;
    font-size: 1.1em;
    letter-spacing: 0.5px;
    text-align: right;
}

.input-group-text {
    background-color: #f8f9fa;
    border: 1px solid #ced4da;
}

#volumen-info {
    background: rgba(0,0,0,0.03);
    padding: 10px;
    border-radius: 5px;
    border-left: 4px solid #17a2b8;
}

#volumen-info div {
    margin-bottom: 5px;
    font-family: 'Courier New', monospace;
}

#volumen-info span {
    font-weight: bold;
    color: #17a2b8;
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
</style>
@endsection