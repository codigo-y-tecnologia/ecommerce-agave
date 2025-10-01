@extends('layouts.app')

@section('title', 'Etiquetas de Productos')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-tag me-2"></i>Etiquetas de Productos</h1>
    <a href="{{ route('etiquetas.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Nueva Etiqueta
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        @if($etiquetas->count() > 0)
        <div class="row">
            @foreach($etiquetas as $etiqueta)
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-tag me-2"></i>{{ $etiqueta->vNombre }}
                        </h5>
                        <p class="card-text">
                            @if($etiqueta->tDescripcion)
                                {{ $etiqueta->tDescripcion }}
                            @else
                                <span class="text-muted">Sin descripción</span>
                            @endif
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-primary">
                                {{ $etiqueta->productos->count() }} productos
                            </span>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('etiquetas.edit', $etiqueta) }}" class="btn btn-outline-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('etiquetas.destroy', $etiqueta) }}" method="POST" 
                                      onsubmit="return confirm('¿Eliminar etiqueta?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-tags fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">No hay etiquetas registradas</h4>
            <p class="text-muted">Crea etiquetas para categorizar tus productos</p>
            <a href="{{ route('etiquetas.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Crear Primera Etiqueta
            </a>
        </div>
        @endif
    </div>
</div>
@endsection