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

    @php
        $tieneDescuentoActivo = $variacion->tieneDescuentoActivo();
        $porcentajeDescuento = $variacion->porcentaje_descuento;
        
        $imagenesAdicionales = $variacion->imagenesRegistradas()
            ->where('eTipo', 'adicional')
            ->orderBy('iOrden')
            ->get();
        $totalImagenesActuales = $imagenesAdicionales->count();
        
        $imagenesExistentesData = [];
        foreach($imagenesAdicionales as $imagen) {
            $imagenesExistentesData[] = [
                'id' => $imagen->id_variacion_imagen,
                'url' => $imagen->url,
                'filename' => basename($imagen->vRuta),
                'ruta' => $imagen->vRuta
            ];
        }
    @endphp

    <!-- Barra de progreso de tamaño total de archivos -->
    <div class="alert alert-info py-2 mb-3" id="sizeInfo">
        <div class="row align-items-center">
            <div class="col-md-6">
                <i class="fas fa-camera me-1"></i>
                <strong>Total de archivos multimedia:</strong> 
                <span id="total-imagenes">{{ $totalImagenesActuales + ($variacion->imagen_principal_url ? 1 : 0) + ($variacion->gif_url ? 1 : 0) }}</span> archivos
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

    <div class="alert alert-warning" id="limiteArchivosMsg" style="display: none;">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>¡Atención!</strong> Has excedido el límite de tamaño total de archivos (50MB).
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
                        @if($tieneDescuentoActivo)
                            <div class="alert alert-success mb-3">
                                <i class="fas fa-tag me-2"></i>
                                <strong>¡Esta variación tiene un descuento activo del {{ $porcentajeDescuento }}%!</strong>
                                @if($variacion->vMotivo_descuento)
                                    <br><small>Motivo: {{ $variacion->vMotivo_descuento }}</small>
                                @endif
                                @if($variacion->dFecha_inicio_descuento && $variacion->dFecha_fin_descuento)
                                    <br><small>Período: {{ \Carbon\Carbon::parse($variacion->dFecha_inicio_descuento)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($variacion->dFecha_fin_descuento)->format('d/m/Y') }}</small>
                                @endif
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="vSKU" class="form-label fw-bold">
                                        SKU <span class="text-danger">*</span>
                                    </label>
                                    <div class="position-relative">
                                        <input type="text" 
                                               name="vSKU" 
                                               id="vSKU" 
                                               class="form-control @error('vSKU') is-invalid @enderror"
                                               value="{{ old('vSKU', $variacion->vSKU) }}" 
                                               maxlength="25" 
                                               required
                                               oninput="validarSKU(this); verificarSKUVariacionLocal(this)"
                                               pattern="[A-Za-z0-9\-]+"
                                               title="Solo letras, números y guiones (máximo 25 caracteres)"
                                               autocomplete="off"
                                               data-original-sku="{{ $variacion->vSKU }}">
                                        <div id="sku-error" class="invalid-feedback" style="display: none;"></div>
                                    </div>
                                    @error('vSKU')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Ej: AGAVE001-ROJO, MEZCAL2024-VERDE (máximo 25 caracteres, solo letras, números y guiones)
                                    </small>
                                </div>
                            </div>
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
                                               value="{{ old('dPrecio', number_format($variacion->dPrecio, 2, '.', '')) }}" 
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
                                    <input type="text" 
                                           name="iStock" 
                                           id="iStock" 
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

                        <!-- CAMPOS DE DESCUENTO -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-percentage me-1"></i>Descuento Especial
                                    </label>
                                    <div class="form-check form-switch">
                                        <input type="checkbox" name="bTiene_descuento" id="bTiene_descuento" 
                                               class="form-check-input" value="1"
                                               {{ old('bTiene_descuento', $variacion->bTiene_descuento) ? 'checked' : '' }}
                                               onchange="toggleDescuentoFields()">
                                        <label class="form-check-label" for="bTiene_descuento">
                                            Activar descuento para esta variación
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Permite establecer un precio de descuento por tiempo limitado
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- CAMPOS DE DESCUENTO -->
                        <div id="descuentoFields" style="display: {{ old('bTiene_descuento', $variacion->bTiene_descuento) ? 'block' : 'none' }};">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="dPrecio_descuento" class="form-label fw-bold">
                                            Precio de descuento <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="text" 
                                                   name="dPrecio_descuento" 
                                                   id="dPrecio_descuento" 
                                                   class="form-control @error('dPrecio_descuento') is-invalid @enderror"
                                                   value="{{ old('dPrecio_descuento', $variacion->dPrecio_descuento ? number_format($variacion->dPrecio_descuento, 2, '.', '') : '') }}" 
                                                   oninput="validarPrecio(this); validarPrecioDescuentoInstantaneo(this); actualizarPrecioFinal();"
                                                   onblur="validarPrecioDescuento()"
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
                                               value="{{ old('dFecha_inicio_descuento', $variacion->dFecha_inicio_descuento ? \Carbon\Carbon::parse($variacion->dFecha_inicio_descuento)->format('Y-m-d') : '') }}"
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
                                               value="{{ old('dFecha_fin_descuento', $variacion->dFecha_fin_descuento ? \Carbon\Carbon::parse($variacion->dFecha_fin_descuento)->format('Y-m-d') : '') }}"
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
                                               value="{{ old('vMotivo_descuento', $variacion->vMotivo_descuento) }}"
                                               maxlength="255"
                                               placeholder="Ej: Liquidación de temporada, Black Friday, etc."
                                               autocomplete="off">
                                        @error('vMotivo_descuento')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Opcional: Razón del descuento</small>
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
                                        <option value="">-- Heredar del producto --</option>
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
                                                        data-nombre="{{ $impuesto->vNombre }}"
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
                                    <h5 class="fw-bold mb-0" id="precio-final-display">${{ number_format($variacion->precio_final, 2) }}</h5>
                                    <small class="text-muted" id="detalle-impuesto-display">
                                        @if($variacion->impuesto)
                                            {{ $variacion->impuesto->vNombre }}: {{ $variacion->impuesto->dPorcentaje }}%
                                        @else
                                            Sin impuesto
                                        @endif
                                    </small>
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
                                        <h6 class="text-muted">Precio base (con descuento aplicado)</h6>
                                        <h3 class="fw-bold" id="precio-base-display">
                                            ${{ number_format($variacion->precio_actual, 2) }}
                                        </h3>
                                        @if($tieneDescuentoActivo)
                                            <small class="text-muted" id="precio-original-display">
                                                Precio original: ${{ number_format($variacion->dPrecio, 2) }}
                                            </small>
                                        @else
                                            <small class="text-muted" id="precio-original-display" style="display: none;"></small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-white text-dark">
                                    <div class="card-body text-center">
                                        <h6 class="text-muted">Impuesto</h6>
                                        <h3 class="fw-bold" id="total-impuestos-display">
                                            ${{ number_format($variacion->total_impuesto, 2) }}
                                        </h3>
                                        <small id="porcentaje-impuestos-display">
                                            {{ $variacion->porcentaje_impuesto }}%
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h6>Precio final (con impuesto)</h6>
                                        <h2 class="fw-bold" id="precio-final-total-display">
                                            ${{ number_format($variacion->precio_final, 2) }}
                                        </h2>
                                        <small>Este es el precio que verá el cliente</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="alert alert-light text-dark p-3" id="detalle-impuesto-info">
                                    @if($variacion->impuesto)
                                        <strong>Cálculo de {{ $variacion->impuesto->vNombre }}:</strong><br>
                                        Precio base: ${{ number_format($variacion->precio_actual, 2) }}<br>
                                        {{ $variacion->impuesto->vNombre }} ({{ $variacion->impuesto->dPorcentaje }}%): +${{ number_format($variacion->total_impuesto, 2) }}<br>
                                        <strong>Total: ${{ number_format($variacion->precio_final, 2) }}</strong>
                                    @else
                                        Precio final: ${{ number_format($variacion->precio_actual, 2) }} (Sin impuestos)
                                    @endif
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
                                            @php
                                                $selected = old('atributos.' . $valor->atributo->id_atributo, $valoresSeleccionados[$valor->atributo->id_atributo] ?? '') == $valor->id_atributo_valor;
                                            @endphp
                                            <div class="form-check mb-2">
                                                <input type="radio" 
                                                       name="atributos[{{ $valor->atributo->id_atributo }}]" 
                                                       id="atributo_{{ $valor->atributo->id_atributo }}_{{ $valor->id_atributo_valor }}"
                                                       value="{{ $valor->id_atributo_valor }}"
                                                       class="form-check-input atributo-radio"
                                                       data-atributo-id="{{ $valor->atributo->id_atributo }}"
                                                       data-atributo-nombre="{{ $nombreAtributo }}"
                                                       data-valor-nombre="{{ $valor->vValor }}"
                                                       {{ $selected ? 'checked' : '' }}
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
                            
                            @php
                                $imagenPrincipalActual = $variacion->imagen_principal_url;
                            @endphp
                            
                            @if($imagenPrincipalActual)
                                <div id="current_principal_container" class="mb-2">
                                    <div class="border rounded p-2 text-center bg-light position-relative">
                                        <img src="{{ $imagenPrincipalActual }}" 
                                             class="img-thumbnail" 
                                             style="max-width: 150px; max-height: 150px; object-fit: contain;"
                                             alt="Imagen principal actual"
                                             id="current_principal_img">
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
                            
                            @php
                                $gifActual = $variacion->gif_url;
                            @endphp
                            
                            @if($gifActual)
                                <div id="current_gif_container" class="mb-2">
                                    <div class="border rounded p-2 text-center bg-light position-relative">
                                        <img src="{{ $gifActual }}" 
                                             class="img-thumbnail" 
                                             style="max-width: 150px; max-height: 150px; object-fit: contain;"
                                             alt="GIF actual"
                                             id="current_gif_img">
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
                            
                            @if($imagenesAdicionales && $imagenesAdicionales->count() > 0)
                                <div id="existing-images-container" class="mb-3">
                                    <label class="form-label small text-muted">Imágenes actuales ({{ $totalImagenesActuales }}):</label>
                                    <div class="row g-2" id="existing-images-grid">
                                        @foreach($imagenesAdicionales as $index => $imagen)
                                            @php
                                                $nombreArchivo = basename($imagen->vRuta);
                                            @endphp
                                            <div class="col-6 col-md-4 mb-2 existing-image-item" data-id="{{ $imagen->id_variacion_imagen }}" data-filename="{{ $nombreArchivo }}" data-ruta="{{ $imagen->vRuta }}">
                                                <div class="card border position-relative">
                                                    <button type="button" 
                                                            class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1"
                                                            style="width: 24px; height: 24px; padding: 0; border-radius: 50%; z-index: 10;"
                                                            onclick="eliminarImagenAdicionalExistente(event, this, '{{ $imagen->id_variacion_imagen }}', '{{ addslashes($nombreArchivo) }}')">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                    <img src="{{ $imagen->url }}" 
                                                         class="card-img-top" 
                                                         style="height: 80px; object-fit: contain; background: #f8f9fa; padding: 4px;"
                                                         alt="Imagen adicional {{ $index + 1 }}"
                                                         onerror="this.onerror=null; this.src='https://via.placeholder.com/80x80?text=Error';">
                                                    <div class="card-body p-1 text-center">
                                                        <small class="text-muted d-block" style="font-size: 10px;">Imagen {{ $index + 1 }}</small>
                                                        <small class="text-muted d-block" style="font-size: 8px;">{{ Str::limit($nombreArchivo, 15) }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
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
                            <div class="mt-2 d-flex justify-content-between align-items-center">
                                <span class="badge bg-info" id="selected-images-count">
                                    {{ $totalImagenesActuales }} imágenes actuales + <span id="new-images-count">0</span> nuevas
                                </span>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="limpiarTodasLasImagenes()" id="btn-limpiar-imagenes" style="display: none;">
                                    <i class="fas fa-trash me-1"></i>Limpiar nuevas
                                </button>
                            </div>
                            
                            <!-- Input oculto para almacenar los IDs de imágenes a eliminar -->
                            <input type="hidden" name="imagenes_a_eliminar" id="imagenes_a_eliminar" value="">
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

/* Estilos para sección de descuento */
#descuentoFields {
    transition: all 0.3s ease;
}

