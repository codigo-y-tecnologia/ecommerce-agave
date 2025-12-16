@extends('layout.app')

@section('title', 'Lista de Detalles de Venta')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-list"></i> Detalles de Venta
                    </h4>
                    <a href="{{ route('detalle_venta.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nuevo Detalle
                    </a>
                </div>
                
                <div class="card-body">
                    @if($detallesVenta->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">No hay detalles de venta registrados</h5>
                            <p class="text-muted">Comienza agregando un nuevo detalle de venta.</p>
                            <a href="{{ route('detalle_venta.create') }}" class="btn btn-primary mt-2">
                                <i class="fas fa-plus"></i> Crear Primer Detalle
                            </a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th width="80">ID</th>
                                        <th>Venta ID</th>
                                        <th>Producto ID</th>
                                        <th>Cantidad</th>
                                        <th>Precio Unitario</th>
                                        <th>Subtotal</th>
                                        <th width="150" class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($detallesVenta as $detalle)
                                    <tr>
                                        <td><strong>{{ $detalle->id_detalle_venta }}</strong></td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                #{{ $detalle->id_venta }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                #{{ $detalle->id_producto }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary rounded-pill">
                                                {{ $detalle->iCantidad }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-success fw-bold">
                                                ${{ number_format($detalle->dPrecio_unitario, 2) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-success fw-bold">
                                                ${{ number_format($detalle->dSubtotal, 2) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('detalle_venta.show', $detalle->id_detalle_venta) }}" 
                                                   class="btn btn-info" title="Ver">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('detalle_venta.edit', $detalle->id_detalle_venta) }}" 
                                                   class="btn btn-warning" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('detalle_venta.destroy', $detalle->id_detalle_venta) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger" 
                                                            title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-light">
                                        <td colspan="5" class="text-end fw-bold">TOTAL:</td>
                                        <td class="fw-bold text-success">
                                            ${{ number_format($detallesVenta->sum('dSubtotal'), 2) }}
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        
                        <div class="mt-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="text-muted">
                                        Mostrando <strong>{{ $detallesVenta->count() }}</strong> registros
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection