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

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('post_max_size_error'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>¡Atención!</strong> El tamaño total de los archivos excede el límite del servidor (50MB). Por favor, reduce el tamaño de los archivos.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
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
                            <div class="position-relative">
                                <input type="text" name="vCodigo_barras" id="vCodigo_barras" 
                                       class="form-control @error('vCodigo_barras') is-invalid @enderror"
                                       value="{{ old('vCodigo_barras') }}" 
                                       maxlength="15" 
                                       required
                                       oninput="validarSKU(this); verificarSKUProductoLocal(this)"
                                       pattern="[A-Za-z0-9]+"
                                       title="Solo letras y números (máximo 15 caracteres)"
                                       data-original-value="{{ old('vCodigo_barras') }}"
                                       autocomplete="off">
                                <div id="sku-error" class="invalid-feedback" style="display: none;"></div>
                            </div>
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
                            <div class="position-relative">
                                <input type="text" name="vNombre" id="vNombre" 
                                       class="form-control @error('vNombre') is-invalid @enderror" 
                                       value="{{ old('vNombre') }}" 
                                       maxlength="100" 
                                       required
                                       autocomplete="off"
                                       data-original-value="{{ old('vNombre') }}"
                                       oninput="verificarNombreProductoLocal(this)">
                                <div id="nombre-error" class="invalid-feedback" style="display: none;"></div>
                            </div>
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
                                   pattern="[0-9]{1,6}"
                                   title="Máximo 6 dígitos (0-999999)"
                                   inputmode="numeric"
                                   min="0"
                                   max="999999"
                                   autocomplete="off">
                            @error('iStock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Máximo 999,999 unidades</small>
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
                <div id="descuentoFields" style="display: {{ old('bTiene_descuento') ? 'block' : 'none' }};">
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
                                           oninput="validarPrecio(this); validarPrecioDescuentoProducto(); actualizarPrecioFinal();"
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
                                       onchange="validarFechasDescuento()"
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
                                       onchange="validarFechasDescuento()"
                                       autocomplete="off">
                                <div id="error-fechas-descuento" class="invalid-feedback" style="display: none;"></div>
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

        <!-- CATEGORÍA, MARCA E IMPUESTO (CON BOTONES DE CREACIÓN RÁPIDA) -->
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
                            <div class="input-group">
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
                                <button type="button" class="btn btn-outline-primary" onclick="abrirModalCategoria()">
                                    <i class="fas fa-plus-circle"></i>
                                </button>
                            </div>
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
                            <div class="input-group">
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
                                <button type="button" class="btn btn-outline-primary" onclick="abrirModalMarca()">
                                    <i class="fas fa-plus-circle"></i>
                                </button>
                            </div>
                            @error('id_marca')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                   <!-- SECCIÓN DE IMPUESTO (IVA e IEPS) -->
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="id_impuesto" class="form-label fw-bold">
                                <i class="fas fa-file-invoice-dollar me-1"></i>Impuesto Aplicable
                            </label>
                            <div class="input-group">
                                @if(isset($impuestos) && $impuestos->count() > 0)
                                    <select name="id_impuesto" id="id_impuesto" 
                                            class="form-select @error('id_impuesto') is-invalid @enderror"
                                            onchange="actualizarPrecioFinal()">
                                        <option value="">-- Sin impuesto --</option>
                                        @foreach($impuestos as $impuesto)
                                            <option value="{{ $impuesto->id_impuesto }}" 
                                                data-porcentaje="{{ $impuesto->dPorcentaje }}"
                                                data-tipo="{{ $impuesto->eTipo }}"
                                                data-nombre="{{ $impuesto->vNombre }}"
                                                {{ old('id_impuesto') == $impuesto->id_impuesto ? 'selected' : '' }}>
                                                {{ $impuesto->vNombre }} ({{ $impuesto->dPorcentaje }}%)
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-outline-primary" onclick="abrirModalImpuesto()">
                                        <i class="fas fa-plus-circle"></i>
                                    </button>
                                @else
                                    <select name="id_impuesto" id="id_impuesto" 
                                            class="form-select @error('id_impuesto') is-invalid @enderror">
                                        <option value="">-- Sin impuesto --</option>
                                    </select>
                                    <button type="button" class="btn btn-outline-primary" onclick="abrirModalImpuesto()">
                                        <i class="fas fa-plus-circle"></i>
                                    </button>
                                    <div class="alert alert-warning mt-2 w-100">
                                        <small>No hay impuestos configurados. Crea uno nuevo.</small>
                                    </div>
                                @endif
                            </div>
                            @error('id_impuesto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted mt-2">
                                Selecciona el impuesto que aplica a este producto (IVA o IEPS)
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- IMAGEN PRINCIPAL, GIF E IMÁGENES ADICIONALES DEL PRODUCTO -->
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="fas fa-images me-2"></i>Multimedia del Producto Principal</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning" id="limiteArchivosMsg" style="display: none;">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>¡Atención!</strong> Has excedido el límite de tamaño total de archivos (50MB).
                </div>

                <!-- Barra de progreso de tamaño total -->
                <div class="alert alert-info py-2 mb-3">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <i class="fas fa-camera me-1"></i>
                            <strong>Total de archivos multimedia:</strong> 
                            <span id="total-imagenes">0</span> archivos
                        </div>
                        <div class="col-md-6">
                            <strong>Tamaño total:</strong>
                            <span id="total-size">0 KB</span>
                            <span class="text-muted ms-2">(Máx: 50MB)</span>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <div class="progress" style="height: 8px;">
                                <div id="size-progress-bar" class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- IMAGEN PRINCIPAL -->
                    <div class="col-md-6">
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
                    
                    <!-- GIF DEL PRODUCTO (OPCIONAL) -->
                    <div class="col-md-6">
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

        <!-- DESCRIPCIÓN Y ETIQUETAS (CON BOTÓN DE CREACIÓN RÁPIDA) -->
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
                    
                    <div class="col-md-6" id="etiquetas-container">
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Etiquetas (Opcional)</label>
                            <div class="row">
                                @if(isset($etiquetas) && $etiquetas->count() > 0)
                                    @foreach ($etiquetas as $etiqueta)
                                        <div class="col-md-6 col-6 mb-2 etiqueta-item" data-etiqueta-id="{{ $etiqueta->id_etiqueta }}">
                                            <div class="form-check">
                                                <input type="checkbox" 
                                                       name="etiquetas[]" 
                                                       value="{{ $etiqueta->id_etiqueta }}" 
                                                       class="form-check-input"
                                                       {{ is_array(old('etiquetas')) && in_array($etiqueta->id_etiqueta, old('etiquetas', [])) ? 'checked' : '' }}
                                                       id="etiqueta_{{ $etiqueta->id_etiqueta }}">
                                                <label class="form-check-label" for="etiqueta_{{ $etiqueta->id_etiqueta }}">
                                                    <span class="badge bg-secondary">
                                                        {{ $etiqueta->vNombre }}
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="col-12" id="no-etiquetas-msg">
                                        <div class="alert alert-info py-2">
                                            <i class="fas fa-info-circle me-1"></i>
                                            No hay etiquetas disponibles.
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="abrirModalEtiqueta()">
                                    <i class="fas fa-plus-circle me-1"></i> Crear Nueva Etiqueta
                                </button>
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
                <h5 class="mb-0"><i class="fas fa-calculator me-2"></i>Precio Final</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card bg-white text-dark">
                            <div class="card-body text-center">
                                <h6 class="text-muted">Precio base</h6>
                                <h3 class="fw-bold" id="precio-base-display">$0.00</h3>
                                <small class="text-muted" id="precio-original-display" style="display: none;"></small>
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
                                <h6>Total final</h6>
                                <h2 class="fw-bold" id="precio-final-display">$0.00</h2>
                                <small id="tipo-operacion">Precio final</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Información detallada -->
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="alert alert-light text-dark p-3">
                            <i class="fas fa-calculator me-2"></i>
                            <div id="isr-info">
                                Selecciona un impuesto para ver el cálculo
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Nota explicativa -->
                <div class="row mt-2">
                    <div class="col-12">
                        <div class="alert alert-warning text-dark p-2 small">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Resumen:</strong><br>
                            • <strong>IVA</strong>: Se SUMA al precio base<br>
                            • <strong>IEPS</strong>: Se SUMA al precio base
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ATRIBUTOS DEL PRODUCTO (SELECCIÓN Y CREACIÓN DE VALORES) CON BOTÓN PARA CREAR ATRIBUTO -->
        <div class="card mb-4">
            <div class="card-header" style="background-color: #45c973ff; color: white;">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-tags me-2"></i>Seleccionar Atributos para Variaciones</h5>
                    <button type="button" class="btn btn-light btn-sm" onclick="abrirModalAtributo()">
                        <i class="fas fa-plus-circle me-1"></i> Crear Nuevo Atributo
                    </button>
                </div>
            </div>
            <div class="card-body" style="background-color: #f8f9fa;">
                <div class="alert alert-info mb-4" style="color: #0c5460; background-color: #d1ecf1; border-color: #bee5eb;">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong style="color: #0c5460;">Instrucciones:</strong> 
                    <span style="color: #0c5460;">Marca los atributos que deseas activar y selecciona los valores correspondientes. También puedes crear nuevos valores para atributos existentes haciendo clic en "Agregar Valor".</span>
                </div>
                
                @if(isset($atributos) && $atributos->count() > 0)
                    <div class="row" id="atributos-container">
                        @foreach($atributos as $atributo)
                        <div class="col-md-6 mb-4 atributo-item" data-atributo-id="{{ $atributo->id_atributo }}">
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
                    <div class="text-center py-5" id="no-atributos-msg">
                        <i class="fas fa-tags fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">No hay atributos disponibles</h4>
                        <p class="text-muted">Crea atributos en el panel de herramientas</p>
                        <button type="button" class="btn btn-primary mt-3" onclick="abrirModalAtributo()">
                            <i class="fas fa-plus-circle me-2"></i> Crear Atributo
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- VARIACIONES DEL PRODUCTO - PESTAÑAS POR VALOR CON IMÁGENES MÚLTIPLES -->
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
                        
                        <!-- Contenido de las pestañas - Formularios de variación con imágenes -->
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

    <!-- MODALES PARA CREACIÓN RÁPIDA -->
    
    <!-- MODAL PARA CREAR CATEGORÍA (CON IMAGEN) -->
    <div class="modal fade" id="modalCategoria" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-tags me-2"></i>Crear Nueva Categoría
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="categoriaModalForm" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="vNombre_categoria_modal" class="form-label fw-bold">Nombre de la Categoría *</label>
                            <input type="text" class="form-control" 
                                   id="vNombre_categoria_modal" name="vNombre" 
                                   required
                                   placeholder="Ej: Tequila, Mezcal, Añejos..."
                                   oninput="generarSlugCategoria(this.value)">
                            <small class="form-text text-muted">Nombre descriptivo para la categoría</small>
                        </div>

                        <div class="mb-3">
                            <label for="vSlug_categoria_modal" class="form-label fw-bold">Slug (URL amigable) *</label>
                            <input type="text" class="form-control" 
                                   id="vSlug_categoria_modal" name="vSlug" 
                                   required
                                   placeholder="tequila-reposado">
                            <small class="form-text text-muted">
                                URL para la categoría. Se genera automáticamente.
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="id_categoria_padre_modal" class="form-label fw-bold">Categoría Padre</label>
                            <select class="form-control" id="id_categoria_padre_modal" name="id_categoria_padre">
                                <option value="">-- Seleccionar Categoría Padre (Opcional) --</option>
                                @php
                                    function mostrarCategoriasModal($categorias, $nivel = 0) {
                                        foreach($categorias as $categoria) {
                                            $prefijo = str_repeat('&nbsp;&nbsp;&nbsp;', $nivel);
                                            $icono = $nivel == 0 ? '🏠 ' : '↳ ';
                                            echo '<option value="' . $categoria->id_categoria . '">' .
                                                 $prefijo . $icono . htmlspecialchars($categoria->vNombre) . 
                                                 '</option>';
                                            
                                            if ($categoria->hijos && $categoria->hijos->count() > 0) {
                                                mostrarCategoriasModal($categoria->hijos, $nivel + 1);
                                            }
                                        }
                                    }
                                    
                                    $categoriasRaiz = $categorias->where('id_categoria_padre', null)->where('bActivo', true);
                                @endphp
                                
                                @php mostrarCategoriasModal($categoriasRaiz, 0); @endphp
                            </select>
                            <small class="form-text text-muted">Selecciona si esta categoría pertenece a otra</small>
                        </div>

                        <div class="mb-3">
                            <label for="tDescripcion_categoria_modal" class="form-label fw-bold">Descripción</label>
                            <textarea class="form-control" 
                                      id="tDescripcion_categoria_modal" name="tDescripcion" rows="3"
                                      placeholder="Describe la categoría..."></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Imagen de la Categoría</label>
                            
                            <!-- Preview de imagen -->
                            <div class="mb-3" id="categoriaModalImagePreview" style="display: none;">
                                <div class="border rounded p-3 text-center">
                                    <img id="categoriaModalPreviewImg" src="#" 
                                         class="img-thumbnail" 
                                         style="max-width: 150px; max-height: 150px; object-fit: cover;"
                                         alt="Preview">
                                    <div class="mt-2">
                                        <small class="text-muted d-block">Vista previa</small>
                                        <button type="button" class="btn btn-sm btn-outline-danger mt-1" onclick="cancelarImagenCategoriaModal()">
                                            <i class="fas fa-times me-1"></i>Cancelar imagen
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <input type="file" class="form-control" 
                                   id="vImagen_categoria_modal" name="vImagen"
                                   accept="image/jpeg,image/jpg,image/png,image/webp"
                                   onchange="previewImagenCategoriaModal(this)">
                            <small class="form-text text-muted">
                                Formatos: JPG, JPEG, PNG, WebP. Tamaño máximo: 2MB. La imagen es opcional.
                            </small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" 
                                       id="bActivo_categoria_modal" name="bActivo" value="1" checked>
                                <label class="form-check-label" for="bActivo_categoria_modal">
                                    Categoría activa
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" onclick="guardarCategoria()">
                        <i class="fas fa-save me-1"></i> Crear Categoría
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL PARA CREAR MARCA -->
    <div class="modal fade" id="modalMarca" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-industry me-2"></i>Crear Nueva Marca
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="marcaModalForm">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="vNombre_marca_modal" class="form-label fw-bold">Nombre de la Marca *</label>
                            <input type="text" class="form-control" id="vNombre_marca_modal" name="vNombre" 
                                   placeholder="Ej: José Cuervo, Patrón, Don Julio" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="tDescripcion_marca_modal" class="form-label fw-bold">Descripción (Opcional)</label>
                            <textarea class="form-control" id="tDescripcion_marca_modal" name="tDescripcion" rows="3" 
                                      placeholder="Describe la marca..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" onclick="guardarMarca()">
                        <i class="fas fa-save me-1"></i> Crear Marca
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL PARA CREAR ETIQUETA (SIN COLOR) -->
    <div class="modal fade" id="modalEtiqueta" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-tag me-2"></i>Crear Nueva Etiqueta
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="etiquetaModalForm">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="vNombre_eti_modal" class="form-label fw-bold">Nombre de la Etiqueta *</label>
                            <input type="text" class="form-control" id="vNombre_eti_modal" name="vNombre" 
                                   placeholder="Ej: Artesanal, Orgánico, Premium" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="tDescripcion_eti_modal" class="form-label fw-bold">Descripción (Opcional)</label>
                            <textarea class="form-control" id="tDescripcion_eti_modal" name="tDescripcion" rows="2" 
                                      placeholder="Descripción de la etiqueta..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" onclick="guardarEtiqueta()">
                        <i class="fas fa-save me-1"></i> Crear Etiqueta
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL PARA CREAR ATRIBUTO -->
    <div class="modal fade" id="modalAtributo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-list-alt me-2"></i>Crear Nuevo Atributo
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="atributoModalForm">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="vNombre_attr_modal" class="form-label fw-bold">Nombre del Atributo *</label>
                            <input type="text" class="form-control" id="vNombre_attr_modal" name="vNombre" 
                                   placeholder="Ej: Tamaño, Color, Sabor, Edad"
                                   oninput="generarSlugAtributo(this.value)" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="vSlug_attr_modal" class="form-label fw-bold">Slug (URL amigable)</label>
                            <input type="text" class="form-control" id="vSlug_attr_modal" name="vSlug" 
                                   placeholder="tamano, color, material">
                            <small class="form-text text-muted">Se genera automáticamente desde el nombre</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="tDescripcion_attr_modal" class="form-label fw-bold">Descripción (Opcional)</label>
                            <textarea class="form-control" id="tDescripcion_attr_modal" name="tDescripcion" rows="2" 
                                      placeholder="Describe el atributo..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" onclick="guardarAtributo()">
                        <i class="fas fa-save me-1"></i> Crear Atributo
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL PARA CREAR IMPUESTO (IVA e IEPS) -->
    <div class="modal fade" id="modalImpuesto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-file-invoice-dollar me-2"></i>Crear Nuevo Impuesto
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="impuestoModalForm">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="vNombre_impuesto_modal" class="form-label fw-bold">Nombre del Impuesto *</label>
                            <input type="text" class="form-control" id="vNombre_impuesto_modal" name="vNombre" 
                                   placeholder="Ej: IVA, IEPS" value="" required>
                            <small class="text-muted">Ejemplos: IVA, IEPS</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="eTipo_impuesto_modal" class="form-label fw-bold">Tipo de Impuesto *</label>
                            <select class="form-control" id="eTipo_impuesto_modal" name="eTipo" required>
                                <option value="">Seleccionar tipo</option>
                                <option value="IVA">IVA</option>
                                <option value="IEPS">IEPS</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="dPorcentaje_impuesto_modal" class="form-label fw-bold">Porcentaje *</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" max="100" class="form-control" 
                                       id="dPorcentaje_impuesto_modal" name="dPorcentaje" 
                                       placeholder="16.00" value="" required>
                                <span class="input-group-text">%</span>
                            </div>
                            <small class="text-muted">IVA 16%, IEPS 8% (ejemplo)</small>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" 
                                       id="bActivo_impuesto_modal" name="bActivo" value="1" checked>
                                <label class="form-check-label" for="bActivo_impuesto_modal">Activo</label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" onclick="guardarImpuesto()">
                        <i class="fas fa-save me-1"></i> Crear Impuesto
                    </button>
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

/* Estilos para errores de duplicado */
.duplicate-error {
    display: none;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #dc3545;
    animation: fadeIn 0.3s ease-in-out;
    padding: 5px 0;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.position-relative {
    position: relative !important;
}

.is-invalid {
    border-color: #dc3545 !important;
    padding-right: calc(1.5em + 0.75rem) !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e") !important;
    background-repeat: no-repeat !important;
    background-position: right calc(0.375em + 0.1875rem) center !important;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem) !important;
}
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// ============ PASAR DATOS DE LA BASE DE DATOS A JAVASCRIPT ============
const productosExistentes = @json(\App\Models\Producto::select('vCodigo_barras', 'vNombre')->get()->map(function($producto) {
    return [
        'sku' => $producto->vCodigo_barras,
        'nombre' => $producto->vNombre
    ];
})->values());

const variacionesExistentes = @json(\App\Models\ProductoVariacion::select('vSKU')->get()->map(function($variacion) {
    return [
        'sku' => $variacion->vSKU
    ];
})->values());

// Crear sets para búsqueda rápida
const skusExistentes = new Set(productosExistentes.map(p => p.sku));
const nombresExistentes = new Set(productosExistentes.map(p => p.nombre));
const skusVariacionExistentes = new Set(variacionesExistentes.map(v => v.sku));

// ============ VARIABLES GLOBALES ============
let selectedImages = [];
let imageCounter = 0;
let atributosActivos = {};
let imagenPrincipalFile = null;
let gifFile = null;
let variacionCounter = 0;
let valorModal = null;
let maxTotalSize = 50 * 1024 * 1024; // 50MB en bytes
let limiteExcedido = false;

// Variables para imágenes de categoría
let categoriaImagenFile = null;
let categoriaModalImagenFile = null;

// Almacenar imágenes de variaciones por pestaña
let imagenesVariacion = {};

// Variables para modales
let modalCategoria = null;
let modalMarca = null;
let modalEtiqueta = null;
let modalAtributo = null;
let modalImpuesto = null;

// ============ FUNCIONES DE VALIDACIÓN ============

function verificarSKUProductoLocal(input) {
    const sku = input.value.trim();
    const errorDiv = document.getElementById('sku-error');
    const originalValue = input.getAttribute('data-original-value');
    
    if (sku === '' || sku === originalValue) {
        input.classList.remove('is-invalid');
        if (errorDiv) {
            errorDiv.style.display = 'none';
            errorDiv.textContent = '';
        }
        return true;
    }
    
    if (skusExistentes.has(sku)) {
        input.classList.add('is-invalid');
        if (errorDiv) {
            errorDiv.textContent = `⚠️ Ya existe un producto con el SKU "${sku}".`;
            errorDiv.style.display = 'block';
        }
        return false;
    } else {
        input.classList.remove('is-invalid');
        if (errorDiv) {
            errorDiv.style.display = 'none';
            errorDiv.textContent = '';
        }
        return true;
    }
}

function verificarNombreProductoLocal(input) {
    const nombre = input.value.trim();
    const errorDiv = document.getElementById('nombre-error');
    const originalValue = input.getAttribute('data-original-value');
    
    if (nombre === '' || nombre === originalValue) {
        input.classList.remove('is-invalid');
        if (errorDiv) {
            errorDiv.style.display = 'none';
            errorDiv.textContent = '';
        }
        return true;
    }
    
    if (nombresExistentes.has(nombre)) {
        input.classList.add('is-invalid');
        if (errorDiv) {
            errorDiv.textContent = `⚠️ Ya existe un producto con el nombre "${nombre}".`;
            errorDiv.style.display = 'block';
        }
        return false;
    } else {
        input.classList.remove('is-invalid');
        if (errorDiv) {
            errorDiv.style.display = 'none';
            errorDiv.textContent = '';
        }
        return true;
    }
}

function verificarSKUVariacionLocal(input, valorKey) {
    const sku = input.value.trim();
    const container = input.closest('.form-group');
    let errorDiv = container ? container.querySelector('.duplicate-error') : null;
    
    if (!errorDiv && container) {
        errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback duplicate-error';
        container.appendChild(errorDiv);
    }
    
    if (sku === '') {
        input.classList.remove('is-invalid');
        if (errorDiv) {
            errorDiv.style.display = 'none';
            errorDiv.textContent = '';
        }
        return true;
    }
    
    if (skusExistentes.has(sku) || skusVariacionExistentes.has(sku)) {
        input.classList.add('is-invalid');
        if (errorDiv) {
            errorDiv.textContent = `⚠️ Ya existe un producto o variación con el SKU "${sku}".`;
            errorDiv.style.display = 'block';
        }
        return false;
    } else {
        input.classList.remove('is-invalid');
        if (errorDiv) {
            errorDiv.style.display = 'none';
            errorDiv.textContent = '';
        }
        return true;
    }
}

function validarSKU(input) {
    input.value = input.value.replace(/[^A-Za-z0-9-]/g, '');
    if (input.value.length > 50) {
        input.value = input.value.substring(0, 50);
    }
    input.value = input.value.toUpperCase();
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

function validarStock(input) {
    input.value = input.value.replace(/[^0-9]/g, '');
    if (input.value.length > 6) {
        input.value = input.value.substring(0, 6);
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

function validarPeso(input) {
    validarDimension(input, 3, 999.999);
}

function validarDimensionCm(input) {
    validarDimension(input, 2, 999.99);
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

// ============ FUNCIONES DE DESCUENTO ============

function toggleDescuentoFields() {
    const descuentoFields = document.getElementById('descuentoFields');
    const tieneDescuento = document.getElementById('bTiene_descuento').checked;
    const precioDescuento = document.getElementById('dPrecio_descuento');
    const fechaInicio = document.getElementById('dFecha_inicio_descuento');
    const fechaFin = document.getElementById('dFecha_fin_descuento');

    if (tieneDescuento) {
        descuentoFields.style.display = 'block';
        if (precioDescuento) precioDescuento.required = true;
        if (fechaInicio) fechaInicio.required = true;
        if (fechaFin) fechaFin.required = true;

        setTimeout(() => {
            validarPrecioDescuentoProducto();
            validarFechasDescuento();
            actualizarPrecioFinal();
        }, 100);
    } else {
        descuentoFields.style.display = 'none';
        if (precioDescuento) {
            precioDescuento.required = false;
            precioDescuento.classList.remove('is-invalid');
        }
        if (fechaInicio) fechaInicio.required = false;
        if (fechaFin) {
            fechaFin.required = false;
            fechaFin.classList.remove('is-invalid');
        }

        const errorDiv = document.getElementById('error-precio-descuento');
        if (errorDiv) errorDiv.style.display = 'none';

        const errorFechas = document.getElementById('error-fechas-descuento');
        if (errorFechas) errorFechas.style.display = 'none';

        actualizarPrecioFinal();
    }
}

function validarPrecioDescuentoProducto() {
    const tieneDescuentoCheckbox = document.getElementById('bTiene_descuento');
    const precioVentaInput = document.getElementById('dPrecio_venta');
    const precioDescuentoInput = document.getElementById('dPrecio_descuento');
    const errorDiv = document.getElementById('error-precio-descuento');
    
    // Si no hay descuento activado, no validar
    if (!tieneDescuentoCheckbox || !tieneDescuentoCheckbox.checked) {
        if (precioDescuentoInput) {
            precioDescuentoInput.classList.remove('is-invalid');
        }
        if (errorDiv) {
            errorDiv.style.display = 'none';
        }
        return true;
    }
    
    const precioVenta = parseFloat(precioVentaInput?.value) || 0;
    const precioDescuento = parseFloat(precioDescuentoInput?.value) || 0;
    const inputValue = precioDescuentoInput?.value;
    
    let esValido = true;
    
    // Validar que el campo no esté vacío
    if (!inputValue || inputValue === '') {
        esValido = false;
        if (errorDiv) {
            errorDiv.textContent = 'Este campo es obligatorio cuando el descuento está activo.';
        }
    }
    // Validar que el precio de descuento sea menor al precio de venta
    else if (precioDescuento >= precioVenta) {
        esValido = false;
        if (errorDiv) {
            errorDiv.textContent = 'El precio de descuento debe ser menor que el precio de venta.';
        }
    }
    // Validar que el precio de descuento sea mayor a 0
    else if (precioDescuento <= 0) {
        esValido = false;
        if (errorDiv) {
            errorDiv.textContent = 'El precio de descuento debe ser mayor a 0.';
        }
    }
    
    if (!esValido) {
        precioDescuentoInput.classList.add('is-invalid');
        if (errorDiv) {
            errorDiv.style.display = 'block';
        }
    } else {
        precioDescuentoInput.classList.remove('is-invalid');
        if (errorDiv) {
            errorDiv.style.display = 'none';
            errorDiv.textContent = '';
        }
    }
    
    // Forzar actualización del precio final después de validar
    actualizarPrecioFinal();
    
    return esValido;
}

function validarFechasDescuento() {
    const fechaInicio = document.getElementById('dFecha_inicio_descuento');
    const fechaFin = document.getElementById('dFecha_fin_descuento');
    const errorDiv = document.getElementById('error-fechas-descuento');
    
    if (!fechaInicio || !fechaFin) return true;
    
    if (fechaInicio.value && fechaFin.value) {
        const inicio = new Date(fechaInicio.value);
        const fin = new Date(fechaFin.value);
        
        inicio.setHours(0, 0, 0, 0);
        fin.setHours(0, 0, 0, 0);
        
        if (fin < inicio) {
            fechaFin.classList.add('is-invalid');
            if (errorDiv) {
                errorDiv.style.display = 'block';
                errorDiv.textContent = 'La fecha de fin no puede ser anterior a la fecha de inicio';
            }
            return false;
        } else {
            fechaFin.classList.remove('is-invalid');
            if (errorDiv) {
                errorDiv.style.display = 'none';
                errorDiv.textContent = '';
            }
            return true;
        }
    }
    return true;
}

function validarFechasDescuento() {
    const fechaInicio = document.getElementById('dFecha_inicio_descuento');
    const fechaFin = document.getElementById('dFecha_fin_descuento');
    const errorDiv = document.getElementById('error-fechas-descuento');
    
    if (!fechaInicio || !fechaFin) return true;
    
    if (fechaInicio.value && fechaFin.value) {
        const inicio = new Date(fechaInicio.value);
        const fin = new Date(fechaFin.value);
        
        // Comparar solo fechas (sin horas)
        inicio.setHours(0, 0, 0, 0);
        fin.setHours(0, 0, 0, 0);
        
        // PERMITIR que fecha fin sea igual a fecha inicio
        if (fin < inicio) {
            fechaFin.classList.add('is-invalid');
            if (errorDiv) {
                errorDiv.style.display = 'block';
                errorDiv.textContent = 'La fecha de fin no puede ser anterior a la fecha de inicio';
            }
            return false;
        } else {
            fechaFin.classList.remove('is-invalid');
            if (errorDiv) {
                errorDiv.style.display = 'none';
                errorDiv.textContent = '';
            }
            return true;
        }
    }
    return true;
}

function validarPrecioDescuentoVariacionInstantaneo(input) {
    const precioNormalId = input.dataset.precioNormalId;
    const precioNormal = document.getElementById(precioNormalId);
    const valorKey = input.dataset.valorKey;
    const checkbox = document.getElementById(`descuento-${valorKey}`);
    const errorDiv = document.getElementById(`error-precio-descuento-${valorKey}`);
    
    if (!checkbox || !checkbox.checked) return true;
    
    if (precioNormal && input.value) {
        const precioNormalValor = parseFloat(precioNormal.value) || 0;
        const precioDescuentoValor = parseFloat(input.value) || 0;
        
        if (precioDescuentoValor >= precioNormalValor && precioDescuentoValor > 0 && input.value !== '') {
            input.classList.add('is-invalid');
            if (errorDiv) {
                errorDiv.style.display = 'block';
                errorDiv.textContent = 'El precio de descuento debe ser menor que el precio normal';
            }
            return false;
        } else {
            input.classList.remove('is-invalid');
            if (errorDiv) {
                errorDiv.style.display = 'none';
                errorDiv.textContent = '';
            }
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
    const errorDiv = document.getElementById(`error-precio-descuento-${valorKey}`);
    
    if (!checkbox || !checkbox.checked) return true;
    
    if (precioNormal && input.value) {
        const precioNormalValor = parseFloat(precioNormal.value) || 0;
        const precioDescuentoValor = parseFloat(input.value) || 0;
        
        if (precioDescuentoValor >= precioNormalValor && precioDescuentoValor > 0) {
            input.classList.add('is-invalid');
            if (errorDiv) {
                errorDiv.style.display = 'block';
                errorDiv.textContent = 'El precio de descuento debe ser menor que el precio normal';
            }
            return false;
        } else {
            input.classList.remove('is-invalid');
            if (errorDiv) {
                errorDiv.style.display = 'none';
                errorDiv.textContent = '';
            }
            return true;
        }
    }
    return true;
}

function validarFechasDescuentoVariacion(inicioId, finId, valorKey) {
    const fechaInicio = document.getElementById(inicioId);
    const fechaFin = document.getElementById(finId);
    const errorDiv = document.getElementById(`error-fechas-descuento-${valorKey}`);
    
    if (!fechaInicio || !fechaFin) return true;
    
    if (fechaInicio.value && fechaFin.value) {
        const inicio = new Date(fechaInicio.value);
        const fin = new Date(fechaFin.value);
        
        if (fin < inicio) {
            fechaFin.classList.add('is-invalid');
            if (errorDiv) {
                errorDiv.style.display = 'block';
                errorDiv.textContent = 'La fecha de fin no puede ser anterior a la fecha de inicio';
            }
            return false;
        } else {
            fechaFin.classList.remove('is-invalid');
            if (errorDiv) {
                errorDiv.style.display = 'none';
                errorDiv.textContent = '';
            }
            return true;
        }
    }
    return true;
}

function toggleDescuentoVariacion(checkbox, valorKey) {
    const fields = document.querySelector(`.descuento-fields-${valorKey}`);
    const precioDescuento = document.getElementById(`precio_descuento-${valorKey}`);
    const fechaInicio = document.getElementById(`fecha-inicio-${valorKey}`);
    const fechaFin = document.getElementById(`fecha-fin-${valorKey}`);
    
    if (checkbox.checked) {
        if (fields) fields.style.display = 'block';
        if (precioDescuento) precioDescuento.required = true;
        if (fechaInicio) fechaInicio.required = true;
        if (fechaFin) fechaFin.required = true;
        
        // ACTUALIZAR INMEDIATAMENTE DESPUÉS DE ACTIVAR
        setTimeout(() => {
            if (precioDescuento) validarPrecioDescuentoVariacion(precioDescuento);
            actualizarPrecioFinalVariacion(valorKey);  // <--- FORZAR ACTUALIZACIÓN
        }, 50);
    } else {
        if (fields) fields.style.display = 'none';
        if (precioDescuento) {
            precioDescuento.required = false;
            precioDescuento.classList.remove('is-invalid');
        }
        if (fechaInicio) fechaInicio.required = false;
        if (fechaFin) {
            fechaFin.required = false;
            fechaFin.classList.remove('is-invalid');
        }
        
        // ACTUALIZAR INMEDIATAMENTE DESPUÉS DE DESACTIVAR
        actualizarPrecioFinalVariacion(valorKey);
    }
}
// ============ FUNCIÓN DE CÁLCULO DE PRECIO FINAL CORREGIDA ============

function actualizarPrecioFinal() {
    const precioVentaInput = document.getElementById('dPrecio_venta');
    const tieneDescuentoCheckbox = document.getElementById('bTiene_descuento');
    const precioDescuentoInput = document.getElementById('dPrecio_descuento');
    const fechaInicioInput = document.getElementById('dFecha_inicio_descuento');
    const fechaFinInput = document.getElementById('dFecha_fin_descuento');
    const impuestoSelect = document.getElementById('id_impuesto');

    if (!precioVentaInput) return;

    // Obtener valores numéricos
    let precioVenta = parseFloat(precioVentaInput.value) || 0;
    let precioBase = precioVenta;

    // Variable para saber si el descuento está activo HOY
    let descuentoActivoHoy = false;
    let precioDescuento = 0;

    // ========== 1. VERIFICAR DESCUENTO ACTIVO HOY ==========
    if (tieneDescuentoCheckbox && tieneDescuentoCheckbox.checked && precioDescuentoInput) {
        precioDescuento = parseFloat(precioDescuentoInput.value) || 0;
        const fechaInicioStr = fechaInicioInput?.value || '';
        const fechaFinStr = fechaFinInput?.value || '';

        // Solo si el precio de descuento es válido y menor al precio normal
        if (precioDescuento > 0 && precioDescuento < precioVenta) {
            // Obtener fecha actual SIN zona horaria (solo año-mes-día)
            const hoy = new Date();
            const fechaHoy = new Date(hoy.getFullYear(), hoy.getMonth(), hoy.getDate());
            
            let fechaInicio = null;
            let fechaFin = null;
            
            if (fechaInicioStr) {
                const [year, month, day] = fechaInicioStr.split('-').map(Number);
                fechaInicio = new Date(year, month - 1, day);
            }
            
            if (fechaFinStr) {
                const [year, month, day] = fechaFinStr.split('-').map(Number);
                fechaFin = new Date(year, month - 1, day);
                fechaFin.setHours(23, 59, 59, 999);
            }
            
            // Determinar si el descuento aplica HOY
            if (fechaInicio && fechaFin) {
                // Comparar solo fechas (sin horas)
                descuentoActivoHoy = fechaHoy >= fechaInicio && fechaHoy <= fechaFin;
            } else if (fechaInicio && !fechaFin) {
                descuentoActivoHoy = fechaHoy >= fechaInicio;
            } else if (!fechaInicio && fechaFin) {
                descuentoActivoHoy = fechaHoy <= fechaFin;
            } else {
                descuentoActivoHoy = true;
            }
            
            // Aplicar descuento SOLO si está activo HOY
            if (descuentoActivoHoy) {
                precioBase = precioDescuento;
            }
            
            // Debug - para verificar en consola
            console.log('=== VALIDACIÓN DE DESCUENTO ===');
            console.log('Fecha actual (sin hora):', fechaHoy.toISOString().split('T')[0]);
            if (fechaInicio) console.log('Fecha inicio:', fechaInicio.toISOString().split('T')[0]);
            if (fechaFin) console.log('Fecha fin:', fechaFin.toISOString().split('T')[0]);
            console.log('¿Descuento activo hoy?', descuentoActivoHoy);
            console.log('Precio base usado:', precioBase);
        }
    }

    // ========== 2. ACTUALIZAR DISPLAY DEL PRECIO BASE ==========
    const precioBaseDisplay = document.getElementById('precio-base-display');
    if (precioBaseDisplay) {
        precioBaseDisplay.textContent = '$' + precioBase.toFixed(2);
    }

    // ========== 3. MOSTRAR INFO DEL DESCUENTO ==========
    const precioOriginalDisplay = document.getElementById('precio-original-display');
    if (precioOriginalDisplay) {
        if (tieneDescuentoCheckbox && tieneDescuentoCheckbox.checked && precioDescuentoInput?.value && precioDescuento > 0 && precioDescuento < precioVenta) {
            precioOriginalDisplay.style.display = 'block';
            
            if (descuentoActivoHoy) {
                // Calcular porcentaje de descuento
                const porcentajeDesc = Math.round(((precioVenta - precioDescuento) / precioVenta) * 100);
                precioOriginalDisplay.innerHTML = `
                    <div class="alert alert-success p-2 mb-2">
                        <strong>✓ ¡DESCUENTO ACTIVO HOY!</strong><br>
                        <span class="text-decoration-line-through">$${precioVenta.toFixed(2)}</span> → 
                        <strong class="text-danger">$${precioBase.toFixed(2)}</strong>
                        <span class="badge bg-danger ms-2">-${porcentajeDesc}%</span>
                    </div>
                `;
            } else {
                // Descuento programado para el futuro o ya expirado
                let mensaje = '⏱️ Descuento programado';
                let fechaTexto = '';
                
                if (fechaInicioInput?.value && fechaFinInput?.value) {
                    fechaTexto = ` (${fechaInicioInput.value} al ${fechaFinInput.value})`;
                    mensaje = '⏱️ Descuento programado' + fechaTexto;
                } else if (fechaInicioInput?.value) {
                    fechaTexto = ` desde ${fechaInicioInput.value}`;
                    mensaje = '⏱️ Descuento programado' + fechaTexto;
                } else if (fechaFinInput?.value) {
                    fechaTexto = ` hasta ${fechaFinInput.value}`;
                    mensaje = '⏱️ Descuento programado' + fechaTexto;
                }
                
                precioOriginalDisplay.innerHTML = `
                    <div class="alert alert-warning p-2 mb-2">
                        <strong>${mensaje}</strong><br>
                        Precio actual: <strong>$${precioVenta.toFixed(2)}</strong><br>
                        Precio con descuento: $${precioDescuento.toFixed(2)}
                    </div>
                `;
            }
        } else {
            precioOriginalDisplay.style.display = 'none';
        }
    }

    // ========== 4. CALCULAR IMPUESTOS SOBRE EL PRECIO BASE ACTUAL ==========
    let totalImpuesto = 0;
    let porcentaje = 0;
    let nombreImpuesto = '';
    let precioFinal = precioBase;

    if (impuestoSelect && impuestoSelect.value) {
        const selectedOption = impuestoSelect.options[impuestoSelect.selectedIndex];
        porcentaje = parseFloat(selectedOption.dataset.porcentaje) || 0;
        nombreImpuesto = selectedOption.dataset.nombre || selectedOption.text.split('(')[0].trim();

        totalImpuesto = precioBase * (porcentaje / 100);
        precioFinal = precioBase + totalImpuesto;
    }

    // ========== 5. ACTUALIZAR DISPLAY DE IMPUESTOS ==========
    const totalImpuestosDisplay = document.getElementById('total-impuestos-display');
    if (totalImpuestosDisplay) {
        totalImpuestosDisplay.textContent = '+$' + totalImpuesto.toFixed(2);
    }

    const precioFinalDisplay = document.getElementById('precio-final-display');
    if (precioFinalDisplay) {
        precioFinalDisplay.textContent = '$' + precioFinal.toFixed(2);
    }

    const porcentajeImpuestosDisplay = document.getElementById('porcentaje-impuestos-display');
    if (porcentajeImpuestosDisplay) {
        porcentajeImpuestosDisplay.textContent = porcentaje > 0 ? `+${porcentaje.toFixed(2)}% (${nombreImpuesto})` : '0%';
    }

    // ========== 6. ACTUALIZAR DETALLE DE CÁLCULO ==========
    const detalleInfo = document.getElementById('isr-info');
    if (detalleInfo) {
        if (porcentaje > 0) {
            detalleInfo.innerHTML = `
                <strong>Cálculo de ${nombreImpuesto}:</strong><br>
                Precio base: $${precioBase.toFixed(2)}<br>
                ${nombreImpuesto} (${porcentaje}%): +$${totalImpuesto.toFixed(2)}<br>
                <strong>Total: $${precioFinal.toFixed(2)}</strong>
            `;
        } else {
            detalleInfo.innerHTML = `Precio final: $${precioBase.toFixed(2)} (Sin impuestos)`;
        }
    }
    
    console.log('=== CÁLCULO FINAL ===');
    console.log('Precio venta:', precioVenta);
    console.log('Descuento activo hoy:', descuentoActivoHoy);
    console.log('Precio base usado:', precioBase);
    console.log('Impuesto:', porcentaje + '%');
    console.log('Precio final:', precioFinal);
}

function actualizarPrecioFinalVariacion(valorKey) {
    const precioInput = document.getElementById(`precio-${valorKey}`);
    const descuentoCheckbox = document.getElementById(`descuento-${valorKey}`);
    const precioDescuentoInput = document.getElementById(`precio_descuento-${valorKey}`);
    const fechaInicioInput = document.getElementById(`fecha-inicio-${valorKey}`);
    const fechaFinInput = document.getElementById(`fecha-fin-${valorKey}`);
    const impuestoSelect = document.getElementById(`impuesto-${valorKey}`);
    const precioFinalSpan = document.getElementById(`precio-final-${valorKey}`);
    const detalleImpuestoSpan = document.getElementById(`detalle-impuesto-${valorKey}`);
    
    if (!precioInput) return;
    
    let precioNormal = parseFloat(precioInput.value) || 0;
    let precioBase = precioNormal;
    let tieneDescuento = descuentoCheckbox && descuentoCheckbox.checked;
    let precioDescuento = 0;
    let descuentoActivo = false;
    let mensaje = '';
    
    // ========== VERIFICAR SI EL DESCUENTO DEBE APLICAR ==========
    if (tieneDescuento && precioDescuentoInput && precioDescuentoInput.value) {
        precioDescuento = parseFloat(precioDescuentoInput.value) || 0;
        
        if (precioDescuento > 0 && precioDescuento < precioNormal) {
            const fechaInicioStr = fechaInicioInput?.value || '';
            const fechaFinStr = fechaFinInput?.value || '';
            
            // Obtener fecha actual (solo fecha, sin hora)
            const hoy = new Date();
            const fechaHoy = new Date(hoy.getFullYear(), hoy.getMonth(), hoy.getDate());
            
            let fechaInicio = null;
            let fechaFin = null;
            
            if (fechaInicioStr) {
                const [year, month, day] = fechaInicioStr.split('-').map(Number);
                fechaInicio = new Date(year, month - 1, day);
            }
            
            if (fechaFinStr) {
                const [year, month, day] = fechaFinStr.split('-').map(Number);
                fechaFin = new Date(year, month - 1, day);
                fechaFin.setHours(23, 59, 59, 999);
            }
            
            // ========== LÓGICA CORRECTA ==========
            if (fechaInicio && fechaFin) {
                // Caso 1: Tiene ambas fechas
                if (fechaHoy >= fechaInicio && fechaHoy <= fechaFin) {
                    descuentoActivo = true;
                    mensaje = '✓ Descuento activo HOY';
                } else {
                    descuentoActivo = false;
                    mensaje = `⏱️ Descuento programado (${fechaInicioStr} al ${fechaFinStr})`;
                }
            } 
            else if (fechaInicio && !fechaFin) {
                // Caso 2: Solo fecha de inicio
                if (fechaHoy >= fechaInicio) {
                    descuentoActivo = true;
                    mensaje = '✓ Descuento activo HOY';
                } else {
                    descuentoActivo = false;
                    mensaje = `⏱️ Descuento programado desde ${fechaInicioStr}`;
                }
            } 
            else if (!fechaInicio && fechaFin) {
                // Caso 3: Solo fecha de fin
                if (fechaHoy <= fechaFin) {
                    descuentoActivo = true;
                    mensaje = '✓ Descuento activo HOY';
                } else {
                    descuentoActivo = false;
                    mensaje = `⏱️ Descuento programado hasta ${fechaFinStr}`;
                }
            } 
            else {
                // Caso 4: Sin fechas
                descuentoActivo = true;
                mensaje = '✓ Descuento activo';
            }
            
            // SOLO si el descuento está activo, usamos el precio con descuento
            if (descuentoActivo) {
                precioBase = precioDescuento;
            }
        }
    }
    
    // ========== CALCULAR IMPUESTOS ==========
    let totalImpuesto = 0;
    let porcentajeImpuesto = 0;
    let nombreImpuesto = '';
    let precioFinal = precioBase;
    
    if (impuestoSelect && impuestoSelect.value) {
        const selectedOption = impuestoSelect.options[impuestoSelect.selectedIndex];
        porcentajeImpuesto = parseFloat(selectedOption.dataset.porcentaje) || 0;
        nombreImpuesto = selectedOption.dataset.nombre || selectedOption.text.split('(')[0].trim();
        totalImpuesto = precioBase * (porcentajeImpuesto / 100);
        precioFinal = precioBase + totalImpuesto;
    }
    
    // ========== ACTUALIZAR DISPLAY ==========
    if (precioFinalSpan) {
        precioFinalSpan.textContent = '$' + precioFinal.toFixed(2);
    }
    
    if (detalleImpuestoSpan) {
        let html = '';
        if (mensaje) {
            const colorClass = descuentoActivo ? 'text-success' : 'text-warning';
            html += `<span class="${colorClass}">${mensaje}</span><br>`;
        }
        if (porcentajeImpuesto > 0) {
            html += `${nombreImpuesto}: +${porcentajeImpuesto.toFixed(2)}% ($${totalImpuesto.toFixed(2)})`;
        } else {
            html += 'Sin impuesto';
        }
        detalleImpuestoSpan.innerHTML = html;
    }
    
    // Actualizar resumen de precios
    const precioBaseDisplay = document.getElementById(`precio-base-display-${valorKey}`);
    const totalImpuestosDisplay = document.getElementById(`total-impuestos-display-${valorKey}`);
    const precioFinalTotalDisplay = document.getElementById(`precio-final-total-display-${valorKey}`);
    
    if (precioBaseDisplay) precioBaseDisplay.textContent = '$' + precioBase.toFixed(2);
    if (totalImpuestosDisplay) totalImpuestosDisplay.textContent = '+$' + totalImpuesto.toFixed(2);
    if (precioFinalTotalDisplay) precioFinalTotalDisplay.textContent = '$' + precioFinal.toFixed(2);
    
    // LOG PARA DEPURACIÓN
    console.log('=== VARIACIÓN ' + valorKey + ' ===');
    console.log('Precio normal:', precioNormal);
    console.log('Precio descuento:', precioDescuento);
    console.log('Descuento marcado:', tieneDescuento);
    console.log('Descuento activo HOY?', descuentoActivo);
    console.log('Precio BASE usado:', precioBase);
    console.log('Precio FINAL:', precioFinal);
}
// ============ FUNCIONES DE IMÁGENES ============

function calcularTamañoTotal() {
    let total = 0;
    
    if (imagenPrincipalFile) total += imagenPrincipalFile.size;
    if (gifFile) total += gifFile.size;
    
    selectedImages.forEach(img => {
        total += img.file.size;
    });
    
    Object.keys(imagenesVariacion).forEach(valorKey => {
        if (imagenesVariacion[valorKey] && imagenesVariacion[valorKey].imagenes) {
            imagenesVariacion[valorKey].imagenes.forEach(img => {
                total += img.file.size;
            });
        }
    });
    
    return total;
}

function actualizarBarraProgresoTamaño() {
    const totalSize = calcularTamañoTotal();
    const porcentaje = (totalSize / maxTotalSize) * 100;
    const progressBar = document.getElementById('size-progress-bar');
    const totalSizeSpan = document.getElementById('total-size');
    const limiteMsg = document.getElementById('limiteArchivosMsg');
    
    if (totalSizeSpan) {
        if (totalSize < 1024) {
            totalSizeSpan.textContent = totalSize + ' B';
        } else if (totalSize < 1024 * 1024) {
            totalSizeSpan.textContent = (totalSize / 1024).toFixed(2) + ' KB';
        } else {
            totalSizeSpan.textContent = (totalSize / (1024 * 1024)).toFixed(2) + ' MB';
        }
    }
    
    if (progressBar) {
        progressBar.style.width = Math.min(porcentaje, 100) + '%';
    }
    
    if (totalSize > maxTotalSize) {
        limiteExcedido = true;
        if (limiteMsg) limiteMsg.style.display = 'block';
        const btnSubmit = document.getElementById('btnSubmit');
        if (btnSubmit) btnSubmit.disabled = true;
    } else {
        limiteExcedido = false;
        if (limiteMsg) limiteMsg.style.display = 'none';
        const btnSubmit = document.getElementById('btnSubmit');
        if (btnSubmit) btnSubmit.disabled = false;
    }
}

function actualizarContadorImagenes() {
    const totalImagenesSpan = document.getElementById('total-imagenes');
    if (!totalImagenesSpan) return;
    
    let total = (imagenPrincipalFile ? 1 : 0) + (gifFile ? 1 : 0) + selectedImages.length;
    
    Object.keys(imagenesVariacion).forEach(valorKey => {
        if (imagenesVariacion[valorKey] && imagenesVariacion[valorKey].imagenes) {
            total += imagenesVariacion[valorKey].imagenes.length;
        }
    });
    
    totalImagenesSpan.textContent = total;
}

function previewImagenPrincipal(input) {
    const previewContainer = document.getElementById('preview_principal_container');
    const previewImg = document.getElementById('preview_principal_img');
    
    if (input.files && input.files[0]) {
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
        
        if (file.size > 5 * 1024 * 1024) {
            Swal.fire({
                icon: 'error',
                title: 'Archivo demasiado grande',
                text: 'La imagen principal no puede exceder los 5MB'
            });
            input.value = '';
            return;
        }
        
        imagenPrincipalFile = file;
        const reader = new FileReader();
        
        reader.onload = function(e) {
            if (previewImg) previewImg.src = e.target.result;
            if (previewContainer) previewContainer.style.display = 'block';
            actualizarBarraProgresoTamaño();
            actualizarContadorImagenes();
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        if (previewContainer) previewContainer.style.display = 'none';
        imagenPrincipalFile = null;
        actualizarBarraProgresoTamaño();
        actualizarContadorImagenes();
    }
}

function cancelarImagenPrincipal() {
    const input = document.getElementById('imagen_principal');
    const previewContainer = document.getElementById('preview_principal_container');
    
    input.value = '';
    if (previewContainer) previewContainer.style.display = 'none';
    imagenPrincipalFile = null;
    actualizarBarraProgresoTamaño();
    actualizarContadorImagenes();
}

function previewGif(input) {
    const previewContainer = document.getElementById('preview_gif_container');
    const previewImg = document.getElementById('preview_gif');
    
    if (input.files && input.files[0]) {
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
        
        if (file.size > 10 * 1024 * 1024) {
            Swal.fire({
                icon: 'error',
                title: 'Archivo demasiado grande',
                text: 'El GIF no puede exceder los 10MB'
            });
            input.value = '';
            return;
        }
        
        gifFile = file;
        const reader = new FileReader();
        
        reader.onload = function(e) {
            if (previewImg) previewImg.src = e.target.result;
            if (previewContainer) previewContainer.style.display = 'block';
            actualizarBarraProgresoTamaño();
            actualizarContadorImagenes();
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        if (previewContainer) previewContainer.style.display = 'none';
        gifFile = null;
        actualizarBarraProgresoTamaño();
        actualizarContadorImagenes();
    }
}

function cancelarGif() {
    const input = document.getElementById('gif_producto');
    const previewContainer = document.getElementById('preview_gif_container');
    
    input.value = '';
    if (previewContainer) previewContainer.style.display = 'none';
    gifFile = null;
    actualizarBarraProgresoTamaño();
    actualizarContadorImagenes();
}

function handleImageSelection(event) {
    const files = event.target.files;
    const maxFiles = 7;
    const currentCount = selectedImages.length;
    const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    
    if (!files || files.length === 0) {
        event.target.value = '';
        return;
    }
    
    const tamanioActual = calcularTamañoTotal();
    
    let nuevoTamanio = tamanioActual;
    for (let i = 0; i < files.length; i++) {
        nuevoTamanio += files[i].size;
    }
    
    if (nuevoTamanio > maxTotalSize) {
        Swal.fire({
            icon: 'warning',
            title: 'Límite de tamaño excedido',
            html: `
                <div class="text-center">
                    <p>Si agregas estos archivos, excederás el límite de 50MB.</p>
                    <p class="mb-0"><strong>Tamaño actual:</strong> ${(tamanioActual / (1024 * 1024)).toFixed(2)}MB</p>
                    <p><strong>Tamaño con nuevos archivos:</strong> ${(nuevoTamanio / (1024 * 1024)).toFixed(2)}MB</p>
                </div>
            `,
            confirmButtonText: 'Entendido'
        });
        event.target.value = '';
        return;
    }
    
    if (currentCount + files.length > maxFiles) {
        Swal.fire({
            icon: 'warning',
            title: 'Límite de imágenes',
            text: `Solo puedes seleccionar máximo ${maxFiles} imágenes adicionales. Ya tienes ${currentCount} seleccionadas.`
        });
        event.target.value = '';
        return;
    }
    
    let archivosAgregados = 0;
    
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        
        if (!validTypes.includes(file.type)) {
            continue;
        }
        
        if (file.size > 5 * 1024 * 1024) {
            continue;
        }
        
        if (isImageDuplicate(file)) {
            continue;
        }
        
        const imageId = 'img_' + Date.now() + '_' + imageCounter++;
        const preview = URL.createObjectURL(file);
        
        selectedImages.push({
            id: imageId,
            file: file,
            preview: preview,
            name: file.name,
            size: file.size
        });
        archivosAgregados++;
    }
    
    if (archivosAgregados > 0) {
        const countSpan = document.getElementById('selected-images-count');
        if (countSpan) {
            countSpan.textContent = selectedImages.length + ' archivos';
        }
        
        renderSelectedImages();
        actualizarBarraProgresoTamaño();
        actualizarContadorImagenes();
    }
    
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
    
    const countSpan = document.getElementById('selected-images-count');
    if (countSpan) countSpan.textContent = selectedImages.length + ' archivos';
    renderSelectedImages();
    actualizarContadorImagenes();
    actualizarBarraProgresoTamaño();
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
        small1.textContent = image.file.name.length > 20 ? image.file.name.substring(0, 20) + '...' : image.file.name;
        
        const small2 = document.createElement('small');
        small2.className = 'text-muted d-block';
        let sizeText = '';
        if (image.file.size < 1024) {
            sizeText = image.file.size + ' B';
        } else if (image.file.size < 1024 * 1024) {
            sizeText = (image.file.size / 1024).toFixed(2) + ' KB';
        } else {
            sizeText = (image.file.size / (1024 * 1024)).toFixed(2) + ' MB';
        }
        small2.textContent = sizeText;
        
        cardBody.appendChild(small1);
        cardBody.appendChild(small2);
        
        card.appendChild(btn);
        card.appendChild(img);
        card.appendChild(cardBody);
        
        col.appendChild(card);
        container.appendChild(col);
    });
}

function validarTamañoTotalAntesDeEnviar() {
    const totalSize = calcularTamañoTotal();
    const maxSize = 50 * 1024 * 1024;
    
    if (!imagenPrincipalFile) {
        Swal.fire({
            icon: 'error',
            title: 'Imagen principal requerida',
            text: 'Debes seleccionar una imagen principal para el producto'
        });
        return false;
    }
    
    if (totalSize > maxSize) {
        Swal.fire({
            icon: 'error',
            title: 'Archivos demasiado grandes',
            text: `El tamaño total de los archivos (${(totalSize / (1024 * 1024)).toFixed(2)}MB) excede el límite de 50MB.`
        });
        return false;
    }
    
    return true;
}

function validarCamposUnicosAntesDeEnviar() {
    const skuInput = document.getElementById('vCodigo_barras');
    const nombreInput = document.getElementById('vNombre');
    const tieneDescuentoCheckbox = document.getElementById('bTiene_descuento');
    const precioVentaInput = document.getElementById('dPrecio_venta');
    const precioDescuentoInput = document.getElementById('dPrecio_descuento');
    const fechaInicioInput = document.getElementById('dFecha_inicio_descuento');
    const fechaFinInput = document.getElementById('dFecha_fin_descuento');

    let errores = [];

    // 1. Validar SKU del Producto
    if (skuInput && !verificarSKUProductoLocal(skuInput)) {
        errores.push(`Ya existe un producto con el SKU "${skuInput.value}".`);
    }

    // 2. Validar Nombre del Producto
    if (nombreInput && !verificarNombreProductoLocal(nombreInput)) {
        errores.push(`Ya existe un producto con el nombre "${nombreInput.value}".`);
    }

    // 3. Validar Descuento del Producto
    if (tieneDescuentoCheckbox && tieneDescuentoCheckbox.checked) {
        const precioVenta = parseFloat(precioVentaInput?.value) || 0;
        const precioDescuento = parseFloat(precioDescuentoInput?.value) || 0;

        if (!precioDescuentoInput?.value || precioDescuentoInput.value === '') {
            errores.push('El precio de descuento es obligatorio cuando el descuento está activo.');
        } else if (precioDescuento >= precioVenta) {
            errores.push(`El precio de descuento ($${precioDescuento.toFixed(2)}) debe ser menor que el precio de venta ($${precioVenta.toFixed(2)}).`);
        }

        if (!fechaInicioInput?.value) {
            errores.push('La fecha de inicio es obligatoria cuando el descuento está activo.');
        }
        if (!fechaFinInput?.value) {
            errores.push('La fecha de fin es obligatoria cuando el descuento está activo.');
        }

        if (fechaInicioInput?.value && fechaFinInput?.value) {
            const inicio = new Date(fechaInicioInput.value);
            const fin = new Date(fechaFinInput.value);
            if (fin < inicio) {
                errores.push('La fecha de fin no puede ser anterior a la fecha de inicio.');
            }
        }
    }

    // 4. Validar SKUs de variaciones
    const variacionesSKU = document.querySelectorAll('[id^="sku-"]');
    for (let input of variacionesSKU) {
        if (input.value && input.value.trim() !== '') {
            const valorKey = input.id.replace('sku-', '');
            if (!verificarSKUVariacionLocal(input, valorKey)) {
                errores.push(`Ya existe un producto o variación con el SKU "${input.value}".`);
                break;
            }
        }
    }

    // 5. Validar descuentos de variaciones
    Object.keys(atributosActivos).forEach(atributoId => {
        Object.keys(atributosActivos[atributoId].valores).forEach(valorId => {
            const valorKey = `${atributoId}_${valorId}`;
            const descuentoCheckbox = document.getElementById(`descuento-${valorKey}`);
            const precioInput = document.getElementById(`precio-${valorKey}`);
            const precioDescuentoInput = document.getElementById(`precio_descuento-${valorKey}`);
            const fechaInicioInput = document.getElementById(`fecha-inicio-${valorKey}`);
            const fechaFinInput = document.getElementById(`fecha-fin-${valorKey}`);
            const valorNombre = atributosActivos[atributoId].valores[valorId].nombre;
            
            if (descuentoCheckbox && descuentoCheckbox.checked) {
                const precioNormal = parseFloat(precioInput?.value) || 0;
                const precioDescuento = parseFloat(precioDescuentoInput?.value) || 0;
                
                if (!precioDescuentoInput?.value || precioDescuentoInput.value === '') {
                    errores.push(`El precio de descuento es obligatorio para la variación "${valorNombre}".`);
                } else if (precioDescuento >= precioNormal) {
                    errores.push(`El precio de descuento ($${precioDescuento.toFixed(2)}) debe ser menor que el precio normal ($${precioNormal.toFixed(2)}) para la variación "${valorNombre}".`);
                }
                
                if (!fechaInicioInput?.value) {
                    errores.push(`La fecha de inicio es obligatoria para la variación "${valorNombre}".`);
                }
                if (!fechaFinInput?.value) {
                    errores.push(`La fecha de fin es obligatoria para la variación "${valorNombre}".`);
                }
                
                if (fechaInicioInput?.value && fechaFinInput?.value) {
                    const inicio = new Date(fechaInicioInput.value);
                    const fin = new Date(fechaFinInput.value);
                    if (fin < inicio) {
                        errores.push(`La fecha de fin no puede ser anterior a la fecha de inicio para la variación "${valorNombre}".`);
                    }
                }
            }
        });
    });

    // 6. NUEVA VALIDACIÓN: Atributos activos sin valores seleccionados
    let atributosIncompletos = [];
    document.querySelectorAll('.atributo-activo-checkbox:checked').forEach(checkbox => {
        const atributoId = checkbox.dataset.atributoId;
        const atributoNombre = checkbox.dataset.atributoNombre;
        const valoresContainer = document.getElementById(`valores-container-${atributoId}`);
        
        if (valoresContainer) {
            const valoresSeleccionados = valoresContainer.querySelectorAll('.valor-checkbox:checked');
            if (valoresSeleccionados.length === 0) {
                atributosIncompletos.push(`"${atributoNombre}"`);
            }
        }
    });
    
    if (atributosIncompletos.length > 0) {
        errores.push(`Los siguientes atributos están activados pero no tienen valores seleccionados: ${atributosIncompletos.join(', ')}.`);
    }

    // 7. Mostrar errores con SweetAlert
    if (errores.length > 0) {
        Swal.fire({
            icon: 'error',
            title: '¡Error de validación!',
            html: `
                <div class="text-left" style="max-height: 400px; overflow-y: auto;">
                    <p class="mb-2 fw-bold">Por favor, corrige los siguientes errores:</p>
                    <ul class="text-left list-unstyled">
                        ${errores.map(err => `<li class="mb-2 text-danger"><i class="fas fa-exclamation-circle me-2"></i>${err}</li>`).join('')}
                    </ul>
                </div>
            `,
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#d33',
            width: '600px'
        });
        return false;
    }

    return true;
}

// ============ FUNCIONES DE ATRIBUTOS Y VARIACIONES ============

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
                atributoId: atributo.id,
                atributoNombre: atributo.nombre
            });
        });
    });
    
    if (todosLosValores.length === 0) {
        tabsContainer.style.display = 'none';
        if (noAtributosMsg) noAtributosMsg.style.display = 'block';
        if (totalValoresBadge) totalValoresBadge.textContent = '0 valores';
        return;
    }
    
    tabsContainer.style.display = 'block';
    if (noAtributosMsg) noAtributosMsg.style.display = 'none';
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
        
        tabButton.appendChild(document.createTextNode(' ' + valor.nombre));
        
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
        
        // IMPORTANTE: Aquí está el HTML corregido de cada pestaña de variación
        const formHtml = `
            <div class="variacion-form-container">
                <div class="variacion-header-info mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="fw-bold mb-2">
                                <i class="fas fa-cube me-2"></i>
                                Variación: <span class="text-warning">${valor.nombre}</span>
                                <small class="d-block text-white-50 mt-1">Atributo: ${valor.atributoNombre}</small>
                            </h6>
                            <p class="small mb-0 opacity-75">
                                <i class="fas fa-info-circle me-1"></i>
                                Configura los datos específicos para esta variación del producto
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="badge bg-white text-dark p-2">
                                <i class="fas fa-barcode me-1"></i>
                                SKU Sugerido: ${skuSugerido}
                            </span>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="variaciones[${valorKey}][id_atributo]" value="${valor.atributoId}">
                <input type="hidden" name="variaciones[${valorKey}][id_atributo_valor]" value="${valor.id}">
                <input type="hidden" name="variaciones[${valorKey}][vNombre_variacion]" 
                       value="${valor.atributoNombre}: ${valor.nombre}">

                <!-- SECCIÓN DE IMÁGENES DE LA VARIACIÓN -->
                <div class="card mb-4 border-primary">
                    <div class="card-header bg-primary text-white py-2">
                        <h6 class="mb-0"><i class="fas fa-images me-2"></i>Imágenes de la Variación</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- IMAGEN PRINCIPAL DE LA VARIACIÓN -->
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="img_principal_${valorKey}" class="form-label fw-bold">
                                        <i class="fas fa-star text-warning me-1"></i>Imagen Principal
                                    </label>
                                    <input type="file" 
                                           name="variaciones[${valorKey}][imagen_principal]" 
                                           id="img_principal_${valorKey}" 
                                           class="form-control"
                                           accept="image/jpeg,image/jpg,image/png"
                                           onchange="previewImagenPrincipalVariacion(this, 'preview_principal_${valorKey}')">
                                    <small class="form-text text-muted">
                                        JPG, JPEG, PNG. Máx: 5MB
                                    </small>
                                    <div id="preview_principal_${valorKey}" class="mt-2" style="display: none;">
                                        <div class="border rounded p-2 text-center bg-light">
                                            <img src="#" class="img-thumbnail" style="max-width: 150px; max-height: 150px; object-fit: contain;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- GIF DE LA VARIACIÓN -->
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="gif_${valorKey}" class="form-label fw-bold">
                                        <i class="fas fa-file-image text-success me-1"></i>GIF Animado
                                    </label>
                                    <input type="file" 
                                           name="variaciones[${valorKey}][gif]" 
                                           id="gif_${valorKey}" 
                                           class="form-control"
                                           accept="image/gif"
                                           onchange="previewGifVariacion(this, 'preview_gif_${valorKey}')">
                                    <small class="form-text text-muted">
                                        GIF. Máx: 10MB
                                    </small>
                                    <div id="preview_gif_${valorKey}" class="mt-2" style="display: none;">
                                        <div class="border rounded p-2 text-center bg-light">
                                            <img src="#" class="img-thumbnail" style="max-width: 150px; max-height: 150px; object-fit: contain;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- IMÁGENES ADICIONALES DE LA VARIACIÓN -->
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="imagenes_adicionales_${valorKey}" class="form-label fw-bold">
                                        <i class="fas fa-images me-1"></i>Imágenes Adicionales (Máx 7)
                                    </label>
                                    <input type="file" 
                                           name="variaciones[${valorKey}][imagenes_adicionales][]" 
                                           id="imagenes_adicionales_${valorKey}" 
                                           class="form-control"
                                           multiple
                                           accept="image/jpeg,image/jpg,image/png,image/webp"
                                           onchange="handleImagenesAdicionalesVariacion(event, '${valorKey}')">
                                    <small class="form-text text-muted">
                                        JPG, JPEG, PNG, WEBP. Máx: 5MB c/u
                                    </small>
                                    <div id="container_adicionales_${valorKey}" class="row mt-2"></div>
                                    <div class="mt-2">
                                        <span class="badge bg-info" id="count_adicionales_${valorKey}">0 seleccionadas</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

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
                                       oninput="validarSKU(this); verificarSKUVariacionLocal(this, '${valorKey}')"
                                       onblur="verificarSKUVariacionLocal(this, '${valorKey}')"
                                       title="Solo letras, números y guiones"
                                       placeholder="Ej: ${skuSugerido}"
                                       data-atributo-id="${valor.atributoId}"
                                       data-valor-id="${valor.id}"
                                       autocomplete="off">
                            </div>
                            <small class="form-text text-muted">
                                <i class="fas fa-lightbulb me-1"></i>
                                El SKU debe ser único. Solo letras, números y guiones.
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
                                       oninput="validarPrecio(this); actualizarPrecioFinalVariacion('${valorKey}')"
                                       placeholder="0.00"
                                       title="Máximo: 9,999,999.99 (7 dígitos enteros, 2 decimales)"
                                       autocomplete="off">
                            </div>
                            <small class="form-text text-muted">Máximo: 9,999,999.99</small>
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
                                       pattern="[0-9]{1,6}"
                                       min="0"
                                       max="999999"
                                       placeholder="0"
                                       title="Máximo 6 dígitos (0-999999)"
                                       autocomplete="off">
                            </div>
                            <small class="form-text text-muted">Máximo 999,999 unidades</small>
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

                <!-- SECCIÓN DE IMPUESTO PARA LA VARIACIÓN -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="card border">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0"><i class="fas fa-file-invoice-dollar me-2"></i>Impuesto para la Variación</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group mb-3">
                                            <label for="impuesto-${valorKey}" class="form-label fw-bold">
                                                Impuesto Aplicable
                                            </label>
                                            <select name="variaciones[${valorKey}][id_impuesto]" 
                                                    id="impuesto-${valorKey}" 
                                                    class="form-select"
                                                    onchange="actualizarPrecioFinalVariacion('${valorKey}')">
                                                <option value="">-- Sin impuesto (heredar del producto) --</option>
                                                @if(isset($impuestos) && $impuestos->count() > 0)
                                                    @foreach($impuestos as $impuesto)
                                                        <option value="{{ $impuesto->id_impuesto }}" 
                                                                data-porcentaje="{{ $impuesto->dPorcentaje }}"
                                                                data-tipo="{{ $impuesto->eTipo }}">
                                                            {{ $impuesto->vNombre }} ({{ $impuesto->dPorcentaje }}%)
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            <small class="form-text text-muted">
                                                Selecciona un impuesto específico para esta variación (IVA o IEPS)
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="bg-light p-3 rounded text-center">
                                            <small class="text-muted d-block">Precio con impuesto</small>
                                            <h5 class="fw-bold mb-0" id="precio-final-${valorKey}">$0.00</h5>
                                            <small class="text-muted" id="detalle-impuesto-${valorKey}"></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                            <small class="form-text text-muted">Máx: 999.999 kg</small>
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
                            <small class="form-text text-muted">Máx: 999.99 cm</small>
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
                            <small class="form-text text-muted">Máx: 999.99 cm</small>
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
                            <small class="form-text text-muted">Máx: 999.99 cm</small>
                        </div>
                    </div>
                </div>

                <!-- DESCUENTO ESPECIAL DE LA VARIACIÓN -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="card border">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0"><i class="fas fa-percentage me-2"></i>Descuento Especial de la Variación</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label fw-bold">
                                                <i class="fas fa-percentage me-1"></i>Descuento Especial
                                            </label>
                                            <div class="form-check form-switch">
                                                <input type="checkbox" 
                                                       name="variaciones[${valorKey}][bTiene_descuento]" 
                                                       id="descuento-${valorKey}" 
                                                       class="form-check-input" 
                                                       value="1"
                                                       onchange="toggleDescuentoVariacion(this, '${valorKey}')">
                                                <label class="form-check-label" for="descuento-${valorKey}">
                                                    Activar Descuento para esta variación
                                                </label>
                                            </div>
                                            <small class="form-text text-muted">
                                                Permite establecer un precio de descuento por tiempo limitado
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- CAMPOS DE DESCUENTO (OCULTOS INICIALMENTE) -->
                                <div class="descuento-fields-${valorKey}" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group mb-3">
                                                <label for="precio_descuento-${valorKey}" class="form-label fw-bold">
                                                    Precio de Descuento <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="text" 
                                                           name="variaciones[${valorKey}][dPrecio_descuento]" 
                                                           id="precio_descuento-${valorKey}"
                                                           class="form-control variacion-precio-descuento"
                                                           data-precio-normal-id="precio-${valorKey}"
                                                           data-valor-key="${valorKey}"
                                                           value=""
                                                           oninput="validarPrecio(this); validarPrecioDescuentoVariacionInstantaneo(this); actualizarPrecioFinalVariacion('${valorKey}')"
                                                           onblur="validarPrecioDescuentoVariacion(this)"
                                                           placeholder="0.00"
                                                           autocomplete="off">
                                                </div>
                                                <div id="error-precio-descuento-${valorKey}" class="invalid-feedback" style="display: none;"></div>
                                                <small class="form-text text-muted">Debe ser menor al precio de venta</small>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="form-group mb-3">
                                                <label for="fecha-inicio-${valorKey}" class="form-label fw-bold">
                                                    Fecha inicio <span class="text-danger">*</span>
                                                </label>
                                                <input type="date" 
                                                       name="variaciones[${valorKey}][dFecha_inicio_descuento]" 
                                                       class="form-control fecha-inicio-${valorKey}"
                                                       id="fecha-inicio-${valorKey}"
                                                       value=""
                                                       onchange="validarFechasDescuentoVariacion('fecha-inicio-${valorKey}', 'fecha-fin-${valorKey}', '${valorKey}')"
                                                       autocomplete="off">
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="form-group mb-3">
                                                <label for="fecha-fin-${valorKey}" class="form-label fw-bold">
                                                    Fecha fin <span class="text-danger">*</span>
                                                </label>
                                                <input type="date" 
                                                       name="variaciones[${valorKey}][dFecha_fin_descuento]" 
                                                       class="form-control fecha-fin-${valorKey}"
                                                       id="fecha-fin-${valorKey}"
                                                       value=""
                                                       onchange="validarFechasDescuentoVariacion('fecha-inicio-${valorKey}', 'fecha-fin-${valorKey}', '${valorKey}')"
                                                       autocomplete="off">
                                                <div id="error-fechas-descuento-${valorKey}" class="invalid-feedback" style="display: none;"></div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="motivo-${valorKey}" class="form-label fw-bold">
                                                    Motivo del descuento
                                                </label>
                                                <input type="text" 
                                                       name="variaciones[${valorKey}][vMotivo_descuento]" 
                                                       id="motivo-${valorKey}"
                                                       class="form-control"
                                                       value=""
                                                       maxlength="255"
                                                       placeholder="Ej: Liquidación de temporada, Black Friday, etc."
                                                       autocomplete="off">
                                                <small class="form-text text-muted">Opcional</small>
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
    
    // Cargar impuestos en los selects de las nuevas variaciones
    setTimeout(cargarImpuestosEnVariaciones, 200);
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

function actualizarResumenAtributos() {
    const resumenDiv = document.getElementById('resumen-atributos');
    const lista = document.getElementById('atributos-activos-lista');
    const totalAtributosBadge = document.getElementById('total-atributos-activos-badge');
    
    if (!lista || !totalAtributosBadge) return;
    
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
                <span class="fw-bold text-primary">${atributo.nombre}: </span>
                <span class="badge bg-success ms-2">${valoresArray.length} valores</span>
                <div class="mt-1 small">
                    ${valoresArray.map(v => `<span class="badge bg-light text-dark me-1">${v.nombre}</span>`).join('')}
                </div>
            `;
            lista.appendChild(item);
        }
    });
    
    if (resumenDiv) {
        resumenDiv.style.display = atributosCount > 0 ? 'block' : 'none';
    }
    totalAtributosBadge.textContent = atributosCount > 0 ? 
        `${atributosCount} atributos activos (${totalValores} valores)` : 
        '0 atributos activos';
}
function validarAtributosAntesDeEnviar() {
    let errores = [];
    let hayAtributosActivos = false;
    
    // Recorrer todos los checkboxes de atributos activos
    document.querySelectorAll('.atributo-activo-checkbox').forEach(checkbox => {
        const atributoId = checkbox.dataset.atributoId;
        const atributoNombre = checkbox.dataset.atributoNombre;
        
        if (checkbox.checked) {
            hayAtributosActivos = true;
            
            // Verificar si tiene valores seleccionados
            const valoresContainer = document.getElementById(`valores-container-${atributoId}`);
            if (valoresContainer) {
                const valoresSeleccionados = valoresContainer.querySelectorAll('.valor-checkbox:checked');
                
                if (valoresSeleccionados.length === 0) {
                    errores.push(`❌ El atributo "${atributoNombre}" está activado pero no tiene ningún valor seleccionado.`);
                }
            }
        }
    });
    
    // Si hay errores, mostrarlos y prevenir envío
    if (errores.length > 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Atributos incompletos',
            html: `
                <div class="text-left">
                    <p class="mb-3">Por favor, corrige los siguientes errores:</p>
                    <ul class="text-left">
                        ${errores.map(err => `<li class="mb-2 text-danger">${err}</li>`).join('')}
                    </ul>
                    <hr>
                    <p class="text-muted small mb-0">
                        <i class="fas fa-lightbulb me-1"></i>
                        Si no quieres usar un valor de este atributo, desmarca la casilla del atributo.
                    </p>
                </div>
            `,
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#3085d6'
        });
        return false;
    }
    
    return true;
}

function cargarImpuestosEnVariaciones() {
    const selectPrincipal = document.getElementById('id_impuesto');
    if (!selectPrincipal) return;
    
    document.querySelectorAll('select[id^="impuesto-"]').forEach(select => {
        const valorActual = select.value;
        
        while (select.options.length > 1) {
            select.remove(1);
        }
        
        for (let i = 1; i < selectPrincipal.options.length; i++) {
            const option = document.createElement('option');
            option.value = selectPrincipal.options[i].value;
            option.setAttribute('data-porcentaje', selectPrincipal.options[i].getAttribute('data-porcentaje'));
            option.setAttribute('data-tipo', selectPrincipal.options[i].getAttribute('data-tipo'));
            option.textContent = selectPrincipal.options[i].textContent;
            select.appendChild(option);
        }
        
        if (valorActual && valorActual !== '') {
            select.value = valorActual;
        }
    });
}

function actualizarSelectsImpuestosVariaciones(impuesto) {
    const selectsVariacion = document.querySelectorAll('select[id^="impuesto-"]');
    
    if (selectsVariacion.length === 0) return;
    
    const impuestoId = impuesto.id_impuesto;
    const porcentaje = impuesto.dPorcentaje;
    const tipo = impuesto.eTipo;
    const texto = impuesto.vNombre + ' (' + tipo + ' - ' + parseFloat(porcentaje).toFixed(2) + '%)';
    
    selectsVariacion.forEach(select => {
        let existe = false;
        for (let i = 0; i < select.options.length; i++) {
            if (select.options[i].value == impuestoId) {
                existe = true;
                break;
            }
        }
        
        if (!existe) {
            const option = document.createElement('option');
            option.value = impuestoId;
            option.setAttribute('data-porcentaje', porcentaje);
            option.setAttribute('data-tipo', tipo);
            option.textContent = texto;
            select.appendChild(option);
        }
    });
}

// ============ FUNCIONES PARA IMÁGENES DE VARIACIONES ============

function previewImagenPrincipalVariacion(input, previewId) {
    const previewContainer = document.getElementById(previewId);
    
    if (input.files && input.files[0]) {
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
        
        if (file.size > 5 * 1024 * 1024) {
            Swal.fire({
                icon: 'error',
                title: 'Archivo demasiado grande',
                text: 'La imagen principal no puede exceder los 5MB'
            });
            input.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            if (previewContainer) {
                const img = previewContainer.querySelector('img');
                if (img) {
                    img.src = e.target.result;
                    previewContainer.style.display = 'block';
                }
            }
            actualizarBarraProgresoTamaño();
            actualizarContadorImagenes();
        }
        reader.readAsDataURL(file);
    } else {
        if (previewContainer) previewContainer.style.display = 'none';
        actualizarBarraProgresoTamaño();
        actualizarContadorImagenes();
    }
}

function previewGifVariacion(input, previewId) {
    const previewContainer = document.getElementById(previewId);
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        if (file.type !== 'image/gif') {
            Swal.fire({
                icon: 'error',
                title: 'Formato no válido',
                text: 'Solo se permiten archivos GIF'
            });
            input.value = '';
            return;
        }
        
        if (file.size > 10 * 1024 * 1024) {
            Swal.fire({
                icon: 'error',
                title: 'Archivo demasiado grande',
                text: 'El GIF no puede exceder los 10MB'
            });
            input.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            if (previewContainer) {
                const img = previewContainer.querySelector('img');
                if (img) {
                    img.src = e.target.result;
                    previewContainer.style.display = 'block';
                }
            }
            actualizarBarraProgresoTamaño();
            actualizarContadorImagenes();
        }
        reader.readAsDataURL(file);
    } else {
        if (previewContainer) previewContainer.style.display = 'none';
        actualizarBarraProgresoTamaño();
        actualizarContadorImagenes();
    }
}