/* Estilo para imágenes marcadas para eliminar */
.existing-image-item.eliminada {
    opacity: 0.5;
    filter: grayscale(100%);
    position: relative;
    background-color: #f8d7da;
    border-color: #f5c6cb;
    display: none !important;
}

.position-relative {
    position: relative !important;
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
// ============ DATOS EXISTENTES PARA VALIDACIÓN ============
const productosExistentes = @json(\App\Models\Producto::select('vCodigo_barras as sku')->get()->map(function($p) {
    return $p->sku;
})->values());

const variacionesExistentes = @json(\App\Models\ProductoVariacion::where('id_variacion', '!=', $variacion->id_variacion)
    ->select('vSKU as sku')
    ->get()
    ->map(function($v) {
        return $v->sku;
    })->values());

const skusProductosExistentes = new Set(productosExistentes);
const skusVariacionesExistentes = new Set(variacionesExistentes);

// ============ VARIABLES GLOBALES ============
let imagenPrincipalFile = null;
let gifFile = null;
let selectedImages = [];
let imageCounter = 0;
let maxTotalSize = 50 * 1024 * 1024;
let limiteExcedido = false;
let imagenesAEliminar = [];

// ============ FUNCIONES DE VALIDACIÓN ============

function verificarSKUVariacionLocal(input) {
    const sku = input.value.trim();
    let errorDiv = document.getElementById('sku-error');
    const originalSku = input.getAttribute('data-original-sku') || '';
    
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.id = 'sku-error';
        errorDiv.className = 'invalid-feedback';
        errorDiv.style.display = 'none';
        input.parentNode.appendChild(errorDiv);
    }
    
    if (sku === '' || sku === originalSku) {
        input.classList.remove('is-invalid');
        errorDiv.style.display = 'none';
        errorDiv.textContent = '';
        return true;
    }
    
    if (skusProductosExistentes.has(sku)) {
        input.classList.add('is-invalid');
        errorDiv.textContent = `⚠️ Ya existe un producto con el SKU "${sku}".`;
        errorDiv.style.display = 'block';
        return false;
    }
    
    if (skusVariacionesExistentes.has(sku)) {
        input.classList.add('is-invalid');
        errorDiv.textContent = `⚠️ Ya existe una variación con el SKU "${sku}".`;
        errorDiv.style.display = 'block';
        return false;
    }
    
    input.classList.remove('is-invalid');
    errorDiv.style.display = 'none';
    errorDiv.textContent = '';
    return true;
}

function validarSKU(input) {
    const cursorPos = input.selectionStart;
    let valor = input.value.replace(/[^A-Za-z0-9\-]/g, '');
    if (valor.length > 25) valor = valor.substring(0, 25);
    valor = valor.toUpperCase();
    
    if (input.value !== valor) {
        input.value = valor;
        setTimeout(() => input.setSelectionRange(cursorPos, cursorPos), 0);
    }
    
    if (input.value.trim() === '') input.classList.add('is-invalid');
    else input.classList.remove('is-invalid');
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
    if (value.startsWith('.')) value = '0' + value;
    
    const partesNumero = value.split('.');
    if (partesNumero[0].length > 7) {
        value = partesNumero[0].substring(0, 7) + (partesNumero[1] ? '.' + partesNumero[1] : '');
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
        setTimeout(() => input.setSelectionRange(newCursorPos, newCursorPos), 0);
    }
    
    input.classList.remove('is-invalid');
    
    if (value) {
        const numero = parseFloat(value);
        if (!isNaN(numero) && numero > 9999999.99) {
            input.classList.add('is-invalid');
        }
    }
    
    if (input.id === 'dPrecio_descuento') validarPrecioDescuentoInstantaneo(input);
    actualizarPrecioFinal();
}

function validarPrecioDescuentoInstantaneo(input) {
    const tieneDescuento = document.getElementById('bTiene_descuento');
    if (!tieneDescuento || !tieneDescuento.checked) return true;
    
    const precioVenta = parseFloat(document.getElementById('dPrecio').value) || 0;
    const precioDescuento = parseFloat(input.value) || 0;
    const errorDiv = document.getElementById('error-precio-descuento');
    
    if (precioDescuento >= precioVenta && precioDescuento > 0 && input.value !== '') {
        input.classList.add('is-invalid');
        if (errorDiv) {
            errorDiv.style.display = 'block';
            errorDiv.textContent = 'El precio de descuento debe ser menor que el precio de venta';
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

function validarPrecioDescuento() {
    const tieneDescuento = document.getElementById('bTiene_descuento');
    if (!tieneDescuento || !tieneDescuento.checked) return true;
    
    const precioVenta = parseFloat(document.getElementById('dPrecio').value) || 0;
    const precioDescuento = parseFloat(document.getElementById('dPrecio_descuento').value) || 0;
    const input = document.getElementById('dPrecio_descuento');
    const errorDiv = document.getElementById('error-precio-descuento');
    
    if (precioDescuento >= precioVenta && precioDescuento > 0) {
        input.classList.add('is-invalid');
        if (errorDiv) {
            errorDiv.style.display = 'block';
            errorDiv.textContent = 'El precio de descuento debe ser menor que el precio de venta';
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

function validarStock(input) {
    input.value = input.value.replace(/[^0-9]/g, '');
    if (input.value.length > 6) input.value = input.value.substring(0, 6);
    if (input.value && parseInt(input.value) < 0) input.value = '0';
    if (input.value.length > 1 && input.value.startsWith('0')) input.value = input.value.replace(/^0+/, '');
    if (input.value === '') input.value = '0';
    input.classList.remove('is-invalid');
}

function validarPeso(input) {
    let value = input.value;
    if (value === '') return;
    value = value.replace(/[^0-9.]/g, '');
    const puntos = value.split('.').length - 1;
    if (puntos > 1) {
        const partes = value.split('.');
        value = partes[0] + '.' + partes.slice(1).join('');
    }
    if (value.startsWith('.')) value = '0' + value;
    if (value.includes('.')) {
        const partes = value.split('.');
        if (partes[1].length > 3) {
            partes[1] = partes[1].substring(0, 3);
            value = partes[0] + '.' + partes[1];
        }
        if (partes[0].length > 3) value = partes[0].substring(0, 3) + '.' + partes[1];
    } else {
        if (value.length > 3) value = value.substring(0, 3);
    }
    input.value = value;
    
    if (value) {
        const numero = parseFloat(value);
        if (!isNaN(numero) && numero > 999.999) {
            input.classList.add('is-invalid');
        } else {
            input.classList.remove('is-invalid');
        }
    }
}

function validarDimensionCm(input) {
    let value = input.value;
    if (value === '') return;
    value = value.replace(/[^0-9.]/g, '');
    const puntos = value.split('.').length - 1;
    if (puntos > 1) {
        const partes = value.split('.');
        value = partes[0] + '.' + partes.slice(1).join('');
    }
    if (value.startsWith('.')) value = '0' + value;
    if (value.includes('.')) {
        const partes = value.split('.');
        if (partes[1].length > 2) {
            partes[1] = partes[1].substring(0, 2);
            value = partes[0] + '.' + partes[1];
        }
        if (partes[0].length > 3) value = partes[0].substring(0, 3) + '.' + partes[1];
    } else {
        if (value.length > 3) value = value.substring(0, 3);
    }
    input.value = value;
    
    if (value) {
        const numero = parseFloat(value);
        if (!isNaN(numero) && numero > 999.99) {
            input.classList.add('is-invalid');
        } else {
            input.classList.remove('is-invalid');
        }
    }
}

function formatearPeso(input) {
    let value = input.value;
    if (!value || value === '.' || value.endsWith('.')) return;
    let num = parseFloat(value);
    if (isNaN(num)) {
        input.value = '';
        return;
    }
    if (num > 999.999) num = 999.999;
    input.value = num.toString();
}

function formatearDimensionCm(input) {
    let value = input.value;
    if (!value || value === '.' || value.endsWith('.')) return;
    let num = parseFloat(value);
    if (isNaN(num)) {
        input.value = '';
        return;
    }
    if (num > 999.99) num = 999.99;
    input.value = num.toString();
}

function permitirBorrado(e) {
    const teclasPermitidas = ['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'Home', 'End', 'Tab', 'Enter'];
    if (e.ctrlKey || e.metaKey) return true;
    if (teclasPermitidas.includes(e.key)) return true;
    if (e.key >= '0' && e.key <= '9') return true;
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

// ==================== FUNCIONES PARA DESCUENTO ====================

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
            validarPrecioDescuento();
            actualizarPrecioFinal();
        }, 100);
    } else {
        descuentoFields.style.display = 'none';
        precioDescuento.required = false;
        fechaInicio.required = false;
        fechaFin.required = false;
        precioDescuento.classList.remove('is-invalid');
        const errorDiv = document.getElementById('error-precio-descuento');
        if (errorDiv) errorDiv.style.display = 'none';
        fechaFin.classList.remove('is-invalid');
        const errorFechas = document.getElementById('error-fechas-descuento');
        if (errorFechas) errorFechas.style.display = 'none';
        actualizarPrecioFinal();
    }
}

// ==================== FUNCIÓN DE CÁLCULO DE IMPUESTO ====================

function actualizarPrecioFinal() {
    const precioInput = document.getElementById('dPrecio');
    const tieneDescuento = document.getElementById('bTiene_descuento')?.checked;
    const precioDescuentoInput = document.getElementById('dPrecio_descuento');
    const impuestoSelect = document.getElementById('id_impuesto');
    
    if (!precioInput) return;
    
    let precioBase = parseFloat(precioInput.value) || 0;
    let precioOriginal = precioBase;
    let descuentoActivo = false;
    
    if (tieneDescuento && precioDescuentoInput && precioDescuentoInput.value) {
        const fechaHoy = new Date();
        fechaHoy.setHours(0, 0, 0, 0);
        const fechaInicioInput = document.getElementById('dFecha_inicio_descuento');
        const fechaFinInput = document.getElementById('dFecha_fin_descuento');
        let fechaInicio = null, fechaFin = null;
        if (fechaInicioInput?.value) fechaInicio = new Date(fechaInicioInput.value + 'T00:00:00');
        if (fechaFinInput?.value) fechaFin = new Date(fechaFinInput.value + 'T23:59:59');
        const precioDescuento = parseFloat(precioDescuentoInput.value) || 0;
        
        let aplicar = false;
        if (fechaInicio && fechaFin) aplicar = fechaHoy >= fechaInicio && fechaHoy <= fechaFin;
        else if (fechaInicio && !fechaFin) aplicar = fechaHoy >= fechaInicio;
        else if (!fechaInicio && fechaFin) aplicar = fechaHoy <= fechaFin;
        else aplicar = true;
        
        if (aplicar && precioDescuento > 0 && precioDescuento < precioBase) {
            precioBase = precioDescuento;
            descuentoActivo = true;
        }
    }
    
    document.getElementById('precio-base-display').textContent = '$' + precioBase.toFixed(2);
    const precioOriginalDisplay = document.getElementById('precio-original-display');
    if (precioOriginalDisplay) {
        if (tieneDescuento && precioDescuentoInput?.value && descuentoActivo) {
            precioOriginalDisplay.style.display = 'block';
            precioOriginalDisplay.innerHTML = `<span class="text-decoration-line-through">$${precioOriginal.toFixed(2)}</span><span class="badge bg-danger ms-2">¡DESCUENTO!</span>`;
        } else {
            precioOriginalDisplay.style.display = 'none';
        }
    }
    
    let totalImpuestos = 0, porcentaje = 0, nombreImpuesto = '';
    if (impuestoSelect && impuestoSelect.value) {
        const selectedOption = impuestoSelect.options[impuestoSelect.selectedIndex];
        porcentaje = parseFloat(selectedOption.dataset.porcentaje) || 0;
        nombreImpuesto = selectedOption.dataset.nombre || selectedOption.text.split('(')[0].trim();
        totalImpuestos = precioBase * (porcentaje / 100);
    }
    
    const precioFinal = precioBase + totalImpuestos;
    document.getElementById('total-impuestos-display').textContent = '+$' + totalImpuestos.toFixed(2);
    document.getElementById('precio-final-total-display').textContent = '$' + precioFinal.toFixed(2);
    document.getElementById('precio-final-display').textContent = '$' + precioFinal.toFixed(2);
    
    if (porcentaje > 0) {
        document.getElementById('porcentaje-impuestos-display').textContent = `+${porcentaje.toFixed(2)}% (${nombreImpuesto})`;
        document.getElementById('detalle-impuesto-display').textContent = `${nombreImpuesto}: ${porcentaje.toFixed(2)}% (+$${totalImpuestos.toFixed(2)})`;
        const detalleInfo = document.getElementById('detalle-impuesto-info');
        if (detalleInfo) {
            detalleInfo.innerHTML = `<strong>Cálculo de ${nombreImpuesto}:</strong><br>Precio base: $${precioBase.toFixed(2)}<br>${nombreImpuesto} (${porcentaje}%): +$${totalImpuestos.toFixed(2)}<br><strong>Total: $${precioFinal.toFixed(2)}</strong>`;
        }
    } else {
        document.getElementById('porcentaje-impuestos-display').textContent = '0%';
        document.getElementById('detalle-impuesto-display').textContent = 'Sin impuesto';
        const detalleInfo = document.getElementById('detalle-impuesto-info');
        if (detalleInfo) detalleInfo.innerHTML = `Precio final: $${precioBase.toFixed(2)} (Sin impuestos)`;
    }
}

// ==================== FUNCIONES PARA IMÁGENES ====================

function calcularTamañoTotal() {
    let total = 0;
    if (imagenPrincipalFile) total += imagenPrincipalFile.size;
    if (gifFile) total += gifFile.size;
    selectedImages.forEach(img => { total += img.file.size; });
    return total;
}

function actualizarBarraProgresoTamaño() {
    const totalSize = calcularTamañoTotal();
    const maxSize = 50 * 1024 * 1024;
    const porcentaje = (totalSize / maxSize) * 100;
    const progressBar = document.getElementById('size-progress-bar');
    const totalSizeSpan = document.getElementById('total-size');
    const totalImagenesSpan = document.getElementById('total-imagenes');
    const limiteMsg = document.getElementById('limiteArchivosMsg');
    
    if (totalSizeSpan) {
        if (totalSize < 1024) totalSizeSpan.textContent = totalSize + ' B';
        else if (totalSize < 1024 * 1024) totalSizeSpan.textContent = (totalSize / 1024).toFixed(2) + ' KB';
        else totalSizeSpan.textContent = (totalSize / (1024 * 1024)).toFixed(2) + ' MB';
    }
    
    if (progressBar) {
        progressBar.style.width = Math.min(porcentaje, 100) + '%';
        if (porcentaje > 90) {
            progressBar.classList.remove('bg-success');
            progressBar.classList.add('bg-danger');
        } else if (porcentaje > 70) {
            progressBar.classList.remove('bg-success', 'bg-danger');
            progressBar.classList.add('bg-warning');
        } else {
            progressBar.classList.remove('bg-warning', 'bg-danger');
            progressBar.classList.add('bg-success');
        }
    }
    
    if (totalImagenesSpan) {
        let total = (imagenPrincipalFile ? 1 : 0) + (gifFile ? 1 : 0) + selectedImages.length;
        const imagenesActualesCount = {{ $totalImagenesActuales }};
        total += imagenesActualesCount - imagenesAEliminar.length;
        totalImagenesSpan.textContent = total;
    }
    
    if (totalSize > maxSize) {
        limiteExcedido = true;
        if (limiteMsg) limiteMsg.style.display = 'block';
        document.getElementById('btnSubmit').disabled = true;
    } else {
        limiteExcedido = false;
        if (limiteMsg) limiteMsg.style.display = 'none';
        document.getElementById('btnSubmit').disabled = false;
    }
}

function previewImagenPrincipal(input) {
    const previewContainer = document.getElementById('preview_principal_container');
    const previewImg = document.getElementById('preview_principal_img');
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        if (!['image/jpeg', 'image/jpg', 'image/png'].includes(file.type)) {
            Swal.fire({ icon: 'error', title: 'Formato no válido', text: 'La imagen principal solo acepta formatos JPG, JPEG y PNG' });
            input.value = '';
            return;
        }
        if (file.size > 5 * 1024 * 1024) {
            Swal.fire({ icon: 'error', title: 'Archivo demasiado grande', text: 'La imagen principal no puede exceder los 5MB' });
            input.value = '';
            return;
        }
        imagenPrincipalFile = file;
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewContainer.style.display = 'block';
            actualizarBarraProgresoTamaño();
        };
        reader.readAsDataURL(file);
    } else {
        previewContainer.style.display = 'none';
        imagenPrincipalFile = null;
        actualizarBarraProgresoTamaño();
    }
}

function cancelarImagenPrincipal() {
    document.getElementById('imagen_principal').value = '';
    document.getElementById('preview_principal_container').style.display = 'none';
    imagenPrincipalFile = null;
    actualizarBarraProgresoTamaño();
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
            actualizarBarraProgresoTamaño();
            Swal.fire({ title: 'Imagen marcada para eliminar', text: 'La imagen será eliminada al guardar los cambios.', icon: 'info', timer: 2000, showConfirmButton: false });
        }
    });
}

