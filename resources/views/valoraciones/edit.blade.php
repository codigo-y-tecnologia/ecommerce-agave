@extends('layouts.app')

@section('title', 'Editar Valoración - ' . $producto->vNombre)
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1><i class="fas fa-edit me-2"></i>Editar Valoración</h1>
            <p class="text-muted">Producto: {{ $producto->vNombre }}</p>
        </div>
        <div>
            <a href="{{ route('valoraciones.show', $producto->id_producto) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Cancelar
            </a>
        </div>
    </div>

    <form action="{{ route('valoraciones.update', ['producto_id' => $producto->id_producto, 'variacion_id' => $variacion->id_variacion]) }}" 
          method="POST" enctype="multipart/form-data" id="valoracionForm" class="guardar-cambios-form">
        @csrf
        @method('PUT')

        <!-- Campo oculto para controlar la imagen -->
        <input type="hidden" name="mantener_imagen" id="mantener_imagen_hidden" value="1">

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-cubes me-2"></i>Información de la Valoración</h5>
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
                                           value="{{ old('vSKU', $variacion->vSKU) }}" required
                                           maxlength="50"
                                           oninput="validarSKU(this)"
                                           placeholder="Ej: MEZ-750ML-REP-01"
                                           autocomplete="off">
                                    @error('vSKU')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Máximo 50 caracteres</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="dPrecio" class="form-label fw-bold">
                                        Precio <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="text" name="dPrecio" id="dPrecio" 
                                               class="form-control @error('dPrecio') is-invalid @enderror"
                                               value="{{ old('dPrecio', $variacion->dPrecio) }}" 
                                               required 
                                               oninput="validarPrecio(this)"
                                               placeholder="0.00"
                                               title="Máximo: 9,999,999.99"
                                               autocomplete="off">
                                    </div>
                                    @error('dPrecio')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Máximo: 9,999,999.99 (7 dígitos enteros)
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- SECCIÓN: OFERTA ESPECIAL CORREGIDA -->
                        <div class="card mb-4 border">
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
                                        <input type="checkbox" name="bTiene_oferta" id="bTiene_oferta" 
                                               class="form-check-input"
                                               onchange="toggleOfertaForm()"
                                               value="1"
                                               {{ (old('bTiene_oferta', $variacion->bTiene_oferta) == 1) ? 'checked' : '' }}
                                               autocomplete="off">
                                        <label for="bTiene_oferta" class="form-check-label fw-bold">
                                            Activar oferta especial
                                        </label>
                                    </div>
                                </div>
                                
                                <div id="oferta-form" style="display: {{ (old('bTiene_oferta', $variacion->bTiene_oferta) == 1) ? 'block' : 'none' }};">
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
                                                           value="{{ old('dPrecio_oferta', $variacion->dPrecio_oferta) }}" 
                                                           oninput="validarPrecioOferta()"
                                                           placeholder="0.00"
                                                           title="Precio de oferta especial (debe ser menor que el precio de venta)"
                                                           autocomplete="off">
                                                    @error('dPrecio_oferta')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
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
                                                       value="{{ old('vMotivo_oferta', $variacion->vMotivo_oferta) }}" 
                                                       maxlength="255"
                                                       placeholder="Ej: Temporada navideña, Liquidación, etc."
                                                       autocomplete="off">
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
                                                       value="{{ old('dFecha_inicio_oferta', $variacion->dFecha_inicio_oferta ? \Carbon\Carbon::parse($variacion->dFecha_inicio_oferta)->format('Y-m-d') : '') }}"
                                                       autocomplete="off">
                                                @error('dFecha_inicio_oferta')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
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
                                                       value="{{ old('dFecha_fin_oferta', $variacion->dFecha_fin_oferta ? \Carbon\Carbon::parse($variacion->dFecha_fin_oferta)->format('Y-m-d') : '') }}"
                                                       autocomplete="off">
                                                @error('dFecha_fin_oferta')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
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

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="iStock" class="form-label fw-bold">
                                        Stock <span class="text-danger">*</span>
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
                                    <small class="form-text text-muted">Máximo 6 dígitos (0-999999)</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="vClase_envio" class="form-label fw-bold">
                                        Clase de Envío
                                    </label>
                                    <select name="vClase_envio" id="vClase_envio" 
                                            class="form-control @error('vClase_envio') is-invalid @enderror"
                                            autocomplete="off">
                                        <option value="">Igual que el producto padre</option>
                                        <option value="Estandar" {{ old('vClase_envio', $variacion->vClase_envio) == 'Estandar' ? 'selected' : '' }}>Estándar</option>
                                        <option value="Fragil" {{ old('vClase_envio', $variacion->vClase_envio) == 'Fragil' ? 'selected' : '' }}>Frágil</option>
                                        <option value="Pesado" {{ old('vClase_envio', $variacion->vClase_envio) == 'Pesado' ? 'selected' : '' }}>Pesado</option>
                                        <option value="Otro" {{ old('vClase_envio', $variacion->vClase_envio) == 'Otro' ? 'selected' : '' }}>Otro</option>
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
                                        @if($variacion->vClase_envio && $variacion->vClase_envio != $producto->vClase_envio)
                                            <br>Actual: <strong>{{ $variacion->vClase_envio }}</strong>
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="dPeso" class="form-label fw-bold">
                                        Peso (kg)
                                    </label>
                                    <div class="input-group">
                                        <input type="text" 
                                               name="dPeso" 
                                               id="dPeso" 
                                               class="form-control @error('dPeso') is-invalid @enderror"
                                               value="{{ old('dPeso', $variacion->dPeso) }}" 
                                               oninput="validarPeso(this)"
                                               placeholder="0.000"
                                               title="Peso en kilogramos (ej: 1.250)"
                                               autocomplete="off">
                                        <span class="input-group-text">kg</span>
                                    </div>
                                    @error('dPeso')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Ej: 1.250 (máximo 1000.000 kg)</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <div class="form-check form-switch mt-4">
                                        <input type="checkbox" name="bActivo" id="bActivo" 
                                               class="form-check-input" value="1" 
                                               {{ old('bActivo', $variacion->bActivo) ? 'checked' : '' }}
                                               autocomplete="off">
                                        <label for="bActivo" class="form-check-label fw-bold">
                                            Valoración activa
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Si está desactivada, no estará disponible para venta
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- SECCIÓN: DIMENSIONES -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="border rounded p-3 mb-4">
                                    <h6 class="fw-bold mb-3"><i class="fas fa-ruler-combined me-2"></i>Dimensiones del Producto</h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group mb-3">
                                                <label for="dLargo_cm" class="form-label fw-bold">
                                                    Largo (cm)
                                                </label>
                                                <div class="input-group">
                                                    <input type="text" 
                                                           name="dLargo_cm" 
                                                           id="dLargo_cm" 
                                                           class="form-control @error('dLargo_cm') is-invalid @enderror"
                                                           value="{{ old('dLargo_cm', $variacion->dLargo_cm) }}" 
                                                           oninput="validarDimension(this)"
                                                           placeholder="0.00"
                                                           title="Largo en centímetros"
                                                           autocomplete="off">
                                                    <span class="input-group-text">cm</span>
                                                </div>
                                                @error('dLargo_cm')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">Ej: 30.50 cm (máx 500.00)</small>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="form-group mb-3">
                                                <label for="dAncho_cm" class="form-label fw-bold">
                                                    Ancho (cm)
                                                </label>
                                                <div class="input-group">
                                                    <input type="text" 
                                                           name="dAncho_cm" 
                                                           id="dAncho_cm" 
                                                           class="form-control @error('dAncho_cm') is-invalid @enderror"
                                                           value="{{ old('dAncho_cm', $variacion->dAncho_cm) }}" 
                                                           oninput="validarDimension(this)"
                                                           placeholder="0.00"
                                                           title="Ancho en centímetros"
                                                           autocomplete="off">
                                                    <span class="input-group-text">cm</span>
                                                </div>
                                                @error('dAncho_cm')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">Ej: 15.20 cm (máx 500.00)</small>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="form-group mb-3">
                                                <label for="dAlto_cm" class="form-label fw-bold">
                                                    Alto (cm)
                                                </label>
                                                <div class="input-group">
                                                    <input type="text" 
                                                           name="dAlto_cm" 
                                                           id="dAlto_cm" 
                                                           class="form-control @error('dAlto_cm') is-invalid @enderror"
                                                           value="{{ old('dAlto_cm', $variacion->dAlto_cm) }}" 
                                                           oninput="validarDimension(this)"
                                                           placeholder="0.00"
                                                           title="Alto en centímetros"
                                                           autocomplete="off">
                                                    <span class="input-group-text">cm</span>
                                                </div>
                                                @error('dAlto_cm')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">Ej: 45.00 cm (máx 500.00)</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert alert-info small mt-2">
                                        <i class="fas fa-info-circle me-1"></i>
                                        <strong>Nota:</strong> Las dimensiones se utilizan para calcular el costo de envío.
                                        <div id="volumen-info" class="mt-1"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">
                                        Imagen de la Valoración
                                    </label>
                                    
                                    <input type="file" name="imagen" id="imagen" 
                                        class="form-control @error('imagen') is-invalid @enderror d-none"
                                        accept=".jpg,.jpeg,.png,.gif,.webp,.bmp,.svg"
                                        onchange="handleImageSelection(event)"
                                        autocomplete="off">
                                    
                                    <!-- Botón personalizado para seleccionar imagen -->
                                    <button type="button" class="btn btn-outline-primary mb-2" onclick="abrirSelectorImagen()">
                                        <i class="fas fa-folder-open me-1"></i> Seleccionar Imagen
                                    </button>
                                    
                                    @error('imagen')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Imagen específica para esta valoración (opcional, máximo 5MB)
                                    </small>
                                    
                                    <!-- Vista previa de la imagen actual -->
                                    @if($variacion->vImagen)
                                        <div class="mt-2" id="current-image-container">
                                            <img src="{{ asset($variacion->vImagen) }}" 
                                                alt="Imagen actual"
                                                style="max-width: 150px; max-height: 150px; object-fit: cover; border-radius: 4px; margin-top: 10px;"
                                                id="current-image-preview">
                                            <div class="form-check mt-2">
                                                <input type="checkbox" name="mantener_imagen" id="mantener_imagen" 
                                                    class="form-check-input" value="1" checked
                                                    onchange="toggleMantenerImagen()">
                                                <label for="mantener_imagen" class="form-check-label">
                                                    <i class="fas fa-check-circle text-success me-1"></i> Mantener imagen actual
                                                </label>
                                            </div>
                                        </div>
                                    @else
                                        <div class="mt-2 text-muted" id="no-image-message">
                                            <i class="fas fa-image me-1"></i> No hay imagen asignada a esta valoración
                                        </div>
                                    @endif
                                    
                                    <!-- Contenedor para vista previa de nueva imagen -->
                                    <div id="nueva-imagen-container" class="mt-2"></div>
                                    
                                    <!-- Botón para eliminar imagen (solo si hay imagen actual) -->
                                    @if($variacion->vImagen)
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-danger" id="eliminar-imagen-btn">
                                                <i class="fas fa-trash-alt me-1"></i> Eliminar imagen actual
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="tDescripcion" class="form-label fw-bold">
                                        Descripción (Opcional)
                                    </label>
                                    <textarea name="tDescripcion" id="tDescripcion" 
                                              class="form-control @error('tDescripcion') is-invalid @enderror"
                                              rows="3" placeholder="Descripción específica de esta valoración"
                                              autocomplete="off">{{ old('tDescripcion', $variacion->tDescripcion) }}</textarea>
                                    @error('tDescripcion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-tags me-2"></i>Atributos de la Valoración</h5>
                    </div>
                    <div class="card-body">
                        @if(count($atributos) > 0)
                            @php
                                $atributosSeleccionados = [];
                                foreach ($variacion->atributos as $atributo) {
                                    $atributosSeleccionados[$atributo->id_atributo] = $atributo->id_atributo_valor;
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
                                                       {{ isset($atributosSeleccionados[$valor->atributo->id_atributo]) && 
                                                          $atributosSeleccionados[$valor->atributo->id_atributo] == $valor->id_atributo_valor ? 'checked' : '' }}
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
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Este producto no tiene atributos asignados.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary btn-lg guardar-btn">
                <i class="fas fa-save me-2"></i> Actualizar Valoración
            </button>
            <a href="{{ route('valoraciones.show', $producto->id_producto) }}" class="btn btn-secondary btn-lg">
                <i class="fas fa-times me-2"></i> Cancelar
            </a>
        </div>
    </form>
</div>

@push('styles')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@push('scripts')
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Variables globales para manejar la imagen
let imagenSeleccionada = null;
let imagenPreviewUrl = null;
let imagenEliminada = false;

// ==================== VALIDACIONES ====================

// Validar SKU (letras, números, guiones)
function validarSKU(input) {
    // Permitir letras, números y guiones
    input.value = input.value.replace(/[^A-Za-z0-9\-]/g, '');
    
    // Limitar a 50 caracteres
    if (input.value.length > 50) {
        input.value = input.value.substring(0, 50);
    }
    
    // Convertir a mayúsculas automáticamente
    input.value = input.value.toUpperCase();
    
    input.classList.remove('is-invalid');
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
    
    // Si es precio normal, validar precio de oferta
    if (input.id === 'dPrecio') {
        validarPrecioContraPrecioVenta();
    }
}

// Validar precio de oferta
function validarPrecioOferta() {
    const precioVentaInput = document.getElementById('dPrecio');
    const precioOfertaInput = document.getElementById('dPrecio_oferta');
    const tieneOfertaCheckbox = document.getElementById('bTiene_oferta');
    
    if (!precioVentaInput || !precioOfertaInput || !tieneOfertaCheckbox) {
        return;
    }
    
    // Solo validar si la oferta está activada
    if (!tieneOfertaCheckbox.checked) {
        limpiarErrorOferta();
        return;
    }
    
    const precioVenta = parseFloat(precioVentaInput.value) || 0;
    const precioOferta = parseFloat(precioOfertaInput.value) || 0;
    
    // Limpiar error anterior
    limpiarErrorOferta();
    
    // Si está vacío pero la oferta está activada
    if (precioOfertaInput.value.trim() === '') {
        mostrarErrorOferta('El precio de oferta es obligatorio cuando se activa la oferta');
        return;
    }
    
    // Validar que sea menor que el precio de venta
    if (precioOferta > 0 && precioOferta >= precioVenta) {
        mostrarErrorOferta('El precio de oferta debe ser menor que el precio de venta');
    }
}

// Función para mostrar error de precio
function mostrarErrorPrecio(input, mensaje) {
    // Remover error anterior si existe
    const errorId = `error-${input.id}-limite`;
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

// Función para limpiar error de precio
function limpiarErrorPrecio(input) {
    const errorId = `error-${input.id}-limite`;
    const errorElement = document.getElementById(errorId);
    if (errorElement) {
        errorElement.remove();
    }
    input.classList.remove('is-invalid');
}

// Función para validar que el precio de oferta sea menor que el precio normal
function validarPrecioContraPrecioVenta() {
    const precioVentaInput = document.getElementById('dPrecio');
    const precioOfertaInput = document.getElementById('dPrecio_oferta');
    const tieneOfertaCheckbox = document.getElementById('bTiene_oferta');
    
    if (!precioVentaInput || !precioOfertaInput || !tieneOfertaCheckbox) {
        return;
    }
    
    // Solo validar si la oferta está activada
    if (!tieneOfertaCheckbox.checked) {
        limpiarErrorOferta();
        return;
    }
    
    const precioVenta = parseFloat(precioVentaInput.value) || 0;
    const precioOferta = parseFloat(precioOfertaInput.value) || 0;
    
    // Limpiar error anterior
    limpiarErrorOferta();
    
    // Validar que sea menor que el precio de venta
    if (precioOferta > 0 && precioOferta >= precioVenta) {
        mostrarErrorOferta('El precio de oferta debe ser menor que el precio de venta');
    }
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

// Validar stock (máximo 6 dígitos)
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

// Validar peso (kg con 3 decimales)
function validarPeso(input) {
    let value = input.value;
    const cursorPos = input.selectionStart;
    
    if (value === '') {
        limpiarErrorDimension(input);
        calcularVolumen();
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
    
    // Limitar longitud total a 10 caracteres
    if (value.length > 10) {
        value = value.substring(0, 10);
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
    
    // Validar que no exceda 1000 kg
    if (value) {
        const numero = parseFloat(value);
        if (!isNaN(numero) && numero > 1000) {
            input.classList.add('is-invalid');
            mostrarErrorDimension(input, 'El peso máximo es 1000 kg');
        }
    }
    
    calcularVolumen();
}

// Validar dimensiones (cm con 2 decimales)
function validarDimension(input) {
    let value = input.value;
    const cursorPos = input.selectionStart;
    
    if (value === '') {
        limpiarErrorDimension(input);
        calcularVolumen();
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
    
    // Limitar longitud total a 10 caracteres
    if (value.length > 10) {
        value = value.substring(0, 10);
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
    
    // Validar que no exceda 500 cm
    if (value) {
        const numero = parseFloat(value);
        if (!isNaN(numero) && numero > 500) {
            input.classList.add('is-invalid');
            mostrarErrorDimension(input, 'La dimensión máxima es 500 cm');
        }
    }
    
    calcularVolumen();
}

// Función para mostrar error de dimensiones
function mostrarErrorDimension(input, mensaje) {
    const errorElement = document.getElementById(`error-${input.id}`);
    if (errorElement) {
        errorElement.textContent = mensaje;
    } else {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback d-block';
        errorDiv.id = `error-${input.id}`;
        errorDiv.textContent = mensaje;
        input.parentNode.appendChild(errorDiv);
    }
}

// Función para limpiar error de dimensiones
function limpiarErrorDimension(input) {
    const errorElement = document.getElementById(`error-${input.id}`);
    if (errorElement) {
        errorElement.textContent = '';
    }
    input.classList.remove('is-invalid');
}

// ==================== FUNCIONES PARA OFERTA ====================

// Mostrar/ocultar formulario de oferta
function toggleOfertaForm() {
    const ofertaCheckbox = document.getElementById('bTiene_oferta');
    const ofertaForm = document.getElementById('oferta-form');
    
    if (ofertaCheckbox.checked) {
        ofertaForm.style.display = 'block';
        // Validar precio de oferta al activar
        setTimeout(() => {
            validarPrecioContraPrecioVenta();
        }, 100);
    } else {
        ofertaForm.style.display = 'none';
        // Limpiar errores al desactivar
        limpiarErrorOferta();
    }
}

// ==================== FUNCIONES PARA IMAGEN ====================

// Abrir selector de imagen
function abrirSelectorImagen() {
    document.getElementById('imagen').click();
}

// Manejar selección de imagen
function handleImageSelection(event) {
    const file = event.target.files[0];
    const container = document.getElementById('nueva-imagen-container');
    const mantenerImagenCheckbox = document.getElementById('mantener_imagen');
    const mantenerImagenHidden = document.getElementById('mantener_imagen_hidden');
    
    if (file) {
        // Validar tamaño máximo (5MB)
        const maxSize = 5 * 1024 * 1024; // 5MB en bytes
        if (file.size > maxSize) {
            Swal.fire({
                icon: 'error',
                title: 'Archivo demasiado grande',
                text: 'La imagen no debe pesar más de 5MB',
                position: "center"
            });
            event.target.value = '';
            return;
        }
        
        // Guardar la imagen seleccionada
        imagenSeleccionada = file;
        
        // Crear URL para vista previa
        if (imagenPreviewUrl) {
            URL.revokeObjectURL(imagenPreviewUrl);
        }
        imagenPreviewUrl = URL.createObjectURL(file);
        
        // Desmarcar checkbox de mantener imagen
        if (mantenerImagenCheckbox) {
            mantenerImagenCheckbox.checked = false;
            mantenerImagenHidden.value = '0';
        }
        
        // Ocultar imagen actual si existe
        const currentImageContainer = document.getElementById('current-image-container');
        const noImageMessage = document.getElementById('no-image-message');
        if (currentImageContainer) currentImageContainer.style.display = 'none';
        if (noImageMessage) noImageMessage.style.display = 'none';
        
        // Mostrar vista previa de nueva imagen
        container.innerHTML = `
            <div class="card border position-relative" style="max-width: 200px;">
                <button type="button" 
                        class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1"
                        onclick="cancelarNuevaImagen()"
                        style="width: 28px; height: 28px; padding: 0; border-radius: 50%; z-index: 10;">
                    <i class="fas fa-times"></i>
                </button>
                <img src="${imagenPreviewUrl}" 
                     class="card-img-top" 
                     style="height: 120px; object-fit: contain; background: #f8f9fa; padding: 8px;"
                     alt="Nueva imagen seleccionada">
                <div class="card-body p-2 text-center">
                    <small class="text-muted d-block" style="font-size: 11px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                        ${file.name.length > 15 ? file.name.substring(0, 15) + '...' : file.name}
                    </small>
                    <small class="text-muted d-block">
                        ${(file.size / 1024).toFixed(2)} KB
                    </small>
                </div>
            </div>
        `;
    }
}

// Cancelar nueva imagen y restaurar imagen actual
function cancelarNuevaImagen() {
    const imagenInput = document.getElementById('imagen');
    const nuevaImagenContainer = document.getElementById('nueva-imagen-container');
    const mantenerImagenCheckbox = document.getElementById('mantener_imagen');
    const mantenerImagenHidden = document.getElementById('mantener_imagen_hidden');
    const currentImageContainer = document.getElementById('current-image-container');
    const noImageMessage = document.getElementById('no-image-message');
    
    // Limpiar el input de archivo
    imagenInput.value = '';
    
    // Liberar la URL de la vista previa
    if (imagenPreviewUrl) {
        URL.revokeObjectURL(imagenPreviewUrl);
        imagenPreviewUrl = null;
    }
    
    // Limpiar la imagen seleccionada
    imagenSeleccionada = null;
    
    // Limpiar contenedor de nueva imagen
    nuevaImagenContainer.innerHTML = '';
    
    // Si hay imagen actual, mostrarla y marcar checkbox
    if (currentImageContainer) {
        currentImageContainer.style.display = 'block';
        if (mantenerImagenCheckbox) {
            mantenerImagenCheckbox.checked = true;
            mantenerImagenHidden.value = '1';
        }
    } else if (noImageMessage) {
        noImageMessage.style.display = 'block';
    }
}

// Eliminar imagen actual
function eliminarImagenActual() {
    const mantenerImagenHidden = document.getElementById('mantener_imagen_hidden');
    const mantenerImagenCheckbox = document.getElementById('mantener_imagen');
    const currentImageContainer = document.getElementById('current-image-container');
    const noImageMessage = document.getElementById('no-image-message');
    const nuevaImagenContainer = document.getElementById('nueva-imagen-container');
    const eliminarImagenBtn = document.getElementById('eliminar-imagen-btn');
    
    Swal.fire({
        title: "¿Estás seguro?",
        text: "La imagen actual será eliminada permanentemente.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
        position: "center"
    }).then((result) => {
        if (result.isConfirmed) {
            imagenEliminada = true;
            mantenerImagenHidden.value = '0';
            
            if (mantenerImagenCheckbox) {
                mantenerImagenCheckbox.checked = false;
            }
            
            // Ocultar imagen actual
            if (currentImageContainer) {
                currentImageContainer.style.display = 'none';
            }
            
            // Mostrar mensaje de no hay imagen
            if (noImageMessage) {
                noImageMessage.style.display = 'block';
                noImageMessage.innerHTML = '<i class="fas fa-image me-1"></i> Imagen marcada para eliminación';
            }
            
            // Limpiar nueva imagen si existe
            if (nuevaImagenContainer) {
                nuevaImagenContainer.innerHTML = '';
            }
            
            // Ocultar botón de eliminar
            if (eliminarImagenBtn) {
                eliminarImagenBtn.style.display = 'none';
            }
            
            Swal.fire({
                title: "Imagen marcada para eliminación",
                text: "La imagen será removida al guardar los cambios.",
                icon: "info",
                timer: 2000,
                showConfirmButton: false
            });
        }
    });
}

// Toggle mantener imagen
function toggleMantenerImagen() {
    const mantenerImagenCheckbox = document.getElementById('mantener_imagen');
    const mantenerImagenHidden = document.getElementById('mantener_imagen_hidden');
    const nuevaImagenContainer = document.getElementById('nueva-imagen-container');
    const imagenInput = document.getElementById('imagen');
    
    if (mantenerImagenCheckbox.checked) {
        mantenerImagenHidden.value = '1';
        // Limpiar nueva imagen si existe
        if (nuevaImagenContainer) {
            nuevaImagenContainer.innerHTML = '';
        }
        if (imagenInput) {
            imagenInput.value = '';
        }
        if (imagenPreviewUrl) {
            URL.revokeObjectURL(imagenPreviewUrl);
            imagenPreviewUrl = null;
        }
        imagenSeleccionada = null;
    } else {
        mantenerImagenHidden.value = '0';
    }
}

// Calcular volumen y peso volumétrico
function calcularVolumen() {
    const largo = parseFloat(document.getElementById('dLargo_cm').value) || 0;
    const ancho = parseFloat(document.getElementById('dAncho_cm').value) || 0;
    const alto = parseFloat(document.getElementById('dAlto_cm').value) || 0;
    const peso = parseFloat(document.getElementById('dPeso').value) || 0;
    
    // Volumen en cm³
    const volumen = largo * ancho * alto;
    const volumenElement = document.getElementById('volumen-info');
    
    if (volumenElement) {
        const pesoVolumetrico = volumen / 5000;
        const pesoFacturable = Math.max(peso, pesoVolumetrico);
        
        if (largo > 0 && ancho > 0 && alto > 0) {
            volumenElement.innerHTML = `
                <strong>Dimensiones:</strong> ${largo.toFixed(2)} × ${ancho.toFixed(2)} × ${alto.toFixed(2)} cm<br>
                <strong>Volumen:</strong> ${volumen.toFixed(2)} cm³<br>
                <strong>Peso volumétrico (estimado):</strong> ${pesoVolumetrico.toFixed(3)} kg<br>
                <strong>Peso facturable (estimado):</strong> ${pesoFacturable.toFixed(3)} kg
            `;
        } else {
            volumenElement.innerHTML = 'Ingresa las tres dimensiones para calcular el volumen.';
        }
    }
}

// ==================== VALIDACIÓN DEL FORMULARIO ====================

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('valoracionForm');
    const imagenInput = document.getElementById('imagen');
    const eliminarImagenBtn = document.getElementById('eliminar-imagen-btn');
    const guardarBtn = document.querySelector('.guardar-btn');
    
    // Inicializar cálculo de volumen
    calcularVolumen();
    
    // Event listeners para calcular automáticamente
    ['dPeso', 'dLargo_cm', 'dAncho_cm', 'dAlto_cm'].forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            input.addEventListener('input', calcularVolumen);
        }
    });
    
    // Event listeners para validar precio de oferta en tiempo real
    const precioVentaInput = document.getElementById('dPrecio');
    const precioOfertaInput = document.getElementById('dPrecio_oferta');
    const tieneOfertaCheckbox = document.getElementById('bTiene_oferta');
    
    if (precioVentaInput && precioOfertaInput && tieneOfertaCheckbox) {
        // Validar cuando cambia el precio de venta
        precioVentaInput.addEventListener('input', function() {
            setTimeout(() => {
                validarPrecioContraPrecioVenta();
            }, 100);
        });
        
        // Validar cuando cambia el precio de oferta
        if (precioOfertaInput) {
            precioOfertaInput.addEventListener('input', validarPrecioContraPrecioVenta);
        }
        
        // Validar cuando cambia el checkbox de oferta
        tieneOfertaCheckbox.addEventListener('change', function() {
            setTimeout(() => {
                validarPrecioContraPrecioVenta();
            }, 100);
        });
    }
    
    // Inicializar toggle de oferta
    toggleOfertaForm();
    
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
    
    // Botón para eliminar imagen actual
    if (eliminarImagenBtn) {
        eliminarImagenBtn.addEventListener('click', eliminarImagenActual);
    }
    
    // Prevenir que el navegador guarde valores del autocomplete
    document.querySelectorAll('input, select, textarea').forEach(element => {
        element.setAttribute('autocomplete', 'off');
    });
    
    // Manejar el botón de guardar con SweetAlert2
    if (guardarBtn && form) {
        guardarBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Primero validar el formulario
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
                if (isNaN(stockNum) || stockNum < 0 || stockNum > 999999) {
                    stockInput.classList.add('is-invalid');
                    erroresCriticos = true;
                    
                    if (!stockInput.nextElementSibling || !stockInput.nextElementSibling.classList.contains('invalid-feedback')) {
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'invalid-feedback';
                        errorDiv.textContent = 'El stock debe ser un número entre 0 y 999999';
                        stockInput.parentNode.appendChild(errorDiv);
                    }
                }
            }
            
            // 3. Validar formato de precios
            const precioInput = document.getElementById('dPrecio');
            const precioOfertaInput = document.getElementById('dPrecio_oferta');
            
            // Validar precio normal
            if (precioInput.value.trim()) {
                const regexPrecio = /^[0-9]*\.?[0-9]*$/;
                if (!regexPrecio.test(precioInput.value.trim())) {
                    precioInput.classList.add('is-invalid');
                    erroresCriticos = true;
                } else {
                    const numero = parseFloat(precioInput.value.trim());
                    if (!isNaN(numero) && numero > 9999999.99) {
                        precioInput.classList.add('is-invalid');
                        erroresCriticos = true;
                    }
                }
            }
            
            // Validar precio de oferta si tiene valor
            if (precioOfertaInput.value.trim()) {
                const regexPrecio = /^[0-9]*\.?[0-9]*$/;
                if (!regexPrecio.test(precioOfertaInput.value.trim())) {
                    precioOfertaInput.classList.add('is-invalid');
                    erroresCriticos = true;
                } else {
                    const numero = parseFloat(precioOfertaInput.value.trim());
                    if (!isNaN(numero) && numero > 9999999.99) {
                        precioOfertaInput.classList.add('is-invalid');
                        erroresCriticos = true;
                    }
                }
            }
            
            // 4. Validar precio de oferta si está activa
            const tieneOfertaCheckbox = document.getElementById('bTiene_oferta');
            
            if (tieneOfertaCheckbox && tieneOfertaCheckbox.checked) {
                // Validar que precio de oferta tenga valor
                if (!precioOfertaInput.value.trim()) {
                    precioOfertaInput.classList.add('is-invalid');
                    erroresCriticos = true;
                }
                
                // Validar que precio de oferta sea menor que precio de venta
                if (precioOfertaInput.value.trim() && precioInput.value.trim()) {
                    const precioOferta = parseFloat(precioOfertaInput.value.trim());
                    const precioVenta = parseFloat(precioInput.value.trim());
                    
                    if (precioOferta >= precioVenta) {
                        precioOfertaInput.classList.add('is-invalid');
                        erroresCriticos = true;
                    }
                }
                
                // Validar fechas de oferta
                const fechaInicioInput = document.getElementById('dFecha_inicio_oferta');
                const fechaFinInput = document.getElementById('dFecha_fin_oferta');
                
                if (!fechaInicioInput.value) {
                    fechaInicioInput.classList.add('is-invalid');
                    erroresCriticos = true;
                }
                
                if (!fechaFinInput.value) {
                    fechaFinInput.classList.add('is-invalid');
                    erroresCriticos = true;
                }
                
                if (fechaInicioInput.value && fechaFinInput.value) {
                    const inicio = new Date(fechaInicioInput.value);
                    const fin = new Date(fechaFinInput.value);
                    
                    if (fin < inicio) {
                        fechaFinInput.classList.add('is-invalid');
                        erroresCriticos = true;
                    }
                }
            }
            
            // 5. Validar dimensiones si tienen valor
            const camposDimensiones = ['dPeso', 'dLargo_cm', 'dAncho_cm', 'dAlto_cm'];
            camposDimensiones.forEach(campoId => {
                const input = document.getElementById(campoId);
                if (input && input.value.trim()) {
                    const regexDimension = /^[0-9]*\.?[0-9]*$/;
                    if (!regexDimension.test(input.value.trim())) {
                        input.classList.add('is-invalid');
                        erroresCriticos = true;
                    }
                }
            });
            
            // 6. Validar atributos
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
                    if (errorExistente) {
                        errorExistente.remove();
                    }
                    
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'error-atributo text-danger small mt-1';
                    errorDiv.innerHTML = `<i class="fas fa-exclamation-circle me-1"></i> Selecciona un valor`;
                    container.appendChild(errorDiv);
                } else {
                    container.classList.remove('border-danger');
                    const errorExistente = container.querySelector('.error-atributo');
                    if (errorExistente) {
                        errorExistente.remove();
                    }
                }
            });
            
            if (!atributosValidos) {
                erroresCriticos = true;
            }
            
            // Si hay errores críticos, mostrar alerta de error
            if (erroresCriticos) {
                // Enfocar el primer campo con error
                const primerError = document.querySelector('.is-invalid');
                if (primerError) {
                    primerError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    primerError.focus();
                }
                
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "Por favor corrige los errores en el formulario.",
                    footer: '<a href="#form-errors">Ver errores en el formulario</a>',
                    position: "center"
                });
                return false;
            }
            
            // Si no hay errores, mostrar confirmación para guardar
            Swal.fire({
                title: "¿Deseas guardar los cambios?",
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: "Guardar",
                denyButtonText: `No guardar`,
                cancelButtonText: "Cancelar",
                position: "center"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Enviar el formulario
                    form.submit();
                } else if (result.isDenied) {
                    // Redirigir sin guardar
                    window.location.href = "{{ route('valoraciones.show', $producto->id_producto) }}";
                }
            });
        });
    }
    
    // Asegurar que la imagen seleccionada se envíe
    if (imagenSeleccionada && (!imagenInput.files || imagenInput.files.length === 0)) {
        // Si hay una imagen en memoria pero no en el input, agregarla
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(imagenSeleccionada);
        imagenInput.files = dataTransfer.files;
    }
});

