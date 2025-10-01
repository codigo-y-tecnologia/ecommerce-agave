@extends('layouts.app')

@section('title', 'Crear Categoría')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <h1>Crear Categoría</h1>
        
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

       <form action="{{ route('categorias.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="vNombre" class="form-label">Nombre *</label>
                <input type="text" class="form-control @error('vNombre') is-invalid @enderror" 
                       id="vNombre" name="vNombre" 
                       value="{{ old('vNombre') }}" required>
                @error('vNombre')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="tDescripcion" class="form-label">Descripción</label>
                <textarea class="form-control @error('tDescripcion') is-invalid @enderror" 
                          id="tDescripcion" name="tDescripcion" rows="3">{{ old('tDescripcion') }}</textarea>
                @error('tDescripcion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="/categorias" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@endsection