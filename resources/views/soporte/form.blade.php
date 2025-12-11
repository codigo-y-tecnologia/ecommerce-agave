@extends('layouts.app')

@section('title', 'Soporte')

@section('content')
<div class="container mt-5">

    <h2 class="fw-bold mb-4 text-center">Contactar a Soporte</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('soporte.send') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Tu mensaje</label>
                    <textarea name="mensaje" class="form-control" rows="5" required></textarea>
                    @error('mensaje')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <button class="btn btn-primary px-4">Enviar mensaje</button>
            </form>
        </div>
    </div>

</div>
@endsection