// Mostrar mensaje SweetAlert2 después de actualizar exitosamente
@if(session('success'))
Swal.fire({
    title: "¡Guardado!",
    text: "{{ session('success') }}",
    icon: "success",
    position: "center",
    draggable: true,
    timer: 3000,
    showConfirmButton: false
});
@endif

// Mostrar mensaje SweetAlert2 si hay error al actualizar
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
    footer: '<a href="#form-errors">Ver errores en el formulario</a>',
    position: "center",
    draggable: true
});
@endif
</script>
@endpush

<style>
.form-check-input:checked + .form-check-label {
    font-weight: bold;
    color: #0d6efd;
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

#nueva-imagen-container .card:hover,
#current-image-preview:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

#nueva-imagen-container .btn-danger {
    transition: all 0.3s ease;
}

#nueva-imagen-container .btn-danger:hover {
    transform: scale(1.1);
    background-color: #c82333;
    border-color: #bd2130;
}

.form-control:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
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

/* Estilos para el cálculo de volumen */
#volumen-info {
    background: rgba(0,0,0,0.03);
    padding: 8px;
    border-radius: 5px;
    border-left: 4px solid #17a2b8;
    font-size: 0.9em;
}

#volumen-info strong {
    color: #17a2b8;
}

/* Estilos para sección de oferta */
#oferta-form {
    transition: all 0.3s ease;
}

.card-header.bg-danger {
    background: linear-gradient(135deg, #dc3545, #c82333);
}

/* Estilos para campos obligatorios en oferta */
#oferta-form .form-label span.text-danger {
    font-size: 1.1em;
}

/* Responsive */
@media (max-width: 768px) {
    #nueva-imagen-container .card-img-top {
        height: 100px !important;
    }
    
    #current-image-preview {
        max-width: 120px !important;
        max-height: 120px !important;
    }
}
</style>
@endsection
