@extends('layouts.app')

@section('title', 'Editar Variación - ' . $producto->vNombre)
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1><i class="fas fa-edit me-2"></i>Editar Variación</h1>
            <p class="text-muted">Producto: {{ $producto->vNombre }}</p>
            <p class="text-muted small">
                <i class="fas fa-info-circle me-1"></i>
                Modifica los datos de la variación específica de este producto.
            </p>
        </div>
        <div>
            <a href="{{ route('variaciones.show', $producto->id_producto) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Volver a Variaciones
            </a>
        </div>
    </div>

    <form action="{{ route('variaciones.update', ['producto_id' => $producto->id_producto, 'variacion_id' => $variacion->id_variacion]) }}" 
          method="POST" enctype="multipart/form-data" id="variacionForm">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-md-8">
                <!-- INFORMACIÓN BÁSICA DE LA VARIACIÓN -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información Básica de la Variación</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="vSKU" class="form-label fw-bold">
                                        SKU <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="vSKU" id="vSKU" 
                                           class="form-control @error('vSKU') is-invalid @enderror"
                                           value="{{ old('vSKU', $variacion->vSKU) }}" 
                                           maxlength="15" 
                                           required
                                           oninput="validarSKU(this)"
                                           pattern="[A-Za-z0-9]+"
                                           title="Solo letras y números (máximo 15 caracteres)"
                                           autocomplete="off">
                                    @error('vSKU')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Ej: AGAVE001, MEZCAL2024 (15 caracteres máximo, solo letras y números)</small>
                                </div>
                            </div>
                            
                            <!-- ELIMINADO: Campo Nombre de la variación -->
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="dPrecio" class="form-label fw-bold">
                                        Precio de venta <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="text" 
                                               name="dPrecio" 
                                               id="dPrecio" 
                                               class="form-control @error('dPrecio') is-invalid @enderror"
                                               value="{{ old('dPrecio', $variacion->dPrecio) }}" 
                                               required 
                                               oninput="validarPrecio(this); actualizarPrecioFinal();"
                                               placeholder="0.00"
                                               title="Máximo: 9,999,999.99 (7 dígitos enteros, 2 decimales)"
                                               autocomplete="off">
                                        @error('dPrecio')
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
                                        Stock disponible <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="iStock" id="iStock" 
                                           class="form-control @error('iStock') is-invalid @enderror"
                                           value="{{ old('iStock', $variacion->iStock) }}" 
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
                            
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <div class="form-check mt-4">
                                        <input type="checkbox" name="bActivo" id="bActivo" 
                                               class="form-check-input" value="1"
                                               {{ old('bActivo', $variacion->bActivo) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="bActivo">
                                            Variación activa
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Si está desactivada, no estará disponible para venta
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- CAMPOS DE OFERTA -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-percentage me-1"></i>Oferta Especial
                                    </label>
                                    <div class="form-check form-switch">
                                        <input type="checkbox" name="bTiene_oferta" id="bTiene_oferta" 
                                               class="form-check-input" value="1"
                                               {{ old('bTiene_oferta', $variacion->bTiene_oferta) ? 'checked' : '' }}
                                               onchange="toggleOfertaFields()">
                                        <label class="form-check-label" for="bTiene_oferta">
                                            Activar Oferta para esta variación
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Permite establecer un precio de oferta por tiempo limitado
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- CAMPOS DE OFERTA (OCULTOS INICIALMENTE) -->
                        <div id="ofertaFields" style="display: {{ old('bTiene_oferta', $variacion->bTiene_oferta) ? 'block' : 'none' }};">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="dPrecio_oferta" class="form-label fw-bold">
                                            Precio de Oferta <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="text" 
                                                   name="dPrecio_oferta" 
                                                   id="dPrecio_oferta" 
                                                   class="form-control @error('dPrecio_oferta') is-invalid @enderror"
                                                   value="{{ old('dPrecio_oferta', $variacion->dPrecio_oferta) }}" 
                                                   oninput="validarPrecio(this); validarPrecioOfertaInstantaneo(this); actualizarPrecioFinal();"
                                                   onblur="validarPrecioOferta()"
                                                   placeholder="0.00"
                                                   autocomplete="off">
                                        </div>
                                        <div id="error-precio-oferta" class="invalid-feedback" style="display: none;"></div>
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
                                               value="{{ old('dFecha_inicio_oferta', $variacion->dFecha_inicio_oferta ? \Carbon\Carbon::parse($variacion->dFecha_inicio_oferta)->format('Y-m-d') : '') }}"
                                               onchange="validarFechasOferta()"
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
                                               value="{{ old('dFecha_fin_oferta', $variacion->dFecha_fin_oferta ? \Carbon\Carbon::parse($variacion->dFecha_fin_oferta)->format('Y-m-d') : '') }}"
                                               onchange="validarFechasOferta()"
                                               autocomplete="off">
                                        <div id="error-fechas-oferta" class="invalid-feedback" style="display: none;"></div>
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
                                               value="{{ old('vMotivo_oferta', $variacion->vMotivo_oferta) }}"
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
                                    <div class="input-group">
                                        <input type="text" 
                                               name="dPeso" 
                                               id="dPeso" 
                                               class="form-control @error('dPeso') is-invalid @enderror"
                                               value="{{ old('dPeso', $variacion->dPeso) }}"
                                               oninput="validarPeso(this)"
                                               onblur="formatearPeso(this)"
                                               onkeydown="permitirBorrado(event)"
                                               placeholder="0.000"
                                               title="Máximo: 999.999 kg (máximo 3 dígitos enteros, 3 decimales)"
                                               autocomplete="off">
                                        <span class="input-group-text">kg</span>
                                    </div>
                                    @error('dPeso')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Máximo: 999.999 kg (3 dígitos enteros, 3 decimales)</small>
                                    <div id="error-dPeso" class="invalid-feedback d-block" style="display: none;"></div>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="dLargo_cm" class="form-label fw-bold">
                                        <i class="fas fa-ruler-vertical me-1"></i>Largo (cm)
                                    </label>
                                    <div class="input-group">
                                        <input type="text" 
                                               name="dLargo_cm" 
                                               id="dLargo_cm" 
                                               class="form-control @error('dLargo_cm') is-invalid @enderror"
                                               value="{{ old('dLargo_cm', $variacion->dLargo_cm) }}"
                                               oninput="validarDimensionCm(this)"
                                               onblur="formatearDimensionCm(this)"
                                               onkeydown="permitirBorrado(event)"
                                               placeholder="0.00"
                                               title="Máximo: 999.99 cm (máximo 3 dígitos enteros, 2 decimales)"
                                               autocomplete="off">
                                        <span class="input-group-text">cm</span>
                                    </div>
                                    @error('dLargo_cm')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Máximo: 999.99 cm (3 dígitos enteros, 2 decimales)</small>
                                    <div id="error-dLargo_cm" class="invalid-feedback d-block" style="display: none;"></div>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="dAncho_cm" class="form-label fw-bold">
                                        <i class="fas fa-ruler-horizontal me-1"></i>Ancho (cm)
                                    </label>
                                    <div class="input-group">
                                        <input type="text" 
                                               name="dAncho_cm" 
                                               id="dAncho_cm" 
                                               class="form-control @error('dAncho_cm') is-invalid @enderror"
                                               value="{{ old('dAncho_cm', $variacion->dAncho_cm) }}"
                                               oninput="validarDimensionCm(this)"
                                               onblur="formatearDimensionCm(this)"
                                               onkeydown="permitirBorrado(event)"
                                               placeholder="0.00"
                                               title="Máximo: 999.99 cm (máximo 3 dígitos enteros, 2 decimales)"
                                               autocomplete="off">
                                        <span class="input-group-text">cm</span>
                                    </div>
                                    @error('dAncho_cm')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Máximo: 999.99 cm (3 dígitos enteros, 2 decimales)</small>
                                    <div id="error-dAncho_cm" class="invalid-feedback d-block" style="display: none;"></div>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="dAlto_cm" class="form-label fw-bold">
                                        <i class="fas fa-arrows-alt-v me-1"></i>Alto (cm)
                                    </label>
                                    <div class="input-group">
                                        <input type="text" 
                                               name="dAlto_cm" 
                                               id="dAlto_cm" 
                                               class="form-control @error('dAlto_cm') is-invalid @enderror"
                                               value="{{ old('dAlto_cm', $variacion->dAlto_cm) }}"
                                               oninput="validarDimensionCm(this)"
                                               onblur="formatearDimensionCm(this)"
                                               onkeydown="permitirBorrado(event)"
                                               placeholder="0.00"
                                               title="Máximo: 999.99 cm (máximo 3 dígitos enteros, 2 decimales)"
                                               autocomplete="off">
                                        <span class="input-group-text">cm</span>
                                    </div>
                                    @error('dAlto_cm')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Máximo: 999.99 cm (3 dígitos enteros, 2 decimales)</small>
                                    <div id="error-dAlto_cm" class="invalid-feedback d-block" style="display: none;"></div>
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
                                        <option value="estandar" {{ old('vClase_envio', $variacion->vClase_envio) == 'estandar' ? 'selected' : '' }}>Estándar</option>
                                        <option value="express" {{ old('vClase_envio', $variacion->vClase_envio) == 'express' ? 'selected' : '' }}>Express</option>
                                        <option value="fragil" {{ old('vClase_envio', $variacion->vClase_envio) == 'fragil' ? 'selected' : '' }}>Frágil</option>
                                        <option value="grandes_dimensiones" {{ old('vClase_envio', $variacion->vClase_envio) == 'grandes_dimensiones' ? 'selected' : '' }}>Grandes dimensiones</option>
                                    </select>
                                    @error('vClase_envio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        @if($producto->vClase_envio)
                                            Producto padre: <strong>{{ $producto->vClase_envio }}</strong>
                                        @else
                                            Producto padre: <strong>Sin clase de envío definida</strong>
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- DESCRIPCIÓN -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="tDescripcion" class="form-label fw-bold">
                                        Descripción de la variación
                                    </label>
                                    <textarea name="tDescripcion" id="tDescripcion" 
                                              class="form-control @error('tDescripcion') is-invalid @enderror" 
                                              rows="3" placeholder="Descripción específica de esta variación (opcional)">{{ old('tDescripcion', $variacion->tDescripcion) }}</textarea>
                                    @error('tDescripcion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN DE IMPUESTO -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-file-invoice-dollar me-2"></i>Impuesto para la Variación</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group mb-3">
                                    <label for="id_impuesto" class="form-label fw-bold">
                                        Impuesto Aplicable
                                    </label>
                                    <select name="id_impuesto" id="id_impuesto" 
                                            class="form-select @error('id_impuesto') is-invalid @enderror"
                                            onchange="actualizarPrecioFinal()">
                                        <option value="">-- Sin impuesto (heredar del producto) --</option>
                                        @if(isset($impuestos) && $impuestos->count() > 0)
                                            @foreach($impuestos as $impuesto)
                                                <option value="{{ $impuesto->id_impuesto }}" 
                                                        data-porcentaje="{{ $impuesto->dPorcentaje }}"
                                                        data-tipo="{{ $impuesto->eTipo }}"
                                                        {{ old('id_impuesto', $variacion->id_impuesto) == $impuesto->id_impuesto ? 'selected' : '' }}>
                                                    {{ $impuesto->vNombre }} ({{ $impuesto->eTipo }} - {{ number_format($impuesto->dPorcentaje, 2) }}%)
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('id_impuesto')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted mt-2">
                                        Selecciona un impuesto específico para esta variación. Si no seleccionas, se usará el impuesto del producto.
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="bg-light p-3 rounded text-center">
                                    <small class="text-muted d-block">Precio con impuesto</small>
                                    <h5 class="fw-bold mb-0" id="precio-final-display">$0.00</h5>
                                    <small class="text-muted" id="detalle-impuesto-display"></small>
                                </div>
                            </div>
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
                                        <h6 class="text-muted">Precio base (con oferta aplicada)</h6>
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
                                        <h6>Precio final (con impuesto)</h6>
                                        <h2 class="fw-bold" id="precio-final-total-display">$0.00</h2>
                                        <small>Este es el precio que verá el cliente</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- ATRIBUTOS -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-tags me-2"></i>Seleccionar Atributos</h5>
                    </div>
                    <div class="card-body">
                        @if(count($atributos) > 0)
                            <div class="alert alert-info small">
                                <i class="fas fa-info-circle me-2"></i>
                                Selecciona un valor para cada atributo asignado al producto.
                            </div>
                            
                            @php
                                // Obtener los valores seleccionados actualmente
                                $valoresSeleccionados = [];
                                foreach($variacion->atributos as $atributoRel) {
                                    $valoresSeleccionados[$atributoRel->id_atributo] = $atributoRel->id_atributo_valor;
                                }
                            @endphp
                            
                            @foreach($atributos as $nombreAtributo => $valores)
                                <div class="mb-4 p-3 border rounded atributo-container">
                                    <label class="fw-bold mb-2">{{ $nombreAtributo }} <span class="text-danger">*</span></label>
                                    <div class="form-group">
                                        @foreach($valores as $valor)
                                            <div class="form-check mb-2">
                                                <input type="radio" 
                                                       name="atributos[{{ $valor->atributo->id_atributo }}]" 
                                                       id="atributo_{{ $valor->atributo->id_atributo }}_{{ $valor->id_atributo_valor }}"
                                                       value="{{ $valor->id_atributo_valor }}"
                                                       class="form-check-input atributo-radio"
                                                       data-atributo-id="{{ $valor->atributo->id_atributo }}"
                                                       {{ old('atributos.' . $valor->atributo->id_atributo, $valoresSeleccionados[$valor->atributo->id_atributo] ?? '') == $valor->id_atributo_valor ? 'checked' : '' }}
                                                       required
                                                       autocomplete="off">
                                                <label class="form-check-label" for="atributo_{{ $valor->atributo->id_atributo }}_{{ $valor->id_atributo_valor }}">
                                                    {{ $valor->vValor }}
                                                    @if($valor->pivot && $valor->pivot->dPrecio_extra > 0)
                                                        <small class="text-muted">
                                                            (+${{ number_format($valor->pivot->dPrecio_extra, 2) }})</small>
                                                    @endif
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                            
                        @else
                            <div class="alert alert-warning text-center">
                                <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                                <h5>No hay atributos asignados</h5>
                                <p class="mb-3">Este producto no tiene atributos asignados.</p>
                                <p class="small">Primero asigna atributos al producto desde la página de edición.</p>
                                <a href="{{ route('productos.edit', $producto->id_producto) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit me-1"></i> Editar Producto
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- MULTIMEDIA DE LA VARIACIÓN -->
                <div class="card mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-images me-2"></i>Multimedia de la Variación</h5>
                    </div>
                    <div class="card-body">
                        <!-- IMAGEN PRINCIPAL DE LA VARIACIÓN -->
                        <div class="form-group mb-3">
                            <label for="imagen_principal" class="form-label fw-bold">
                                <i class="fas fa-star text-warning me-1"></i>Imagen Principal
                            </label>
                            
                            <!-- Imagen actual si existe -->
                            @php
                                $imagenPrincipalActual = $variacion->imagen_principal;
                            @endphp
                            
                            @if($imagenPrincipalActual)
                                <div id="current_principal_container" class="mb-2">
                                    <div class="border rounded p-2 text-center bg-light position-relative">
                                        <img src="{{ $imagenPrincipalActual }}" 
                                             class="img-thumbnail" 
                                             style="max-width: 150px; max-height: 150px; object-fit: contain;"
                                             alt="Imagen principal actual">
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarImagenPrincipal()">
                                                <i class="fas fa-trash me-1"></i>Eliminar imagen actual
                                            </button>
                                        </div>
                                        <input type="hidden" name="eliminar_imagen_principal" id="eliminar_imagen_principal" value="0">
                                    </div>
                                </div>
                            @endif
                            
                            <input type="file" name="imagen_principal" id="imagen_principal" 
                                   class="form-control @error('imagen_principal') is-invalid @enderror" 
                                   accept="image/jpeg,image/jpg,image/png"
                                   onchange="previewImagenPrincipal(this)">
                            @error('imagen_principal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Imagen específica para esta variación. Formatos: JPG, JPEG, PNG. Máximo 5MB.
                                @if($imagenPrincipalActual)
                                    <br><span class="text-warning">Si seleccionas una nueva imagen, reemplazará a la actual.</span>
                                @endif
                            </small>
                            
                            <!-- Preview de nueva imagen principal -->
                            <div id="preview_principal_container" class="mt-2" style="display: none;">
                                <div class="border rounded p-2 text-center bg-light">
                                    <img id="preview_principal_img" src="#" 
                                         class="img-thumbnail" 
                                         style="max-width: 150px; max-height: 150px; object-fit: contain;"
                                         alt="Preview imagen principal">
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-sm btn-outline-danger mt-1" onclick="cancelarImagenPrincipal()">
                                            <i class="fas fa-times me-1"></i>Cancelar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- GIF DE LA VARIACIÓN -->
                        <div class="form-group mb-3">
                            <label for="gif" class="form-label fw-bold">
                                <i class="fas fa-file-image text-success me-1"></i>GIF Animado (Opcional)
                            </label>
                            
                            <!-- GIF actual si existe -->
                            @php
                                $gifActual = $variacion->gif_url;
                            @endphp
                            
                            @if($gifActual)
                                <div id="current_gif_container" class="mb-2">
                                    <div class="border rounded p-2 text-center bg-light position-relative">
                                        <img src="{{ $gifActual }}" 
                                             class="img-thumbnail" 
                                             style="max-width: 150px; max-height: 150px; object-fit: contain;"
                                             alt="GIF actual">
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarGif()">
                                                <i class="fas fa-trash me-1"></i>Eliminar GIF actual
                                            </button>
                                        </div>
                                        <input type="hidden" name="eliminar_gif" id="eliminar_gif" value="0">
                                    </div>
                                </div>
                            @endif
                            
                            <input type="file" name="gif" id="gif" 
                                   class="form-control @error('gif') is-invalid @enderror" 
                                   accept="image/gif"
                                   onchange="previewGif(this)">
                            @error('gif')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Formatos: GIF. Máximo 10MB. Animación de la variación.
                                @if($gifActual)
                                    <br><span class="text-warning">Si seleccionas un nuevo GIF, reemplazará al actual.</span>
                                @endif
                            </small>
                            
                            <!-- Preview de nuevo GIF -->
                            <div id="preview_gif_container" class="mt-2" style="display: none;">
                                <div class="border rounded p-2 text-center bg-light">
                                    <img id="preview_gif" src="#" 
                                         class="img-thumbnail" 
                                         style="max-width: 150px; max-height: 150px; object-fit: contain;"
                                         alt="Preview GIF">
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-sm btn-outline-danger mt-1" onclick="cancelarGif()">
                                            <i class="fas fa-times me-1"></i>Cancelar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- IMÁGENES ADICIONALES DE LA VARIACIÓN -->
                        <div class="form-group mb-3">
                            <label for="imagenes_adicionales" class="form-label fw-bold">
                                <i class="fas fa-images me-1"></i>Imágenes Adicionales (Máximo 7)
                            </label>
                            
                            <!-- Mostrar imágenes adicionales existentes -->
                            @php
                                $imagenesAdicionales = $variacion->imagenes_adicionales ?? [];
                            @endphp
                            
                            @if(!empty($imagenesAdicionales))
                                <div id="existing-images-container" class="mb-3">
                                    <label class="form-label small text-muted">Imágenes actuales:</label>
                                    <div class="row g-2">
                                        @foreach($imagenesAdicionales as $index => $imgUrl)
                                            <div class="col-6 col-md-4 mb-2 existing-image-item" data-index="{{ $index }}">
                                                <div class="card border position-relative">
                                                    <button type="button" 
                                                            class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1"
                                                            style="width: 24px; height: 24px; padding: 0; border-radius: 50%; z-index: 10;"
                                                            onclick="marcarImagenParaEliminar(this, '{{ $imgUrl }}', {{ $index }})">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                    <img src="{{ $imgUrl }}" 
                                                         class="card-img-top" 
                                                         style="height: 80px; object-fit: contain; background: #f8f9fa; padding: 4px;"
                                                         alt="Imagen adicional {{ $index + 1 }}">
                                                    <div class="card-body p-1 text-center">
                                                        <small class="text-muted d-block" style="font-size: 10px;">Imagen {{ $index + 1 }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <input type="hidden" name="imagenes_a_eliminar" id="imagenes_a_eliminar" value="">
                            @endif
                            
                            <input type="file" name="imagenes_adicionales[]" id="imagenes_adicionales" 
                                   class="form-control @error('imagenes_adicionales') is-invalid @enderror" 
                                   multiple accept="image/jpeg,image/jpg,image/png,image/webp"
                                   onchange="handleImageSelection(event)">
                            @error('imagenes_adicionales')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Formatos: JPG, JPEG, PNG, WEBP. Máximo 5MB por imagen.
                                Puedes seleccionar hasta 7 imágenes adicionales.
                            </small>
                            <div class="mt-2">
                                <span class="badge bg-info" id="selected-images-count">0 archivos nuevos</span>
                            </div>
                        </div>
                        
                        <!-- Galería de nuevas imágenes adicionales seleccionadas -->
                        <div class="mt-3">
                            <h6 class="fw-bold mb-2"><i class="fas fa-images me-2"></i>Nuevas imágenes a agregar:</h6>
                            <div id="selected-images-container" class="row g-2"></div>
                            <div class="alert alert-info py-2" id="no-imagenes-msg" style="display: block;">
                                <i class="fas fa-info-circle me-1"></i>
                                <small>No hay nuevas imágenes seleccionadas</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- BOTONES DE ACCIÓN -->
        <div class="d-flex gap-2 mb-4">
            <button type="submit" class="btn btn-success btn-lg px-4" id="btnSubmit">
                <i class="fas fa-save me-2"></i> Actualizar Variación
            </button>
            <a href="{{ route('variaciones.show', $producto->id_producto) }}" class="btn btn-secondary btn-lg px-4">
                <i class="fas fa-times me-2"></i> Cancelar
            </a>
        </div>
    </form>
</div>

@push('styles')
<style>
.form-check-input:checked + .form-check-label {
    font-weight: bold;
    color: #198754;
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

#selected-images-container .card:hover,
#existing-images-container .card:hover,
#preview_principal_container .card:hover,
#preview_gif_container .card:hover,
#current_principal_container .card:hover,
#current_gif_container .card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

#selected-images-container .btn-danger,
#existing-images-container .btn-danger,
#preview_principal_container .btn-danger,
#preview_gif_container .btn-danger,
#current_principal_container .btn-danger,
#current_gif_container .btn-danger {
    transition: all 0.3s ease;
}

#selected-images-container .btn-danger:hover,
#existing-images-container .btn-danger:hover,
#preview_principal_container .btn-danger:hover,
#preview_gif_container .btn-danger:hover,
#current_principal_container .btn-danger:hover,
#current_gif_container .btn-danger:hover {
    transform: scale(1.1);
    background-color: #c82333;
    border-color: #bd2130;
}

.form-control:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.form-control::placeholder {
    color: #6c757d;
    opacity: 0.7;
}

.alert-info {
    background-color: #d1ecf1;
    border-color: #bee5eb;
    color: #0c5460;
}

.border-danger {
    border-color: #dc3545 !important;
    background-color: rgba(220, 53, 69, 0.05);
}

.atributo-container {
    transition: all 0.3s ease;
}

.atributo-container:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

/* Estilos para validación mejorada */
.form-control.is-invalid {
    border-color: #dc3545;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.form-control.is-invalid:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
}

.invalid-feedback {
    display: block;
    font-size: 0.875em;
    margin-top: 0.25rem;
}

/* Estilos para inputs de moneda y dimensiones */
.input-group-text {
    background-color: #f8f9fa;
    border: 1px solid #ced4da;
}

/* Estilos para sección de impuestos */
.bg-info .card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.bg-info .card .card-body {
    padding: 1rem;
}

/* Estilos para sección de oferta */
#ofertaFields {
    transition: all 0.3s ease;
}

/* Estilo para imágenes marcadas para eliminar */
.existing-image-item.marcado-eliminar {
    opacity: 0.5;
    filter: grayscale(100%);
    position: relative;
}

.existing-image-item.marcado-eliminar::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 40px;
    color: #dc3545;
    font-weight: bold;
    z-index: 20;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
}

/* Responsive */
@media (max-width: 768px) {
    #selected-images-container .card-img-top,
    #existing-images-container .card-img-top {
        height: 100px !important;
    }
    
    #preview_principal_img,
    #preview_gif {
        max-width: 120px !important;
        max-height: 120px !important;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Variables globales para imágenes
let imagenPrincipalFile = null;
let gifFile = null;
let selectedImages = [];
let imageCounter = 0;
let imagenesAEliminar = [];

// ==================== VALIDACIONES ====================

function validarSKU(input) {
    // Permitir letras, números y guiones
    input.value = input.value.replace(/[^A-Za-z0-9\-]/g, '');
    
    // Limitar a 15 caracteres
    if (input.value.length > 15) {
        input.value = input.value.substring(0, 15);
    }
    
    // Convertir a mayúsculas automáticamente
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
    
    // Eliminar todo excepto números y un punto decimal
    value = value.replace(/[^0-9.]/g, '');
    
    // Verificar que no haya más de un punto decimal
    const puntos = value.split('.').length - 1;
    if (puntos > 1) {
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
    
    // Limpiar error específico del input
    limpiarErrorPrecio(input);
    
    // Mostrar error si el número es muy grande
    if (value) {
        const numero = parseFloat(value);
        if (!isNaN(numero) && numero > 9999999.99) {
            input.classList.add('is-invalid');
            mostrarErrorPrecio(input, 'El precio máximo es 9,999,999.99');
        }
    }
    
    // Validaciones específicas
    if (input.id === 'dPrecio_oferta') {
        validarPrecioOfertaInstantaneo(input);
    }
    
    actualizarPrecioFinal();
}

// Función para mostrar error de precio
function mostrarErrorPrecio(input, mensaje) {
    const errorId = `error-${input.id}-limite`;
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

// Función para limpiar error de precio
function limpiarErrorPrecio(input) {
    const errorId = `error-${input.id}-limite`;
    const errorElement = document.getElementById(errorId);
    if (errorElement) {
        errorElement.remove();
    }
    input.classList.remove('is-invalid');
}

function validarPrecioOfertaInstantaneo(input) {
    const tieneOferta = document.getElementById('bTiene_oferta');
    if (!tieneOferta || !tieneOferta.checked) return true;
    
    const precioVenta = parseFloat(document.getElementById('dPrecio').value) || 0;
    const precioOferta = parseFloat(input.value) || 0;
    const errorDiv = document.getElementById('error-precio-oferta');
    
    if (precioOferta >= precioVenta && precioOferta > 0 && input.value !== '') {
        input.classList.add('is-invalid');
        errorDiv.style.display = 'block';
        errorDiv.textContent = 'El precio de oferta debe ser menor que el precio de venta';
        return false;
    } else {
        input.classList.remove('is-invalid');
        errorDiv.style.display = 'none';
        return true;
    }
}

function validarPrecioOferta() {
    const tieneOferta = document.getElementById('bTiene_oferta');
    if (!tieneOferta || !tieneOferta.checked) return true;
    
    const precioVenta = parseFloat(document.getElementById('dPrecio').value) || 0;
    const precioOferta = parseFloat(document.getElementById('dPrecio_oferta').value) || 0;
    const input = document.getElementById('dPrecio_oferta');
    const errorDiv = document.getElementById('error-precio-oferta');
    
    if (precioOferta >= precioVenta && precioOferta > 0) {
        input.classList.add('is-invalid');
        errorDiv.style.display = 'block';
        errorDiv.textContent = 'El precio de oferta debe ser menor que el precio de venta';
        return false;
    } else {
        input.classList.remove('is-invalid');
        errorDiv.style.display = 'none';
        return true;
    }
}

function validarFechasOferta() {
    const fechaInicio = document.getElementById('dFecha_inicio_oferta');
    const fechaFin = document.getElementById('dFecha_fin_oferta');
    const errorDiv = document.getElementById('error-fechas-oferta');
    
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

function validarStock(input) {
    // Remover cualquier caracter que no sea número
    input.value = input.value.replace(/[^0-9]/g, '');
    
    // Limitar a máximo 6 dígitos
    if (input.value.length > 6) {
        input.value = input.value.substring(0, 6);
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

// ==================== VALIDACIONES DE DIMENSIONES ====================

function validarPeso(input) {
    let value = input.value;
    const cursorPos = input.selectionStart;
    
    if (value === '') {
        limpiarErrorDimension(input);
        return;
    }
    
    // Solo números y punto decimal
    value = value.replace(/[^0-9.]/g, '');
    
    // Un solo punto decimal
    const puntos = value.split('.').length - 1;
    if (puntos > 1) {
        const partes = value.split('.');
        value = partes[0] + '.' + partes.slice(1).join('');
    }
    
    // Eliminar múltiples puntos
    value = value.replace(/\.{2,}/g, '.');
    
    // Agregar 0 si empieza con punto
    if (value.startsWith('.')) {
        value = '0' + value;
    }
    
    // Limitar decimales a máximo 3
    if (value.includes('.')) {
        const partes = value.split('.');
        if (partes[1].length > 3) {
            partes[1] = partes[1].substring(0, 3);
            value = partes[0] + '.' + partes[1];
        }
    }
    
    // Limitar parte entera a máximo 3 dígitos
    if (value.includes('.')) {
        const partes = value.split('.');
        if (partes[0].length > 3) {
            partes[0] = partes[0].substring(0, 3);
            value = partes[0] + '.' + partes[1];
        }
    } else {
        if (value.length > 3) {
            value = value.substring(0, 3);
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
    
    // Limpiar error primero
    limpiarErrorDimension(input);
    
    // Validar que no exceda 999.999 kg
    if (value) {
        const numero = parseFloat(value);
        if (!isNaN(numero) && numero > 999.999) {
            input.classList.add('is-invalid');
            mostrarErrorDimension(input, 'El peso máximo es 999.999 kg');
        }
    }
}

function validarDimensionCm(input) {
    let value = input.value;
    const cursorPos = input.selectionStart;
    
    if (value === '') {
        limpiarErrorDimension(input);
        return;
    }
    
    // Solo números y punto decimal
    value = value.replace(/[^0-9.]/g, '');
    
    // Un solo punto decimal
    const puntos = value.split('.').length - 1;
    if (puntos > 1) {
        const partes = value.split('.');
        value = partes[0] + '.' + partes.slice(1).join('');
    }
    
    // Eliminar múltiples puntos
    value = value.replace(/\.{2,}/g, '.');
    
    // Agregar 0 si empieza con punto
    if (value.startsWith('.')) {
        value = '0' + value;
    }
    
    // Limitar decimales a máximo 2
    if (value.includes('.')) {
        const partes = value.split('.');
        if (partes[1].length > 2) {
            partes[1] = partes[1].substring(0, 2);
            value = partes[0] + '.' + partes[1];
        }
    }
    
    // Limitar parte entera a máximo 3 dígitos
    if (value.includes('.')) {
        const partes = value.split('.');
        if (partes[0].length > 3) {
            partes[0] = partes[0].substring(0, 3);
            value = partes[0] + '.' + partes[1];
        }
    } else {
        if (value.length > 3) {
            value = value.substring(0, 3);
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
    
    // Limpiar error primero
    limpiarErrorDimension(input);
    
    // Validar que no exceda 999.99 cm
    if (value) {
        const numero = parseFloat(value);
        if (!isNaN(numero) && numero > 999.99) {
            input.classList.add('is-invalid');
            mostrarErrorDimension(input, 'La dimensión máxima es 999.99 cm');
        }
    }
}

// Función para mostrar error de dimensiones
function mostrarErrorDimension(input, mensaje) {
    const errorId = `error-${input.id}`;
    let errorElement = document.getElementById(errorId);
    
    if (!errorElement) {
        errorElement = document.createElement('div');
        errorElement.className = 'invalid-feedback d-block';
        errorElement.id = errorId;
        input.parentNode.appendChild(errorElement);
    }
    
    errorElement.textContent = mensaje;
    input.classList.add('is-invalid');
}

// Función para limpiar error de dimensiones
function limpiarErrorDimension(input) {
    const errorId = `error-${input.id}`;
    const errorElement = document.getElementById(errorId);
    if (errorElement) {
        errorElement.textContent = '';
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
    
    // Asegurar que tenga el formato correcto (máx 3 decimales)
    if (input.value.includes('.')) {
        const partes = input.value.split('.');
        if (partes[1].length > 3) {
            input.value = partes[0] + '.' + partes[1].substring(0, 3);
        }
    }
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
    
    // Asegurar que tenga el formato correcto (máx 2 decimales)
    if (input.value.includes('.')) {
        const partes = input.value.split('.');
        if (partes[1].length > 2) {
            input.value = partes[0] + '.' + partes[1].substring(0, 2);
        }
    }
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

// ==================== FUNCIONES PARA OFERTA ====================

function toggleOfertaFields() {
    const ofertaFields = document.getElementById('ofertaFields');
    const tieneOferta = document.getElementById('bTiene_oferta').checked;
    const precioOferta = document.getElementById('dPrecio_oferta');
    const fechaInicio = document.getElementById('dFecha_inicio_oferta');
    const fechaFin = document.getElementById('dFecha_fin_oferta');
    
    if (tieneOferta) {
        ofertaFields.style.display = 'block';
        precioOferta.required = true;
        fechaInicio.required = true;
        fechaFin.required = true;
        
        setTimeout(() => {
            validarPrecioOferta();
            actualizarPrecioFinal();
        }, 100);
    } else {
        ofertaFields.style.display = 'none';
        precioOferta.required = false;
        fechaInicio.required = false;
        fechaFin.required = false;
        
        precioOferta.classList.remove('is-invalid');
        document.getElementById('error-precio-oferta').style.display = 'none';
        fechaFin.classList.remove('is-invalid');
        document.getElementById('error-fechas-oferta').style.display = 'none';
        
        actualizarPrecioFinal();
    }
}

// ==================== FUNCIÓN DE CÁLCULO DE IMPUESTO Y PRECIO FINAL ====================

function actualizarPrecioFinal() {
    const precioInput = document.getElementById('dPrecio');
    const tieneOferta = document.getElementById('bTiene_oferta')?.checked;
    const precioOfertaInput = document.getElementById('dPrecio_oferta');
    const impuestoSelect = document.getElementById('id_impuesto');
    
    if (!precioInput) return;
    
    // Determinar qué precio usar (original o con oferta)
    let precioBase = parseFloat(precioInput.value) || 0;
    let precioOriginal = precioBase;
    
    if (tieneOferta && precioOfertaInput && precioOfertaInput.value) {
        const precioOferta = parseFloat(precioOfertaInput.value) || 0;
        if (precioOferta > 0 && precioOferta < precioBase) {
            precioBase = precioOferta;
        }
    }
    
    // Mostrar precio base
    document.getElementById('precio-base-display').textContent = '$' + precioBase.toFixed(2);
    
    // Mostrar precio original si hay oferta
    const precioOriginalDisplay = document.getElementById('precio-original-display');
    if (tieneOferta && precioBase < precioOriginal) {
        precioOriginalDisplay.style.display = 'block';
        precioOriginalDisplay.textContent = 'Precio original: $' + precioOriginal.toFixed(2);
    } else {
        precioOriginalDisplay.style.display = 'none';
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
    document.getElementById('total-impuestos-display').textContent = '$' + totalImpuestos.toFixed(2);
    document.getElementById('precio-final-total-display').textContent = '$' + precioFinal.toFixed(2);
    document.getElementById('precio-final-display').textContent = '$' + precioFinal.toFixed(2);
    
    if (porcentaje > 0) {
        document.getElementById('porcentaje-impuestos-display').textContent = porcentaje.toFixed(2) + '%';
        document.getElementById('detalle-impuesto-display').textContent = `${nombreImpuesto}: ${porcentaje.toFixed(2)}% ($${totalImpuestos.toFixed(2)})`;
    } else {
        document.getElementById('porcentaje-impuestos-display').textContent = '0%';
        document.getElementById('detalle-impuesto-display').textContent = 'Sin impuesto';
    }
}

// ==================== FUNCIONES PARA IMÁGENES ====================

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
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        previewContainer.style.display = 'none';
        imagenPrincipalFile = null;
    }
}

function cancelarImagenPrincipal() {
    const input = document.getElementById('imagen_principal');
    const previewContainer = document.getElementById('preview_principal_container');
    
    input.value = '';
    previewContainer.style.display = 'none';
    imagenPrincipalFile = null;
}

function eliminarImagenPrincipal() {
    Swal.fire({
        title: '¿Eliminar imagen principal?',
        text: 'Esta acción marcará la imagen actual para ser eliminada al guardar los cambios.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('eliminar_imagen_principal').value = '1';
            document.getElementById('current_principal_container').style.display = 'none';
            
            Swal.fire({
                title: 'Imagen marcada para eliminar',
                text: 'La imagen será eliminada al guardar los cambios.',
                icon: 'info',
                timer: 2000,
                showConfirmButton: false
            });
        }
    });
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
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        previewContainer.style.display = 'none';
        gifFile = null;
    }
}

function cancelarGif() {
    const input = document.getElementById('gif');
    const previewContainer = document.getElementById('preview_gif_container');
    
    input.value = '';
    previewContainer.style.display = 'none';
    gifFile = null;
}

function eliminarGif() {
    Swal.fire({
        title: '¿Eliminar GIF?',
        text: 'Esta acción marcará el GIF actual para ser eliminado al guardar los cambios.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('eliminar_gif').value = '1';
            document.getElementById('current_gif_container').style.display = 'none';
            
            Swal.fire({
                title: 'GIF marcado para eliminar',
                text: 'El GIF será eliminado al guardar los cambios.',
                icon: 'info',
                timer: 2000,
                showConfirmButton: false
            });
        }
    });
}

function marcarImagenParaEliminar(btn, imgUrl, index) {
    const container = btn.closest('.existing-image-item');
    
    if (container.classList.contains('marcado-eliminar')) {
        // Desmarcar
        container.classList.remove('marcado-eliminar');
        const idx = imagenesAEliminar.indexOf(imgUrl);
        if (idx > -1) {
            imagenesAEliminar.splice(idx, 1);
        }
    } else {
        // Marcar
        container.classList.add('marcado-eliminar');
        imagenesAEliminar.push(imgUrl);
    }
    
    document.getElementById('imagenes_a_eliminar').value = JSON.stringify(imagenesAEliminar);
}

function handleImageSelection(event) {
    const files = event.target.files;
    const maxFiles = 7;
    const currentCount = selectedImages.length;
    const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    
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
        document.getElementById('selected-images-count').textContent = selectedImages.length + ' archivos nuevos';
        renderSelectedImages();
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
    
    document.getElementById('selected-images-count').textContent = selectedImages.length + ' archivos nuevos';
    renderSelectedImages();
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
        col.className = 'col-6 col-md-4 mb-2';
        
        const card = document.createElement('div');
        card.className = 'card border image-preview-card position-relative';
        
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'btn btn-danger btn-sm position-absolute top-0 end-0 m-1 remove-btn';
        btn.style.cssText = 'width: 24px; height: 24px; padding: 0; border-radius: 50%; z-index: 10;';
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
        img.style.cssText = 'height: 80px; object-fit: contain; background: #f8f9fa; padding: 4px;';
        img.alt = 'Imagen ' + (index + 1);
        
        const cardBody = document.createElement('div');
        cardBody.className = 'card-body p-1 text-center';
        
        const small1 = document.createElement('small');
        small1.className = 'text-muted d-block';
        small1.style.cssText = 'font-size: 10px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;';
        small1.textContent = image.file.name.length > 15 ? image.file.name.substring(0, 15) + '...' : image.file.name;
        
        const small2 = document.createElement('small');
        small2.className = 'text-muted d-block';
        small2.style.fontSize = '9px';
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

// ==================== VALIDACIÓN DEL FORMULARIO ====================

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar toggle de oferta
    if (document.getElementById('bTiene_oferta') && document.getElementById('bTiene_oferta').checked) {
        toggleOfertaFields();
    }
    
    // Inicializar cálculo de precio final
    actualizarPrecioFinal();
    
    // Event listeners para calcular precio final cuando cambian los valores
    document.getElementById('dPrecio').addEventListener('input', actualizarPrecioFinal);
    document.getElementById('id_impuesto').addEventListener('change', actualizarPrecioFinal);
    
    // Validación en tiempo real de atributos
    document.querySelectorAll('.atributo-radio').forEach(radio => {
        radio.addEventListener('change', function() {
            const container = this.closest('.atributo-container');
            if (container) {
                container.classList.remove('border-danger');
                const errorExistente = container.querySelector('.error-atributo');
                if (errorExistente) {
                    errorExistente.remove();
                }
            }
        });
    });
    
    // Prevenir que el navegador guarde valores del autocomplete
    document.querySelectorAll('input, select, textarea').forEach(element => {
        element.setAttribute('autocomplete', 'off');
    });
    
    // Validación del formulario al enviar
    const form = document.getElementById('variacionForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            let erroresCriticos = false;
            
            // 1. Validar campos obligatorios
            const camposObligatorios = [
                {id: 'vSKU', nombre: 'SKU'},
                {id: 'dPrecio', nombre: 'Precio'},
                {id: 'iStock', nombre: 'Stock'}
            ];
            
            camposObligatorios.forEach(campo => {
                const elemento = document.getElementById(campo.id);
                if (!elemento.value.trim()) {
                    elemento.classList.add('is-invalid');
                    erroresCriticos = true;
                }
            });
            
            // 2. Validar atributos
            let atributosValidos = true;
            const atributosContainers = document.querySelectorAll('.atributo-container');
            
            atributosContainers.forEach(container => {
                const radioButtons = container.querySelectorAll('.atributo-radio');
                let seleccionado = false;
                
                radioButtons.forEach(radio => {
                    if (radio.checked) {
                        seleccionado = true;
                    }
                });
                
                if (!seleccionado) {
                    atributosValidos = false;
                    container.classList.add('border-danger');
                    
                    const errorExistente = container.querySelector('.error-atributo');
                    if (!errorExistente) {
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'error-atributo text-danger small mt-1';
                        errorDiv.innerHTML = `<i class="fas fa-exclamation-circle me-1"></i> Selecciona un valor`;
                        container.appendChild(errorDiv);
                    }
                }
            });
            
            if (!atributosValidos) {
                erroresCriticos = true;
            }
            
            // 3. Validar precio de oferta si está activo
            if (document.getElementById('bTiene_oferta') && document.getElementById('bTiene_oferta').checked) {
                if (!validarPrecioOferta()) {
                    erroresCriticos = true;
                }
                if (!validarFechasOferta()) {
                    erroresCriticos = true;
                }
            }
            
            // 4. Actualizar input de imágenes a eliminar
            if (imagenesAEliminar.length > 0) {
                document.getElementById('imagenes_a_eliminar').value = JSON.stringify(imagenesAEliminar);
            }
            
            // Si hay errores críticos, prevenir envío
            if (erroresCriticos) {
                e.preventDefault();
                
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "Por favor corrige los errores en el formulario.",
                    position: "center"
                });
                return false;
            }
            
            return true;
        });
    }
});

// Mostrar mensaje SweetAlert2 después de actualizar exitosamente
@if(session('success'))
Swal.fire({
    title: "¡Actualizado!",
    text: "{{ session('success') }}",
    icon: "success",
    draggable: true,
    position: "center",
    timer: 3000,
    showConfirmButton: false
});
@endif

// Mostrar mensaje SweetAlert2 si hay error
@if(session('error') || $errors->any())
@php
    $errorMessage = session('error');
    if (!$errorMessage && $errors->any()) {
        $errorMessage = 'Por favor corrige los errores en el formulario.';
    }
@endphp
Swal.fire({
    icon: "error",
    title: "Oops...",
    text: "{{ $errorMessage }}",
    position: "center",
    draggable: true
});
@endif
</script>
@endpush

@endsection