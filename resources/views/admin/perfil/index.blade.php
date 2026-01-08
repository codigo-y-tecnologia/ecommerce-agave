@extends('layouts.admins')

@section('content')
<div class="container">
    <h2 class="mb-4">Mi Perfil (Admin)</h2>

    @include('admin.perfil.partials.datos')
    <hr>
    @include('admin.perfil.partials.password')
    <hr>
    @include('admin.perfil.partials.seguridad')
</div>
@endsection
