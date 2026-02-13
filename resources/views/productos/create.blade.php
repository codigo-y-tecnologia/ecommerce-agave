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

        <!-- ========================================= -->
        <!-- INFORMACIÓN BÁSICA DEL PRODUCTO          -->
        <!-- ========================================= -->
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

                <!-- CAMPOS DE OFERTA -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-percentage me-1"></i>Oferta Especial
                            </label>
                            <div class="form-check form-switch">
                                <input type="checkbox" name="bTiene_oferta" id="bTiene_oferta" 
                                       class="form-check-input" value="1"
                                       {{ old('bTiene_oferta') ? 'checked' : '' }}
                                       onchange="toggleOfertaFields()">
                                <label class="form-check-label" for="bTiene_oferta">
                                    Activar oferta para este producto
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                Permite establecer un precio de oferta por tiempo limitado
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

                <!-- CAMPOS DE OFERTA (OCULTOS INICIALMENTE) -->
                <div id="ofertaFields" style="display: none;">
                    <div class="row">
                        <div class="col-md-4">
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
                                           value="{{ old('dPrecio_oferta') }}" 
                                           oninput="validarPrecio(this)"
                                           placeholder="0.00"
                                           autocomplete="off">
                                </div>
                                @error('dPrecio_oferta')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Debe ser menor al precio de venta</small>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="dFecha_inicio_oferta" class="form-label fw-bold">
                                    Fecha inicio <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       name="dFecha_inicio_oferta" 
                                       id="dFecha_inicio_oferta" 
                                       class="form-control @error('dFecha_inicio_oferta') is-invalid @enderror"
                                       value="{{ old('dFecha_inicio_oferta') }}"
                                       autocomplete="off">
                                @error('dFecha_inicio_oferta')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="dFecha_fin_oferta" class="form-label fw-bold">
                                    Fecha fin <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       name="dFecha_fin_oferta" 
                                       id="dFecha_fin_oferta" 
                                       class="form-control @error('dFecha_fin_oferta') is-invalid @enderror"
                                       value="{{ old('dFecha_fin_oferta') }}"
                                       autocomplete="off">
                                @error('dFecha_fin_oferta')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label for="vMotivo_oferta" class="form-label fw-bold">
                                    Motivo de la oferta
                                </label>
                                <input type="text" 
                                       name="vMotivo_oferta" 
                                       id="vMotivo_oferta" 
                                       class="form-control @error('vMotivo_oferta') is-invalid @enderror"
                                       value="{{ old('vMotivo_oferta') }}"
                                       maxlength="255"
                                       placeholder="Ej: Liquidación de temporada, Black Friday, etc."
                                       autocomplete="off">
                                @error('vMotivo_oferta')
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
                            <input type="text" name="dPeso" id="dPeso" 
                                   class="form-control @error('dPeso') is-invalid @enderror"
                                   value="{{ old('dPeso') }}"
                                   oninput="validarDecimal(this, 3)"
                                   placeholder="0.000"
                                   title="Máximo: 999.999 kg"
                                   autocomplete="off">
                            @error('dPeso')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Máximo 999.999 kg</small>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label for="dLargo_cm" class="form-label fw-bold">
                                <i class="fas fa-ruler-vertical me-1"></i>Largo (cm)
                            </label>
                            <input type="text" name="dLargo_cm" id="dLargo_cm" 
                                   class="form-control @error('dLargo_cm') is-invalid @enderror"
                                   value="{{ old('dLargo_cm') }}"
                                   oninput="validarDecimal(this, 2)"
                                   placeholder="0.00"
                                   title="Máximo: 999.99 cm"
                                   autocomplete="off">
                            @error('dLargo_cm')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Máximo 999.99 cm</small>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label for="dAncho_cm" class="form-label fw-bold">
                                <i class="fas fa-ruler-horizontal me-1"></i>Ancho (cm)
                            </label>
                            <input type="text" name="dAncho_cm" id="dAncho_cm" 
                                   class="form-control @error('dAncho_cm') is-invalid @enderror"
                                   value="{{ old('dAncho_cm') }}"
                                   oninput="validarDecimal(this, 2)"
                                   placeholder="0.00"
                                   title="Máximo: 999.99 cm"
                                   autocomplete="off">
                            @error('dAncho_cm')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Máximo 999.99 cm</small>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label for="dAlto_cm" class="form-label fw-bold">
                                <i class="fas fa-arrows-alt-v me-1"></i>Alto (cm)
                            </label>
                            <input type="text" name="dAlto_cm" id="dAlto_cm" 
                                   class="form-control @error('dAlto_cm') is-invalid @enderror"
                                   value="{{ old('dAlto_cm') }}"
                                   oninput="validarDecimal(this, 2)"
                                   placeholder="0.00"
                                   title="Máximo: 999.99 cm"
                                   autocomplete="off">
                            @error('dAlto_cm')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Máximo 999.99 cm</small>
                        </div>
                    </div>
                </div>

                <!-- CLASE DE ENVÍO Y ETIQUETAS ESPECIALES -->
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
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-star me-1"></i>Etiquetas especiales
                            </label>
                            <div class="row">
                                <div class="col-6 col-md-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="etiquetas_especiales[]" 
                                               value="nuevo" class="form-check-input"
                                               id="etiqueta_nuevo"
                                               {{ is_array(old('etiquetas_especiales')) && in_array('nuevo', old('etiquetas_especiales')) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="etiqueta_nuevo">
                                            <span class="badge bg-primary">Nuevo</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="etiquetas_especiales[]" 
                                               value="popular" class="form-check-input"
                                               id="etiqueta_popular"
                                               {{ is_array(old('etiquetas_especiales')) && in_array('popular', old('etiquetas_especiales')) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="etiqueta_popular">
                                            <span class="badge bg-success">Popular</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="etiquetas_especiales[]" 
                                               value="oferta" class="form-check-input"
                                               id="etiqueta_oferta"
                                               {{ is_array(old('etiquetas_especiales')) && in_array('oferta', old('etiquetas_especiales')) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="etiqueta_oferta">
                                            <span class="badge bg-danger">Oferta</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="etiquetas_especiales[]" 
                                               value="destacado" class="form-check-input"
                                               id="etiqueta_destacado"
                                               {{ is_array(old('etiquetas_especiales')) && in_array('destacado', old('etiquetas_especiales')) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="etiqueta_destacado">
                                            <span class="badge bg-warning text-dark">Destacado</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <small class="form-text text-muted">Etiquetas especiales para destacar el producto</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================= -->
        <!-- CATEGORÍA Y MARCA                         -->
        <!-- ========================================= -->
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

        <!-- ========================================= -->
        <!-- IMÁGENES Y DESCRIPCIÓN                   -->
        <!-- ========================================= -->
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
                            
                            <div class="mt-3">
                                <h6 class="fw-bold mb-2">Imágenes seleccionadas:</h6>
                                <div id="selected-images-container" class="row mb-3"></div>
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
                                        <span class="etiqueta-badge" style="background-color: {{ $etiqueta->color ?? '#007bff' }}; color: white;">
                                            {{ $etiqueta->vNombre }}
                                        </span>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================= -->
        <!-- ATRIBUTOS DEL PRODUCTO (SELECCIÓN)       -->
        <!-- ========================================= -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-tags me-2"></i>Seleccionar Atributos para Variaciones</h5>
                <small class="text-white-50">Selecciona los atributos y valores que quieres usar en tus variaciones</small>
            </div>
            <div class="card-body">
                @if($atributos->count() > 0)
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Instrucciones:</strong> Marca los atributos que deseas activar y selecciona los valores correspondientes. Luego ve a la sección <strong>"Variaciones del Producto"</strong> para configurar cada variación.
                    </div>
                    
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
                                        <label class="form-check-label fw-bold" for="atributo-activo-{{ $atributo->id_atributo }}">
                                            {{ $atributo->vNombre }}
                                            <span class="badge bg-secondary ms-2">{{ $atributo->valoresActivos->count() }} valores</span>
                                        </label>
                                    </div>
                                    <span class="badge bg-warning text-dark atributo-estado-badge" id="estado-{{ $atributo->id_atributo }}" style="display: none;">
                                        <i class="fas fa-check-circle me-1"></i>Activo
                                    </span>
                                </div>
                                
                                <div class="card-body atributo-valores-container" id="valores-container-{{ $atributo->id_atributo }}" style="display: none;">
                                    @if($atributo->valoresActivos->count() > 0)
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input type="checkbox" 
                                                       class="form-check-input seleccionar-todos-checkbox" 
                                                       id="seleccionar-todos-{{ $atributo->id_atributo }}"
                                                       data-atributo-id="{{ $atributo->id_atributo }}">
                                                <label class="form-check-label" for="seleccionar-todos-{{ $atributo->id_atributo }}">
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
                                                    <label class="form-check-label" for="valor-{{ $valor->id_atributo_valor }}">
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
                                            <button type="button" class="btn btn-link p-0 ms-1" onclick="activarTabAtributos()">
                                                Crear valores
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
                        <h6 class="fw-bold mb-3"><i class="fas fa-check-circle text-success me-2"></i>Atributos activados para variaciones:</h6>
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

        <!-- ========================================= -->
        <!-- VARIACIONES DEL PRODUCTO - PESTAÑAS POR VALOR -->
        <!-- ========================================= -->
        <div class="card mb-4">
            <div class="card-header bg-purple text-white d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0"><i class="fas fa-cubes me-2"></i>Variaciones del Producto</h5>
                    <small class="text-white-50">Cada valor seleccionado es una pestaña - Configura cada variación individualmente</small>
                </div>
                <div>
                    <span class="badge bg-light text-dark me-2" id="total-atributos-activos-badge">0 atributos activos</span>
                    <span class="badge bg-warning text-dark" id="total-valores-badge">0 valores</span>
                </div>
            </div>
            
            <div class="card-body">
                @if($atributos->count() > 0)
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
                        <ul class="nav nav-tabs valores-nav" id="valoresTab" role="tablist"></ul>
                        
                        <!-- Contenido de las pestañas - Formularios de variación -->
                        <div class="tab-content p-4 border border-top-0 rounded-bottom bg-white" id="valoresTabContent"></div>
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

        <!-- ========================================= -->
        <!-- BOTONES DE ACCIÓN                        -->
        <!-- ========================================= -->
        <div class="d-flex gap-2 mb-4">
            <button type="submit" class="btn btn-success btn-lg px-4" id="btnSubmit">
                <i class="fas fa-save me-2"></i> Guardar Producto
            </button>
            <a href="{{ route('productos.index') }}" class="btn btn-secondary btn-lg px-4">
                <i class="fas fa-times me-2"></i> Cancelar
            </a>
        </div>
    </form>

    <!-- ========================================= -->
    <!-- PANEL DE HERRAMIENTAS CON TABS           -->
    <!-- ========================================= -->
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
                    <button class="nav-link" id="categorias-tab" data-bs-toggle="tab" 
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
            </ul>
            
            <!-- CONTENIDO DE LOS TABS (FORMULARIOS RÁPIDOS) -->
            <div class="tab-content p-4">
                <!-- TAB: CATEGORÍAS -->
                <div class="tab-pane fade" id="categorias-content" role="tabpanel">
                    <div class="quick-form" id="quick-categoria-form">
                        <h5><i class="fas fa-tags me-2"></i>Crear Nueva Categoría</h5>
                        <p class="text-muted small mb-3">Las categorías te ayudan a organizar tus productos. Pueden tener jerarquía (categoría padre > subcategoría).</p>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="vNombre_cat" class="form-label fw-bold">Nombre de la Categoría *</label>
                                <input type="text" class="form-control" id="vNombre_cat" 
                                       placeholder="Ej: Tequila, Mezcal, Añejos..."
                                       oninput="quickActualizarSlug(this.value, 'vSlug_cat')">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="vSlug_cat" class="form-label fw-bold">Slug (URL amigable)</label>
                                <input type="text" class="form-control" id="vSlug_cat" 
                                       placeholder="tequila-reposado">
                                <small class="text-muted">Se genera automáticamente desde el nombre</small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="id_categoria_padre_cat" class="form-label fw-bold">Categoría Padre</label>
                                <select class="form-select" id="id_categoria_padre_cat">
                                    <option value="">-- Sin categoría padre (Principal) --</option>
                                    @foreach($categorias as $categoria)
                                        @if(!$categoria->id_categoria_padre)
                                            <option value="{{ $categoria->id_categoria }}">{{ $categoria->vNombre }}</option>
                                            @foreach($categoria->hijos as $hijo)
                                                <option value="{{ $hijo->id_categoria }}">↳ {{ $hijo->vNombre }}</option>
                                            @endforeach
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="bActivo_cat" class="form-label fw-bold d-block">Estado</label>
                                <div class="form-check form-switch mt-2">
                                    <input type="checkbox" class="form-check-input" id="bActivo_cat" checked>
                                    <label class="form-check-label" for="bActivo_cat">Categoría activa</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="tDescripcion_cat" class="form-label fw-bold">Descripción (Opcional)</label>
                            <textarea class="form-control" id="tDescripcion_cat" rows="2" 
                                      placeholder="Describe la categoría..."></textarea>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-primary" onclick="crearCategoria()">
                                <i class="fas fa-save me-1"></i> Crear Categoría
                            </button>
                        </div>
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
                            <strong>Nota:</strong> Después de crear el atributo, podrás agregar valores específicos en la sección <strong>"Seleccionar Atributos para Variaciones"</strong>.
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-primary" onclick="crearAtributo()">
                                <i class="fas fa-save me-1"></i> Crear Atributo
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* ========================================= */
/* ESTILOS GENERALES                        */
/* ========================================= */
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

/* ========================================= */
/* ESTILOS PARA PESTAÑAS DE VALORES         */
/* ========================================= */
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

/* ========================================= */
/* ESTILOS PARA FORMULARIOS RÁPIDOS         */
/* ========================================= */
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

/* ========================================= */
/* ESTILOS RESPONSIVE                       */
/* ========================================= */
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
// ============================================
// VARIABLES GLOBALES
// ============================================
let selectedImages = [];
let imageCounter = 0;
let atributosActivos = {};

// ============================================
// FUNCIONES DE VALIDACIÓN
// ============================================
function validarSKU(input) {
    input.value = input.value.replace(/[^A-Za-z0-9]/g, '');
    if (input.value.length > 15) {
        input.value = input.value.substring(0, 15);
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
}

function validarPrecioOferta(input) {
    validarPrecio(input);
    const precioVenta = document.getElementById('dPrecio_venta').value;
    const precioOferta = input.value;
    if (precioVenta && precioOferta && parseFloat(precioOferta) >= parseFloat(precioVenta)) {
        input.classList.add('is-invalid');
        return false;
    }
    return true;
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

function validarDecimal(input, maxDecimales) {
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
    if (value.includes('.')) {
        const partes = value.split('.');
        if (partes[1].length > maxDecimales) {
            partes[1] = partes[1].substring(0, maxDecimales);
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
}

// ============================================
// FUNCIONES PARA OFERTA
// ============================================
function toggleOfertaFields() {
    const ofertaFields = document.getElementById('ofertaFields');
    const tieneOferta = document.getElementById('bTiene_oferta').checked;
    
    if (tieneOferta) {
        ofertaFields.style.display = 'block';
        document.getElementById('dPrecio_oferta').required = true;
        document.getElementById('dFecha_inicio_oferta').required = true;
        document.getElementById('dFecha_fin_oferta').required = true;
    } else {
        ofertaFields.style.display = 'none';
        document.getElementById('dPrecio_oferta').required = false;
        document.getElementById('dFecha_inicio_oferta').required = false;
        document.getElementById('dFecha_fin_oferta').required = false;
    }
}

// ============================================
// FUNCIONES PARA IMÁGENES
// ============================================
function handleImageSelection(event) {
    const files = event.target.files;
    const maxFiles = 8;
    
    if (selectedImages.length + files.length > maxFiles) {
        Swal.fire({
            icon: 'warning',
            title: 'Límite de imágenes',
            text: `Solo puedes seleccionar máximo ${maxFiles} imágenes. Ya tienes ${selectedImages.length} seleccionadas.`
        });
        event.target.value = '';
        return;
    }
    
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        if (!isImageDuplicate(file)) {
            const imageId = 'img_' + Date.now() + '_' + imageCounter++;
            selectedImages.push({
                id: imageId,
                file: file,
                preview: URL.createObjectURL(file)
            });
        }
    }
    
    renderSelectedImages();
    event.target.value = '';
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
    renderSelectedImages();
    updateFileInput();
}

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
    
    const counterInfo = document.createElement('div');
    counterInfo.className = 'col-12 mb-2';
    counterInfo.innerHTML = `
        <div class="alert alert-secondary py-2 mb-0">
            <i class="fas fa-camera me-1"></i>
            <strong>${selectedImages.length}</strong> de 8 imágenes seleccionadas
        </div>
    `;
    container.appendChild(counterInfo);
    
    selectedImages.forEach((image, index) => {
        const col = document.createElement('div');
        col.className = 'col-6 col-md-3 mb-3';
        col.innerHTML = `
            <div class="card border position-relative">
                <button type="button" 
                        class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1"
                        onclick="removeSelectedImage('${image.id}')"
                        style="width: 28px; height: 28px; padding: 0; border-radius: 50%; z-index: 10;">
                    <i class="fas fa-times"></i>
                </button>
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

function updateFileInput() {
    const dataTransfer = new DataTransfer();
    selectedImages.forEach(image => {
        dataTransfer.items.add(image.file);
    });
    const fileInput = document.getElementById('imagenes');
    fileInput.files = dataTransfer.files;
}

// ============================================
// FUNCIONES PARA ATRIBUTOS ACTIVOS
// ============================================

// Manejar activación/desactivación de atributos
document.querySelectorAll('.atributo-activo-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const atributoId = this.dataset.atributoId;
        const atributoNombre = this.dataset.atributoNombre;
        const valoresContainer = document.getElementById(`valores-container-${atributoId}`);
        const estadoBadge = document.getElementById(`estado-${atributoId}`);
        
        if (this.checked) {
            valoresContainer.style.display = 'block';
            estadoBadge.style.display = 'inline-block';
            
            // Inicializar atributo activo
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
            
            // Desmarcar todos los valores
            const checkboxes = valoresContainer.querySelectorAll('.valor-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = false;
            });
            
            // Eliminar atributo activo
            delete atributosActivos[atributoId];
            
            // Resetear seleccionar todos
            const seleccionarTodos = document.getElementById(`seleccionar-todos-${atributoId}`);
            if (seleccionarTodos) {
                seleccionarTodos.checked = false;
            }
        }
        
        actualizarPestanasValores();
        actualizarResumenAtributos();
    });
});

// Manejar selección de todos los valores
document.querySelectorAll('.seleccionar-todos-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const atributoId = this.dataset.atributoId;
        const valoresContainer = document.getElementById(`valores-container-${atributoId}`);
        const valorCheckboxes = valoresContainer.querySelectorAll('.valor-checkbox');
        
        valorCheckboxes.forEach(cb => {
            cb.checked = this.checked;
            
            // Actualizar atributos activos
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

// Manejar selección individual de valores
document.querySelectorAll('.valor-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const atributoId = this.dataset.atributoId;
        const atributoNombre = this.dataset.atributoNombre;
        const valorId = this.value;
        const valorNombre = this.dataset.valorNombre;
        
        // Asegurar que el atributo está activo
        const atributoActivo = document.getElementById(`atributo-activo-${atributoId}`);
        if (!atributoActivo.checked) {
            atributoActivo.checked = true;
            atributoActivo.dispatchEvent(new Event('change'));
        }
        
        // Actualizar atributos activos
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
        
        // Actualizar seleccionar todos
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

// Actualizar resumen de atributos activos
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
            item.innerHTML = `
                <span class="fw-bold text-primary">${atributo.nombre}:</span>
                <span class="badge bg-success ms-2">${valoresArray.length} valores</span>
                <div class="mt-1 small">
                    ${valoresArray.map(v => `<span class="badge bg-light text-dark me-1">${v.nombre}</span>`).join('')}
                </div>
            `;
            lista.appendChild(item);
        }
    });
    
    if (atributosCount > 0) {
        resumenDiv.style.display = 'block';
        totalAtributosBadge.innerHTML = `${atributosCount} atributos activos (${totalValores} valores)`;
    } else {
        resumenDiv.style.display = 'none';
        totalAtributosBadge.innerHTML = '0 atributos activos';
    }
}

// ============================================
// FUNCIONES PARA PESTAÑAS DE VALORES (NUEVO SISTEMA)
// ============================================

function actualizarPestanasValores() {
    const tabsContainer = document.getElementById('valores-activos-tabs-container');
    const noAtributosMsg = document.getElementById('no-atributos-activos-message');
    const navTabs = document.querySelector('#valoresTab');
    const tabContent = document.querySelector('#valoresTabContent');
    const totalValoresBadge = document.getElementById('total-valores-badge');
    
    if (!tabsContainer || !navTabs || !tabContent) return;
    
    // Obtener todos los valores seleccionados de todos los atributos
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
        if (totalValoresBadge) totalValoresBadge.innerHTML = '0 valores';
        return;
    }
    
    tabsContainer.style.display = 'block';
    noAtributosMsg.style.display = 'none';
    if (totalValoresBadge) {
        totalValoresBadge.innerHTML = `${todosLosValores.length} ${todosLosValores.length === 1 ? 'valor' : 'valores'}`;
    }
    
    // Limpiar contenido actual
    navTabs.innerHTML = '';
    tabContent.innerHTML = '';
    
    // Ordenar valores por atributo y nombre
    todosLosValores.sort((a, b) => {
        if (a.atributoNombre === b.atributoNombre) {
            return a.nombre.localeCompare(b.nombre);
        }
        return a.atributoNombre.localeCompare(b.atributoNombre);
    });
    
    // Crear pestañas y contenido para cada valor
    todosLosValores.forEach((valor, index) => {
        const valorId = valor.id;
        const valorKey = `${valor.atributoId}_${valorId}`;
        
        // Crear pestaña
        const tabItem = document.createElement('li');
        tabItem.className = 'nav-item';
        tabItem.role = 'presentation';
        tabItem.innerHTML = `
            <button 
                class="nav-link ${index === 0 ? 'active' : ''}" 
                id="valor-tab-${valorKey}" 
                data-bs-toggle="tab" 
                data-bs-target="#valor-content-${valorKey}" 
                type="button" 
                role="tab"
                data-valor-id="${valorId}"
                data-atributo-id="${valor.atributoId}">
                <i class="fas fa-cube me-1"></i>
                ${valor.atributoNombre}: ${valor.nombre}
                <span class="badge">${valor.atributoNombre}</span>
            </button>
        `;
        navTabs.appendChild(tabItem);
        
        // Crear contenido de pestaña - FORMULARIO DE VARIACIÓN
        const contentPane = document.createElement('div');
        contentPane.className = `tab-pane fade ${index === 0 ? 'show active' : ''}`;
        contentPane.id = `valor-content-${valorKey}`;
        contentPane.role = 'tabpanel';
        
        // Generar SKU sugerido
        const productoSku = document.getElementById('vCodigo_barras')?.value || 'PROD';
        const combinacion = [{
            atributoId: valor.atributoId,
            atributoNombre: valor.atributoNombre,
            valorId: valor.id,
            valorNombre: valor.nombre
        }];
        const skuSugerido = generarSkuSugerido(productoSku, combinacion);
        
        // Verificar si ya existe una variación guardada para este valor
        const variacionGuardada = obtenerVariacionGuardada(valor.atributoId, valor.id);
        
        contentPane.innerHTML = `
            <!-- ========================================= -->
            <!-- FORMULARIO DE VARIACIÓN POR VALOR        -->
            <!-- ========================================= -->
            <div class="variacion-form-container">
                <!-- Header informativo -->
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

                <!-- Campos ocultos para identificación -->
                <input type="hidden" name="variaciones[${valorKey}][id_atributo]" value="${valor.atributoId}">
                <input type="hidden" name="variaciones[${valorKey}][id_atributo_valor]" value="${valor.id}">
                <input type="hidden" name="variaciones[${valorKey}][vNombre_variacion]" 
                       value="${valor.atributoNombre}: ${valor.nombre}">

                <!-- Fila 1: SKU y Estado -->
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
                                       value="${variacionGuardada?.sku || skuSugerido}"
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
                                       ${variacionGuardada?.activo !== false ? 'checked' : ''}>
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

                <!-- Fila 2: Precios y Stock -->
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
                                       value="${variacionGuardada?.precio || ''}"
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
                                       value="${variacionGuardada?.stock || '0'}"
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
                                <option value="estandar" ${variacionGuardada?.claseEnvio === 'estandar' ? 'selected' : ''}>Estándar</option>
                                <option value="express" ${variacionGuardada?.claseEnvio === 'express' ? 'selected' : ''}>Express</option>
                                <option value="fragil" ${variacionGuardada?.claseEnvio === 'fragil' ? 'selected' : ''}>Frágil</option>
                                <option value="grandes_dimensiones" ${variacionGuardada?.claseEnvio === 'grandes_dimensiones' ? 'selected' : ''}>Grandes dimensiones</option>
                            </select>
                            <small class="form-text text-muted">Dejar vacío para heredar del producto</small>
                        </div>
                    </div>
                </div>

                <!-- Fila 3: Dimensiones y Peso -->
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
                                   value="${variacionGuardada?.peso || ''}"
                                   oninput="validarDecimal(this, 3)"
                                   placeholder="0.000"
                                   title="Máximo: 999.999 kg"
                                   autocomplete="off">
                            <small class="form-text text-muted">Opcional</small>
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
                                   value="${variacionGuardada?.largo || ''}"
                                   oninput="validarDecimal(this, 2)"
                                   placeholder="0.00"
                                   title="Máximo: 999.99 cm"
                                   autocomplete="off">
                            <small class="form-text text-muted">Opcional</small>
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
                                   value="${variacionGuardada?.ancho || ''}"
                                   oninput="validarDecimal(this, 2)"
                                   placeholder="0.00"
                                   title="Máximo: 999.99 cm"
                                   autocomplete="off">
                            <small class="form-text text-muted">Opcional</small>
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
                                   value="${variacionGuardada?.alto || ''}"
                                   oninput="validarDecimal(this, 2)"
                                   placeholder="0.00"
                                   title="Máximo: 999.99 cm"
                                   autocomplete="off">
                            <small class="form-text text-muted">Opcional</small>
                        </div>
                    </div>
                </div>

                <!-- Fila 4: Oferta para variación -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="card bg-light border">
                            <div class="card-body py-2">
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <div class="form-check form-switch">
                                            <input type="checkbox" 
                                                   name="variaciones[${valorKey}][bTiene_oferta]" 
                                                   id="oferta-${valorKey}" 
                                                   class="form-check-input" 
                                                   value="1"
                                                   ${variacionGuardada?.tieneOferta ? 'checked' : ''}
                                                   onchange="document.querySelector('.oferta-fields-${valorKey}').style.display = this.checked ? 'flex' : 'none'">
                                            <label class="form-check-label fw-bold" for="oferta-${valorKey}">
                                                <i class="fas fa-percentage me-1"></i>
                                                Activar oferta para esta variación
                                            </label>
                                        </div>
                                        <small class="text-muted d-block mt-1">
                                            Precio especial por tiempo limitado
                                        </small>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="row oferta-fields-${valorKey}" 
                                             style="display: ${variacionGuardada?.tieneOferta ? 'flex' : 'none'};">
                                            <div class="col-md-4 mb-2 mb-md-0">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">$</span>
                                                    <input type="text" 
                                                           name="variaciones[${valorKey}][dPrecio_oferta]" 
                                                           class="form-control"
                                                           value="${variacionGuardada?.precioOferta || ''}"
                                                           oninput="validarPrecio(this)"
                                                           placeholder="Precio oferta"
                                                           autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-2 mb-md-0">
                                                <input type="date" 
                                                       name="variaciones[${valorKey}][dFecha_inicio_oferta]" 
                                                       class="form-control form-control-sm"
                                                       value="${variacionGuardada?.fechaInicio || ''}"
                                                       autocomplete="off">
                                            </div>
                                            <div class="col-md-3 mb-2 mb-md-0">
                                                <input type="date" 
                                                       name="variaciones[${valorKey}][dFecha_fin_oferta]" 
                                                       class="form-control form-control-sm"
                                                       value="${variacionGuardada?.fechaFin || ''}"
                                                       autocomplete="off">
                                            </div>
                                            <div class="col-md-2">
                                                <input type="text" 
                                                       name="variaciones[${valorKey}][vMotivo_oferta]" 
                                                       class="form-control form-control-sm"
                                                       value="${variacionGuardada?.motivoOferta || ''}"
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

                <!-- Fila 5: Descripción e Imagen -->
                <div class="row mb-3">
                    <div class="col-md-9">
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
                                      autocomplete="off">${variacionGuardada?.descripcion || ''}</textarea>
                            <small class="form-text text-muted">
                                Máximo 500 caracteres. Descripción específica para esta variación.
                            </small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="imagen-${valorKey}" class="form-label fw-bold">
                                Imagen específica
                            </label>
                            <input type="file" 
                                   name="variaciones[${valorKey}][vImagen]" 
                                   id="imagen-${valorKey}" 
                                   class="form-control form-control-sm"
                                   accept="image/*"
                                   autocomplete="off">
                            <small class="form-text text-muted">
                                Opcional. Máx 2MB. JPG, PNG, WEBP
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <hr class="my-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-secondary p-2">
                                    <i class="fas fa-check-circle me-1"></i>
                                    Configuración completada
                                </span>
                                <span class="badge bg-light text-dark p-2 ms-2">
                                    <i class="fas fa-barcode me-1"></i>
                                    SKU: <span id="sku-preview-${valorKey}">${variacionGuardada?.sku || skuSugerido}</span>
                                </span>
                            </div>
                            <div>
                                <button type="button" 
                                        class="btn btn-sm btn-outline-danger" 
                                        onclick="eliminarValorDeAtributo(${valor.atributoId}, ${valor.id})">
                                    <i class="fas fa-trash-alt me-1"></i>
                                    Quitar variación
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        tabContent.appendChild(contentPane);
    });
}

// Función para obtener variación guardada
function obtenerVariacionGuardada(atributoId, valorId) {
    const inputs = document.querySelectorAll(`input[name*="[vSKU]"][data-atributo-id="${atributoId}"][data-valor-id="${valorId}"]`);
    if (inputs.length > 0) {
        const input = inputs[0];
        const form = input.closest('.variacion-form-container');
        
        if (form) {
            const precioInput = form.querySelector('input[name*="[dPrecio]"]');
            const stockInput = form.querySelector('input[name*="[iStock]"]');
            const activoCheckbox = form.querySelector('input[name*="[bActivo]"]');
            const ofertaCheckbox = form.querySelector('input[name*="[bTiene_oferta]"]');
            const precioOfertaInput = form.querySelector('input[name*="[dPrecio_oferta]"]');
            const fechaInicioInput = form.querySelector('input[name*="[dFecha_inicio_oferta]"]');
            const fechaFinInput = form.querySelector('input[name*="[dFecha_fin_oferta]"]');
            const motivoInput = form.querySelector('input[name*="[vMotivo_oferta]"]');
            const descripcionTextarea = form.querySelector('textarea[name*="[tDescripcion]"]');
            const claseEnvioSelect = form.querySelector('select[name*="[vClase_envio]"]');
            const pesoInput = form.querySelector('input[name*="[dPeso]"]');
            const largoInput = form.querySelector('input[name*="[dLargo_cm]"]');
            const anchoInput = form.querySelector('input[name*="[dAncho_cm]"]');
            const altoInput = form.querySelector('input[name*="[dAlto_cm]"]');
            
            return {
                sku: input.value,
                precio: precioInput?.value,
                stock: stockInput?.value,
                activo: activoCheckbox?.checked,
                tieneOferta: ofertaCheckbox?.checked,
                precioOferta: precioOfertaInput?.value,
                fechaInicio: fechaInicioInput?.value,
                fechaFin: fechaFinInput?.value,
                motivoOferta: motivoInput?.value,
                descripcion: descripcionTextarea?.value,
                claseEnvio: claseEnvioSelect?.value,
                peso: pesoInput?.value,
                largo: largoInput?.value,
                ancho: anchoInput?.value,
                alto: altoInput?.value
            };
        }
    }
    return null;
}

// Función para generar SKU sugerido
function generarSkuSugerido(productoSku, combinacion) {
    let sku = productoSku || 'PROD';
    combinacion.forEach(item => {
        const attrCode = item.atributoNombre.substring(0, 3).toUpperCase();
        const valCode = item.valorNombre.replace(/[^a-zA-Z0-9]/g, '').substring(0, 3).toUpperCase();
        sku += `-${attrCode}${valCode}`;
    });
    return sku;
}

// Eliminar valor de atributo desde pestañas
function eliminarValorDeAtributo(atributoId, valorId) {
    Swal.fire({
        title: '¿Eliminar variación?',
        text: 'Esta acción eliminará la variación y todos sus datos configurados.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Desmarcar checkbox
            const checkbox = document.getElementById(`valor-${valorId}`);
            if (checkbox) {
                checkbox.checked = false;
                checkbox.dispatchEvent(new Event('change'));
            }
            
            Swal.fire(
                'Eliminado',
                'La variación ha sido eliminada.',
                'success'
            );
        }
    });
}

// ============================================
// FUNCIONES PARA FORMULARIOS RÁPIDOS
// ============================================
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
    if (!nombre) return;
    let slug = nombre.toLowerCase();
    slug = slug.replace(/á/gi, 'a');
    slug = slug.replace(/é/gi, 'e');
    slug = slug.replace(/í/gi, 'i');
    slug = slug.replace(/ó/gi, 'o');
    slug = slug.replace(/ú/gi, 'u');
    slug = slug.replace(/ñ/gi, 'n');
    slug = slug.replace(/[^a-z0-9]+/g, '-');
    slug = slug.replace(/^-+/, '').replace(/-+$/, '');
    document.getElementById(slugId).value = slug;
}

function limpiarFormularioMarca() {
    document.getElementById('vNombre_marca').value = '';
    document.getElementById('tDescripcion_marca').value = '';
}

// ============================================
// FUNCIONES AJAX PARA FORMULARIOS RÁPIDOS
// ============================================
function crearCategoria() {
    const nombre = document.getElementById('vNombre_cat').value.trim();
    const slug = document.getElementById('vSlug_cat').value.trim();
    const padre = document.getElementById('id_categoria_padre_cat').value;
    const descripcion = document.getElementById('tDescripcion_cat').value.trim();
    const activa = document.getElementById('bActivo_cat').checked ? 1 : 0;
    
    if (!nombre) {
        Swal.fire('Error', 'El nombre de la categoría es obligatorio', 'error');
        return;
    }
    
    if (!slug) {
        Swal.fire('Error', 'El slug de la categoría es obligatorio', 'error');
        return;
    }
    
    Swal.fire({
        title: 'Creando categoría...',
        text: 'Por favor espera',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });
    
    $.ajax({
        url: '{{ route("categorias.quick-create") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            vNombre: nombre,
            vSlug: slug,
            id_categoria_padre: padre || '',
            tDescripcion: descripcion,
            bActivo: activa
        },
        success: function(response) {
            Swal.close();
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });
                
                document.getElementById('quick-categoria-form').reset();
                
                const select = document.getElementById('id_categoria');
                const option = document.createElement('option');
                option.value = response.categoria.id_categoria;
                let prefijo = padre ? '↳ ' : '🏠 ';
                option.text = prefijo + nombre;
                select.appendChild(option);
                select.value = response.categoria.id_categoria;
                
                const optionPadre = document.createElement('option');
                optionPadre.value = response.categoria.id_categoria;
                optionPadre.text = (padre ? prefijo : '') + nombre;
                document.getElementById('id_categoria_padre_cat').appendChild(optionPadre);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'Error al crear la categoría'
                });
            }
        },
        error: function(xhr) {
            Swal.close();
            let message = 'Error en la solicitud';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                const errors = Object.values(xhr.responseJSON.errors).flat();
                message = errors.join(', ');
            }
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message
            });
        }
    });
}

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
    
    $.ajax({
        url: '{{ route("marcas.quick-create") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            vNombre: nombre,
            tDescripcion: descripcion
        },
        success: function(response) {
            Swal.close();
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });
                
                limpiarFormularioMarca();
                
                const select = document.getElementById('id_marca');
                const option = document.createElement('option');
                option.value = response.marca.id_marca;
                option.text = nombre;
                select.appendChild(option);
                select.value = response.marca.id_marca;
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'Error al crear la marca'
                });
            }
        },
        error: function(xhr) {
            Swal.close();
            let message = 'Error en la solicitud';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                const errors = Object.values(xhr.responseJSON.errors).flat();
                message = errors.join(', ');
            }
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message
            });
        }
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
    
    $.ajax({
        url: '{{ route("etiquetas.quick-create") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            vNombre: nombre,
            color: color,
            tDescripcion: descripcion
        },
        success: function(response) {
            Swal.close();
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });
                
                document.getElementById('quick-etiqueta-form').reset();
                document.getElementById('color_text_eti').value = '#007bff';
                document.getElementById('color_eti').value = '#007bff';
                
                agregarEtiquetaAlFormulario(response.etiqueta);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'Error al crear la etiqueta'
                });
            }
        },
        error: function(xhr) {
            Swal.close();
            let message = 'Error en la solicitud';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                const errors = Object.values(xhr.responseJSON.errors).flat();
                message = errors.join(', ');
            }
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message
            });
        }
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
    
    $.ajax({
        url: '{{ route("atributos.quick-create") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            vNombre: nombre,
            vSlug: slug,
            tDescripcion: descripcion
        },
        success: function(response) {
            Swal.close();
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });
                
                document.getElementById('quick-atributo-form').reset();
                
                // Recargar la página después de crear el atributo
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'Error al crear el atributo'
                });
            }
        },
        error: function(xhr) {
            Swal.close();
            let message = 'Error en la solicitud';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                const errors = Object.values(xhr.responseJSON.errors).flat();
                message = errors.join(', ');
            }
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message
            });
        }
    });
}

function agregarEtiquetaAlFormulario(etiqueta) {
    const container = document.querySelector('.form-group:has(.row) .row');
    const col = document.createElement('div');
    col.className = 'col-md-3 col-6 mb-2';
    col.innerHTML = `
        <div class="form-check">
            <input type="checkbox" 
                   name="etiquetas[]" 
                   value="${etiqueta.id_etiqueta}" 
                   class="form-check-input"
                   id="etiqueta_${etiqueta.id_etiqueta}">
            <label class="form-check-label" for="etiqueta_${etiqueta.id_etiqueta}">
                <span class="etiqueta-badge" style="background-color: ${etiqueta.color || '#007bff'}; color: white;">
                    ${etiqueta.vNombre}
                </span>
            </label>
        </div>
    `;
    container.appendChild(col);
}

function activarTabAtributos() {
    const tab = document.getElementById('atributos-tab');
    if (tab) {
        tab.click();
        tab.scrollIntoView({ behavior: 'smooth' });
    }
}

// ============================================
// EVENT LISTENERS INICIALES
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    // Mostrar campos de oferta si ya estaba activado
    if (document.getElementById('bTiene_oferta')) {
        if (document.getElementById('bTiene_oferta').checked) {
            toggleOfertaFields();
        }
    }
    
    // Inicializar estados de checkboxes
    document.querySelectorAll('.atributo-activo-checkbox').forEach(checkbox => {
        if (checkbox.checked) {
            checkbox.dispatchEvent(new Event('change'));
        }
    });
    
    // Inicializar variaciones
    renderSelectedImages();
    actualizarResumenAtributos();
    actualizarPestanasValores();
    
    // Sincronizar color picker
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
});

