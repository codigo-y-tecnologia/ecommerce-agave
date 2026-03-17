@extends('layouts.app')

@section('title', 'Editar Valor')
@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-success">
            <i class="fas fa-edit me-2"></i>Editar Valor: {{ $valor->vValor }}
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
            <form action="{{ route('atributos.valores.update', ['atributo' => $atributo, 'valor' => $valor]) }}" method="POST" id="formValor">
                @csrf
                @method('PUT')

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
                                   value="{{ old('vValor', $valor->vValor) }}" 
                                   required 
                                   placeholder="Ej: 750ml, Joven, 6 meses"
                                   maxlength="100"
                                   autocomplete="off">
                            @error('vValor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                                   value="{{ old('vSlug', $valor->vSlug) }}"
                                   placeholder="Se genera automáticamente"
                                   maxlength="100"
                                   autocomplete="off">
                            @error('vSlug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="slug-info bg-light p-2 rounded mt-1" id="slugPreview">
                                <i class="fas fa-link me-1 text-success"></i>
                                <span id="slugText">{{ $valor->vSlug }}</span>
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
                               {{ old('bActivo', $valor->bActivo) ? 'checked' : '' }}>
                        <label for="bActivo" class="form-check-label fw-bold">
                            Valor activo
                        </label>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="button" class="btn btn-success px-4" id="btnGuardar">
                        <i class="fas fa-save me-2"></i> Actualizar Valor
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
let slugEditedManually = false;
let lastGeneratedSlug = '{{ $valor->vSlug }}';
let originalValor = '{{ $valor->vValor }}';

function generateSlug(text) {
    return text
        .toLowerCase()
        .normalize("NFD").replace(/[\u0300-\u036f]/g, "")
        .replace(/[^a-z0-9\s]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .replace(/^-|-$/g, '')
        .trim();
}

function updateSlugPreview(slug) {
    const slugText = document.getElementById('slugText');
    if (slug.trim() === '') {
        slugText.textContent = 'URL generada aparecerá aquí';
        slugText.className = 'text-muted';
    } else {
        slugText.textContent = slug;
        slugText.className = slugEditedManually ? 'text-warning fw-bold' : 'text-success fw-bold';
    }
}

document.getElementById('vValor').addEventListener('input', function() {
    const valor = this.value.trim();
    const slugInput = document.getElementById('vSlug');
    
    if (!slugEditedManually) {
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
    
    this.value = currentSlug
        .toLowerCase()
        .replace(/[^a-z0-9-]/g, '-')
        .replace(/-+/g, '-')
        .replace(/^-|-$/g, '');
    
    updateSlugPreview(this.value);
});

document.getElementById('btnGuardar').addEventListener('click', function(e) {
    e.preventDefault();
    
    const valorInput = document.getElementById('vValor');
    const slugInput = document.getElementById('vSlug');
    const btnGuardar = document.getElementById('btnGuardar');
    const nuevoValor = valorInput.value.trim();
    const form = document.getElementById('formValor');
    
    if (!nuevoValor) {
        Swal.fire({
            icon: "warning",
            title: "Campo requerido",
            text: "El valor es obligatorio",
            confirmButtonText: "Entendido",
            confirmButtonColor: "#2E8B57"
        }).then(() => {
            valorInput.focus();
        });
        return;
    }
    
    if (!slugInput.value.trim()) {
        const generatedSlug = generateSlug(nuevoValor);
        slugInput.value = generatedSlug;
    }
    
    Swal.fire({
        title: "¿Guardar cambios?",
        text: "Confirma si quieres guardar los cambios realizados.",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Sí, guardar",
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#2E8B57",
        cancelButtonColor: "#d33"
    }).then((result) => {
        if (result.isConfirmed) {
            btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Guardando...';
            btnGuardar.disabled = true;
            form.submit();
        }
    });
});

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
    
    const currentSlug = document.getElementById('vSlug').value;
    updateSlugPreview(currentSlug);
    
    const valorInput = document.getElementById('vValor');
    if (valorInput) {
        valorInput.focus();
    }
});

document.querySelectorAll('input').forEach(input => {
    input.addEventListener('input', function() {
        this.classList.remove('is-invalid');
    });
});
</script>
@endpush