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
                                    <label for="imagen" class="form-label fw-bold">
                                        Imagen de la Valoración <small class="text-muted">(Formatos: JPG, PNG, GIF, WebP, BMP, SVG)</small>
                                    </label>
                                    <input type="file" name="imagen" id="imagen" 
                                        class="form-control @error('imagen') is-invalid @enderror"
                                        accept=".jpg,.jpeg,.png,.gif,.webp,.bmp,.svg,.tiff,.ico,.heic,.heif">
                                    @error('imagen')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror>
                                    
                                    <!-- Campos ocultos para controlar la imagen -->
                                    <input type="hidden" name="mantener_imagen" id="mantener_imagen_hidden" value="1">
                                    
                                    @if($variacion->vImagen)
                                        <div class="mt-2">
                                            <img src="{{ asset($variacion->vImagen) }}" 
                                                alt="Imagen actual"
                                                style="max-width: 100px; max-height: 100px; object-fit: cover; border-radius: 4px;"
                                                id="current-image-preview">
                                            <div class="form-check mt-1">
                                                <input type="checkbox" name="usar_imagen_actual" id="usar_imagen_actual" 
                                                    class="form-check-input" value="1" checked>
                                                <label for="usar_imagen_actual" class="form-check-label small">
                                                    Mantener esta imagen
                                                </label>
                                            </div>
                                            <div class="mt-2" id="nueva-imagen-preview"></div>
                                        </div>
                                    @else
                                        <div class="mt-2 text-muted small">
                                            <i class="fas fa-image me-1"></i> No hay imagen asignada a esta valoración
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
    const nuevaImagenPreview = document.getElementById('nueva-imagen-preview');
    
    // Variables para almacenar imagen temporal
    let nuevaImagenDataURL = null;
    
    if (imagenInput) {
        // Manejar cambio en el input de archivo
        imagenInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                
                // Desmarcar checkbox de mantener imagen
                if (usarImagenActualCheckbox) {
                    usarImagenActualCheckbox.checked = false;
                    mantenerImagenHidden.value = '0';
                }
                
                // Leer la imagen como Data URL
                const reader = new FileReader();
                reader.onload = function(e) {
                    nuevaImagenDataURL = e.target.result;
                    
                    // Mostrar vista previa de nueva imagen
                    if (nuevaImagenPreview) {
                        nuevaImagenPreview.innerHTML = `
                            <div class="mt-2">
                                <strong>Nueva imagen seleccionada:</strong>
                                <img src="${nuevaImagenDataURL}" 
                                     style="max-width: 100px; max-height: 100px; object-fit: cover; border-radius: 4px; margin-top: 5px;"
                                     alt="Nueva imagen">
                                <button type="button" class="btn btn-danger btn-sm mt-2" onclick="cancelarNuevaImagen()">
                                    <i class="fas fa-times me-1"></i> Cancelar cambio
                                </button>
                            </div>
                        `;
                    }
                };
                reader.readAsDataURL(file);
            } else {
                // Si canceló la selección, restaurar la imagen original
                if (usarImagenActualCheckbox && usarImagenActualCheckbox.checked) {
                    mantenerImagenHidden.value = '1';
                    if (nuevaImagenPreview) {
                        nuevaImagenPreview.innerHTML = '';
                    }
                    nuevaImagenDataURL = null;
                }
            }
        });
        
        // Manejar el checkbox de mantener imagen
        if (usarImagenActualCheckbox) {
            usarImagenActualCheckbox.addEventListener('change', function() {
                mantenerImagenHidden.value = this.checked ? '1' : '0';
                if (this.checked) {
                    // Limpiar nueva imagen si se vuelve a marcar "mantener"
                    nuevaImagenDataURL = null;
                    if (nuevaImagenPreview) {
                        nuevaImagenPreview.innerHTML = '';
                    }
                    imagenInput.value = '';
                }
            });
        }
    }
    
    // Función global para cancelar nueva imagen
    window.cancelarNuevaImagen = function() {
        nuevaImagenDataURL = null;
        imagenInput.value = '';
        
        if (usarImagenActualCheckbox) {
            usarImagenActualCheckbox.checked = true;
            mantenerImagenHidden.value = '1';
        }
        
        if (nuevaImagenPreview) {
            nuevaImagenPreview.innerHTML = '';
        }
    };
    
    // Validación de atributos
    document.getElementById('valoracionForm').addEventListener('submit', function(e) {
        const atributosRadios = document.querySelectorAll('input[type="radio"][name^="atributos"]:checked');
        const atributosRequeridos = document.querySelectorAll('.border.rounded').length;
        
        if (atributosRadios.length !== atributosRequeridos) {
            e.preventDefault();
            alert('Debes seleccionar un valor para cada atributo.');
            return false;
        }
    });
});
</script>
@endsection