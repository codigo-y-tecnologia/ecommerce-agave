@extends('layouts.admins')

@section('title', 'Listado de Cupones')

@section('content')
<div class="container my-5">

    {{-- Encabezado --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">🎟️ Listado de Cupones</h1>
        <a href="{{ route('cupones.create') }}" class="btn btn-primary">
            ➕ Crear nuevo cupón
        </a>
    </div>

    {{-- Tabla de cupones --}}
    @if($cupones->isNotEmpty())
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Código</th>
                                <th scope="col">Descuento</th>
                                <th scope="col">Tipo</th>
                                <th scope="col">Fecha de creación</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cupones as $cupon)
                                <tr>
                                    <td class="fw-semibold">{{ $cupon->vCodigo_cupon }}</td>
                                    <td>
                                        {{ $cupon->eTipo === 'porcentaje' 
                                            ? $cupon->dDescuento . '%' 
                                            : '$' . number_format($cupon->dDescuento, 2) }}
                                    </td>
                                    <td>
                                        <span class="badge 
                                            {{ $cupon->eTipo === 'porcentaje' ? 'bg-success' : 'bg-info' }}">
                                            {{ ucfirst($cupon->eTipo) }}
                                        </span>
                                    </td>
                                    <td class="text-muted small">
                                        {{ $cupon->created_at ? $cupon->created_at->format('d/m/Y H:i') : 'N/A' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        {{-- Mensaje si no hay cupones --}}
        <div class="alert alert-info text-center mt-4" role="alert">
            No hay cupones registrados por ahora. ¡Crea uno nuevo! 😄
        </div>
    @endif

</div>
@endsection
