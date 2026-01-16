@extends('layouts.app')

@section('title', 'Gestionar Administradores')

@section('content')
<div class="container py-4">
    <h2 class="mb-4 text-center">🧑‍💼 Administradores</h2>

    @include('superadmin.partials.alerts')

    {{-- Buscador --}}
    <div class="input-group mb-3">
        <input type="text" id="search" class="form-control" placeholder="Buscar administrador...">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
    </div>

    <div id="table-container">
        @include('superadmin.partials.table', ['usuarios' => $usuarios])
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="{{ route('superadmin.admins.create') }}" class="btn btn-success">
        + Crear administrador
    </a>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.querySelector('#search');
    const tableContainer = document.querySelector('#table-container');
    let timeout;

    searchInput.addEventListener('keyup', function() {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            const query = this.value;
            fetch(`{{ route('superadmin.admins.index') }}?q=${encodeURIComponent(query)}`, {
                headers: {'X-Requested-With': 'XMLHttpRequest'}
            })
            .then(res => res.json())
            .then(data => tableContainer.innerHTML = data.html)
            .catch(console.error);
        }, 300);
    });
});
</script>
@endsection
