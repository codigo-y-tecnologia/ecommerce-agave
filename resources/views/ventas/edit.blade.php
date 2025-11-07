@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Editar Venta #{{ $venta->id_venta }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('ventas.update', $venta->id_venta) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="id_pedido" class="form-label">ID Pedido</label>
                                <input type="number" class="form-control @error('id_pedido') is-invalid @enderror" id="id_pedido" name="id_pedido" value="{{ old('id_pedido', $venta->id_pedido) }}" required>
                                @error('id_pedido')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="id_usuario" class="form-label">ID Usuario</label>
                                <input type="number" class="form-control @error('id_usuario') is-invalid @enderror" id="id_usuario" name="id_usuario" value="{{ old('id_usuario', $venta->id_usuario) }}" required>
                                @error('id_usuario')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="dTotal" class="form-label">Total</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control @error('dTotal') is-invalid @enderror" id="dTotal" name="dTotal" value="{{ old('dTotal', $venta->dTotal) }}" required>
                                @error('dTotal')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="eMetodo_pago" class="form-label">Método de Pago</label>
                            <select class="form-select @error('eMetodo_pago') is-invalid @enderror" id="eMetodo_pago" name="eMetodo_pago" required>
                                <option value="stripe" {{ old('eMetodo_pago', $venta->eMetodo_pago) == 'stripe' ? 'selected' : '' }}>Stripe</option>
                                <option value="tarjeta" {{ old('eMetodo_pago', $venta->eMetodo_pago) == 'tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                                <option value="transferencia" {{ old('eMetodo_pago', $venta->eMetodo_pago) == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                            </select>
                            @error('eMetodo_pago')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="eEstado" class="form-label">Estado</label>
                            <select class="form-select @error('eEstado') is-invalid @enderror" id="eEstado" name="eEstado">
                                <option value="completada" {{ old('eEstado', $venta->eEstado) == 'completada' ? 'selected' : '' }}>Completada</option>
                                <option value="devuelta" {{ old('eEstado', $venta->eEstado) == 'devuelta' ? 'selected' : '' }}>Devuelta</option>
                                <option value="reembolsada" {{ old('eEstado', $venta->eEstado) == 'reembolsada' ? 'selected' : '' }}>Reembolsada</option>
                                <option value="cancelada" {{ old('eEstado', $venta->eEstado) == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                            </select>
                            @error('eEstado')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('ventas.show', $venta->id_venta) }}" class="btn btn-secondary me-md-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Actualizar Venta</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection