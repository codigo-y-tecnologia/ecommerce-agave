@extends('layouts.app')

@section('title', 'Crear Etiqueta')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-plus me-2"></i>Crear Nueva Etiqueta</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('etiquetas.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="vNombre" class="form-label">Nombre de la Etiqueta</label>
                            <input type="text" class="form-control @error('vNombre') is-invalid @enderror" 
                                   id="vNombre" name="vNombre" value="{{ old('vNombre') }}" 
                                   placeholder="Ej: Oferta, Nuevo, Popular..." required>
                            @error('vNombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="tDescripcion" class="form-label">Descripción</label>
                            <textarea class="form-control @error('tDescripcion') is-invalid @enderror" 
                                      id="tDescripcion" name="tDescripcion" rows="3" 
                                      placeholder="Descripción opcional de la etiqueta...">{{ old('tDescripcion') }}</textarea>
                            @error('tDescripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('etiquetas.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Volver
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Guardar Etiqueta
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection