@extends('layouts.admins')

@section('content')
<div class="container">
    <h1 class="mb-3">
        @isset($cupon)
            Editar Cupón Usado #{{ $cupon->id_cupon }}
        @else
            Registrar Cupón Usado
        @endisset
    </h1>

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

    <form action="{{ isset($cupon) ? route('cupones_usados.update', $cupon->id_cupon) : route('cupones_usados.store') }}"
          method="POST" class="card card-body">
        @csrf
        @isset($cupon)
            @method('PUT')
        @endisset

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">ID Venta *</label>
                    <select name="id_venta" class="form-control" required>
                        <option value="">Seleccione una venta</option>
                        @foreach($ventas as $venta)
                            <option value="{{ $venta->id_venta }}"
                                @selected(old('id_venta', $cupon->id_venta ?? '') == $venta->id_venta)>
                                Venta #{{ $venta->id_venta }}
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
                                @selected(old('id_usuario', $cupon->id_usuario ?? '') == $usuario->id_usuario)>
                                #{{ $usuario->id_usuario }} — {{ $usuario->vNombre }} {{ $usuario->vApaterno }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Guest Token</label>
                    <input type="text"
                           name="guest_token"
                           class="form-control font-monospace"
                           maxlength="36"
                           placeholder="UUID del invitado (opcional)"
                           value="{{ old('guest_token', $cupon->guest_token ?? '') }}">
                    <small class="text-muted">Solo si el cupón fue usado por un invitado.</small>
                </div>
            </div>
        </div>

        <div class="alert alert-info">
            <strong>Nota:</strong> La fecha de uso se registrará automáticamente al guardar.
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('cupones_usados.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">
                @isset($cupon) Actualizar Cupón @else Registrar Cupón @endisset
            </button>
        </div>
    </form>
</div>
@endsection