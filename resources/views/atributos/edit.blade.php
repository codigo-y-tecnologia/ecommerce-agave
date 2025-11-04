@extends('layouts.app')

@section('title', 'Editar Atributo')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Editar Atributo: {{ $atributo->vNombre }}</h3>
                    <a href="{{ route('atributos.index') }}" class="btn btn-secondary float-end">
                        <i class="fas fa-arrow-left me-1"></i> Volver
                    </a>
                </div>
                <form action="{{ route('atributos.update', $atributo) }}" method="POST" id="atributoForm">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="vNombre">Nombre del Atributo *</label>
                                    <input type="text" class="form-control @error('vNombre') is-invalid @enderror" 
                                           id="vNombre" name="vNombre" value="{{ old('vNombre', $atributo->vNombre) }}" 
                                           placeholder="Ej: Color, Tamaño, Material" required>
                                    @error('vNombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="eTipo">Tipo de Campo *</label>
                                    <select class="form-control @error('eTipo') is-invalid @enderror" 
                                            id="eTipo" name="eTipo" required>
                                        <option value="">Seleccione un tipo</option>
                                        @foreach($tipos as $valor => $etiqueta)
                                            <option value="{{ $valor }}" 
                                                {{ old('eTipo', $atributo->eTipo) == $valor ? 'selected' : '' }}>
                                                {{ $etiqueta }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('eTipo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="vLabel">Label (Texto visible)</label>
                                    <input type="text" class="form-control @error('vLabel') is-invalid @enderror" 
                                           id="vLabel" name="vLabel" value="{{ old('vLabel', $atributo->vLabel) }}" 
                                           placeholder="Ej: Selecciona el color">
                                    @error('vLabel')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="vPlaceholder">Texto de ejemplo</label>
                                    <input type="text" class="form-control @error('vPlaceholder') is-invalid @enderror" 
                                           id="vPlaceholder" name="vPlaceholder" value="{{ old('vPlaceholder', $atributo->vPlaceholder) }}" 
                                           placeholder="Ej: Escribe aquí...">
                                    @error('vPlaceholder')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="iOrden">Orden</label>
                                    <input type="number" class="form-control @error('iOrden') is-invalid @enderror" 
                                           id="iOrden" name="iOrden" value="{{ old('iOrden', $atributo->iOrden) }}" min="0">
                                    @error('iOrden')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="tDescripcion">Descripción</label>
                                    <textarea class="form-control @error('tDescripcion') is-invalid @enderror" 
                                              id="tDescripcion" name="tDescripcion" rows="2"
                                              placeholder="Descripción del atributo">{{ old('tDescripcion', $atributo->tDescripcion) }}</textarea>
                                    @error('tDescripcion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check mb-3">
                                    <input type="checkbox" class="form-check-input" 
                                           id="bRequerido" name="bRequerido" value="1"
                                           {{ old('bRequerido', $atributo->bRequerido) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="bRequerido">
                                        Campo requerido
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check mb-3">
                                    <input type="checkbox" class="form-check-input" 
                                           id="bActivo" name="bActivo" value="1"
                                           {{ old('bActivo', $atributo->bActivo) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="bActivo">
                                        Atributo activo
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Sección de Opciones (solo para select, radio, checkbox) -->
                        <div id="opcionesSection" style="display: none;">
                            <hr>
                            <h5>Opciones del Atributo</h5>
                            <div id="opcionesContainer">
                                <!-- Las opciones se agregarán dinámicamente aquí -->
                            </div>
                            <button type="button" id="agregarOpcion" class="btn btn-outline-primary btn-sm mt-2">
                                <i class="fas fa-plus me-1"></i> Agregar Opción
                            </button>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Actualizar Atributo
                        </button>
                        <a href="{{ route('atributos.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tipoSelect = document.getElementById('eTipo');
    const opcionesSection = document.getElementById('opcionesSection');
    const opcionesContainer = document.getElementById('opcionesContainer');
    const agregarOpcionBtn = document.getElementById('agregarOpcion');

    // Tipos que requieren opciones
    const tiposConOpciones = ['select', 'radio', 'checkbox'];

    // Cargar opciones existentes si las hay
    function cargarOpcionesExistentes() {
        @if($atributo->opciones && $atributo->opciones->count() > 0)
            @foreach($atributo->opciones as $index => $opcion)
                agregarOpcionExistente({{ $index }}, '{{ $opcion->vValor }}', '{{ $opcion->vEtiqueta }}', {{ $opcion->bPredeterminado ? 'true' : 'false' }});
            @endforeach
        @endif
    }

    // Agregar opción existente
    function agregarOpcionExistente(index, valor, etiqueta, predeterminado) {
        const opcionDiv = document.createElement('div');
        opcionDiv.className = 'row opcion-item mb-2';
        opcionDiv.innerHTML = `
            <div class="col-md-4">
                <input type="text" class="form-control" name="opciones[${index}][vValor]" 
                       value="${valor}" placeholder="Valor (ej: rojo)" required>
            </div>
            <div class="col-md-4">
                <input type="text" class="form-control" name="opciones[${index}][vEtiqueta]" 
                       value="${etiqueta}" placeholder="Etiqueta (ej: Rojo)" required>
            </div>
            <div class="col-md-3">
                <div class="form-check mt-2">
                    <input type="checkbox" class="form-check-input" name="opciones[${index}][bPredeterminado]" value="1" ${predeterminado ? 'checked' : ''}>
                    <label class="form-check-label">Predeterminado</label>
                </div>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger btn-sm quitar-opcion" 
                        ${index === 0 ? 'disabled' : ''}>
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        opcionesContainer.appendChild(opcionDiv);
    }

    // Mostrar/ocultar sección de opciones
    function toggleOpcionesSection() {
        if (tiposConOpciones.includes(tipoSelect.value)) {
            opcionesSection.style.display = 'block';
            if (opcionesContainer.children.length === 0) {
                cargarOpcionesExistentes();
                if (opcionesContainer.children.length === 0) {
                    agregarOpcion();
                }
            }
        } else {
            opcionesSection.style.display = 'none';
            opcionesContainer.innerHTML = '';
        }
    }

    // Agregar nueva opción
    function agregarOpcion() {
        const index = opcionesContainer.children.length;
        const opcionDiv = document.createElement('div');
        opcionDiv.className = 'row opcion-item mb-2';
        opcionDiv.innerHTML = `
            <div class="col-md-4">
                <input type="text" class="form-control" name="opciones[${index}][vValor]" 
                       placeholder="Valor (ej: rojo)" required>
            </div>
            <div class="col-md-4">
                <input type="text" class="form-control" name="opciones[${index}][vEtiqueta]" 
                       placeholder="Etiqueta (ej: Rojo)" required>
            </div>
            <div class="col-md-3">
                <div class="form-check mt-2">
                    <input type="checkbox" class="form-check-input" name="opciones[${index}][bPredeterminado]" value="1">
                    <label class="form-check-label">Predeterminado</label>
                </div>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger btn-sm quitar-opcion" 
                        ${index === 0 ? 'disabled' : ''}>
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        opcionesContainer.appendChild(opcionDiv);
        actualizarBotonesEliminar();
    }

    // Actualizar estado de botones eliminar
    function actualizarBotonesEliminar() {
        const botones = opcionesContainer.querySelectorAll('.quitar-opcion');
        botones.forEach((btn, index) => {
            btn.disabled = index === 0;
        });
    }

    // Event listeners
    tipoSelect.addEventListener('change', toggleOpcionesSection);
    agregarOpcionBtn.addEventListener('click', agregarOpcion);

    // Eliminar opción
    opcionesContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('quitar-opcion') || 
            e.target.closest('.quitar-opcion')) {
            const btn = e.target.classList.contains('quitar-opcion') ? 
                       e.target : e.target.closest('.quitar-opcion');
            if (!btn.disabled) {
                btn.closest('.opcion-item').remove();
                // Renumerar los índices
                const opciones = opcionesContainer.querySelectorAll('.opcion-item');
                opciones.forEach((opcion, index) => {
                    const inputs = opcion.querySelectorAll('input');
                    inputs[0].name = `opciones[${index}][vValor]`;
                    inputs[1].name = `opciones[${index}][vEtiqueta]`;
                    inputs[2].name = `opciones[${index}][bPredeterminado]`;
                });
                actualizarBotonesEliminar();
            }
        }
    });

    // Inicializar
    toggleOpcionesSection();
});
</script>
@endpush