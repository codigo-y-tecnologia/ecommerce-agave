@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4>Lista de Reembolsos</h4>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID Reembolso</th>
                                    <th>ID Venta</th>
                                    <th>Fecha Reembolso</th>
                                    <th>Monto</th>
                                    <th>Motivo</th>
                                    <th>Método Pago</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($reembolsos as $reembolso)
                                    <tr>
                                        <td>{{ $reembolso->id_reembolso }}</td>
                                        <td>{{ $reembolso->id_venta }}</td>
                                        <td>
                                            @if($reembolso->tFecha_reembolso)
                                                {{ $reembolso->tFecha_reembolso->format('d/m/Y H:i') }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>

                                        <td>${{ number_format($reembolso->dMonto, 2) }}</td>
                                        <td>{{ $reembolso->vMotivo }}</td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $reembolso->eMetodo_pago }}</span>
                                        </td>
                                        <td>
                                            <span class="badge 
                                                @if($reembolso->eEstado == 'procesado') bg-success
                                                @elseif($reembolso->eEstado == 'pendiente') bg-warning
                                                @elseif($reembolso->eEstado == 'fallido') bg-danger
                                                @else bg-secondary @endif">
                                                {{ $reembolso->eEstado }}
                                            </span>
                                        </td>

                                        <td>
                                            <div class="btn-group" role="group">

                                                {{-- BOTÓN EDITAR CORREGIDO --}}
                                                <a href="{{ route('reembolsos.edit', $reembolso->id_reembolso) }}" 
                                                   class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i> Editar
                                                </a>

                                                {{-- BOTÓN ELIMINAR CORREGIDO --}}
                                                <form action="{{ route('reembolsos.destroy', $reembolso->id_reembolso) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                            onclick="return confirm('¿Estás seguro de eliminar este reembolso?')">
                                                        <i class="fas fa-trash"></i> Eliminar
                                                    </button>
                                                </form>

                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No hay reembolsos registrados</td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
