@extends('layouts.app')

@section('title', 'Atributos del Producto - ' . $producto->vNombre)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-tags me-2"></i>Atributos del Producto: {{ $producto->vNombre }}
                    </h3>
                    <div>
                        <a href="{{ route('productos.edit', $producto) }}" class="btn btn-secondary me-2">
                            <i class="fas fa-edit me-1"></i> Editar Producto
                        </a>
                        <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Formulario para agregar nuevo atributo -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-plus me-2"></i>Agregar Nuevo Atributo
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('productos.atributos.store', $producto) }}" method="POST" id="atributoForm">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="id_atributo">Atributo *</label>
                                                    <select class="form-control @error('id_atributo') is-invalid @enderror" 
                                                            id="id_atributo" name="id_atributo" required>
                                                        <option value="">Seleccione un atributo</option>
                                                        @foreach($atributosDisponibles as $atributo)
                                                            <option value="{{ $atributo->id_atributo }}">
                                                                {{ $atributo->vNombre }} ({{ $atributo->eTipo }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('id_atributo')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="id_opcion">Opción</label>
                                                    <select class="form-control @error('id_opcion') is-invalid @enderror" 
                                                            id="id_opcion" name="id_opcion">
                                                        <option value="">Seleccione una opción</option>
                                                    </select>
                                                    @error('id_opcion')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="vValor">Valor Personalizado</label>
                                                    <input type="text" class="form-control @error('vValor') is-invalid @enderror" 
                                                           id="vValor" name="vValor" placeholder="Ingrese un valor personalizado">
                                                    @error('vValor')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-12">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-save me-1"></i> Agregar Atributo
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lista de atributos existentes -->
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Atributos Asignados</h5>
                            
                            @if($producto->productoAtributos->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>Atributo</th>
                                                <th>Tipo</th>
                                                <th>Valor/Opción</th>
                                                <th width="120">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($producto->productoAtributos as $productoAtributo)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $productoAtributo->atributo->vNombre }}</strong>
                                                        @if($productoAtributo->atributo->vLabel)
                                                            <br><small class="text-muted">{{ $productoAtributo->atributo->vLabel }}</small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @php
                                                            $badgeClass = [
                                                                'texto' => 'primary',
                                                                'textarea' => 'info',
                                                                'select' => 'success',
                                                                'radio' => 'warning',
                                                                'checkbox' => 'secondary',
                                                                'archivo' => 'dark'
                                                            ][$productoAtributo->atributo->eTipo] ?? 'secondary';
                                                        @endphp
                                                        <span class="badge bg-{{ $badgeClass }}">
                                                            {{ $productoAtributo->atributo->eTipo }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if($productoAtributo->opcion)
                                                            <span class="badge bg-success">{{ $productoAtributo->opcion->vEtiqueta }}</span>
                                                        @elseif($productoAtributo->vValor)
                                                            <code>{{ $productoAtributo->vValor }}</code>
                                                        @else
                                                            <span class="text-muted">No asignado</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <button type="button" class="btn btn-warning" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#editModal{{ $loop->index }}">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <!-- CORRECCIÓN: Usar los parámetros corregidos -->
                                                            <form action="{{ route('productos.atributos.destroy', [
                                                                'producto' => $producto->id_producto, 
                                                                'atributo' => $productoAtributo->id_atributo
                                                            ]) }}" 
                                                                  method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger" 
                                                                        onclick="return confirm('¿Estás seguro de eliminar este atributo?')">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>

                                                        <!-- Modal para editar -->
                                                        <div class="modal fade" id="editModal{{ $loop->index }}" tabindex="-1">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Editar Atributo</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                    </div>
                                                                    <!-- CORRECCIÓN: Usar los parámetros corregidos -->
                                                                    <form action="{{ route('productos.atributos.update', [
                                                                        'producto' => $producto->id_producto, 
                                                                        'atributo' => $productoAtributo->id_atributo
                                                                    ]) }}" method="POST">
                                                                        @csrf
                                                                        @method('PUT')
                                                                        <div class="modal-body">
                                                                            <div class="form-group">
                                                                                <label>Atributo</label>
                                                                                <input type="text" class="form-control" 
                                                                                       value="{{ $productoAtributo->atributo->vNombre }}" disabled>
                                                                            </div>
                                                                            @if($productoAtributo->atributo->opciones->count() > 0)
                                                                                <div class="form-group">
                                                                                    <label for="edit_opcion_{{ $loop->index }}">Opción</label>
                                                                                    <select class="form-control" 
                                                                                            id="edit_opcion_{{ $loop->index }}" 
                                                                                            name="id_opcion">
                                                                                        <option value="">Seleccione una opción</option>
                                                                                        @foreach($productoAtributo->atributo->opciones as $opcion)
                                                                                            <option value="{{ $opcion->id_opcion }}" 
                                                                                                {{ $productoAtributo->id_opcion == $opcion->id_opcion ? 'selected' : '' }}>
                                                                                                {{ $opcion->vEtiqueta }}
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            @endif
                                                                            <div class="form-group">
                                                                                <label for="edit_valor_{{ $loop->index }}">Valor Personalizado</label>
                                                                                <input type="text" class="form-control" 
                                                                                       id="edit_valor_{{ $loop->index }}" 
                                                                                       name="vValor" 
                                                                                       value="{{ $productoAtributo->vValor }}"
                                                                                       placeholder="Ingrese un valor personalizado">
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No hay atributos asignados</h5>
                                    <p class="text-muted">Agrega atributos usando el formulario superior</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const atributoSelect = document.getElementById('id_atributo');
    const opcionSelect = document.getElementById('id_opcion');
    const valorInput = document.getElementById('vValor');

    // Cargar opciones cuando se selecciona un atributo
    atributoSelect.addEventListener('change', function() {
        const atributoId = this.value;
        
        if (atributoId) {
            fetch(`/atributos/${atributoId}/opciones`)
                .then(response => response.json())
                .then(data => {
                    // Limpiar opciones anteriores
                    opcionSelect.innerHTML = '<option value="">Seleccione una opción</option>';
                    
                    // Agregar nuevas opciones
                    data.opciones.forEach(opcion => {
                        const option = document.createElement('option');
                        option.value = opcion.id_opcion;
                        option.textContent = opcion.vEtiqueta;
                        opcionSelect.appendChild(option);
                    });

                    // Mostrar/ocultar campos según el tipo
                    if (data.tipo === 'texto' || data.tipo === 'textarea') {
                        valorInput.style.display = 'block';
                        valorInput.closest('.form-group').style.display = 'block';
                        opcionSelect.style.display = 'none';
                        opcionSelect.closest('.form-group').style.display = 'none';
                    } else if (['select', 'radio', 'checkbox'].includes(data.tipo)) {
                        valorInput.style.display = 'none';
                        valorInput.closest('.form-group').style.display = 'none';
                        opcionSelect.style.display = 'block';
                        opcionSelect.closest('.form-group').style.display = 'block';
                    } else {
                        valorInput.style.display = 'block';
                        valorInput.closest('.form-group').style.display = 'block';
                        opcionSelect.style.display = 'block';
                        opcionSelect.closest('.form-group').style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    opcionSelect.innerHTML = '<option value="">Error al cargar opciones</option>';
                });
        } else {
            opcionSelect.innerHTML = '<option value="">Seleccione una opción</option>';
            valorInput.style.display = 'block';
            opcionSelect.style.display = 'block';
        }
    });

    // Inicializar estado
    if (atributoSelect.value) {
        atributoSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush