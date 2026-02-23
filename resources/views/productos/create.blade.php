@extends('layouts.app')

@section('title', 'Registrar Nuevo Producto')
@section('content')
<div class="container-fluid">
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
                                   title="Solo letras y números (máximo 15 caracteres)"
                                   autocomplete="off">
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
                                   required
                                   autocomplete="off">
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
                                       title="Máximo: 9,999,999.99 (7 dígitos enteros, 2 decimales)"
                                       autocomplete="off">
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
                                Precio de venta (sin impuesto) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="text" 
                                       name="dPrecio_venta" 
                                       id="dPrecio_venta" 
                                       class="form-control @error('dPrecio_venta') is-invalid @enderror"
                                       value="{{ old('dPrecio_venta') }}" 
                                       required 
                                       oninput="validarPrecio(this); actualizarPrecioFinal();"
                                       placeholder="0.00"
                                       title="Máximo: 9,999,999.99 (7 dígitos enteros, 2 decimales)"
                                       autocomplete="off">
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
                                   max="9999"
                                   autocomplete="off">
                            @error('iStock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Máximo 4 dígitos (0-9999)</small>
                        </div>
                    </div>
                </div>

                <!-- CAMPOS DE DESCUENTO -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-percentage me-1"></i>Descuento Especial
                            </label>
                            <div class="form-check form-switch">
                                <input type="checkbox" name="bTiene_descuento" id="bTiene_descuento" 
                                       class="form-check-input" value="1"
                                       {{ old('bTiene_descuento') ? 'checked' : '' }}
                                       onchange="toggleDescuentoFields()">
                                <label class="form-check-label" for="bTiene_descuento">
                                    Activar Descuento para este producto
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                Permite establecer un precio de descuento por tiempo limitado
                            </small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="bActivo" id="bActivo" 
                                       class="form-check-input" value="1"
                                       {{ old('bActivo', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="bActivo">
                                    Producto activo
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                Si está desactivado, el producto no será visible en el catálogo
                            </small>
                        </div>
                    </div>
                </div>

                <!-- CAMPOS DE DESCUENTO (OCULTOS INICIALMENTE) -->
                <div id="descuentoFields" style="display: none;">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="dPrecio_descuento" class="form-label fw-bold">
                                    Precio de Descuento <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="text" 
                                           name="dPrecio_descuento" 
                                           id="dPrecio_descuento" 
                                           class="form-control @error('dPrecio_descuento') is-invalid @enderror"
                                           value="{{ old('dPrecio_descuento') }}" 
                                           oninput="validarPrecio(this); validarPrecioDescuentoProductoInstantaneo(this);"
                                           onblur="validarPrecioDescuentoProducto()"
                                           placeholder="0.00"
                                           autocomplete="off">
                                </div>
                                <div id="error-precio-descuento" class="invalid-feedback" style="display: none;"></div>
                                @error('dPrecio_descuento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Debe ser menor al precio de venta</small>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="dFecha_inicio_descuento" class="form-label fw-bold">
                                    Fecha inicio <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       name="dFecha_inicio_descuento" 
                                       id="dFecha_inicio_descuento" 
                                       class="form-control @error('dFecha_inicio_descuento') is-invalid @enderror"
                                       value="{{ old('dFecha_inicio_descuento') }}"
                                       autocomplete="off">
                                @error('dFecha_inicio_descuento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="dFecha_fin_descuento" class="form-label fw-bold">
                                    Fecha fin <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       name="dFecha_fin_descuento" 
                                       id="dFecha_fin_descuento" 
                                       class="form-control @error('dFecha_fin_descuento') is-invalid @enderror"
                                       value="{{ old('dFecha_fin_descuento') }}"
                                       autocomplete="off">
                                @error('dFecha_fin_descuento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label for="vMotivo_descuento" class="form-label fw-bold">
                                    Motivo del descuento
                                </label>
                                <input type="text" 
                                       name="vMotivo_descuento" 
                                       id="vMotivo_descuento" 
                                       class="form-control @error('vMotivo_descuento') is-invalid @enderror"
                                       value="{{ old('vMotivo_descuento') }}"
                                       maxlength="255"
                                       placeholder="Ej: Liquidación de temporada, Black Friday, etc."
                                       autocomplete="off">
                                @error('vMotivo_descuento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CAMPOS PARA DIMENSIONES Y PESO -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label for="dPeso" class="form-label fw-bold">
                                <i class="fas fa-weight-hanging me-1"></i>Peso (kg)
                            </label>
                            <input type="text" 
                                   name="dPeso" 
                                   id="dPeso" 
                                   class="form-control @error('dPeso') is-invalid @enderror"
                                   value="{{ old('dPeso') }}"
                                   oninput="validarPeso(this)"
                                   onblur="formatearPeso(this)"
                                   onkeydown="permitirBorrado(event)"
                                   placeholder="0.000"
                                   title="Máximo: 999.999 kg (máximo 3 decimales)"
                                   autocomplete="off">
                            @error('dPeso')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label for="dLargo_cm" class="form-label fw-bold">
                                <i class="fas fa-ruler-vertical me-1"></i>Largo (cm)
                            </label>
                            <input type="text" 
                                   name="dLargo_cm" 
                                   id="dLargo_cm" 
                                   class="form-control @error('dLargo_cm') is-invalid @enderror"
                                   value="{{ old('dLargo_cm') }}"
                                   oninput="validarDimensionCm(this)"
                                   onblur="formatearDimensionCm(this)"
                                   onkeydown="permitirBorrado(event)"
                                   placeholder="0.00"
                                   title="Máximo: 999.99 cm (máximo 2 decimales)"
                                   autocomplete="off">
                            @error('dLargo_cm')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label for="dAncho_cm" class="form-label fw-bold">
                                <i class="fas fa-ruler-horizontal me-1"></i>Ancho (cm)
                            </label>
                            <input type="text" 
                                   name="dAncho_cm" 
                                   id="dAncho_cm" 
                                   class="form-control @error('dAncho_cm') is-invalid @enderror"
                                   value="{{ old('dAncho_cm') }}"
                                   oninput="validarDimensionCm(this)"
                                   onblur="formatearDimensionCm(this)"
                                   onkeydown="permitirBorrado(event)"
                                   placeholder="0.00"
                                   title="Máximo: 999.99 cm (máximo 2 decimales)"
                                   autocomplete="off">
                            @error('dAncho_cm')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label for="dAlto_cm" class="form-label fw-bold">
                                <i class="fas fa-arrows-alt-v me-1"></i>Alto (cm)
                            </label>
                            <input type="text" 
                                   name="dAlto_cm" 
                                   id="dAlto_cm" 
                                   class="form-control @error('dAlto_cm') is-invalid @enderror"
                                   value="{{ old('dAlto_cm') }}"
                                   oninput="validarDimensionCm(this)"
                                   onblur="formatearDimensionCm(this)"
                                   onkeydown="permitirBorrado(event)"
                                   placeholder="0.00"
                                   title="Máximo: 999.99 cm (máximo 2 decimales)"
                                   autocomplete="off">
                            @error('dAlto_cm')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- CLASE DE ENVÍO -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="vClase_envio" class="form-label fw-bold">
                                <i class="fas fa-shipping-fast me-1"></i>Clase de envío
                            </label>
                            <select name="vClase_envio" id="vClase_envio" 
                                    class="form-select @error('vClase_envio') is-invalid @enderror">
                                <option value="">Seleccionar clase de envío</option>
                                <option value="estandar" {{ old('vClase_envio') == 'estandar' ? 'selected' : '' }}>Estándar</option>
                                <option value="express" {{ old('vClase_envio') == 'express' ? 'selected' : '' }}>Express</option>
                                <option value="fragil" {{ old('vClase_envio') == 'fragil' ? 'selected' : '' }}>Frágil</option>
                                <option value="grandes_dimensiones" {{ old('vClase_envio') == 'grandes_dimensiones' ? 'selected' : '' }}>Grandes dimensiones</option>
                            </select>
                            @error('vClase_envio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Determina el tipo de envío para este producto</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CATEGORÍA, MARCA E IMPUESTO -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-tags me-2"></i>Categoría, Marca e Impuesto</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
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
                    
                    <div class="col-md-4">
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

                    <!-- SECCIÓN DE IMPUESTO (SELECTOR ÚNICO) -->
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="id_impuesto" class="form-label fw-bold">
                                <i class="fas fa-file-invoice-dollar me-1"></i>Impuesto Aplicable
                            </label>
                            @if(isset($impuestos) && $impuestos->count() > 0)
                                <select name="id_impuesto" id="id_impuesto" 
                                        class="form-select @error('id_impuesto') is-invalid @enderror"
                                        onchange="actualizarPrecioFinal()">
                                    <option value="">-- Sin impuesto --</option>
                                    @foreach($impuestos as $impuesto)
                                        <option value="{{ $impuesto->id_impuesto }}" 
                                            data-porcentaje="{{ $impuesto->dPorcentaje }}"
                                            data-tipo="{{ $impuesto->eTipo }}"
                                            {{ old('id_impuesto') == $impuesto->id_impuesto ? 'selected' : '' }}>
                                            {{ $impuesto->vNombre }} ({{ $impuesto->eTipo }} - {{ number_format($impuesto->dPorcentaje, 2) }}%)
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_impuesto')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted mt-2">
                                    Selecciona el impuesto que aplica a este producto (opcional)
                                </small>
                            @else
                                <div class="alert alert-warning mb-0">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    No hay impuestos disponibles. 
                                    <button type="button" class="btn btn-link p-0 ms-1" onclick="activarTabImpuestos()">
                                        Crear impuestos
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- IMAGEN PRINCIPAL, VIDEO E IMÁGENES ADICIONALES DEL PRODUCTO -->
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="fas fa-images me-2"></i>Multimedia del Producto Principal</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- IMAGEN PRINCIPAL -->
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="imagen_principal" class="form-label fw-bold">
                                <i class="fas fa-star text-warning me-1"></i>Imagen Principal <span class="text-danger">*</span>
                            </label>
                            <input type="file" name="imagen_principal" id="imagen_principal" 
                                   class="form-control @error('imagen_principal') is-invalid @enderror" 
                                   accept="image/jpeg,image/jpg,image/png"
                                   required
                                   onchange="previewImagenPrincipal(this)">
                            @error('imagen_principal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Esta será la imagen principal del producto (portada). Formatos: JPG, JPEG, PNG. Máximo 5MB.
                            </small>
                            
                            <!-- Preview de imagen principal -->
                            <div id="preview_principal_container" class="mt-2" style="display: none;">
                                <div class="border rounded p-2 text-center bg-light">
                                    <img id="preview_principal_img" src="#" 
                                         class="img-thumbnail" 
                                         style="max-width: 200px; max-height: 200px; object-fit: contain;"
                                         alt="Preview imagen principal">
                                    <div class="mt-2">
                                        <small class="text-muted d-block">Imagen principal (portada)</small>
                                        <button type="button" class="btn btn-sm btn-outline-danger mt-1" onclick="cancelarImagenPrincipal()">
                                            <i class="fas fa-times me-1"></i>Quitar imagen
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- VIDEO DEL PRODUCTO (OPCIONAL) -->
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="video_producto" class="form-label fw-bold">
                                <i class="fas fa-video text-danger me-1"></i>Video del Producto (Opcional)
                            </label>
                            <input type="file" name="video_producto" id="video_producto" 
                                   class="form-control @error('video_producto') is-invalid @enderror" 
                                   accept="video/mp4,video/webm,video/ogg,video/avi,video/mov,video/mkv"
                                   onchange="previewVideo(this)">
                            @error('video_producto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Formatos: MP4, WebM, OGG, AVI, MOV, MKV. Máximo 50MB.
                            </small>
                            
                            <!-- Preview de video -->
                            <div id="preview_video_container" class="mt-2" style="display: none;">
                                <div class="border rounded p-2 text-center bg-light">
                                    <video id="preview_video" controls style="max-width: 100%; max-height: 150px;">
                                        <source src="#" type="video/mp4">
                                        Tu navegador no soporta el elemento de video.
                                    </video>
                                    <div class="mt-2">
                                        <small class="text-muted d-block">Video seleccionado</small>
                                        <button type="button" class="btn btn-sm btn-outline-danger mt-1" onclick="cancelarVideo()">
                                            <i class="fas fa-times me-1"></i>Quitar video
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- GIF DEL PRODUCTO (OPCIONAL) -->
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="gif_producto" class="form-label fw-bold">
                                <i class="fas fa-file-image text-success me-1"></i>GIF Animado (Opcional)
                            </label>
                            <input type="file" name="gif_producto" id="gif_producto" 
                                   class="form-control @error('gif_producto') is-invalid @enderror" 
                                   accept="image/gif"
                                   onchange="previewGif(this)">
                            @error('gif_producto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Formatos: GIF. Máximo 10MB. Animación del producto.
                            </small>
                            
                            <!-- Preview de GIF -->
                            <div id="preview_gif_container" class="mt-2" style="display: none;">
                                <div class="border rounded p-2 text-center bg-light">
                                    <img id="preview_gif" src="#" 
                                         class="img-thumbnail" 
                                         style="max-width: 200px; max-height: 200px; object-fit: contain;"
                                         alt="Preview GIF">
                                    <div class="mt-2">
                                        <small class="text-muted d-block">GIF seleccionado</small>
                                        <button type="button" class="btn btn-sm btn-outline-danger mt-1" onclick="cancelarGif()">
                                            <i class="fas fa-times me-1"></i>Quitar GIF
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- IMÁGENES ADICIONALES DEL PRODUCTO -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="form-group mb-3">
                            <label for="imagenes" class="form-label fw-bold">
                                <i class="fas fa-images me-1"></i>Imágenes Adicionales del Producto (Máximo 7)
                            </label>
                            <input type="file" name="imagenes[]" id="imagenes" 
                                   class="form-control @error('imagenes') is-invalid @enderror" 
                                   multiple accept="image/jpeg,image/jpg,image/png,image/webp"
                                   onchange="handleImageSelection(event)">
                            @error('imagenes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Formatos: JPG, JPEG, PNG, WEBP. Máximo 5MB por imagen.
                                Puedes seleccionar hasta 7 imágenes adicionales.
                            </small>
                            <div class="mt-2">
                                <span class="badge bg-info" id="selected-images-count">0 archivos</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Contador de imágenes del producto -->
                <div class="alert alert-info py-2 mt-2">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <i class="fas fa-camera me-1"></i>
                            <strong>Total de imágenes del producto:</strong> 
                            <span id="total-imagenes">0</span> de 9 (1 principal + 1 GIF + 7 adicionales)
                        </div>
                        <div class="col-md-6 text-end">
                            <span class="badge bg-primary me-2" id="principal-count">Principal: 0</span>
                            <span class="badge bg-success me-2" id="gif-count">GIF: 0</span>
                            <span class="badge bg-secondary" id="adicionales-count">Adicionales: 0</span>
                        </div>
                    </div>
                </div>
                
                <!-- Galería de imágenes adicionales seleccionadas -->
                <div class="mt-3">
                    <h6 class="fw-bold mb-2"><i class="fas fa-images me-2"></i>Imágenes adicionales seleccionadas:</h6>
                    <div id="selected-images-container" class="row g-2"></div>
                    <div class="alert alert-warning py-2" id="no-imagenes-msg">
                        <i class="fas fa-info-circle me-1"></i>
                        <small>No hay imágenes adicionales seleccionadas</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- DESCRIPCIÓN Y ETIQUETAS -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-align-left me-2"></i>Descripción y Etiquetas</h5>
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
                                      rows="3">{{ old('tDescripcion_corta') }}</textarea>
                            @error('tDescripcion_corta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Etiquetas (Opcional)</label>
                            <div class="row">
                                @if(isset($etiquetas) && $etiquetas->count() > 0)
                                    @foreach ($etiquetas as $etiqueta)
                                        <div class="col-md-6 col-6 mb-2">
                                            <div class="form-check">
                                                <input type="checkbox" 
                                                       name="etiquetas[]" 
                                                       value="{{ $etiqueta->id_etiqueta }}" 
                                                       class="form-check-input"
                                                       {{ is_array(old('etiquetas')) && in_array($etiqueta->id_etiqueta, old('etiquetas', [])) ? 'checked' : '' }}
                                                       id="etiqueta_{{ $etiqueta->id_etiqueta }}">
                                                <label class="form-check-label" for="etiqueta_{{ $etiqueta->id_etiqueta }}">
                                                    <span class="etiqueta-badge" style="background-color: {{ $etiqueta->color ?? '#007bff' }}; color: white;">
                                                        {{ $etiqueta->vNombre }}
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="col-12">
                                        <div class="alert alert-info py-2">
                                            <i class="fas fa-info-circle me-1"></i>
                                            No hay etiquetas disponibles. 
                                            <button type="button" class="btn btn-link p-0 ms-1" onclick="activarTabEtiquetas()">
                                                Crear etiquetas
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
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
            </div>
        </div>

        <!-- SECCIÓN DE PRECIO FINAL CON IMPUESTO -->
        <div class="card mb-4 bg-info text-white">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="fas fa-calculator me-2"></i>Precio Final con Impuesto</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card bg-white text-dark">
                            <div class="card-body text-center">
                                <h6 class="text-muted">Precio base (sin impuesto)</h6>
                                <h3 class="fw-bold" id="precio-base-display">$0.00</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-white text-dark">
                            <div class="card-body text-center">
                                <h6 class="text-muted">Impuesto</h6>
                                <h3 class="fw-bold" id="total-impuestos-display">$0.00</h3>
                                <small id="porcentaje-impuestos-display">0%</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h6>Precio final (con impuesto)</h6>
                                <h2 class="fw-bold" id="precio-final-display">$0.00</h2>
                                <small>Este es el precio que verá el cliente</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ATRIBUTOS DEL PRODUCTO (SELECCIÓN Y CREACIÓN DE VALORES) -->
        <div class="card mb-4">
            <div class="card-header" style="background-color: #45c973ff; color: white;">
                <h5 class="mb-0"><i class="fas fa-tags me-2"></i>Seleccionar Atributos para Variaciones</h5>
            </div>
            <div class="card-body" style="background-color: #f8f9fa;">
                <div class="alert alert-info mb-4" style="color: #0c5460; background-color: #d1ecf1; border-color: #bee5eb;">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong style="color: #0c5460;">Instrucciones:</strong> 
                    <span style="color: #0c5460;">Marca los atributos que deseas activar y selecciona los valores correspondientes. También puedes crear nuevos valores para atributos existentes haciendo clic en "Agregar Valor".</span>
                </div>
                
                @if(isset($atributos) && $atributos->count() > 0)
                    <div class="row">
                        @foreach($atributos as $atributo)
                        <div class="col-md-6 mb-4">
                            <div class="card border h-100">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <div class="form-check">
                                        <input type="checkbox" 
                                               class="form-check-input atributo-activo-checkbox" 
                                               id="atributo-activo-{{ $atributo->id_atributo }}"
                                               data-atributo-id="{{ $atributo->id_atributo }}"
                                               data-atributo-nombre="{{ $atributo->vNombre }}">
                                        <label class="form-check-label fw-bold" for="atributo-activo-{{ $atributo->id_atributo }}" style="color: #495057;">
                                            {{ $atributo->vNombre }}
                                            <span class="badge bg-secondary ms-2">{{ $atributo->valoresActivos->count() }} valores</span>
                                        </label>
                                    </div>
                                    <div>
                                        <span class="badge bg-warning text-dark atributo-estado-badge" id="estado-{{ $atributo->id_atributo }}" style="display: none;">
                                            <i class="fas fa-check-circle me-1"></i>Activo
                                        </span>
                                        <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="mostrarFormularioValor({{ $atributo->id_atributo }}, '{{ $atributo->vNombre }}')">
                                            <i class="fas fa-plus-circle me-1"></i>Agregar Valor
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="card-body atributo-valores-container" id="valores-container-{{ $atributo->id_atributo }}" style="display: none; background-color: white;">
                                    @if($atributo->valoresActivos->count() > 0)
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input type="checkbox" 
                                                       class="form-check-input seleccionar-todos-checkbox" 
                                                       id="seleccionar-todos-{{ $atributo->id_atributo }}"
                                                       data-atributo-id="{{ $atributo->id_atributo }}">
                                                <label class="form-check-label" for="seleccionar-todos-{{ $atributo->id_atributo }}" style="color: #495057;">
                                                    <strong>Seleccionar todos</strong>
                                                </label>
                                            </div>
                                        </div>
                                        <hr class="my-2">
                                        <div class="row">
                                            @foreach($atributo->valoresActivos as $valor)
                                            <div class="col-md-6 mb-2">
                                                <div class="form-check">
                                                    <input type="checkbox" 
                                                           name="atributos[{{ $atributo->id_atributo }}][]" 
                                                           value="{{ $valor->id_atributo_valor }}" 
                                                           class="form-check-input valor-checkbox"
                                                           id="valor-{{ $valor->id_atributo_valor }}"
                                                           data-atributo-id="{{ $atributo->id_atributo }}"
                                                           data-atributo-nombre="{{ $atributo->vNombre }}"
                                                           data-valor-nombre="{{ $valor->vValor }}"
                                                           {{ is_array(old('atributos.'.$atributo->id_atributo)) && in_array($valor->id_atributo_valor, old('atributos.'.$atributo->id_atributo, [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="valor-{{ $valor->id_atributo_valor }}" style="color: #495057;">
                                                        {{ $valor->vValor }}
                                                        @if($valor->dPrecio_extra > 0)
                                                            <span class="badge bg-success ms-1">+${{ number_format($valor->dPrecio_extra, 2) }}</span>
                                                        @endif
                                                        @if($valor->iStock > 0)
                                                            <small class="text-muted d-block">Stock: {{ $valor->iStock }}</small>
                                                        @endif
                                                    </label>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="alert alert-warning mb-0">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            Este atributo no tiene valores. 
                                            <button type="button" class="btn btn-link p-0 ms-1" onclick="mostrarFormularioValor({{ $atributo->id_atributo }}, '{{ $atributo->vNombre }}')">
                                                Crear primer valor
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <!-- Resumen de atributos seleccionados -->
                    <div class="mt-4 p-3 bg-light border rounded" id="resumen-atributos" style="display: none;">
                        <h6 class="fw-bold mb-3" style="color: #495057;"><i class="fas fa-check-circle text-success me-2"></i>Atributos activados para variaciones:</h6>
                        <div id="atributos-activos-lista" class="d-flex flex-wrap gap-3"></div>
                    </div>
                    
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-tags fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">No hay atributos disponibles</h4>
                        <p class="text-muted">Crea atributos en la pestaña "Atributos" del panel de herramientas</p>
                        <button type="button" class="btn btn-primary mt-3" onclick="activarTabAtributos()">
                            <i class="fas fa-plus-circle me-2"></i> Crear Atributo
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- VARIACIONES DEL PRODUCTO - PESTAÑAS POR VALOR -->
        <div class="card mb-4">
            <div class="card-header" style="background-color: #6f42c1; color: white;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0"><i class="fas fa-cubes me-2"></i>Variaciones del Producto</h5>
                        <small style="color: rgba(255,255,255,0.9);">Cada valor seleccionado es una pestaña - Configura cada variación individualmente</small>
                    </div>
                    <div>
                        <span class="badge bg-light text-dark me-2" id="total-atributos-activos-badge">0 atributos activos</span>
                        <span class="badge bg-warning text-dark" id="total-valores-badge">0 valores</span>
                    </div>
                </div>
            </div>
            
            <div class="card-body" style="background-color: #f8f9fa;">
                @if(isset($atributos) && $atributos->count() > 0)
                    <!-- Mensaje cuando no hay atributos activos -->
                    <div id="no-atributos-activos-message" class="text-center py-5">
                        <i class="fas fa-cubes fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">No hay atributos activos</h5>
                        <p class="text-muted">
                            Activa atributos en la sección <strong>"Seleccionar Atributos para Variaciones"</strong> 
                            marcando el checkbox del atributo y seleccionando sus valores.
                        </p>
                    </div>
                    
                    <!-- PESTAÑAS DE VALORES -->
                    <div id="valores-activos-tabs-container" style="display: none;">
                        <!-- Cabecera de pestañas - Valores -->
                        <ul class="nav nav-tabs valores-nav" id="valoresTab" role="tablist" style="background-color: white;"></ul>
                        
                        <!-- Contenido de las pestañas - Formularios de variación -->
                        <div class="tab-content p-4 border border-top-0 rounded-bottom" id="valoresTabContent" style="background-color: white;"></div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-cubes fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">No hay atributos disponibles</h5>
                        <p class="text-muted">Crea atributos primero para poder generar variaciones</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- BOTONES DE ACCIÓN -->
        <div class="d-flex gap-2 mb-4">
            <button type="submit" class="btn btn-success btn-lg px-4" id="btnSubmit">
                <i class="fas fa-save me-2"></i> Guardar Producto
            </button>
            <a href="{{ route('productos.index') }}" class="btn btn-secondary btn-lg px-4">
                <i class="fas fa-times me-2"></i> Cancelar
            </a>
        </div>
    </form>

    <!-- PANEL DE HERRAMIENTAS CON TABS -->
    <div class="card mt-4 border">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0"><i class="fas fa-tools me-2"></i>Herramientas de Gestión Rápida</h4>
                <small class="text-muted">Crea rápidamente elementos necesarios para tu producto</small>
            </div>
        </div>
        <div class="card-body p-0">
            <!-- TABS DE NAVEGACIÓN -->
            <ul class="nav nav-tabs" id="toolsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="categorias-tab" data-bs-toggle="tab" 
                            data-bs-target="#categorias-content" type="button" role="tab">
                        <i class="fas fa-tags me-1"></i>Categorías
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="marcas-tab" data-bs-toggle="tab" 
                            data-bs-target="#marcas-content" type="button" role="tab">
                        <i class="fas fa-industry me-1"></i>Marcas
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="etiquetas-tab" data-bs-toggle="tab" 
                            data-bs-target="#etiquetas-content" type="button" role="tab">
                        <i class="fas fa-tag me-1"></i>Etiquetas
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="atributos-tab" data-bs-toggle="tab" 
                            data-bs-target="#atributos-content" type="button" role="tab">
                        <i class="fas fa-list-alt me-1"></i>Atributos
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="impuestos-tab" data-bs-toggle="tab" 
                            data-bs-target="#impuestos-content" type="button" role="tab">
                        <i class="fas fa-file-invoice-dollar me-1"></i>Impuestos
                    </button>
                </li>
            </ul>
            
            <!-- CONTENIDO DE LOS TABS (FORMULARIOS RÁPIDOS) -->
            <div class="tab-content p-4">
                <!-- TAB: CATEGORÍAS -->
                <div class="tab-pane fade show active" id="categorias-content" role="tabpanel">
                    <div class="quick-form" id="quick-categoria-form">
                        <h5><i class="fas fa-tags me-2"></i>Crear Nueva Categoría</h5>
                        <p class="text-muted small mb-3">Las categorías ayudan a organizar tus productos de forma jerárquica.</p>
                        
                        <form id="categoriaQuickForm" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="vNombre_categoria" class="form-label fw-bold">Nombre de la Categoría *</label>
                                <input type="text" class="form-control" 
                                       id="vNombre_categoria" name="vNombre" 
                                       required
                                       placeholder="Ej: Tequila, Mezcal, Añejos..."
                                       oninput="quickActualizarSlug(this.value, 'vSlug_categoria')">
                                <small class="form-text text-muted">Nombre descriptivo para la categoría</small>
                            </div>

                            <div class="mb-3">
                                <label for="vSlug_categoria" class="form-label fw-bold">Slug (URL amigable) *</label>
                                <input type="text" class="form-control" 
                                       id="vSlug_categoria" name="vSlug" 
                                       required
                                       placeholder="tequila-reposado">
                                <small class="form-text text-muted">
                                    URL para la categoría (ej: tequila-reposado). Se genera automáticamente desde el nombre.
                                </small>
                            </div>

                            <div class="mb-3">
                                <label for="id_categoria_padre_quick" class="form-label fw-bold">Categoría Padre</label>
                                <select class="form-control" id="id_categoria_padre_quick" name="id_categoria_padre">
                                    <option value="">-- Seleccionar Categoría Padre (Opcional) --</option>
                                    @php
                                        function mostrarCategoriasParaQuick($categorias, $nivel = 0) {
                                            foreach($categorias as $categoria) {
                                                $prefijo = str_repeat('&nbsp;&nbsp;&nbsp;', $nivel);
                                                $icono = $nivel == 0 ? '🏠 ' : '↳ ';
                                                echo '<option value="' . $categoria->id_categoria . '">' .
                                                     $prefijo . $icono . htmlspecialchars($categoria->vNombre) . 
                                                     '</option>';
                                                
                                                if ($categoria->hijos && $categoria->hijos->count() > 0) {
                                                    mostrarCategoriasParaQuick($categoria->hijos, $nivel + 1);
                                                }
                                            }
                                        }
                                        
                                        $categoriasRaiz = $categorias->where('id_categoria_padre', null)->where('bActivo', true);
                                    @endphp
                                    
                                    @php mostrarCategoriasParaQuick($categoriasRaiz, 0); @endphp
                                </select>
                                <small class="form-text text-muted">Selecciona si esta categoría pertenece a otra</small>
                            </div>

                            <div class="mb-3">
                                <label for="tDescripcion_categoria" class="form-label fw-bold">Descripción</label>
                                <textarea class="form-control" 
                                          id="tDescripcion_categoria" name="tDescripcion" rows="3"
                                          placeholder="Describe la categoría..."></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Estado</label>
                                <div class="form-check mt-2">
                                    <input type="checkbox" class="form-check-input" 
                                           id="bActivo_categoria" name="bActivo" value="1" checked>
                                    <label class="form-check-label" for="bActivo_categoria">
                                        Categoría activa
                                    </label>
                                </div>
                                <small class="form-text text-muted">Las categorías inactivas no estarán disponibles</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Imagen de la Categoría</label>
                                
                                <!-- Preview de nueva imagen -->
                                <div class="mb-3" id="categoriaImagePreview" style="display: none;">
                                    <div class="border rounded p-3 text-center">
                                        <img id="categoriaPreviewImg" src="#" 
                                             class="img-thumbnail" 
                                             style="max-width: 150px; max-height: 150px; object-fit: cover;"
                                             alt="Preview">
                                        <div class="mt-2">
                                            <small class="text-muted d-block">Vista previa</small>
                                            <button type="button" class="btn btn-sm btn-outline-danger mt-1" onclick="cancelarImagenCategoria()">
                                                <i class="fas fa-times me-1"></i>Cancelar imagen
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="input-group">
                                    <input type="file" class="form-control" 
                                           id="vImagen_categoria" name="vImagen"
                                           accept="image/jpeg,image/jpg,image/png,image/webp"
                                           onchange="previewImagenCategoria(this)">
                                    <button type="button" class="btn btn-outline-secondary" onclick="resetearInputImagen()">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                </div>
                                <small class="form-text text-muted">
                                    Formatos: JPG, JPEG, PNG, WebP. Tamaño máximo: 2MB. La imagen es opcional.
                                </small>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-secondary" onclick="limpiarFormularioCategoria()">
                                    <i class="fas fa-undo me-1"></i> Limpiar
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Crear Categoría
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- TAB: MARCAS -->
                <div class="tab-pane fade" id="marcas-content" role="tabpanel">
                    <div class="quick-form" id="quick-marca-form">
                        <h5><i class="fas fa-industry me-2"></i>Crear Nueva Marca</h5>
                        <p class="text-muted small mb-3">Las marcas identifican al fabricante o productor del artículo.</p>
                        
                        <div class="mb-3">
                            <label for="vNombre_marca" class="form-label fw-bold">Nombre de la Marca *</label>
                            <input type="text" class="form-control" id="vNombre_marca" 
                                   placeholder="Ej: José Cuervo, Patrón, Don Julio">
                        </div>
                        
                        <div class="mb-3">
                            <label for="tDescripcion_marca" class="form-label fw-bold">Descripción (Opcional)</label>
                            <textarea class="form-control" id="tDescripcion_marca" rows="3" 
                                      placeholder="Describe la marca..."></textarea>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" onclick="limpiarFormularioMarca()">
                                <i class="fas fa-undo me-1"></i> Limpiar
                            </button>
                            <button type="button" class="btn btn-primary" onclick="crearMarca()">
                                <i class="fas fa-save me-1"></i> Crear Marca
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- TAB: ETIQUETAS -->
                <div class="tab-pane fade" id="etiquetas-content" role="tabpanel">
                    <div class="quick-form" id="quick-etiqueta-form">
                        <h5><i class="fas fa-tag me-2"></i>Crear Nueva Etiqueta</h5>
                        <p class="text-muted small mb-3">Las etiquetas son palabras clave que ayudan a clasificar productos.</p>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="vNombre_eti" class="form-label fw-bold">Nombre de la Etiqueta *</label>
                                <input type="text" class="form-control" id="vNombre_eti" 
                                       placeholder="Ej: Artesanal, Orgánico, Premium">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="color_eti" class="form-label fw-bold">Color</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" 
                                           id="color_eti" value="#007bff">
                                    <input type="text" class="form-control" 
                                           id="color_text_eti" value="#007bff" 
                                           placeholder="#007bff" maxlength="7">
                                </div>
                                <small class="text-muted">Color opcional para identificar la etiqueta</small>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="tDescripcion_eti" class="form-label fw-bold">Descripción (Opcional)</label>
                            <textarea class="form-control" id="tDescripcion_eti" rows="2" 
                                      placeholder="Descripción de la etiqueta..."></textarea>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Nota:</strong> Después de crear la etiqueta, estará disponible en la sección "Etiquetas" del formulario.
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-primary" onclick="crearEtiqueta()">
                                <i class="fas fa-save me-1"></i> Crear Etiqueta
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- TAB: ATRIBUTOS -->
                <div class="tab-pane fade" id="atributos-content" role="tabpanel">
                    <div class="quick-form" id="quick-atributo-form">
                        <h5><i class="fas fa-list-alt me-2"></i>Crear Nuevo Atributo</h5>
                        <p class="text-muted small mb-3">Los atributos son características que definen las variaciones de un producto (Tamaño, Color, Material, etc.).</p>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="vNombre_attr" class="form-label fw-bold">Nombre del Atributo *</label>
                                <input type="text" class="form-control" id="vNombre_attr" 
                                       placeholder="Ej: Tamaño, Color, Sabor, Edad"
                                       oninput="quickGenerarSlug(this.value, 'vSlug_attr')">
                                <small class="text-muted">Ejemplos: Tamaño, Color, Material, Sabor</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="vSlug_attr" class="form-label fw-bold">Slug (URL amigable)</label>
                                <input type="text" class="form-control" id="vSlug_attr" 
                                       placeholder="tamano, color, material">
                                <small class="text-muted">Se genera automáticamente desde el nombre</small>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="tDescripcion_attr" class="form-label fw-bold">Descripción (Opcional)</label>
                            <textarea class="form-control" id="tDescripcion_attr" rows="2" 
                                      placeholder="Describe el atributo..."></textarea>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Nota:</strong> Después de crear el atributo, podrás agregar valores específicos en la sección <strong>"Seleccionar Atributos para Variaciones"</strong> usando el botón "Agregar Valor" junto a cada atributo.
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-primary" onclick="crearAtributo()">
                                <i class="fas fa-save me-1"></i> Crear Atributo
                            </button>
                        </div>
                    </div>
                </div>

                <!-- TAB: IMPUESTOS -->
                <div class="tab-pane fade" id="impuestos-content" role="tabpanel">
                    <div class="quick-form" id="quick-impuesto-form">
                        <h5><i class="fas fa-file-invoice-dollar me-2"></i>Crear Nuevo Impuesto</h5>
                        <p class="text-muted small mb-3">Los impuestos se aplicarán automáticamente al precio de venta del producto.</p>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="vNombre_impuesto" class="form-label fw-bold">Nombre del Impuesto *</label>
                                <input type="text" class="form-control" id="vNombre_impuesto" 
                                       placeholder="Ej: IVA, IEPS, ISR">
                                <small class="text-muted">Nombre identificador del impuesto</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="eTipo_impuesto" class="form-label fw-bold">Tipo de Impuesto *</label>
                                <select class="form-select" id="eTipo_impuesto">
                                    <option value="">Seleccionar tipo</option>
                                    <option value="IVA">IVA (Impuesto al Valor Agregado)</option>
                                    <option value="IEPS">IEPS (Impuesto Especial)</option>
                                    <option value="ISR">ISR (Impuesto Sobre la Renta)</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="dPorcentaje_impuesto" class="form-label fw-bold">Porcentaje *</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="dPorcentaje_impuesto" 
                                           placeholder="16.00"
                                           oninput="validarPrecio(this)">
                                    <span class="input-group-text">%</span>
                                </div>
                                <small class="text-muted">Ej: 16 para IVA 16%</small>
                            </div>
                            
                            <div class="col-md-8 mb-3">
                                <label for="tDescripcion_impuesto" class="form-label fw-bold">Descripción (Opcional)</label>
                                <input type="text" class="form-control" id="tDescripcion_impuesto" 
                                       placeholder="Descripción del impuesto">
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="bActivo_impuesto" checked>
                                <label class="form-check-label" for="bActivo_impuesto">
                                    Impuesto activo
                                </label>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Nota:</strong> Después de crear el impuesto, estará disponible en el selector de impuestos del producto y se calculará automáticamente el precio final.
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-primary" onclick="crearImpuesto()">
                                <i class="fas fa-save me-1"></i> Crear Impuesto
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PARA CREAR VALOR DE ATRIBUTO -->
<div class="modal fade" id="crearValorModal" tabindex="-1" aria-labelledby="crearValorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="crearValorModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Crear Nuevo Valor para <span id="atributoNombreModal"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="valorQuickForm">
                    @csrf
                    <input type="hidden" id="valor_atributo_id" name="atributo_id">
                    
                    <div class="mb-3">
                        <label for="vValor_modal" class="form-label fw-bold">Valor <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="vValor_modal" name="vValor" required
                               placeholder="Ej: 750ml, Rojo, Joven, 6 meses"
                               oninput="generarSlugValor(this.value)">
                        <small class="form-text text-muted">El valor que aparecerá en las opciones del producto</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="vSlug_valor_modal" class="form-label fw-bold">Slug (URL amigable)</label>
                        <input type="text" class="form-control" id="vSlug_valor_modal" name="vSlug"
                               placeholder="Se genera automáticamente">
                        <small class="form-text text-muted">Versión para URL del valor</small>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="bActivo_valor_modal" name="bActivo" value="1" checked>
                            <label class="form-check-label" for="bActivo_valor_modal">Activo</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary" onclick="guardarValorAtributo()">
                    <i class="fas fa-save me-1"></i>Guardar Valor
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.etiqueta-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 15px;
    font-size: 13px;
    margin: 2px;
}

.bg-purple {
    background-color: #6f42c1 !important;
}

.valores-nav {
    border-bottom: 2px solid #dee2e6;
    padding-left: 10px;
    background: white;
    border-radius: 8px 8px 0 0;
    flex-wrap: wrap;
}

.valores-nav .nav-item {
    margin-right: 2px;
    margin-bottom: 5px;
}

.valores-nav .nav-link {
    color: #495057;
    border: none;
    border-bottom: 3px solid transparent;
    margin-bottom: -2px;
    padding: 10px 15px;
    font-weight: 500;
    transition: all 0.3s ease;
    background: transparent;
    position: relative;
    border-radius: 8px 8px 0 0;
}

.valores-nav .nav-link:hover {
    color: #007bff;
    border-bottom-color: #adb5bd;
    background: rgba(0,123,255,0.05);
}

.valores-nav .nav-link.active {
    color: #007bff;
    background: white;
    border-bottom: 3px solid #007bff;
    font-weight: 600;
}

.valores-nav .nav-link .badge {
    margin-left: 8px;
    background-color: #6c757d;
    color: white;
}

.valores-nav .nav-link.active .badge {
    background-color: #007bff !important;
    color: white;
}

.valor-tab-content {
    background: white;
    border-radius: 0 0 8px 8px;
}

.variacion-form-container {
    padding: 20px;
    background: white;
}

.variacion-header-info {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.variacion-header-info h6 {
    margin-bottom: 5px;
    font-size: 1.1rem;
}

.quick-form {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    border: 1px solid #dee2e6;
    margin-bottom: 15px;
}

.quick-form h5 {
    color: #2E8B57;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #2E8B57;
}

.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #dc3545;
}

.variacion-precio-descuento.is-invalid {
    border-color: #dc3545;
    padding-right: calc(1.5em + 0.75rem);
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.image-preview-card {
    transition: transform 0.2s;
    border: 2px solid transparent;
}

.image-preview-card:hover {
    transform: scale(1.02);
    border-color: #007bff;
}

.image-preview-card.principal {
    border-color: #ffc107;
    background-color: #fff3cd;
}

.principal-badge {
    position: absolute;
    top: 5px;
    left: 5px;
    background-color: #ffc107;
    color: #000;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
    z-index: 2;
}

.video-preview, .gif-preview {
    max-width: 100%;
    max-height: 150px;
    border-radius: 8px;
}

.selected-image-item {
    position: relative;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 8px;
    background: #fff;
}

.selected-image-item .remove-btn {
    position: absolute;
    top: 5px;
    right: 5px;
    width: 24px;
    height: 24px;
    padding: 0;
    border-radius: 50%;
    z-index: 10;
}

@media (max-width: 768px) {
    .valores-nav .nav-link {
        padding: 8px 12px;
        font-size: 0.9rem;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Variables globales
let selectedImages = [];
let imageCounter = 0;
let atributosActivos = {};
let imagenPrincipalFile = null;
let videoFile = null;
let gifFile = null;
let variacionCounter = 0;
let valorModal = null;

// Variable para almacenar la imagen de categoría seleccionada
let categoriaImagenFile = null;

// Inicializar modal cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    valorModal = new bootstrap.Modal(document.getElementById('crearValorModal'));
});

// ============ FUNCIÓN DE CÁLCULO DE IMPUESTO Y PRECIO FINAL ============

function actualizarPrecioFinal() {
    const precioVentaInput = document.getElementById('dPrecio_venta');
    const impuestoSelect = document.getElementById('id_impuesto');
    
    if (!precioVentaInput) return;
    
    const precioVenta = parseFloat(precioVentaInput.value) || 0;
    
    // Mostrar precio base
    document.getElementById('precio-base-display').textContent = '$' + precioVenta.toFixed(2);
    
    // Obtener impuesto seleccionado
    let totalImpuestos = 0;
    let porcentaje = 0;
    let nombreImpuesto = '';
    let tipoImpuesto = '';
    
    if (impuestoSelect && impuestoSelect.value) {
        const selectedOption = impuestoSelect.options[impuestoSelect.selectedIndex];
        porcentaje = parseFloat(selectedOption.dataset.porcentaje) || 0;
        tipoImpuesto = selectedOption.dataset.tipo || '';
        nombreImpuesto = selectedOption.text.split('(')[0].trim();
        
        totalImpuestos = precioVenta * (porcentaje / 100);
    }
    
    const precioFinal = precioVenta + totalImpuestos;
    
    // Mostrar resultados
    document.getElementById('total-impuestos-display').textContent = '$' + totalImpuestos.toFixed(2);
    document.getElementById('precio-final-display').textContent = '$' + precioFinal.toFixed(2);
    
    if (porcentaje > 0) {
        document.getElementById('porcentaje-impuestos-display').textContent = porcentaje.toFixed(2) + '%';
    } else {
        document.getElementById('porcentaje-impuestos-display').textContent = '0%';
    }
}

// ============ FUNCIONES DE VALIDACIÓN ============

function validarPrecioDescuentoProductoInstantaneo(input) {
    const tieneDescuento = document.getElementById('bTiene_descuento');
    if (!tieneDescuento || !tieneDescuento.checked) return true;
    
    const precioVenta = parseFloat(document.getElementById('dPrecio_venta').value) || 0;
    const precioDescuento = parseFloat(input.value) || 0;
    const errorDiv = document.getElementById('error-precio-descuento');
    
    if (precioDescuento >= precioVenta && precioDescuento > 0 && input.value !== '') {
        input.classList.add('is-invalid');
        errorDiv.style.display = 'block';
        errorDiv.textContent = 'El precio de descuento debe ser menor que el precio de venta';
        return false;
    } else {
        input.classList.remove('is-invalid');
        errorDiv.style.display = 'none';
        return true;
    }
}

function validarPrecioDescuentoProducto() {
    const tieneDescuento = document.getElementById('bTiene_descuento');
    if (!tieneDescuento || !tieneDescuento.checked) return true;
    
    const precioVenta = parseFloat(document.getElementById('dPrecio_venta').value) || 0;
    const precioDescuento = parseFloat(document.getElementById('dPrecio_descuento').value) || 0;
    const input = document.getElementById('dPrecio_descuento');
    const errorDiv = document.getElementById('error-precio-descuento');
    
    if (precioDescuento >= precioVenta && precioDescuento > 0) {
        input.classList.add('is-invalid');
        errorDiv.style.display = 'block';
        errorDiv.textContent = 'El precio de descuento debe ser menor que el precio de venta';
        return false;
    } else {
        input.classList.remove('is-invalid');
        errorDiv.style.display = 'none';
        return true;
    }
}

function validarDimension(input, maxDecimales, maxValor) {
    let value = input.value;
    const cursorPos = input.selectionStart;
    
    if (value === '') {
        input.classList.remove('is-invalid');
        return;
    }
    
    const lastCharWasDot = value.endsWith('.');
    
    value = value.replace(/[^0-9.]/g, '');
    
    const puntos = value.split('.').length - 1;
    if (puntos > 1) {
        const partes = value.split('.');
        value = partes[0] + '.' + partes.slice(1).join('');
    }
    
    if (value.startsWith('.')) {
        value = '0' + value;
    }
    
    const partesNumero = value.split('.');
    let parteEntera = partesNumero[0];
    let parteDecimal = partesNumero[1] || '';
    
    if (parteEntera.length > 3) {
        parteEntera = parteEntera.substring(0, 3);
    }
    
    if (parteDecimal.length > maxDecimales) {
        parteDecimal = parteDecimal.substring(0, maxDecimales);
    }
    
    value = parteEntera;
    if (parteDecimal || lastCharWasDot) {
        value += '.' + parteDecimal;
    }
    
    if (value && !value.endsWith('.') && !isNaN(parseFloat(value))) {
        let numValue = parseFloat(value);
        if (numValue > maxValor) {
            value = maxValor.toString();
        }
    }
    
    if (input.value !== value) {
        const oldLength = input.value.length;
        input.value = value;
        const newLength = value.length;
        
        let newCursorPos = cursorPos;
        if (oldLength > newLength) {
            newCursorPos = Math.min(cursorPos, newLength);
        } else if (oldLength < newLength) {
            newCursorPos = cursorPos + (newLength - oldLength);
        }
        
        setTimeout(() => {
            input.setSelectionRange(newCursorPos, newCursorPos);
        }, 0);
    }
    
    input.classList.remove('is-invalid');
}

function validarPeso(input) {
    validarDimension(input, 3, 999.999);
}

function validarDimensionCm(input) {
    validarDimension(input, 2, 999.99);
}

function formatearPeso(input) {
    let value = input.value;
    
    if (!value || value === '.' || value.endsWith('.')) {
        return;
    }
    
    let num = parseFloat(value);
    if (isNaN(num)) {
        input.value = '';
        return;
    }
    
    if (num > 999.999) {
        num = 999.999;
    }
    
    input.value = num.toString();
}

function formatearDimensionCm(input) {
    let value = input.value;
    
    if (!value || value === '.' || value.endsWith('.')) {
        return;
    }
    
    let num = parseFloat(value);
    if (isNaN(num)) {
        input.value = '';
        return;
    }
    
    if (num > 999.99) {
        num = 999.99;
    }
    
    input.value = num.toString();
}

function permitirBorrado(e) {
    const teclasPermitidas = [
        'Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown',
        'Home', 'End', 'Tab', 'Enter'
    ];
    
    if (e.ctrlKey || e.metaKey) {
        return true;
    }
    
    if (teclasPermitidas.includes(e.key)) {
        return true;
    }
    
    if (e.key >= '0' && e.key <= '9') {
        return true;
    }
    
    if (e.key === '.') {
        if (e.target.value.includes('.')) {
            e.preventDefault();
            return false;
        }
        return true;
    }
    
    e.preventDefault();
    return false;
}

function validarSKU(input) {
    input.value = input.value.replace(/[^A-Za-z0-9-]/g, '');
    if (input.value.length > 50) {
        input.value = input.value.substring(0, 50);
    }
    input.value = input.value.toUpperCase();
    input.classList.remove('is-invalid');
}

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
    
    if (input.id === 'dPrecio_descuento') {
        validarPrecioDescuentoProductoInstantaneo(input);
    }
    
    // Actualizar precio final cuando cambia el precio de venta
    if (input.id === 'dPrecio_venta') {
        actualizarPrecioFinal();
    }
}

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

function toggleDescuentoFields() {
    const descuentoFields = document.getElementById('descuentoFields');
    const tieneDescuento = document.getElementById('bTiene_descuento').checked;
    const precioDescuento = document.getElementById('dPrecio_descuento');
    const fechaInicio = document.getElementById('dFecha_inicio_descuento');
    const fechaFin = document.getElementById('dFecha_fin_descuento');
    
    if (tieneDescuento) {
        descuentoFields.style.display = 'block';
        precioDescuento.required = true;
        fechaInicio.required = true;
        fechaFin.required = true;
        
        setTimeout(() => validarPrecioDescuentoProducto(), 100);
    } else {
        descuentoFields.style.display = 'none';
        precioDescuento.required = false;
        fechaInicio.required = false;
        fechaFin.required = false;
        
        precioDescuento.classList.remove('is-invalid');
        document.getElementById('error-precio-descuento').style.display = 'none';
    }
}

// ============ FUNCIONES DE IMÁGENES Y VIDEO DEL PRODUCTO PRINCIPAL ============

function previewImagenPrincipal(input) {
    const previewContainer = document.getElementById('preview_principal_container');
    const previewImg = document.getElementById('preview_principal_img');
    
    if (input.files && input.files[0]) {
        // Validar formato
        const file = input.files[0];
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        
        if (!validTypes.includes(file.type)) {
            Swal.fire({
                icon: 'error',
                title: 'Formato no válido',
                text: 'La imagen principal solo acepta formatos JPG, JPEG y PNG'
            });
            input.value = '';
            return;
        }
        
        imagenPrincipalFile = file;
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewContainer.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        previewContainer.style.display = 'none';
        imagenPrincipalFile = null;
    }
    
    actualizarContadorImagenes();
}

function cancelarImagenPrincipal() {
    const input = document.getElementById('imagen_principal');
    const previewContainer = document.getElementById('preview_principal_container');
    
    input.value = '';
    previewContainer.style.display = 'none';
    imagenPrincipalFile = null;
    actualizarContadorImagenes();
}

function previewVideo(input) {
    const previewContainer = document.getElementById('preview_video_container');
    const previewVideo = document.getElementById('preview_video');
    const source = previewVideo.querySelector('source');
    
    if (input.files && input.files[0]) {
        videoFile = input.files[0];
        const url = URL.createObjectURL(input.files[0]);
        source.src = url;
        source.type = input.files[0].type;
        previewVideo.load();
        previewContainer.style.display = 'block';
    } else {
        previewContainer.style.display = 'none';
        videoFile = null;
    }
}

function cancelarVideo() {
    const input = document.getElementById('video_producto');
    const previewContainer = document.getElementById('preview_video_container');
    const previewVideo = document.getElementById('preview_video');
    const source = previewVideo.querySelector('source');
    
    input.value = '';
    previewContainer.style.display = 'none';
    source.src = '#';
    videoFile = null;
}

function previewGif(input) {
    const previewContainer = document.getElementById('preview_gif_container');
    const previewImg = document.getElementById('preview_gif');
    
    if (input.files && input.files[0]) {
        // Validar formato
        const file = input.files[0];
        
        if (file.type !== 'image/gif') {
            Swal.fire({
                icon: 'error',
                title: 'Formato no válido',
                text: 'El campo GIF solo acepta archivos con formato GIF'
            });
            input.value = '';
            return;
        }
        
        gifFile = file;
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewContainer.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        previewContainer.style.display = 'none';
        gifFile = null;
    }
    
    actualizarContadorImagenes();
}

function cancelarGif() {
    const input = document.getElementById('gif_producto');
    const previewContainer = document.getElementById('preview_gif_container');
    
    input.value = '';
    previewContainer.style.display = 'none';
    gifFile = null;
    actualizarContadorImagenes();
}

// ============ FUNCIONES PARA IMÁGENES DE CATEGORÍA ============

function previewImagenCategoria(input) {
    const preview = document.getElementById('categoriaImagePreview');
    const previewImg = document.getElementById('categoriaPreviewImg');
    
    if (input.files && input.files.length > 0) {
        const file = input.files[0];
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        
        if (!validTypes.includes(file.type)) {
            Swal.fire({
                icon: 'error',
                title: 'Formato no válido',
                text: 'Solo se permiten imágenes JPG, JPEG, PNG o WebP'
            });
            input.value = '';
            return;
        }
        
        categoriaImagenFile = file;
        
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        }
        
        reader.readAsDataURL(file);
    }
}

function cancelarImagenCategoria() {
    const preview = document.getElementById('categoriaImagePreview');
    const fileInput = document.getElementById('vImagen_categoria');
    
    preview.style.display = 'none';
    fileInput.value = '';
    categoriaImagenFile = null;
}

function resetearInputImagen() {
    const fileInput = document.getElementById('vImagen_categoria');
    fileInput.value = '';
    document.getElementById('categoriaImagePreview').style.display = 'none';
    categoriaImagenFile = null;
}

function limpiarFormularioCategoria() {
    document.getElementById('vNombre_categoria').value = '';
    document.getElementById('vSlug_categoria').value = '';
    document.getElementById('id_categoria_padre_quick').value = '';
    document.getElementById('tDescripcion_categoria').value = '';
    document.getElementById('bActivo_categoria').checked = true;
    document.getElementById('vImagen_categoria').value = '';
    document.getElementById('categoriaImagePreview').style.display = 'none';
    categoriaImagenFile = null;
}

function limpiarFormularioMarca() {
    document.getElementById('vNombre_marca').value = '';
    document.getElementById('tDescripcion_marca').value = '';
}

// ============ FUNCIÓN PARA LIMPIAR FORMULARIO DE ETIQUETAS ============
function limpiarFormularioEtiqueta() {
    document.getElementById('vNombre_eti').value = '';
    document.getElementById('tDescripcion_eti').value = '';
    document.getElementById('color_eti').value = '#007bff';
    document.getElementById('color_text_eti').value = '#007bff';
}

// ============ FUNCIONES PARA LIMPIAR FORMULARIO DE IMPUESTOS ============
function limpiarFormularioImpuesto() {
    document.getElementById('vNombre_impuesto').value = '';
    document.getElementById('eTipo_impuesto').value = '';
    document.getElementById('dPorcentaje_impuesto').value = '';
    document.getElementById('tDescripcion_impuesto').value = '';
    document.getElementById('bActivo_impuesto').checked = true;
}

// ============ FUNCIONES PARA VALORES DE ATRIBUTOS ============

function mostrarFormularioValor(atributoId, atributoNombre) {
    document.getElementById('atributoNombreModal').textContent = atributoNombre;
    document.getElementById('valor_atributo_id').value = atributoId;
    document.getElementById('vValor_modal').value = '';
    document.getElementById('vSlug_valor_modal').value = '';
    document.getElementById('bActivo_valor_modal').checked = true;
    
    valorModal.show();
}

function generarSlugValor(valor) {
    if (!valor) {
        document.getElementById('vSlug_valor_modal').value = '';
        return;
    }
    
    let slug = valor.toLowerCase();
    slug = slug.replace(/á/gi, 'a');
    slug = slug.replace(/é/gi, 'e');
    slug = slug.replace(/í/gi, 'i');
    slug = slug.replace(/ó/gi, 'o');
    slug = slug.replace(/ú/gi, 'u');
    slug = slug.replace(/ñ/gi, 'n');
    slug = slug.replace(/[^a-z0-9\s]/g, '');
    slug = slug.replace(/\s+/g, '-');
    slug = slug.replace(/-+/g, '-');
    slug = slug.replace(/^-+/, '').replace(/-+$/, '');
    
    document.getElementById('vSlug_valor_modal').value = slug;
}

function guardarValorAtributo() {
    const atributoId = document.getElementById('valor_atributo_id').value;
    const vValor = document.getElementById('vValor_modal').value.trim();
    const vSlug = document.getElementById('vSlug_valor_modal').value.trim();
    const bActivo = document.getElementById('bActivo_valor_modal').checked ? 1 : 0;
    
    if (!vValor) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'El valor es obligatorio'
        });
        return;
    }
    
    Swal.fire({
        title: 'Creando valor...',
        text: 'Por favor espera',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });
    
    fetch(`/atributos/${atributoId}/valores-quick`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            vValor: vValor,
            vSlug: vSlug,
            bActivo: bActivo
        })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw err; });
        }
        return response.json();
    })
    .then(data => {
        Swal.close();
        
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: data.message || 'Valor creado exitosamente',
                timer: 2000,
                showConfirmButton: false
            });
            
            valorModal.hide();
            
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Error al crear el valor'
            });
        }
    })
    .catch(error => {
        Swal.close();
        
        let errorMessage = 'Error en la solicitud';
        if (error.errors) {
            errorMessage = Object.values(error.errors).flat().join(', ');
        } else if (error.message) {
            errorMessage = error.message;
        }
        
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: errorMessage
        });
    });
}

function handleImageSelection(event) {
    const files = event.target.files;
    const maxFiles = 7;
    const currentCount = selectedImages.length;
    const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    
    // Verificar límite total
    if (currentCount + files.length > maxFiles) {
        Swal.fire({
            icon: 'warning',
            title: 'Límite de imágenes',
            text: `Solo puedes seleccionar máximo ${maxFiles} imágenes adicionales. Ya tienes ${currentCount} seleccionadas.`
        });
        event.target.value = '';
        return;
    }
    
    // Procesar cada archivo
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        
        // Validar tipo de archivo
        if (!validTypes.includes(file.type)) {
            Swal.fire({
                icon: 'warning',
                title: 'Formato no válido',
                text: `El archivo "${file.name}" no es un formato válido. Formatos aceptados: JPG, JPEG, PNG, WEBP.`
            });
            continue;
        }
        
        // Validar tamaño (5MB)
        if (file.size > 5 * 1024 * 1024) {
            Swal.fire({
                icon: 'warning',
                title: 'Archivo demasiado grande',
                text: `La imagen "${file.name}" excede el límite de 5MB.`
            });
            continue;
        }
        
        // Evitar duplicados
        if (!isImageDuplicate(file)) {
            const imageId = 'img_' + Date.now() + '_' + imageCounter++;
            const preview = URL.createObjectURL(file);
            
            selectedImages.push({
                id: imageId,
                file: file,
                preview: preview,
                name: file.name,
                size: file.size
            });
        }
    }
    
    // Actualizar contador en el badge
    document.getElementById('selected-images-count').textContent = selectedImages.length + ' archivos';
    
    // Renderizar las imágenes seleccionadas
    renderSelectedImages();
    
    // Limpiar el input para permitir seleccionar los mismos archivos si es necesario
    event.target.value = '';
    
    // Actualizar contador total
    actualizarContadorImagenes();
}

