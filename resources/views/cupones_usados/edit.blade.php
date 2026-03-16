@extends('layouts.admins')

@section('content')
<div class="container">
    <h1 class="mb-3">Editar Cupón Usado — Venta #{{ $cuponUsado->id_venta }}</h1>

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

    <form action="{{ route('cupones_usados.update', ['id' => $cuponUsado->id_cupon . '-' . $cuponUsado->id_venta]) }}"
          method="POST" class="card card-body">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Cupón *</label>
                    <select name="id_cupon" class="form-control" required>
                        <option value="">Seleccione un cupón</option>
                        @foreach($cupones as $cupon)
                            <option value="{{ $cupon->id_cupon }}"
                                @selected(old('id_cupon', $cuponUsado->id_cupon) == $cupon->id_cupon)>
                                {{ $cupon->vCodigo_cupon }} ({{ ucfirst($cupon->eTipo) }} — ${{ number_format($cupon->dDescuento,2) }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Usuario</label>
                    <select name="id_usuario" class="form-control">
                        <option value="">Sin usuario (invitado)</option>
                        @foreach($usuarios as $usuario)
                            <option value="{{ $usuario->id_usuario }}"
                                @selected(old('id_usuario', $cuponUsado->id_usuario) == $usuario->id_usuario)>
                                {{ $usuario->vNombre }} {{ $usuario->vApaterno }} — {{ $usuario->vEmail }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Venta *</label>
                    <select name="id_venta" class="form-control" required>
                        <option value="">Seleccione una venta</option>
                        @foreach($ventas as $venta)
                            <option value="{{ $venta->id_venta }}"
                                @selected(old('id_venta', $cuponUsado->id_venta) == $venta->id_venta)>
                                Venta #{{ $venta->id_venta }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Guest Token</label>
                    <input type="text" name="guest_token" class="form-control"
                           value="{{ old('guest_token', $cuponUsado->guest_token) }}"
                           placeholder="Solo para compras de invitados" maxlength="36">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Fecha de Uso *</label>
                    <input type="datetime-local" name="tFecha_uso" class="form-control"
                           value="{{ old('tFecha_uso', \Carbon\Carbon::parse($cuponUsado->tFecha_uso)->format('Y-m-d\TH:i')) }}"
                           required>
                </div>
            </div>
        </div>

        <div class="mb-4">
            <div class="alert alert-info">
                <strong>Registro actual:</strong>
                Cupón #{{ $cuponUsado->id_cupon }} —
                Venta #{{ $cuponUsado->id_venta }} —
                Fecha: {{ \Carbon\Carbon::parse($cuponUsado->tFecha_uso)->format('d/m/Y H:i') }}
            </div>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('cupones_usados.show', ['id' => $cuponUsado->id_cupon . '-' . $cuponUsado->id_venta]) }}"
               class="btn btn-outline-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Actualizar Registro</button>
        </div>
    </form>
</div>
@endsection