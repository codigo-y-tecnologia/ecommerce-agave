@extends('layouts.admins')

@section('content')
<div class="container">
    <h1 class="text-2xl font-bold text-red-700">
    Perfil del Superadministrador
</h1>

    @include('superadmin.partials.alerts')
    <hr>
    @include('superadmin.partials.datos')
    <hr>
    @include('superadmin.partials.password')
    <hr>
    @include('superadmin.partials.seguridad')
</div>
@endsection