function isImageDuplicate(newFile) {
    return selectedImages.some(img => 
        img.file.name === newFile.name && 
        img.file.size === newFile.size && 
        img.file.lastModified === newFile.lastModified
    );
}

function removeSelectedImage(imageId) {
    const image = selectedImages.find(img => img.id === imageId);
    if (image && image.preview) {
        URL.revokeObjectURL(image.preview);
    }
    selectedImages = selectedImages.filter(img => img.id !== imageId);
    
    // Actualizar contador en el badge
    document.getElementById('selected-images-count').textContent = selectedImages.length + ' archivos';
    
    renderSelectedImages();
    actualizarContadorImagenes();
}

function renderSelectedImages() {
    const container = document.getElementById('selected-images-container');
    const noMsg = document.getElementById('no-imagenes-msg');
    
    if (!container) return;
    
    container.innerHTML = '';
    
    if (selectedImages.length === 0) {
        if (noMsg) noMsg.style.display = 'block';
        return;
    }
    
    if (noMsg) noMsg.style.display = 'none';
    
    selectedImages.forEach((image, index) => {
        const col = document.createElement('div');
        col.className = 'col-6 col-md-3 mb-3';
        
        const card = document.createElement('div');
        card.className = 'card border image-preview-card position-relative';
        
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'btn btn-danger btn-sm position-absolute top-0 end-0 m-1 remove-btn';
        btn.style.cssText = 'width: 28px; height: 28px; padding: 0; border-radius: 50%; z-index: 10;';
        btn.onclick = function(e) { 
            e.preventDefault();
            removeSelectedImage(image.id); 
        };
        
        const btnIcon = document.createElement('i');
        btnIcon.className = 'fas fa-times';
        btn.appendChild(btnIcon);
        
        const img = document.createElement('img');
        img.src = image.preview;
        img.className = 'card-img-top';
        img.style.cssText = 'height: 120px; object-fit: contain; background: #f8f9fa; padding: 8px;';
        img.alt = 'Imagen ' + (index + 1);
        
        const cardBody = document.createElement('div');
        cardBody.className = 'card-body p-2 text-center';
        
        const small1 = document.createElement('small');
        small1.className = 'text-muted d-block';
        small1.style.cssText = 'font-size: 11px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;';
        small1.textContent = image.file.name.length > 20 ? image.file.name.substring(0, 20) + '...' : image.file.name;
        
        const small2 = document.createElement('small');
        small2.className = 'text-muted d-block';
        small2.textContent = (image.file.size / 1024).toFixed(2) + ' KB';
        
        cardBody.appendChild(small1);
        cardBody.appendChild(small2);
        
        card.appendChild(btn);
        card.appendChild(img);
        card.appendChild(cardBody);
        
        col.appendChild(card);
        container.appendChild(col);
    });
}

