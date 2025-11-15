@extends('admin.productos.administrar-productos')

@section('title', 'Detalles del Atributo')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-eye me-2"></i>Detalles del Atributo
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Información Básica</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">ID:</th>
                                    <td>{{ $atributo->id_atributo }}</td>
                                </tr>
                                <tr>
                                    <th>Nombre:</th>
                                    <td><strong>{{ $atributo->vNombre }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Tipo:</th>
                                    <td>{{ $atributo->eTipo }}</td>
                                </tr>
                                <tr>
                                    <th>Label:</th>
                                    <td>{{ $atributo->vLabel ?: 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Placeholder:</th>
                                    <td>{{ $atributo->vPlaceholder ?: 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Configuración</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Requerido:</th>
                                    <td>
                                        @if($atributo->bRequerido)
                                            Sí
                                        @else
                                            No
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Estado:</th>
                                    <td>
                                        @if($atributo->bActivo)
                                            Activo
                                        @else
                                            Inactivo
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Orden:</th>
                                    <td>{{ $atributo->iOrden }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($atributo->tDescripcion)
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h5>Descripción</h5>
                            <div class="card">
                                <div class="card-body">
                                    {{ $atributo->tDescripcion }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(in_array($atributo->eTipo, ['select', 'radio', 'checkbox']) && $atributo->opciones->count() > 0)
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5>Opciones Configuradas</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th width="50">#</th>
                                            <th>Valor</th>
                                            <th>Etiqueta</th>
                                            <th width="120">Predeterminado</th>
                                            <th width="80">Orden</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($atributo->opciones as $opcion)
                                            <tr>
                                                <td>{{ $opcion->id_opcion }}</td>
                                                <td><code>{{ $opcion->vValor }}</code></td>
                                                <td>{{ $opcion->vEtiqueta }}</td>
                                                <td class="text-center">
                                                    @if($opcion->bPredeterminado)
                                                        Sí
                                                    @else
                                                        No
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ $opcion->iOrden }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('atributos.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Volver a la lista
                        </a>
                        <div>
                            <a href="{{ route('atributos.edit', $atributo) }}" class="btn btn-warning">
                                <i class="fas fa-edit me-1"></i> Editar
                            </a>
                            <form action="{{ route('atributos.destroy', $atributo) }}" method="POST" class="d-inline" 
                                  onsubmit="return confirm('¿Estás seguro de eliminar este atributo?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash me-1"></i> Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection