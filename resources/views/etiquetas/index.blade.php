@extends('layouts.app')

@section('title', 'Etiquetas de Productos')
@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Etiquetas</h2>
                <a href="{{ route('etiquetas.create') }}" class="btn btn-primary">+ Nueva</a>
            </div>
            
            <!-- Formulario de búsqueda -->
            <form method="GET" action="{{ route('etiquetas.index') }}" class="mt-3" id="searchForm">
                <div class="row">
                    <div class="col-md-8">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Buscar por ID, nombre o descripción..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Buscar</button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('etiquetas.index') }}" class="btn btn-secondary w-100">Limpiar</a>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body">
            @if(request('search') && request('search') != '')
                <div class="alert alert-info mb-3">
                    Resultados para: "{{ request('search') }}"
                </div>
            @endif

            @if($etiquetas->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Productos</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($etiquetas as $etiqueta)
                        <tr>
                            <td>#{{ $etiqueta->id_etiqueta }}</td>
                            <td>
                                <strong>{{ $etiqueta->vNombre }}</strong>
                            </td>
                            <td>
                                @if($etiqueta->tDescripcion)
                                    {{ Str::limit($etiqueta->tDescripcion, 50) }}
                                @else
                                    <span class="text-muted">Sin descripción</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $etiqueta->productos->count() }} productos</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('etiquetas.edit', $etiqueta) }}" class="btn btn-warning">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <button type="button" class="btn btn-danger" 
                                            onclick="confirmarEliminacion({{ $etiqueta->id_etiqueta }}, '{{ addslashes($etiqueta->vNombre) }}')">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                    <form id="delete-form-{{ $etiqueta->id_etiqueta }}" 
                                          action="{{ route('etiquetas.destroy', $etiqueta) }}" 
                                          method="POST" class="d-none">
                                        @csrf @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3 p-3 bg-light rounded">
                <strong>Total:</strong> {{ $etiquetas->count() }} etiquetas
            </div>
            
            @else
            <div class="text-center py-5">
                @if(request('search'))
                    <h4 class="text-muted">No se encontraron resultados</h4>
                    <p class="text-muted">No hay etiquetas que coincidan con "{{ request('search') }}"</p>
                    <a href="{{ route('etiquetas.index') }}" class="btn btn-primary">
                        Ver todas las etiquetas
                    </a>
                @else
                    <h4 class="text-muted">No hay etiquetas registradas</h4>
                    <p class="text-muted">Comienza agregando etiquetas para organizar tus productos</p>
                    <a href="{{ route('etiquetas.create') }}" class="btn btn-primary">
                        + Crear Primera Etiqueta
                    </a>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function confirmarEliminacion(id, nombre) {
    Swal.fire({
        title: "¿Estás seguro?",
        text: "¡La etiqueta \"" + nombre + "\" será eliminada permanentemente!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loader
            Swal.fire({
                title: "Eliminando...",
                text: "Por favor espera",
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Enviar el formulario de eliminación
            setTimeout(() => {
                document.getElementById('delete-form-' + id).submit();
            }, 500);
        }
    });
}

// Mostrar mensaje de éxito después de operaciones
@if(session('success'))
document.addEventListener('DOMContentLoaded', function() {
    const successMessage = "{{ session('success') }}";
    
    if (successMessage.includes('eliminada')) {
        Swal.fire({
            title: "¡Eliminado!",
            text: successMessage,
            icon: "success",
            timer: 3000,
            showConfirmButton: false
        });
    } else if (successMessage.includes('creada')) {
        Swal.fire({
            title: "¡Éxito!",
            text: successMessage,
            icon: "success",
            draggable: true,
            timer: 3000,
            timerProgressBar: true
        });
    } else if (successMessage.includes('actualizada')) {
        Swal.fire({
            title: "¡Actualizado!",
            text: successMessage,
            icon: "success",
            timer: 3000,
            showConfirmButton: false
        });
    } else {
        Swal.fire({
            title: "¡Éxito!",
            text: successMessage,
            icon: "success",
            timer: 3000
        });
    }
});
@endif

// Mostrar mensaje de error
@if(session('error'))
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: "error",
        title: "Oops...",
        text: "{{ session('error') }}",
        footer: 'Por favor, verifica la información'
    });
});
@endif
</script>

@push('styles')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@push('scripts')
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush
@endsection
