@extends('admin.productos.administrar-productos')

@section('title', 'Editar Marca')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-edit me-2"></i>Editar Marca</h1>
            <a href="{{ route('marcas.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>
        </div>

        <!-- Agregar mensajes de éxito/error -->
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

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('marcas.update', $marca) }}" method="POST">
                    @csrf @method('PUT')
                    
                    <div class="mb-3">
                        <label for="vNombre" class="form-label">Nombre de la Marca *</label>
                        <input type="text" class="form-control @error('vNombre') is-invalid @enderror" 
                               id="vNombre" name="vNombre" 
                               value="{{ old('vNombre', $marca->vNombre) }}" 
                               placeholder="Ej: José Cuervo, Patrón, Don Julio" required>
                        @error('vNombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="tDescripcion" class="form-label">Descripción</label>
                        <textarea class="form-control @error('tDescripcion') is-invalid @enderror" 
                                  id="tDescripcion" name="tDescripcion" rows="4" 
                                  placeholder="Describe la marca...">{{ old('tDescripcion', $marca->tDescripcion) }}</textarea>
                        @error('tDescripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('marcas.index') }}" class="btn btn-secondary me-md-2">
                            <i class="fas fa-times me-1"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Actualizar Marca
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection