@extends('layouts.app')

@section('title', 'Atributos y Variaciones - ' . $producto->vNombre)
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1><i class="fas fa-wine-bottle me-2"></i>{{ $producto->vNombre }}</h1>
            <p class="text-muted">Gestionar atributos y variaciones del producto</p>
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

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-cogs me-2"></i>Atributos Disponibles</h5>
                </div>
                <div class="card-body">
                    <div id="atributos-container">
                        @foreach($atributos as $atributo)
                        <div class="atributo-card mb-3 p-3 border rounded">
                            <div class="form-check">
                                <input type="checkbox" 
                                       class="form-check-input atributo-checkbox" 
                                       id="atributo-{{ $atributo->id_atributo }}"
                                       data-id="{{ $atributo->id_atributo }}"
                                       data-nombre="{{ $atributo->vNombre }}">
                                <label class="form-check-label fw-bold" for="atributo-{{ $atributo->id_atributo }}">
                                    {{ $atributo->vNombre }}
                                </label>
                            </div>
                            
                            <div class="valores-container mt-2" 
                                 id="valores-{{ $atributo->id_atributo }}"
                                 style="display: none;">
                                <small class="text-muted d-block mb-2">Selecciona los valores:</small>
                                <div class="row">
                                    @foreach($atributo->valoresActivos as $valor)
                                    <div class="col-6 mb-2">
                                        <div class="form-check">
                                            <input type="checkbox" 
                                                   class="form-check-input valor-checkbox" 
                                                   id="valor-{{ $valor->id_atributo_valor }}"
                                                   data-atributo-id="{{ $atributo->id_atributo }}"
                                                   data-valor-id="{{ $valor->id_atributo_valor }}"
                                                   data-valor-nombre="{{ $valor->vValor }}">
                                            <label class="form-check-label" for="valor-{{ $valor->id_atributo_valor }}">
                                                <div class="d-flex align-items-center">
                                                    @if($valor->vHexColor)
                                                    <div class="me-2" style="width: 15px; height: 15px; background-color: {{ $valor->vHexColor }}; border-radius: 2px; border: 1px solid #dee2e6;"></div>
                                                    @endif
                                                    <span>{{ $valor->vValor }}</span>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <button class="btn btn-primary w-100 mt-3" onclick="generarCombinaciones()">
                        <i class="fas fa-magic me-2"></i> Generar Combinaciones
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list-alt me-2"></i>Variaciones Generadas</h5>
                    <span id="total-variaciones" class="badge bg-light text-dark">0 variaciones</span>
                </div>
                <div class="card-body">
                    <div id="combinaciones-container" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-hover" id="tabla-variaciones">
                                <thead class="table-dark">
                                    <tr>
                                        <th>SKU</th>
                                        <th>Combinación</th>
                                        <th>Precio</th>
                                        <th>Stock</th>
                                        <th>Código Barras</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="variaciones-body">
                                    <!-- Las variaciones se insertarán aquí -->
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <button class="btn btn-success" onclick="guardarVariaciones()">
                                <i class="fas fa-save me-2"></i> Guardar Variaciones
                            </button>
                            <button class="btn btn-outline-danger" onclick="limpiarCombinaciones()">
                                <i class="fas fa-trash me-2"></i> Limpiar Todo
                            </button>
                        </div>
                    </div>
                    
                    <div id="sin-combinaciones" class="text-center py-5">
                        <i class="fas fa-cubes fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">No hay variaciones generadas</h4>
                        <p class="text-muted">Selecciona atributos y haz clic en "Generar Combinaciones"</p>
                    </div>
                </div>
            </div>
            
            <!-- Sección para variaciones existentes -->
            @if($producto->variaciones->count() > 0)
            <div class="card mt-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>Variaciones Existentes</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>SKU</th>
                                    <th>Combinación</th>
                                    <th>Precio</th>
                                    <th>Stock</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($producto->variaciones as $variacion)
                                <tr>
                                    <td><code>{{ $variacion->vSKU }}</code></td>
                                    <td>
                                        <small>{{ $variacion->nombre_combinacion }}</small>
                                    </td>
                                    <td>
                                        ${{ number_format($variacion->dPrecio, 2) }}
                                        @if($variacion->tieneOferta())
                                        <br>
                                        <small class="text-success">Oferta: ${{ number_format($variacion->dPrecio_oferta, 2) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $variacion->iStock > 10 ? 'bg-success' : ($variacion->iStock > 0 ? 'bg-warning' : 'bg-danger') }}">
                                            {{ $variacion->iStock }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $variacion->bActivo ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $variacion->bActivo ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal para editar variación -->
<div class="modal fade" id="modalEditarVariacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Editar Variación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formEditarVariacion">
                    <input type="hidden" id="edit-variacion-index">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>SKU *</label>
                                <input type="text" id="edit-sku" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Código de Barras</label>
                                <input type="text" id="edit-codigo-barras" class="form-control">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Precio *</label>
                                <input type="number" id="edit-precio" class="form-control" min="0" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Precio de Oferta</label>
                                <input type="number" id="edit-precio-oferta" class="form-control" min="0" step="0.01">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Stock *</label>
                                <input type="number" id="edit-stock" class="form-control" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Peso (kg)</label>
                                <input type="number" id="edit-peso" class="form-control" min="0" step="0.01">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label>Ancho (cm)</label>
                                <input type="number" id="edit-ancho" class="form-control" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label>Alto (cm)</label>
                                <input type="number" id="edit-alto" class="form-control" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label>Profundidad (cm)</label>
                                <input type="number" id="edit-profundidad" class="form-control" min="0" step="0.01">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarEdicionVariacion()">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.atributo-card {
    transition: all 0.3s ease;
}

