@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Atributo: {{ $atributo->vNombre }}</h1>
        <div>
            <a href="{{ route('atributos.edit', $atributo) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i> Editar
            </a>
            <a href="{{ route('atributos.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Información General</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="30%">Nombre:</th>
                            <td>{{ $atributo->vNombre }}</td>
                        </tr>
                        <tr>
                            <th>Descripción:</th>
                            <td>{{ $atributo->tDescripcion ?: 'Sin descripción' }}</td>
                        </tr>
                        <tr>
                            <th>Estado:</th>
                            <td>
                                <span class="badge {{ $atributo->bActivo ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $atributo->bActivo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Creado:</th>
                            <td>{{ $atributo->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Actualizado:</th>
                            <td>{{ $atributo->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Valores ({{ $atributo->valores->count() }})</h5>
                </div>
                <div class="card-body">
                    @if($atributo->valores->count() > 0)
                    <div class="row">
                        @foreach($atributo->valores as $valor)
                        <div class="col-md-6 mb-3">
                            <div class="border rounded p-3">
                                <div class="d-flex align-items-center mb-2">
                                    @if($valor->vHexColor)
                                    <div class="me-2" style="width: 20px; height: 20px; background-color: {{ $valor->vHexColor }}; border-radius: 3px; border: 1px solid #dee2e6;"></div>
                                    @endif
                                    <strong>{{ $valor->vValor }}</strong>
                                </div>
                                
                                @if($valor->vImagenUrl)
                                <div class="mb-2">
                                    <img src="{{ $valor->vImagenUrl }}" 
                                         alt="{{ $valor->vValor }}" 
                                         style="width: 100%; height: 100px; object-fit: cover; border-radius: 4px;">
                                </div>
                                @endif
                                
                                <div class="small text-muted">
                                    <div>Orden: {{ $valor->iOrden }}</div>
                                    <div>Estado: 
                                        <span class="badge {{ $valor->bActivo ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $valor->bActivo ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-tags fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">No hay valores registrados</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection