@extends('layouts.app')

@section('title', 'Editar Categoría')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2>Editar Categoría</h2>
                    <a href="{{ route('categorias.index') }}" class="btn btn-secondary btn-sm">← Volver</a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('categorias.update', $categoria) }}" method="POST" enctype="multipart/form-data" id="editForm">
                        @csrf 
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="vNombre" class="form-label">Nombre de la Categoría *</label>
                            <input type="text" class="form-control @error('vNombre') is-invalid @enderror" 
                                   id="vNombre" name="vNombre" 
                                   value="{{ old('vNombre', $categoria->vNombre) }}" required>
                            @error('vNombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Campo slug -->
                        <div class="mb-3">
                            <label for="vSlug" class="form-label">Slug (URL amigable) *</label>
                            <input type="text" class="form-control @error('vSlug') is-invalid @enderror" 
                                   id="vSlug" name="vSlug" 
                                   value="{{ old('vSlug', $categoria->vSlug) }}" required>
                            @error('vSlug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                URL para la categoría (ej: tequila-reposado)
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="id_categoria_padre" class="form-label">Categoría Padre</label>
                            <select class="form-control @error('id_categoria_padre') is-invalid @enderror" 
                                    id="id_categoria_padre" name="id_categoria_padre">
                                <option value="">-- Sin Categoría Padre (Categoría Raíz) --</option>
                                @foreach($categoriasPadre as $id => $nombre)
                                    <option value="{{ $id }}" {{ old('id_categoria_padre', $categoria->id_categoria_padre) == $id ? 'selected' : '' }}>
                                        {{ $nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_categoria_padre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="tDescripcion" class="form-label">Descripción</label>
                            <textarea class="form-control @error('tDescripcion') is-invalid @enderror" 
                                      id="tDescripcion" name="tDescripcion" rows="3">{{ old('tDescripcion', $categoria->tDescripcion) }}</textarea>
                            @error('tDescripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Estado</label>
                            <div class="form-check mt-2">
                                <input type="checkbox" class="form-check-input" 
                                       id="bActivo" name="bActivo" value="1"
                                       {{ old('bActivo', $categoria->bActivo) ? 'checked' : '' }}>
                                <label class="form-check-label" for="bActivo">
                                    Categoría activa
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Imagen de la Categoría</label>
                            
                            <!-- Sección para mostrar imagen actual -->
                            <div class="mb-3" id="currentImageSection">
                                @if($categoria->tiene_imagen)
                                    <div class="mb-2">
                                        <img src="{{ $categoria->imagen_url }}" 
                                             class="img-thumbnail" 
                                             style="width: 200px; height: 200px; object-fit: cover;"
                                             alt="{{ $categoria->vNombre }}"
                                             id="currentImage">
                                        <br>
                                        <small class="text-muted">Imagen actual</small>
                                    </div>
                                    
                                    <div class="form-check mb-2">
                                        <input type="checkbox" class="form-check-input" 
                                               id="eliminar_imagen" name="eliminar_imagen" value="1">
                                        <label class="form-check-label text-danger" for="eliminar_imagen">
                                            ❌ Eliminar imagen actual
                                        </label>
                                        <small class="form-text text-muted d-block">
                                            Marca esta opción solo si deseas eliminar permanentemente la imagen actual
                                        </small>
                                    </div>
                                @else
                                    <div class="border rounded p-4 mb-3 text-muted text-center">
                                        <div style="font-size: 2rem; margin-bottom: 10px;">📷</div>
                                        <p>No hay imagen cargada</p>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Preview de nueva imagen -->
                            <div class="mb-3" id="newImagePreviewSection" style="display: none;">
                                <div class="mb-2">
                                    <img id="newImagePreview" src="#" 
                                         class="img-thumbnail" 
                                         style="width: 200px; height: 200px; object-fit: cover;"
                                         alt="Preview de nueva imagen">
                                    <br>
                                    <small class="text-muted">Vista previa de nueva imagen seleccionada</small>
                                    <div class="mt-2">
                                       
                                        <small class="form-text text-muted d-block mt-1">
                                            Si cancelas, se mantendrá la imagen actual
                                        </small>
                                    </div>
                                </div>
                            </div>
                            
                            <label for="vImagen" class="form-label">Cambiar imagen (opcional)</label>
                            <div class="input-group">
                                <input type="file" class="form-control @error('vImagen') is-invalid @enderror" 
                                       id="vImagen" name="vImagen"
                                       accept="image/*">
                                <button type="button" class="btn btn-outline-secondary" onclick="resetFileInput()">
                                    🔄
                                </button>
                            </div>
                            @error('vImagen')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Selecciona una nueva imagen solo si deseas cambiar la actual. <br>
                                Deja en blanco para mantener la imagen actual. <br>
                                Si seleccionas un archivo y luego abres de nuevo el selector, <br>
                                puedes seleccionar el mismo u otro archivo sin problema. <br>
                                Formatos aceptados: JPG, JPEG, PNG, GIF, WebP. Tamaño máximo: 2MB
                            </small>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('categorias.index') }}" class="btn btn-secondary">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Actualizar Categoría
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Variables de estado
    let currentSelectedFile = null;
    let hasNewImage = false;
    
    // Obtener elementos del DOM
    const fileInput = document.getElementById('vImagen');
    const previewSection = document.getElementById('newImagePreviewSection');
    const deleteCheckbox = document.getElementById('eliminar_imagen');
    
    // Configurar evento change del input de archivo
    fileInput.addEventListener('change', function(event) {
        handleFileSelection(event);
    });
    
    // Función para manejar la selección de archivo
    function handleFileSelection(event) {
        const input = event.target;
        
        if (input.files && input.files[0]) {
            // Hay un archivo seleccionado
            currentSelectedFile = input.files[0];
            hasNewImage = true;
            
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const preview = document.getElementById('newImagePreview');
                preview.src = e.target.result;
                previewSection.style.display = 'block';
                
                // Si hay checkbox de eliminar, desmarcarlo
                if (deleteCheckbox) {
                    deleteCheckbox.checked = false;
                    deleteCheckbox.disabled = false;
                }
            }
            
            reader.readAsDataURL(input.files[0]);
        } else {
            // No hay archivo seleccionado (usuario canceló)
            // PERO mantenemos el archivo seleccionado anteriormente si existe
            if (currentSelectedFile) {
                // Restaurar el archivo seleccionado
                restoreSelectedFile();
            }
        }
    }
    
    // Función para restaurar el archivo seleccionado
    function restoreSelectedFile() {
        if (currentSelectedFile) {
            // Crear un nuevo DataTransfer para asignar el archivo
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(currentSelectedFile);
            fileInput.files = dataTransfer.files;
            
            // Forzar el evento change para mostrar el preview
            const event = new Event('change', { bubbles: true });
            fileInput.dispatchEvent(event);
        }
    }
    
    // Función para resetear el input de archivo (permitir nueva selección)
    function resetFileInput() {
        // Crear un nuevo input de archivo para resetearlo
        const newInput = document.createElement('input');
        newInput.type = 'file';
        newInput.name = 'vImagen';
        newInput.id = 'vImagen';
        newInput.className = 'form-control';
        newInput.accept = 'image/*';
        
        // Reemplazar el input viejo con el nuevo
        const parent = fileInput.parentNode;
        parent.replaceChild(newInput, fileInput);
        
        // Actualizar referencia y eventos
        document.getElementById('vImagen').addEventListener('change', function(event) {
            handleFileSelection(event);
        });
        
        // Resetear variables
        currentSelectedFile = null;
        hasNewImage = false;
        previewSection.style.display = 'none';
        
        // Habilitar checkbox si existe
        if (deleteCheckbox) {
            deleteCheckbox.disabled = false;
        }
    }
    
    // Función para cancelar la nueva imagen seleccionada
    function cancelNewImage() {
        // Resetear el input
        resetFileInput();
        
        // Resetear variables
        currentSelectedFile = null;
        hasNewImage = false;
    }
    
    // Función para manejar el checkbox de eliminar
    function handleDeleteCheckbox() {
        if (deleteCheckbox && deleteCheckbox.checked) {
            // Si marca eliminar:
            // 1. Deshabilitar input de nueva imagen
            fileInput.disabled = true;
            fileInput.value = '';
            
            // 2. Cancelar cualquier nueva imagen seleccionada
            previewSection.style.display = 'none';
            
            // 3. Resetear variables
            currentSelectedFile = null;
            hasNewImage = false;
        } else if (deleteCheckbox) {
            // Si desmarca eliminar:
            // 1. Habilitar input de nueva imagen
            fileInput.disabled = false;
        }
    }
    
    // Función para manejar el envío del formulario
    document.getElementById('editForm')?.addEventListener('submit', function(e) {
        // Si el checkbox de eliminar está marcado, asegurar que no se envíe nueva imagen
        if (deleteCheckbox && deleteCheckbox.checked) {
            // Deshabilitar temporalmente el input para que no se envíe
            fileInput.disabled = true;
        }
        
        // Si hay una nueva imagen seleccionada, asegurarse de que se envíe
        if (hasNewImage && currentSelectedFile) {
            // El archivo ya está en el input, se enviará automáticamente
        }
    });
    
    // Inicializar
    document.addEventListener('DOMContentLoaded', function() {
        if (deleteCheckbox) {
            // Configurar estado inicial
            if (deleteCheckbox.checked) {
                fileInput.disabled = true;
            }
            
            // Escuchar cambios en el checkbox
            deleteCheckbox.addEventListener('change', handleDeleteCheckbox);
        }
        
        // Prevenir envío accidental con Enter
        fileInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
            }
        });
    });
</script>
@endsection