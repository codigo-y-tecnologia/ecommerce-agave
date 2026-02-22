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

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="dPrecio_extra">Precio extra ($)</label>
                            <input type="text" name="dPrecio_extra" id="dPrecio_extra" 
                                   class="form-control @error('dPrecio_extra') is-invalid @enderror"
                                   value="{{ old('dPrecio_extra', '0.00') }}"
                                   oninput="validarPrecio(this)"
                                   placeholder="0.00"
                                   autocomplete="off">
                            @error('dPrecio_extra')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="iStock">Stock específico</label>
                            <input type="text" name="iStock" id="iStock" 
                                   class="form-control @error('iStock') is-invalid @enderror"
                                   value="{{ old('iStock', '0') }}"
                                   oninput="validarStock(this)"
                                   placeholder="0"
                                   autocomplete="off">
                            @error('iStock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="iOrden">Orden</label>
                            <input type="text" name="iOrden" id="iOrden" 
                                   class="form-control @error('iOrden') is-invalid @enderror"
                                   value="{{ old('iOrden', '0') }}"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                   placeholder="0"
                                   autocomplete="off">
                            @error('iOrden')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
// Función para validar precio
function validarPrecio(input) {
    let value = input.value;
    const cursorPos = input.selectionStart;
    
    if (value === '') {
        input.value = '0.00';
        return;
    }
    
    // Permitir solo números y punto
    value = value.replace(/[^0-9.]/g, '');
    
    // Evitar múltiples puntos
    const puntos = value.split('.').length - 1;
    if (puntos > 1) {
        const partes = value.split('.');
        value = partes[0] + '.' + partes.slice(1).join('');
    }
    
    // Si empieza con punto, agregar 0
    if (value.startsWith('.')) {
        value = '0' + value;
    }
    
    // Limitar parte entera a 7 dígitos
    const partesNumero = value.split('.');
    const parteEntera = partesNumero[0];
    if (parteEntera.length > 7) {
        partesNumero[0] = parteEntera.substring(0, 7);
        value = partesNumero.join('.');
    }
    
    // Limitar parte decimal a 2 dígitos
    if (partesNumero[1] && partesNumero[1].length > 2) {
        partesNumero[1] = partesNumero[1].substring(0, 2);
        value = partesNumero[0] + '.' + partesNumero[1];
    }
    
    input.value = value;
    
    // Restaurar posición del cursor
    const newLength = input.value.length;
    const newCursorPos = Math.min(cursorPos, newLength);
    setTimeout(() => {
        input.setSelectionRange(newCursorPos, newCursorPos);
    }, 0);
}

// Función para validar stock
function validarStock(input) {
    input.value = input.value.replace(/[^0-9]/g, '');
    if (input.value.length > 6) {
        input.value = input.value.substring(0, 6);
    }
    if (input.value && parseInt(input.value) < 0) {
        input.value = '0';
    }
    if (input.value === '') {
        input.value = '0';
    }
}

// Función para generar slug
function generarSlug(texto) {
    return texto
        .toLowerCase()
        .normalize("NFD").replace(/[\u0300-\u036f]/g, "")
        .replace(/[^a-z0-9\s]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .replace(/^-|-$/g, '')
        .trim();
}

// Variables para controlar estado
let slugEditadoManualmente = false;
let ultimoSlugGenerado = '';

// Cuando se escribe en el campo de valor
document.getElementById('vValor').addEventListener('input', function() {
    const valor = this.value.trim();
    const slugInput = document.getElementById('vSlug');
    
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
    
    if (this.value === valorSlug) {
        slugEditadoManualmente = false;
    } else {
        slugEditadoManualmente = true;
    }
});

// Cuando se escribe en el slug
document.getElementById('vSlug').addEventListener('input', function() {
    slugEditadoManualmente = true;
    
    this.value = this.value
        .toLowerCase()
        .replace(/[^a-z0-9-]/g, '-')
        .replace(/-+/g, '-')
        .replace(/^-|-$/g, '');
});

// Cuando se pierde el focus del slug
document.getElementById('vSlug').addEventListener('blur', function() {
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
    const slugGroup = document.querySelector('.form-group:has(#vSlug)');
    const regenerarBtn = document.createElement('button');
    regenerarBtn.type = 'button';
    regenerarBtn.className = 'btn btn-sm btn-outline-secondary mt-2';
    regenerarBtn.innerHTML = '<i class="fas fa-redo me-1"></i> Regenerar desde valor';
    regenerarBtn.id = 'regenerarSlugBtn';
    
    slugGroup.appendChild(regenerarBtn);
    
    document.getElementById('regenerarSlugBtn').addEventListener('click', function() {
        const valorInput = document.getElementById('vValor');
        const slugInput = document.getElementById('vSlug');
        const valor = valorInput.value.trim();
        
        if (valor) {
            const nuevoSlug = generarSlug(valor);
            slugInput.value = nuevoSlug;
            ultimoSlugGenerado = nuevoSlug;
            slugEditadoManualmente = false;
            
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