function handleImagenesAdicionalesVariacion(event, valorKey) {
    const input = event.target;
    const container = document.getElementById(`container_adicionales_${valorKey}`);
    const countSpan = document.getElementById(`count_adicionales_${valorKey}`);
    
    if (!container || !countSpan) return;
    
    const files = Array.from(input.files);
    const maxFiles = 7;
    const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    
    if (!imagenesVariacion[valorKey]) {
        imagenesVariacion[valorKey] = {
            imagenes: []
        };
    }
    
    if (imagenesVariacion[valorKey].imagenes.length + files.length > maxFiles) {
        Swal.fire({
            icon: 'warning',
            title: 'Límite de imágenes',
            text: `Solo puedes seleccionar máximo ${maxFiles} imágenes adicionales.`
        });
        input.value = '';
        return;
    }
    
    const tamanioActual = calcularTamañoTotal();
    let nuevoTamanio = tamanioActual;
    files.forEach(file => nuevoTamanio += file.size);
    
    if (nuevoTamanio > maxTotalSize) {
        Swal.fire({
            icon: 'warning',
            title: 'Límite de tamaño excedido',
            text: 'Si agregas estas imágenes, excederás el límite de 50MB.'
        });
        input.value = '';
        return;
    }
    
    files.forEach(file => {
        if (!validTypes.includes(file.type)) return;
        if (file.size > 5 * 1024 * 1024) return;
        
        const imageId = 'var_img_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        const preview = URL.createObjectURL(file);
        
        imagenesVariacion[valorKey].imagenes.push({
            id: imageId,
            file: file,
            preview: preview,
            name: file.name,
            size: file.size
        });
    });
    
    renderImagenesAdicionalesVariacion(valorKey);
    countSpan.textContent = imagenesVariacion[valorKey].imagenes.length + ' seleccionadas';
    actualizarBarraProgresoTamaño();
    actualizarContadorImagenes();
    input.value = '';
}

