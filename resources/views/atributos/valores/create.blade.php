@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Agregar Valor para: {{ $atributo->vNombre }}</h1>
        <a href="{{ route('atributos.valores', $atributo) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Volver
        </a>
    </div>

    <form action="{{ route('atributos.valores.store', $atributo) }}" method="POST">
        @csrf

        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Información del Valor</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="vValor">Valor *</label>
                            <input type="text" name="vValor" id="vValor" 
                                   class="form-control @error('vValor') is-invalid @enderror"
                                   value="{{ old('vValor') }}" required 
                                   placeholder="Ej: 750ml, Joven, 6 meses"
                                   autocomplete="off">
                            @error('vValor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                El valor que aparecerá en las opciones del producto
                            </small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="vSlug">Slug (URL amigable)</label>
                            <input type="text" name="vSlug" id="vSlug" 
                                   class="form-control @error('vSlug') is-invalid @enderror"
                                   value="{{ old('vSlug') }}"
                                   placeholder="Se genera automáticamente"
                                   autocomplete="off">
                            @error('vSlug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Versión para URL del valor. Se sincroniza automáticamente.
                            </small>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <div class="form-check">
                        <input type="checkbox" name="bActivo" id="bActivo" 
                               class="form-check-input" value="1" 
                               {{ old('bActivo', true) ? 'checked' : '' }}>
                        <label for="bActivo" class="form-check-label">Activo</label>
                    </div>
                    <small class="form-text text-muted">
                        Si está desactivado, el valor no estará disponible para asignar a productos
                    </small>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save me-1"></i> Guardar Valor
            </button>
            <a href="{{ route('atributos.valores', $atributo) }}" class="btn btn-secondary">
                <i class="fas fa-times me-1"></i> Cancelar
            </a>
        </div>
    </form>
</div>

<script>
// Función para generar slug
function generarSlug(texto) {
    return texto
        .toLowerCase()
        .normalize("NFD").replace(/[\u0300-\u036f]/g, "") // quitar acentos
        .replace(/[^a-z0-9\s]/g, '') // quitar caracteres especiales
        .replace(/\s+/g, '-') // espacios por guiones
        .replace(/-+/g, '-') // guiones múltiples por uno solo
        .trim();
}

// Variables para controlar estado
let slugEditadoManualmente = false;
let ultimoSlugGenerado = '';

// Cuando se escribe en el campo de valor
document.getElementById('vValor').addEventListener('input', function() {
    const valor = this.value.trim();
    const slugInput = document.getElementById('vSlug');
    
    // Solo actualizar si el slug no ha sido editado manualmente
    if (!slugEditadoManualmente) {
        if (valor) {
            const nuevoSlug = generarSlug(valor);
            slugInput.value = nuevoSlug;
            ultimoSlugGenerado = nuevoSlug;
        } else {
            slugInput.value = '';
            ultimoSlugGenerado = '';
        }
    }
});

// Cuando se hace focus en el slug
document.getElementById('vSlug').addEventListener('focus', function() {
    const valorInput = document.getElementById('vValor');
    const valorSlug = generarSlug(valorInput.value.trim());
    
    // Si el slug actual es igual al que se generaría automáticamente
    if (this.value === valorSlug) {
        slugEditadoManualmente = false;
    } else {
        slugEditadoManualmente = true;
    }
});

// Cuando se escribe en el slug
document.getElementById('vSlug').addEventListener('input', function() {
    // Marcar como editado manualmente
    slugEditadoManualmente = true;
    
    // Limpiar el slug: solo letras, números y guiones
    this.value = this.value
        .toLowerCase()
        .replace(/[^a-z0-9-]/g, '-') // caracteres no permitidos por guiones
        .replace(/-+/g, '-') // guiones múltiples por uno solo
        .replace(/^-|-$/g, ''); // quitar guiones al inicio y final
});

// Cuando se pierde el focus del slug
document.getElementById('vSlug').addEventListener('blur', function() {
    // Si el campo está vacío y no fue editado manualmente, regenerar
    if (this.value.trim() === '' && !slugEditadoManualmente) {
        const valorInput = document.getElementById('vValor');
        const valor = valorInput.value.trim();
        
        if (valor) {
            const nuevoSlug = generarSlug(valor);
            this.value = nuevoSlug;
            ultimoSlugGenerado = nuevoSlug;
        }
    }
});

// Botón para regenerar slug
document.addEventListener('DOMContentLoaded', function() {
    // Crear botón para regenerar slug
    const slugGroup = document.querySelector('.form-group:has(#vSlug)');
    const regenerarBtn = document.createElement('button');
    regenerarBtn.type = 'button';
    regenerarBtn.className = 'btn btn-sm btn-outline-secondary mt-2';
    regenerarBtn.innerHTML = '<i class="fas fa-redo me-1"></i> Regenerar desde valor';
    regenerarBtn.id = 'regenerarSlugBtn';
    
    slugGroup.appendChild(regenerarBtn);
    
    // Evento del botón regenerar
    document.getElementById('regenerarSlugBtn').addEventListener('click', function() {
        const valorInput = document.getElementById('vValor');
        const slugInput = document.getElementById('vSlug');
        const valor = valorInput.value.trim();
        
        if (valor) {
            const nuevoSlug = generarSlug(valor);
            slugInput.value = nuevoSlug;
            ultimoSlugGenerado = nuevoSlug;
            slugEditadoManualmente = false;
            
            // Feedback visual
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-check me-1"></i> ¡Regenerado!';
            this.classList.remove('btn-outline-secondary');
            this.classList.add('btn-outline-success');
            
            setTimeout(() => {
                this.innerHTML = originalText;
                this.classList.remove('btn-outline-success');
                this.classList.add('btn-outline-secondary');
            }, 1500);
        } else {
            alert('Primero escribe un valor');
        }
    });
});
</script>
@endsection
