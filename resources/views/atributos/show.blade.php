@extends('layouts.app')

@section('title', 'Detalles de Atributo')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="mb-0">Detalles de Atributo</h2>
                        <a href="{{ route('atributos.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Volver
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <div class="mb-3">
                                <div class="display-1 text-primary mb-3">
                                    <i class="fas fa-tag"></i>
                                </div>
                                <h3>{{ $atributo->vNombre }}</h3>
                            </div>
                            
                            <div class="mb-3">
                                @if($atributo->bActivo)
                                    <span class="badge bg-success fs-6">Activo</span>
                                @else
                                    <span class="badge bg-secondary fs-6">Inactivo</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-8">
                            <!-- Mostrar Slug -->
                            <div class="mb-4">
                                <strong>Slug (URL):</strong>
                                <div class="mt-1">
                                    <code class="bg-light p-2 rounded d-block">
                                        {{ $atributo->vSlug }}
                                    </code>
                                    <small class="text-muted">URL amigable para el atributo</small>
                                </div>
                            </div>

                            @if($atributo->tDescripcion)
                            <div class="mb-4">
                                <strong>Descripción:</strong>
                                <p class="mt-2">{{ $atributo->tDescripcion }}</p>
                            </div>
                            @endif

                            <div class="mb-4">
                                <strong>ID:</strong> <span class="badge bg-dark">{{ $atributo->id_atributo }}</span>
                            </div>

                            <div class="mb-4">
                                <strong>Total de Valores:</strong>
                                <span class="badge bg-info fs-6">{{ $atributo->valores->count() }} valores</span>
                                <a href="{{ route('atributos.valores', $atributo) }}" class="btn btn-outline-primary btn-sm ms-2">
                                    <i class="fas fa-list me-1"></i> Gestionar Valores
                                </a>
                            </div>

                            @if($atributo->valores->count() > 0)
                            <div class="mb-4">
                                <strong>Valores del Atributo:</strong>
                                <div class="mt-2">
                                    @foreach($atributo->valores as $valor)
                                        <div class="border p-2 mb-2 rounded">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong>{{ $valor->vValor }}</strong>
                                                    <div class="text-muted" style="font-size: 0.85rem;">
                                                        Slug: <code>{{ $valor->vSlug }}</code> | 
                                                        Orden: {{ $valor->iOrden }} | 
                                                        Precio extra: ${{ number_format($valor->dPrecio_extra, 2) }}
                                                        @if($valor->iStock !== null)
                                                            | Stock: {{ $valor->iStock }} unidades
                                                        @endif
                                                    </div>
                                                </div>
                                                <div>
                                                    @if($valor->bActivo)
                                                        <span class="badge bg-success">Activo</span>
                                                    @else
                                                        <span class="badge bg-secondary">Inactivo</span>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            @if($valor->vHexColor)
                                            <div class="mt-2">
                                                <small>Color:</small>
                                                <div style="width: 30px; height: 30px; background-color: {{ $valor->vHexColor }}; 
                                                            border-radius: 4px; display: inline-block; margin-left: 5px;"></div>
                                            </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <div class="mt-4 pt-3 border-top">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('atributos.edit', $atributo) }}" class="btn btn-warning">
                                        <i class="fas fa-edit me-1"></i> Editar
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
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cargar SweetAlert2 -->
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
        cancelButtonText: "Cancelar",
        customClass: {
            popup: 'animated fadeIn'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar mensaje de eliminando
            Swal.fire({
                title: "Eliminando...",
                text: "Por favor espera",
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Enviar formulario de eliminación
            document.getElementById('formEliminar').submit();
        }
    });
}
</script>
@endsection