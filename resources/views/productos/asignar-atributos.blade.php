@extends('admin.productos.administrar-productos')

@section('title', 'Asignar Atributos - ' . $producto->vNombre)
@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1><i class="fas fa-wine-bottle me-2"></i>{{ $producto->vNombre }}</h1>
            <p class="text-muted">Asignar atributos al producto</p>
        </div>
        <div>
            <a href="{{ route('productos.edit', $producto) }}" class="btn btn-outline-primary">
                <i class="fas fa-edit me-1"></i> Editar Producto
            </a>
            <a href="{{ route('productos.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>
        </div>
    </div>

    <form action="{{ route('productos.guardar-atributos', $producto->id_producto) }}" method="POST">
        @csrf

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-tags me-2"></i>Atributos Disponibles</h5>
            </div>
            <div class="card-body">
                @if($atributos->count() > 0)
                    @foreach($atributos as $atributo)
                    <div class="atributo-card mb-4 p-3 border rounded">
                        <div class="form-check mb-2">
                            <input type="checkbox" 
                                   class="form-check-input atributo-checkbox" 
                                   id="atributo-{{ $atributo->id_atributo }}"
                                   data-id="{{ $atributo->id_atributo }}">
                            <label class="form-check-label fw-bold" for="atributo-{{ $atributo->id_atributo }}">
                                {{ $atributo->vNombre }}
                            </label>
                            @if($atributo->tDescripcion)
                            <p class="small text-muted mb-1">{{ $atributo->tDescripcion }}</p>
                            @endif
                        </div>
                        
                        <div class="valores-container" 
                             id="valores-{{ $atributo->id_atributo }}"
                             style="display: none;">
                            <input type="hidden" name="atributos[{{ $atributo->id_atributo }}][id_atributo]" 
                                   value="{{ $atributo->id_atributo }}">
                            
                            <div class="row">
                                @foreach($atributo->valoresActivos as $valor)
                                @php
                                    $seleccionado = $producto->valoresAtributos->contains('id_atributo_valor', $valor->id_atributo_valor);
                                    $precioExtra = $seleccionado ? $producto->valoresAtributos->firstWhere('id_atributo_valor', $valor->id_atributo_valor)->pivot->dPrecio_extra : 0;
                                @endphp
                                <div class="col-md-4 mb-2">
                                    <div class="card">
                                        <div class="card-body p-2">
                                            <div class="form-check">
                                                <input type="checkbox" 
                                                       class="form-check-input valor-checkbox" 
                                                       name="atributos[{{ $atributo->id_atributo }}][valores][{{ $valor->id_atributo_valor }}][id_valor]"
                                                       value="{{ $valor->id_atributo_valor }}"
                                                       {{ $seleccionado ? 'checked' : '' }}>
                                                <label class="form-check-label">
                                                    <strong>{{ $valor->vValor }}</strong>
                                                </label>
                                            </div>
                                            
                                            <div class="mt-2">
                                                <label class="small">Precio extra:</label>
                                                <input type="number" 
                                                       name="atributos[{{ $atributo->id_atributo }}][valores][{{ $valor->id_atributo_valor }}][precio_extra]"
                                                       class="form-control form-control-sm" 
                                                       value="{{ $precioExtra }}"
                                                       min="0" step="0.01" placeholder="0.00">
                                            </div>
                                            
                                            <div class="mt-1 small text-muted">
                                                <div>Stock: {{ $valor->iStock }}</div>
                                                <div>Precio base extra: ${{ number_format($valor->dPrecio_extra, 2) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                <div class="text-center py-4">
                    <i class="fas fa-tags fa-3x text-muted mb-2"></i>
                    <p class="text-muted">No hay atributos disponibles</p>
                    <a href="{{ route('atributos.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> Crear Atributo
                    </a>
                </div>
                @endif
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save me-2"></i> Guardar Atributos
            </button>
            <button type="button" class="btn btn-outline-secondary" onclick="window.location.reload()">
                <i class="fas fa-redo me-2"></i> Cancelar Cambios
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mostrar/ocultar valores cuando se selecciona un atributo
    document.querySelectorAll('.atributo-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const valoresContainer = document.getElementById(`valores-${this.dataset.id}`);
            if (this.checked) {
                valoresContainer.style.display = 'block';
            } else {
                valoresContainer.style.display = 'none';
                // Desmarcar todos los valores de este atributo
                valoresContainer.querySelectorAll('.valor-checkbox').forEach(vc => {
                    vc.checked = false;
                });
            }
        });
        
        // Verificar si ya hay valores seleccionados para mostrar el contenedor
        const valoresContainer = document.getElementById(`valores-${checkbox.dataset.id}`);
        const tieneValoresSeleccionados = valoresContainer.querySelectorAll('.valor-checkbox:checked').length > 0;
        if (tieneValoresSeleccionados) {
            checkbox.checked = true;
            valoresContainer.style.display = 'block';
        }
    });
});
</script>
@endsection
