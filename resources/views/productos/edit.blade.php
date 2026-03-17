@extends('admin.productos.administrar-productos')

@section('title', 'Editar Producto - ' . $producto->vNombre)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-edit me-2"></i>Editar Producto: {{ $producto->vNombre }}</h1>
        <a href="{{ route('productos.show', $producto->id_producto) }}" class="btn btn-info">
            <i class="fas fa-eye me-1"></i> Ver Detalle
        </a>
    </div>

    
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

    <form action="{{ route('productos.update', $producto->id_producto) }}" method="POST" enctype="multipart/form-data" id="productoForm">
        @csrf
        @method('PUT')

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
                                   value="{{ old('vCodigo_barras', $producto->vCodigo_barras) }}" 
                                   maxlength="15" 
                                   required
                                   oninput="validarSKU(this)"
                                   pattern="[A-Za-z0-9\-]+"
                                   title="Solo letras, números y guiones (máximo 15 caracteres)"
                                   autocomplete="off">
                            @error('vCodigo_barras')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Ej: AGAVE001, MEZCAL2024 (15 caracteres máximo, solo letras, números y guiones)</small>
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
                                       value="{{ old('dPrecio_compra', $producto->dPrecio_compra) }}" 
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
                                       value="{{ old('dPrecio_venta', $producto->dPrecio_venta) }}" 
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
                                   value="{{ old('iStock', $producto->iStock) }}" 
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
                                       {{ old('bTiene_descuento', $producto->bTiene_oferta) ? 'checked' : '' }}
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
                                       {{ old('bActivo', $producto->bActivo) ? 'checked' : '' }}>
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
                <div id="descuentoFields" style="{{ $producto->bTiene_oferta ? 'display: block;' : 'display: none;' }}">
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
                                           value="{{ old('dPrecio_descuento', $producto->dPrecio_oferta) }}" 
                                           oninput="validarPrecio(this); validarPrecioDescuentoProductoInstantaneo(this); actualizarPrecioFinal();"
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
                                       value="{{ old('dFecha_inicio_descuento', $producto->dFecha_inicio_oferta ? \Carbon\Carbon::parse($producto->dFecha_inicio_oferta)->format('Y-m-d') : '') }}"
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
                                       value="{{ old('dFecha_fin_descuento', $producto->dFecha_fin_oferta ? \Carbon\Carbon::parse($producto->dFecha_fin_oferta)->format('Y-m-d') : '') }}"
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
                                       value="{{ old('vMotivo_descuento', $producto->vMotivo_oferta) }}"
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
                                   value="{{ old('dPeso', $producto->dPeso) }}"
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
                                   value="{{ old('dLargo_cm', $producto->dLargo_cm) }}"
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
                                   value="{{ old('dAncho_cm', $producto->dAncho_cm) }}"
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
                                   value="{{ old('dAlto_cm', $producto->dAlto_cm) }}"
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
                                <option value="estandar" {{ old('vClase_envio', $producto->vClase_envio) == 'estandar' ? 'selected' : '' }}>Estándar</option>
                                <option value="express" {{ old('vClase_envio', $producto->vClase_envio) == 'express' ? 'selected' : '' }}>Express</option>
                                <option value="fragil" {{ old('vClase_envio', $producto->vClase_envio) == 'fragil' ? 'selected' : '' }}>Frágil</option>
                                <option value="grandes_dimensiones" {{ old('vClase_envio', $producto->vClase_envio) == 'grandes_dimensiones' ? 'selected' : '' }}>Grandes dimensiones</option>
                            </select>
                            @error('vClase_envio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Determina el tipo de envío para este producto</small>
                            
                            <!-- Mostrar la clase de envío seleccionada actualmente -->
                            @if($producto->vClase_envio)
                                <div class="mt-2 p-2 bg-light rounded">
                                    <strong>Clase actual:</strong> 
                                    @php
                                        $claseText = '';
                                        $claseClass = '';
                                        switch($producto->vClase_envio) {
                                            case 'estandar':
                                                $claseText = 'Estándar';
                                                $claseClass = 'bg-primary';
                                                break;
                                            case 'express':
                                                $claseText = 'Express';
                                                $claseClass = 'bg-success';
                                                break;
                                            case 'fragil':
                                                $claseText = 'Frágil';
                                                $claseClass = 'bg-warning text-dark';
                                                break;
                                            case 'grandes_dimensiones':
                                                $claseText = 'Grandes dimensiones';
                                                $claseClass = 'bg-danger';
                                                break;
                                            default:
                                                $claseText = $producto->vClase_envio;
                                                $claseClass = 'bg-secondary';
                                        }
                                    @endphp
                                    <span class="badge {{ $claseClass }}">{{ $claseText }}</span>
                                    <small class="d-block text-muted mt-1">Valor guardado: "{{ $producto->vClase_envio }}"</small>
                                </div>
                            @else
                                <div class="mt-2 p-2 bg-light rounded">
                                    <span class="text-muted">No hay clase de envío seleccionada</span>
                                </div>
                            @endif
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
                                        function mostrarCategoriasJerarquicamenteEdit($categorias, $nivel = 0, $oldValue = null, $selectedValue = null)
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
                                                
                                                $selected = ($oldValue == $categoria->id_categoria || $selectedValue == $categoria->id_categoria) ? 'selected' : '';
                                                
                                                echo '<option value="' . $categoria->id_categoria . '" ' . $selected . '>' .
                                                     $prefijo . $icono . htmlspecialchars($categoria->vNombre) . 
                                                     '</option>';
                                                
                                                if ($categoria->hijos && $categoria->hijos->count() > 0) {
                                                    mostrarCategoriasJerarquicamenteEdit($categoria->hijos, $nivel + 1, $oldValue, $selectedValue);
                                                }
                                            }
                                        }
                                        
                                        $oldCategoria = old('id_categoria');
                                        $categoriasRaiz = $categorias->where('id_categoria_padre', null)->where('bActivo', true);
                                    @endphp
                                    
                                    @php
                                        mostrarCategoriasJerarquicamenteEdit($categoriasRaiz, 0, $oldCategoria, $producto->id_categoria);
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
                                            {{ old('id_marca', $producto->id_marca) == $marca->id_marca ? 'selected' : '' }}>
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

                    <!-- SECCIÓN DE IMPUESTO (SELECTOR ÚNICO) CON BOTÓN DE CREACIÓN RÁPIDA -->
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
                                                {{ old('id_impuesto', $producto->impuestos->first()->id_impuesto ?? '') == $impuesto->id_impuesto ? 'selected' : '' }}>
                                                {{ $impuesto->vNombre }} ({{ $impuesto->eTipo }} - {{ number_format($impuesto->dPorcentaje, 2) }}%)
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
                                @endif
                            </div>
                            @error('id_impuesto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted mt-2">
                                Selecciona el impuesto que aplica a este producto (opcional)
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
                            <span id="total-imagenes">{{ $producto->getNumeroImagenes() }}</span> archivos
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
                                <i class="fas fa-star text-warning me-1"></i>Imagen Principal
                            </label>
                            <input type="file" name="imagen_principal" id="imagen_principal" 
                                   class="form-control @error('imagen_principal') is-invalid @enderror" 
                                   accept="image/jpeg,image/jpg,image/png"
                                   onchange="previewImagenPrincipal(this)">
                            @error('imagen_principal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Esta será la imagen principal del producto (portada). Formatos: JPG, JPEG, PNG. Máximo 5MB. Dejar vacío para mantener la actual.
                            </small>
                            
                            <!-- Preview de imagen principal actual -->
                            @if($producto->imagen_principal_url)
                                <div id="current_principal_container" class="mt-2">
                                    <div class="border rounded p-2 text-center bg-light position-relative">
                                        <div class="position-absolute top-0 end-0 m-2" style="z-index: 10;">
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-circle" 
                                                    onclick="eliminarImagenPrincipalExistente()"
                                                    style="width: 30px; height: 30px;"
                                                    title="Eliminar imagen principal">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                        <img src="{{ $producto->imagen_principal_url }}" 
                                             class="img-thumbnail" 
                                             style="max-width: 200px; max-height: 200px; object-fit: contain;"
                                             alt="Imagen principal actual">
                                        <div class="mt-2">
                                            <small class="text-muted d-block">Imagen principal actual</small>
                                            <input type="hidden" name="eliminar_imagen_principal" id="eliminar_imagen_principal" value="0">
                                        </div>
                                    </div>
                                </div>
                            @elseif($producto->vImagen_principal)
                                <div id="current_principal_container" class="mt-2">
                                    <div class="border rounded p-2 text-center bg-light position-relative">
                                        <div class="position-absolute top-0 end-0 m-2" style="z-index: 10;">
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-circle" 
                                                    onclick="eliminarImagenPrincipalExistente()"
                                                    style="width: 30px; height: 30px;"
                                                    title="Eliminar imagen principal">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                        <img src="{{ Storage::url($producto->vImagen_principal) }}" 
                                             class="img-thumbnail" 
                                             style="max-width: 200px; max-height: 200px; object-fit: contain;"
                                             alt="Imagen principal actual">
                                        <div class="mt-2">
                                            <small class="text-muted d-block">Imagen principal actual</small>
                                            <input type="hidden" name="eliminar_imagen_principal" id="eliminar_imagen_principal" value="0">
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Preview de nueva imagen principal -->
                            <div id="preview_principal_container" class="mt-2" style="display: none;">
                                <div class="border rounded p-2 text-center bg-light position-relative">
                                    <div class="position-absolute top-0 end-0 m-2" style="z-index: 10;">
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-circle" 
                                                onclick="cancelarImagenPrincipal()"
                                                style="width: 30px; height: 30px;"
                                                title="Quitar nueva imagen">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <img id="preview_principal_img" src="#" 
                                         class="img-thumbnail" 
                                         style="max-width: 200px; max-height: 200px; object-fit: contain;"
                                         alt="Preview imagen principal">
                                    <div class="mt-2">
                                        <small class="text-muted d-block">Nueva imagen principal</small>
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
                                Formatos: GIF. Máximo 10MB. Animación del producto. Dejar vacío para mantener el actual.
                            </small>
                            
                            <!-- Preview de GIF actual -->
                            @if($producto->gif_url)
                                <div id="current_gif_container" class="mt-2">
                                    <div class="border rounded p-2 text-center bg-light position-relative">
                                        <div class="position-absolute top-0 end-0 m-2" style="z-index: 10;">
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-circle" 
                                                    onclick="eliminarGifExistente()"
                                                    style="width: 30px; height: 30px;"
                                                    title="Eliminar GIF">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                        <img src="{{ $producto->gif_url }}" 
                                             class="img-thumbnail" 
                                             style="max-width: 200px; max-height: 200px; object-fit: contain;"
                                             alt="GIF actual">
                                        <div class="mt-2">
                                            <small class="text-muted d-block">GIF actual</small>
                                            <input type="hidden" name="eliminar_gif" id="eliminar_gif" value="0">
                                        </div>
                                    </div>
                                </div>
                            @elseif($producto->vGif)
                                <div id="current_gif_container" class="mt-2">
                                    <div class="border rounded p-2 text-center bg-light position-relative">
                                        <div class="position-absolute top-0 end-0 m-2" style="z-index: 10;">
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-circle" 
                                                    onclick="eliminarGifExistente()"
                                                    style="width: 30px; height: 30px;"
                                                    title="Eliminar GIF">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                        <img src="{{ Storage::url($producto->vGif) }}" 
                                             class="img-thumbnail" 
                                             style="max-width: 200px; max-height: 200px; object-fit: contain;"
                                             alt="GIF actual">
                                        <div class="mt-2">
                                            <small class="text-muted d-block">GIF actual</small>
                                            <input type="hidden" name="eliminar_gif" id="eliminar_gif" value="0">
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Preview de nuevo GIF -->
                            <div id="preview_gif_container" class="mt-2" style="display: none;">
                                <div class="border rounded p-2 text-center bg-light position-relative">
                                    <div class="position-absolute top-0 end-0 m-2" style="z-index: 10;">
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-circle" 
                                                onclick="cancelarGif()"
                                                style="width: 30px; height: 30px;"
                                                title="Quitar nuevo GIF">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <img id="preview_gif" src="#" 
                                         class="img-thumbnail" 
                                         style="max-width: 200px; max-height: 200px; object-fit: contain;"
                                         alt="Preview GIF">
                                    <div class="mt-2">
                                        <small class="text-muted d-block">Nuevo GIF seleccionado</small>
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
                                Puedes seleccionar hasta 7 imágenes adicionales. Las nuevas imágenes se agregarán a las existentes.
                            </small>
                            <div class="mt-2">
                                <span class="badge bg-info" id="selected-images-count">0 archivos nuevos</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Imágenes adicionales actuales -->
                @if($producto->vImagenes_adicionales && count($producto->vImagenes_adicionales) > 0)
                <div class="mt-3">
                    <h6 class="fw-bold mb-2"><i class="fas fa-images me-2"></i>Imágenes adicionales actuales:</h6>
                    <div class="row g-2" id="current-images-container">
                        @foreach($producto->getNombresArchivosImagenesAdicionales() as $index => $nombreArchivo)
                            <div class="col-6 col-md-3 mb-3 current-image-item" data-filename="{{ $nombreArchivo }}">
                                <div class="card border image-preview-card position-relative">
                                    <div class="position-absolute top-0 end-0 m-1 z-index-1">
                                        <button type="button" 
                                                class="btn btn-danger btn-sm rounded-circle" 
                                                style="width: 30px; height: 30px;"
                                                onclick="eliminarImagenAdicionalExistente('{{ $nombreArchivo }}', {{ $index }})"
                                                title="Eliminar imagen">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <img src="{{ Storage::url($producto->vImagenes_adicionales[$index]) }}" 
                                         class="card-img-top" 
                                         style="height: 120px; object-fit: contain; background: #f8f9fa; padding: 8px;"
                                         alt="Imagen adicional {{ $index + 1 }}">
                                    <div class="card-body p-2 text-center">
                                        <small class="text-muted d-block text-truncate">{{ $nombreArchivo }}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <!-- Hidden inputs para imágenes a eliminar -->
                    <div id="imagenes-eliminar-container"></div>
                </div>
                @endif
                
                <!-- Galería de nuevas imágenes adicionales seleccionadas -->
                <div class="mt-3">
                    <h6 class="fw-bold mb-2"><i class="fas fa-images me-2"></i>Nuevas imágenes adicionales seleccionadas:</h6>
                    <div id="selected-images-container" class="row g-2"></div>
                    <div class="alert alert-warning py-2" id="no-imagenes-msg" style="{{ count($producto->vImagenes_adicionales ?? []) > 0 ? 'display: none;' : '' }}">
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
                                      rows="3">{{ old('tDescripcion_corta', $producto->tDescripcion_corta) }}</textarea>
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
                                                       {{ is_array(old('etiquetas', $producto->etiquetas->pluck('id_etiqueta')->toArray())) && in_array($etiqueta->id_etiqueta, old('etiquetas', $producto->etiquetas->pluck('id_etiqueta')->toArray())) ? 'checked' : '' }}
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
                              rows="5">{{ old('tDescripcion_larga', $producto->tDescripcion_larga) }}</textarea>
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
                                <h6 class="text-muted">Precio base (con descuento aplicado)</h6>
                                <h3 class="fw-bold" id="precio-base-display">${{ number_format($producto->ofertaVigente() ? $producto->dPrecio_oferta : $producto->dPrecio_venta, 2) }}</h3>
                                <small class="text-muted" id="precio-original-display" style="{{ $producto->ofertaVigente() ? 'display: block;' : 'display: none;' }}">
                                    Precio original: ${{ number_format($producto->dPrecio_venta, 2) }}
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-white text-dark">
                            <div class="card-body text-center">
                                <h6 class="text-muted">Impuesto</h6>
                                <h3 class="fw-bold" id="total-impuestos-display">
                                    @php
                                        $precioBase = $producto->ofertaVigente() ? $producto->dPrecio_oferta : $producto->dPrecio_venta;
                                        $totalImpuestos = 0;
                                        $porcentaje = 0;
                                        if($producto->impuestos->count() > 0) {
                                            $porcentaje = $producto->impuestos->first()->dPorcentaje ?? 0;
                                            $totalImpuestos = $precioBase * ($porcentaje / 100);
                                        }
                                    @endphp
                                    ${{ number_format($totalImpuestos, 2) }}
                                </h3>
                                <small id="porcentaje-impuestos-display">{{ number_format($porcentaje, 2) }}%</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h6>Precio final (con impuesto)</h6>
                                <h2 class="fw-bold" id="precio-final-display">${{ number_format($precioBase + $totalImpuestos, 2) }}</h2>
                                <small>Este es el precio que verá el cliente</small>
                            </div>
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
                
                @php
                    // Obtener IDs de valores de atributos ya seleccionados en el producto
                    $valoresSeleccionados = $producto->valoresAtributos->pluck('id_atributo_valor')->toArray();
                    
                    // Obtener IDs de atributos que tienen al menos un valor seleccionado
                    $atributosConValores = $producto->valoresAtributos->groupBy('id_atributo')->keys()->toArray();
                @endphp
                
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
                                               data-atributo-nombre="{{ $atributo->vNombre }}"
                                               {{ in_array($atributo->id_atributo, $atributosConValores) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="atributo-activo-{{ $atributo->id_atributo }}" style="color: #495057;">
                                            {{ $atributo->vNombre }}
                                            <span class="badge bg-secondary ms-2">{{ $atributo->valoresActivos->count() }} valores</span>
                                        </label>
                                    </div>
                                    <div>
                                        <span class="badge bg-warning text-dark atributo-estado-badge" id="estado-{{ $atributo->id_atributo }}" style="{{ in_array($atributo->id_atributo, $atributosConValores) ? 'display: inline-block;' : 'display: none;' }}">
                                            <i class="fas fa-check-circle me-1"></i>Activo
                                        </span>
                                        <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="mostrarFormularioValor({{ $atributo->id_atributo }}, '{{ $atributo->vNombre }}')">
                                            <i class="fas fa-plus-circle me-1"></i>Agregar Valor
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="card-body atributo-valores-container" id="valores-container-{{ $atributo->id_atributo }}" style="{{ in_array($atributo->id_atributo, $atributosConValores) ? 'display: block;' : 'display: none;' }} background-color: white;">
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
                                                           {{ in_array($valor->id_atributo_valor, $valoresSeleccionados) ? 'checked' : '' }}>
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
                    <div class="mt-4 p-3 bg-light border rounded" id="resumen-atributos" style="{{ count($atributosConValores) > 0 ? 'display: block;' : 'display: none;' }}">
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
                        <span class="badge bg-light text-dark me-2" id="total-atributos-activos-badge">{{ count($atributosConValores) }} atributos activos</span>
                        <span class="badge bg-warning text-dark" id="total-valores-badge">{{ count($valoresSeleccionados) }} valores</span>
                    </div>
                </div>
            </div>
            
            <div class="card-body" style="background-color: #f8f9fa;">
                @if(isset($atributos) && $atributos->count() > 0)
                    <!-- Mensaje cuando no hay atributos activos -->
                    <div id="no-atributos-activos-message" class="text-center py-5" style="{{ count($valoresSeleccionados) > 0 ? 'display: none;' : 'display: block;' }}">
                        <i class="fas fa-cubes fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">No hay atributos activos</h5>
                        <p class="text-muted">
                            Activa atributos en la sección <strong>"Seleccionar Atributos para Variaciones"</strong> 
                            marcando el checkbox del atributo y seleccionando sus valores.
                        </p>
                    </div>
                    
                    <!-- PESTAÑAS DE VALORES -->
                    <div id="valores-activos-tabs-container" style="{{ count($valoresSeleccionados) > 0 ? 'display: block;' : 'display: none;' }}">
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
                <i class="fas fa-save me-2"></i> Actualizar Producto
            </button>
            <a href="{{ route('productos.show', $producto->id_producto) }}" class="btn btn-info btn-lg px-4">
                <i class="fas fa-eye me-2"></i> Ver Detalle
            </a>
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
                                        function mostrarCategoriasParaQuickEdit($categorias, $nivel = 0) {
                                            foreach($categorias as $categoria) {
                                                $prefijo = str_repeat('&nbsp;&nbsp;&nbsp;', $nivel);
                                                $icono = $nivel == 0 ? '🏠 ' : '↳ ';
                                                echo '<option value="' . $categoria->id_categoria . '">' .
                                                     $prefijo . $icono . htmlspecialchars($categoria->vNombre) . 
                                                     '</option>';
                                                
                                                if ($categoria->hijos && $categoria->hijos->count() > 0) {
                                                    mostrarCategoriasParaQuickEdit($categoria->hijos, $nivel + 1);
                                                }
                                            }
                                        }
                                        
                                        $categoriasRaiz = $categorias->where('id_categoria_padre', null)->where('bActivo', true);
                                    @endphp
                                    
                                    @php mostrarCategoriasParaQuickEdit($categoriasRaiz, 0); @endphp
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
                                <button type="submit" class="btn btn-primary" id="btnCrearCategoria">
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
                        
                        <form id="marcaQuickForm">
                            @csrf
                            <div class="mb-3">
                                <label for="vNombre_marca" class="form-label fw-bold">Nombre de la Marca *</label>
                                <input type="text" class="form-control" id="vNombre_marca" name="vNombre" 
                                       placeholder="Ej: José Cuervo, Patrón, Don Julio" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="tDescripcion_marca" class="form-label fw-bold">Descripción (Opcional)</label>
                                <textarea class="form-control" id="tDescripcion_marca" name="tDescripcion" rows="3" 
                                          placeholder="Describe la marca..."></textarea>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-secondary" onclick="limpiarFormularioMarca()">
                                    <i class="fas fa-undo me-1"></i> Limpiar
                                </button>
                                <button type="submit" class="btn btn-primary" id="btnCrearMarca">
                                    <i class="fas fa-save me-1"></i> Crear Marca
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- TAB: ETIQUETAS (SIN COLOR) -->
                <div class="tab-pane fade" id="etiquetas-content" role="tabpanel">
                    <div class="quick-form" id="quick-etiqueta-form">
                        <h5><i class="fas fa-tag me-2"></i>Crear Nueva Etiqueta</h5>
                        <p class="text-muted small mb-3">Las etiquetas son palabras clave que ayudan a clasificar productos.</p>
                        
                        <form id="etiquetaQuickForm">
                            @csrf
                            <div class="mb-3">
                                <label for="vNombre_eti" class="form-label fw-bold">Nombre de la Etiqueta *</label>
                                <input type="text" class="form-control" id="vNombre_eti" name="vNombre" 
                                       placeholder="Ej: Artesanal, Orgánico, Premium" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="tDescripcion_eti" class="form-label fw-bold">Descripción (Opcional)</label>
                                <textarea class="form-control" id="tDescripcion_eti" name="tDescripcion" rows="2" 
                                          placeholder="Descripción de la etiqueta..."></textarea>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Nota:</strong> Después de crear la etiqueta, estará disponible en la sección "Etiquetas" del formulario.
                            </div>
                            
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary" id="btnCrearEtiqueta">
                                    <i class="fas fa-save me-1"></i> Crear Etiqueta
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- TAB: ATRIBUTOS -->
                <div class="tab-pane fade" id="atributos-content" role="tabpanel">
                    <div class="quick-form" id="quick-atributo-form">
                        <h5><i class="fas fa-list-alt me-2"></i>Crear Nuevo Atributo</h5>
                        <p class="text-muted small mb-3">Los atributos son características que definen las variaciones de un producto (Tamaño, Color, Material, etc.).</p>
                        
                        <form id="atributoQuickForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="vNombre_attr" class="form-label fw-bold">Nombre del Atributo *</label>
                                    <input type="text" class="form-control" id="vNombre_attr" name="vNombre" 
                                           placeholder="Ej: Tamaño, Color, Sabor, Edad"
                                           oninput="quickGenerarSlug(this.value, 'vSlug_attr')" required>
                                    <small class="text-muted">Ejemplos: Tamaño, Color, Material, Sabor</small>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="vSlug_attr" class="form-label fw-bold">Slug (URL amigable)</label>
                                    <input type="text" class="form-control" id="vSlug_attr" name="vSlug" 
                                           placeholder="tamano, color, material">
                                    <small class="text-muted">Se genera automáticamente desde el nombre</small>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="tDescripcion_attr" class="form-label fw-bold">Descripción (Opcional)</label>
                                <textarea class="form-control" id="tDescripcion_attr" name="tDescripcion" rows="2" 
                                          placeholder="Describe el atributo..."></textarea>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Nota:</strong> Después de crear el atributo, podrás agregar valores específicos en la sección <strong>"Seleccionar Atributos para Variaciones"</strong> usando el botón "Agregar Valor" junto a cada atributo.
                            </div>
                            
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary" id="btnCrearAtributo">
                                    <i class="fas fa-save me-1"></i> Crear Atributo
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- TAB: IMPUESTOS -->
                <div class="tab-pane fade" id="impuestos-content" role="tabpanel">
                    <div class="quick-form" id="quick-impuesto-form">
                        <h5><i class="fas fa-file-invoice-dollar me-2"></i>Crear Nuevo Impuesto</h5>
                        <form id="impuestoQuickForm">
                            @csrf
                            <div class="mb-3">
                                <label for="vNombre_impuesto" class="form-label fw-bold">Nombre del Impuesto *</label>
                                <input type="text" class="form-control" id="vNombre_impuesto" name="vNombre" 
                                       placeholder="Ej: IVA, ISR, IEPS" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="eTipo_impuesto" class="form-label fw-bold">Tipo de Impuesto *</label>
                                <select class="form-control" id="eTipo_impuesto" name="eTipo" required>
                                    <option value="">Seleccionar tipo</option>
                                    <option value="IVA">IVA</option>
                                    <option value="IEPS">IEPS</option>
                                    <option value="OTRO">OTRO</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="dPorcentaje_impuesto" class="form-label fw-bold">Porcentaje *</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" max="100" class="form-control" 
                                           id="dPorcentaje_impuesto" name="dPorcentaje" 
                                           placeholder="16.00" required>
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="tDescripcion_impuesto" class="form-label fw-bold">Descripción (Opcional)</label>
                                <textarea class="form-control" id="tDescripcion_impuesto" name="tDescripcion" rows="2"></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" 
                                           id="bActivo_impuesto" name="bActivo" value="1" checked>
                                    <label class="form-check-label" for="bActivo_impuesto">Activo</label>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary" id="btnCrearImpuesto">
                                    <i class="fas fa-save me-1"></i> Crear Impuesto
                                </button>
                            </div>
                        </form>
                    </div>
                </div>  
            </div>
        </div>
    </div>

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

    <!-- MODAL PARA CREAR IMPUESTO -->
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
                                   placeholder="Ej: IVA, ISR, IEPS" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="eTipo_impuesto_modal" class="form-label fw-bold">Tipo de Impuesto *</label>
                            <select class="form-control" id="eTipo_impuesto_modal" name="eTipo" required>
                                <option value="">Seleccionar tipo</option>
                                <option value="IVA">IVA</option>
                                <option value="IEPS">IEPS</option>
                                <option value="OTRO">OTRO</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="dPorcentaje_impuesto_modal" class="form-label fw-bold">Porcentaje *</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" max="100" class="form-control" 
                                       id="dPorcentaje_impuesto_modal" name="dPorcentaje" 
                                       placeholder="16.00" required>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="tDescripcion_impuesto_modal" class="form-label fw-bold">Descripción (Opcional)</label>
                            <textarea class="form-control" id="tDescripcion_impuesto_modal" name="tDescripcion" rows="2"></textarea>
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

.current-image-item .form-check {
    background: white;
    padding: 2px 5px;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
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
let gifFile = null;
let variacionCounter = 0;
let valorModal = null;
let maxTotalSize = 50 * 1024 * 1024; // 50MB en bytes
let limiteExcedido = false;

// Variable para la imagen de categoría
let categoriaImagenFile = null;
let categoriaModalImagenFile = null;

// Almacenar imágenes de variaciones por pestaña
let imagenesVariacion = {};

// Datos de variaciones existentes
let variacionesExistentes = @json($producto->variaciones->keyBy(function($item) {
    return $item->atributos->first()->id_atributo . '_' . $item->atributos->first()->id_atributo_valor;
}) ?? []);

// Valores seleccionados en el producto
let valoresSeleccionadosIniciales = @json($valoresSeleccionados);

// Array para almacenar imágenes adicionales existentes a eliminar
let imagenesAEliminar = [];

// Variables para modales
let modalCategoria = null;
let modalMarca = null;
let modalEtiqueta = null;
let modalAtributo = null;
let modalImpuesto = null;

// Inicializar modal cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    valorModal = new bootstrap.Modal(document.getElementById('crearValorModal'));
    
    // Inicializar modales
    modalCategoria = new bootstrap.Modal(document.getElementById('modalCategoria'));
    modalMarca = new bootstrap.Modal(document.getElementById('modalMarca'));
    modalEtiqueta = new bootstrap.Modal(document.getElementById('modalEtiqueta'));
    modalAtributo = new bootstrap.Modal(document.getElementById('modalAtributo'));
    modalImpuesto = new bootstrap.Modal(document.getElementById('modalImpuesto'));
    
    // Inicializar los formularios rápidos
    initQuickForms();
    
    // Inicializar atributos activos basados en valores seleccionados
    inicializarAtributosActivos();
});

// ============ FUNCIONES PARA ABRIR MODALES ============

function abrirModalCategoria() {
    document.getElementById('vNombre_categoria_modal').value = '';
    document.getElementById('vSlug_categoria_modal').value = '';
    document.getElementById('id_categoria_padre_modal').value = '';
    document.getElementById('tDescripcion_categoria_modal').value = '';
    document.getElementById('vImagen_categoria_modal').value = '';
    document.getElementById('categoriaModalImagePreview').style.display = 'none';
    document.getElementById('bActivo_categoria_modal').checked = true;
    categoriaModalImagenFile = null;
    modalCategoria.show();
}

function abrirModalMarca() {
    document.getElementById('vNombre_marca_modal').value = '';
    document.getElementById('tDescripcion_marca_modal').value = '';
    modalMarca.show();
}

function abrirModalEtiqueta() {
    document.getElementById('vNombre_eti_modal').value = '';
    document.getElementById('tDescripcion_eti_modal').value = '';
    modalEtiqueta.show();
}

function abrirModalAtributo() {
    document.getElementById('vNombre_attr_modal').value = '';
    document.getElementById('vSlug_attr_modal').value = '';
    document.getElementById('tDescripcion_attr_modal').value = '';
    modalAtributo.show();
}

function abrirModalImpuesto() {
    document.getElementById('vNombre_impuesto_modal').value = '';
    document.getElementById('eTipo_impuesto_modal').value = '';
    document.getElementById('dPorcentaje_impuesto_modal').value = '';
    document.getElementById('tDescripcion_impuesto_modal').value = '';
    document.getElementById('bActivo_impuesto_modal').checked = true;
    modalImpuesto.show();
}

// ============ FUNCIONES PARA GUARDAR DESDE MODALES ============

function generarSlugCategoria(nombre) {
    if (!nombre) {
        document.getElementById('vSlug_categoria_modal').value = '';
        return;
    }
    
    let slug = nombre.toLowerCase();
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
    
    document.getElementById('vSlug_categoria_modal').value = slug;
}

function generarSlugAtributo(nombre) {
    if (!nombre) {
        document.getElementById('vSlug_attr_modal').value = '';
        return;
    }
    
    let slug = nombre.toLowerCase();
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
    
    document.getElementById('vSlug_attr_modal').value = slug;
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
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        }
        
        reader.readAsDataURL(file);
    }
}

function cancelarImagenCategoriaModal() {
    const preview = document.getElementById('categoriaModalImagePreview');
    const fileInput = document.getElementById('vImagen_categoria_modal');
    
    preview.style.display = 'none';
    fileInput.value = '';
    categoriaModalImagenFile = null;
}

function guardarCategoria() {
    const vNombre = document.getElementById('vNombre_categoria_modal').value.trim();
    const vSlug = document.getElementById('vSlug_categoria_modal').value.trim();
    const idCategoriaPadre = document.getElementById('id_categoria_padre_modal').value;
    const tDescripcion = document.getElementById('tDescripcion_categoria_modal').value;
    const bActivo = document.getElementById('bActivo_categoria_modal').checked ? 1 : 0;
    
    if (!vNombre) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'El nombre de la categoría es obligatorio'
        });
        return;
    }
    
    if (!vSlug) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'El slug es obligatorio'
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
    formData.append('tDescripcion', tDescripcion);
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
            
            agregarCategoriaAlSelect(data.categoria);
            modalCategoria.hide();
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
    const vNombre = document.getElementById('vNombre_marca_modal').value.trim();
    const tDescripcion = document.getElementById('tDescripcion_marca_modal').value;
    
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
            tDescripcion: tDescripcion
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
            const option = document.createElement('option');
            option.value = data.marca.id_marca;
            option.textContent = data.marca.vNombre;
            select.appendChild(option);
            select.value = data.marca.id_marca;
            
            modalMarca.hide();
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
    const vNombre = document.getElementById('vNombre_eti_modal').value.trim();
    const tDescripcion = document.getElementById('tDescripcion_eti_modal').value;
    
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
            tDescripcion: tDescripcion
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
            
            agregarEtiquetaAlListado(data.etiqueta);
            modalEtiqueta.hide();
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

function guardarAtributo() {
    const vNombre = document.getElementById('vNombre_attr_modal').value.trim();
    const vSlug = document.getElementById('vSlug_attr_modal').value.trim();
    const tDescripcion = document.getElementById('tDescripcion_attr_modal').value;
    
    if (!vNombre) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'El nombre del atributo es obligatorio'
        });
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
            vNombre: vNombre,
            vSlug: vSlug || undefined,
            tDescripcion: tDescripcion
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
            
            agregarAtributoAlListado(data.atributo);
            modalAtributo.hide();
        } else {
            let errorMessage = data.message || 'Error al crear el atributo';
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

function guardarImpuesto() {
    const vNombre = document.getElementById('vNombre_impuesto_modal').value.trim();
    const eTipo = document.getElementById('eTipo_impuesto_modal').value;
    const dPorcentaje = document.getElementById('dPorcentaje_impuesto_modal').value;
    const tDescripcion = document.getElementById('tDescripcion_impuesto_modal').value;
    const bActivo = document.getElementById('bActivo_impuesto_modal').checked ? 1 : 0;
    
    if (!vNombre) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'El nombre del impuesto es obligatorio'
        });
        return;
    }
    
    if (!eTipo) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'El tipo de impuesto es obligatorio'
        });
        return;
    }
    
    if (!dPorcentaje || dPorcentaje <= 0) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'El porcentaje debe ser mayor a 0'
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
            tDescripcion: tDescripcion,
            bActivo: bActivo
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
            
            const select = document.getElementById('id_impuesto');
            const option = document.createElement('option');
            option.value = data.impuesto.id_impuesto;
            option.setAttribute('data-porcentaje', data.impuesto.dPorcentaje);
            option.setAttribute('data-tipo', data.impuesto.eTipo);
            option.textContent = data.impuesto.vNombre + ' (' + data.impuesto.eTipo + ' - ' + parseFloat(data.impuesto.dPorcentaje).toFixed(2) + '%)';
            select.appendChild(option);
            
            select.value = data.impuesto.id_impuesto;
            actualizarPrecioFinal();
            
            modalImpuesto.hide();
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
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error de conexión al servidor'
        });
    });
}

