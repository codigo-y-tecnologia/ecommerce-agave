@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Editar Valor: {{ $valor->vValor }}</h1>
        <a href="{{ route('atributos.valores', $atributo) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Volver
        </a>
    </div>

    <form action="{{ route('atributos.valores.update', ['atributo' => $atributo, 'valor' => $valor]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Información del Valor</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="vValor">Valor *</label>
                            <input type="text" name="vValor" id="vValor" 
                                   class="form-control @error('vValor') is-invalid @enderror"
                                   value="{{ old('vValor', $valor->vValor) }}" required 
                                   placeholder="Ej: 750ml, Joven, 6 meses">
                            @error('vValor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="vSlug">Slug (URL amigable)</label>
                            <input type="text" name="vSlug" id="vSlug" 
                                   class="form-control @error('vSlug') is-invalid @enderror"
                                   value="{{ old('vSlug', $valor->vSlug) }}">
                            @error('vSlug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="dPrecio_extra">Precio Extra</label>
                            <input type="number" name="dPrecio_extra" id="dPrecio_extra" 
                                   class="form-control @error('dPrecio_extra') is-invalid @enderror"
                                   value="{{ old('dPrecio_extra', $valor->dPrecio_extra) }}" 
                                   min="0" step="0.01">
                            @error('dPrecio_extra')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="iStock">Stock</label>
                            <input type="number" name="iStock" id="iStock" 
                                   class="form-control @error('iStock') is-invalid @enderror"
                                   value="{{ old('iStock', $valor->iStock) }}" min="0">
                            @error('iStock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="iOrden">Orden</label>
                            <input type="number" name="iOrden" id="iOrden" 
                                   class="form-control @error('iOrden') is-invalid @enderror"
                                   value="{{ old('iOrden', $valor->iOrden) }}" min="0">
                            @error('iOrden')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <div class="form-check">
                        <input type="checkbox" name="bActivo" id="bActivo" 
                               class="form-check-input" value="1" 
                               {{ old('bActivo', $valor->bActivo) ? 'checked' : '' }}>
                        <label for="bActivo" class="form-check-label">Activo</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Actualizar Valor
            </button>
            <a href="{{ route('atributos.valores', $atributo) }}" class="btn btn-secondary">
                <i class="fas fa-times me-1"></i> Cancelar
            </a>
        </div>
    </form>
</div>
@endsection