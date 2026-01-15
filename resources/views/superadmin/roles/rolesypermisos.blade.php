@extends('layouts.app')

@section('title', 'Roles y Permisos')

@section('content')
<div class="container mt-5">
    <h2 class="fw-bold mb-4 text-center">🧾 Roles y Permisos</h2>

    <div class="list-group shadow-sm">
        @can('gestionar_permisos')
            <a href="{{ route('roles.index') }}" class="list-group-item list-group-item-action">
            🧾 Roles del sistema
        </a>
        @endcan
        @can('gestionar_permisos')
            <a href="{{ route('permissions.index') }}" class="list-group-item list-group-item-action">
                🧾 Permisos del sistema
            </a>
        @endcan
        @can('gestionar_roles')
            <a href="{{ route('usuarios.index') }}"
               class="list-group-item list-group-item-action">
                👤 Asignar roles a usuarios
            </a>
        @endcan
    </div>
</div>
@endsection
