@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="mb-4">
                <h1>Lista de Ventas</h1>
            </div>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <div class="card">
                <div class="card-body">
                    @if($ventas && $ventas->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Pedido</th>
                                    <th>Usuario</th>
                                    <th>Fecha</th>
                                    <th>Total</th>
                                    <th>Método Pago</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ventas as $venta)
                                <tr>
                                    <td>{{ $venta->id_venta }}</td>
                                    <td>{{ $venta->id_pedido }}</td>
                                    <td>{{ $venta->id_usuario }}</td>
                                    <td>
                                        @if($venta->tFecha_venta instanceof \DateTime)
                                            {{ $venta->tFecha_venta->format('d/m/Y H:i') }}
                                        @else
                                            {{ \Carbon\Carbon::parse($venta->tFecha_venta)->format('d/m/Y H:i') }}
                                        @endif
                                    </td>
                                    <td>${{ number_format($venta->dTotal, 2) }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $venta->eMetodo_pago }}</span>
                                    </td>
                                    <td>
                                        @php
                                        $badgeClass = [
                                            'completada' => 'bg-success',
                                            'devuelta' => 'bg-warning',
                                            'reembolsada' => 'bg-info',
                                            'cancelada' => 'bg-danger'
                                        ][$venta->eEstado] ?? 'bg-secondary';
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ $venta->eEstado }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('ventas.show', $venta->id_venta) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> Ver
                                            </a>
                                            <a href="{{ route('ventas.edit', $venta->id_venta) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i> Editar
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No hay ventas registradas</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection