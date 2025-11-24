@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Editar Reembolso</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('reembolsos.update', $reembolso->id_reembolso) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group mb-3">
                            <label for="Id_venta" class="form-label">ID Venta</label>
                            <input type="number" class="form-control @error('Id_venta') is-invalid @enderror" 
                                   id="Id_venta" name="Id_venta" value="{{ old('Id_venta', $reembolso->Id_venta) }}" 
                                   placeholder="Ingrese el ID de la venta" required>
                            @error('Id_venta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="dMonto" class="form-label">Monto</label>
                            <input type="number" step="0.01" class="form-control @error('dMonto') is-invalid @enderror" 
                                   id="dMonto" name="dMonto" value="{{ old('dMonto', $reembolso->dMonto) }}" 
                                   placeholder="0.00" min="0" required>
                            @error('dMonto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="vMotivo" class="form-label">Motivo</label>
                            <textarea class="form-control @error('vMotivo') is-invalid @enderror" 
                                      id="vMotivo" name="vMotivo" rows="3" 
                                      placeholder="Ingrese el motivo del reembolso" required>{{ old('vMotivo', $reembolso->vMotivo) }}</textarea>
                            @error('vMotivo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="eMetodo_pago" class="form-label">Método de Pago</label>
                            <select class="form-control @error('eMetodo_pago') is-invalid @enderror" 
                                    id="eMetodo_pago" name="eMetodo_pago" required>
                                <option value="">Seleccione un método</option>
                                <option value="paypal" {{ old('eMetodo_pago', $reembolso->eMetodo_pago) == 'paypal' ? 'selected' : '' }}>PayPal</option>
                                <option value="transferencia" {{ old('eMetodo_pago', $reembolso->eMetodo_pago) == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                                <option value="tarjeta" {{ old('eMetodo_pago', $reembolso->eMetodo_pago) == 'tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                                <option value="stripe" {{ old('eMetodo_pago', $reembolso->eMetodo_pago) == 'stripe' ? 'selected' : '' }}>Stripe</option>
                            </select>
                            @error('eMetodo_pago')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="eEstado" class="form-label">Estado</label>
                            <select class="form-control @error('eEstado') is-invalid @enderror" 
                                    id="eEstado" name="eEstado" required>
                                <option value="">Seleccione un estado</option>
                                <option value="pendiente" {{ old('eEstado', $reembolso->eEstado) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="procesado" {{ old('eEstado', $reembolso->eEstado) == 'procesado' ? 'selected' : '' }}>Procesado</option>
                                <option value="fallido" {{ old('eEstado', $reembolso->eEstado) == 'fallido' ? 'selected' : '' }}>Fallido</option>
                            </select>
                            @error('eEstado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('reembolsos.index') }}" class="btn btn-secondary me-md-2">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Actualizar Reembolso
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection