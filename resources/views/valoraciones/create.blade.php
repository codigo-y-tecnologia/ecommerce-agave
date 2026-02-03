@extends('layouts.app')

@section('title', 'Nueva Valoración - ' . $producto->vNombre)
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1><i class="fas fa-plus-circle me-2"></i>Nueva Valoración</h1>
            <p class="text-muted">Producto: {{ $producto->vNombre }}</p>
            <p class="text-muted small">
                <i class="fas fa-info-circle me-1"></i>
                Crea una variación específica de este producto seleccionando valores de los atributos asignados.
            </p>
        </div>
        <div>
            <a href="{{ route('valoraciones.show', $producto->id_producto) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Volver a Valoraciones
            </a>
        </div>
    </div>

    <form action="{{ route('valoraciones.store', $producto->id_producto) }}" method="POST" enctype="multipart/form-data" id="valoracionForm">
        @csrf
        
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
                                           value="{{ old('vSKU') }}" required
                                           maxlength="50"
                                           oninput="validarSKU(this)"
                                           placeholder="Ej: MEZ-750ML-REP-01">
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
                                               value="{{ old('dPrecio') }}" 
                                               required 
                                               oninput="validarPrecio(this)"
                                               placeholder="0.00"
                                               title="Máximo: 9,999,999.99">
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

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="dPrecio_oferta" class="form-label fw-bold">
                                        Precio de Oferta
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="text" name="dPrecio_oferta" id="dPrecio_oferta" 
                                               class="form-control @error('dPrecio_oferta') is-invalid @enderror"
                                               value="{{ old('dPrecio_oferta') }}" 
                                               oninput="validarPrecio(this)"
                                               placeholder="0.00"
                                               title="Máximo: 9,999,999.99">
                                    </div>
                                    @error('dPrecio_oferta')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Máximo: 9,999,999.99 (7 dígitos enteros)
                                    </small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="iStock" class="form-label fw-bold">
                                        Stock <span class="text-danger">*</span>
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
                                           max="999999">
                                    @error('iStock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Máximo 6 dígitos (0-999999)</small>
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
                                               value="{{ old('dPeso') }}" 
                                               oninput="validarPeso(this)"
                                               placeholder="0.000"
                                               title="Peso en kilogramos (ej: 1.250)">
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
                                    <label for="vClase_envio" class="form-label fw-bold">
                                        Clase de Envío
                                    </label>
                                    <select name="vClase_envio" id="vClase_envio" 
                                            class="form-control @error('vClase_envio') is-invalid @enderror">
                                        <option value="">Igual que el producto padre</option>
                                        <option value="Estandar" {{ old('vClase_envio') == 'Estandar' ? 'selected' : '' }}>Estándar</option>
                                        <option value="Fragil" {{ old('vClase_envio') == 'Fragil' ? 'selected' : '' }}>Frágil</option>
                                        <option value="Pesado" {{ old('vClase_envio') == 'Pesado' ? 'selected' : '' }}>Pesado</option>
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
                                                           value="{{ old('dLargo_cm') }}" 
                                                           oninput="validarDimension(this)"
                                                           placeholder="0.00"
                                                           title="Largo en centímetros">
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
                                                           value="{{ old('dAncho_cm') }}" 
                                                           oninput="validarDimension(this)"
                                                           placeholder="0.00"
                                                           title="Ancho en centímetros">
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
                                                           value="{{ old('dAlto_cm') }}" 
                                                           oninput="validarDimension(this)"
                                                           placeholder="0.00"
                                                           title="Alto en centímetros">
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
                                    <div class="form-check form-switch mt-4">
                                        <input type="checkbox" name="bActivo" id="bActivo" 
                                               class="form-check-input" value="1" 
                                               {{ old('bActivo', true) ? 'checked' : '' }}>
                                        <label for="bActivo" class="form-check-label fw-bold">
                                            Valoración activa
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Si está desactivada, no estará disponible para venta
                                    </small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="imagen" class="form-label fw-bold">
                                        Imagen de la Valoración
                                    </label>
                                    <input type="file" name="imagen" id="imagen" 
                                        class="form-control @error('imagen') is-invalid @enderror"
                                        accept=".jpg,.jpeg,.png,.gif,.webp,.bmp,.svg">
                                    @error('imagen')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Imagen específica para esta valoración (opcional, máximo 5MB)
                                    </small>
                                    <div id="preview-container" class="mt-2"></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="tDescripcion" class="form-label fw-bold">
                                Descripción (Opcional)
                            </label>
                            <textarea name="tDescripcion" id="tDescripcion" 
                                      class="form-control @error('tDescripcion') is-invalid @enderror"
                                      rows="3" placeholder="Descripción específica de esta valoración">{{ old('tDescripcion') }}</textarea>
                            @error('tDescripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
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
                                                       {{ old('atributos.' . $valor->atributo->id_atributo) == $valor->id_atributo_valor ? 'checked' : '' }}
                                                       required>
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
            </div>
        </div>

        <div class="d-flex gap-2 mb-5">
            <button type="submit" class="btn btn-success btn-lg">
                <i class="fas fa-save me-2"></i> Crear Valoración
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
// Objeto para guardar valores originales
const valoresOriginales = {};

// Guardar valor original cuando el campo recibe focus
function guardarValorOriginal(input) {
    valoresOriginales[input.id] = input.value;
}

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
    
    input.classList.remove('is-invalid');
    
    // Mostrar error si el número es muy grande
    if (value) {
        const numero = parseFloat(value);
        if (!isNaN(numero) && numero > 9999999.99) {
            input.classList.add('is-invalid');
            mostrarErrorGeneral(input, 'El precio máximo es 9,999,999.99');
        }
    }
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

// Validar peso (kg con 3 decimales, máximo 1000.000)
function validarPeso(input) {
    let value = input.value;
    const cursorPos = input.selectionStart;
    
    if (value === '') {
        input.classList.remove('is-invalid');
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
    
    // Limitar enteros a 4 dígitos (1000.000)
    const partesNumero = value.split('.');
    const parteEntera = partesNumero[0];
    
    if (parteEntera.length > 4) {
        value = parteEntera.substring(0, 4) + (partesNumero[1] ? '.' + partesNumero[1] : '');
    }
    
    // Limitar decimales a máximo 3
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
    
    // Validar límite
    if (value) {
        const numero = parseFloat(value);
        if (!isNaN(numero) && numero > 1000.000) {
            input.classList.add('is-invalid');
            mostrarErrorGeneral(input, 'El peso máximo es 1000.000 kg');
        }
    }
    
    calcularVolumen();
}

// Validar dimensiones (cm con 2 decimales, máximo 500.00)
function validarDimension(input) {
    let value = input.value;
    const cursorPos = input.selectionStart;
    
    if (value === '') {
        input.classList.remove('is-invalid');
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
    
    // Limitar enteros a 3 dígitos (500.00)
    const partesNumero = value.split('.');
    const parteEntera = partesNumero[0];
    
    if (parteEntera.length > 3) {
        value = parteEntera.substring(0, 3) + (partesNumero[1] ? '.' + partesNumero[1] : '');
    }
    
    // Limitar decimales a máximo 2
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
    
    // Validar límite
    if (value) {
        const numero = parseFloat(value);
        if (!isNaN(numero) && numero > 500.00) {
            input.classList.add('is-invalid');
            mostrarErrorGeneral(input, 'La dimensión máxima es 500.00 cm');
        }
    }
    
    calcularVolumen();
}

// Función auxiliar para mostrar errores generales
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
        
        volumenElement.innerHTML = `
            <strong>Dimensiones:</strong> ${largo.toFixed(2)} × ${ancho.toFixed(2)} × ${alto.toFixed(2)} cm<br>
            <strong>Volumen:</strong> ${volumen.toFixed(2)} cm³<br>
            <strong>Peso volumétrico (estimado):</strong> ${pesoVolumetrico.toFixed(3)} kg<br>
            <strong>Peso facturable (estimado):</strong> ${pesoFacturable.toFixed(3)} kg
        `;
    }
}

// Validación del formulario al enviar
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('valoracionForm');
    const imagenInput = document.getElementById('imagen');
    const previewContainer = document.getElementById('preview-container');
    
    // Inicializar cálculo de volumen
    calcularVolumen();
    
    // Event listeners para calcular automáticamente
    ['dPeso', 'dLargo_cm', 'dAncho_cm', 'dAlto_cm'].forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            input.addEventListener('input', calcularVolumen);
        }
    });
    
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
    
    // Manejo de imagen
    if (imagenInput && previewContainer) {
        imagenInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewContainer.innerHTML = '';
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.maxWidth = '200px';
                    img.style.maxHeight = '200px';
                    img.style.objectFit = 'cover';
                    img.style.borderRadius = '8px';
                    img.style.marginTop = '10px';
                    
                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'btn btn-danger btn-sm mt-2';
                    removeBtn.innerHTML = '<i class="fas fa-trash me-1"></i> Eliminar imagen';
                    removeBtn.onclick = function() {
                        imagenInput.value = '';
                        previewContainer.innerHTML = '';
                    };
                    
                    previewContainer.appendChild(img);
                    previewContainer.appendChild(removeBtn);
                };
                
                reader.readAsDataURL(file);
            } else {
                previewContainer.innerHTML = '';
            }
        });
    }
    
    // Validación del formulario al enviar
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
            
            // Validar precio
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
            
            // 4. Validar dimensiones si tienen valor
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
            
            // 5. Validar atributos
            let atributosValidos = true;
            const atributosContainers = document.querySelectorAll('.atributo-container');
            const mensajesError = [];
            
            atributosContainers.forEach(container => {
                const nombreAtributo = container.querySelector('label').textContent.replace('*', '').trim();
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
                    
                    mensajesError.push(`• ${nombreAtributo}`);
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
                const mensaje = `Debes seleccionar un valor para los siguientes atributos:\n\n${mensajesError.join('\n')}`;
                if (!erroresCriticos) {
                    alert(mensaje);
                }
            }
            
            // Si hay errores críticos, prevenir envío
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
            
            return true;
        });
    }
});

// Mostrar mensaje SweetAlert2 después de crear exitosamente
@if(session('success'))
Swal.fire({
    title: "¡Valoración Registrada!",
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
    color: #198754;
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

#preview-container img {
    transition: all 0.3s ease;
}

#preview-container img:hover {
    transform: scale(1.05);
}

#preview-container button {
    display: block;
    margin-top: 10px;
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
</style>
@endsection