// ============================================
// VALIDACIÓN DEL FORMULARIO PRINCIPAL
// ============================================
document.getElementById('productoForm').addEventListener('submit', function(e) {
    const btnSubmit = document.getElementById('btnSubmit');
    
    // Validar precio de oferta si está activado
    if (document.getElementById('bTiene_oferta') && document.getElementById('bTiene_oferta').checked) {
        const precioVenta = parseFloat(document.getElementById('dPrecio_venta').value) || 0;
        const precioOferta = parseFloat(document.getElementById('dPrecio_oferta').value) || 0;
        
        if (precioOferta >= precioVenta) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error en precio de oferta',
                text: 'El precio de oferta debe ser menor al precio de venta'
            });
            document.getElementById('dPrecio_oferta').focus();
            return false;
        }
    }
    
    // Validar fechas de oferta
    if (document.getElementById('bTiene_oferta') && document.getElementById('bTiene_oferta').checked) {
        const fechaInicio = document.getElementById('dFecha_inicio_oferta').value;
        const fechaFin = document.getElementById('dFecha_fin_oferta').value;
        
        if (fechaInicio && fechaFin && new Date(fechaFin) < new Date(fechaInicio)) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error en fechas de oferta',
                text: 'La fecha de fin debe ser posterior a la fecha de inicio'
            });
            return false;
        }
    }
    
    // Validar que todas las variaciones tengan SKU
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
    
    // Actualizar input de archivos
    updateFileInput();
    
    // Cambiar texto del botón
    if (btnSubmit) {
        btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Guardando...';
        btnSubmit.disabled = true;
    }
    
    return true;
});

// Remover clases de error al escribir
document.querySelectorAll('input, select, textarea').forEach(elemento => {
    elemento.addEventListener('input', function() {
        this.classList.remove('is-invalid');
    });
});

// Prevenir comportamiento por defecto de botones dentro del formulario
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