function actualizarContadorImagenes() {
    const total = (imagenPrincipalFile ? 1 : 0) + (gifFile ? 1 : 0) + selectedImages.length;
    document.getElementById('total-imagenes').textContent = total;
    document.getElementById('principal-count').textContent = `Principal: ${imagenPrincipalFile ? 1 : 0}`;
    document.getElementById('gif-count').textContent = `GIF: ${gifFile ? 1 : 0}`;
    document.getElementById('adicionales-count').textContent = `Adicionales: ${selectedImages.length}`;
}

// ============ FUNCIONES DE ATRIBUTOS Y VARIACIONES ============

document.querySelectorAll('.atributo-activo-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const atributoId = this.dataset.atributoId;
        const atributoNombre = this.dataset.atributoNombre;
        const valoresContainer = document.getElementById(`valores-container-${atributoId}`);
        const estadoBadge = document.getElementById(`estado-${atributoId}`);
        
        if (this.checked) {
            valoresContainer.style.display = 'block';
            estadoBadge.style.display = 'inline-block';
            
            if (!atributosActivos[atributoId]) {
                atributosActivos[atributoId] = {
                    id: atributoId,
                    nombre: atributoNombre,
                    valores: {}
                };
            }
        } else {
            valoresContainer.style.display = 'none';
            estadoBadge.style.display = 'none';
            
            const checkboxes = valoresContainer.querySelectorAll('.valor-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = false;
            });
            
            delete atributosActivos[atributoId];
            
            const seleccionarTodos = document.getElementById(`seleccionar-todos-${atributoId}`);
            if (seleccionarTodos) {
                seleccionarTodos.checked = false;
            }
        }
        
        actualizarPestanasValores();
        actualizarResumenAtributos();
    });
});

