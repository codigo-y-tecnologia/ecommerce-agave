@extends('layouts.admins')

@section('content')
<div class="container">
    <h1 class="mb-3">Editar Venta #{{ $venta->id_venta }}</h1>

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

    <form action="{{ route('ventas.update', $venta->id_venta) }}" method="POST" class="card card-body">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">ID Pedido</label>
                    <input type="number" name="id_pedido" class="form-control" 
                           value="{{ old('id_pedido', $venta->id_pedido) }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">ID Usuario</label>
                    <input type="number" name="id_usuario" class="form-control"
                           value="{{ old('id_usuario', $venta->id_usuario) }}">
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Total</label>
            <input type="number" step="0.01" name="dTotal" class="form-control"
                   value="{{ old('dTotal', $venta->dTotal) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Método de Pago</label>
            <select name="eMetodo_pago" class="form-select" required>
                <option value="stripe" @selected(old('eMetodo_pago', $venta->eMetodo_pago)=='stripe')>Stripe</option>
                <option value="paypal" @selected(old('eMetodo_pago', $venta->eMetodo_pago)=='paypal')>PayPal</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="form-label">Estado</label>
            <select name="eEstado" class="form-select" required>
                <option value="completada" @selected(old('eEstado', $venta->eEstado)=='completada')>Completada</option>
                <option value="devuelta" @selected(old('eEstado', $venta->eEstado)=='devuelta')>Devuelta</option>
                <option value="reembolsada" @selected(old('eEstado', $venta->eEstado)=='reembolsada')>Reembolsada</option>
                <option value="cancelada" @selected(old('eEstado', $venta->eEstado)=='cancelada')>Cancelada</option>
            </select>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('ventas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Actualizar Venta</button>
        </div>
    </form>
</div>
@endsection