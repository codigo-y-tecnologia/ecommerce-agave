@extends('layouts.app')

@section('title', 'Editar Etiqueta')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Editar Etiqueta</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('etiquetas.update', $etiqueta) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="vNombre" class="form-label">Nombre de la Etiqueta</label>
                            <input type="text" class="form-control @error('vNombre') is-invalid @enderror" 
                                   id="vNombre" name="vNombre" value="{{ old('vNombre', $etiqueta->vNombre) }}" 
                                   placeholder="Ej: Oferta, Nuevo, Popular..." required>
                            @error('vNombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="tDescripcion" class="form-label">Descripción</label>
                            <textarea class="form-control @error('tDescripcion') is-invalid @enderror" 
                                      id="tDescripcion" name="tDescripcion" rows="3" 
                                      placeholder="Descripción opcional de la etiqueta...">{{ old('tDescripcion', $etiqueta->tDescripcion) }}</textarea>
                            @error('tDescripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('etiquetas.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Volver
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Actualizar Etiqueta
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection