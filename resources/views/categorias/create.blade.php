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
                    <form action="{{ route('categorias.store') }}" method="POST" enctype="multipart/form-data">
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
                            <label for="vImagen" class="form-label">Imagen de la Categoría</label>
                            <input type="file" class="form-control @error('vImagen') is-invalid @enderror" 
                                   id="vImagen" name="vImagen"
                                   accept="image/*">
                            @error('vImagen')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror>
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

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const nombreInput = document.getElementById('vNombre');
        
        form.addEventListener('submit', function(e) {
            if (!nombreInput.value.trim()) {
                e.preventDefault();
                alert('El nombre de la categoría es obligatorio');
                nombreInput.focus();
                return false;
            }
        });
        
        if (nombreInput.value) {
            actualizarSlug(nombreInput.value);
        }
    });
</script>
@endsection