function renderImagenesAdicionalesVariacion(valorKey) {
    const container = document.getElementById(`container_adicionales_${valorKey}`);
    if (!container) return;
    
    container.innerHTML = '';
    
    if (!imagenesVariacion[valorKey] || imagenesVariacion[valorKey].imagenes.length === 0) {
        container.innerHTML = '<div class="col-12 text-muted small">No hay imágenes seleccionadas</div>';
        return;
    }
    
    imagenesVariacion[valorKey].imagenes.forEach((img, index) => {
        const col = document.createElement('div');
        col.className = 'col-4 col-md-3 mb-2';
        
        const card = document.createElement('div');
        card.className = 'border rounded p-1 text-center bg-light position-relative';
        
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'btn btn-danger btn-sm position-absolute top-0 end-0 m-1';
        btn.style.cssText = 'width: 20px; height: 20px; padding: 0; border-radius: 50%; font-size: 10px;';
        btn.onclick = function(e) {
            e.preventDefault();
            eliminarImagenAdicionalVariacion(valorKey, img.id);
        };
        
        const btnIcon = document.createElement('i');
        btnIcon.className = 'fas fa-times';
        btn.appendChild(btnIcon);
        
        const imgElement = document.createElement('img');
        imgElement.src = img.preview;
        imgElement.className = 'img-fluid';
        imgElement.style.cssText = 'height: 60px; object-fit: contain;';
        
        const small = document.createElement('small');
        small.className = 'd-block text-truncate';
        small.textContent = img.name.length > 10 ? img.name.substring(0, 10) + '...' : img.name;
        
        card.appendChild(btn);
        card.appendChild(imgElement);
        card.appendChild(small);
        col.appendChild(card);
        container.appendChild(col);
    });
}