.atributo-card:hover {
    background-color: #f8f9fa;
    transform: translateY(-2px);
}

.valor-checkbox:checked + label {
    font-weight: bold;
    color: #0d6efd;
}

#variaciones-body tr {
    cursor: pointer;
    transition: background-color 0.2s ease;
}

#variaciones-body tr:hover {
    background-color: #f8f9fa;
}
</style>
@endsection

@section('scripts')
<script>
let combinaciones = [];
let atributosSeleccionados = [];

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
    });
});

function obtenerAtributosSeleccionados() {
    atributosSeleccionados = [];
    
    document.querySelectorAll('.atributo-checkbox:checked').forEach(atributoCheckbox => {
        const atributoId = parseInt(atributoCheckbox.dataset.id);
        const valoresSeleccionados = [];
        
        document.querySelectorAll(`.valor-checkbox[data-atributo-id="${atributoId}"]:checked`).forEach(valorCheckbox => {
            valoresSeleccionados.push(parseInt(valorCheckbox.dataset.valorId));
        });
        
        if (valoresSeleccionados.length > 0) {
            atributosSeleccionados.push({
                id_atributo: atributoId,
                valores: valoresSeleccionados
            });
        }
    });
    
    return atributosSeleccionados;
}

function generarCombinaciones() {
    const atributos = obtenerAtributosSeleccionados();
    
    if (atributos.length === 0) {
        alert('Por favor, selecciona al menos un atributo con valores.');
        return;
    }
    
    // Verificar que cada atributo tenga al menos un valor seleccionado
    for (const atributo of atributos) {
        if (atributo.valores.length === 0) {
            alert(`El atributo no tiene valores seleccionados.`);
            return;
        }
    }
    
    // Calcular total de combinaciones
    let totalCombinaciones = 1;
    for (const atributo of atributos) {
        totalCombinaciones *= atributo.valores.length;
    }
    
    if (totalCombinaciones > 50) {
        if (!confirm(`Se generarán ${totalCombinaciones} combinaciones. ¿Deseas continuar?`)) {
            return;
        }
    }
    
    // Mostrar loading
    const btnGenerar = event.target;
    const originalText = btnGenerar.innerHTML;
    btnGenerar.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Generando...';
    btnGenerar.disabled = true;
    
    // Enviar solicitud al servidor
    fetch('{{ route("productos.generar-combinaciones", $producto->id_producto) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            atributos_seleccionados: atributos
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            combinaciones = data.combinaciones;
            mostrarCombinaciones();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al generar combinaciones');
    })
    .finally(() => {
        btnGenerar.innerHTML = originalText;
        btnGenerar.disabled = false;
    });
}

