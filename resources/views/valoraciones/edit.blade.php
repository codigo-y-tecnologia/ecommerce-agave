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
                                    <input type="number" name="dPeso" id="dPeso" 
                                           class="form-control @error('dPeso') is-invalid @enderror"
                                           value="{{ old('dPeso', $variacion->dPeso) }}" min="0" step="0.01"
                                           placeholder="Ej: 1.25">
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
                                        <option value="Otro" {{ old('vClase_envio', $variacion->vClase_envio) == 'Estandar' ? 'selected' : '' }}>Estandar</option>
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
                                <div class="mb-4 p-3 border rounded">
                                    <label class="fw-bold mb-2">{{ $nombreAtributo }} <span class="text-danger">*</span></label>
                                    <div class="form-group">
                                        @foreach($valores as $valor)
                                            <div class="form-check mb-2">
                                                <input type="radio" 
                                                       name="atributos[{{ $valor->atributo->id_atributo }}]" 
                                                       id="atributo_{{ $valor->id_atributo_valor }}"
                                                       value="{{ $valor->id_atributo_valor }}"
                                                       class="form-check-input"
                                                       {{ isset($atributosSeleccionados[$valor->atributo->id_atributo]) && 
                                                          $atributosSeleccionados[$valor->atributo->id_atributo] == $valor->id_atributo_valor ? 'checked' : '' }}
                                                       required>
                                                <label class="form-check-label" for="atributo_{{ $valor->id_atributo_valor }}">
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
    
    // Variables para controlar el estado
    let tieneImagenOriginal = '{{ $variacion->vImagen ? 'true' : 'false' }}' === 'true';
    let archivoOriginal = null;
    let archivoNuevo = null;
    let imagenEliminada = false;
    
    // Guardar el estado original antes de cualquier interacción
    if (imagenInput.files && imagenInput.files[0]) {
        archivoOriginal = imagenInput.files[0];
    }
    
    // Manejar cuando el usuario hace clic en el input de imagen
    imagenInput.addEventListener('click', function() {
        // Guardar el archivo actual como original antes de que el usuario interactúe
        if (this.files && this.files[0]) {
            archivoOriginal = this.files[0];
        }
    });
    
    // Manejar cambio en el input de archivo
    imagenInput.addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            const file = this.files[0];
            archivoNuevo = file;
            
            // Desmarcar checkbox de mantener imagen
            if (usarImagenActualCheckbox) {
                usarImagenActualCheckbox.checked = false;
                mantenerImagenHidden.value = '0';
            }
            
            // Mostrar vista previa de nueva imagen
            mostrarNuevaImagenPreview(file);
        } else {
            // El usuario canceló la selección
            if (archivoNuevo) {
                // Si ya había seleccionado una nueva imagen, mantenerla
                restaurarArchivoEnInput(archivoNuevo);
                mostrarNuevaImagenPreview(archivoNuevo);
                
                if (usarImagenActualCheckbox) {
                    usarImagenActualCheckbox.checked = false;
                    mantenerImagenHidden.value = '0';
                }
            } else if (archivoOriginal && !imagenEliminada) {
                // Restaurar el archivo original
                restaurarArchivoEnInput(archivoOriginal);
                
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
    
    // Manejar el checkbox de mantener imagen
    if (usarImagenActualCheckbox) {
        usarImagenActualCheckbox.addEventListener('change', function() {
            if (this.checked) {
                mantenerImagenHidden.value = '1';
                archivoNuevo = null;
                
                // Limpiar vista previa de nueva imagen
                if (nuevaImagenContainer) {
                    nuevaImagenContainer.innerHTML = '';
                }
                
                // Limpiar el input de archivo
                imagenInput.value = '';
            } else {
                mantenerImagenHidden.value = '0';
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
                
                // Limpiar el input de archivo
                imagenInput.value = '';
                
                // Ocultar la imagen actual
                if (currentImagePreview) {
                    currentImagePreview.style.display = 'none';
                }
                
                // Desmarcar checkbox de mantener imagen
                if (usarImagenActualCheckbox) {
                    usarImagenActualCheckbox.checked = false;
                }
                
                // Limpiar cualquier vista previa de nueva imagen
                if (nuevaImagenContainer) {
                    nuevaImagenContainer.innerHTML = '';
                }
                
                // Mostrar mensaje de que la imagen será eliminada
                const mensajeEliminar = document.createElement('div');
                mensajeEliminar.className = 'alert alert-warning mt-2';
                mensajeEliminar.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i> La imagen actual será eliminada al guardar los cambios.';
                
                if (currentImagePreview && currentImagePreview.parentNode) {
                    currentImagePreview.parentNode.insertBefore(mensajeEliminar, currentImagePreview.nextSibling);
                }
                
                // Ocultar el botón de eliminar
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
            // Si ya se eliminó la imagen, limpiar todo
            imagenInput.value = '';
            if (nuevaImagenContainer) {
                nuevaImagenContainer.innerHTML = '';
            }
        }
    };
    
    // Validación de atributos y manejo de imagen al enviar
    form.addEventListener('submit', function(e) {
        const atributosRadios = document.querySelectorAll('input[type="radio"][name^="atributos"]:checked');
        const atributosRequeridos = document.querySelectorAll('.border.rounded').length;
        
        if (atributosRadios.length !== atributosRequeridos) {
            e.preventDefault();
            alert('Debes seleccionar un valor para cada atributo.');
            return false;
        }
        
        // Si hay un archivo nuevo pero el input está vacío, restaurarlo
        if (archivoNuevo && (!imagenInput.files || imagenInput.files.length === 0)) {
            restaurarArchivoEnInput(archivoNuevo);
        }
        
        // Si se eliminó la imagen explícitamente, asegurar que el input esté vacío
        if (imagenEliminada && imagenInput.files && imagenInput.files.length > 0) {
            imagenInput.value = '';
        }
        
        // Si no hay archivo nuevo y no se eliminó, asegurar que se mantenga la imagen
        if (!archivoNuevo && !imagenEliminada && usarImagenActualCheckbox) {
            mantenerImagenHidden.value = '1';
        }
    });
});
</script>
@endsection