function eliminarImagenAdicionalVariacion(valorKey, imageId) {
    if (imagenesVariacion[valorKey]) {
        const image = imagenesVariacion[valorKey].imagenes.find(img => img.id === imageId);
        if (image && image.preview) {
            URL.revokeObjectURL(image.preview);
        }
        
        imagenesVariacion[valorKey].imagenes = imagenesVariacion[valorKey].imagenes.filter(img => img.id !== imageId);
        
        renderImagenesAdicionalesVariacion(valorKey);
        
        const countSpan = document.getElementById(`count_adicionales_${valorKey}`);
        if (countSpan) {
            countSpan.textContent = imagenesVariacion[valorKey].imagenes.length + ' seleccionadas';
        }
        
        actualizarBarraProgresoTamaño();
        actualizarContadorImagenes();
    }
}

// ============ FUNCIONES PARA MODALES ============

function abrirModalCategoria() {
    const inputNombre = document.getElementById('vNombre_categoria_modal');
    const inputSlug = document.getElementById('vSlug_categoria_modal');
    const selectPadre = document.getElementById('id_categoria_padre_modal');
    const textareaDesc = document.getElementById('tDescripcion_categoria_modal');
    const inputImagen = document.getElementById('vImagen_categoria_modal');
    const checkboxActivo = document.getElementById('bActivo_categoria_modal');
    
    if (inputNombre) inputNombre.value = '';
    if (inputSlug) inputSlug.value = '';
    if (selectPadre) selectPadre.value = '';
    if (textareaDesc) textareaDesc.value = '';
    if (inputImagen) inputImagen.value = '';
    
    const preview = document.getElementById('categoriaModalImagePreview');
    if (preview) preview.style.display = 'none';
    
    if (checkboxActivo) checkboxActivo.checked = true;
    
    categoriaModalImagenFile = null;
    
    if (modalCategoria) modalCategoria.show();
}

function abrirModalMarca() {
    const inputNombre = document.getElementById('vNombre_marca_modal');
    const textareaDesc = document.getElementById('tDescripcion_marca_modal');
    
    if (inputNombre) inputNombre.value = '';
    if (textareaDesc) textareaDesc.value = '';
    
    if (modalMarca) modalMarca.show();
}

function abrirModalEtiqueta() {
    const inputNombre = document.getElementById('vNombre_eti_modal');
    const textareaDesc = document.getElementById('tDescripcion_eti_modal');
    
    if (inputNombre) inputNombre.value = '';
    if (textareaDesc) textareaDesc.value = '';
    
    if (modalEtiqueta) modalEtiqueta.show();
}

function abrirModalAtributo() {
    const inputNombre = document.getElementById('vNombre_attr_modal');
    const inputSlug = document.getElementById('vSlug_attr_modal');
    const textareaDesc = document.getElementById('tDescripcion_attr_modal');
    
    if (inputNombre) inputNombre.value = '';
    if (inputSlug) inputSlug.value = '';
    if (textareaDesc) textareaDesc.value = '';
    
    if (modalAtributo) modalAtributo.show();
}

function abrirModalImpuesto() {
    const inputNombre = document.getElementById('vNombre_impuesto_modal');
    const selectTipo = document.getElementById('eTipo_impuesto_modal');
    const inputPorcentaje = document.getElementById('dPorcentaje_impuesto_modal');
    const checkboxActivo = document.getElementById('bActivo_impuesto_modal');
    
    if (inputNombre) inputNombre.value = '';
    if (selectTipo) selectTipo.value = 'IVA';
    if (inputPorcentaje) inputPorcentaje.value = '16.00';
    if (checkboxActivo) checkboxActivo.checked = true;
    
    if (modalImpuesto) modalImpuesto.show();
}

function mostrarFormularioValor(atributoId, atributoNombre) {
    const spanNombre = document.getElementById('atributoNombreModal');
    const inputAtributoId = document.getElementById('valor_atributo_id');
    const inputValor = document.getElementById('vValor_modal');
    const inputSlug = document.getElementById('vSlug_valor_modal');
    const checkboxActivo = document.getElementById('bActivo_valor_modal');
    
    if (spanNombre) spanNombre.textContent = atributoNombre;
    if (inputAtributoId) inputAtributoId.value = atributoId;
    if (inputValor) inputValor.value = '';
    if (inputSlug) inputSlug.value = '';
    if (checkboxActivo) checkboxActivo.checked = true;
    
    if (valorModal) valorModal.show();
}

function generarSlugValor(valor) {
    if (!valor) {
        const inputSlug = document.getElementById('vSlug_valor_modal');
        if (inputSlug) inputSlug.value = '';
        return;
    }
    
    let slug = valor.toLowerCase()
        .replace(/á/g, 'a').replace(/é/g, 'e').replace(/í/g, 'i').replace(/ó/g, 'o').replace(/ú/g, 'u')
        .replace(/ñ/g, 'n')
        .replace(/[^a-z0-9\s]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .replace(/^-+/, '').replace(/-+$/, '');
    
    const inputSlug = document.getElementById('vSlug_valor_modal');
    if (inputSlug) inputSlug.value = slug;
}

function generarSlugCategoria(nombre) {
    if (!nombre) {
        const inputSlug = document.getElementById('vSlug_categoria_modal');
        if (inputSlug) inputSlug.value = '';
        return;
    }
    let slug = nombre.toLowerCase()
        .replace(/á/g, 'a').replace(/é/g, 'e').replace(/í/g, 'i').replace(/ó/g, 'o').replace(/ú/g, 'u')
        .replace(/ñ/g, 'n')
        .replace(/[^a-z0-9\s]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .replace(/^-+/, '').replace(/-+$/, '');
    
    const inputSlug = document.getElementById('vSlug_categoria_modal');
    if (inputSlug) inputSlug.value = slug;
}

function generarSlugAtributo(nombre) {
    if (!nombre) {
        const inputSlug = document.getElementById('vSlug_attr_modal');
        if (inputSlug) inputSlug.value = '';
        return;
    }
    let slug = nombre.toLowerCase()
        .replace(/á/g, 'a').replace(/é/g, 'e').replace(/í/g, 'i').replace(/ó/g, 'o').replace(/ú/g, 'u')
        .replace(/ñ/g, 'n')
        .replace(/[^a-z0-9\s]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .replace(/^-+/, '').replace(/-+$/, '');
    
    const inputSlug = document.getElementById('vSlug_attr_modal');
    if (inputSlug) inputSlug.value = slug;
}

function quickGenerarSlug(texto, inputId) {
    if (!texto) return;
    let slug = texto.toLowerCase()
        .replace(/á/g, 'a').replace(/é/g, 'e').replace(/í/g, 'i').replace(/ó/g, 'o').replace(/ú/g, 'u')
        .replace(/ñ/g, 'n')
        .replace(/[^a-z0-9\s]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .replace(/^-+/, '').replace(/-+$/, '');
    
    const input = document.getElementById(inputId);
    if (input) input.value = slug;
}

function quickActualizarSlug(nombre, slugId) {
    quickGenerarSlug(nombre, slugId);
}

// ============ FUNCIONES PARA GUARDAR EN MODALES ============

function guardarCategoria() {
    const vNombre = document.getElementById('vNombre_categoria_modal')?.value.trim();
    const vSlug = document.getElementById('vSlug_categoria_modal')?.value.trim();
    const idCategoriaPadre = document.getElementById('id_categoria_padre_modal')?.value;
    const tDescripcion = document.getElementById('tDescripcion_categoria_modal')?.value;
    const bActivo = document.getElementById('bActivo_categoria_modal')?.checked ? 1 : 0;
    
    if (!vNombre || !vSlug) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'El nombre y slug son obligatorios'
        });
        return;
    }
    
    Swal.fire({
        title: 'Creando categoría...',
        text: 'Por favor espera',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });
    
    const formData = new FormData();
    formData.append('vNombre', vNombre);
    formData.append('vSlug', vSlug);
    formData.append('tDescripcion', tDescripcion || '');
    formData.append('bActivo', bActivo);
    if (idCategoriaPadre) {
        formData.append('id_categoria_padre', idCategoriaPadre);
    }
    if (categoriaModalImagenFile) {
        formData.append('vImagen', categoriaModalImagenFile);
    }
    
    fetch('{{ route("categorias.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
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
            
            const select = document.getElementById('id_categoria');
            if (select) {
                const option = document.createElement('option');
                option.value = data.categoria.id_categoria;
                option.innerHTML = (data.categoria.id_categoria_padre ? '↳ ' : '🏠 ') + data.categoria.vNombre;
                select.appendChild(option);
                select.value = data.categoria.id_categoria;
            }
            
            if (modalCategoria) modalCategoria.hide();
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
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error de conexión al servidor'
        });
    });
}

