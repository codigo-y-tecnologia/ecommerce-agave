@extends('layouts.app')

@section('title', 'Valores del Atributo: ' . $atributo->vNombre)
@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1><i class="fas fa-list me-2"></i>Valores de: {{ $atributo->vNombre }}</h1>
            <p class="text-muted">Gestiona los valores disponibles para este atributo</p>
        </div>
        <div>
            <a href="{{ route('atributos.valores.create', $atributo) }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Nuevo Valor
            </a>
            <a href="{{ route('atributos.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Volver a Atributos
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            @if($valores->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Valor</th>
                            <th>Slug</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($valores as $valor)
                        <tr>
                            <td>{{ $valor->id_atributo_valor }}</td>
                            <td>
                                <strong>{{ $valor->vValor }}</strong>
                            </td>
                            <td>
                                <code>{{ $valor->vSlug }}</code>
                            </td>
                            <td>
                                <span class="badge {{ $valor->bActivo ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $valor->bActivo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('atributos.valores.edit', ['atributo' => $atributo, 'valor' => $valor]) }}" 
                                       class="btn btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('atributos.valores.destroy', ['atributo' => $atributo, 'valor' => $valor]) }}" 
                                          method="POST" class="d-inline" onsubmit="return confirmDelete()">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-list fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">No hay valores registrados</h4>
                <p class="text-muted">Agrega valores para este atributo</p>
                <a href="{{ route('atributos.valores.create', $atributo) }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Agregar Primer Valor
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function confirmDelete() {
    return confirm('¿Estás seguro de que deseas eliminar este valor?');
}
</script>
@endsection