function previewGif(input) {
    const previewContainer = document.getElementById('preview_gif_container');
    const previewImg = document.getElementById('preview_gif');
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        if (file.type !== 'image/gif') {
            Swal.fire({ icon: 'error', title: 'Formato no válido', text: 'El campo GIF solo acepta archivos con formato GIF' });
            input.value = '';
            return;
        }
        if (file.size > 10 * 1024 * 1024) {
            Swal.fire({ icon: 'error', title: 'Archivo demasiado grande', text: 'El GIF no puede exceder los 10MB' });
            input.value = '';
            return;
        }
        gifFile = file;
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewContainer.style.display = 'block';
            actualizarBarraProgresoTamaño();
        };
        reader.readAsDataURL(file);
    } else {
        previewContainer.style.display = 'none';
        gifFile = null;
        actualizarBarraProgresoTamaño();
    }
}

function cancelarGif() {
    document.getElementById('gif').value = '';
    document.getElementById('preview_gif_container').style.display = 'none';
    gifFile = null;
    actualizarBarraProgresoTamaño();
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
            actualizarBarraProgresoTamaño();
            Swal.fire({ title: 'GIF marcado para eliminar', text: 'El GIF será eliminado al guardar los cambios.', icon: 'info', timer: 2000, showConfirmButton: false });
        }
    });
}

