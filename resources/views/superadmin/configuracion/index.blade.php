@extends('layouts.admins')

@section('title','Configuración Global')

@section('content')

<div class="container">

<h2 class="mb-4">⚙️ Configuración Global</h2>

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<form method="POST" action="{{ route('superadmin.configuracion.update') }}">
@csrf

<div class="row g-4">

{{-- CONFIG TIENDA --}}
<div class="col-md-6">
<div class="card shadow-sm">
<div class="card-header bg-dark text-white">
Configuración de tienda
</div>

<div class="card-body">

<div class="mb-3">
<label>Nombre de tienda</label>
<input type="text" name="nombre_tienda" class="form-control @error('nombre_tienda') is-invalid @enderror"
value="{{ old('nombre_tienda', $configs['nombre_tienda'] ?? '') }}" required>

@error('nombre_tienda')
<div class="invalid-feedback">
    {{ $message }}
</div>
@enderror
</div>

<div class="mb-3">
<label>Email soporte</label>
<input type="email" name="email_soporte" class="form-control @error('email_soporte') is-invalid @enderror"
value="{{ old('email_soporte', $configs['email_soporte'] ?? '') }}" required>

@error('email_soporte')
<div class="invalid-feedback">
    {{ $message }}
</div>
@enderror
</div>

<div class="mb-3">
<label>Teléfono</label>
<input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror"
value="{{ old('telefono', $configs['telefono'] ?? '') }}" required>

@error('telefono')
<div class="invalid-feedback">
    {{ $message }}
</div>
@enderror
</div>

<div class="mb-3">
<label>Moneda</label>
<input type="text" name="moneda" class="form-control @error('moneda') is-invalid @enderror"
value="{{ old('moneda', $configs['moneda'] ?? 'MXN') }}" required>

@error('moneda')
<div class="invalid-feedback">
    {{ $message }}
</div>
@enderror
</div>

</div>
</div>
</div>

{{-- CONFIG ENVÍOS --}}
<div class="col-md-6">
<div class="card shadow-sm">
<div class="card-header bg-primary text-white">
Configuración de envíos
</div>

<div class="card-body">

<div class="mb-3">
<label>Costo envío estándar</label>
<input type="number" name="envio_estandar" class="form-control"
value="{{ $configs['envio_estandar'] ?? '' }}">
</div>

<div class="mb-3">
<label>Envío gratis desde</label>
<input type="number" name="envio_gratis" class="form-control"
value="{{ $configs['envio_gratis'] ?? '' }}">
</div>

</div>
</div>
</div>

{{-- CONFIG IMPUESTOS --}}
<div class="col-md-6">
<div class="card shadow-sm">
<div class="card-header bg-warning">
Impuestos
</div>

<div class="card-body">

<div class="mb-3">
<label>IVA (%)</label>
<input type="number" name="iva" class="form-control"
value="{{ $configs['iva'] ?? 16 }}">
</div>

</div>
</div>
</div>

{{-- CONFIG SISTEMA --}}
<div class="col-md-6">
<div class="card shadow-sm">
<div class="card-header bg-danger text-white">
Sistema
</div>

<div class="card-body">

<div class="mb-3">
<label>Modo mantenimiento</label>

<select name="modo_mantenimiento" class="form-control">
<option value="0" {{ ($configs['modo_mantenimiento'] ?? 0) == 0 ? 'selected' : '' }}>
Desactivado
</option>

<option value="1" {{ ($configs['modo_mantenimiento'] ?? 0) == 1 ? 'selected' : '' }}>
Activado
</option>

</select>

</div>

</div>
</div>
</div>

</div>

<div class="mt-4">
<button class="btn btn-success">
Guardar configuración
</button>
</div>

</form>

</div>

@endsection