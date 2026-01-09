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
          method="POST" enctype="multipart/form-data">
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
                                        SKU * <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="vSKU" id="vSKU" 
                                           class="form-control @error('vSKU') is-invalid @enderror"
                                           value="{{ old('vSKU', $variacion->vSKU) }}" required>
                                    @error('vSKU')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="vCodigo_barras" class="form-label fw-bold">
                                        Código de Barras (GTIN/UPC/EAN/ISBN)
                                    </label>
                                    <input type="text" name="vCodigo_barras" id="vCodigo_barras" 
                                           class="form-control @error('vCodigo_barras') is-invalid @enderror"
                                           value="{{ old('vCodigo_barras', $variacion->vCodigo_barras) }}">
                                    @error('vCodigo_barras')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="dPrecio" class="form-label fw-bold">
                                        Precio * <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" name="dPrecio" id="dPrecio" 
                                           class="form-control @error('dPrecio') is-invalid @enderror"
                                           value="{{ old('dPrecio', $variacion->dPrecio) }}" required min="0" step="0.01">
                                    @error('dPrecio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="dPrecio_oferta" class="form-label fw-bold">
                                        Precio de Rebaja
                                    </label>
                                    <input type="number" name="dPrecio_oferta" id="dPrecio_oferta" 
                                           class="form-control @error('dPrecio_oferta') is-invalid @enderror"
                                           value="{{ old('dPrecio_oferta', $variacion->dPrecio_oferta) }}" min="0" step="0.01">
                                    @error('dPrecio_oferta')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="iStock" class="form-label fw-bold">
                                        Stock * <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" name="iStock" id="iStock" 
                                           class="form-control @error('iStock') is-invalid @enderror"
                                           value="{{ old('iStock', $variacion->iStock) }}" required min="0">
                                    @error('iStock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="dPeso" class="form-label fw-bold">
                                        Peso (kg) *
                                    </label>
                                    <input type="number" name="dPeso" id="dPeso" 
                                           class="form-control @error('dPeso') is-invalid @enderror"
                                           value="{{ old('dPeso', $variacion->dPeso) }}" required min="0" step="0.01">
                                    @error('dPeso')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror>
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
                                        <option value="">Seleccionar clase</option>
                                        <option value="Estándar" {{ old('vClase_envio', $variacion->vClase_envio) == 'Estándar' ? 'selected' : '' }}>Estándar</option>
                                        <option value="Fragil" {{ old('vClase_envio', $variacion->vClase_envio) == 'Fragil' ? 'selected' : '' }}>Frágil</option>
                                        <option value="Liquido" {{ old('vClase_envio', $variacion->vClase_envio) == 'Liquido' ? 'selected' : '' }}>Líquido</option>
                                        <option value="Especial" {{ old('vClase_envio', $variacion->vClase_envio) == 'Especial' ? 'selected' : '' }}>Especial</option>
                                    </select>
                                    @error('vClase_envio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="imagen" class="form-label fw-bold">
                                        Imagen de la Valoración
                                    </label>
                                    <input type="file" name="imagen" id="imagen" 
                                           class="form-control @error('imagen') is-invalid @enderror"
                                           accept="image/*">
                                    @error('imagen')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror>
                                    @if($variacion->imagen_url)
                                        <div class="mt-2">
                                            <img src="{{ $variacion->imagen_url }}" 
                                                 alt="Imagen actual"
                                                 style="max-width: 100px; max-height: 100px; object-fit: cover; border-radius: 4px;">
                                            <small class="d-block text-muted">Imagen actual</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="tDescripcion" class="form-label fw-bold">
                                Descripción (Opcional)
                            </label>
                            <textarea name="tDescripcion" id="tDescripcion" 
                                      class="form-control @error('tDescripcion') is-invalid @enderror"
                                      rows="3">{{ old('tDescripcion', $variacion->tDescripcion) }}</textarea>
                            @error('tDescripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror>
                        </div>

                        <div class="form-group mb-3">
                            <div class="form-check form-switch">
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
                                <div class="mb-4">
                                    <label class="fw-bold mb-2">{{ $nombreAtributo }}</label>
                                    <div class="form-group">
                                        @foreach($valores as $valor)
                                            <div class="form-check mb-2">
                                                <input type="radio" 
                                                       name="atributos[{{ $valor->atributo->id_atributo }}]" 
                                                       id="atributo_{{ $valor->id_atributo_valor }}"
                                                       value="{{ $valor->id_atributo_valor }}"
                                                       class="form-check-input"
                                                       {{ isset($atributosSeleccionados[$valor->atributo->id_atributo]) && 
                                                          $atributosSeleccionados[$valor->atributo->id_atributo] == $valor->id_atributo_valor ? 'checked' : '' }}>
                                                <label class="form-check-label" for="atributo_{{ $valor->id_atributo_valor }}">
                                                    {{ $valor->vValor }}
                                                    @if($valor->pivot && $valor->pivot->dPrecio_extra > 0)
                                                        <small class="text-muted">
                                                            (+${{ number_format($valor->pivot->dPrecio_extra, 2) }})
                                                        </small>
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
// Preview de imagen
document.getElementById('imagen').addEventListener('change', function(e) {
    const previewContainer = document.getElementById('preview-container');
    if (previewContainer) {
        previewContainer.innerHTML = '';
        
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.maxWidth = '200px';
                img.style.maxHeight = '200px';
                img.style.objectFit = 'cover';
                img.style.borderRadius = '8px';
                img.style.marginTop = '10px';
                
                previewContainer.appendChild(img);
            }
            
            reader.readAsDataURL(this.files[0]);
        }
    }
});
</script>
@endsection