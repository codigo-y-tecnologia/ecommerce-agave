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
                                    <small class="form-text text-muted">
                                        Código único de identificación (SKU es tu código de barras)
                                    </small>
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
                                    <input type="number" name="dPeso" id="dPeso" 
                                           class="form-control @error('dPeso') is-invalid @enderror"
                                           value="{{ old('dPeso') }}" min="0" step="0.01"
                                           placeholder="Ej: 1.25">
                                    @error('dPeso')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Peso total del producto (botella + empaque)
                                    </small>
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
                                        <option value="Otro" {{ old('vClase_envio') == 'Otro' ? 'selected' : '' }}>Otro</option>
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
                                        Imagen de la Valoración <small class="text-muted">(Formatos: JPG, PNG, GIF, WebP, BMP, SVG)</small>
                                    </label>
                                    <input type="file" name="imagen" id="imagen" 
                                        class="form-control @error('imagen') is-invalid @enderror"
                                        accept=".jpg,.jpeg,.png,.gif,.webp,.bmp,.svg,.tiff,.ico,.heic,.heif">
                                    @error('imagen')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Imagen específica para esta valoración (opcional, máximo 5MB)
                                    </small>
                                    <!-- Contenedor para vista previa -->
                                    <div id="preview-container" class="mt-2"></div>
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
                                                       {{ old('atributos.' . $valor->atributo->id_atributo) == $valor->id_atributo_valor ? 'checked' : '' }}
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
                            
                            <div class="alert alert-warning small mt-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Nota:</strong> Debes seleccionar un valor para cada atributo listado.
                            </div>
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
    
    // Manejar la selección de imagen
    imagenInput.addEventListener('change', function(e) {
        // Solo procesar si realmente seleccionó un archivo
        if (this.files && this.files[0]) {
            const file = this.files[0];
            
            // Leer el archivo como Data URL
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const imagenDataURL = e.target.result;
                
                // Mostrar vista previa
                previewContainer.innerHTML = '';
                const img = document.createElement('img');
                img.src = imagenDataURL;
                img.style.maxWidth = '200px';
                img.style.maxHeight = '200px';
                img.style.objectFit = 'cover';
                img.style.borderRadius = '8px';
                img.style.marginTop = '10px';
                img.style.border = '1px solid #dee2e6';
                
                // Botón para quitar imagen
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'btn btn-danger btn-sm mt-2';
                removeBtn.innerHTML = '<i class="fas fa-trash me-1"></i> Quitar imagen';
                removeBtn.onclick = function() {
                    imagenInput.value = '';
                    previewContainer.innerHTML = '';
                };
                
                previewContainer.appendChild(img);
                previewContainer.appendChild(removeBtn);
            };
            
            reader.readAsDataURL(file);
        }
    });
    
    // Eliminar el auto-generar SKU para que el usuario pueda escribir su propio SKU
    // El campo estará vacío por defecto
    
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

/* Estilos para campos de entrada */
.form-control:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.form-control::placeholder {
    color: #6c757d;
    opacity: 0.7;
}
</style>
@endsection