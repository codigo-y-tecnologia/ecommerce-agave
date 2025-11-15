@extends('admin.productos.administrar-productos')

@section('title', 'Gestión de Atributos')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-list-alt me-2"></i>Lista de Atributos
                    </h3>
                    <a href="{{ route('atributos.create') }}" class="btn btn-success">
                        <i class="fas fa-plus me-1"></i> Nuevo Atributo
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($atributos->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th width="50">#</th>
                                    <th>Nombre</th>
                                    <th width="120">Tipo</th>
                                    <th>Label</th>
                                    <th width="100">Requerido</th>
                                    <th width="100">Opciones</th>
                                    <th width="100">Estado</th>
                                    <th width="80">Orden</th>
                                    <th width="180" class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($atributos as $atributo)
                                    <tr>
                                        <td>{{ $atributo->id_atributo }}</td>
                                        <td>
                                            <strong>{{ $atributo->vNombre }}</strong>
                                            @if($atributo->tDescripcion)
                                                <br><small class="text-muted">{{ Str::limit($atributo->tDescripcion, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $badgeClass = [
                                                    'texto' => 'primary',
                                                    'textarea' => 'info',
                                                    'select' => 'success',
                                                    'radio' => 'warning',
                                                    'checkbox' => 'secondary',
                                                    'archivo' => 'dark'
                                                ][$atributo->eTipo] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $badgeClass }}">
                                                {{ $atributo->eTipo }}
                                            </span>
                                        </td>
                                        <td>{{ $atributo->vLabel ?: 'N/A' }}</td>
                                        <td class="text-center">
                                            @if($atributo->bRequerido)
                                                <span class="badge bg-warning">Sí</span>
                                            @else
                                                <span class="badge bg-secondary">No</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if(in_array($atributo->eTipo, ['select', 'radio', 'checkbox']))
                                                <span class="badge bg-primary">{{ $atributo->opciones->count() }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($atributo->bActivo)
                                                <span class="badge bg-success">Activo</span>
                                            @else
                                                <span class="badge bg-danger">Inactivo</span>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $atributo->iOrden }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('atributos.show', $atributo) }}" 
                                                   class="btn btn-info" title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('atributos.edit', $atributo) }}" 
                                                   class="btn btn-warning" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('atributos.destroy', $atributo) }}" 
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('¿Estás seguro de eliminar este atributo? Esta acción no se puede deshacer.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger" title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-list-alt fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">No hay atributos registrados</h4>
                        <p class="text-muted">Comienza creando tu primer atributo para los productos</p>
                        <a href="{{ route('atributos.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> Crear Primer Atributo
                        </a>
                    </div>
                    @endif
                </div>
                @if($atributos->count() > 0)
                <div class="card-footer">
                    <small class="text-muted">
                        Total de atributos: <strong>{{ $atributos->count() }}</strong> | 
                        Activos: <strong>{{ $atributos->where('bActivo', true)->count() }}</strong> | 
                        Con opciones: <strong>{{ $atributos->whereIn('eTipo', ['select', 'radio', 'checkbox'])->count() }}</strong>
                    </small>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Auto-dismiss alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    });
</script>
@endsection