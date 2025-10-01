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
                                @if($message == 'The vNombre has already been taken.')
                                    <div class="invalid-feedback">Este nombre de etiqueta ya existe.</div>
                                @elseif($message == 'The vNombre field is required.')
                                    <div class="invalid-feedback">El nombre de la etiqueta es obligatorio.</div>
                                @elseif($message == 'The vNombre must not be greater than 100 characters.')
                                    <div class="invalid-feedback">El nombre no puede tener más de 100 caracteres.</div>
                                @else
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @endif
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="tDescripcion" class="form-label">Descripción</label>
                            <textarea class="form-control @error('tDescripcion') is-invalid @enderror" 
                                      id="tDescripcion" name="tDescripcion" rows="3" 
                                      placeholder="Descripción opcional de la etiqueta...">{{ old('tDescripcion') }}</textarea>
                            @error('tDescripcion')
                                @if($message == 'The tDescripcion must not be greater than 500 characters.')
                                    <div class="invalid-feedback">La descripción no puede tener más de 500 caracteres.</div>
                                @else
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @endif
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