// ============ FUNCIONES PARA ELIMINAR IMÁGENES EXISTENTES ============

function eliminarImagenPrincipalExistente() {
    const container = document.getElementById('current_principal_container');
    if (container) {
        container.style.display = 'none';
        document.getElementById('eliminar_imagen_principal').value = '1';
        
        // Mostrar notificación
        Swal.fire({
            icon: 'success',
            title: 'Imagen marcada para eliminar',
            text: 'La imagen principal será eliminada al guardar los cambios',
            timer: 2000,
            showConfirmButton: false
        });
    }
}

function eliminarGifExistente() {
    const container = document.getElementById('current_gif_container');
    if (container) {
        container.style.display = 'none';
        document.getElementById('eliminar_gif').value = '1';
        
        Swal.fire({
            icon: 'success',
            title: 'GIF marcado para eliminar',
            text: 'El GIF será eliminado al guardar los cambios',
            timer: 2000,
            showConfirmButton: false
        });
    }
}

function eliminarImagenAdicionalExistente(nombreArchivo, index) {
    // Agregar al array de imágenes a eliminar
    imagenesAEliminar.push(nombreArchivo);
    
    // Ocultar el elemento visualmente
    const elemento = document.querySelector(`.current-image-item[data-filename="${nombreArchivo}"]`);
    if (elemento) {
        elemento.style.display = 'none';
    }
    
    // Crear o actualizar el input hidden
    const container = document.getElementById('imagenes-eliminar-container');
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'imagenes_a_eliminar[]';
    input.value = nombreArchivo;
    input.id = `eliminar_img_${Date.now()}`;
    container.appendChild(input);
    
    // Actualizar contador
    actualizarContadorImagenes();
    
    Swal.fire({
        icon: 'success',
        title: 'Imagen marcada para eliminar',
        text: 'La imagen será eliminada al guardar los cambios',
        timer: 1500,
        showConfirmButton: false
    });
}