document.querySelectorAll('.seleccionar-todos-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const atributoId = this.dataset.atributoId;
        const valoresContainer = document.getElementById(`valores-container-${atributoId}`);
        const valorCheckboxes = valoresContainer.querySelectorAll('.valor-checkbox');
        
        valorCheckboxes.forEach(cb => {
            cb.checked = this.checked;
            
            const atributoNombre = cb.dataset.atributoNombre;
            const valorId = cb.value;
            const valorNombre = cb.dataset.valorNombre;
            
            if (this.checked) {
                if (!atributosActivos[atributoId]) {
                    atributosActivos[atributoId] = {
                        id: atributoId,
                        nombre: atributoNombre,
                        valores: {}
                    };
                }
                atributosActivos[atributoId].valores[valorId] = {
                    id: valorId,
                    nombre: valorNombre,
                    atributoId: atributoId,
                    atributoNombre: atributoNombre
                };
            } else {
                if (atributosActivos[atributoId]) {
                    delete atributosActivos[atributoId].valores[valorId];
                    if (Object.keys(atributosActivos[atributoId].valores).length === 0) {
                        delete atributosActivos[atributoId];
                    }
                }
            }
        });
        
        actualizarPestanasValores();
        actualizarResumenAtributos();
    });
});

document.querySelectorAll('.valor-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const atributoId = this.dataset.atributoId;
        const atributoNombre = this.dataset.atributoNombre;
        const valorId = this.value;
        const valorNombre = this.dataset.valorNombre;
        
        const atributoActivo = document.getElementById(`atributo-activo-${atributoId}`);
        if (!atributoActivo.checked) {
            atributoActivo.checked = true;
            atributoActivo.dispatchEvent(new Event('change'));
        }
        
        if (!atributosActivos[atributoId]) {
            atributosActivos[atributoId] = {
                id: atributoId,
                nombre: atributoNombre,
                valores: {}
            };
        }
        
        if (this.checked) {
            atributosActivos[atributoId].valores[valorId] = {
                id: valorId,
                nombre: valorNombre,
                atributoId: atributoId,
                atributoNombre: atributoNombre
            };
        } else {
            delete atributosActivos[atributoId].valores[valorId];
            if (Object.keys(atributosActivos[atributoId].valores).length === 0) {
                delete atributosActivos[atributoId];
            }
        }
        
        const valoresContainer = document.getElementById(`valores-container-${atributoId}`);
        const valorCheckboxes = valoresContainer.querySelectorAll('.valor-checkbox');
        const seleccionarTodos = document.getElementById(`seleccionar-todos-${atributoId}`);
        const seleccionados = valoresContainer.querySelectorAll('.valor-checkbox:checked');
        
        if (seleccionarTodos) {
            if (seleccionados.length === valorCheckboxes.length) {
                seleccionarTodos.checked = true;
                seleccionarTodos.indeterminate = false;
            } else if (seleccionados.length > 0) {
                seleccionarTodos.checked = false;
                seleccionarTodos.indeterminate = true;
            } else {
                seleccionarTodos.checked = false;
                seleccionarTodos.indeterminate = false;
            }
        }
        
        actualizarPestanasValores();
        actualizarResumenAtributos();
    });
});

