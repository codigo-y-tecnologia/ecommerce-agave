@extends('admin.productos.administrar-productos')

@section('title', 'Atributos')
@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0"><i class="fas fa-cubes me-2"></i>Atributos</h2>
                <a href="{{ route('atributos.create') }}" class="btn btn-light">
                    <i class="fas fa-plus-circle me-1"></i> Nuevo Atributo
                </a>
            </div>
            
            <!-- Formulario de búsqueda -->
            <form method="GET" action="{{ route('atributos.index') }}" class="mt-3">
                <div class="row">
                    <div class="col-md-8">
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-search text-success"></i>
                            </span>
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Buscar por ID o nombre..." 
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-light w-100">
                            <i class="fas fa-search me-1"></i> Buscar
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('atributos.index') }}" class="btn btn-outline-light w-100">
                            <i class="fas fa-times me-1"></i> Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body">
            @if(session('success'))
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: '{{ session('success') }}',
                        confirmButtonColor: '#2E8B57'
                    });
                </script>
            @endif

            @if(session('error'))
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: '{{ session('error') }}',
                        confirmButtonColor: '#2E8B57'
                    });
                </script>
            @endif

            @if(request('search') && request('search') != '')
                <div class="alert alert-info mb-3">
                    <i class="fas fa-info-circle me-1"></i>
                    Resultados para: <strong>"{{ request('search') }}"</strong>
                </div>
            @endif

            @if($atributos->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
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
                            <td><span class="badge bg-dark">#{{ $atributo->id_atributo }}</span></td>
                            <td>
                                <div>
                                    <strong class="text-success">{{ $atributo->vNombre }}</strong>
                                    @if($atributo->tDescripcion)
                                    <div class="text-muted small mt-1">
                                        <i class="fas fa-align-left me-1"></i>
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
                                    <a href="{{ route('atributos.show', $atributo) }}" class="btn btn-info text-white" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('atributos.edit', $atributo) }}" class="btn btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('atributos.valores', $atributo) }}" class="btn btn-success" title="Gestionar valores">
                                        <i class="fas fa-list"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-eliminar" 
                                            data-id="{{ $atributo->id_atributo }}"
                                            data-nombre="{{ $atributo->vNombre }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3 p-3 bg-light rounded">
                <strong>Total:</strong> {{ $atributos->count() }} atributos encontrados
            </div>
            
            @else
            <div class="text-center py-5">
                <i class="fas fa-cubes fa-4x text-muted mb-3"></i>
                @if(request('search'))
                    <h4 class="text-muted">No se encontraron resultados</h4>
                    <p class="text-muted">No hay atributos que coincidan con "{{ request('search') }}"</p>
                    <a href="{{ route('atributos.index') }}" class="btn btn-success">
                        <i class="fas fa-times me-1"></i> Limpiar búsqueda
                    </a>
                @else
                    <h4 class="text-muted">No hay atributos registrados</h4>
                    <p class="text-muted">Comienza agregando tu primer atributo</p>
                    <a href="{{ route('atributos.create') }}" class="btn btn-success">
                        <i class="fas fa-plus-circle me-1"></i> Crear Primer Atributo
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: "Eliminando...",
                        text: "Por favor espera",
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    document.getElementById(`delete-form-${id}`).submit();
                }
            });
        });
    });
});
</script>
@endpush