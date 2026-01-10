@extends('layouts.admins')

@section('content')

@if (session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

@if (session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif

<h2>🧾 Roles del sistema</h2>

<table class="table table-bordered">
    <tr>
        <th>Rol</th>
        <th>Acciones</th>
    </tr>

    @foreach($roles as $role)
    <tr>
        <td>{{ $role->name }}</td>
        <td>
            <a href="{{ route('roles.permissions.edit', $role) }}"
               class="btn btn-info btn-sm">
                Gestionar permisos
            </a>
        </td>
    </tr>
    @endforeach
</table>
@endsection
