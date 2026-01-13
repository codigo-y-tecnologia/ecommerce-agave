@extends('layouts.admins')

@section('content')
<h2>Editar Rol: {{ $role->name }}</h2>

<form method="POST" action="{{ route('roles.update', $role) }}">
@csrf
@method('PUT')

<input class="form-control mb-3" name="name" value="{{ $role->name }}">

@foreach($permissions as $permission)
<div class="form-check">
    <input type="checkbox"
           name="permissions[]"
           value="{{ $permission->name }}"
           {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
    {{ $permission->name }}
</div>
@endforeach

<button class="btn btn-success mt-3">Actualizar</button>
</form>
@endsection