// ============ FUNCIÓN PARA INICIALIZAR ATRIBUTOS ACTIVOS ============
function inicializarAtributosActivos() {
    // Limpiar atributos activos
    atributosActivos = {};
    
    // Recorrer todos los checkboxes de valores
    document.querySelectorAll('.valor-checkbox:checked').forEach(checkbox => {
        const atributoId = checkbox.dataset.atributoId;
        const atributoNombre = checkbox.dataset.atributoNombre;
        const valorId = checkbox.value;
        const valorNombre = checkbox.dataset.valorNombre;
        
        // Asegurar que el atributo esté activo
        const atributoActivo = document.getElementById(`atributo-activo-${atributoId}`);
        if (atributoActivo && !atributoActivo.checked) {
            atributoActivo.checked = true;
            // Disparar evento change manualmente
            const event = new Event('change', { bubbles: true });
            atributoActivo.dispatchEvent(event);
        }
        
        // Agregar a atributosActivos
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
    });
    
    // Actualizar resumen y pestañas
    actualizarResumenAtributos();
    actualizarPestanasValores();
}

// ============ FUNCIONES DE VALIDACIÓN DE TAMAÑO ============
function validarTamañoTotalAntesDeEnviar() {
    const totalSize = calcularTamañoTotal();
    const maxSize = 50 * 1024 * 1024; // 50MB
    
    // Lista de archivos para mostrar al usuario
    let archivosGrandes = [];
    
    // Verificar archivos individualmente
    if (imagenPrincipalFile && imagenPrincipalFile.size > 5 * 1024 * 1024) {
        archivosGrandes.push(`Imagen principal: ${(imagenPrincipalFile.size / (1024 * 1024)).toFixed(2)}MB (máx 5MB)`);
    }
    
    if (gifFile && gifFile.size > 10 * 1024 * 1024) {
        archivosGrandes.push(`GIF: ${(gifFile.size / (1024 * 1024)).toFixed(2)}MB (máx 10MB)`);
    }
    
    selectedImages.forEach(img => {
        if (img.file.size > 5 * 1024 * 1024) {
            archivosGrandes.push(`Imagen adicional "${img.file.name}": ${(img.file.size / (1024 * 1024)).toFixed(2)}MB (máx 5MB)`);
        }
    });
    
    // Verificar imágenes de variaciones
    Object.keys(imagenesVariacion).forEach(valorKey => {
        if (imagenesVariacion[valorKey] && imagenesVariacion[valorKey].imagenes) {
            imagenesVariacion[valorKey].imagenes.forEach(img => {
                if (img.file.size > 5 * 1024 * 1024) {
                    archivosGrandes.push(`Imagen adicional de variación "${img.name}": ${(img.file.size / (1024 * 1024)).toFixed(2)}MB (máx 5MB)`);
                }
            });
        }
    });
    
    if (archivosGrandes.length > 0) {
        Swal.fire({
            icon: 'error',
            title: 'Archivos demasiado grandes',
            html: `
                <div class="text-left">
                    <p class="mb-3">Los siguientes archivos exceden su límite individual:</p>
                    <ul class="text-left">
                        ${archivosGrandes.map(msg => `<li class="mb-2">⚠️ ${msg}</li>`).join('')}
                    </ul>
                    <hr>
                    <p class="text-muted small mb-0">Por favor, reduce el tamaño de estos archivos antes de continuar.</p>
                </div>
            `,
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#3085d6'
        });
        return false;
    }
    
    if (totalSize > maxSize) {
        // Calcular cuánto hay que reducir
        const exceso = totalSize - maxSize;
        
        Swal.fire({
            icon: 'error',
            title: 'Archivos demasiado grandes',
            html: `
                <div class="text-center">
                    <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                    <h5 class="mb-3">El tamaño total excede el límite del servidor</h5>
                    <p class="mb-2"><strong>Tamaño actual:</strong> ${(totalSize / (1024 * 1024)).toFixed(2)}MB</p>
                    <p class="mb-2"><strong>Límite permitido:</strong> 50MB</p>
                    <p class="mb-3"><strong>Debes reducir:</strong> ${(exceso / (1024 * 1024)).toFixed(2)}MB</p>
                    
                    <div class="bg-light p-3 rounded mt-3">
                        <p class="fw-bold mb-2">📊 Desglose de archivos:</p>
                        <ul class="text-left small">
                            ${imagenPrincipalFile ? `<li>📷 Imagen principal: ${(imagenPrincipalFile.size / (1024 * 1024)).toFixed(2)}MB</li>` : ''}
                            ${gifFile ? `<li>🎬 GIF: ${(gifFile.size / (1024 * 1024)).toFixed(2)}MB</li>` : ''}
                            ${selectedImages.map(img => `<li>🖼️ ${img.file.name.substring(0, 20)}...: ${(img.file.size / (1024 * 1024)).toFixed(2)}MB</li>`).join('')}
                        </ul>
                    </div>
                    
                    <hr>
                    <p class="text-muted small mt-3">💡 Recomendaciones:</p>
                    <ul class="text-left small">
                        <li>Comprime las imágenes antes de subirlas</li>
                        <li>Sube menos imágenes adicionales</li>
                    </ul>
                </div>
            `,
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#3085d6',
            width: '600px'
        });
        return false;
    }
    
    return true;
}

// ============ FUNCIÓN DE CÁLCULO DE IMPUESTO Y PRECIO FINAL ============

function actualizarPrecioFinal() {
    const precioVentaInput = document.getElementById('dPrecio_venta');
    const tieneDescuento = document.getElementById('bTiene_descuento')?.checked;
    const precioDescuentoInput = document.getElementById('dPrecio_descuento');
    const impuestoSelect = document.getElementById('id_impuesto');
    
    if (!precioVentaInput) return;
    
    // Determinar qué precio usar (original o con descuento)
    let precioBase = parseFloat(precioVentaInput.value) || 0;
    let precioOriginal = precioBase;
    
    if (tieneDescuento && precioDescuentoInput && precioDescuentoInput.value) {
        const precioDescuento = parseFloat(precioDescuentoInput.value) || 0;
        if (precioDescuento > 0 && precioDescuento < precioBase) {
            precioBase = precioDescuento;
        }
    }
    
    // Mostrar precio base (el que se usará para calcular impuestos)
    document.getElementById('precio-base-display').textContent = '$' + precioBase.toFixed(2);
    
    // Mostrar precio original si hay descuento
    const precioOriginalDisplay = document.getElementById('precio-original-display');
    if (tieneDescuento && precioBase < precioOriginal) {
        precioOriginalDisplay.style.display = 'block';
        precioOriginalDisplay.textContent = 'Precio original: $' + precioOriginal.toFixed(2);
        precioOriginalDisplay.className = 'text-muted';
    } else {
        precioOriginalDisplay.style.display = 'none';
    }
    
    // Obtener impuesto seleccionado
    let totalImpuestos = 0;
    let porcentaje = 0;
    
    if (impuestoSelect && impuestoSelect.value) {
        const selectedOption = impuestoSelect.options[impuestoSelect.selectedIndex];
        porcentaje = parseFloat(selectedOption.dataset.porcentaje) || 0;
        
        totalImpuestos = precioBase * (porcentaje / 100);
    }
    
    const precioFinal = precioBase + totalImpuestos;
    
    // Mostrar resultados
    document.getElementById('total-impuestos-display').textContent = '$' + totalImpuestos.toFixed(2);
    document.getElementById('precio-final-display').textContent = '$' + precioFinal.toFixed(2);
    
    if (porcentaje > 0) {
        document.getElementById('porcentaje-impuestos-display').textContent = porcentaje.toFixed(2) + '%';
    } else {
        document.getElementById('porcentaje-impuestos-display').textContent = '0%';
    }
}

function actualizarPrecioFinalVariacion(valorKey) {
    const precioInput = document.getElementById(`precio-${valorKey}`);
    const descuentoCheckbox = document.getElementById(`descuento-${valorKey}`);
    const precioDescuentoInput = document.getElementById(`precio_descuento-${valorKey}`);
    const impuestoSelect = document.getElementById(`impuesto-${valorKey}`);
    const precioFinalSpan = document.getElementById(`precio-final-${valorKey}`);
    const detalleImpuestoSpan = document.getElementById(`detalle-impuesto-${valorKey}`);
    
    if (!precioInput || !precioFinalSpan) return;
    
    // Determinar qué precio usar (original o con descuento)
    let precioBase = parseFloat(precioInput.value) || 0;
    
    if (descuentoCheckbox && descuentoCheckbox.checked && precioDescuentoInput && precioDescuentoInput.value) {
        const precioDescuento = parseFloat(precioDescuentoInput.value) || 0;
        if (precioDescuento > 0 && precioDescuento < precioBase) {
            precioBase = precioDescuento;
        }
    }
    
    // Obtener impuesto seleccionado
    let totalImpuestos = 0;
    let porcentaje = 0;
    let nombreImpuesto = '';
    
    if (impuestoSelect && impuestoSelect.value) {
        const selectedOption = impuestoSelect.options[impuestoSelect.selectedIndex];
        porcentaje = parseFloat(selectedOption.dataset.porcentaje) || 0;
        nombreImpuesto = selectedOption.text.split('(')[0].trim();
        
        totalImpuestos = precioBase * (porcentaje / 100);
    }
    
    const precioFinal = precioBase + totalImpuestos;
    
    // Mostrar resultados
    precioFinalSpan.textContent = '$' + precioFinal.toFixed(2);
    
    if (detalleImpuestoSpan) {
        if (porcentaje > 0) {
            detalleImpuestoSpan.textContent = `${nombreImpuesto}: ${porcentaje.toFixed(2)}% ($${totalImpuestos.toFixed(2)})`;
        } else {
            detalleImpuestoSpan.textContent = 'Sin impuesto';
        }
    }
}

// ============ FUNCIONES DE VALIDACIÓN DE FECHAS ============

function validarFechasDescuento() {
    const fechaInicio = document.getElementById('dFecha_inicio_descuento');
    const fechaFin = document.getElementById('dFecha_fin_descuento');
    const errorDiv = document.getElementById('error-fechas-descuento');
    
    if (!fechaInicio || !fechaFin) return true;
    
    if (fechaInicio.value && fechaFin.value) {
        const inicio = new Date(fechaInicio.value);
        const fin = new Date(fechaFin.value);
        
        if (fin < inicio) {
            fechaFin.classList.add('is-invalid');
            errorDiv.style.display = 'block';
            errorDiv.textContent = 'La fecha de fin no puede ser anterior a la fecha de inicio';
            return false;
        } else {
            fechaFin.classList.remove('is-invalid');
            errorDiv.style.display = 'none';
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
            errorDiv.style.display = 'block';
            errorDiv.textContent = 'La fecha de fin no puede ser anterior a la fecha de inicio';
            return false;
        } else {
            fechaFin.classList.remove('is-invalid');
            errorDiv.style.display = 'none';
            return true;
        }
    }
    return true;
}

// ============ FUNCIONES DE VALIDACIÓN DE PRECIOS ============

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
            errorDiv.style.display = 'block';
            errorDiv.textContent = 'El precio de descuento debe ser menor que el precio normal';
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
    const errorDiv = document.getElementById(`error-precio-descuento-${valorKey}`);
    
    if (!checkbox || !checkbox.checked) return true;
    
    if (precioNormal && input.value) {
        const precioNormalValor = parseFloat(precioNormal.value) || 0;
        const precioDescuentoValor = parseFloat(input.value) || 0;
        
        if (precioDescuentoValor >= precioNormalValor && precioDescuentoValor > 0) {
            input.classList.add('is-invalid');
            errorDiv.style.display = 'block';
            errorDiv.textContent = 'El precio de descuento debe ser menor que el precio normal';
            return false;
        } else {
            input.classList.remove('is-invalid');
            errorDiv.style.display = 'none';
            return true;
        }
    }
    return true;
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
    
    if (input.id === 'dPrecio_venta') {
        actualizarPrecioFinal();
    }
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
        
        setTimeout(() => {
            validarPrecioDescuentoProducto();
            actualizarPrecioFinal();
        }, 100);
    } else {
        descuentoFields.style.display = 'none';
        precioDescuento.required = false;
        fechaInicio.required = false;
        fechaFin.required = false;
        
        precioDescuento.classList.remove('is-invalid');
        document.getElementById('error-precio-descuento').style.display = 'none';
        fechaFin.classList.remove('is-invalid');
        document.getElementById('error-fechas-descuento').style.display = 'none';
        
        actualizarPrecioFinal();
    }
}

function toggleDescuentoVariacion(checkbox, valorKey) {
    const fields = document.querySelector(`.descuento-fields-${valorKey}`);
    const precioDescuento = document.getElementById(`precio_descuento-${valorKey}`);
    const fechaInicio = document.getElementById(`fecha-inicio-${valorKey}`);
    const fechaFin = document.getElementById(`fecha-fin-${valorKey}`);
    const errorDiv = document.getElementById(`error-precio-descuento-${valorKey}`);
    
    if (checkbox.checked) {
        fields.style.display = 'block';
        precioDescuento.required = true;
        fechaInicio.required = true;
        fechaFin.required = true;
        
        setTimeout(() => {
            validarPrecioDescuentoVariacion(precioDescuento);
            actualizarPrecioFinalVariacion(valorKey);
        }, 100);
    } else {
        fields.style.display = 'none';
        precioDescuento.required = false;
        fechaInicio.required = false;
        fechaFin.required = false;
        
        precioDescuento.classList.remove('is-invalid');
        if (errorDiv) errorDiv.style.display = 'none';
        fechaFin.classList.remove('is-invalid');
        document.getElementById(`error-fechas-descuento-${valorKey}`).style.display = 'none';
        
        actualizarPrecioFinalVariacion(valorKey);
    }
}

// ============ FUNCIONES DE IMÁGENES Y VIDEO DEL PRODUCTO PRINCIPAL ============

function calcularTamañoTotal() {
    let total = 0;
    
    if (imagenPrincipalFile) total += imagenPrincipalFile.size;
    if (gifFile) total += gifFile.size;
    
    selectedImages.forEach(img => {
        total += img.file.size;
    });
    
    // Agregar imágenes de variaciones
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
    
    if (totalSize < 1024) {
        totalSizeSpan.textContent = totalSize + ' B';
    } else if (totalSize < 1024 * 1024) {
        totalSizeSpan.textContent = (totalSize / 1024).toFixed(2) + ' KB';
    } else {
        totalSizeSpan.textContent = (totalSize / (1024 * 1024)).toFixed(2) + ' MB';
    }
    
    progressBar.style.width = Math.min(porcentaje, 100) + '%';
    
    if (porcentaje > 90) {
        progressBar.classList.remove('bg-success');
        progressBar.classList.add('bg-danger');
    } else if (porcentaje > 70) {
        progressBar.classList.remove('bg-success');
        progressBar.classList.add('bg-warning');
    } else {
        progressBar.classList.remove('bg-danger', 'bg-warning');
        progressBar.classList.add('bg-success');
    }
    
    if (totalSize > maxTotalSize) {
        limiteExcedido = true;
        limiteMsg.style.display = 'block';
        document.getElementById('btnSubmit').disabled = true;
    } else {
        limiteExcedido = false;
        limiteMsg.style.display = 'none';
        document.getElementById('btnSubmit').disabled = false;
    }
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
            previewImg.src = e.target.result;
            previewContainer.style.display = 'block';
            actualizarBarraProgresoTamaño();
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        previewContainer.style.display = 'none';
        imagenPrincipalFile = null;
        actualizarBarraProgresoTamaño();
    }
    
    actualizarContadorImagenes();
}

function cancelarImagenPrincipal() {
    const input = document.getElementById('imagen_principal');
    const previewContainer = document.getElementById('preview_principal_container');
    
    input.value = '';
    previewContainer.style.display = 'none';
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
            previewImg.src = e.target.result;
            previewContainer.style.display = 'block';
            actualizarBarraProgresoTamaño();
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        previewContainer.style.display = 'none';
        gifFile = null;
        actualizarBarraProgresoTamaño();
    }
    
    actualizarContadorImagenes();
}

function cancelarGif() {
    const input = document.getElementById('gif_producto');
    const previewContainer = document.getElementById('preview_gif_container');
    
    input.value = '';
    previewContainer.style.display = 'none';
    gifFile = null;
    actualizarBarraProgresoTamaño();
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

function limpiarFormularioEtiqueta() {
    document.getElementById('vNombre_eti').value = '';
    document.getElementById('tDescripcion_eti').value = '';
}

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
            
            agregarValorAlAtributo(data.valor);
            
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
    
    // Calcular tamaño actual
    const tamanioActual = calcularTamañoTotal();
    
    // Verificar si con estos archivos nuevos se excede el límite
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
            Swal.fire({
                icon: 'warning',
                title: 'Formato no válido',
                text: `El archivo "${file.name}" no es un formato válido. Formatos aceptados: JPG, JPEG, PNG, WEBP.`
            });
            continue;
        }
        
        if (file.size > 5 * 1024 * 1024) {
            Swal.fire({
                icon: 'warning',
                title: 'Archivo demasiado grande',
                text: `La imagen "${file.name}" excede el límite de 5MB.`
            });
            continue;
        }
        
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
            archivosAgregados++;
        }
    }
    
    if (archivosAgregados > 0) {
        document.getElementById('selected-images-count').textContent = selectedImages.length + ' archivos';
        renderSelectedImages();
        actualizarBarraProgresoTamaño();
    }
    
    event.target.value = '';
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
    
    document.getElementById('selected-images-count').textContent = selectedImages.length + ' archivos';
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
        small1.style.cssText = 'font-size: 11px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;';
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

function actualizarContadorImagenes() {
    let total = (imagenPrincipalFile ? 1 : 0) + (gifFile ? 1 : 0) + selectedImages.length;
    
    // Agregar imágenes de variaciones
    Object.keys(imagenesVariacion).forEach(valorKey => {
        if (imagenesVariacion[valorKey] && imagenesVariacion[valorKey].imagenes) {
            total += imagenesVariacion[valorKey].imagenes.length;
        }
    });
    
    // Contar imágenes existentes del producto que no serán eliminadas
    if (@json($producto->vImagen_principal ? true : false) && document.getElementById('current_principal_container')?.style.display !== 'none') {
        total += 1;
    }
    if (@json($producto->vGif ? true : false) && document.getElementById('current_gif_container')?.style.display !== 'none') {
        total += 1;
    }
    
    // Contar imágenes adicionales existentes que no están marcadas para eliminar
    const imagenesExistentesCount = @json(count($producto->vImagenes_adicionales ?? []));
    total += imagenesExistentesCount - imagenesAEliminar.length;
    
    document.getElementById('total-imagenes').textContent = total;
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
            // Forzar la actualización de la UI
            const event = new Event('change', { bubbles: true });
            atributoActivo.dispatchEvent(event);
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
                atributoId: atributo.id,
                atributoNombre: atributo.nombre
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
        const variacionExistente = variacionesExistentes[valorKey] || null;
        
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
        
        if (variacionExistente) {
            const badge = document.createElement('span');
            badge.className = 'badge bg-success ms-1';
            badge.textContent = '✓';
            tabButton.appendChild(badge);
        }
        
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
        
        // Valores pre-cargados si la variación existe
        const precioValue = variacionExistente ? variacionExistente.dPrecio : '';
        const stockValue = variacionExistente ? variacionExistente.iStock : '0';
        const claseEnvioValue = variacionExistente ? variacionExistente.vClase_envio : '';
        const idImpuestoValue = variacionExistente && variacionExistente.id_impuesto ? variacionExistente.id_impuesto : '';
        const descripcionValue = variacionExistente ? variacionExistente.tDescripcion : '';
        const pesoValue = variacionExistente && variacionExistente.dPeso ? variacionExistente.dPeso : '';
        const largoValue = variacionExistente && variacionExistente.dLargo_cm ? variacionExistente.dLargo_cm : '';
        const anchoValue = variacionExistente && variacionExistente.dAncho_cm ? variacionExistente.dAncho_cm : '';
        const altoValue = variacionExistente && variacionExistente.dAlto_cm ? variacionExistente.dAlto_cm : '';
        const tieneDescuento = variacionExistente && variacionExistente.bTiene_oferta ? true : false;
        const precioDescuentoValue = variacionExistente && variacionExistente.dPrecio_oferta ? variacionExistente.dPrecio_oferta : '';
        
        // CORRECCIÓN: Extraer fechas correctamente del formato YYYY-MM-DD HH:MM:SS
        let fechaInicioValue = '';
        if (variacionExistente && variacionExistente.dFecha_inicio_oferta) {
            const fechaStr = variacionExistente.dFecha_inicio_oferta;
            if (typeof fechaStr === 'string') {
                fechaInicioValue = fechaStr.substring(0, 10);
            }
        }
        
        let fechaFinValue = '';
        if (variacionExistente && variacionExistente.dFecha_fin_oferta) {
            const fechaStr = variacionExistente.dFecha_fin_oferta;
            if (typeof fechaStr === 'string') {
                fechaFinValue = fechaStr.substring(0, 10);
            }
        }
        
        const motivoDescuentoValue = variacionExistente && variacionExistente.vMotivo_oferta ? variacionExistente.vMotivo_oferta : '';
        const activoValue = variacionExistente ? (variacionExistente.bActivo ? true : false) : true;
        
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
                                SKU: ${variacionExistente ? variacionExistente.vSKU : skuSugerido}
                            </span>
                        </div>
                    </div>
                </div>

                ${variacionExistente ? `
                <input type="hidden" name="variaciones[${valorKey}][id_variacion]" value="${variacionExistente.id_variacion}">
                ` : ''}
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
                                        JPG, JPEG, PNG. Máx: 5MB. Dejar vacío para mantener la actual.
                                    </small>
                                    
                                    ${variacionExistente && variacionExistente.imagen_principal_url ? `
                                    <div id="current_principal_var_${valorKey}" class="mt-2">
                                        <div class="border rounded p-2 text-center bg-light position-relative">
                                            <div class="position-absolute top-0 end-0 m-2" style="z-index: 10;">
                                                <button type="button" class="btn btn-sm btn-outline-danger rounded-circle" 
                                                        onclick="eliminarImagenPrincipalVariacionExistente('${valorKey}')"
                                                        style="width: 30px; height: 30px;">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            <img src="${variacionExistente.imagen_principal_url}" 
                                                 class="img-thumbnail" 
                                                 style="max-width: 150px; max-height: 150px; object-fit: contain;">
                                            <div class="mt-2">
                                                <small class="text-muted d-block">Imagen actual</small>
                                                <input type="hidden" name="variaciones[${valorKey}][eliminar_imagen]" id="eliminar_img_${valorKey}" value="0">
                                            </div>
                                        </div>
                                    </div>
                                    ` : variacionExistente && variacionExistente.vImagen ? `
                                    <div id="current_principal_var_${valorKey}" class="mt-2">
                                        <div class="border rounded p-2 text-center bg-light position-relative">
                                            <div class="position-absolute top-0 end-0 m-2" style="z-index: 10;">
                                                <button type="button" class="btn btn-sm btn-outline-danger rounded-circle" 
                                                        onclick="eliminarImagenPrincipalVariacionExistente('${valorKey}')"
                                                        style="width: 30px; height: 30px;">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            <img src="{{ Storage::url('${variacionExistente.vImagen}') }}" 
                                                 class="img-thumbnail" 
                                                 style="max-width: 150px; max-height: 150px; object-fit: contain;">
                                            <div class="mt-2">
                                                <small class="text-muted d-block">Imagen actual</small>
                                                <input type="hidden" name="variaciones[${valorKey}][eliminar_imagen]" id="eliminar_img_${valorKey}" value="0">
                                            </div>
                                        </div>
                                    </div>
                                    ` : ''}
                                    
                                    <div id="preview_principal_${valorKey}" class="mt-2" style="display: none;">
                                        <div class="border rounded p-2 text-center bg-light position-relative">
                                            <div class="position-absolute top-0 end-0 m-2" style="z-index: 10;">
                                                <button type="button" class="btn btn-sm btn-outline-danger rounded-circle" 
                                                        onclick="cancelarImagenPrincipalVariacion('preview_principal_${valorKey}', 'img_principal_${valorKey}')"
                                                        style="width: 30px; height: 30px;">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            <img src="#" class="img-thumbnail" style="max-width: 150px; max-height: 150px; object-fit: contain;">
                                            <div class="mt-2">
                                                <small class="text-muted d-block">Nueva imagen</small>
                                            </div>
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
                                        GIF. Máx: 10MB. Dejar vacío para mantener el actual.
                                    </small>
                                    
                                    ${variacionExistente && variacionExistente.gif_url ? `
                                    <div id="current_gif_var_${valorKey}" class="mt-2">
                                        <div class="border rounded p-2 text-center bg-light position-relative">
                                            <div class="position-absolute top-0 end-0 m-2" style="z-index: 10;">
                                                <button type="button" class="btn btn-sm btn-outline-danger rounded-circle" 
                                                        onclick="eliminarGifVariacionExistente('${valorKey}')"
                                                        style="width: 30px; height: 30px;">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            <img src="${variacionExistente.gif_url}" 
                                                 class="img-thumbnail" 
                                                 style="max-width: 150px; max-height: 150px; object-fit: contain;">
                                            <div class="mt-2">
                                                <small class="text-muted d-block">GIF actual</small>
                                                <input type="hidden" name="variaciones[${valorKey}][eliminar_gif]" id="eliminar_gif_${valorKey}" value="0">
                                            </div>
                                        </div>
                                    </div>
                                    ` : variacionExistente && variacionExistente.vGif ? `
                                    <div id="current_gif_var_${valorKey}" class="mt-2">
                                        <div class="border rounded p-2 text-center bg-light position-relative">
                                            <div class="position-absolute top-0 end-0 m-2" style="z-index: 10;">
                                                <button type="button" class="btn btn-sm btn-outline-danger rounded-circle" 
                                                        onclick="eliminarGifVariacionExistente('${valorKey}')"
                                                        style="width: 30px; height: 30px;">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            <img src="{{ Storage::url('${variacionExistente.vGif}') }}" 
                                                 class="img-thumbnail" 
                                                 style="max-width: 150px; max-height: 150px; object-fit: contain;">
                                            <div class="mt-2">
                                                <small class="text-muted d-block">GIF actual</small>
                                                <input type="hidden" name="variaciones[${valorKey}][eliminar_gif]" id="eliminar_gif_${valorKey}" value="0">
                                            </div>
                                        </div>
                                    </div>
                                    ` : ''}
                                    
                                    <div id="preview_gif_${valorKey}" class="mt-2" style="display: none;">
                                        <div class="border rounded p-2 text-center bg-light position-relative">
                                            <div class="position-absolute top-0 end-0 m-2" style="z-index: 10;">
                                                <button type="button" class="btn btn-sm btn-outline-danger rounded-circle" 
                                                        onclick="cancelarGifVariacion('preview_gif_${valorKey}', 'gif_${valorKey}')"
                                                        style="width: 30px; height: 30px;">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            <img src="#" class="img-thumbnail" style="max-width: 150px; max-height: 150px; object-fit: contain;">
                                            <div class="mt-2">
                                                <small class="text-muted d-block">Nuevo GIF</small>
                                            </div>
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
                                        JPG, JPEG, PNG, WEBP. Máx: 5MB c/u. Las nuevas imágenes se agregarán a las existentes.
                                    </small>
                                    
                                    ${variacionExistente && variacionExistente.imagenes_adicionales && variacionExistente.imagenes_adicionales.length > 0 ? `
                                    <div class="mt-2">
                                        <label class="fw-bold">Imágenes adicionales actuales:</label>
                                        <div class="row" id="current_adicionales_var_${valorKey}">
                                            ${variacionExistente.imagenes_adicionales.map((img, idx) => {
                                                const nombreArchivo = img.split('/').pop();
                                                return `
                                                <div class="col-4 mb-2 current-image-item" data-filename="${nombreArchivo}" data-valor-key="${valorKey}">
                                                    <div class="border rounded p-1 text-center bg-light position-relative">
                                                        <div class="position-absolute top-0 end-0 m-1" style="z-index: 10;">
                                                            <button type="button" 
                                                                    class="btn btn-danger btn-sm rounded-circle"
                                                                    style="width: 25px; height: 25px;"
                                                                    onclick="eliminarImagenAdicionalVariacionExistente('${valorKey}', '${nombreArchivo}')">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </div>
                                                        <img src="${img}" class="img-fluid" style="height: 60px; object-fit: contain;">
                                                    </div>
                                                </div>
                                            `}).join('')}
                                        </div>
                                        <div id="imagenes_eliminar_${valorKey}" class="hidden-inputs"></div>
                                    </div>
                                    ` : ''}
                                    
                                    <div id="container_adicionales_${valorKey}" class="row mt-2"></div>
                                    <div class="mt-2">
                                        <span class="badge bg-info" id="count_adicionales_${valorKey}">0 nuevas seleccionadas</span>
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
                                       value="${variacionExistente ? variacionExistente.vSKU : skuSugerido}"
                                       maxlength="50"
                                       required
                                       oninput="validarSKU(this)"
                                       pattern="[A-Za-z0-9\-]+"
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
                                       ${activoValue ? 'checked' : ''}>
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
                                       value="${precioValue}"
                                       required
                                       oninput="validarPrecio(this); actualizarPrecioFinalVariacion('${valorKey}')"
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
                                       value="${stockValue}"
                                       required
                                       oninput="validarStock(this)"
                                       pattern="[0-9]{1,6}"
                                       min="0"
                                       max="999999"
                                       placeholder="0"
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
                                <option value="estandar" ${claseEnvioValue === 'estandar' ? 'selected' : ''}>Estándar</option>
                                <option value="express" ${claseEnvioValue === 'express' ? 'selected' : ''}>Express</option>
                                <option value="fragil" ${claseEnvioValue === 'fragil' ? 'selected' : ''}>Frágil</option>
                                <option value="grandes_dimensiones" ${claseEnvioValue === 'grandes_dimensiones' ? 'selected' : ''}>Grandes dimensiones</option>
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
                                                                data-tipo="{{ $impuesto->eTipo }}"
                                                                ${idImpuestoValue == {{ $impuesto->id_impuesto }} ? 'selected' : ''}>
                                                            {{ $impuesto->vNombre }} ({{ $impuesto->eTipo }} - {{ number_format($impuesto->dPorcentaje, 2) }}%)
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            <small class="form-text text-muted">
                                                Selecciona un impuesto específico para esta variación. Si no seleccionas, se usará el impuesto del producto.
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
                                   value="${pesoValue}"
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
                                   value="${largoValue}"
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
                                   value="${anchoValue}"
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
                                   value="${altoValue}"
                                   oninput="validarDimensionCm(this)"
                                   onblur="formatearDimensionCm(this)"
                                   onkeydown="permitirBorrado(event)"
                                   placeholder="0.00"
                                   title="Máximo: 999.99 cm (máximo 2 decimales)"
                                   autocomplete="off">
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
                                                       ${tieneDescuento ? 'checked' : ''}
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
                                <div class="descuento-fields-${valorKey}" style="${tieneDescuento ? 'display: block;' : 'display: none;'}">
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
                                                           value="${precioDescuentoValue}"
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
                                                       class="form-control"
                                                       id="fecha-inicio-${valorKey}"
                                                       value="${fechaInicioValue}"
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
                                                       class="form-control"
                                                       id="fecha-fin-${valorKey}"
                                                       value="${fechaFinValue}"
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
                                                       value="${motivoDescuentoValue}"
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
                                      autocomplete="off">${descripcionValue}</textarea>
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
        
        // Calcular precio final para esta variación si tiene datos
        if (precioValue) {
            setTimeout(() => {
                actualizarPrecioFinalVariacion(valorKey);
            }, 100);
        }
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

// ============ FUNCIONES PARA IMÁGENES DE VARIACIONES ============

function eliminarImagenPrincipalVariacionExistente(valorKey) {
    const container = document.getElementById(`current_principal_var_${valorKey}`);
    if (container) {
        container.style.display = 'none';
        document.getElementById(`eliminar_img_${valorKey}`).value = '1';
        
        Swal.fire({
            icon: 'success',
            title: 'Imagen marcada para eliminar',
            text: 'La imagen principal será eliminada al guardar los cambios',
            timer: 1500,
            showConfirmButton: false
        });
    }
}

function eliminarGifVariacionExistente(valorKey) {
    const container = document.getElementById(`current_gif_var_${valorKey}`);
    if (container) {
        container.style.display = 'none';
        document.getElementById(`eliminar_gif_${valorKey}`).value = '1';
        
        Swal.fire({
            icon: 'success',
            title: 'GIF marcado para eliminar',
            text: 'El GIF será eliminado al guardar los cambios',
            timer: 1500,
            showConfirmButton: false
        });
    }
}

function eliminarImagenAdicionalVariacionExistente(valorKey, nombreArchivo) {
    // Ocultar el elemento visualmente
    const elemento = document.querySelector(`.current-image-item[data-filename="${nombreArchivo}"][data-valor-key="${valorKey}"]`);
    if (elemento) {
        elemento.style.display = 'none';
    }
    
    // Crear input hidden para eliminar
    const container = document.getElementById(`imagenes_eliminar_${valorKey}`);
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = `variaciones[${valorKey}][imagenes_a_eliminar][]`;
    input.value = nombreArchivo;
    container.appendChild(input);
    
    Swal.fire({
        icon: 'success',
        title: 'Imagen marcada para eliminar',
        text: 'La imagen será eliminada al guardar los cambios',
        timer: 1500,
        showConfirmButton: false
    });
}

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
            previewContainer.style.display = 'block';
            const img = previewContainer.querySelector('img');
            img.src = e.target.result;
        }
        reader.readAsDataURL(file);
        
        // Actualizar barra de progreso
        actualizarBarraProgresoTamaño();
        actualizarContadorImagenes();
    } else {
        previewContainer.style.display = 'none';
    }
}

function cancelarImagenPrincipalVariacion(previewId, inputId) {
    const previewContainer = document.getElementById(previewId);
    const input = document.getElementById(inputId);
    
    previewContainer.style.display = 'none';
    input.value = '';
    
    // La imagen existente no se elimina
    actualizarBarraProgresoTamaño();
    actualizarContadorImagenes();
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
            previewContainer.style.display = 'block';
            const img = previewContainer.querySelector('img');
            img.src = e.target.result;
        }
        reader.readAsDataURL(file);
        
        // Actualizar barra de progreso
        actualizarBarraProgresoTamaño();
        actualizarContadorImagenes();
    } else {
        previewContainer.style.display = 'none';
    }
}

function cancelarGifVariacion(previewId, inputId) {
    const previewContainer = document.getElementById(previewId);
    const input = document.getElementById(inputId);
    
    previewContainer.style.display = 'none';
    input.value = '';
    
    // El GIF existente no se elimina
    actualizarBarraProgresoTamaño();
    actualizarContadorImagenes();
}

