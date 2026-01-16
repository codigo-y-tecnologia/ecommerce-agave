@extends('layouts.admins')

@section('content')

@include('superadmin.partials.alerts')

<h3>Permisos del rol: {{ $role->name }}</h3>

<form method="POST"
      action="{{ route('roles.permissions.update', $role) }}">
@csrf

@foreach($permissions as $permission)
<div class="form-check">
    <input class="form-check-input"
           type="checkbox"
           name="permissions[]"
           value="{{ $permission->name }}"
           {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>

    <label class="form-check-label">
        {{ $permission->name }}
    </label>
</div>
@endforeach

<button class="btn btn-success mt-3">Guardar cambios</button>
</form>
@endsection
