@extends('layouts.app')

@section('title', 'Marcas de Agave')
@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Marcas</h2>
                <a href="{{ route('marcas.create') }}" class="btn btn-primary">+ Nueva</a>
            </div>
            
            <!-- Formulario de búsqueda -->
            <form method="GET" action="{{ route('marcas.index') }}" class="mt-3">
                <div class="row">
                    <div class="col-md-8">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Buscar por ID o nombre de marca..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Buscar</button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('marcas.index') }}" class="btn btn-secondary w-100">Limpiar</a>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if(request('search') && request('search') != '')
                <div class="alert alert-info mb-3">
                    Resultados para: "{{ request('search') }}"
                </div>
            @endif

            @if($marcas->count() > 0)
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
                        @foreach($marcas as $marca)
                        <tr>
                            <td>#{{ $marca->id_marca }}</td>
                            <td>
                                <strong>{{ $marca->vNombre }}</strong>
                            </td>
                            <td>
                                @if($marca->tDescripcion)
                                    {{ Str::limit($marca->tDescripcion, 50) }}
                                @else
                                    <span class="text-muted">Sin descripción</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $marca->productos->count() }} productos</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('marcas.edit', $marca) }}" class="btn btn-warning">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <button type="button" class="btn btn-danger" 
                                            onclick="confirmarEliminacion({{ $marca->id_marca }}, '{{ addslashes($marca->vNombre) }}', {{ $marca->productos->count() }})">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                    <form id="delete-form-{{ $marca->id_marca }}" 
                                          action="{{ route('marcas.destroy', $marca) }}" 
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
            
            <!-- Paginación -->
            @if($marcas->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $marcas->appends(request()->query())->links() }}
            </div>
            @endif
            
            <div class="mt-3 p-3 bg-light rounded">
                <strong>Total:</strong> {{ $marcas->total() }} marcas
                @if(request('search'))
                    <span class="ms-3">(filtradas)</span>
                @endif
            </div>
            
            @else
            <div class="text-center py-5">
                @if(request('search'))
                    <h4 class="text-muted">No se encontraron resultados</h4>
                    <p class="text-muted">No hay marcas que coincidan con "{{ request('search') }}"</p>
                    <a href="{{ route('marcas.index') }}" class="btn btn-primary">
                        Ver todas las marcas
                    </a>
                @else
                    <h4 class="text-muted">No hay marcas registradas</h4>
                    <p class="text-muted">Comienza agregando marcas de bebidas de agave</p>
                    <a href="{{ route('marcas.create') }}" class="btn btn-primary">
                        + Crear Primera Marca
                    </a>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@push('scripts')
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function confirmarEliminacion(id, nombre, productosCount) {
    let mensaje = "¿Estás seguro de que deseas eliminar la marca \"" + nombre + "\"?";
    
    if (productosCount > 0) {
        mensaje = "¡ADVERTENCIA!\n\nLa marca \"" + nombre + "\" tiene " + productosCount + " producto(s) asociado(s).\n\n" +
                  "Si eliminas esta marca, los productos quedarán sin marca asignada.\n\n" +
                  "¿Estás seguro de que deseas continuar?";
    }
    
    Swal.fire({
        title: "¿Estás seguro?",
        text: mensaje,
        icon: productosCount > 0 ? "warning" : "question",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            // Enviar el formulario de eliminación
            document.getElementById('delete-form-' + id).submit();
        }
    });
}

// Mostrar mensaje de éxito después de eliminar
@if(session('success'))
Swal.fire({
    title: "¡Éxito!",
    text: "{{ session('success') }}",
    icon: "success",
    timer: 3000,
    showConfirmButton: false
});
@endif

// Mostrar mensaje de error si hubo un problema
@if(session('error'))
Swal.fire({
    title: "¡Error!",
    text: "{{ session('error') }}",
    icon: "error",
    timer: 3000,
    showConfirmButton: false
});
@endif
</script>
@endpush
