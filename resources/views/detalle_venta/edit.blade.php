@extends('layouts.admins')

@section('content')
<div class="container">
    <h1 class="mb-3">Editar Detalle de Venta #{{ $detalleVenta->id_detalle_venta }}</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Revisa los campos:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('detalle_venta.update', $detalleVenta->id_detalle_venta) }}" method="POST" class="card card-body">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">ID Venta *</label>
                    <select name="id_venta" class="form-control" required>
                        <option value="">Seleccione una venta</option>
                        @foreach($ventas as $venta)
                            <option value="{{ $venta->id_venta }}" 
                                @selected(old('id_venta', $detalleVenta->id_venta) == $venta->id_venta)>
                                Venta #{{ $venta->id_venta }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Producto *</label>
                    <select name="id_producto" class="form-control" required>
                        <option value="">Seleccione un producto</option>
                        @foreach($productos as $producto)
                            <option value="{{ $producto->id_producto }}" 
                                @selected(old('id_producto', $detalleVenta->id_producto) == $producto->id_producto)>
                                {{ $producto->vNombre }} - ${{ number_format($producto->dPrecio, 2) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Cantidad *</label>
                    <input type="number" name="iCantidad" class="form-control"
                           value="{{ old('iCantidad', $detalleVenta->iCantidad) }}" required min="1">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Precio Unitario *</label>
                    <input type="number" step="0.01" name="dPrecio_unitario" class="form-control"
                           value="{{ old('dPrecio_unitario', $detalleVenta->dPrecio_unitario) }}" required min="0">
                </div>
            </div>
        </div>

        <div class="mb-4">
            <div class="alert alert-info">
                <strong>Nota:</strong> El subtotal se calculará automáticamente como: 
                <span class="fw-bold">Cantidad × Precio Unitario</span>
                <br>
                <span class="text-muted">Subtotal = {{ old('iCantidad', $detalleVenta->iCantidad) }} × ${{ number_format(old('dPrecio_unitario', $detalleVenta->dPrecio_unitario), 2) }} = ${{ number_format(old('iCantidad', $detalleVenta->iCantidad) * old('dPrecio_unitario', $detalleVenta->dPrecio_unitario), 2) }}</span>
            </div>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('detalle_venta.show', $detalleVenta->id_detalle_venta) }}" class="btn btn-outline-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Actualizar Detalle</button>
        </div>
    </form>
</div>
@endsection