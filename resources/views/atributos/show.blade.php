@extends('layouts.app')

@section('title', 'Detalles de Atributo')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2>Detalles de Atributo</h2>
                    <a href="{{ route('atributos.index') }}" class="btn btn-secondary">← Volver</a>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <div class="mb-3">
                                <div style="font-size: 3rem; margin-bottom: 15px;">🏷️</div>
                                <h3>{{ $atributo->vNombre }}</h3>
                            </div>
                            
                            <div class="mb-3">
                                @if($atributo->bActivo)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-secondary">Inactivo</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-8">
                            <!-- Mostrar Slug -->
                            <div class="mb-3">
                                <strong>Slug (URL):</strong>
                                <div class="mt-1">
                                    <code class="bg-light p-2 rounded d-block">
                                        {{ $atributo->vSlug }}
                                    </code>
                                    <small class="text-muted">URL amigable para el atributo</small>
                                </div>
                            </div>

                            @if($atributo->tDescripcion)
                            <div class="mb-3">
                                <strong>Descripción:</strong>
                                <p>{{ $atributo->tDescripcion }}</p>
                            </div>
                            @endif

                            <div class="mb-3">
                                <strong>ID:</strong> {{ $atributo->id_atributo }}
                            </div>

                            <div class="mb-3">
                                <strong>Total de Valores:</strong>
                                <span class="badge bg-info">{{ $atributo->valores->count() }} valores</span>
                                <a href="{{ route('atributos.valores', $atributo) }}" class="btn btn-sm btn-outline-primary ms-2">
                                    Gestionar Valores
                                </a>
                            </div>

                            @if($atributo->valores->count() > 0)
                            <div class="mb-3">
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

                            <div class="mt-4">
                                <a href="{{ route('atributos.edit', $atributo) }}" class="btn btn-warning">
                                    ✏️ Editar
                                </a>
                                
                                <button type="button" class="btn btn-danger" id="btnEliminarAtributo">
                                    🗑️ Eliminar
                                </button>
                                
                                <form action="{{ route('atributos.destroy', $atributo) }}" method="POST" id="deleteForm" style="display: none;">
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
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Botón de eliminar atributo
    document.getElementById('btnEliminarAtributo').addEventListener('click', function() {
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
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.isConfirmed) {
                // Enviar formulario de eliminación
                document.getElementById('deleteForm').submit();
            }
        });
    });
});
</script>
@endsection