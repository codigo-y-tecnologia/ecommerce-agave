@extends('layouts.app')

@section('title', 'Registrar Nuevo Producto')
@section('content')
<div class="container">
    <h1><i class="fas fa-plus-circle me-2"></i>Registrar Producto</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data" id="productoForm">
        @csrf

        <!-- INFORMACIÓN BÁSICA DEL PRODUCTO -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información Básica</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="vCodigo_barras" class="form-label fw-bold">
                                Código de barras <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="vCodigo_barras" id="vCodigo_barras" 
                                   class="form-control @error('vCodigo_barras') is-invalid @enderror"
                                   value="{{ old('vCodigo_barras') }}" 
                                   maxlength="20" 
                                   required>
                            @error('vCodigo_barras')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Ej: 7501001234567 (solo números)</small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="vNombre" class="form-label fw-bold">
                                Nombre del producto <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="vNombre" id="vNombre" 
                                   class="form-control @error('vNombre') is-invalid @enderror" 
                                   value="{{ old('vNombre') }}" 
                                   maxlength="100" 
                                   required>
                            @error('vNombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="dPrecio_compra" class="form-label fw-bold">
                                Precio de compra
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="dPrecio_compra" id="dPrecio_compra" 
                                       class="form-control @error('dPrecio_compra') is-invalid @enderror"
                                       value="{{ old('dPrecio_compra') }}" 
                                       step="0.01" min="0">
                                @error('dPrecio_compra')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="dPrecio_venta" class="form-label fw-bold">
                                Precio de venta <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="dPrecio_venta" id="dPrecio_venta" 
                                       class="form-control @error('dPrecio_venta') is-invalid @enderror"
                                       value="{{ old('dPrecio_venta') }}" 
                                       required step="0.01" min="0">
                                @error('dPrecio_venta')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="iStock" class="form-label fw-bold">
                                Stock inicial <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="iStock" id="iStock" 
                                   class="form-control @error('iStock') is-invalid @enderror"
                                   value="{{ old('iStock') }}" 
                                   required min="0">
                            @error('iStock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CATEGORÍA Y MARCA -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-tags me-2"></i>Categorización</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="id_categoria" class="form-label fw-bold">
                                Categoría <span class="text-danger">*</span>
                            </label>
                            <select name="id_categoria" id="id_categoria" 
                                    class="form-select @error('id_categoria') is-invalid @enderror" 
                                    required>
                                <option value="">Seleccionar categoría</option>
                                @php
                                    function mostrarCategoriasJerarquicamente($categorias, $nivel = 0, $oldValue = null)
                                    {
                                        foreach($categorias as $categoria) {
                                            $prefijo = str_repeat('&nbsp;&nbsp;&nbsp;', $nivel);
                                            $icono = '';
                                            
                                            if ($nivel == 0) {
                                                $icono = '🏠 ';
                                            } elseif ($nivel == 1) {
                                                $icono = '↳ ';
                                            } elseif ($nivel >= 2) {
                                                $icono = str_repeat('↳&nbsp;', $nivel);
                                            }
                                            
                                            $selected = ($oldValue == $categoria->id_categoria) ? 'selected' : '';
                                            
                                            echo '<option value="' . $categoria->id_categoria . '" ' . $selected . '>' .
                                                 $prefijo . $icono . htmlspecialchars($categoria->vNombre) . 
                                                 '</option>';
                                            
                                            if ($categoria->hijos && $categoria->hijos->count() > 0) {
                                                mostrarCategoriasJerarquicamente($categoria->hijos, $nivel + 1, $oldValue);
                                            }
                                        }
                                    }
                                    
                                    // Pasar el valor old si existe
                                    $oldCategoria = old('id_categoria');
                                    $categoriasRaiz = $categorias->where('id_categoria_padre', null)->where('bActivo', true);
                                @endphp
                                
                                @php
                                    mostrarCategoriasJerarquicamente($categoriasRaiz, 0, $oldCategoria);
                                @endphp
                            </select>
                            @error('id_categoria')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Selecciona la categoría principal o subcategoría para este producto
                            </small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="id_marca" class="form-label fw-bold">
                                Marca <span class="text-danger">*</span>
                            </label>
                            <select name="id_marca" id="id_marca" 
                                    class="form-select @error('id_marca') is-invalid @enderror" 
                                    required>
                                <option value="">Seleccionar marca</option>
                                @foreach ($marcas as $marca)
                                    <option value="{{ $marca->id_marca }}" 
                                        {{ old('id_marca') == $marca->id_marca ? 'selected' : '' }}>
                                        {{ $marca->vNombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_marca')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ATRIBUTOS (OPCIONAL) -->
        @if($atributos && $atributos->count() > 0)
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="fas fa-tags me-2"></i>Atributos (Opcional)</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Los atributos son opcionales. Puedes asignarlos después de crear el producto.
                </div>
                
                <div class="row">
                    @foreach($atributos as $atributo)
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 border">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 fw-bold">{{ $atributo->vNombre }}</h6>
                                    @if($atributo->tDescripcion)
                                        <p class="small text-muted mb-0 mt-1">{{ $atributo->tDescripcion }}</p>
                                    @endif
                                </div>
                                <div class="card-body">
                                    @if($atributo->valoresActivos && $atributo->valoresActivos->count() > 0)
                                        @foreach($atributo->valoresActivos as $valor)
                                            <div class="form-check mb-2">
                                                <input type="checkbox" 
                                                       class="form-check-input" 
                                                       name="atributos[{{ $atributo->id_atributo }}][]"
                                                       value="{{ $valor->id_atributo_valor }}"
                                                       id="atributo_{{ $atributo->id_atributo }}_valor_{{ $valor->id_atributo_valor }}">
                                                <label class="form-check-label" for="atributo_{{ $atributo->id_atributo }}_valor_{{ $valor->id_atributo_valor }}">
                                                    {{ $valor->vValor }}
                                                    @if($valor->dPrecio_extra > 0)
                                                        <small class="text-success">(+${{ number_format($valor->dPrecio_extra, 2) }})</small>
                                                    @endif
                                                </label>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="alert alert-warning py-2 mb-0">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            No hay valores disponibles
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- IMÁGENES Y DESCRIPCIÓN -->
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="fas fa-images me-2"></i>Imágenes y Descripción</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="imagenes" class="form-label fw-bold">
                                Imágenes del producto (Máximo 6)
                            </label>
                            <input type="file" name="imagenes[]" id="imagenes" 
                                   class="form-control @error('imagenes') is-invalid @enderror" 
                                   multiple accept="image/*">
                            @error('imagenes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Formatos: JPG, JPEG, PNG, GIF, WEBP. Máximo 2MB por imagen.
                            </small>
                            <div id="preview-container" class="mt-2 row"></div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="tDescripcion_corta" class="form-label fw-bold">
                                Descripción corta
                            </label>
                            <textarea name="tDescripcion_corta" id="tDescripcion_corta" 
                                      class="form-control @error('tDescripcion_corta') is-invalid @enderror" 
                                      maxlength="255" 
                                      rows="3">{{ old('tDescripcion_corta') }}</textarea>
                            @error('tDescripcion_corta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="form-group mb-3">
                    <label for="tDescripcion_larga" class="form-label fw-bold">
                        Descripción detallada
                    </label>
                    <textarea name="tDescripcion_larga" id="tDescripcion_larga" 
                              class="form-control @error('tDescripcion_larga') is-invalid @enderror" 
                              rows="5">{{ old('tDescripcion_larga') }}</textarea>
                    @error('tDescripcion_larga')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group mb-3">
                    <label class="form-label fw-bold">Etiquetas (Opcional)</label>
                    <div class="row">
                        @foreach ($etiquetas as $etiqueta)
                            <div class="col-md-3 col-6 mb-2">
                                <div class="form-check">
                                    <input type="checkbox" 
                                           name="etiquetas[]" 
                                           value="{{ $etiqueta->id_etiqueta }}" 
                                           class="form-check-input"
                                           {{ is_array(old('etiquetas')) && in_array($etiqueta->id_etiqueta, old('etiquetas')) ? 'checked' : '' }}
                                           id="etiqueta_{{ $etiqueta->id_etiqueta }}">
                                    <label class="form-check-label" for="etiqueta_{{ $etiqueta->id_etiqueta }}">
                                        {{ $etiqueta->vNombre }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="form-group mb-3">
                    <div class="form-check form-switch">
                        <input type="checkbox" name="bActivo" id="bActivo" 
                               class="form-check-input" value="1" 
                               {{ old('bActivo', true) ? 'checked' : '' }}>
                        <label for="bActivo" class="form-check-label fw-bold">
                            Producto activo
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- BOTONES DE ACCIÓN -->
        <div class="d-flex gap-2 mb-4">
            <button type="submit" class="btn btn-success btn-lg px-4">
                <i class="fas fa-save me-2"></i> Guardar Producto
            </button>
            <a href="{{ route('productos.index') }}" class="btn btn-secondary btn-lg px-4">
                <i class="fas fa-times me-2"></i> Cancelar
            </a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Preview de imágenes
    document.getElementById('imagenes').addEventListener('change', function(e) {
        const previewContainer = document.getElementById('preview-container');
        previewContainer.innerHTML = '';
        
        const files = e.target.files;
        const maxFiles = 6;
        
        if (files.length > maxFiles) {
            alert('Solo puedes seleccionar máximo ' + maxFiles + ' imágenes.');
            this.value = '';
            return;
        }
        
        for (let i = 0; i < files.length && i < maxFiles; i++) {
            const file = files[i];
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-4 col-md-3 mb-2';
                col.innerHTML = `
                    <div class="card border">
                        <img src="${e.target.result}" 
                             class="card-img-top" 
                             style="height: 100px; object-fit: cover;"
                             alt="Previsualización">
                        <div class="card-body p-2 text-center">
                            <small class="text-muted">${file.name.length > 15 ? file.name.substring(0, 15) + '...' : file.name}</small>
                        </div>
                    </div>
                `;
                previewContainer.appendChild(col);
            }
            
            reader.readAsDataURL(file);
        }
    });

    // Validación de formulario antes de enviar
    document.getElementById('productoForm').addEventListener('submit', function(e) {
        // Remover clases de error anteriores
        document.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
        
        // Validar campos requeridos
        const codigoBarras = document.getElementById('vCodigo_barras');
        const nombre = document.getElementById('vNombre');
        const precioVenta = document.getElementById('dPrecio_venta');
        const stock = document.getElementById('iStock');
        const categoria = document.getElementById('id_categoria');
        const marca = document.getElementById('id_marca');
        
        let isValid = true;
        
        if (!codigoBarras.value.trim()) {
            codigoBarras.classList.add('is-invalid');
            isValid = false;
        }
        
        if (!nombre.value.trim()) {
            nombre.classList.add('is-invalid');
            isValid = false;
        }
        
        if (!precioVenta.value || parseFloat(precioVenta.value) < 0) {
            precioVenta.classList.add('is-invalid');
            isValid = false;
        }
        
        if (!stock.value || parseInt(stock.value) < 0) {
            stock.classList.add('is-invalid');
            isValid = false;
        }
        
        if (!categoria.value) {
            categoria.classList.add('is-invalid');
            isValid = false;
        }
        
        if (!marca.value) {
            marca.classList.add('is-invalid');
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
            alert('Por favor completa todos los campos requeridos correctamente.');
        }
    });
});
</script>

<style>
.card {
    border: 1px solid #dee2e6;
}

.card-header {
    border-bottom: 1px solid rgba(0,0,0,0.125);
}

#preview-container img {
    transition: all 0.3s ease;
}

#preview-container img:hover {
    transform: scale(1.05);
}

/* Estilos para la jerarquía de categorías */
select option.categoria-raiz {
    font-weight: bold;
    color: #2c3e50;
    padding: 8px;
    background-color: #f8f9fa;
}

select option.categoria-hijo {
    color: #34495e;
    padding-left: 20px;
    font-size: 14px;
}

select option.categoria-subhijo {
    color: #7f8c8d;
    padding-left: 40px;
    font-size: 13px;
    font-style: italic;
}

select option[value=""] {
    color: #95a5a6;
    font-style: italic;
}

/* Mejorar la visualización de los iconos */
select option {
    white-space: pre;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
</style>
@endsection