// Función para eliminar imagen adicional existente (eliminación directa, sin marcado)
function eliminarImagenAdicionalExistente(event, btn, imagenId, nombreArchivo) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    Swal.fire({
        title: '¿Eliminar imagen?',
        text: 'Esta acción eliminará esta imagen adicional de la variación.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const container = btn.closest('.existing-image-item');
            
            // Agregar el ID a la lista de imágenes a eliminar
            if (!imagenesAEliminar.some(item => item.id == imagenId)) {
                imagenesAEliminar.push({ id: parseInt(imagenId), filename: nombreArchivo });
            }
            
            // Actualizar el input oculto
            document.getElementById('imagenes_a_eliminar').value = JSON.stringify(imagenesAEliminar);
            
            // Ocultar la imagen
            if (container) {
                container.classList.add('eliminada');
                container.style.display = 'none';
            }
            
            // Actualizar contadores
            actualizarBarraProgresoTamaño();
            
            Swal.fire({
                title: '¡Imagen marcada para eliminar!',
                text: 'La imagen será eliminada al guardar los cambios.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        }
    });
}

function handleImageSelection(event) {
    const files = event.target.files;
    if (!files || files.length === 0) {
        event.target.value = '';
        return;
    }
    
    const maxFiles = 7;
    const currentCount = selectedImages.length;
    const existingCount = {{ $totalImagenesActuales }};
    const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    const tamanioActual = calcularTamañoTotal();
    let nuevoTamanio = tamanioActual;
    for (let i = 0; i < files.length; i++) nuevoTamanio += files[i].size;
    
    if (nuevoTamanio > maxTotalSize) {
        Swal.fire({
            icon: 'warning',
            title: 'Límite de tamaño excedido',
            html: `<div class="text-center"><p>Si agregas estos archivos, excederás el límite de 50MB.</p><p class="mb-0"><strong>Tamaño actual:</strong> ${(tamanioActual / (1024 * 1024)).toFixed(2)}MB</p><p><strong>Tamaño con nuevos archivos:</strong> ${(nuevoTamanio / (1024 * 1024)).toFixed(2)}MB</p></div>`,
            confirmButtonText: 'Entendido'
        });
        event.target.value = '';
        return;
    }
    
    const imagenesNoEliminadas = existingCount - imagenesAEliminar.length;
    if (imagenesNoEliminadas + currentCount + files.length > 7) {
        Swal.fire({ icon: 'warning', title: 'Límite de imágenes', text: `No puedes tener más de 7 imágenes adicionales en total.` });
        event.target.value = '';
        return;
    }
    
    let archivosAgregados = 0;
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        if (!validTypes.includes(file.type)) continue;
        if (file.size > 5 * 1024 * 1024) continue;
        if (selectedImages.some(img => img.file.name === file.name && img.file.size === file.size)) continue;
        
        const imageId = 'img_' + Date.now() + '_' + imageCounter++;
        const preview = URL.createObjectURL(file);
        selectedImages.push({ id: imageId, file: file, preview: preview, name: file.name, size: file.size });
        archivosAgregados++;
    }
    
    if (archivosAgregados > 0) {
        const totalImagenesActuales = {{ $totalImagenesActuales }};
        document.getElementById('new-images-count').textContent = selectedImages.length;
        document.getElementById('selected-images-count').innerHTML = `${totalImagenesActuales} imágenes actuales + <span id="new-images-count">${selectedImages.length}</span> nuevas`;
        renderSelectedImages();
        actualizarBarraProgresoTamaño();
        if (selectedImages.length > 0) document.getElementById('btn-limpiar-imagenes').style.display = 'inline-block';
        Swal.fire({ icon: 'success', title: 'Imágenes agregadas', text: `Se agregaron ${archivosAgregados} imagen(es) correctamente.`, timer: 2000, showConfirmButton: false });
    }
    event.target.value = '';
}

