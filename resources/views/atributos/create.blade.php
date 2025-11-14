@extends('layouts.app')

@section('title', 'Crear Atributo')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Crear Nuevo Atributo</h3>
                    <a href="{{ route('atributos.index') }}" class="btn btn-secondary float-end">
                        <i class="fas fa-arrow-left me-1"></i> Volver
                    </a>
                </div>
                <form action="{{ route('atributos.store') }}" method="POST" id="atributoForm">
                    @csrf
                    <div class="card-body">
                        <!-- Mostrar errores generales -->
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="vNombre">Nombre del Atributo *</label>
                                    <input type="text" class="form-control @error('vNombre') is-invalid @enderror" 
                                           id="vNombre" name="vNombre" value="{{ old('vNombre') }}" 
                                           placeholder="Ej: Tipo de Agave, Añejamiento, Graduación Alcohólica" required>
                                    @error('vNombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Ej: tipo_agave, anejamiento, grado_alcohol</small>
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
                                                {{ old('eTipo') == $valor ? 'selected' : '' }}>
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
                                           id="vLabel" name="vLabel" value="{{ old('vLabel') }}" 
                                           placeholder="Ej: Selecciona el tipo de agave">
                                    @error('vLabel')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Texto que verá el usuario</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="vPlaceholder">Texto de ejemplo</label>
                                    <input type="text" class="form-control @error('vPlaceholder') is-invalid @enderror" 
                                           id="vPlaceholder" name="vPlaceholder" value="{{ old('vPlaceholder') }}" 
                                           placeholder="Ej: Escribe aquí el tipo de agave...">
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
                                           id="iOrden" name="iOrden" value="{{ old('iOrden', 0) }}" min="0">
                                    @error('iOrden')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Orden de aparición (0 = primero)</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="tDescripcion">Descripción</label>
                                    <textarea class="form-control @error('tDescripcion') is-invalid @enderror" 
                                              id="tDescripcion" name="tDescripcion" rows="2"
                                              placeholder="Descripción del atributo">{{ old('tDescripcion') }}</textarea>
                                    @error('tDescripcion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check mb-3">
                                    <input type="checkbox" class="form-check-input @error('bRequerido') is-invalid @enderror" 
                                           id="bRequerido" name="bRequerido" value="1"
                                           {{ old('bRequerido') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="bRequerido">
                                        Campo requerido
                                    </label>
                                    @error('bRequerido')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check mb-3">
                                    <input type="checkbox" class="form-check-input @error('bActivo') is-invalid @enderror" 
                                           id="bActivo" name="bActivo" value="1"
                                           {{ old('bActivo', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="bActivo">
                                        Atributo activo
                                    </label>
                                    @error('bActivo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Sección de Opciones (solo para select, radio, checkbox) -->
                        <div id="opcionesSection" style="display: none;">
                            <hr>
                            <h5>Opciones del Atributo</h5>
                            
                            <!-- Mensaje de error para opciones -->
                            @error('opciones')
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                                </div>
                            @enderror
                            
                            <div id="opcionesContainer">
                                <!-- Las opciones se agregarán dinámicamente aquí -->
                            </div>
                            
                            <!-- Mensaje de error para opciones individuales -->
                            @if($errors->has('opciones.0.vValor') || $errors->has('opciones.0.vEtiqueta'))
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-circle"></i> 
                                    Todas las opciones deben tener valor y etiqueta completos
                                </div>
                            @endif
                            
                            <button type="button" id="agregarOpcion" class="btn btn-outline-primary btn-sm mt-2">
                                <i class="fas fa-plus me-1"></i> Agregar Opción
                            </button>
                            <small class="form-text text-muted d-block mt-1">
                                Ejemplo para agave: Valor: "tequila", Etiqueta: "Tequila 100% Agave"
                            </small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Guardar Atributo
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

    // Mostrar/ocultar sección de opciones
    function toggleOpcionesSection() {
        if (tiposConOpciones.includes(tipoSelect.value)) {
            opcionesSection.style.display = 'block';
            if (opcionesContainer.children.length === 0) {
                // Si hay datos antiguos, cargarlos
                cargarOpcionesAntiguas();
                if (opcionesContainer.children.length === 0) {
                    agregarOpcion();
                }
            }
        } else {
            opcionesSection.style.display = 'none';
            opcionesContainer.innerHTML = '';
        }
    }

    // Cargar opciones de datos antiguos (si hubo error de validación)
    function cargarOpcionesAntiguas() {
        @if(old('opciones'))
            const opcionesAntiguas = @json(old('opciones'));
            opcionesAntiguas.forEach((opcion, idx) => {
                if (opcion.vValor || opcion.vEtiqueta) {
                    agregarOpcionSimple(idx, opcion.vValor, opcion.vEtiqueta, opcion.bPredeterminado || false);
                }
            });
            actualizarBotonesEliminar();
        @endif
    }

    // Agregar opción simple sin validaciones complejas
    function agregarOpcionSimple(index, valor = '', etiqueta = '', predeterminado = false) {
        const opcionDiv = document.createElement('div');
        opcionDiv.className = 'row opcion-item mb-2';
        opcionDiv.innerHTML = `
            <div class="col-md-4">
                <input type="text" class="form-control" 
                       name="opciones[${index}][vValor]" 
                       value="${valor}" 
                       placeholder="Valor (ej: tequila)" required>
            </div>
            <div class="col-md-4">
                <input type="text" class="form-control" 
                       name="opciones[${index}][vEtiqueta]" 
                       value="${etiqueta}" 
                       placeholder="Etiqueta (ej: Tequila)" required>
            </div>
            <div class="col-md-3">
                <div class="form-check mt-2">
                    <input type="checkbox" class="form-check-input" 
                           name="opciones[${index}][bPredeterminado]" value="1" ${predeterminado ? 'checked' : ''}>
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

    // Agregar nueva opción
    function agregarOpcion() {
        const index = opcionesContainer.children.length;
        agregarOpcionSimple(index);
        actualizarBotonesEliminar();
    }

    // Actualizar estado de botones eliminar
    function actualizarBotonesEliminar() {
        const botones = opcionesContainer.querySelectorAll('.quitar-opcion');
        botones.forEach((btn, index) => {
            btn.disabled = index === 0;
        });
    }

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
                    const inputs = opcion.querySelectorAll('input[type="text"]');
                    const checkbox = opcion.querySelector('input[type="checkbox"]');
                    inputs[0].name = `opciones[${index}][vValor]`;
                    inputs[1].name = `opciones[${index}][vEtiqueta]`;
                    checkbox.name = `opciones[${index}][bPredeterminado]`;
                });
                actualizarBotonesEliminar();
            }
        }
    });

    // Event listeners
    tipoSelect.addEventListener('change', toggleOpcionesSection);
    agregarOpcionBtn.addEventListener('click', agregarOpcion);

    // Inicializar
    toggleOpcionesSection();
});
</script>
@endpush