function mostrarCombinaciones() {
    if (combinaciones.length === 0) {
        document.getElementById('combinaciones-container').style.display = 'none';
        document.getElementById('sin-combinaciones').style.display = 'block';
        document.getElementById('total-variaciones').textContent = '0 variaciones';
        return;
    }
    
    const tbody = document.getElementById('variaciones-body');
    tbody.innerHTML = '';
    
    combinaciones.forEach((combinacion, index) => {
        const row = document.createElement('tr');
        row.dataset.index = index;
        
        // Crear texto de combinación
        let combinacionTexto = '';
        combinacion.atributos.forEach(atributo => {
            combinacionTexto += `<div><small>${atributo.nombre_atributo}: <strong>${atributo.valor}</strong></small></div>`;
        });
        
        row.innerHTML = `
            <td><code>${combinacion.sku}</code></td>
            <td>${combinacionTexto}</td>
            <td>
                <input type="number" class="form-control form-control-sm precio-input" 
                       value="${combinacion.precio}" min="0" step="0.01"
                       data-index="${index}" style="width: 100px;">
                ${combinacion.precio_oferta ? `<br><small class="text-success">Oferta: $${combinacion.precio_oferta}</small>` : ''}
            </td>
            <td>
                <input type="number" class="form-control form-control-sm stock-input" 
                       value="${combinacion.stock}" min="0"
                       data-index="${index}" style="width: 80px;">
            </td>
            <td><small>${combinacion.codigo_barras}</small></td>
            <td>
                <button class="btn btn-sm btn-warning" onclick="editarVariacion(${index})" title="Editar">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-danger" onclick="eliminarVariacion(${index})" title="Eliminar">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        
        tbody.appendChild(row);
    });
    
    // Agregar event listeners para inputs
    document.querySelectorAll('.precio-input').forEach(input => {
        input.addEventListener('change', function() {
            const index = parseInt(this.dataset.index);
            combinaciones[index].precio = parseFloat(this.value);
        });
    });
    
    document.querySelectorAll('.stock-input').forEach(input => {
        input.addEventListener('change', function() {
            const index = parseInt(this.dataset.index);
            combinaciones[index].stock = parseInt(this.value);
        });
    });
    
    document.getElementById('combinaciones-container').style.display = 'block';
    document.getElementById('sin-combinaciones').style.display = 'none';
    document.getElementById('total-variaciones').textContent = `${combinaciones.length} variaciones`;
}

function editarVariacion(index) {
    const combinacion = combinaciones[index];
    
    document.getElementById('edit-variacion-index').value = index;
    document.getElementById('edit-sku').value = combinacion.sku;
    document.getElementById('edit-codigo-barras').value = combinacion.codigo_barras;
    document.getElementById('edit-precio').value = combinacion.precio;
    document.getElementById('edit-precio-oferta').value = combinacion.precio_oferta;
    document.getElementById('edit-stock').value = combinacion.stock;
    document.getElementById('edit-peso').value = combinacion.peso;
    document.getElementById('edit-ancho').value = combinacion.ancho;
    document.getElementById('edit-alto').value = combinacion.alto;
    document.getElementById('edit-profundidad').value = combinacion.profundidad;
    
    const modal = new bootstrap.Modal(document.getElementById('modalEditarVariacion'));
    modal.show();
}

function guardarEdicionVariacion() {
    const index = document.getElementById('edit-variacion-index').value;
    
    combinaciones[index].sku = document.getElementById('edit-sku').value;
    combinaciones[index].codigo_barras = document.getElementById('edit-codigo-barras').value;
    combinaciones[index].precio = parseFloat(document.getElementById('edit-precio').value) || 0;
    combinaciones[index].precio_oferta = document.getElementById('edit-precio-oferta').value ? 
        parseFloat(document.getElementById('edit-precio-oferta').value) : null;
    combinaciones[index].stock = parseInt(document.getElementById('edit-stock').value) || 0;
    combinaciones[index].peso = document.getElementById('edit-peso').value ? 
        parseFloat(document.getElementById('edit-peso').value) : null;
    combinaciones[index].ancho = document.getElementById('edit-ancho').value ? 
        parseFloat(document.getElementById('edit-ancho').value) : null;
    combinaciones[index].alto = document.getElementById('edit-alto').value ? 
        parseFloat(document.getElementById('edit-alto').value) : null;
    combinaciones[index].profundidad = document.getElementById('edit-profundidad').value ? 
        parseFloat(document.getElementById('edit-profundidad').value) : null;
    
    // Actualizar la tabla
    mostrarCombinaciones();
    
    // Cerrar modal
    bootstrap.Modal.getInstance(document.getElementById('modalEditarVariacion')).hide();
}

function eliminarVariacion(index) {
    if (confirm('¿Estás seguro de eliminar esta variación?')) {
        combinaciones.splice(index, 1);
        mostrarCombinaciones();
    }
}

function limpiarCombinaciones() {
    if (combinaciones.length > 0 && confirm('¿Estás seguro de limpiar todas las variaciones?')) {
        combinaciones = [];
        mostrarCombinaciones();
    }
}

function guardarVariaciones() {
    if (combinaciones.length === 0) {
        alert('No hay variaciones para guardar.');
        return;
    }
    
    // Preparar datos para enviar
    const variacionesData = combinaciones.map(combinacion => {
        return {
            vSKU: combinacion.sku,
            vCodigo_barras: combinacion.codigo_barras,
            dPrecio: combinacion.precio,
            dPrecio_oferta: combinacion.precio_oferta,
            iStock: combinacion.stock,
            dPeso: combinacion.peso,
            dAncho: combinacion.ancho,
            dAlto: combinacion.alto,
            dProfundidad: combinacion.profundidad,
            atributos: combinacion.atributos.map(atributo => {
                return {
                    id_atributo: atributo.id_atributo,
                    id_atributo_valor: atributo.id_atributo_valor
                };
            })
        };
    });
    
    // Mostrar loading
    const btnGuardar = event.target;
    const originalText = btnGuardar.innerHTML;
    btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Guardando...';
    btnGuardar.disabled = true;
    
    // Enviar al servidor
    fetch('{{ route("productos.guardar-variaciones", $producto->id_producto) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            variaciones: variacionesData
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Variaciones guardadas exitosamente');
            window.location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al guardar variaciones');
    })
    .finally(() => {
        btnGuardar.innerHTML = originalText;
        btnGuardar.disabled = false;
    });
}
</script>
@endsection