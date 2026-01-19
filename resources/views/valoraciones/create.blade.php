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
                                           placeholder="Ej: MEZ-750ML-REP-01">
                                    @error('vSKU')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="dPrecio" class="form-label fw-bold">
                                        Precio <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" name="dPrecio" id="dPrecio" 
                                           class="form-control @error('dPrecio') is-invalid @enderror"
                                           value="{{ old('dPrecio') }}" required min="0" step="0.01"
                                           placeholder="Ej: 299.99">
                                    @error('dPrecio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="dPrecio_oferta" class="form-label fw-bold">
                                        Precio de Oferta
                                    </label>
                                    <input type="number" name="dPrecio_oferta" id="dPrecio_oferta" 
                                           class="form-control @error('dPrecio_oferta') is-invalid @enderror"
                                           value="{{ old('dPrecio_oferta') }}" min="0" step="0.01"
                                           placeholder="Ej: 249.99">
                                    @error('dPrecio_oferta')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="iStock" class="form-label fw-bold">
                                        Stock <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" name="iStock" id="iStock" 
                                           class="form-control @error('iStock') is-invalid @enderror"
                                           value="{{ old('iStock', 0) }}" required min="0"
                                           placeholder="Ej: 50">
                                    @error('iStock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="dPeso" class="form-label fw-bold">
                                        Peso (kg)
                                    </label>
                                    <input type="text" name="dPeso" id="dPeso" 
                                           class="form-control dimension-input @error('dPeso') is-invalid @enderror"
                                           value="{{ old('dPeso') }}"
                                           placeholder="Ej: 1.25"
                                           data-max="1000">
                                    @error('dPeso')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
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
                                                <input type="text" name="dLargo_cm" id="dLargo_cm" 
                                                       class="form-control dimension-input @error('dLargo_cm') is-invalid @enderror"
                                                       value="{{ old('dLargo_cm') }}"
                                                       placeholder="Ej: 30.5"
                                                       data-max="500">
                                                @error('dLargo_cm')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="form-group mb-3">
                                                <label for="dAncho_cm" class="form-label fw-bold">
                                                    Ancho (cm)
                                                </label>
                                                <input type="text" name="dAncho_cm" id="dAncho_cm" 
                                                       class="form-control dimension-input @error('dAncho_cm') is-invalid @enderror"
                                                       value="{{ old('dAncho_cm') }}"
                                                       placeholder="Ej: 15.2"
                                                       data-max="500">
                                                @error('dAncho_cm')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="form-group mb-3">
                                                <label for="dAlto_cm" class="form-label fw-bold">
                                                    Alto (cm)
                                                </label>
                                                <input type="text" name="dAlto_cm" id="dAlto_cm" 
                                                       class="form-control dimension-input @error('dAlto_cm') is-invalid @enderror"
                                                       value="{{ old('dAlto_cm') }}"
                                                       placeholder="Ej: 45.0"
                                                       data-max="500">
                                                @error('dAlto_cm')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const imagenInput = document.getElementById('imagen');
    const previewContainer = document.getElementById('preview-container');
    const form = document.getElementById('valoracionForm');
    
    // Variables para dimensiones
    const largoInput = document.getElementById('dLargo_cm');
    const anchoInput = document.getElementById('dAncho_cm');
    const altoInput = document.getElementById('dAlto_cm');
    const pesoInput = document.getElementById('dPeso');
    const volumenInfo = document.getElementById('volumen-info');
    
    // Array de inputs para validación
    const dimensionInputs = [largoInput, anchoInput, altoInput, pesoInput];
    
    // Calcular volumen automáticamente
    function calcularVolumen() {
        const largo = parseFloat(largoInput.value) || 0;
        const ancho = parseFloat(anchoInput.value) || 0;
        const alto = parseFloat(altoInput.value) || 0;
        
        if (largo > 0 && ancho > 0 && alto > 0) {
            const volumen = largo * ancho * alto;
            const pesoVolumetrico = volumen / 5000;
            
            volumenInfo.innerHTML = `
                <strong>Dimensiones:</strong> ${largo.toFixed(1)} × ${ancho.toFixed(1)} × ${alto.toFixed(1)} cm<br>
                <strong>Volumen:</strong> ${volumen.toFixed(0)} cm³<br>
                <strong>Peso volumétrico (estimado):</strong> ${pesoVolumetrico.toFixed(2)} kg
            `;
        } else {
            volumenInfo.innerHTML = 'Ingresa las tres dimensiones para calcular el volumen.';
        }
    }
    
    // Inicializar cálculo de volumen
    if (largoInput && anchoInput && altoInput) {
        [largoInput, anchoInput, altoInput].forEach(input => {
            if (input) {
                input.addEventListener('input', calcularVolumen);
                input.addEventListener('change', calcularVolumen);
            }
        });
        calcularVolumen();
    }
    
    // CONFIGURAR VALIDACIÓN PARA TODOS LOS INPUTS DE DIMENSIÓN
    function setupDimensionInputValidation() {
        dimensionInputs.forEach(input => {
            if (!input) return;
            
            // Guardar última posición válida del cursor
            let lastValidValue = input.value;
            let validationTimeout;
            
            input.addEventListener('input', function(e) {
                // Guardar posición actual del cursor
                const cursorPos = this.selectionStart;
                const originalValue = this.value;
                
                // Permitir solo números y un punto decimal
                let newValue = originalValue.replace(/[^0-9.]/g, '');
                
                // Asegurar solo un punto decimal
                const dotCount = (newValue.match(/\./g) || []).length;
                if (dotCount > 1) {
                    // Eliminar puntos extra manteniendo solo el primero
                    const firstDotIndex = newValue.indexOf('.');
                    newValue = newValue.substring(0, firstDotIndex + 1) + 
                               newValue.substring(firstDotIndex + 1).replace(/\./g, '');
                }
                
                // Limitar a máximo 2 decimales después del punto
                if (newValue.includes('.')) {
                    const parts = newValue.split('.');
                    if (parts[1] && parts[1].length > 2) {
                        newValue = parts[0] + '.' + parts[1].substring(0, 2);
                    }
                }
                
                // Limitar longitud total a 10 caracteres
                if (newValue.length > 10) {
                    newValue = newValue.substring(0, 10);
                }
                
                // Aplicar el nuevo valor si es diferente
                if (newValue !== originalValue) {
                    this.value = newValue;
                    
                    // Ajustar posición del cursor
                    const lengthDiff = newValue.length - originalValue.length;
                    const newCursorPos = Math.max(0, Math.min(cursorPos + lengthDiff, newValue.length));
                    this.setSelectionRange(newCursorPos, newCursorPos);
                }
                
                // Limpiar timeout anterior y programar nueva validación
                clearTimeout(validationTimeout);
                validationTimeout = setTimeout(() => {
                    // Solo remover clases de error si el valor es válido
                    const numValue = parseFloat(this.value) || 0;
                    const maxVal = parseFloat(this.dataset.max) || 500;
                    
                    if (!this.value || this.value.trim() === '' || (!isNaN(numValue) && numValue >= 0 && numValue <= maxVal)) {
                        this.classList.remove('is-invalid');
                        const parent = this.parentNode;
                        let existingFeedback = parent.querySelector('.invalid-feedback');
                        if (existingFeedback) {
                            existingFeedback.remove();
                        }
                    }
                }, 500); // Validar después de 500ms sin escribir
                
                lastValidValue = this.value;
            });
            
            // También limpiar errores al perder el foco
            input.addEventListener('blur', function() {
                clearTimeout(validationTimeout);
                const numValue = parseFloat(this.value) || 0;
                const maxVal = parseFloat(this.dataset.max) || 500;
                
                if (!this.value || this.value.trim() === '' || (!isNaN(numValue) && numValue >= 0 && numValue <= maxVal)) {
                    this.classList.remove('is-invalid');
                    const parent = this.parentNode;
                    let existingFeedback = parent.querySelector('.invalid-feedback');
                    if (existingFeedback) {
                        existingFeedback.remove();
                    }
                }
            });
            
            // Limpiar clases de error al cargar la página
            input.classList.remove('is-invalid');
            const parent = input.parentNode;
            let existingFeedback = parent.querySelector('.invalid-feedback');
            if (existingFeedback) {
                existingFeedback.remove();
            }
        });
    }
    
    // Inicializar validación de dimensiones
    setupDimensionInputValidation();
    
    // Manejo de imagen
    let archivoOriginal = null;
    let archivoTemporal = null;
    
    if (imagenInput) {
        imagenInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                archivoOriginal = file;
                archivoTemporal = file;
                
                mostrarVistaPrevia(file);
            } else {
                if (archivoTemporal && archivoOriginal) {
                    restaurarArchivoEnInput(archivoOriginal);
                    mostrarVistaPrevia(archivoOriginal);
                } else if (archivoTemporal && !archivoOriginal) {
                    restaurarArchivoEnInput(archivoTemporal);
                    mostrarVistaPrevia(archivoTemporal);
                } else {
                    previewContainer.innerHTML = '';
                }
            }
        });
    }
    
    function mostrarVistaPrevia(file) {
        if (!previewContainer) return;
        
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
                archivoOriginal = null;
                archivoTemporal = null;
            };
            
            previewContainer.appendChild(img);
            previewContainer.appendChild(removeBtn);
        };
        
        reader.readAsDataURL(file);
    }
    
    function restaurarArchivoEnInput(file) {
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        imagenInput.files = dataTransfer.files;
    }
    
    // VALIDACIÓN DE ATRIBUTOS AL ENVIAR FORMULARIO
    if (form) {
        form.addEventListener('submit', function(e) {
            // 1. Validar atributos
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
                e.preventDefault();
                const mensaje = `Debes seleccionar un valor para los siguientes atributos:\n\n${mensajesError.join('\n')}`;
                alert(mensaje);
                return false;
            }
            
            // 2. Validar dimensiones y peso (sin mostrar mensajes)
            let dimensionesValidas = true;
            
            dimensionInputs.forEach(input => {
                if (!input) return;
                
                const value = input.value;
                if (value && value.trim() !== '') {
                    const numValue = parseFloat(value);
                    const maxVal = parseFloat(input.dataset.max) || 500;
                    
                    if (isNaN(numValue) || numValue < 0 || numValue > maxVal) {
                        dimensionesValidas = false;
                        input.classList.add('is-invalid');
                    }
                }
            });
            
            if (!dimensionesValidas) {
                e.preventDefault();
                alert('Por favor, corrige los valores de dimensiones o peso antes de continuar.');
                return false;
            }
            
            // 3. Manejar archivo de imagen
            if (archivoTemporal && (!imagenInput.files || imagenInput.files.length === 0)) {
                restaurarArchivoEnInput(archivoTemporal);
            }
            
            if (!archivoTemporal && imagenInput.files && imagenInput.files.length > 0) {
                imagenInput.value = '';
            }
            
            return true;
        });
    }
    
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
});
</script>

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

.dimension-input {
    font-family: monospace;
}
</style>
@endsection