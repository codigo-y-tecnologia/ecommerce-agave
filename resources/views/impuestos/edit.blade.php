@extends('layouts.admins')

@section('content')
<div class="container">
    <h1 class="mb-3">Editar impuesto #{{ $impuesto->id_impuesto }}</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Revisa los campos:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('impuestos.update', $impuesto->id_impuesto) }}" method="POST" class="card card-body">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Nombre (vNombre)</label>
            <input type="text" name="vNombre" class="form-control" maxlength="100"
                   value="{{ old('vNombre', $impuesto->vNombre) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Tipo (eTipo)</label>
            <select name="eTipo" class="form-select" required>
                @foreach (['IVA','IEPS','OTRO'] as $opt)
                    <option value="{{ $opt }}" @selected(old('eTipo', $impuesto->eTipo)===$opt)>{{ $opt }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Porcentaje (dPorcentaje)</label>
            <input type="number" name="dPorcentaje" class="form-control" step="0.01" min="0" max="100"
                   value="{{ old('dPorcentaje', $impuesto->dPorcentaje) }}" required>
        </div>

        <div class="form-check form-switch mb-4">
            <input class="form-check-input" type="checkbox" name="bActivo" id="bActivo" value="1"
                   @checked(old('bActivo', (bool)$impuesto->bActivo))>
            <label class="form-check-label" for="bActivo">Activo</label>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('impuestos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            <button class="btn btn-primary">Actualizar</button>
        </div>
    </form>
</div>
@endsection