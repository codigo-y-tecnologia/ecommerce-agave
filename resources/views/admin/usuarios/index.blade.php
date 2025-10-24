@extends('layouts.app')

@section('title', 'Clientes Registrados')

@section('content')
<div class="container py-4">
    <h2 class="mb-4 text-center">👥 Clientes Registrados</h2>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {!! session('success') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- 🔍 Buscador dinámico --}}
    <div class="input-group mb-4">
        <input type="text" id="search" class="form-control" placeholder="Buscar cliente por nombre, apellido o correo...">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
    </div>

    {{-- 📋 Contenedor dinámico de tabla --}}
    <div id="table-container">
        @include('admin.usuarios.partials.table', ['usuarios' => $usuarios])
    </div>
</div>

{{--Script AJAX (búsqueda + paginación dinámica) --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.querySelector('#search');
    const tableContainer = document.querySelector('#table-container');

    let timeout = null;

    // 🔎 Búsqueda dinámica
    searchInput.addEventListener('keyup', function() {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            fetchData(this.value);
        }, 400);
    });

    // 🔁 Delegación para la paginación dinámica
    document.addEventListener('click', function(e) {
        if (e.target.matches('.pagination a')) {
            e.preventDefault();
            const url = e.target.getAttribute('href');
            const query = searchInput.value;
            fetchData(query, url);
        }
    });

    function fetchData(query = '', url = "{{ route('admin.usuarios') }}") {
        const fetchUrl = url + (url.includes('?') ? '&' : '?') + 'q=' + encodeURIComponent(query);

        fetch(fetchUrl, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            tableContainer.innerHTML = data.html;
        })
        .catch(err => console.error('Error al cargar datos:', err));
    }
});
</script>
@endsection
