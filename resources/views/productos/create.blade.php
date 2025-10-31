@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Registrar Producto</h1>

    <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group mb-3">
            <label for="vCodigo_barras">Código de barras</label>
            <input type="text" name="vCodigo_barras" id="vCodigo_barras" class="form-control @error('vCodigo_barras') is-invalid @enderror"
                   value="{{ old('vCodigo_barras') }}" maxlength="20" required oninput="soloNumeros(this)">
            @error('vCodigo_barras')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="vNombre">Nombre del producto</label>
            <input type="text" name="vNombre" id="vNombre" class="form-control @error('vNombre') is-invalid @enderror" 
                value="{{ old('vNombre') }}" maxlength="100" required oninput="removerError(this)">
            @error('vNombre')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="tDescripcion_corta">Descripción corta</label>
            <textarea name="tDescripcion_corta" id="tDescripcion_corta" class="form-control @error('tDescripcion_corta') is-invalid @enderror" 
                      maxlength="255" rows="3">{{ old('tDescripcion_corta') }}</textarea>
            @error('tDescripcion_corta')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">Máximo 255 caracteres</small>
        </div>

        <div class="form-group mb-3">
            <label for="tDescripcion_larga">Descripción larga</label>
            <textarea name="tDescripcion_larga" id="tDescripcion_larga" class="form-control @error('tDescripcion_larga') is-invalid @enderror" 
                      rows="5">{{ old('tDescripcion_larga') }}</textarea>
            @error('tDescripcion_larga')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="dPrecio_compra">Precio de compra</label>
            <input type="text" name="dPrecio_compra" id="dPrecio_compra" class="form-control @error('dPrecio_compra') is-invalid @enderror"
                   value="{{ old('dPrecio_compra') }}" oninput="soloNumerosYDecimal(this)">
            @error('dPrecio_compra')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="dPrecio_venta">Precio de venta</label>
            <input type="text" name="dPrecio_venta" id="dPrecio_venta" class="form-control @error('dPrecio_venta') is-invalid @enderror"
                   value="{{ old('dPrecio_venta') }}" required oninput="soloNumerosYDecimal(this)">
            @error('dPrecio_venta')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="iStock">Stock</label>
            <input type="text" name="iStock" id="iStock" class="form-control @error('iStock') is-invalid @enderror"
                   value="{{ old('iStock') }}" required oninput="soloNumeros(this)">
            @error('iStock')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="id_categoria">Categoría</label>
            <select name="id_categoria" id="id_categoria" class="form-control @error('id_categoria') is-invalid @enderror" required>
                <option value="">Seleccionar</option>
                @foreach ($categorias as $categoria)
                    <option value="{{ $categoria->id_categoria }}" {{ old('id_categoria') == $categoria->id_categoria ? 'selected' : '' }}>
                        {{ $categoria->vNombre }}
                    </option>
                @endforeach
            </select>
            @error('id_categoria')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="id_marca">Marca</label>
            <select name="id_marca" id="id_marca" class="form-control @error('id_marca') is-invalid @enderror" required>
                <option value="">Seleccionar</option>
                @foreach ($marcas as $marca)
                    <option value="{{ $marca->id_marca }}" {{ old('id_marca') == $marca->id_marca ? 'selected' : '' }}>
                        {{ $marca->vNombre }}
                    </option>
                @endforeach
            </select>
            @error('id_marca')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- NUEVO CAMPO PARA IMÁGENES -->
        <div class="form-group mb-3">
            <label for="imagenes">Imágenes del producto (Máximo 6 imágenes)</label>
            <input type="file" name="imagenes[]" id="imagenes" class="form-control @error('imagenes') is-invalid @enderror" 
                   multiple accept="image/*">
            @error('imagenes')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            @error('imagenes.*')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">
                Formatos permitidos: JPG, JPEG, PNG, GIF, WEBP. Máximo 2MB por imagen.
                Las imágenes se guardarán en una carpeta con el ID del producto.
            </small>
            <div id="preview-container" class="mt-2 row"></div>
        </div>

        <div class="form-group mb-3">
            <label>Etiquetas</label><br>
            @foreach ($etiquetas as $etiqueta)
                <label class="me-3">
                    <input type="checkbox" name="etiquetas[]" value="{{ $etiqueta->id_etiqueta }}" 
                           {{ is_array(old('etiquetas')) && in_array($etiqueta->id_etiqueta, old('etiquetas')) ? 'checked' : '' }}>
                    {{ $etiqueta->vNombre }}
                </label>
            @endforeach
            @error('etiquetas')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-3">
            <div class="form-check form-switch">
                <input type="checkbox" name="bActivo" id="bActivo" class="form-check-input" value="1" 
                       {{ old('bActivo', true) ? 'checked' : '' }}>
                <label for="bActivo" class="form-check-label">Producto activo</label>
            </div>
            <small class="form-text text-muted">Si está desactivado, el producto no se mostrará en la tienda</small>
        </div>

        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="{{ route('productos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
    // Solo números (para código de barras y stock)
    function soloNumeros(input) {
        input.value = input.value.replace(/[^0-9]/g, '');
        // Remover clase de error cuando el usuario escribe
        input.classList.remove('is-invalid');
    }
    function removerError(input) {
        // Remover clase de error cuando el usuario escribe
        input.classList.remove('is-invalid');
    }

    // Números y punto decimal (para precios)
    function soloNumerosYDecimal(input) {
        // Permite números y un solo punto decimal
        input.value = input.value.replace(/[^0-9.]/g, '');
        
        // Asegura que solo haya un punto decimal
        let puntos = input.value.split('.').length - 1;
        if (puntos > 1) {
            input.value = input.value.slice(0, -1);
        }
        
        // Limita a 2 decimales después del punto
        if (input.value.includes('.')) {
            let partes = input.value.split('.');
            if (partes[1].length > 2) {
                partes[1] = partes[1].substring(0, 2);
                input.value = partes[0] + '.' + partes[1];
            }
        }
        
        // Remover clase de error cuando el usuario escribe
        input.classList.remove('is-invalid');
    }

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
                    <div class="card">
                        <img src="${e.target.result}" class="card-img-top" style="height: 100px; object-fit: cover;">
                        <div class="card-body p-2">
                            <small class="text-muted">${file.name}</small>
                        </div>
                    </div>
                `;
                previewContainer.appendChild(col);
            }
            
            reader.readAsDataURL(file);
        }
    });

    // Remover error cuando se selecciona una opción en los selects
    document.addEventListener('DOMContentLoaded', function() {
        const selects = document.querySelectorAll('select');
        selects.forEach(select => {
            select.addEventListener('change', function() {
                this.classList.remove('is-invalid');
            });
        });

        // Remover error cuando se escribe en textareas
        const textareas = document.querySelectorAll('textarea');
        textareas.forEach(textarea => {
            textarea.addEventListener('input', function() {
                this.classList.remove('is-invalid');
            });
        });
    });
</script>
@endsection