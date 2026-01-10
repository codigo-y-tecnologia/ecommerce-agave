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

<h2>🧾 Permisos</h2>

<a href="{{ route('permissions.create') }}"
   class="btn btn-primary mb-3">
    Nuevo permiso
</a>

<ul class="list-group">
@foreach($permissions as $permission)
    <li class="list-group-item">{{ $permission->name }}</li>
@endforeach
</ul>
@endsection
