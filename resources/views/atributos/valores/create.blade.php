@extends('admin.productos.administrar-productos')

@section('title', 'Crear Valor para: ' . $atributo->vNombre)
@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-success">
            <i class="fas fa-plus-circle me-2"></i>Crear Valor para: {{ $atributo->vNombre }}
        </h1>
        <a href="{{ route('atributos.valores', $atributo) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Volver a Valores
        </a>
    </div>

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

    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información del Valor</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('atributos.valores.store', $atributo) }}" method="POST" id="formValor">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="vValor" class="form-label fw-bold">
                                Valor <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="vValor" 
                                   id="vValor" 
                                   class="form-control @error('vValor') is-invalid @enderror"
                                   value="{{ old('vValor') }}" 
                                   required 
                                   placeholder="Ej: 750ml, Joven, 6 meses"
                                   maxlength="100"
                                   autocomplete="off">
                            @error('vValor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-muted mt-1">
                                <i class="fas fa-lightbulb me-1"></i>
                                Ejemplos: 750ml, Joven, Reposado, 6 meses
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
                                El slug se genera automáticamente desde el valor
                            </div>
                        </div>
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
                            Valor activo
                        </label>
                    </div>
                    <div class="form-text text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Si está desactivado, el valor no estará disponible para asignar a productos
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-success px-4" id="btnSubmit">
                        <i class="fas fa-save me-2"></i> Guardar Valor
                    </button>
                    <a href="{{ route('atributos.valores', $atributo) }}" class="btn btn-secondary px-4">
                        <i class="fas fa-times me-2"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
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
    if (slug.trim() === '') {
        slugText.textContent = 'URL generada aparecerá aquí';
        slugText.className = 'text-muted';
    } else {
        slugText.textContent = slug;
        slugText.className = 'text-success fw-bold';
    }
}

// Evento para el campo de valor
document.getElementById('vValor').addEventListener('input', function() {
    const valor = this.value.trim();
    const slugInput = document.getElementById('vSlug');
    
    // Solo actualizar automáticamente si no ha sido editado manualmente
    if (!slugEditedManually || slugInput.value === '' || slugInput.value === lastGeneratedSlug) {
        if (valor) {
            const generatedSlug = generateSlug(valor);
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
    const valorInput = document.getElementById('vValor');
    const currentSlug = this.value.trim();
    const valorSlug = generateSlug(valorInput.value.trim());
    
    if (currentSlug !== valorSlug && currentSlug !== '') {
        slugEditedManually = true;
    }
    
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
    if (this.value.trim() === '' && !slugEditedManually) {
        const valorInput = document.getElementById('vValor');
        const valor = valorInput.value.trim();
        
        if (valor) {
            const generatedSlug = generateSlug(valor);
            this.value = generatedSlug;
            lastGeneratedSlug = generatedSlug;
            updateSlugPreview(generatedSlug);
        }
    }
});

// Botón para regenerar slug desde el valor
document.addEventListener('DOMContentLoaded', function() {
    const slugGroup = document.querySelector('.form-group:has(#vSlug)');
    if (slugGroup && !document.getElementById('regenerateSlugBtn')) {
        const regenerateButton = document.createElement('button');
        regenerateButton.type = 'button';
        regenerateButton.className = 'btn btn-sm btn-outline-secondary mt-2';
        regenerateButton.innerHTML = '<i class="fas fa-redo me-1"></i> Regenerar desde valor';
        regenerateButton.id = 'regenerateSlugBtn';
        
        slugGroup.appendChild(regenerateButton);
        
        document.getElementById('regenerateSlugBtn').addEventListener('click', function() {
            const valorInput = document.getElementById('vValor');
            const slugInput = document.getElementById('vSlug');
            const valor = valorInput.value.trim();
            
            if (valor) {
                const generatedSlug = generateSlug(valor);
                slugInput.value = generatedSlug;
                lastGeneratedSlug = generatedSlug;
                slugEditedManually = false;
                updateSlugPreview(generatedSlug);
                
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
    
    const initialSlug = document.getElementById('vSlug').value;
    updateSlugPreview(initialSlug);
    
    const valorInput = document.getElementById('vValor');
    if (valorInput) {
        valorInput.focus();
    }
});

// Validación del formulario
document.getElementById('formValor').addEventListener('submit', function(e) {
    const valorInput = document.getElementById('vValor');
    const slugInput = document.getElementById('vSlug');
    
    if (!valorInput.value.trim()) {
        e.preventDefault();
        Swal.fire({
            icon: "warning",
            title: "Campo requerido",
            text: "El valor es obligatorio",
            confirmButtonText: "Entendido",
            confirmButtonColor: "#2E8B57"
        }).then(() => {
            valorInput.focus();
        });
        return false;
    }
    
    if (!slugInput.value.trim()) {
        const generatedSlug = generateSlug(valorInput.value.trim());
        slugInput.value = generatedSlug;
    }
    
    const btnSubmit = document.getElementById('btnSubmit');
    btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Guardando...';
    btnSubmit.disabled = true;
});

// Remover clase de error cuando el usuario escribe
document.querySelectorAll('input').forEach(input => {
    input.addEventListener('input', function() {
        this.classList.remove('is-invalid');
    });
});
</script>
@endpush