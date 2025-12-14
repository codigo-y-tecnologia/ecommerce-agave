@extends('layouts.admins')

@section('title', 'Panel Superadmin')

@section('content')
<div class="text-center mb-5">
    <h2>👑 Bienvenido, {{ Auth::user()->vNombre }}</h2>
    <p class="text-muted">Tienes control total del sistema. Administra configuraciones avanzadas, usuarios y seguridad.</p>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card border-danger shadow-sm">
            <div class="card-body text-center">
                <h5>⚙️ Configuración Global</h5>
                <p>Gestiona parámetros críticos del sistema.</p>
                <a href="#" class="btn btn-danger w-100">Configurar sistema</a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-primary shadow-sm">
            <div class="card-body text-center">
                <h5>🧑‍💼 Administradores</h5>
                <p>Agrega o elimina administradores y asigna permisos.</p>
                <a href="{{ route('superadmin.admins.index') }}" class="btn btn-primary w-100">Gestionar administradores</a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-warning shadow-sm">
            <div class="card-body text-center">
                <h5>🛡️ Seguridad</h5>
                <p>Revisa los logs de seguridad y actividad sospechosa.</p>
                <a href="#" class="btn btn-warning w-100">Ver logs de seguridad</a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-info shadow-sm">
            <div class="card-body text-center">
                <h5>🧾 Permisos y Roles</h5>
                <p>Gestiona permisos avanzados y asignaciones de roles.</p>
                <a href="#" class="btn btn-info w-100">Gestionar permisos</a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-success shadow-sm">
            <div class="card-body text-center">
                <h5>📈 Monitoreo</h5>
                <p>Observa el rendimiento y estado del servidor.</p>
                <a href="#" class="btn btn-success w-100">Ver monitoreo</a>
            </div>
        </div>
    </div>
</div>
@endsection
