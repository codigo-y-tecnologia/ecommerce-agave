@extends('layouts.app')

@section('title', 'Nueva Valoración - ' . $producto->vNombre)
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1><i class="fas fa-plus-circle me-2"></i>Nueva Valoración</h1>
            <p class="text-muted">Producto: {{ $producto->vNombre }}</p>
        </div>
        <div>
            <a href="{{ route('valoraciones.show', $producto->id_producto) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Cancelar
            </a>
        </div>
    </div>

    <form action="{{ route('valoraciones.store', $producto->id_producto) }}" method="POST" enctype="multipart/form-data">
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
                                        SKU * <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="vSKU" id="vSKU" 
                                           class="form-control @error('vSKU') is-invalid @enderror"
                                           value="{{ old('vSKU') }}" required
                                           placeholder="Ej: MEZ-750ML-REP-01">
                                    @error('vSKU')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Código único de identificación
                                    </small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="vCodigo_barras" class="form-label fw-bold">
                                        Código de Barras (GTIN/UPC/EAN/ISBN)
                                    </label>
                                    <input type="text" name="vCodigo_barras" id="vCodigo_barras" 
                                           class="form-control @error('vCodigo_barras') is-invalid @enderror"
                                           value="{{ old('vCodigo_barras') }}"
                                           placeholder="Ej: 1234567890123">
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
                                           value="{{ old('dPrecio') }}" required min="0" step="0.01"
                                           placeholder="Ej: 299.99">
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
                                           value="{{ old('dPrecio_oferta') }}" min="0" step="0.01"
                                           placeholder="Ej: 249.99">
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
                                           value="{{ old('iStock', 0) }}" required min="0"
                                           placeholder="Ej: 50">
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
                                           value="{{ old('dPeso') }}" required min="0" step="0.01"
                                           placeholder="Ej: 1.25">
                                    @error('dPeso')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror>
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
                                        <option value="">Seleccionar clase</option>
                                        <option value="Estándar" {{ old('vClase_envio') == 'Estándar' ? 'selected' : '' }}>Estándar</option>
                                        <option value="Fragil" {{ old('vClase_envio') == 'Fragil' ? 'selected' : '' }}>Frágil</option>
                                        <option value="Liquido" {{ old('vClase_envio') == 'Liquido' ? 'selected' : '' }}>Líquido</option>
                                        <option value="Especial" {{ old('vClase_envio') == 'Especial' ? 'selected' : '' }}>Especial</option>
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
                                    <small class="form-text text-muted">
                                        Imagen específica para esta valoración (opcional)
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
                            @enderror>
                        </div>

                        <div class="form-group mb-3">
                            <div class="form-check form-switch">
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
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-tags me-2"></i>Atributos de la Valoración</h5>
                    </div>
                    <div class="card-body">
                        @if(count($atributos) > 0)
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
                                                       {{ old('atributos.' . $valor->atributo->id_atributo) == $valor->id_atributo_valor ? 'checked' : '' }}>
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
                                Este producto no tiene atributos asignados. Primero asigna atributos desde la página del producto.
                            </div>
                            <a href="{{ route('productos.asignar-atributos', $producto->id_producto) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-tags me-1"></i> Asignar Atributos
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success btn-lg">
                <i class="fas fa-save me-2"></i> Guardar Valoración
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
});

// Auto-generar SKU
document.addEventListener('DOMContentLoaded', function() {
    const skuInput = document.getElementById('vSKU');
    const codigoBase = '{{ $producto->vCodigo_barras }}';
    
    if (!skuInput.value && codigoBase) {
        // Generar SKU automático
        const timestamp = Date.now().toString().slice(-4);
        skuInput.value = codigoBase + '-' + timestamp;
    }
});
</script>
@endsection