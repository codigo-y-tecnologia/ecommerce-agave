@extends('layout.app')

@section('title', 'Editar Detalle de Venta')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-edit"></i> Editar Detalle de Venta #{{ $detalleVenta->id_detalle_venta }}
                    </h4>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('detalle_venta.update', $detalleVenta->id_detalle_venta) }}" method="POST" id="detalleForm">
                        @csrf
                        @method('PUT')
                        
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
                                                {{ old('id_venta', $detalleVenta->id_venta) == $venta->id_venta ? 'selected' : '' }}>
                                                Venta #{{ $venta->id_venta }}
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
                                                {{ old('id_producto', $detalleVenta->id_producto) == $producto->id_producto ? 'selected' : '' }}
                                                data-precio="{{ $producto->dPrecio }}">
                                                {{ $producto->vNombre }} - ${{ number_format($producto->dPrecio, 2) }}
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
                                           value="{{ old('iCantidad', $detalleVenta->iCantidad) }}" 
                                           min="1" 
                                           required
                                           oninput="calcularSubtotal()">
                                    @error('iCantidad')
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                        </div>
                                    @enderror
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
                                               value="{{ old('dPrecio_unitario', $detalleVenta->dPrecio_unitario) }}" 
                                               min="0" 
                                               required
                                               oninput="calcularSubtotal()">
                                    </div>
                                    @error('dPrecio_unitario')
                                        <div class="invalid-feedback d-block">
                                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                        </div>
                                    @enderror
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
                                               value="{{ old('dSubtotal', $detalleVenta->dSubtotal) }}" 
                                               readonly>
                                    </div>
                                    <small class="form-text text-muted">
                                        Cantidad × Precio Unitario
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-warning">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                                <div>
                                    <h6 class="mb-1">Información Original</h6>
                                    <p class="mb-0">
                                        <strong>Venta:</strong> #{{ $detalleVenta->id_venta }} | 
                                        <strong>Producto:</strong> #{{ $detalleVenta->id_producto }} | 
                                        <strong>Subtotal Original:</strong> ${{ number_format($detalleVenta->dSubtotal, 2) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('detalle_venta.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                            <div>
                                <a href="{{ route('detalle_venta.show', $detalleVenta->id_detalle_venta) }}" 
                                   class="btn btn-info me-2">
                                    <i class="fas fa-eye"></i> Ver
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Actualizar
                                </button>
                            </div>
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
        calcularSubtotal();
        
        // Cargar precio del producto seleccionado
        const productoSelect = document.getElementById('id_producto');
        const precioInput = document.getElementById('dPrecio_unitario');
        
        productoSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const precio = selectedOption.getAttribute('data-precio');
            if (precio) {
                precioInput.value = parseFloat(precio).toFixed(2);
            }
            calcularSubtotal();
        });
    });
    
    function calcularSubtotal() {
        const cantidad = parseFloat(document.getElementById('iCantidad').value) || 0;
        const precio = parseFloat(document.getElementById('dPrecio_unitario').value) || 0;
        const subtotal = cantidad * precio;
        document.getElementById('dSubtotal').value = subtotal.toFixed(2);
    }
</script>
@endpush