function actualizarResumenAtributos() {
    const resumenDiv = document.getElementById('resumen-atributos');
    const lista = document.getElementById('atributos-activos-lista');
    const totalAtributosBadge = document.getElementById('total-atributos-activos-badge');
    
    lista.innerHTML = '';
    let atributosCount = 0;
    let totalValores = 0;
    
    Object.values(atributosActivos).forEach(atributo => {
        const valoresArray = Object.values(atributo.valores);
        if (valoresArray.length > 0) {
            atributosCount++;
            totalValores += valoresArray.length;
            
            const item = document.createElement('div');
            item.className = 'p-2 bg-white border rounded';
            
            const span1 = document.createElement('span');
            span1.className = 'fw-bold text-primary';
            span1.textContent = atributo.nombre + ': ';
            
            const span2 = document.createElement('span');
            span2.className = 'badge bg-success ms-2';
            span2.textContent = valoresArray.length + ' valores';
            
            const div = document.createElement('div');
            div.className = 'mt-1 small';
            
            valoresArray.forEach(v => {
                const badge = document.createElement('span');
                badge.className = 'badge bg-light text-dark me-1';
                badge.textContent = v.nombre;
                div.appendChild(badge);
            });
            
            item.appendChild(span1);
            item.appendChild(span2);
            item.appendChild(div);
            
            lista.appendChild(item);
        }
    });
    
    if (atributosCount > 0) {
        resumenDiv.style.display = 'block';
        totalAtributosBadge.textContent = atributosCount + ' atributos activos (' + totalValores + ' valores)';
    } else {
        resumenDiv.style.display = 'none';
        totalAtributosBadge.textContent = '0 atributos activos';
    }
}

function actualizarPestanasValores() {
    const tabsContainer = document.getElementById('valores-activos-tabs-container');
    const noAtributosMsg = document.getElementById('no-atributos-activos-message');
    const navTabs = document.querySelector('#valoresTab');
    const tabContent = document.querySelector('#valoresTabContent');
    const totalValoresBadge = document.getElementById('total-valores-badge');
    
    if (!tabsContainer || !navTabs || !tabContent) return;
    
    let todosLosValores = [];
    
    Object.values(atributosActivos).forEach(atributo => {
        Object.values(atributo.valores).forEach(valor => {
            todosLosValores.push({
                ...valor,
                atributoNombre: atributo.nombre,
                atributoId: atributo.id
            });
        });
    });
    
    if (todosLosValores.length === 0) {
        tabsContainer.style.display = 'none';
        noAtributosMsg.style.display = 'block';
        if (totalValoresBadge) totalValoresBadge.textContent = '0 valores';
        return;
    }
    
    tabsContainer.style.display = 'block';
    noAtributosMsg.style.display = 'none';
    if (totalValoresBadge) {
        totalValoresBadge.textContent = todosLosValores.length + ' ' + (todosLosValores.length === 1 ? 'valor' : 'valores');
    }
    
    navTabs.innerHTML = '';
    tabContent.innerHTML = '';
    
    const productoSku = document.getElementById('vCodigo_barras')?.value || 'PROD';
    
    todosLosValores.forEach((valor, index) => {
        const valorId = valor.id;
        const valorKey = `${valor.atributoId}_${valorId}`;
        
        const tabItem = document.createElement('li');
        tabItem.className = 'nav-item';
        tabItem.role = 'presentation';
        
        const tabButton = document.createElement('button');
        tabButton.className = 'nav-link' + (index === 0 ? ' active' : '');
        tabButton.id = 'valor-tab-' + valorKey;
        tabButton.setAttribute('data-bs-toggle', 'tab');
        tabButton.setAttribute('data-bs-target', '#valor-content-' + valorKey);
        tabButton.type = 'button';
        tabButton.role = 'tab';
        tabButton.setAttribute('data-valor-id', valorId);
        tabButton.setAttribute('data-atributo-id', valor.atributoId);
        
        const icon = document.createElement('i');
        icon.className = 'fas fa-cube me-1';
        tabButton.appendChild(icon);
        
        tabButton.appendChild(document.createTextNode(' ' + valor.atributoNombre + ': ' + valor.nombre));
        
        tabItem.appendChild(tabButton);
        navTabs.appendChild(tabItem);
        
        const contentPane = document.createElement('div');
        contentPane.className = 'tab-pane fade' + (index === 0 ? ' show active' : '');
        contentPane.id = 'valor-content-' + valorKey;
        contentPane.role = 'tabpanel';
        
        const combinacion = [{
            atributoId: valor.atributoId,
            atributoNombre: valor.atributoNombre,
            valorId: valor.id,
            valorNombre: valor.nombre
        }];
        const skuSugerido = generarSkuSugerido(productoSku, combinacion);
        
        const formHtml = `
            <div class="variacion-form-container">
                <div class="variacion-header-info mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="fw-bold mb-2">
                                <i class="fas fa-cube me-2"></i>
                                Variación: <span class="text-warning">${valor.atributoNombre}: ${valor.nombre}</span>
                            </h6>
                            <p class="small mb-0 opacity-75">
                                <i class="fas fa-info-circle me-1"></i>
                                Configura los datos específicos para esta variación del producto
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="badge bg-white text-dark p-2">
                                <i class="fas fa-barcode me-1"></i>
                                ID: ${valor.atributoNombre.substring(0,3)}-${valor.nombre.substring(0,3)}
                            </span>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="variaciones[${valorKey}][id_atributo]" value="${valor.atributoId}">
                <input type="hidden" name="variaciones[${valorKey}][id_atributo_valor]" value="${valor.id}">
                <input type="hidden" name="variaciones[${valorKey}][vNombre_variacion]" 
                       value="${valor.atributoNombre}: ${valor.nombre}">

                <div class="row mb-3">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="sku-${valorKey}" class="form-label fw-bold">
                                SKU de la variación <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-barcode"></i>
                                </span>
                                <input type="text" 
                                       name="variaciones[${valorKey}][vSKU]" 
                                       id="sku-${valorKey}" 
                                       class="form-control"
                                       value="${skuSugerido}"
                                       maxlength="50"
                                       required
                                       oninput="validarSKU(this)"
                                       pattern="[A-Za-z0-9-]+"
                                       title="Solo letras, números y guiones"
                                       placeholder="Ej: ${skuSugerido}"
                                       data-atributo-id="${valor.atributoId}"
                                       data-valor-id="${valor.id}"
                                       autocomplete="off">
                            </div>
                            <small class="form-text text-muted">
                                <i class="fas fa-lightbulb me-1"></i>
                                Sugerido: ${skuSugerido}
                            </small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label fw-bold d-block">Estado de la variación</label>
                            <div class="form-check form-switch mt-2">
                                <input type="checkbox" 
                                       name="variaciones[${valorKey}][bActivo]" 
                                       id="activo-${valorKey}" 
                                       class="form-check-input" 
                                       value="1"
                                       checked>
                                <label class="form-check-label" for="activo-${valorKey}">
                                    Variación activa
                                </label>
                            </div>
                            <small class="form-text text-muted d-block">
                                Desactivar para ocultar esta variación
                            </small>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="precio-${valorKey}" class="form-label fw-bold">
                                Precio de venta <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="text" 
                                       name="variaciones[${valorKey}][dPrecio]" 
                                       id="precio-${valorKey}" 
                                       class="form-control"
                                       value=""
                                       required
                                       oninput="validarPrecio(this)"
                                       placeholder="0.00"
                                       title="Máximo: 9,999,999.99"
                                       autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="stock-${valorKey}" class="form-label fw-bold">
                                Stock disponible <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-boxes"></i>
                                </span>
                                <input type="text" 
                                       name="variaciones[${valorKey}][iStock]" 
                                       id="stock-${valorKey}" 
                                       class="form-control"
                                       value="0"
                                       required
                                       oninput="validarStock(this)"
                                       pattern="[0-9]{1,4}"
                                       min="0"
                                       max="9999"
                                       placeholder="0"
                                       autocomplete="off">
                            </div>
                            <small class="form-text text-muted">Máximo 9,999 unidades</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="clase-envio-${valorKey}" class="form-label fw-bold">
                                Clase de envío
                            </label>
                            <select name="variaciones[${valorKey}][vClase_envio]" 
                                    id="clase-envio-${valorKey}" 
                                    class="form-select">
                                <option value="">-- Por defecto --</option>
                                <option value="estandar">Estándar</option>
                                <option value="express">Express</option>
                                <option value="fragil">Frágil</option>
                                <option value="grandes_dimensiones">Grandes dimensiones</option>
                            </select>
                            <small class="form-text text-muted">Dejar vacío para heredar del producto</small>
                        </div>
                    </div>
                </div>

                <!-- CAMPOS DE DIMENSIONES PARA VARIACIONES -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="peso-${valorKey}" class="form-label fw-bold">
                                <i class="fas fa-weight-hanging me-1"></i>Peso (kg)
                            </label>
                            <input type="text" 
                                   name="variaciones[${valorKey}][dPeso]" 
                                   id="peso-${valorKey}" 
                                   class="form-control"
                                   value=""
                                   oninput="validarPeso(this)"
                                   onblur="formatearPeso(this)"
                                   onkeydown="permitirBorrado(event)"
                                   placeholder="0.000"
                                   title="Máximo: 999.999 kg (máximo 3 decimales)"
                                   autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="largo-${valorKey}" class="form-label fw-bold">
                                <i class="fas fa-ruler-vertical me-1"></i>Largo (cm)
                            </label>
                            <input type="text" 
                                   name="variaciones[${valorKey}][dLargo_cm]" 
                                   id="largo-${valorKey}" 
                                   class="form-control"
                                   value=""
                                   oninput="validarDimensionCm(this)"
                                   onblur="formatearDimensionCm(this)"
                                   onkeydown="permitirBorrado(event)"
                                   placeholder="0.00"
                                   title="Máximo: 999.99 cm (máximo 2 decimales)"
                                   autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="ancho-${valorKey}" class="form-label fw-bold">
                                <i class="fas fa-ruler-horizontal me-1"></i>Ancho (cm)
                            </label>
                            <input type="text" 
                                   name="variaciones[${valorKey}][dAncho_cm]" 
                                   id="ancho-${valorKey}" 
                                   class="form-control"
                                   value=""
                                   oninput="validarDimensionCm(this)"
                                   onblur="formatearDimensionCm(this)"
                                   onkeydown="permitirBorrado(event)"
                                   placeholder="0.00"
                                   title="Máximo: 999.99 cm (máximo 2 decimales)"
                                   autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="alto-${valorKey}" class="form-label fw-bold">
                                <i class="fas fa-arrows-alt-v me-1"></i>Alto (cm)
                            </label>
                            <input type="text" 
                                   name="variaciones[${valorKey}][dAlto_cm]" 
                                   id="alto-${valorKey}" 
                                   class="form-control"
                                   value=""
                                   oninput="validarDimensionCm(this)"
                                   onblur="formatearDimensionCm(this)"
                                   onkeydown="permitirBorrado(event)"
                                   placeholder="0.00"
                                   title="Máximo: 999.99 cm (máximo 2 decimales)"
                                   autocomplete="off">
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="card bg-light border">
                            <div class="card-body py-2">
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <div class="form-check form-switch">
                                            <input type="checkbox" 
                                                   name="variaciones[${valorKey}][bTiene_descuento]" 
                                                   id="descuento-${valorKey}" 
                                                   class="form-check-input" 
                                                   value="1"
                                                   onchange="toggleDescuentoVariacion(this, '${valorKey}')">
                                            <label class="form-check-label fw-bold" for="descuento-${valorKey}">
                                                <i class="fas fa-percentage me-1"></i>
                                                Activar descuento para esta variación
                                            </label>
                                        </div>
                                        <small class="text-muted d-block mt-1">
                                            Precio especial por tiempo limitado
                                        </small>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="row descuento-fields-${valorKey}" style="display: none;">
                                            <div class="col-md-4 mb-2 mb-md-0">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">$</span>
                                                    <input type="text" 
                                                           name="variaciones[${valorKey}][dPrecio_descuento]" 
                                                           class="form-control variacion-precio-descuento"
                                                           data-precio-normal-id="precio-${valorKey}"
                                                           data-valor-key="${valorKey}"
                                                           value=""
                                                           oninput="validarPrecio(this); validarPrecioDescuentoVariacionInstantaneo(this);"
                                                           onblur="validarPrecioDescuentoVariacion(this)"
                                                           placeholder="Precio descuento"
                                                           autocomplete="off">
                                                </div>
                                                <div class="invalid-feedback" style="display: none;"></div>
                                            </div>
                                            <div class="col-md-3 mb-2 mb-md-0">
                                                <input type="date" 
                                                       name="variaciones[${valorKey}][dFecha_inicio_descuento]" 
                                                       class="form-control form-control-sm fecha-inicio-${valorKey}"
                                                       value=""
                                                       autocomplete="off">
                                            </div>
                                            <div class="col-md-3 mb-2 mb-md-0">
                                                <input type="date" 
                                                       name="variaciones[${valorKey}][dFecha_fin_descuento]" 
                                                       class="form-control form-control-sm fecha-fin-${valorKey}"
                                                       value=""
                                                       autocomplete="off">
                                            </div>
                                            <div class="col-md-2">
                                                <input type="text" 
                                                       name="variaciones[${valorKey}][vMotivo_descuento]" 
                                                       class="form-control form-control-sm"
                                                       value=""
                                                       placeholder="Motivo"
                                                       maxlength="255"
                                                       autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="descripcion-${valorKey}" class="form-label fw-bold">
                                Descripción de la variación
                            </label>
                            <textarea name="variaciones[${valorKey}][tDescripcion]" 
                                      id="descripcion-${valorKey}" 
                                      class="form-control" 
                                      rows="2"
                                      placeholder="Descripción específica para esta variación (opcional)"
                                      maxlength="500"
                                      autocomplete="off"></textarea>
                            <small class="form-text text-muted">
                                Máximo 500 caracteres. Descripción específica para esta variación.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        contentPane.innerHTML = formHtml;
        tabContent.appendChild(contentPane);
    });
}

function generarSkuSugerido(productoSku, combinacion) {
    let sku = productoSku || 'PROD';
    combinacion.forEach(item => {
        const attrCode = item.atributoNombre.substring(0, 3).toUpperCase();
        const valCode = item.valorNombre.replace(/[^a-zA-Z0-9]/g, '').substring(0, 3).toUpperCase();
        sku += `-${attrCode}${valCode}`;
    });
    return sku;
}

function toggleDescuentoVariacion(checkbox, valorKey) {
    const fields = document.querySelector(`.descuento-fields-${valorKey}`);
    const precioDescuento = fields.querySelector('.variacion-precio-descuento');
    const fechaInicio = fields.querySelector(`.fecha-inicio-${valorKey}`);
    const fechaFin = fields.querySelector(`.fecha-fin-${valorKey}`);
    
    if (checkbox.checked) {
        fields.style.display = 'flex';
        precioDescuento.required = true;
        fechaInicio.required = true;
        fechaFin.required = true;
    } else {
        fields.style.display = 'none';
        precioDescuento.required = false;
        fechaInicio.required = false;
        fechaFin.required = false;
        precioDescuento.classList.remove('is-invalid');
    }
}

function validarPrecioDescuentoVariacionInstantaneo(input) {
    const precioNormalId = input.dataset.precioNormalId;
    const precioNormal = document.getElementById(precioNormalId);
    const valorKey = input.dataset.valorKey;
    const checkbox = document.getElementById(`descuento-${valorKey}`);
    
    if (!checkbox || !checkbox.checked) return true;
    
    if (precioNormal && input.value) {
        const precioNormalValor = parseFloat(precioNormal.value) || 0;
        const precioDescuentoValor = parseFloat(input.value) || 0;
        
        let errorDiv = input.closest('.col-md-4').querySelector('.invalid-feedback');
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            input.closest('.col-md-4').appendChild(errorDiv);
        }
        
        if (precioDescuentoValor >= precioNormalValor && precioDescuentoValor > 0 && input.value !== '') {
            input.classList.add('is-invalid');
            errorDiv.textContent = 'El precio de descuento debe ser menor que el precio normal';
            errorDiv.style.display = 'block';
            return false;
        } else {
            input.classList.remove('is-invalid');
            errorDiv.style.display = 'none';
            return true;
        }
    }
    return true;
}

function validarPrecioDescuentoVariacion(input) {
    const precioNormalId = input.dataset.precioNormalId;
    const precioNormal = document.getElementById(precioNormalId);
    const valorKey = input.dataset.valorKey;
    const checkbox = document.getElementById(`descuento-${valorKey}`);
    
    if (!checkbox || !checkbox.checked) return true;
    
    if (precioNormal && input.value) {
        const precioNormalValor = parseFloat(precioNormal.value) || 0;
        const precioDescuentoValor = parseFloat(input.value) || 0;
        
        let errorDiv = input.closest('.col-md-4').querySelector('.invalid-feedback');
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            input.closest('.col-md-4').appendChild(errorDiv);
        }
        
        if (precioDescuentoValor >= precioNormalValor && precioDescuentoValor > 0) {
            input.classList.add('is-invalid');
            errorDiv.textContent = 'El precio de descuento debe ser menor que el precio normal';
            errorDiv.style.display = 'block';
            return false;
        } else {
            input.classList.remove('is-invalid');
            errorDiv.style.display = 'none';
            return true;
        }
    }
    return true;
}

// ============ FUNCIONES PARA FORMULARIOS RÁPIDOS ============

function quickGenerarSlug(texto, inputId) {
    if (!texto) return;
    let slug = texto.toLowerCase();
    slug = slug.replace(/á/gi, 'a');
    slug = slug.replace(/é/gi, 'e');
    slug = slug.replace(/í/gi, 'i');
    slug = slug.replace(/ó/gi, 'o');
    slug = slug.replace(/ú/gi, 'u');
    slug = slug.replace(/ñ/gi, 'n');
    slug = slug.replace(/[^a-z0-9\s]/g, '');
    slug = slug.replace(/\s+/g, '-');
    slug = slug.replace(/-+/g, '-');
    slug = slug.replace(/^-+/, '').replace(/-+$/, '');
    document.getElementById(inputId).value = slug;
}

function quickActualizarSlug(nombre, slugId) {
    quickGenerarSlug(nombre, slugId);
}

// ============ FUNCIONES PARA CREACIÓN RÁPIDA ============

function crearMarca() {
    const nombre = document.getElementById('vNombre_marca').value.trim();
    const descripcion = document.getElementById('tDescripcion_marca').value.trim();
    
    if (!nombre) {
        Swal.fire('Error', 'El nombre de la marca es obligatorio', 'error');
        return;
    }
    
    Swal.fire({
        title: 'Creando marca...',
        text: 'Por favor espera',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });
    
    fetch('{{ route("marcas.quick-create") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            vNombre: nombre,
            tDescripcion: descripcion
        })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw err; });
        }
        return response.json();
    })
    .then(data => {
        Swal.close();
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            });
            
            limpiarFormularioMarca();
            
            const select = document.getElementById('id_marca');
            const option = document.createElement('option');
            option.value = data.marca.id_marca;
            option.text = nombre;
            select.appendChild(option);
            select.value = data.marca.id_marca;
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Error al crear la marca'
            });
        }
    })
    .catch(error => {
        Swal.close();
        let message = 'Error en la solicitud';
        if (error.errors) {
            message = Object.values(error.errors).flat().join(', ');
        } else if (error.message) {
            message = error.message;
        }
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message
        });
    });
}

function crearEtiqueta() {
    const nombre = document.getElementById('vNombre_eti').value.trim();
    const color = document.getElementById('color_eti').value;
    const descripcion = document.getElementById('tDescripcion_eti').value.trim();
    
    if (!nombre) {
        Swal.fire('Error', 'El nombre de la etiqueta es obligatorio', 'error');
        return;
    }
    
    Swal.fire({
        title: 'Creando etiqueta...',
        text: 'Por favor espera',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });
    
    fetch('{{ route("etiquetas.quick-create") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            vNombre: nombre,
            color: color,
            tDescripcion: descripcion
        })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw err; });
        }
        return response.json();
    })
    .then(data => {
        Swal.close();
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            });
            
            limpiarFormularioEtiqueta();
            
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Error al crear la etiqueta'
            });
        }
    })
    .catch(error => {
        Swal.close();
        let message = 'Error en la solicitud';
        if (error.errors) {
            message = Object.values(error.errors).flat().join(', ');
        } else if (error.message) {
            message = error.message;
        }
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message
        });
    });
}

function crearAtributo() {
    const nombre = document.getElementById('vNombre_attr').value.trim();
    const slug = document.getElementById('vSlug_attr').value.trim();
    const descripcion = document.getElementById('tDescripcion_attr').value.trim();
    
    if (!nombre) {
        Swal.fire('Error', 'El nombre del atributo es obligatorio', 'error');
        return;
    }
    
    Swal.fire({
        title: 'Creando atributo...',
        text: 'Por favor espera',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });
    
    fetch('{{ route("atributos.quick-create") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            vNombre: nombre,
            vSlug: slug,
            tDescripcion: descripcion
        })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw err; });
        }
        return response.json();
    })
    .then(data => {
        Swal.close();
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            });
            
            document.getElementById('vNombre_attr').value = '';
            document.getElementById('vSlug_attr').value = '';
            document.getElementById('tDescripcion_attr').value = '';
            
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Error al crear el atributo'
            });
        }
    })
    .catch(error => {
        Swal.close();
        let message = 'Error en la solicitud';
        if (error.errors) {
            message = Object.values(error.errors).flat().join(', ');
        } else if (error.message) {
            message = error.message;
        }
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message
        });
    });
}

function crearImpuesto() {
    const nombre = document.getElementById('vNombre_impuesto').value.trim();
    const tipo = document.getElementById('eTipo_impuesto').value;
    const porcentaje = document.getElementById('dPorcentaje_impuesto').value.trim();
    const descripcion = document.getElementById('tDescripcion_impuesto').value.trim();
    const activo = document.getElementById('bActivo_impuesto').checked ? 1 : 0;
    
    if (!nombre) {
        Swal.fire('Error', 'El nombre del impuesto es obligatorio', 'error');
        return;
    }
    
    if (!tipo) {
        Swal.fire('Error', 'Debes seleccionar un tipo de impuesto', 'error');
        return;
    }
    
    if (!porcentaje) {
        Swal.fire('Error', 'El porcentaje es obligatorio', 'error');
        return;
    }
    
    Swal.fire({
        title: 'Creando impuesto...',
        text: 'Por favor espera',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });
    
    fetch('{{ route("impuestos.quick-create") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            vNombre: nombre,
            eTipo: tipo,
            dPorcentaje: porcentaje,
            tDescripcion: descripcion,
            bActivo: activo
        })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw err; });
        }
        return response.json();
    })
    .then(data => {
        Swal.close();
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            });
            
            limpiarFormularioImpuesto();
            
            // Agregar el nuevo impuesto al select
            const select = document.getElementById('id_impuesto');
            const option = document.createElement('option');
            option.value = data.impuesto.id_impuesto;
            option.setAttribute('data-porcentaje', data.impuesto.dPorcentaje);
            option.setAttribute('data-tipo', data.impuesto.eTipo);
            option.text = data.impuesto.vNombre + ' (' + data.impuesto.eTipo + ' - ' + parseFloat(data.impuesto.dPorcentaje).toFixed(2) + '%)';
            select.appendChild(option);
            
            // Seleccionar el nuevo impuesto
            select.value = data.impuesto.id_impuesto;
            
            // Actualizar precio final
            actualizarPrecioFinal();
            
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: 'Impuesto creado y seleccionado automáticamente',
                timer: 1500,
                showConfirmButton: false
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Error al crear el impuesto'
            });
        }
    })
    .catch(error => {
        Swal.close();
        let message = 'Error en la solicitud';
        if (error.errors) {
            message = Object.values(error.errors).flat().join(', ');
        } else if (error.message) {
            message = error.message;
        }
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message
        });
    });
}

function activarTabAtributos() {
    const tab = document.getElementById('atributos-tab');
    if (tab) {
        tab.click();
        tab.scrollIntoView({ behavior: 'smooth' });
    }
}

function activarTabEtiquetas() {
    const tab = document.getElementById('etiquetas-tab');
    if (tab) {
        tab.click();
        tab.scrollIntoView({ behavior: 'smooth' });
    }
}

function activarTabImpuestos() {
    const tab = document.getElementById('impuestos-tab');
    if (tab) {
        tab.click();
        tab.scrollIntoView({ behavior: 'smooth' });
    }
}

// ============ EVENT LISTENERS ============

document.addEventListener('DOMContentLoaded', function() {
    const pesoInput = document.getElementById('dPeso');
    if (pesoInput) {
        pesoInput.addEventListener('blur', function() {
            formatearPeso(this);
        });
    }
    
    const dimensionInputs = ['dLargo_cm', 'dAncho_cm', 'dAlto_cm'];
    dimensionInputs.forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            input.addEventListener('blur', function() {
                formatearDimensionCm(this);
            });
        }
    });

    const precioVenta = document.getElementById('dPrecio_venta');
    if (precioVenta) {
        precioVenta.addEventListener('input', function() {
            if (document.getElementById('bTiene_descuento')?.checked) {
                validarPrecioDescuentoProducto();
            }
        });
    }

    const categoriaForm = document.getElementById('categoriaQuickForm');
    
    if (categoriaForm) {
        categoriaForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Crear FormData y agregar la imagen si existe
            const formData = new FormData(this);
            
            // Si hay una imagen seleccionada previamente, asegurarse de que se incluya
            if (categoriaImagenFile) {
                // Reemplazar el archivo en el FormData si ya existe
                if (formData.has('vImagen')) {
                    formData.delete('vImagen');
                }
                formData.append('vImagen', categoriaImagenFile);
            }
            
            Swal.fire({
                title: 'Creando categoría...',
                text: 'Por favor espera',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });
            
            fetch('{{ route("categorias.store") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                Swal.close();
                
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: data.message || 'Categoría creada exitosamente',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    
                    limpiarFormularioCategoria();
                    
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    let errorMessage = data.message || 'Error al crear la categoría';
                    
                    if (data.errors) {
                        errorMessage = Object.values(data.errors).flat().join('<br>');
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: errorMessage
                    });
                }
            })
            .catch(error => {
                Swal.close();
                
                let errorMessage = 'Error de conexión';
                
                if (error.errors) {
                    errorMessage = Object.values(error.errors).flat().join('<br>');
                } else if (error.message) {
                    errorMessage = error.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    html: errorMessage
                });
            });
        });
    }
    
    if (document.getElementById('bTiene_descuento')) {
        if (document.getElementById('bTiene_descuento').checked) {
            toggleDescuentoFields();
        }
    }
    
    document.querySelectorAll('.atributo-activo-checkbox').forEach(checkbox => {
        if (checkbox.checked) {
            checkbox.dispatchEvent(new Event('change'));
        }
    });
    
    // Inicializar contador
    document.getElementById('selected-images-count').textContent = '0 archivos';
    
    renderSelectedImages();
    actualizarResumenAtributos();
    actualizarPestanasValores();
    actualizarContadorImagenes();
    actualizarPrecioFinal();
    
    const colorPicker = document.getElementById('color_eti');
    const colorText = document.getElementById('color_text_eti');
    
    if (colorPicker && colorText) {
        colorPicker.addEventListener('input', function() {
            colorText.value = this.value;
        });
        
        colorText.addEventListener('input', function() {
            if (this.value.match(/^#[0-9A-F]{6}$/i)) {
                colorPicker.value = this.value;
            }
        });
    }

    // Agregar evento al select de impuestos
    document.getElementById('id_impuesto')?.addEventListener('change', actualizarPrecioFinal);
});

// Validación del formulario antes de enviar
document.getElementById('productoForm').addEventListener('submit', function(e) {
    const btnSubmit = document.getElementById('btnSubmit');
    
    if (!imagenPrincipalFile) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Imagen principal requerida',
            text: 'Debes seleccionar una imagen principal para el producto'
        });
        return false;
    }
    
    if (document.getElementById('bTiene_descuento') && document.getElementById('bTiene_descuento').checked) {
        const precioVenta = parseFloat(document.getElementById('dPrecio_venta').value) || 0;
        const precioDescuento = parseFloat(document.getElementById('dPrecio_descuento').value) || 0;
        
        if (precioDescuento >= precioVenta) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error en precio de descuento',
                text: 'El precio de descuento debe ser menor que el precio de venta'
            });
            document.getElementById('dPrecio_descuento').focus();
            return false;
        }
        
        const fechaInicio = document.getElementById('dFecha_inicio_descuento').value;
        const fechaFin = document.getElementById('dFecha_fin_descuento').value;
        
        if (!fechaInicio || !fechaFin) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Fechas requeridas',
                text: 'Cuando el descuento está activo, las fechas de inicio y fin son obligatorias'
            });
            return false;
        }
        
        if (new Date(fechaFin) < new Date(fechaInicio)) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error en fechas de descuento',
                text: 'La fecha de fin debe ser igual o posterior a la fecha de inicio'
            });
            return false;
        }
    }
    
    let errorVariaciones = [];
    document.querySelectorAll('.variacion-precio-descuento').forEach(input => {
        const valorKey = input.dataset.valorKey;
        const checkbox = document.getElementById(`descuento-${valorKey}`);
        
        if (checkbox && checkbox.checked && input.value) {
            const precioNormalId = input.dataset.precioNormalId;
            const precioNormal = document.getElementById(precioNormalId);
            
            if (precioNormal) {
                const precioNormalValor = parseFloat(precioNormal.value) || 0;
                const precioDescuentoValor = parseFloat(input.value) || 0;
                
                if (precioDescuentoValor >= precioNormalValor) {
                    errorVariaciones.push(`En una variación, el precio de descuento debe ser menor que el precio normal`);
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                }
            }
        }
    });
    
    if (errorVariaciones.length > 0) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Errores en precios de descuento',
            html: errorVariaciones.join('<br>')
        });
        return false;
    }
    
    const skuInputs = document.querySelectorAll('input[name*="[vSKU]"]');
    let variacionesValidas = true;
    
    skuInputs.forEach(input => {
        if (!input.value.trim()) {
            variacionesValidas = false;
            input.classList.add('is-invalid');
        }
    });
    
    if (!variacionesValidas) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Error en variaciones',
            text: 'Todas las variaciones deben tener un SKU asignado'
        });
        return false;
    }
    
    if (btnSubmit) {
        btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Guardando...';
        btnSubmit.disabled = true;
    }
    
    return true;
});

document.querySelectorAll('input, select, textarea').forEach(elemento => {
    elemento.addEventListener('input', function() {
        this.classList.remove('is-invalid');
    });
});

document.querySelectorAll('button[type="button"]').forEach(button => {
    button.addEventListener('click', function(e) {
        if (this.closest('form')) {
            e.preventDefault();
        }
    });
});
</script>
@endpush

@endsection