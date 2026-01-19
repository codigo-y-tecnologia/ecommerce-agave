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
          method="POST" enctype="multipart/form-data" id="valoracionForm">
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
                                           value="{{ old('dPrecio', $variacion->dPrecio) }}" required min="0" step="0.01"
                                           placeholder="Ej: 299.99">
                                    @error('dPrecio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="dPrecio_oferta" class="form-label fw-bold">
                                        Precio de Oferta
                                    </label>
                                    <input type="number" name="dPrecio_oferta" id="dPrecio_oferta" 
                                           class="form-control @error('dPrecio_oferta') is-invalid @enderror"
                                           value="{{ old('dPrecio_oferta', $variacion->dPrecio_oferta) }}" min="0" step="0.01"
                                           placeholder="Ej: 249.99">
                                    @error('dPrecio_oferta')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="iStock" class="form-label fw-bold">
                                        Stock <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" name="iStock" id="iStock" 
                                           class="form-control @error('iStock') is-invalid @enderror"
                                           value="{{ old('iStock', $variacion->iStock) }}" required min="0"
                                           placeholder="Ej: 50">
                                    @error('iStock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="dPeso" class="form-label fw-bold">
                                        Peso (kg)
                                    </label>
                                    <input type="text" name="dPeso" id="dPeso" 
                                           class="form-control dimension-input @error('dPeso') is-invalid @enderror"
                                           value="{{ old('dPeso', $variacion->dPeso) }}"
                                           placeholder="Ej: 1.25"
                                           data-max="1000">
                                    @error('dPeso')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                                        <option value="Estandar" {{ old('vClase_envio', $variacion->vClase_envio) == 'Estandar' ? 'selected' : '' }}>Estándar</option>
                                        <option value="Fragil" {{ old('vClase_envio', $variacion->vClase_envio) == 'Fragil' ? 'selected' : '' }}>Frágil</option>
                                        <option value="Pesado" {{ old('vClase_envio', $variacion->vClase_envio) == 'Pesado' ? 'selected' : '' }}>Pesado</option>
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
                                                       value="{{ old('dLargo_cm', $variacion->dLargo_cm) }}"
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
                                                       value="{{ old('dAncho_cm', $variacion->dAncho_cm) }}"
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
                                                       value="{{ old('dAlto_cm', $variacion->dAlto_cm) }}"
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
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="imagen" class="form-label fw-bold">
                                        Imagen de la Valoración <small class="text-muted">(Formatos: JPG, PNG, GIF, WebP, BMP, SVG)</small>
                                    </label>
                                    <input type="file" name="imagen" id="imagen" 
                                        class="form-control @error('imagen') is-invalid @enderror"
                                        accept=".jpg,.jpeg,.png,.gif,.webp,.bmp,.svg,.tiff,.ico,.heic,.heif">
                                    @error('imagen')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    
                                    <!-- Vista previa de la imagen actual -->
                                    @if($variacion->vImagen)
                                        <div class="mt-2">
                                            <img src="{{ asset($variacion->vImagen) }}" 
                                                alt="Imagen actual"
                                                style="max-width: 150px; max-height: 150px; object-fit: cover; border-radius: 4px; margin-top: 10px;"
                                                id="current-image-preview">
                                            <div class="form-check mt-2">
                                                <input type="checkbox" name="usar_imagen_actual" id="usar_imagen_actual" 
                                                    class="form-check-input" value="1" checked>
                                                <label for="usar_imagen_actual" class="form-check-label">
                                                    <i class="fas fa-check-circle text-success me-1"></i> Mantener imagen actual
                                                </label>
                                            </div>
                                        </div>
                                    @else
                                        <div class="mt-2 text-muted">
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
                                    <div class="form-check form-switch mt-4">
                                        <input type="checkbox" name="bActivo" id="bActivo" 
                                               class="form-check-input" value="1" 
                                               {{ old('bActivo', $variacion->bActivo) ? 'checked' : '' }}>
                                        <label for="bActivo" class="form-check-label fw-bold">
                                            Valoración activa
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="tDescripcion" class="form-label fw-bold">
                                Descripción (Opcional)
                            </label>
                            <textarea name="tDescripcion" id="tDescripcion" 
                                      class="form-control @error('tDescripcion') is-invalid @enderror"
                                      rows="3" placeholder="Descripción específica de esta valoración">{{ old('tDescripcion', $variacion->tDescripcion) }}</textarea>
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
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-save me-2"></i> Actualizar Valoración
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
    const usarImagenActualCheckbox = document.getElementById('usar_imagen_actual');
    const mantenerImagenHidden = document.getElementById('mantener_imagen_hidden');
    const nuevaImagenContainer = document.getElementById('nueva-imagen-container');
    const eliminarImagenBtn = document.getElementById('eliminar-imagen-btn');
    const currentImagePreview = document.getElementById('current-image-preview');
    const form = document.getElementById('valoracionForm');
    
    // Variables para dimensiones
    const largoInput = document.getElementById('dLargo_cm');
    const anchoInput = document.getElementById('dAncho_cm');
    const altoInput = document.getElementById('dAlto_cm');
    const pesoInput = document.getElementById('dPeso');
    
    // Array de inputs para validación
    const dimensionInputs = [largoInput, anchoInput, altoInput, pesoInput];
    
    // Variables para controlar el estado de la imagen
    let archivoOriginal = null;
    let archivoNuevo = null;
    let imagenEliminada = false;
    
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
    
    // Inicializar estado de la imagen
    // Por defecto, mantener imagen actual está activado
    if (usarImagenActualCheckbox) {
        usarImagenActualCheckbox.checked = true;
        mantenerImagenHidden.value = '1';
    }
    
    // Guardar el estado original de la imagen
    if (imagenInput && imagenInput.files && imagenInput.files[0]) {
        archivoOriginal = imagenInput.files[0];
    }
    
    // Manejar cambio en el input de archivo
    if (imagenInput) {
        imagenInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                archivoNuevo = file;
                
                // Desmarcar checkbox de mantener imagen automáticamente
                if (usarImagenActualCheckbox) {
                    usarImagenActualCheckbox.checked = false;
                    mantenerImagenHidden.value = '0';
                }
                
                // Mostrar vista previa de nueva imagen
                mostrarNuevaImagenPreview(file);
            } else {
                // Si se cancela la selección y había archivo nuevo
                if (archivoNuevo) {
                    // Restaurar el archivo nuevo
                    restaurarArchivoEnInput(archivoNuevo);
                    mostrarNuevaImagenPreview(archivoNuevo);
                    
                    // Mantener checkbox desmarcado
                    if (usarImagenActualCheckbox) {
                        usarImagenActualCheckbox.checked = false;
                        mantenerImagenHidden.value = '0';
                    }
                } else if (archivoOriginal && !imagenEliminada) {
                    // Si había archivo original y no se eliminó, restaurarlo
                    restaurarArchivoEnInput(archivoOriginal);
                    
                    // Marcar checkbox de mantener imagen
                    if (usarImagenActualCheckbox) {
                        usarImagenActualCheckbox.checked = true;
                        mantenerImagenHidden.value = '1';
                    }
                    
                    // Limpiar vista previa de nueva imagen
                    if (nuevaImagenContainer) {
                        nuevaImagenContainer.innerHTML = '';
                    }
                }
            }
        });
    }
    
    // Manejar el checkbox de mantener imagen
    if (usarImagenActualCheckbox) {
        usarImagenActualCheckbox.addEventListener('change', function() {
            if (this.checked) {
                mantenerImagenHidden.value = '1';
                archivoNuevo = null;
                
                // Limpiar input de archivo
                if (imagenInput) {
                    imagenInput.value = '';
                }
                
                // Limpiar vista previa de nueva imagen
                if (nuevaImagenContainer) {
                    nuevaImagenContainer.innerHTML = '';
                }
            } else {
                mantenerImagenHidden.value = '0';
                // Si hay archivo nuevo, restaurarlo
                if (archivoNuevo) {
                    restaurarArchivoEnInput(archivoNuevo);
                    mostrarNuevaImagenPreview(archivoNuevo);
                }
            }
        });
    }
    
    // Manejar el botón de eliminar imagen
    if (eliminarImagenBtn) {
        eliminarImagenBtn.addEventListener('click', function() {
            if (confirm('¿Estás seguro de que quieres eliminar la imagen actual? Esta acción no se puede deshacer.')) {
                imagenEliminada = true;
                mantenerImagenHidden.value = '0';
                archivoNuevo = null;
                archivoOriginal = null;
                
                // Limpiar input de archivo
                if (imagenInput) {
                    imagenInput.value = '';
                }
                
                // Ocultar imagen actual
                if (currentImagePreview) {
                    currentImagePreview.style.display = 'none';
                }
                
                // Desmarcar checkbox de mantener imagen
                if (usarImagenActualCheckbox) {
                    usarImagenActualCheckbox.checked = false;
                }
                
                // Limpiar vista previa de nueva imagen
                if (nuevaImagenContainer) {
                    nuevaImagenContainer.innerHTML = '';
                }
                
                // Mostrar mensaje de eliminación
                const mensajeEliminar = document.createElement('div');
                mensajeEliminar.className = 'alert alert-warning mt-2';
                mensajeEliminar.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i> La imagen actual será eliminada al guardar los cambios.';
                
                if (currentImagePreview && currentImagePreview.parentNode) {
                    currentImagePreview.parentNode.insertBefore(mensajeEliminar, currentImagePreview.nextSibling);
                }
                
                // Ocultar botón de eliminar
                eliminarImagenBtn.style.display = 'none';
            }
        });
    }
    
    // Función para mostrar vista previa de nueva imagen
    function mostrarNuevaImagenPreview(file) {
        if (!nuevaImagenContainer) return;
        
        const reader = new FileReader();
        
        reader.onload = function(e) {
            nuevaImagenContainer.innerHTML = `
                <div class="mt-2">
                    <strong><i class="fas fa-image me-1"></i> Nueva imagen seleccionada:</strong>
                    <div class="mt-2">
                        <img src="${e.target.result}" 
                             style="max-width: 150px; max-height: 150px; object-fit: cover; border-radius: 4px; margin-top: 10px;"
                             alt="Nueva imagen">
                        <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="cancelarNuevaImagen()">
                            <i class="fas fa-times me-1"></i> Cancelar cambio
                        </button>
                    </div>
                </div>
            `;
        };
        
        reader.readAsDataURL(file);
    }
    
    // Función para restaurar archivo en input
    function restaurarArchivoEnInput(file) {
        if (!imagenInput) return;
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        imagenInput.files = dataTransfer.files;
    }
    
    // Función global para cancelar nueva imagen
    window.cancelarNuevaImagen = function() {
        if (!imagenEliminada && archivoOriginal) {
            // Restaurar archivo original
            restaurarArchivoEnInput(archivoOriginal);
            
            // Marcar checkbox de mantener imagen
            if (usarImagenActualCheckbox) {
                usarImagenActualCheckbox.checked = true;
                mantenerImagenHidden.value = '1';
            }
            
            // Limpiar vista previa de nueva imagen
            if (nuevaImagenContainer) {
                nuevaImagenContainer.innerHTML = '';
            }
            
            archivoNuevo = null;
        } else if (imagenEliminada) {
            // Si se eliminó la imagen, solo limpiar
            if (imagenInput) {
                imagenInput.value = '';
            }
            if (nuevaImagenContainer) {
                nuevaImagenContainer.innerHTML = '';
            }
        }
    };
    
    // VALIDACIÓN DE ATRIBUTOS AL ENVIAR FORMULARIO
    if (form) {
        form.addEventListener('submit', function(e) {
            // 1. Validar atributos
            let atributosValidos = true;
            const atributosContainers = document.querySelectorAll('.atributo-container');
            const mensajesErrorAtributos = [];
            
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
                    
                    mensajesErrorAtributos.push(`• ${nombreAtributo}`);
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
                const mensaje = `Debes seleccionar un valor para los siguientes atributos:\n\n${mensajesErrorAtributos.join('\n')}`;
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
            
            // 3. Asegurar que el campo hidden tenga el valor correcto
            // Esto es importante para que el controlador sepa si mantener o no la imagen
            if (imagenEliminada) {
                mantenerImagenHidden.value = '0';
            } else if (archivoNuevo) {
                // Si hay archivo nuevo, asegurar que el input tenga el archivo
                mantenerImagenHidden.value = '0';
                if (!imagenInput.files || imagenInput.files.length === 0) {
                    restaurarArchivoEnInput(archivoNuevo);
                }
            } else if (usarImagenActualCheckbox && usarImagenActualCheckbox.checked) {
                mantenerImagenHidden.value = '1';
            } else {
                mantenerImagenHidden.value = '0';
            }
            
            // 4. Validar que si se desmarcó "mantener imagen" y no se subió nueva, se elimine la actual
            if (!usarImagenActualCheckbox || !usarImagenActualCheckbox.checked) {
                if (!archivoNuevo && !imagenEliminada) {
                    // El usuario desmarcó mantener imagen pero no subió nueva ni eliminó
                    mantenerImagenHidden.value = '0';
                }
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
    color: #0d6efd;
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

#nueva-imagen-container img,
#current-image-preview {
    transition: all 0.3s ease;
}

#nueva-imagen-container img:hover,
#current-image-preview:hover {
    transform: scale(1.05);
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

.dimension-input {
    font-family: monospace;
}
</style>
@endsection