function guardarMarca() {
    const vNombre = document.getElementById('vNombre_marca_modal')?.value.trim();
    const tDescripcion = document.getElementById('tDescripcion_marca_modal')?.value;
    
    if (!vNombre) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'El nombre de la marca es obligatorio'
        });
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
            vNombre: vNombre,
            tDescripcion: tDescripcion || ''
        })
    })
    .then(response => response.json())
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
            
            const select = document.getElementById('id_marca');
            if (select) {
                const option = document.createElement('option');
                option.value = data.marca.id_marca;
                option.textContent = data.marca.vNombre;
                select.appendChild(option);
                select.value = data.marca.id_marca;
            }
            
            if (modalMarca) modalMarca.hide();
        } else {
            let errorMessage = data.message || 'Error al crear la marca';
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
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error de conexión al servidor'
        });
    });
}

function guardarEtiqueta() {
    const vNombre = document.getElementById('vNombre_eti_modal')?.value.trim();
    const tDescripcion = document.getElementById('tDescripcion_eti_modal')?.value;
    
    if (!vNombre) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'El nombre de la etiqueta es obligatorio'
        });
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
            vNombre: vNombre,
            tDescripcion: tDescripcion || ''
        })
    })
    .then(response => response.json())
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
            
            const container = document.getElementById('etiquetas-container');
            if (container) {
                const row = container.querySelector('.row');
                if (row) {
                    const col = document.createElement('div');
                    col.className = 'col-md-6 col-6 mb-2 etiqueta-item';
                    
                    const divCheck = document.createElement('div');
                    divCheck.className = 'form-check';
                    
                    const input = document.createElement('input');
                    input.type = 'checkbox';
                    input.name = 'etiquetas[]';
                    input.value = data.etiqueta.id_etiqueta;
                    input.className = 'form-check-input';
                    input.id = 'etiqueta_' + data.etiqueta.id_etiqueta;
                    input.checked = true;
                    
                    const label = document.createElement('label');
                    label.className = 'form-check-label';
                    label.htmlFor = 'etiqueta_' + data.etiqueta.id_etiqueta;
                    
                    const span = document.createElement('span');
                    span.className = 'badge bg-secondary';
                    span.textContent = data.etiqueta.vNombre;
                    
                    label.appendChild(span);
                    divCheck.appendChild(input);
                    divCheck.appendChild(label);
                    col.appendChild(divCheck);
                    row.appendChild(col);
                }
            }
            
            if (modalEtiqueta) modalEtiqueta.hide();
        } else {
            let errorMessage = data.message || 'Error al crear la etiqueta';
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
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error de conexión al servidor'
        });
    });
}

async function guardarAtributo() {
    const vNombre = document.getElementById('vNombre_attr_modal')?.value.trim();
    const vSlug = document.getElementById('vSlug_attr_modal')?.value.trim();
    const tDescripcion = document.getElementById('tDescripcion_attr_modal')?.value;
    
    if (!vNombre) {
        Swal.fire({ icon: 'error', title: 'Error', text: 'El nombre del atributo es obligatorio' });
        return;
    }
    
    Swal.fire({
        title: 'Creando atributo...',
        text: 'Por favor espera',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });
    
    // Crear FormData
    const formData = new FormData();
    formData.append('vNombre', vNombre);
    if (vSlug) formData.append('vSlug', vSlug);
    if (tDescripcion) formData.append('tDescripcion', tDescripcion);
    
    // Obtener token CSRF del formulario principal
    const csrfToken = document.querySelector('#productoForm input[name="_token"]')?.value ||
                      document.querySelector('meta[name="csrf-token"]')?.content;
    
    if (!csrfToken) {
        Swal.close();
        Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo obtener el token de seguridad. Recarga la página.' });
        return;
    }
    formData.append('_token', csrfToken);
    
    try {
        const response = await fetch('{{ route("atributos.quick-create") }}', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });
        
        if (response.status === 419) {
            throw new Error('Token CSRF inválido. Recarga la página.');
        }
        
        const data = await response.json();
        if (!response.ok) throw data;
        
        Swal.close();
        
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            });
            
            // ==== AGREGAR EL NUEVO ATRIBUTO AL DOM SIN RECARGAR ====
            agregarAtributoAlListado(data.atributo);
            
            if (modalAtributo) modalAtributo.hide();
            
            // Limpiar campos del modal
            document.getElementById('vNombre_attr_modal').value = '';
            document.getElementById('vSlug_attr_modal').value = '';
            document.getElementById('tDescripcion_attr_modal').value = '';
        } else {
            let errorMessage = data.message || 'Error al crear el atributo';
            if (data.errors) {
                errorMessage = Object.values(data.errors).flat().join('<br>');
            }
            Swal.fire({ icon: 'error', title: 'Error', html: errorMessage });
        }
    } catch (error) {
        Swal.close();
        console.error('Error:', error);
        Swal.fire({ icon: 'error', title: 'Error', text: error.message || 'Error de conexión' });
    }
}

// Función para agregar el nuevo atributo al listado de atributos
function agregarAtributoAlListado(atributo) {
    // Buscar el contenedor de atributos
    const atributosContainer = document.getElementById('atributos-container');
    
    if (!atributosContainer) {
        console.warn('No se encontró el contenedor de atributos');
        return;
    }
    
    // Si no hay atributos (mensaje de "no hay atributos"), limpiar y mostrar el contenedor
    const noAtributosMsg = document.getElementById('no-atributos-msg');
    if (noAtributosMsg) {
        noAtributosMsg.style.display = 'none';
    }
    
    // Crear el HTML del nuevo atributo
    const nuevoAtributoHtml = `
        <div class="col-md-6 mb-4 atributo-item" data-atributo-id="${atributo.id_atributo}">
            <div class="card border h-100">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <div class="form-check">
                        <input type="checkbox" 
                               class="form-check-input atributo-activo-checkbox" 
                               id="atributo-activo-${atributo.id_atributo}"
                               data-atributo-id="${atributo.id_atributo}"
                               data-atributo-nombre="${atributo.vNombre}">
                        <label class="form-check-label fw-bold" for="atributo-activo-${atributo.id_atributo}">
                            ${atributo.vNombre}
                            <span class="badge bg-secondary ms-2">0 valores</span>
                        </label>
                    </div>
                    <div>
                        <span class="badge bg-warning text-dark atributo-estado-badge" id="estado-${atributo.id_atributo}" style="display: none;">
                            <i class="fas fa-check-circle me-1"></i>Activo
                        </span>
                        <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="mostrarFormularioValor(${atributo.id_atributo}, '${atributo.vNombre}')">
                            <i class="fas fa-plus-circle me-1"></i>Agregar Valor
                        </button>
                    </div>
                </div>
                
                <div class="card-body atributo-valores-container" id="valores-container-${atributo.id_atributo}" style="display: none; background-color: white;">
                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Este atributo no tiene valores. 
                        <button type="button" class="btn btn-link p-0 ms-1" onclick="mostrarFormularioValor(${atributo.id_atributo}, '${atributo.vNombre}')">
                            Crear primer valor
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Agregar el nuevo atributo al contenedor
    atributosContainer.insertAdjacentHTML('beforeend', nuevoAtributoHtml);
    
    // Agregar los event listeners necesarios para el nuevo atributo
    const nuevoCheckbox = document.getElementById(`atributo-activo-${atributo.id_atributo}`);
    if (nuevoCheckbox) {
        nuevoCheckbox.addEventListener('change', function() {
            const atributoId = this.dataset.atributoId;
            const valoresContainer = document.getElementById(`valores-container-${atributoId}`);
            const estadoBadge = document.getElementById(`estado-${atributoId}`);
            
            if (this.checked) {
                if (valoresContainer) valoresContainer.style.display = 'block';
                if (estadoBadge) estadoBadge.style.display = 'inline-block';
                if (!atributosActivos[atributoId]) {
                    atributosActivos[atributoId] = {
                        id: atributoId,
                        nombre: this.dataset.atributoNombre,
                        valores: {}
                    };
                }
            } else {
                if (valoresContainer) valoresContainer.style.display = 'none';
                if (estadoBadge) estadoBadge.style.display = 'none';
                delete atributosActivos[atributoId];
            }
            actualizarPestanasValores();
            actualizarResumenAtributos();
        });
    }
    
    // Actualizar el contador de atributos si existe
    const totalAtributosBadge = document.getElementById('total-atributos-activos-badge');
    if (totalAtributosBadge) {
        const atributosCount = document.querySelectorAll('.atributo-item').length;
        totalAtributosBadge.textContent = `${atributosCount} atributos disponibles`;
    }
    
    console.log(`Atributo "${atributo.vNombre}" agregado correctamente`);
}

function guardarImpuesto() {
    const vNombre = document.getElementById('vNombre_impuesto_modal')?.value.trim();
    const eTipo = document.getElementById('eTipo_impuesto_modal')?.value;
    const dPorcentaje = document.getElementById('dPorcentaje_impuesto_modal')?.value;
    const bActivo = document.getElementById('bActivo_impuesto_modal')?.checked ? 1 : 0;
    
    if (!vNombre || !eTipo || !dPorcentaje) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Todos los campos obligatorios deben estar llenos'
        });
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
            vNombre: vNombre,
            eTipo: eTipo,
            dPorcentaje: dPorcentaje,
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
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            });
            
            const selectProducto = document.getElementById('id_impuesto');
            if (selectProducto) {
                let existe = false;
                for (let i = 0; i < selectProducto.options.length; i++) {
                    if (selectProducto.options[i].value == data.impuesto.id_impuesto) {
                        existe = true;
                        break;
                    }
                }
                
                if (!existe) {
                    const option = document.createElement('option');
                    option.value = data.impuesto.id_impuesto;
                    option.setAttribute('data-porcentaje', data.impuesto.dPorcentaje);
                    option.setAttribute('data-tipo', data.impuesto.eTipo);
                    option.textContent = data.impuesto.vNombre + ' (' + data.impuesto.eTipo + ' - ' + parseFloat(data.impuesto.dPorcentaje).toFixed(2) + '%)';
                    selectProducto.appendChild(option);
                }
                
                selectProducto.value = data.impuesto.id_impuesto;
                actualizarPrecioFinal();
            }
            
            actualizarSelectsImpuestosVariaciones(data.impuesto);
            
            if (modalImpuesto) modalImpuesto.hide();
            
            document.getElementById('vNombre_impuesto_modal').value = '';
            document.getElementById('eTipo_impuesto_modal').value = '';
            document.getElementById('dPorcentaje_impuesto_modal').value = '';
            
        } else {
            let errorMessage = data.message || 'Error al crear el impuesto';
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
        
        let errorMessage = 'Error de conexión al servidor';
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

async function guardarValorAtributo() {
    const atributoId = document.getElementById('valor_atributo_id')?.value;
    const vValor = document.getElementById('vValor_modal')?.value.trim();
    const vSlug = document.getElementById('vSlug_valor_modal')?.value.trim();
    const bActivo = document.getElementById('bActivo_valor_modal')?.checked ? 1 : 0;
    
    if (!atributoId || !vValor) {
        Swal.fire({ icon: 'error', title: 'Error', text: 'El valor es obligatorio' });
        return;
    }
    
    Swal.fire({
        title: 'Creando valor...',
        text: 'Por favor espera',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });
    
    // Crear FormData
    const formData = new FormData();
    formData.append('vValor', vValor);
    formData.append('vSlug', vSlug || '');
    formData.append('bActivo', bActivo);
    
    // Obtener token
    const csrfToken = document.querySelector('#productoForm input[name="_token"]')?.value ||
                      document.querySelector('meta[name="csrf-token"]')?.content;
    
    if (!csrfToken) {
        Swal.close();
        Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo obtener el token de seguridad.' });
        return;
    }
    formData.append('_token', csrfToken);
    
    try {
        const response = await fetch(`/atributos/${atributoId}/valores-quick`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });
        
        if (response.status === 419) {
            throw new Error('Token CSRF inválido. Recarga la página.');
        }
        
        const data = await response.json();
        if (!response.ok) throw data;
        
        Swal.close();
        
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: data.message || 'Valor creado exitosamente',
                timer: 2000,
                showConfirmButton: false
            });
            
            // ==== AGREGAR EL NUEVO VALOR AL ATRIBUTO SIN RECARGAR ====
            agregarValorAlAtributo(atributoId, data.valor);
            
            if (valorModal) valorModal.hide();
            
            // Limpiar campos
            document.getElementById('vValor_modal').value = '';
            document.getElementById('vSlug_valor_modal').value = '';
            document.getElementById('bActivo_valor_modal').checked = true;
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Error al crear el valor' });
        }
    } catch (error) {
        Swal.close();
        console.error('Error:', error);
        Swal.fire({ icon: 'error', title: 'Error', text: error.message || 'Error en la solicitud' });
    }
}

// Función para agregar un nuevo valor al atributo
function agregarValorAlAtributo(atributoId, valor) {
    // Buscar el contenedor de valores del atributo
    const valoresContainer = document.getElementById(`valores-container-${atributoId}`);
    
    if (!valoresContainer) {
        console.warn(`No se encontró el contenedor de valores para el atributo ${atributoId}`);
        return;
    }
    
    // Verificar si ya tiene valores o está vacío
    const tieneValores = valoresContainer.querySelectorAll('.valor-checkbox').length > 0;
    
    // Si no tenía valores, eliminar el mensaje de alerta y agregar la estructura
    if (!tieneValores) {
        valoresContainer.innerHTML = `
            <div class="mb-3">
                <div class="form-check">
                    <input type="checkbox" 
                           class="form-check-input seleccionar-todos-checkbox" 
                           id="seleccionar-todos-${atributoId}"
                           data-atributo-id="${atributoId}">
                    <label class="form-check-label">
                        <strong>Seleccionar todos</strong>
                    </label>
                </div>
            </div>
            <hr class="my-2">
            <div class="row" id="valores-row-${atributoId}"></div>
        `;
        
        // Agregar event listener al checkbox "seleccionar todos"
        const seleccionarTodos = document.getElementById(`seleccionar-todos-${atributoId}`);
        if (seleccionarTodos) {
            seleccionarTodos.addEventListener('change', function() {
                const atributoId = this.dataset.atributoId;
                const valoresContainer = document.getElementById(`valores-container-${atributoId}`);
                if (!valoresContainer) return;
                const valorCheckboxes = valoresContainer.querySelectorAll('.valor-checkbox');
                valorCheckboxes.forEach(cb => {
                    cb.checked = this.checked;
                    const atributoNombre = cb.dataset.atributoNombre;
                    const valorId = cb.value;
                    const valorNombre = cb.dataset.valorNombre;
                    if (this.checked) {
                        if (!atributosActivos[atributoId]) {
                            atributosActivos[atributoId] = { id: atributoId, nombre: atributoNombre, valores: {} };
                        }
                        atributosActivos[atributoId].valores[valorId] = { id: valorId, nombre: valorNombre, atributoId: atributoId, atributoNombre: atributoNombre };
                    } else {
                        if (atributosActivos[atributoId] && atributosActivos[atributoId].valores[valorId]) {
                            delete atributosActivos[atributoId].valores[valorId];
                        }
                        if (atributosActivos[atributoId] && Object.keys(atributosActivos[atributoId].valores).length === 0) {
                            delete atributosActivos[atributoId];
                        }
                    }
                });
                actualizarPestanasValores();
                actualizarResumenAtributos();
            });
        }
    }
    
    // Obtener la fila de valores
    const valoresRow = document.getElementById(`valores-row-${atributoId}`) || valoresContainer.querySelector('.row');
    if (!valoresRow) return;
    
    // Obtener el nombre del atributo
    const atributoCheckbox = document.getElementById(`atributo-activo-${atributoId}`);
    const atributoNombre = atributoCheckbox ? atributoCheckbox.dataset.atributoNombre : 'Atributo';
    
    // Crear el HTML del nuevo valor
    const nuevoValorHtml = `
        <div class="col-md-6 mb-2">
            <div class="form-check">
                <input type="checkbox" 
                       name="atributos[${atributoId}][]" 
                       value="${valor.id_atributo_valor}" 
                       class="form-check-input valor-checkbox"
                       id="valor-${valor.id_atributo_valor}"
                       data-atributo-id="${atributoId}"
                       data-atributo-nombre="${atributoNombre}"
                       data-valor-nombre="${valor.vValor}">
                <label class="form-check-label" for="valor-${valor.id_atributo_valor}">
                    ${valor.vValor}
                </label>
            </div>
        </div>
    `;
    
    // Agregar el nuevo valor
    valoresRow.insertAdjacentHTML('beforeend', nuevoValorHtml);
    
    // Actualizar el badge de cantidad de valores
    const badge = document.querySelector(`#atributo-activo-${atributoId} + label .badge`);
    if (badge) {
        const valoresCount = valoresContainer.querySelectorAll('.valor-checkbox').length;
        badge.textContent = `${valoresCount} valores`;
    }
    
    // Agregar event listener al nuevo checkbox
    const nuevoCheckbox = document.getElementById(`valor-${valor.id_atributo_valor}`);
    if (nuevoCheckbox) {
        nuevoCheckbox.addEventListener('change', function() {
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
                atributosActivos[atributoId] = { id: atributoId, nombre: atributoNombre, valores: {} };
            }
            
            if (this.checked) {
                atributosActivos[atributoId].valores[valorId] = { id: valorId, nombre: valorNombre, atributoId: atributoId, atributoNombre: atributoNombre };
            } else {
                if (atributosActivos[atributoId] && atributosActivos[atributoId].valores[valorId]) {
                    delete atributosActivos[atributoId].valores[valorId];
                }
                if (atributosActivos[atributoId] && Object.keys(atributosActivos[atributoId].valores).length === 0) {
                    delete atributosActivos[atributoId];
                }
            }
            
            actualizarPestanasValores();
            actualizarResumenAtributos();
        });
    }
    
    console.log(`Valor "${valor.vValor}" agregado al atributo ID ${atributoId}`);
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

function limpiarFormularioEtiqueta() {
    document.getElementById('vNombre_eti').value = '';
    document.getElementById('tDescripcion_eti').value = '';
}

function limpiarFormularioImpuesto() {
    document.getElementById('vNombre_impuesto').value = '';
    document.getElementById('eTipo_impuesto').value = '';
    document.getElementById('dPorcentaje_impuesto').value = '';
    document.getElementById('bActivo_impuesto').checked = true;
}

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
        
        if (file.size > 2 * 1024 * 1024) {
            Swal.fire({
                icon: 'error',
                title: 'Archivo demasiado grande',
                text: 'La imagen no puede exceder los 2MB'
            });
            input.value = '';
            return;
        }
        
        categoriaImagenFile = file;
        
        const reader = new FileReader();
        reader.onload = function(e) {
            if (previewImg) previewImg.src = e.target.result;
            if (preview) preview.style.display = 'block';
        }
        reader.readAsDataURL(file);
    }
}

function cancelarImagenCategoria() {
    const preview = document.getElementById('categoriaImagePreview');
    const input = document.getElementById('vImagen_categoria');
    
    if (preview) preview.style.display = 'none';
    if (input) input.value = '';
    categoriaImagenFile = null;
}

function resetearInputImagen() {
    cancelarImagenCategoria();
}

function previewImagenCategoriaModal(input) {
    const preview = document.getElementById('categoriaModalImagePreview');
    const previewImg = document.getElementById('categoriaModalPreviewImg');
    
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
        
        if (file.size > 2 * 1024 * 1024) {
            Swal.fire({
                icon: 'error',
                title: 'Archivo demasiado grande',
                text: 'La imagen no puede exceder los 2MB'
            });
            input.value = '';
            return;
        }
        
        categoriaModalImagenFile = file;
        
        const reader = new FileReader();
        reader.onload = function(e) {
            if (previewImg) previewImg.src = e.target.result;
            if (preview) preview.style.display = 'block';
        }
        reader.readAsDataURL(file);
    }
}

function cancelarImagenCategoriaModal() {
    const preview = document.getElementById('categoriaModalImagePreview');
    const input = document.getElementById('vImagen_categoria_modal');
    
    if (preview) preview.style.display = 'none';
    if (input) input.value = '';
    categoriaModalImagenFile = null;
}

// ============ CONFIGURAR EVENTOS PARA ACTUALIZACIÓN EN TIEMPO REAL ============
document.addEventListener('DOMContentLoaded', function() {
    // ============ INICIALIZAR MODALES ============
    try {
        modalCategoria = new bootstrap.Modal(document.getElementById('modalCategoria'));
        modalMarca = new bootstrap.Modal(document.getElementById('modalMarca'));
        modalEtiqueta = new bootstrap.Modal(document.getElementById('modalEtiqueta'));
        modalAtributo = new bootstrap.Modal(document.getElementById('modalAtributo'));
        modalImpuesto = new bootstrap.Modal(document.getElementById('modalImpuesto'));
        valorModal = new bootstrap.Modal(document.getElementById('crearValorModal'));
    } catch(e) { console.error('Error al inicializar modales:', e); }
    
    // ============ EVENTOS DEL PRODUCTO PRINCIPAL ============
    const fechaInicioInput = document.getElementById('dFecha_inicio_descuento');
    const fechaFinInput = document.getElementById('dFecha_fin_descuento');
    const precioDescuentoInput = document.getElementById('dPrecio_descuento');
    const tieneDescuentoCheckbox = document.getElementById('bTiene_descuento');
    const precioVentaInput = document.getElementById('dPrecio_venta');
    const impuestoSelect = document.getElementById('id_impuesto');
    
    if (precioVentaInput) {
        precioVentaInput.addEventListener('input', actualizarPrecioFinal);
        precioVentaInput.addEventListener('change', actualizarPrecioFinal);
    }
    
    if (tieneDescuentoCheckbox) {
        tieneDescuentoCheckbox.addEventListener('change', function() {
            toggleDescuentoFields();
            actualizarPrecioFinal();
        });
    }
    
    if (precioDescuentoInput) {
        precioDescuentoInput.addEventListener('input', function() {
            validarPrecioDescuentoProducto();
            actualizarPrecioFinal();
        });
        precioDescuentoInput.addEventListener('change', function() {
            validarPrecioDescuentoProducto();
            actualizarPrecioFinal();
        });
    }
    
    if (fechaInicioInput) {
        fechaInicioInput.addEventListener('change', function() {
            validarFechasDescuento();
            actualizarPrecioFinal();
        });
        fechaInicioInput.addEventListener('blur', actualizarPrecioFinal);
    }
    
    if (fechaFinInput) {
        fechaFinInput.addEventListener('change', function() {
            validarFechasDescuento();
            actualizarPrecioFinal();
        });
        fechaFinInput.addEventListener('blur', actualizarPrecioFinal);
    }
    
    if (impuestoSelect) {
        impuestoSelect.addEventListener('change', actualizarPrecioFinal);
    }
    
    // ============ EVENTOS PARA ATRIBUTOS Y VARIACIONES ============
    document.querySelectorAll('.atributo-activo-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const atributoId = this.dataset.atributoId;
            const valoresContainer = document.getElementById(`valores-container-${atributoId}`);
            const estadoBadge = document.getElementById(`estado-${atributoId}`);
            
            if (this.checked) {
                if (valoresContainer) valoresContainer.style.display = 'block';
                if (estadoBadge) estadoBadge.style.display = 'inline-block';
                if (!atributosActivos[atributoId]) {
                    atributosActivos[atributoId] = {
                        id: atributoId,
                        nombre: this.dataset.atributoNombre,
                        valores: {}
                    };
                }
            } else {
                if (valoresContainer) valoresContainer.style.display = 'none';
                if (estadoBadge) estadoBadge.style.display = 'none';
                delete atributosActivos[atributoId];
            }
            actualizarPestanasValores();
            actualizarResumenAtributos();
        });
    });
    
    document.querySelectorAll('.seleccionar-todos-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const atributoId = this.dataset.atributoId;
            const valoresContainer = document.getElementById(`valores-container-${atributoId}`);
            if (!valoresContainer) return;
            
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
                    if (atributosActivos[atributoId] && atributosActivos[atributoId].valores[valorId]) {
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
                if (atributosActivos[atributoId] && atributosActivos[atributoId].valores[valorId]) {
                    delete atributosActivos[atributoId].valores[valorId];
                    if (Object.keys(atributosActivos[atributoId].valores).length === 0) {
                        delete atributosActivos[atributoId];
                    }
                }
            }
            
            actualizarPestanasValores();
            actualizarResumenAtributos();
        });
    });
    
    // ============ EVENT LISTENERS PARA FECHAS Y DESCUENTOS DE VARIACIONES ============
    document.addEventListener('change', function(e) {
        // Cuando cambia la fecha de inicio de una variación
        if (e.target.id && e.target.id.startsWith('fecha-inicio-')) {
            const valorKey = e.target.id.replace('fecha-inicio-', '');
            validarFechasDescuentoVariacion(e.target.id, `fecha-fin-${valorKey}`, valorKey);
            actualizarPrecioFinalVariacion(valorKey);
        }
        // Cuando cambia la fecha de fin de una variación
        if (e.target.id && e.target.id.startsWith('fecha-fin-')) {
            const valorKey = e.target.id.replace('fecha-fin-', '');
            validarFechasDescuentoVariacion(`fecha-inicio-${valorKey}`, e.target.id, valorKey);
            actualizarPrecioFinalVariacion(valorKey);
        }
        // Cuando se activa/desactiva el checkbox de descuento de una variación
        if (e.target.id && e.target.id.startsWith('descuento-')) {
            const valorKey = e.target.id.replace('descuento-', '');
            toggleDescuentoVariacion(e.target, valorKey);
            actualizarPrecioFinalVariacion(valorKey);
        }
    });
    
    // ============ INICIALIZAR RESÚMENES ============
    actualizarResumenAtributos();
    actualizarPestanasValores();
    setTimeout(actualizarPrecioFinal, 100);
});

// ============ EVENT LISTENERS ADICIONALES PARA ATRIBUTOS ============
document.querySelectorAll('.atributo-activo-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const atributoId = this.dataset.atributoId;
        const valoresContainer = document.getElementById(`valores-container-${atributoId}`);
        const estadoBadge = document.getElementById(`estado-${atributoId}`);
        
        if (this.checked) {
            if (valoresContainer) valoresContainer.style.display = 'block';
            if (estadoBadge) estadoBadge.style.display = 'inline-block';
            
            if (!atributosActivos[atributoId]) {
                atributosActivos[atributoId] = {
                    id: atributoId,
                    nombre: this.dataset.atributoNombre,
                    valores: {}
                };
            }
        } else {
            if (valoresContainer) valoresContainer.style.display = 'none';
            if (estadoBadge) estadoBadge.style.display = 'none';
            delete atributosActivos[atributoId];
        }
        
        actualizarPestanasValores();
        actualizarResumenAtributos();
    });
});

document.querySelectorAll('.seleccionar-todos-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const atributoId = this.dataset.atributoId;
        const valoresContainer = document.getElementById(`valores-container-${atributoId}`);
        if (!valoresContainer) return;
        
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
                if (atributosActivos[atributoId] && atributosActivos[atributoId].valores[valorId]) {
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
            if (atributosActivos[atributoId] && atributosActivos[atributoId].valores[valorId]) {
                delete atributosActivos[atributoId].valores[valorId];
                if (Object.keys(atributosActivos[atributoId].valores).length === 0) {
                    delete atributosActivos[atributoId];
                }
            }
        }
        
        actualizarPestanasValores();
        actualizarResumenAtributos();
    });
});

// ============ MANEJO DEL ENVÍO DEL FORMULARIO PRINCIPAL ============
document.getElementById('productoForm').addEventListener('submit', function(event) {
    event.preventDefault();
    
    // Primero validar atributos incompletos
    if (!validarAtributosAntesDeEnviar()) {
        return false;
    }
    
    // Luego las demás validaciones
    if (!validarCamposUnicosAntesDeEnviar()) {
        return false;
    }
    
    if (!validarTamañoTotalAntesDeEnviar()) {
        return false;
    }

    
    const form = this;
    const formData = new FormData(form);
    
    // IMPORTANTE: Las imágenes ya están en formData por el input file,
    // pero si selectedImages tiene imágenes, aseguramos que se agreguen
    if (selectedImages.length > 0) {
        // Eliminar las entradas existentes de imagenes[] para evitar duplicados
        formData.delete('imagenes[]');
        
        // Agregar cada imagen seleccionada
        selectedImages.forEach((image) => {
            formData.append('imagenes[]', image.file);
        });
    }
    
    // Agregar imágenes de variaciones
    Object.keys(imagenesVariacion).forEach(valorKey => {
        if (imagenesVariacion[valorKey] && imagenesVariacion[valorKey].imagenes) {
            imagenesVariacion[valorKey].imagenes.forEach((img, idx) => {
                formData.append(`variaciones[${valorKey}][imagenes_adicionales][${idx}]`, img.file);
            });
        }
    });
    
    Swal.fire({
        title: 'Guardando producto...',
        text: 'Por favor espera',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (response.redirected) {
            window.location.href = response.url;
        } else if (response.ok) {
            return response.json();
        } else {
            throw new Error('Error al guardar el producto');
        }
    })
    .then(data => {
        Swal.close();
        if (data && data.redirect) {
            window.location.href = data.redirect;
        } else {
            window.location.href = '{{ route("productos.index") }}';
        }
    })
    .catch(error => {
        Swal.close();
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Hubo un problema al guardar el producto'
        });
    });
});

// Inicializar modales
document.addEventListener('DOMContentLoaded', function() {
    try {
        const modalCategoriaEl = document.getElementById('modalCategoria');
        const modalMarcaEl = document.getElementById('modalMarca');
        const modalEtiquetaEl = document.getElementById('modalEtiqueta');
        const modalAtributoEl = document.getElementById('modalAtributo');
        const modalImpuestoEl = document.getElementById('modalImpuesto');
        const crearValorModalEl = document.getElementById('crearValorModal');
        
        if (modalCategoriaEl) modalCategoria = new bootstrap.Modal(modalCategoriaEl);
        if (modalMarcaEl) modalMarca = new bootstrap.Modal(modalMarcaEl);
        if (modalEtiquetaEl) modalEtiqueta = new bootstrap.Modal(modalEtiquetaEl);
        if (modalAtributoEl) modalAtributo = new bootstrap.Modal(modalAtributoEl);
        if (modalImpuestoEl) modalImpuesto = new bootstrap.Modal(modalImpuestoEl);
        if (crearValorModalEl) valorModal = new bootstrap.Modal(crearValorModalEl);
    } catch (e) {
        console.error('Error al inicializar modales:', e);
    }
});
</script>
@endpush

@endsection