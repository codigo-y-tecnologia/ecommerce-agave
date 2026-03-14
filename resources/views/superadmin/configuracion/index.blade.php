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