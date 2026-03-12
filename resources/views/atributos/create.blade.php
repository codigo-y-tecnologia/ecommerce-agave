@extends('layouts.app')

@section('title', 'Crear Nuevo Atributo')
@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-plus-circle me-2"></i>Crear Nuevo Atributo</h1>
        <a href="{{ route('atributos.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Volver
        </a>
    </div>

    <!-- SweetAlert2 para mensajes de sesión -->
    @if(session('success'))
        <script>
            Swal.fire({
                title: "¡Éxito!",
                text: "{{ session('success') }}",
                icon: "success",
                confirmButtonText: "Aceptar"
            }).then(() => {
                window.location.href = "{{ route('atributos.index') }}";
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "{{ session('error') }}",
                confirmButtonText: "Entendido"
            });
        </script>
    @endif

    @if($errors->any())
        <script>
            let errorMessages = '';
            @foreach($errors->all() as $error)
                errorMessages += '• {{ $error }}\n';
            @endforeach
            
            Swal.fire({
                icon: "error",
                title: "Error de validación",
                text: errorMessages,
                confirmButtonText: "Entendido",
                confirmButtonColor: "#2E8B57"
            });
        </script>
    @endif

    <form action="{{ route('atributos.store') }}" method="POST" id="formAtributo">
        @csrf

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información del Atributo</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="vNombre" class="form-label fw-bold">
                                Nombre del Atributo <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="vNombre" 
                                   id="vNombre" 
                                   class="form-control @error('vNombre') is-invalid @enderror"
                                   value="{{ old('vNombre') }}" 
                                   required 
                                   placeholder="Ej: Tamaño, Tipo, Edad, Sabor"
                                   maxlength="100"
                                   autocomplete="off">
                            @error('vNombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-muted mt-1">
                                <i class="fas fa-lightbulb me-1"></i>
                                Ejemplos: Tamaño (750ml, 1L), Tipo (Joven, Reposado), Edad (6 meses, 1 año)
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="vSlug" class="form-label fw-bold">
                                Slug (URL amigable)
                            </label>
                            <input type="text" 
                                   name="vSlug" 
                                   id="vSlug" 
                                   class="form-control @error('vSlug') is-invalid @enderror"
                                   value="{{ old('vSlug') }}"
                                   placeholder="Se genera automáticamente"
                                   maxlength="100"
                                   autocomplete="off">
                            @error('vSlug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="slug-info bg-light p-2 rounded mt-1" id="slugPreview">
                                <i class="fas fa-link me-1 text-success"></i>
                                <span id="slugText" class="text-muted">URL generada aparecerá aquí</span>
                            </div>
                            <div class="form-text text-muted mt-1">
                                <i class="fas fa-info-circle me-1"></i>
                                El slug se actualiza automáticamente según el nombre.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="tDescripcion" class="form-label fw-bold">
                        Descripción (Opcional)
                    </label>
                    <textarea name="tDescripcion" 
                              id="tDescripcion" 
                              class="form-control @error('tDescripcion') is-invalid @enderror"
                              rows="3" 
                              placeholder="Describe el atributo">{{ old('tDescripcion') }}</textarea>
                    @error('tDescripcion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text text-muted mt-1">
                        <i class="fas fa-lightbulb me-1"></i>
                        Ej: "Tamaño de la botella en mililitros o litros"
                    </div>
                </div>

                <div class="form-group mb-3">
                    <div class="form-check form-switch">
                        <input type="checkbox" 
                               name="bActivo" 
                               id="bActivo" 
                               class="form-check-input" 
                               value="1" 
                               {{ old('bActivo', true) ? 'checked' : '' }}>
                        <label for="bActivo" class="form-check-label fw-bold">
                            Atributo activo
                        </label>
                    </div>
                    <div class="form-text text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Si está desactivado, el atributo no estará disponible para asignar a productos
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success btn-lg px-4" id="btnSubmit">
                <i class="fas fa-save me-2"></i> Guardar Atributo
            </button>
            <a href="{{ route('atributos.index') }}" class="btn btn-secondary btn-lg px-4">
                <i class="fas fa-times me-2"></i> Cancelar
            </a>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
    .slug-info {
        font-size: 0.85rem;
        border-left: 3px solid #2E8B57;
    }
    .form-control:focus {
        border-color: #2E8B57;
        box-shadow: 0 0 0 0.25rem rgba(46, 139, 87, 0.25);
    }
</style>
@endpush

@push('scripts')
<script>
// Variables para controlar si el usuario ha editado manualmente el slug
let slugEditedManually = false;
let lastGeneratedSlug = '';

// Función para generar slug a partir de texto
function generateSlug(text) {
    return text
        .toLowerCase()
        .normalize("NFD").replace(/[\u0300-\u036f]/g, "") // quitar acentos
        .replace(/[^a-z0-9\s]/g, '') // quitar caracteres especiales
        .replace(/\s+/g, '-') // espacios por guiones
        .replace(/-+/g, '-') // guiones múltiples por uno solo
        .replace(/^-|-$/g, '') // quitar guiones al inicio y final
        .trim();
}

// Función para actualizar el preview del slug
function updateSlugPreview(slug) {
    const slugText = document.getElementById('slugText');
    const slugPreview = document.getElementById('slugPreview');
    
    if (slug.trim() === '') {
        slugText.textContent = 'URL generada aparecerá aquí';
        slugText.className = 'text-muted';
    } else {
        const fullUrl = '{{ url("/") }}' + '/atributos/' + slug;
        slugText.textContent = fullUrl;
        slugText.className = 'text-success fw-bold';
    }
}

// Evento para el campo de nombre
document.getElementById('vNombre').addEventListener('input', function() {
    const nombre = this.value.trim();
    const slugInput = document.getElementById('vSlug');
    
    // Solo actualizar automáticamente si no ha sido editado manualmente
    if (!slugEditedManually || slugInput.value === '' || slugInput.value === lastGeneratedSlug) {
        if (nombre) {
            const generatedSlug = generateSlug(nombre);
            slugInput.value = generatedSlug;
            lastGeneratedSlug = generatedSlug;
            updateSlugPreview(generatedSlug);
        } else {
            slugInput.value = '';
            updateSlugPreview('');
        }
    }
});

// Evento para el campo de slug - detectar edición manual
document.getElementById('vSlug').addEventListener('input', function() {
    const nombreInput = document.getElementById('vNombre');
    const currentSlug = this.value.trim();
    const nombreSlug = generateSlug(nombreInput.value.trim());
    
    // Si el usuario empieza a editar manualmente, marcar como editado
    if (currentSlug !== nombreSlug && currentSlug !== '') {
        slugEditedManually = true;
    }
    
    // Si el usuario borra todo el slug, resetear la bandera
    if (currentSlug === '') {
        slugEditedManually = false;
    }
    
    // Limpiar slug: solo letras, números y guiones
    this.value = currentSlug
        .toLowerCase()
        .replace(/[^a-z0-9-]/g, '-')
        .replace(/-+/g, '-')
        .replace(/^-|-$/g, '');
    
    updateSlugPreview(this.value);
});

// Evento para el campo de slug - detectar cuando el usuario pierde focus
document.getElementById('vSlug').addEventListener('blur', function() {
    // Si después de perder focus el campo está vacío, regenerar desde el nombre
    if (this.value.trim() === '' && !slugEditedManually) {
        const nombreInput = document.getElementById('vNombre');
        const nombre = nombreInput.value.trim();
        
        if (nombre) {
            const generatedSlug = generateSlug(nombre);
            this.value = generatedSlug;
            lastGeneratedSlug = generatedSlug;
            updateSlugPreview(generatedSlug);
        }
    }
});

// Botón para regenerar slug desde el nombre
document.addEventListener('DOMContentLoaded', function() {
    // Crear botón para regenerar slug
    const slugGroup = document.querySelector('.form-group:has(#vSlug)');
    if (slugGroup) {
        const regenerateButton = document.createElement('button');
        regenerateButton.type = 'button';
        regenerateButton.className = 'btn btn-sm btn-outline-secondary mt-2';
        regenerateButton.innerHTML = '<i class="fas fa-redo me-1"></i> Regenerar desde nombre';
        regenerateButton.id = 'regenerateSlugBtn';
        
        slugGroup.appendChild(regenerateButton);
        
        // Evento para el botón de regenerar
        document.getElementById('regenerateSlugBtn').addEventListener('click', function() {
            const nombreInput = document.getElementById('vNombre');
            const slugInput = document.getElementById('vSlug');
            const nombre = nombreInput.value.trim();
            
            if (nombre) {
                const generatedSlug = generateSlug(nombre);
                slugInput.value = generatedSlug;
                lastGeneratedSlug = generatedSlug;
                slugEditedManually = false;
                updateSlugPreview(generatedSlug);
                
                // Mostrar mensaje de confirmación
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-check me-1"></i> Regenerado!';
                this.classList.remove('btn-outline-secondary');
                this.classList.add('btn-outline-success');
                
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.classList.remove('btn-outline-success');
                    this.classList.add('btn-outline-secondary');
                }, 1500);
            }
        });
    }
    
    // Inicializar el preview del slug
    const initialSlug = document.getElementById('vSlug').value;
    updateSlugPreview(initialSlug);
    
    // Auto-focus en el primer campo
    const nombreInput = document.getElementById('vNombre');
    if (nombreInput) {
        nombreInput.focus();
    }
});

// Validación del formulario con SweetAlert2
document.getElementById('formAtributo').addEventListener('submit', function(e) {
    const nombreInput = document.getElementById('vNombre');
    const slugInput = document.getElementById('vSlug');
    
    // Validar nombre
    if (!nombreInput.value.trim()) {
        e.preventDefault();
        Swal.fire({
            icon: "warning",
            title: "Campo requerido",
            text: "El nombre del atributo es obligatorio",
            confirmButtonText: "Entendido",
            confirmButtonColor: "#2E8B57"
        }).then(() => {
            nombreInput.focus();
        });
        return false;
    }
    
    // Si el slug está vacío, generarlo automáticamente
    if (!slugInput.value.trim()) {
        const generatedSlug = generateSlug(nombreInput.value.trim());
        slugInput.value = generatedSlug;
    }
    
    // Cambiar estado del botón
    const btnSubmit = document.getElementById('btnSubmit');
    btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Guardando...';
    btnSubmit.disabled = true;
});

// Remover clase de error cuando el usuario escribe
document.querySelectorAll('input, textarea').forEach(input => {
    input.addEventListener('input', function() {
        this.classList.remove('is-invalid');
    });
});
</script>
@endpush