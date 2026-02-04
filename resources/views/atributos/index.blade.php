@extends('layouts.app')

@section('title', 'Atributos')
@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Atributos</h2>
                <a href="{{ route('atributos.create') }}" class="btn btn-primary">+ Nuevo Atributo</a>
            </div>
            
            <!-- Formulario de búsqueda -->
            <form method="GET" action="{{ route('atributos.index') }}" class="mt-3">
                <div class="row">
                    <div class="col-md-8">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Buscar por ID o nombre..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Buscar</button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('atributos.index') }}" class="btn btn-secondary w-100">Limpiar</a>
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

            @if($atributos->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Slug</th>
                            <th>Valores</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($atributos as $atributo)
                        <tr>
                            <td>#{{ $atributo->id_atributo }}</td>
                            <td>
                                <div>
                                    <strong>{{ $atributo->vNombre }}</strong>
                                    @if($atributo->tDescripcion)
                                    <div class="text-muted" style="font-size: 0.85rem; margin-top: 2px;">
                                        {{ Str::limit($atributo->tDescripcion, 50) }}
                                    </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <code class="text-primary">{{ $atributo->vSlug }}</code>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $atributo->valores_count }} valores</span>
                                <a href="{{ route('atributos.valores', $atributo) }}" class="btn btn-sm btn-outline-primary ms-1">
                                    Gestionar
                                </a>
                            </td>
                            <td>
                                @if($atributo->bActivo)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-secondary">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('atributos.show', $atributo) }}" class="btn btn-info">
                                        Ver
                                    </a>
                                    <a href="{{ route('atributos.edit', $atributo) }}" class="btn btn-warning">
                                        Editar
                                    </a>
                                    <button type="button" class="btn btn-danger btn-eliminar" 
                                            data-id="{{ $atributo->id_atributo }}"
                                            data-nombre="{{ $atributo->vNombre }}">
                                        Eliminar
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3 p-3 bg-light rounded">
                <strong>Total:</strong> {{ $atributos->count() }} atributos
            </div>
            
            @else
            <div class="text-center py-5">
                @if(request('search'))
                    <h4 class="text-muted">No se encontraron resultados</h4>
                    <p class="text-muted">No hay atributos que coincidan con "{{ request('search') }}"</p>
                    <a href="{{ route('atributos.index') }}" class="btn btn-primary">
                        Ver todos los atributos
                    </a>
                @else
                    <h4 class="text-muted">No hay atributos registrados</h4>
                    <p class="text-muted">Comienza agregando tu primer atributo</p>
                    <a href="{{ route('atributos.create') }}" class="btn btn-primary">
                        + Crear Primer Atributo
                    </a>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Formularios ocultos para eliminación -->
@foreach($atributos as $atributo)
<form id="delete-form-{{ $atributo->id_atributo }}" 
      action="{{ route('atributos.destroy', $atributo) }}" 
      method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endforeach
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Confirmación de eliminación con SweetAlert2
    document.querySelectorAll('.btn-eliminar').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nombre = this.getAttribute('data-nombre');
            
            Swal.fire({
                title: "¿Estás seguro?",
                html: `Vas a eliminar el atributo: <strong>"${nombre}"</strong><br>
                       <span class="text-danger">¡No podrás revertir esta acción! Todos los valores asociados también serán eliminados.</span>`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Enviar formulario
                    document.getElementById(`delete-form-${id}`).submit();
                }
            });
        });
    });
});
</script>
@endsection