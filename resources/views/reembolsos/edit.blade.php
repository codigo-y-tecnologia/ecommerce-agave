@extends('layout.app')

@section('content')
<div class="container">
    <h1 class="mb-3">Editar Reembolso #{{ $reembolso->id_reembolso }}</h1>

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

    <form action="{{ route('reembolsos.update', $reembolso->id_reembolso) }}" method="POST" class="card card-body">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">ID Venta *</label>
                    <input type="number" name="id_venta" class="form-control" 
                           value="{{ old('id_venta', $reembolso->id_venta) }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Monto *</label>
                    <input type="number" step="0.01" name="dMonto" class="form-control"
                           value="{{ old('dMonto', $reembolso->dMonto) }}" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Fecha Reembolso *</label>
                    <input type="datetime-local" name="tFecha_reembolso" class="form-control"
                           value="{{ old('tFecha_reembolso', $reembolso->tFecha_reembolso ? \Carbon\Carbon::parse($reembolso->tFecha_reembolso)->format('Y-m-d\TH:i') : '') }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Método de Pago *</label>
                    <select name="eMetodo_pago" class="form-select" required>
                        <option value="paypal" @selected(old('eMetodo_pago', $reembolso->eMetodo_pago)=='paypal')>PayPal</option>
                        <option value="stripe" @selected(old('eMetodo_pago', $reembolso->eMetodo_pago)=='stripe')>Stripe</option>
                        <option value="tarjeta" @selected(old('eMetodo_pago', $reembolso->eMetodo_pago)=='tarjeta')>Tarjeta</option>
                        <option value="transferencia" @selected(old('eMetodo_pago', $reembolso->eMetodo_pago)=='transferencia')>Transferencia</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Motivo</label>
            <textarea name="vMotivo" class="form-control" rows="3">{{ old('vMotivo', $reembolso->vMotivo) }}</textarea>
        </div>

        <div class="mb-4">
            <label class="form-label">Estado *</label>
            <select name="eEstado" class="form-select" required>
                <option value="pendiente" @selected(old('eEstado', $reembolso->eEstado)=='pendiente')>Pendiente</option>
                <option value="procesado" @selected(old('eEstado', $reembolso->eEstado)=='Procesado')>Procesado</option>
                <option value="fallido" @selected(old('eEstado', $reembolso->eEstado)=='fallido')>Fallido</option>
            </select>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('reembolsos.show', $reembolso->id_reembolso) }}" class="btn btn-outline-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Actualizar Reembolso</button>
        </div>
    </form>
</div>
@endsection