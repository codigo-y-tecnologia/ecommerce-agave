@extends('layouts.app')

@section('title', 'Crear Etiqueta')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-plus me-2"></i>Crear Nueva Etiqueta</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('etiquetas.store') }}" method="POST" id="createForm">
                        @csrf
                        <div class="mb-3">
                            <label for="vNombre" class="form-label">Nombre de la Etiqueta</label>
                            <input type="text" class="form-control @error('vNombre') is-invalid @enderror" 
                                   id="vNombre" name="vNombre" value="{{ old('vNombre') }}" 
                                   placeholder="Ej: Oferta, Nuevo, Popular..." required>
                            @error('vNombre')
                                @if($message == 'The vNombre has already been taken.')
                                    <div class="invalid-feedback">Este nombre de etiqueta ya existe.</div>
                                @elseif($message == 'The vNombre field is required.')
                                    <div class="invalid-feedback">El nombre de la etiqueta es obligatorio.</div>
                                @elseif($message == 'The vNombre must not be greater than 100 characters.')
                                    <div class="invalid-feedback">El nombre no puede tener más de 100 caracteres.</div>
                                @else
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @endif
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="tDescripcion" class="form-label">Descripción</label>
                            <textarea class="form-control @error('tDescripcion') is-invalid @enderror" 
                                      id="tDescripcion" name="tDescripcion" rows="3" 
                                      placeholder="Descripción opcional de la etiqueta...">{{ old('tDescripcion') }}</textarea>
                            @error('tDescripcion')
                                @if($message == 'The tDescripcion must not be greater than 500 characters.')
                                    <div class="invalid-feedback">La descripción no puede tener más de 500 caracteres.</div>
                                @else
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @endif
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('etiquetas.index') }}" class="btn btn-secondary" id="cancelBtn">
                                <i class="fas fa-arrow-left me-1"></i> Volver
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Guardar Etiqueta
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let formChanged = false;
    let isSubmitting = false;
    
    // Detectar cambios en el formulario
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('createForm');
        const cancelBtn = document.getElementById('cancelBtn');
        
        // Detectar cambios en los inputs
        const inputs = ['vNombre', 'tDescripcion'];
        inputs.forEach(inputId => {
            const element = document.getElementById(inputId);
            if (element) {
                element.addEventListener('input', () => formChanged = true);
                element.addEventListener('change', () => formChanged = true);
            }
        });
        
        // Configurar envío del formulario
        if (form) {
            form.addEventListener('submit', function() {
                isSubmitting = true;
            });
        }
        
        // Configurar botón de cancelar
        if (cancelBtn) {
            cancelBtn.addEventListener('click', function(e) {
                if (formChanged && !isSubmitting) {
                    e.preventDefault();
                    
                    Swal.fire({
                        title: "¿Quieres guardar los cambios?",
                        text: "Tienes cambios sin guardar en el formulario.",
                        icon: "question",
                        showDenyButton: true,
                        showCancelButton: true,
                        confirmButtonText: "Guardar",
                        denyButtonText: "No guardar",
                        cancelButtonText: "Cancelar"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            isSubmitting = true;
                            form.submit();
                        } else if (result.isDenied) {
                            window.location.href = cancelBtn.href;
                        }
                    });
                }
            });
        }
        
        // Mostrar alerta de éxito después de crear
        @if(session('success') && strpos(session('success'), 'creada') !== false)
        Swal.fire({
            title: "¡Éxito!",
            text: "{{ session('success') }}",
            icon: "success",
            draggable: true,
            timer: 3000,
            timerProgressBar: true
        });
        @endif
        
        // Mostrar alerta de error
        @if(session('error'))
        Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "{{ session('error') }}",
            footer: 'Por favor, verifica los datos ingresados'
        });
        @endif
        
        // Mostrar errores de validación con SweetAlert
        @if($errors->any())
        @php
            $errorMessages = [];
            foreach($errors->all() as $error) {
                $errorMessages[] = $error;
            }
            $errorText = implode('\n', $errorMessages);
        @endphp
        Swal.fire({
            icon: "error",
            title: "Error de Validación",
            text: "{{ $errorText }}",
            footer: 'Corrige los errores e intenta de nuevo'
        });
        @endif
    });
    
    // Prevenir salida con cambios sin guardar
    window.addEventListener('beforeunload', function(e) {
        if (formChanged && !isSubmitting) {
            e.preventDefault();
            e.returnValue = 'Tienes cambios sin guardar. ¿Estás seguro de que quieres salir?';
        }
    });
</script>

@push('styles')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@push('scripts')
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush
@endsection