function removeSelectedImage(imageId) {
    Swal.fire({
        title: '¿Eliminar imagen?',
        text: 'Esta acción eliminará la imagen de la selección actual.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const image = selectedImages.find(img => img.id === imageId);
            if (image && image.preview) URL.revokeObjectURL(image.preview);
            selectedImages = selectedImages.filter(img => img.id !== imageId);
            const totalImagenesActuales = {{ $totalImagenesActuales }};
            document.getElementById('new-images-count').textContent = selectedImages.length;
            document.getElementById('selected-images-count').innerHTML = `${totalImagenesActuales} imágenes actuales + <span id="new-images-count">${selectedImages.length}</span> nuevas`;
            renderSelectedImages();
            actualizarBarraProgresoTamaño();
            if (selectedImages.length === 0) document.getElementById('btn-limpiar-imagenes').style.display = 'none';
            Swal.fire({ icon: 'success', title: 'Imagen eliminada', text: 'La imagen se ha eliminado de la selección.', timer: 1500, showConfirmButton: false });
        }
    });
}

function renderSelectedImages() {
    const container = document.getElementById('selected-images-container');
    const noMsg = document.getElementById('no-imagenes-msg');
    const btnLimpiar = document.getElementById('btn-limpiar-imagenes');
    if (!container) return;
    container.innerHTML = '';
    
    if (selectedImages.length === 0) {
        if (noMsg) { noMsg.style.display = 'block'; noMsg.innerHTML = '<i class="fas fa-info-circle me-1"></i><small>No hay nuevas imágenes seleccionadas</small>'; }
        if (btnLimpiar) btnLimpiar.style.display = 'none';
        return;
    }
    
    if (noMsg) noMsg.style.display = 'none';
    if (btnLimpiar) btnLimpiar.style.display = 'inline-block';
    
    selectedImages.forEach((image, index) => {
        const col = document.createElement('div');
        col.className = 'col-6 col-md-4 mb-2';
        const card = document.createElement('div');
        card.className = 'card border image-preview-card position-relative';
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'btn btn-danger btn-sm position-absolute top-0 end-0 m-1 remove-btn';
        btn.style.cssText = 'width: 24px; height: 24px; padding: 0; border-radius: 50%; z-index: 10;';
        btn.onclick = function(e) { e.preventDefault(); e.stopPropagation(); removeSelectedImage(image.id); };
        const btnIcon = document.createElement('i');
        btnIcon.className = 'fas fa-times';
        btn.appendChild(btnIcon);
        const img = document.createElement('img');
        img.src = image.preview;
        img.className = 'card-img-top';
        img.style.cssText = 'height: 80px; object-fit: contain; background: #f8f9fa; padding: 4px;';
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
        if (image.file.size < 1024) sizeText = image.file.size + ' B';
        else if (image.file.size < 1024 * 1024) sizeText = (image.file.size / 1024).toFixed(2) + ' KB';
        else sizeText = (image.file.size / (1024 * 1024)).toFixed(2) + ' MB';
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

function limpiarTodasLasImagenes() {
    if (selectedImages.length === 0) return;
    Swal.fire({
        title: '¿Limpiar todas las imágenes nuevas?',
        text: 'Esta acción eliminará todas las imágenes adicionales recién seleccionadas.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, limpiar todo',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            selectedImages.forEach(img => { if (img.preview) URL.revokeObjectURL(img.preview); });
            selectedImages = [];
            const totalImagenesActuales = {{ $totalImagenesActuales }};
            document.getElementById('new-images-count').textContent = '0';
            document.getElementById('selected-images-count').innerHTML = `${totalImagenesActuales} imágenes actuales + <span id="new-images-count">0</span> nuevas`;
            renderSelectedImages();
            actualizarBarraProgresoTamaño();
            document.getElementById('btn-limpiar-imagenes').style.display = 'none';
            Swal.fire({ icon: 'success', title: 'Imágenes limpiadas', text: 'Todas las imágenes nuevas han sido eliminadas.', timer: 1500, showConfirmButton: false });
        }
    });
}

// ==================== FUNCIÓN PARA ENVÍO DEL FORMULARIO ====================

function prepararFormularioParaEnvio(event) {
    event.preventDefault();
    
    const skuInput = document.getElementById('vSKU');
    if (!verificarSKUVariacionLocal(skuInput)) {
        Swal.fire({ icon: 'error', title: 'Error de validación', text: 'El SKU ya está registrado en otro producto o variación.' });
        return false;
    }
    
    const precioInput = document.getElementById('dPrecio');
    const stockInput = document.getElementById('iStock');
    if (!precioInput.value.trim()) { Swal.fire({ icon: 'error', title: 'Error', text: 'El precio de venta es obligatorio.' }); precioInput.classList.add('is-invalid'); return false; }
    if (!stockInput.value.trim()) { Swal.fire({ icon: 'error', title: 'Error', text: 'El stock es obligatorio.' }); stockInput.classList.add('is-invalid'); return false; }
    
    const atributosRadios = document.querySelectorAll('.atributo-radio');
    const atributosPorGrupo = {};
    atributosRadios.forEach(radio => { if (radio.checked) atributosPorGrupo[radio.dataset.atributoId] = true; });
    const gruposAtributos = new Set();
    atributosRadios.forEach(radio => gruposAtributos.add(radio.dataset.atributoId));
    let atributosFaltantes = [];
    gruposAtributos.forEach(atributoId => {
        if (!atributosPorGrupo[atributoId]) {
            const container = document.querySelector(`.atributo-container input[data-atributo-id="${atributoId}"]`).closest('.atributo-container');
            const nombreAtributo = container?.querySelector('.fw-bold')?.innerText || 'Atributo';
            atributosFaltantes.push(nombreAtributo);
            container?.classList.add('border-danger');
        }
    });
    if (atributosFaltantes.length > 0) {
        Swal.fire({ icon: 'error', title: 'Error', html: `Debes seleccionar un valor para los siguientes atributos:<br><br>${atributosFaltantes.join(', ')}` });
        return false;
    }
    
    const tieneDescuento = document.getElementById('bTiene_descuento');
    if (tieneDescuento && tieneDescuento.checked) {
        if (!validarPrecioDescuento()) { Swal.fire({ icon: 'error', title: 'Error', text: 'El precio de descuento debe ser menor que el precio de venta.' }); return false; }
        if (!validarFechasDescuento()) { Swal.fire({ icon: 'error', title: 'Error', text: 'La fecha de fin no puede ser anterior a la fecha de inicio.' }); return false; }
    }
    
    const form = document.getElementById('variacionForm');
    const formData = new FormData(form);
    formData.append('_method', 'PUT');
    
    const fileInputs = form.querySelectorAll('input[type="file"][name^="imagenes_adicionales"]');
    fileInputs.forEach(input => formData.delete(input.name));
    
    selectedImages.forEach((image, index) => formData.append(`imagenes_adicionales[${index}]`, image.file));
    if (imagenPrincipalFile) formData.set('imagen_principal', imagenPrincipalFile);
    if (gifFile) formData.set('gif', gifFile);
    if (imagenesAEliminar.length > 0) formData.set('imagenes_a_eliminar', JSON.stringify(imagenesAEliminar));
    
    Swal.fire({ title: 'Actualizando variación...', text: 'Por favor espera', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
    
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
            return response.json().then(err => { throw err; });
        }
    })
    .then(data => {
        Swal.close();
        if (data && data.redirect) {
            window.location.href = data.redirect;
        } else if (data && data.success) {
            window.location.href = '{{ route("variaciones.show", $producto->id_producto) }}';
        } else {
            window.location.href = '{{ route("variaciones.show", $producto->id_producto) }}';
        }
    })
    .catch(error => {
        Swal.close();
        console.error('Error:', error);
        let errorMessage = 'Hubo un problema al actualizar la variación.';
        if (error.errors) {
            errorMessage = Object.values(error.errors).flat().join('<br>');
        } else if (error.message) {
            errorMessage = error.message;
        }
        Swal.fire({ icon: 'error', title: 'Error', html: errorMessage });
    });
    
    return false;
}

// ==================== INICIALIZACIÓN ====================

document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('bTiene_descuento') && document.getElementById('bTiene_descuento').checked) toggleDescuentoFields();
    actualizarPrecioFinal();
    document.getElementById('dPrecio').addEventListener('input', actualizarPrecioFinal);
    document.getElementById('id_impuesto').addEventListener('change', actualizarPrecioFinal);
    
    document.querySelectorAll('.atributo-radio').forEach(radio => {
        radio.addEventListener('change', function() {
            const container = this.closest('.atributo-container');
            if (container) {
                container.classList.remove('border-danger');
                const errorExistente = container.querySelector('.error-atributo');
                if (errorExistente) errorExistente.remove();
            }
        });
    });
    
    document.querySelectorAll('input, select, textarea').forEach(element => element.setAttribute('autocomplete', 'off'));
    
    const form = document.getElementById('variacionForm');
    if (form) form.addEventListener('submit', prepararFormularioParaEnvio);
    
    actualizarBarraProgresoTamaño();
});

@if(session('success'))
Swal.fire({ title: "¡Actualizado!", text: "{{ session('success') }}", icon: "success", timer: 3000, showConfirmButton: false });
@endif

@if(session('error') || $errors->any())
@php
    $errorMessage = session('error');
    if (!$errorMessage && $errors->any()) $errorMessage = 'Por favor corrige los errores en el formulario.';
@endphp
Swal.fire({ icon: "error", title: "Oops...", text: "{{ $errorMessage }}", draggable: true });
@endif
</script>
@endpush

@endsection