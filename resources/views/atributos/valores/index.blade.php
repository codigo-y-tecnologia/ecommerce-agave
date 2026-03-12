@extends('layouts.app')

@section('title', 'Valores del Atributo: ' . $atributo->vNombre)
@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="text-success"><i class="fas fa-list me-2"></i>Valores de: {{ $atributo->vNombre }}</h1>
            <p class="text-muted">Gestiona los valores disponibles para este atributo</p>
        </div>
        <div>
            <a href="{{ route('atributos.valores.create', $atributo) }}" class="btn btn-success">
                <i class="fas fa-plus-circle me-1"></i> Nuevo Valor
            </a>
            <a href="{{ route('atributos.show', $atributo) }}" class="btn btn-info text-white">
                <i class="fas fa-eye me-1"></i> Ver Atributo
            </a>
            <a href="{{ route('atributos.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>
        </div>
    </div>

    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#2E8B57'
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('error') }}',
                confirmButtonColor: '#2E8B57'
            });
        </script>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-tags me-2"></i>Lista de Valores</h5>
        </div>
        <div class="card-body">
            @if($valores->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Valor</th>
                            <th>Slug</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($valores as $valor)
                        <tr>
                            <td><span class="badge bg-dark">{{ $valor->id_atributo_valor }}</span></td>
                            <td class="fw-bold">{{ $valor->vValor }}</td>
                            <td><code class="text-primary">{{ $valor->vSlug }}</code></td>
                            <td>
                                @if($valor->bActivo)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-secondary">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('atributos.valores.edit', ['atributo' => $atributo, 'valor' => $valor]) }}" 
                                       class="btn btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-eliminar-valor" 
                                            data-id="{{ $valor->id_atributo_valor }}"
                                            data-valor="{{ $valor->vValor }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <form id="delete-valor-form-{{ $valor->id_atributo_valor }}" 
                                      action="{{ route('atributos.valores.destroy', ['atributo' => $atributo, 'valor' => $valor]) }}" 
                                      method="POST" style="display: none;">
                                    @csrf @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                <strong>Total:</strong> {{ $valores->count() }} valores
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-tags fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">No hay valores registrados</h4>
                <p class="text-muted">Agrega valores para este atributo</p>
                <a href="{{ route('atributos.valores.create', $atributo) }}" class="btn btn-success">
                    <i class="fas fa-plus-circle me-1"></i> Agregar Primer Valor
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-eliminar-valor').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const valor = this.getAttribute('data-valor');
            
            Swal.fire({
                title: "¿Estás seguro?",
                html: `Vas a eliminar el valor: <strong>"${valor}"</strong><br>
                       <span class="text-danger">Esta acción no se puede deshacer.</span>`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: "Eliminando...",
                        text: "Por favor espera",
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    document.getElementById(`delete-valor-form-${id}`).submit();
                }
            });
        });
    });
});
</script>
@endpush