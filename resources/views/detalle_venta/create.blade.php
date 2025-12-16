@extends('layout.app')

@section('title', 'Crear Detalle de Venta')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-plus-circle"></i> Crear Nuevo Detalle de Venta
                    </h4>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('detalle_venta.store') }}" method="POST" id="detalleForm">
                        @csrf
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_venta" class="form-label fw-bold">
                                        <i class="fas fa-receipt"></i> Venta *
                                    </label>
                                    <select name="id_venta" id="id_venta" 
                                            class="form-control form-select @error('id_venta') is-invalid @enderror" 
                                            required>
                                        <option value="">-- Seleccionar Venta --</option>
                                        @foreach($ventas as $venta)
                                            <option value="{{ $venta->id_venta }}" 
                                                {{ old('id_venta') == $venta->id_venta ? 'selected' : '' }}>
                                                Venta #{{ $venta->id_venta }} 
                                                @if($venta->dFecha_venta)
                                                    - {{ $venta->dFecha_venta }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_venta')
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_producto" class="form-label fw-bold">
                                        <i class="fas fa-box"></i> Producto *
                                    </label>
                                    <select name="id_producto" id="id_producto" 
                                            class="form-control form-select @error('id_producto') is-invalid @enderror" 
                                            required>
                                        <option value="">-- Seleccionar Producto --</option>
                                        @foreach($productos as $producto)
                                            <option value="{{ $producto->id_producto }}" 
                                                {{ old('id_producto') == $producto->id_producto ? 'selected' : '' }}
                                                data-precio="{{ $producto->dPrecio }}">
                                                {{ $producto->vNombre }} - 
                                                ${{ number_format($producto->dPrecio, 2) }}
                                                @if($producto->iStock)
                                                    (Stock: {{ $producto->iStock }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_producto')
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="iCantidad" class="form-label fw-bold">
                                        <i class="fas fa-hashtag"></i> Cantidad *
                                    </label>
                                    <input type="number" name="iCantidad" id="iCantidad" 
                                           class="form-control @error('iCantidad') is-invalid @enderror" 
                                           value="{{ old('iCantidad', 1) }}" 
                                           min="1" 
                                           required
                                           oninput="calcularSubtotal()">
                                    @error('iCantidad')
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                        </div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Cantidad de productos
                                    </small>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="dPrecio_unitario" class="form-label fw-bold">
                                        <i class="fas fa-tag"></i> Precio Unitario *
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" name="dPrecio_unitario" id="dPrecio_unitario" 
                                               class="form-control @error('dPrecio_unitario') is-invalid @enderror" 
                                               value="{{ old('dPrecio_unitario', 0) }}" 
                                               min="0" 
                                               required
                                               oninput="calcularSubtotal()">
                                    </div>
                                    @error('dPrecio_unitario')
                                        <div class="invalid-feedback d-block">
                                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                        </div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Precio por unidad
                                    </small>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="dSubtotal" class="form-label fw-bold">
                                        <i class="fas fa-calculator"></i> Subtotal
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" name="dSubtotal" id="dSubtotal" 
                                               class="form-control bg-light" 
                                               value="0.00" 
                                               readonly>
                                    </div>
                                    <small class="form-text text-muted">
                                        Calculado automáticamente
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-info-circle fa-2x me-3"></i>
                                <div>
                                    <h6 class="mb-1">Resumen del Detalle</h6>
                                    <p class="mb-0">
                                        <strong>Producto:</strong> <span id="productoNombre">-</span> | 
                                        <strong>Cantidad:</strong> <span id="cantidadResumen">0</span> | 
                                        <strong>Precio Unitario:</strong> $<span id="precioResumen">0.00</span> | 
                                        <strong>Subtotal:</strong> $<span id="subtotalResumen" class="fw-bold">0.00</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('detalle_venta.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Detalle
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cargar precio cuando se selecciona producto
        const productoSelect = document.getElementById('id_producto');
        const precioInput = document.getElementById('dPrecio_unitario');
        
        productoSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const precio = selectedOption.getAttribute('data-precio');
            const productoNombre = selectedOption.text.split(' - ')[0];
            
            if (precio) {
                precioInput.value = parseFloat(precio).toFixed(2);
                document.getElementById('productoNombre').textContent = productoNombre;
            } else {
                precioInput.value = '0.00';
                document.getElementById('productoNombre').textContent = '-';
            }
            
            calcularSubtotal();
        });
        
        // Calcular subtotal inicial
        calcularSubtotal();
    });
    
    function calcularSubtotal() {
        const cantidad = parseFloat(document.getElementById('iCantidad').value) || 0;
        const precio = parseFloat(document.getElementById('dPrecio_unitario').value) || 0;
        const subtotal = cantidad * precio;
        
        // Actualizar inputs
        document.getElementById('dSubtotal').value = subtotal.toFixed(2);
        
        // Actualizar resumen
        document.getElementById('cantidadResumen').textContent = cantidad;
        document.getElementById('precioResumen').textContent = precio.toFixed(2);
        document.getElementById('subtotalResumen').textContent = subtotal.toFixed(2);
    }
</script>
@endpush