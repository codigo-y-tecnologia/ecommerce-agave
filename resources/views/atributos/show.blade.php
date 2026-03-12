@extends('layouts.app')

@section('title', 'Detalles de Atributo')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="mb-0"><i class="fas fa-tag me-2"></i>Detalles de Atributo</h2>
                        <a href="{{ route('atributos.index') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-1"></i> Volver
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center border-end">
                            <div class="mb-3">
                                <div class="display-1 text-success mb-3">
                                    <i class="fas fa-cubes"></i>
                                </div>
                                <h3 class="fw-bold">{{ $atributo->vNombre }}</h3>
                            </div>
                            
                            <div class="mb-3">
                                @if($atributo->bActivo)
                                    <span class="badge bg-success fs-6 px-3 py-2">Activo</span>
                                @else
                                    <span class="badge bg-secondary fs-6 px-3 py-2">Inactivo</span>
                                @endif
                            </div>

                            <div class="mb-3">
                                <span class="badge bg-info fs-6 px-3 py-2">
                                    <i class="fas fa-list me-1"></i>
                                    {{ $atributo->valores->count() }} Valores
                                </span>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="mb-4">
                                <strong class="text-success"><i class="fas fa-link me-1"></i>Slug (URL):</strong>
                                <div class="mt-2 p-3 bg-light rounded">
                                    <code class="text-primary fs-6">{{ $atributo->vSlug }}</code>
                                </div>
                                <small class="text-muted">URL amigable para el atributo</small>
                            </div>

                            @if($atributo->tDescripcion)
                            <div class="mb-4">
                                <strong class="text-success"><i class="fas fa-align-left me-1"></i>Descripción:</strong>
                                <p class="mt-2 p-3 bg-light rounded">{{ $atributo->tDescripcion }}</p>
                            </div>
                            @endif

                            <div class="mb-4">
                                <strong class="text-success"><i class="fas fa-hashtag me-1"></i>ID:</strong>
                                <span class="badge bg-dark ms-2">{{ $atributo->id_atributo }}</span>
                            </div>

                            <div class="mb-4">
                                <strong class="text-success"><i class="fas fa-calendar me-1"></i>Fecha de registro:</strong>
                                <span class="ms-2">{{ $atributo->tFecha_registro ?? 'No disponible' }}</span>
                            </div>

                            <div class="mt-4 pt-3 border-top">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('atributos.edit', $atributo) }}" class="btn btn-warning">
                                        <i class="fas fa-edit me-1"></i> Editar
                                    </a>
                                    <a href="{{ route('atributos.valores', $atributo) }}" class="btn btn-info text-white">
                                        <i class="fas fa-list me-1"></i> Gestionar Valores
                                    </a>
                                    <button type="button" class="btn btn-danger" onclick="confirmarEliminacion()">
                                        <i class="fas fa-trash me-1"></i> Eliminar
                                    </button>
                                </div>
                                
                                <!-- Formulario oculto para eliminación -->
                                <form id="formEliminar" action="{{ route('atributos.destroy', $atributo) }}" method="POST" style="display: none;">
                                    @csrf 
                                    @method('DELETE')
                                </form>
                            </div>
                        </div>
                    </div>

                    @if($atributo->valores->count() > 0)
                    <div class="mt-5">
                        <h4 class="text-success mb-3">
                            <i class="fas fa-list-ul me-2"></i>Valores del Atributo
                        </h4>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Valor</th>
                                        <th>Slug</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($atributo->valores as $valor)
                                    <tr>
                                        <td>{{ $valor->id_atributo_valor }}</td>
                                        <td class="fw-bold">{{ $valor->vValor }}</td>
                                        <td><code>{{ $valor->vSlug }}</code></td>
                                        <td>
                                            @if($valor->bActivo)
                                                <span class="badge bg-success">Activo</span>
                                            @else
                                                <span class="badge bg-secondary">Inactivo</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmarEliminacion() {
    const atributoNombre = "{{ $atributo->vNombre }}";
    const valoresCount = {{ $atributo->valores->count() }};
    
    let mensaje = `¿Estás seguro de que quieres eliminar el atributo "<strong>${atributoNombre}</strong>"?`;
    
    if (valoresCount > 0) {
        mensaje += `<br><span class="text-danger">¡Se eliminarán también los ${valoresCount} valores asociados!</span>`;
    }
    
    mensaje += `<br><br>Esta acción no se puede deshacer.`;
    
    Swal.fire({
        title: "¿Estás seguro?",
        html: mensaje,
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
            
            document.getElementById('formEliminar').submit();
        }
    });
}
</script>
@endpush