function handleImagenesAdicionalesVariacion(event, valorKey) {
    const input = event.target;
    const container = document.getElementById(`container_adicionales_${valorKey}`);
    const countSpan = document.getElementById(`count_adicionales_${valorKey}`);
    
    if (!container || !countSpan) return;
    
    const files = Array.from(input.files);
    const maxFiles = 7;
    const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    
    // Inicializar el array para esta variación si no existe
    if (!imagenesVariacion[valorKey]) {
        imagenesVariacion[valorKey] = {
            imagenes: []
        };
    }
    
    // Validar límite
    if (imagenesVariacion[valorKey].imagenes.length + files.length > maxFiles) {
        Swal.fire({
            icon: 'warning',
            title: 'Límite de imágenes',
            text: `Solo puedes seleccionar máximo ${maxFiles} imágenes adicionales. Ya tienes ${imagenesVariacion[valorKey].imagenes.length} seleccionadas.`
        });
        input.value = '';
        return;
    }
    
    // Calcular tamaño actual
    const tamanioActual = calcularTamañoTotal();
    
    // Verificar si con estos archivos nuevos se excede el límite
    let nuevoTamanio = tamanioActual;
    files.forEach(file => {
        nuevoTamanio += file.size;
    });
    
    if (nuevoTamanio > maxTotalSize) {
        Swal.fire({
            icon: 'warning',
            title: 'Límite de tamaño excedido',
            html: `
                <div class="text-center">
                    <p>Si agregas estas imágenes, excederás el límite de 50MB.</p>
                    <p class="mb-0"><strong>Tamaño actual:</strong> ${(tamanioActual / (1024 * 1024)).toFixed(2)}MB</p>
                    <p><strong>Tamaño con nuevas imágenes:</strong> ${(nuevoTamanio / (1024 * 1024)).toFixed(2)}MB</p>
                </div>
            `,
            confirmButtonText: 'Entendido'
        });
        input.value = '';
        return;
    }
    
    // Procesar cada archivo
    files.forEach(file => {
        if (!validTypes.includes(file.type)) {
            Swal.fire({
                icon: 'warning',
                title: 'Formato no válido',
                text: `El archivo "${file.name}" no es un formato válido. Formatos aceptados: JPG, JPEG, PNG, WEBP.`
            });
            return;
        }
        
        if (file.size > 5 * 1024 * 1024) {
            Swal.fire({
                icon: 'warning',
                title: 'Archivo demasiado grande',
                text: `La imagen "${file.name}" excede el límite de 5MB.`
            });
            return;
        }
        
        // Crear objeto de imagen
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
    
    // Renderizar las imágenes
    renderImagenesAdicionalesVariacion(valorKey);
    
    // Actualizar contador
    countSpan.textContent = imagenesVariacion[valorKey].imagenes.length + ' nuevas seleccionadas';
    
    // Actualizar barra de progreso y contador total
    actualizarBarraProgresoTamaño();
    actualizarContadorImagenes();
    
    // Limpiar el input para permitir seleccionar los mismos archivos nuevamente
    input.value = '';
}

function renderImagenesAdicionalesVariacion(valorKey) {
    const container = document.getElementById(`container_adicionales_${valorKey}`);
    if (!container) return;
    
    container.innerHTML = '';
    
    if (!imagenesVariacion[valorKey] || imagenesVariacion[valorKey].imagenes.length === 0) {
        container.innerHTML = '<div class="col-12 text-muted small">No hay imágenes nuevas seleccionadas</div>';
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
            countSpan.textContent = imagenesVariacion[valorKey].imagenes.length + ' nuevas seleccionadas';
        }
        
        // Actualizar barra de progreso y contador total
        actualizarBarraProgresoTamaño();
        actualizarContadorImagenes();
    }
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

// ============ FUNCIONES PARA AGREGAR ELEMENTOS DINÁMICAMENTE ============

function agregarCategoriaAlSelect(categoria) {
    const select = document.getElementById('id_categoria');
    
    const option = document.createElement('option');
    option.value = categoria.id_categoria;
    
    let icono = categoria.id_categoria_padre ? '↳ ' : '🏠 ';
    
    option.innerHTML = icono + categoria.vNombre;
    
    select.appendChild(option);
    select.value = categoria.id_categoria;
}

function agregarEtiquetaAlListado(etiqueta) {
    const container = document.getElementById('etiquetas-container').querySelector('.row');
    
    const noEtiquetasMsg = document.getElementById('no-etiquetas-msg');
    if (noEtiquetasMsg) {
        noEtiquetasMsg.remove();
    }
    
    const col = document.createElement('div');
    col.className = 'col-md-6 col-6 mb-2 etiqueta-item';
    col.setAttribute('data-etiqueta-id', etiqueta.id_etiqueta);
    
    const divCheck = document.createElement('div');
    divCheck.className = 'form-check';
    
    const input = document.createElement('input');
    input.type = 'checkbox';
    input.name = 'etiquetas[]';
    input.value = etiqueta.id_etiqueta;
    input.className = 'form-check-input';
    input.id = 'etiqueta_' + etiqueta.id_etiqueta;
    input.checked = true;
    
    const label = document.createElement('label');
    label.className = 'form-check-label';
    label.htmlFor = 'etiqueta_' + etiqueta.id_etiqueta;
    
    const span = document.createElement('span');
    span.className = 'badge bg-secondary';
    span.textContent = etiqueta.vNombre;
    
    label.appendChild(span);
    divCheck.appendChild(input);
    divCheck.appendChild(label);
    col.appendChild(divCheck);
    container.appendChild(col);
}

function agregarAtributoAlListado(atributo) {
    const container = document.getElementById('atributos-container');
    const noAtributosMsg = document.getElementById('no-atributos-msg');
    
    if (noAtributosMsg) {
        noAtributosMsg.remove();
    }
    
    const col = document.createElement('div');
    col.className = 'col-md-6 mb-4 atributo-item';
    col.setAttribute('data-atributo-id', atributo.id_atributo);
    
    const card = document.createElement('div');
    card.className = 'card border h-100';
    
    const cardHeader = document.createElement('div');
    cardHeader.className = 'card-header bg-light d-flex justify-content-between align-items-center';
    cardHeader.innerHTML = `
        <div class="form-check">
            <input type="checkbox" class="form-check-input atributo-activo-checkbox" 
                   id="atributo-activo-${atributo.id_atributo}"
                   data-atributo-id="${atributo.id_atributo}"
                   data-atributo-nombre="${atributo.vNombre}">
            <label class="form-check-label fw-bold" for="atributo-activo-${atributo.id_atributo}" style="color: #495057;">
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
    `;
    
    const cardBody = document.createElement('div');
    cardBody.className = 'card-body atributo-valores-container';
    cardBody.id = `valores-container-${atributo.id_atributo}`;
    cardBody.style.display = 'none';
    cardBody.style.backgroundColor = 'white';
    
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-warning mb-0';
    alertDiv.innerHTML = `
        <i class="fas fa-exclamation-triangle me-2"></i>
        Este atributo no tiene valores. 
        <button type="button" class="btn btn-link p-0 ms-1" onclick="mostrarFormularioValor(${atributo.id_atributo}, '${atributo.vNombre}')">
            Crear primer valor
        </button>
    `;
    
    cardBody.appendChild(alertDiv);
    card.appendChild(cardHeader);
    card.appendChild(cardBody);
    col.appendChild(card);
    container.appendChild(col);
    
    const checkbox = document.getElementById(`atributo-activo-${atributo.id_atributo}`);
    if (checkbox) {
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
    }
}

function agregarValorAlAtributo(valor) {
    const container = document.getElementById(`valores-container-${valor.id_atributo}`);
    if (!container) return;
    
    const alerta = container.querySelector('.alert-warning');
    if (alerta) {
        alerta.remove();
    }
    
    let selectAllDiv = container.querySelector('.mb-3');
    if (!selectAllDiv) {
        selectAllDiv = document.createElement('div');
        selectAllDiv.className = 'mb-3';
        selectAllDiv.innerHTML = `
            <div class="form-check">
                <input type="checkbox" class="form-check-input seleccionar-todos-checkbox" id="seleccionar-todos-${valor.id_atributo}" data-atributo-id="${valor.id_atributo}">
                <label class="form-check-label" for="seleccionar-todos-${valor.id_atributo}">
                    <strong>Seleccionar todos</strong>
                </label>
            </div>
        `;
        container.appendChild(selectAllDiv);
        
        const hr = document.createElement('hr');
        hr.className = 'my-2';
        container.appendChild(hr);
        
        const selectAllCheckbox = document.getElementById(`seleccionar-todos-${valor.id_atributo}`);
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
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
        }
    }
    
    let row = container.querySelector('.row:not(.mb-3)');
    if (!row) {
        row = document.createElement('div');
        row.className = 'row';
        container.appendChild(row);
    }
    
    const col = document.createElement('div');
    col.className = 'col-md-6 mb-2';
    
    const divCheck = document.createElement('div');
    divCheck.className = 'form-check';
    
    const input = document.createElement('input');
    input.type = 'checkbox';
    input.name = `atributos[${valor.id_atributo}][]`;
    input.value = valor.id_atributo_valor;
    input.className = 'form-check-input valor-checkbox';
    input.id = `valor-${valor.id_atributo_valor}`;
    input.setAttribute('data-atributo-id', valor.id_atributo);
    input.setAttribute('data-atributo-nombre', valor.atributo_nombre || '');
    input.setAttribute('data-valor-nombre', valor.vValor);
    
    const label = document.createElement('label');
    label.className = 'form-check-label';
    label.htmlFor = `valor-${valor.id_atributo_valor}`;
    label.textContent = valor.vValor;
    
    divCheck.appendChild(input);
    divCheck.appendChild(label);
    col.appendChild(divCheck);
    row.appendChild(col);
    
    input.addEventListener('change', function() {
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
    
    const badge = container.closest('.card').querySelector('.badge.bg-secondary');
    if (badge) {
        const valorCount = container.querySelectorAll('.valor-checkbox').length;
        badge.textContent = valorCount + ' valores';
    }
}

// ============ INICIALIZAR FORMULARIOS RÁPIDOS ============

function initQuickForms() {
    // Formulario de Categoría
    const categoriaForm = document.getElementById('categoriaQuickForm');
    if (categoriaForm) {
        categoriaForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            if (categoriaImagenFile) {
                formData.delete('vImagen');
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
                    
                    agregarCategoriaAlSelect(data.categoria);
                    limpiarFormularioCategoria();
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
        });
    }
    
    // Formulario de Marca
    const marcaForm = document.getElementById('marcaQuickForm');
    if (marcaForm) {
        marcaForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            Swal.fire({
                title: 'Creando marca...',
                text: 'Por favor espera',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });
            
            fetch('{{ route("marcas.quick-create") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
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
                    const option = document.createElement('option');
                    option.value = data.marca.id_marca;
                    option.textContent = data.marca.vNombre;
                    select.appendChild(option);
                    select.value = data.marca.id_marca;
                    
                    limpiarFormularioMarca();
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
        });
    }
    
    // Formulario de Etiqueta
    const etiquetaForm = document.getElementById('etiquetaQuickForm');
    if (etiquetaForm) {
        etiquetaForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            Swal.fire({
                title: 'Creando etiqueta...',
                text: 'Por favor espera',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });
            
            fetch('{{ route("etiquetas.quick-create") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
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
                    
                    agregarEtiquetaAlListado(data.etiqueta);
                    limpiarFormularioEtiqueta();
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
        });
    }
    
    // Formulario de Atributo
    const atributoForm = document.getElementById('atributoQuickForm');
    if (atributoForm) {
        atributoForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            Swal.fire({
                title: 'Creando atributo...',
                text: 'Por favor espera',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });
            
            fetch('{{ route("atributos.quick-create") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
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
                    
                    agregarAtributoAlListado(data.atributo);
                    
                    document.getElementById('vNombre_attr').value = '';
                    document.getElementById('vSlug_attr').value = '';
                    document.getElementById('tDescripcion_attr').value = '';
                } else {
                    let errorMessage = data.message || 'Error al crear el atributo';
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
        });
    }
    
    // Formulario de Impuesto
    const impuestoForm = document.getElementById('impuestoQuickForm');
    if (impuestoForm) {
        impuestoForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            Swal.fire({
                title: 'Creando impuesto...',
                text: 'Por favor espera',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });
            
            fetch('{{ route("impuestos.quick-create") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
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
                    
                    const select = document.getElementById('id_impuesto');
                    const option = document.createElement('option');
                    option.value = data.impuesto.id_impuesto;
                    option.setAttribute('data-porcentaje', data.impuesto.dPorcentaje);
                    option.setAttribute('data-tipo', data.impuesto.eTipo);
                    option.textContent = data.impuesto.vNombre + ' (' + data.impuesto.eTipo + ' - ' + parseFloat(data.impuesto.dPorcentaje).toFixed(2) + '%)';
                    select.appendChild(option);
                    
                    select.value = data.impuesto.id_impuesto;
                    actualizarPrecioFinal();
                    limpiarFormularioImpuesto();
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
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error de conexión al servidor'
                });
            });
        });
    }
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
            actualizarPrecioFinal();
        });
    }
    
    document.getElementById('dPrecio_descuento')?.addEventListener('input', function() {
        actualizarPrecioFinal();
    });
    
    document.getElementById('id_impuesto')?.addEventListener('change', actualizarPrecioFinal);
    
    if (document.getElementById('bTiene_descuento')) {
        if (document.getElementById('bTiene_descuento').checked) {
            toggleDescuentoFields();
        }
    }
    
    // Inicializar atributos activos
    inicializarAtributosActivos();
    
    document.getElementById('selected-images-count').textContent = '0 archivos';
    
    renderSelectedImages();
    actualizarResumenAtributos();
    actualizarPestanasValores();
    actualizarContadorImagenes();
    actualizarPrecioFinal();
    
    // Delegación de eventos para los selects de impuestos de variaciones
    document.addEventListener('change', function(e) {
        if (e.target.id && e.target.id.startsWith('impuesto-')) {
            const valorKey = e.target.id.replace('impuesto-', '');
            actualizarPrecioFinalVariacion(valorKey);
        }
    });
    
    document.addEventListener('input', function(e) {
        if (e.target.id && e.target.id.startsWith('precio-') && !e.target.id.startsWith('precio_descuento-')) {
            const valorKey = e.target.id.replace('precio-', '');
            actualizarPrecioFinalVariacion(valorKey);
        }
    });
    
    const productoForm = document.getElementById('productoForm');
    if (productoForm) {
        productoForm.addEventListener('submit', function(e) {
            const totalSize = calcularTamañoTotal();
            if (totalSize > maxTotalSize) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Límite de tamaño excedido',
                    text: `El tamaño total de los archivos (${(totalSize / (1024 * 1024)).toFixed(2)}MB) excede el límite permitido de 50MB.`
                });
                return false;
            }
        });
    }
});

// ============ FUNCIÓN PRINCIPAL DE ENVÍO DEL FORMULARIO ============

document.getElementById('productoForm').addEventListener('submit', function(e) {
    // Primero validar con JavaScript
    if (!validarTamañoTotalAntesDeEnviar()) {
        e.preventDefault();
        return false;
    }
    
    const btnSubmit = document.getElementById('btnSubmit');
    
    // Validación de fechas de descuento en producto principal
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
    
    // Validación de fechas de descuento en variaciones
    let erroresFechasVariaciones = [];
    document.querySelectorAll('[id^="fecha-fin-"]').forEach(input => {
        const valorKey = input.id.replace('fecha-fin-', '');
        const fechaInicio = document.getElementById(`fecha-inicio-${valorKey}`);
        const fechaFin = input;
        
        if (fechaInicio && fechaInicio.value && fechaFin.value) {
            const inicio = new Date(fechaInicio.value);
            const fin = new Date(fechaFin.value);
            
            if (fin < inicio) {
                erroresFechasVariaciones.push('En una variación, la fecha de fin no puede ser anterior a la fecha de inicio');
                fechaFin.classList.add('is-invalid');
                document.getElementById(`error-fechas-descuento-${valorKey}`).style.display = 'block';
            }
        }
    });
    
    if (erroresFechasVariaciones.length > 0) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Errores en fechas de descuento',
            html: erroresFechasVariaciones.join('<br>')
        });
        return false;
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
    
    // Prevenir el envío original
    e.preventDefault();
    
    // Crear un nuevo FormData basado en el formulario actual
    const form = this;
    const formData = new FormData(form);
    
    // Eliminar las entradas de variaciones que ya existen para reemplazarlas
    const keysToRemove = [];
    for (let pair of formData.entries()) {
        if (pair[0].startsWith('variaciones[')) {
            keysToRemove.push(pair[0]);
        }
    }
    keysToRemove.forEach(key => formData.delete(key));
    
    // Reconstruir las variaciones con todas las imágenes
    const variacionesKeys = new Set();
    
    // Primero, recolectar todas las claves de variaciones
    document.querySelectorAll('input[name*="[vSKU]"]').forEach(input => {
        const match = input.name.match(/variaciones\[([^\]]+)\]/);
        if (match && match[1]) {
            variacionesKeys.add(match[1]);
        }
    });
    
    // Para cada variación, agregar todos los campos
    variacionesKeys.forEach(valorKey => {
        // Campos básicos
        const idVariacion = document.querySelector(`input[name="variaciones[${valorKey}][id_variacion]"]`);
        const idAtributo = document.querySelector(`input[name="variaciones[${valorKey}][id_atributo]"]`);
        const idAtributoValor = document.querySelector(`input[name="variaciones[${valorKey}][id_atributo_valor]"]`);
        const vNombreVariacion = document.querySelector(`input[name="variaciones[${valorKey}][vNombre_variacion]"]`);
        const vSKU = document.querySelector(`input[name="variaciones[${valorKey}][vSKU]"]`);
        const bActivo = document.querySelector(`input[name="variaciones[${valorKey}][bActivo]"]`);
        const dPrecio = document.querySelector(`input[name="variaciones[${valorKey}][dPrecio]"]`);
        const iStock = document.querySelector(`input[name="variaciones[${valorKey}][iStock]"]`);
        const vClaseEnvio = document.querySelector(`select[name="variaciones[${valorKey}][vClase_envio]"]`);
        const idImpuesto = document.querySelector(`select[name="variaciones[${valorKey}][id_impuesto]"]`);
        const dPeso = document.querySelector(`input[name="variaciones[${valorKey}][dPeso]"]`);
        const dLargoCm = document.querySelector(`input[name="variaciones[${valorKey}][dLargo_cm]"]`);
        const dAnchoCm = document.querySelector(`input[name="variaciones[${valorKey}][dAncho_cm]"]`);
        const dAltoCm = document.querySelector(`input[name="variaciones[${valorKey}][dAlto_cm]"]`);
        const bTieneDescuento = document.querySelector(`input[name="variaciones[${valorKey}][bTiene_descuento]"]`);
        const dPrecioDescuento = document.querySelector(`input[name="variaciones[${valorKey}][dPrecio_descuento]"]`);
        const dFechaInicioDescuento = document.querySelector(`input[name="variaciones[${valorKey}][dFecha_inicio_descuento]"]`);
        const dFechaFinDescuento = document.querySelector(`input[name="variaciones[${valorKey}][dFecha_fin_descuento]"]`);
        const vMotivoDescuento = document.querySelector(`input[name="variaciones[${valorKey}][vMotivo_descuento]"]`);
        const tDescripcion = document.querySelector(`textarea[name="variaciones[${valorKey}][tDescripcion]"]`);
        
        // Agregar campos básicos si existen
        if (idVariacion) formData.append(`variaciones[${valorKey}][id_variacion]`, idVariacion.value);
        if (idAtributo) formData.append(`variaciones[${valorKey}][id_atributo]`, idAtributo.value);
        if (idAtributoValor) formData.append(`variaciones[${valorKey}][id_atributo_valor]`, idAtributoValor.value);
        if (vNombreVariacion) formData.append(`variaciones[${valorKey}][vNombre_variacion]`, vNombreVariacion.value);
        if (vSKU) formData.append(`variaciones[${valorKey}][vSKU]`, vSKU.value);
        if (bActivo && bActivo.checked) formData.append(`variaciones[${valorKey}][bActivo]`, '1');
        if (dPrecio) formData.append(`variaciones[${valorKey}][dPrecio]`, dPrecio.value);
        if (iStock) formData.append(`variaciones[${valorKey}][iStock]`, iStock.value);
        if (vClaseEnvio && vClaseEnvio.value) formData.append(`variaciones[${valorKey}][vClase_envio]`, vClaseEnvio.value);
        if (idImpuesto && idImpuesto.value) formData.append(`variaciones[${valorKey}][id_impuesto]`, idImpuesto.value);
        if (dPeso && dPeso.value) formData.append(`variaciones[${valorKey}][dPeso]`, dPeso.value);
        if (dLargoCm && dLargoCm.value) formData.append(`variaciones[${valorKey}][dLargo_cm]`, dLargoCm.value);
        if (dAnchoCm && dAnchoCm.value) formData.append(`variaciones[${valorKey}][dAncho_cm]`, dAnchoCm.value);
        if (dAltoCm && dAltoCm.value) formData.append(`variaciones[${valorKey}][dAlto_cm]`, dAltoCm.value);
        if (bTieneDescuento && bTieneDescuento.checked) formData.append(`variaciones[${valorKey}][bTiene_descuento]`, '1');
        if (dPrecioDescuento && dPrecioDescuento.value) formData.append(`variaciones[${valorKey}][dPrecio_descuento]`, dPrecioDescuento.value);
        if (dFechaInicioDescuento && dFechaInicioDescuento.value) formData.append(`variaciones[${valorKey}][dFecha_inicio_descuento]`, dFechaInicioDescuento.value);
        if (dFechaFinDescuento && dFechaFinDescuento.value) formData.append(`variaciones[${valorKey}][dFecha_fin_descuento]`, dFechaFinDescuento.value);
        if (vMotivoDescuento && vMotivoDescuento.value) formData.append(`variaciones[${valorKey}][vMotivo_descuento]`, vMotivoDescuento.value);
        if (tDescripcion && tDescripcion.value) formData.append(`variaciones[${valorKey}][tDescripcion]`, tDescripcion.value);
        
        // Eliminar imagen principal
        const eliminarImagen = document.querySelector(`input[name="variaciones[${valorKey}][eliminar_imagen]"]`);
        if (eliminarImagen && eliminarImagen.value === '1') {
            formData.append(`variaciones[${valorKey}][eliminar_imagen]`, '1');
        }
        
        // Eliminar GIF
        const eliminarGif = document.querySelector(`input[name="variaciones[${valorKey}][eliminar_gif]"]`);
        if (eliminarGif && eliminarGif.value === '1') {
            formData.append(`variaciones[${valorKey}][eliminar_gif]`, '1');
        }
        
        // Imágenes a eliminar
        const imagenesEliminar = document.querySelectorAll(`#imagenes_eliminar_${valorKey} input`);
        imagenesEliminar.forEach(input => {
            formData.append(`variaciones[${valorKey}][imagenes_a_eliminar][]`, input.value);
        });
        
        // Imagen principal de la variación
        const imagenPrincipalInput = document.getElementById(`img_principal_${valorKey}`);
        if (imagenPrincipalInput && imagenPrincipalInput.files && imagenPrincipalInput.files[0]) {
            formData.append(`variaciones[${valorKey}][imagen_principal]`, imagenPrincipalInput.files[0]);
        }
        
        // GIF de la variación
        const gifInput = document.getElementById(`gif_${valorKey}`);
        if (gifInput && gifInput.files && gifInput.files[0]) {
            formData.append(`variaciones[${valorKey}][gif]`, gifInput.files[0]);
        }
        
        // Imágenes adicionales de la variación
        if (imagenesVariacion[valorKey] && imagenesVariacion[valorKey].imagenes) {
            imagenesVariacion[valorKey].imagenes.forEach((img, index) => {
                formData.append(`variaciones[${valorKey}][imagenes_adicionales][]`, img.file);
            });
        }
    });
    
    if (btnSubmit) {
        btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Guardando...';
        btnSubmit.disabled = true;
    }
    
    // Enviar con fetch
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (response.redirected) {
            // Redirección exitosa
            window.location.href = response.url;
        } else if (response.ok) {
            return response.json().then(data => {
                if (data.success && data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    // Éxito pero sin redirección
                    window.location.href = '{{ route("productos.index") }}';
                }
            });
        } else {
            // Error
            return response.json().then(data => {
                throw new Error(data.message || 'Error al actualizar el producto');
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message || 'Error al actualizar el producto'
        });
        if (btnSubmit) {
            btnSubmit.innerHTML = '<i class="fas fa-save me-2"></i> Actualizar Producto';
            btnSubmit.disabled = false;
        }
    });
    
    return false; // Cancelar el envío original
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