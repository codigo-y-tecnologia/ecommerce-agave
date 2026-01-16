@extends('layouts.app')

@section('title', 'Crear Categoría')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2>Crear Nueva Categoría</h2>
                    <a href="{{ route('categorias.index') }}" class="btn btn-secondary btn-sm">← Volver</a>
                </div>

                <div class="card-body">
                    <form action="{{ route('categorias.store') }}" method="POST" enctype="multipart/form-data" id="createForm">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="vNombre" class="form-label">Nombre de la Categoría *</label>
                            <input type="text" class="form-control @error('vNombre') is-invalid @enderror" 
                                   id="vNombre" name="vNombre" 
                                   value="{{ old('vNombre') }}" required
                                   placeholder="Ej: Tequila, Mezcal, Añejos..."
                                   oninput="actualizarSlug(this.value)">
                            @error('vNombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Campo slug visible -->
                        <div class="mb-3">
                            <label for="vSlug" class="form-label">Slug (URL amigable) *</label>
                            <input type="text" class="form-control @error('vSlug') is-invalid @enderror" 
                                   id="vSlug" name="vSlug" 
                                   value="{{ old('vSlug') }}" required
                                   placeholder="tequila-reposado">
                            @error('vSlug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                URL para la categoría (ej: tequila-reposado). Se genera automáticamente desde el nombre.
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="id_categoria_padre" class="form-label">Categoría Padre</label>
                            <select class="form-control @error('id_categoria_padre') is-invalid @enderror" 
                                    id="id_categoria_padre" name="id_categoria_padre">
                                <option value="">-- Seleccionar Categoría Padre (Opcional) --</option>
                                @foreach($categoriasPadre as $id => $nombre)
                                    <option value="{{ $id }}" {{ old('id_categoria_padre') == $id ? 'selected' : '' }}>
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
                                      id="tDescripcion" name="tDescripcion" rows="3"
                                      placeholder="Describe la categoría...">{{ old('tDescripcion') }}</textarea>
                            @error('tDescripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Estado</label>
                            <div class="form-check mt-2">
                                <input type="checkbox" class="form-check-input" 
                                       id="bActivo" name="bActivo" value="1"
                                       {{ old('bActivo', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="bActivo">
                                    Categoría activa
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Imagen de la Categoría</label>
                            
                            <!-- Sección para mostrar que no hay imagen actual (en creación) -->
                            <div class="mb-3" id="noImageSection">
                                <div class="border rounded p-4 mb-3 text-muted text-center">
                                    <div style="font-size: 2rem; margin-bottom: 10px;">📷</div>
                                    <p>No hay imagen seleccionada</p>
                                    <small class="text-muted d-block mt-2">
                                        La imagen es completamente opcional. Puedes agregar una imagen ahora o después.
                                    </small>
                                </div>
                            </div>
                            
                            <!-- Preview de nueva imagen seleccionada -->
                            <div class="mb-3" id="newImagePreviewSection" style="display: none;">
                                <div class="mb-2">
                                    <img id="newImagePreview" src="#" 
                                         class="img-thumbnail" 
                                         style="width: 200px; height: 200px; object-fit: cover;"
                                         alt="Preview de nueva imagen">
                                    <br>
                                    <small class="text-muted">Vista previa de la imagen seleccionada</small>
                                    <div class="mt-2">
                                        
                                        <small class="form-text text-muted d-block mt-1">
                                            Si cancelas, no se agregará ninguna imagen a la categoría
                                        </small>
                                    </div>
                                </div>
                            </div>
                            
                            <label for="vImagen" class="form-label">Agregar imagen (opcional)</label>
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
                                La imagen es opcional. <br>
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
                                Crear Categoría
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
    let selectedFile = null;
    let hasImageSelected = false;
    
    // Obtener elementos del DOM
    const fileInput = document.getElementById('vImagen');
    const previewSection = document.getElementById('newImagePreviewSection');
    const noImageSection = document.getElementById('noImageSection');
    
    // Configurar evento change del input de archivo
    fileInput.addEventListener('change', function(event) {
        handleFileSelection(event);
    });
    
    function actualizarSlug(nombre) {
        if (!nombre) return;
        
        let slug = nombre.toLowerCase();
        slug = slug.replace(/á/gi, 'a');
        slug = slug.replace(/é/gi, 'e');
        slug = slug.replace(/í/gi, 'i');
        slug = slug.replace(/ó/gi, 'o');
        slug = slug.replace(/ú/gi, 'u');
        slug = slug.replace(/ñ/gi, 'n');
        slug = slug.replace(/[^a-z0-9]+/g, '-');
        slug = slug.replace(/^-+/, '').replace(/-+$/, '');
        
        document.getElementById('vSlug').value = slug;
    }
    
    // Función para manejar la selección de archivo
    function handleFileSelection(event) {
        const input = event.target;
        
        if (input.files && input.files[0]) {
            // Hay un archivo seleccionado
            selectedFile = input.files[0];
            hasImageSelected = true;
            
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const preview = document.getElementById('newImagePreview');
                preview.src = e.target.result;
                previewSection.style.display = 'block';
                noImageSection.style.display = 'none';
            }
            
            reader.readAsDataURL(input.files[0]);
        } else {
            // No hay archivo seleccionado (usuario canceló)
            // PERO mantenemos el archivo seleccionado anteriormente si existe
            if (selectedFile) {
                // Restaurar el archivo seleccionado
                restoreSelectedFile();
            }
        }
    }
    
    // Función para restaurar el archivo seleccionado
    function restoreSelectedFile() {
        if (selectedFile) {
            // Crear un nuevo DataTransfer para asignar el archivo
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(selectedFile);
            fileInput.files = dataTransfer.files;
            
            // Forzar el evento change para mostrar el preview
            const event = new Event('change', { bubbles: true });
            fileInput.dispatchEvent(event);
        }
    }
    
    // Función para resetear el input de archivo (permitir nueva selección)
    function resetFileInput() {
        // Crear un nuevo input de archivo para resetearlo completamente
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
        selectedFile = null;
        hasImageSelected = false;
        previewSection.style.display = 'none';
        noImageSection.style.display = 'block';
    }
    
    // Función para cancelar la nueva imagen seleccionada
    function cancelNewImage() {
        // Resetear el input
        resetFileInput();
        
        // Resetear variables
        selectedFile = null;
        hasImageSelected = false;
    }
    
    // Función para manejar el envío del formulario
    document.getElementById('createForm')?.addEventListener('submit', function(e) {
        // Validar que si hay una imagen seleccionada, se envíe correctamente
        if (hasImageSelected && selectedFile) {
            // El archivo ya está en el input, se enviará automáticamente
            // No necesitamos hacer nada más
        }
        
        // Validación adicional del nombre (opcional)
        const nombreInput = document.getElementById('vNombre');
        if (!nombreInput.value.trim()) {
            e.preventDefault();
            alert('El nombre de la categoría es obligatorio');
            nombreInput.focus();
            return false;
        }
    });
    
    // Inicializar
    document.addEventListener('DOMContentLoaded', function() {
        // Prevenir envío accidental con Enter en el input de archivo
        fileInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
            }
        });
        
        // Inicializar slug si hay nombre en el input
        const nombreInput = document.getElementById('vNombre');
        if (nombreInput && nombreInput.value) {
            actualizarSlug(nombreInput.value);
        }
    });
</script>
@endsection