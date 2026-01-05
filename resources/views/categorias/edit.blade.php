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

                    <form action="{{ route('categorias.update', $categoria) }}" method="POST" enctype="multipart/form-data">
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
                            @enderror>
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
                            @enderror>
                        </div>

                        <div class="mb-3">
                            <label for="tDescripcion" class="form-label">Descripción</label>
                            <textarea class="form-control @error('tDescripcion') is-invalid @enderror" 
                                      id="tDescripcion" name="tDescripcion" rows="3">{{ old('tDescripcion', $categoria->tDescripcion) }}</textarea>
                            @error('tDescripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror>
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
                            <label class="form-label">Imagen Actual</label>
                            
                            @if($categoria->vImagen)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/categorias/' . $categoria->vImagen) }}" 
                                         class="img-thumbnail" 
                                         style="width: 200px; height: 200px; object-fit: cover;"
                                         alt="{{ $categoria->vNombre }}">
                                    <br>
                                    <small class="text-muted">Imagen actual</small>
                                </div>
                                
                                <div class="form-check mb-2">
                                    <input type="checkbox" class="form-check-input" 
                                           id="eliminar_imagen" name="eliminar_imagen" value="1">
                                    <label class="form-check-label text-danger" for="eliminar_imagen">
                                        ❌ Eliminar imagen actual
                                    </label>
                                </div>
                            @else
                                <p class="text-muted">No hay imagen cargada</p>
                            @endif
                            
                            <label for="vImagen" class="form-label">Cambiar imagen</label>
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
    document.getElementById('eliminar_imagen')?.addEventListener('change', function() {
        const fileInput = document.getElementById('vImagen');
        if (this.checked) {
            fileInput.disabled = true;
            fileInput.value = '';
        } else {
            fileInput.disabled = false;
        }
    });
</script>
@endsection