@extends('layouts.app')

@section('title', 'Consultar pedido')

@section('content')

<div class="container" style="max-width:500px">
    <h2 class="fw-bold mb-4">Consultar pedido</h2>

    @if($errors->has('general'))
        <div class="alert alert-danger">
            {{ $errors->first('general') }}
        </div>
    @endif

    <form method="POST" action="{{ route('consulta.pedido.buscar') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Número de pedido</label>
            <input type="number"
                   name="id_pedido"
                   class="form-control"
                   value="{{ old('id_pedido') }}"
                   required>
        </div>

        <div class="mb-3">
            <label class="form-label">Correo electrónico</label>
            <input type="email"
                   name="email"
                   class="form-control"
                   value="{{ old('email') }}"
                   maxlength="100"
                   required>
        </div>

        <button type="submit" class="btn btn-primary w-100">
            Buscar pedido
        </button>
    </form>
</div>

@endsection