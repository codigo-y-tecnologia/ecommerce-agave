@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-3">Impuestos</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="mb-3">
        <a href="{{ route('impuestos.create') }}" class="btn btn-primary">+ Nuevo impuesto</a>
    </div>

    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>porcentaje (%)</th>
                    <th>Activo</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($impuestos as $imp)
                    <tr>
                        <td>{{ $imp->id_impuesto }}</td>
                        <td>{{ $imp->vNombre }}</td>
                        <td>{{ $imp->eTipo }}</td>
                        <td>{{ number_format($imp->dPorcentaje, 2) }}</td>
                        <td>
                            @if($imp->bActivo) 
                                <span class="badge bg-success">Sí</span>
                            @else
                                <span class="badge bg-secondary">No</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('impuestos.edit', $imp->id_impuesto) }}" class="btn btn-sm btn-warning">Editar</a>
                            <form action="{{ route('impuestos.destroy', $imp->id_impuesto) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('¿Eliminar este impuesto?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">No hay registros.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
