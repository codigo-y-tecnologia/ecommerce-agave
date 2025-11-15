@extends('admin.productos.administrar-productos')

@section('title', 'Crear Marca')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-plus-circle me-2"></i>Crear Nueva Marca</h1>
            <a href="{{ route('marcas.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('marcas.store') }}" method="POST" id="marcaForm">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="vNombre" class="form-label">Nombre de la Marca *</label>
                        <input type="text" class="form-control @error('vNombre') is-invalid @enderror" 
                               id="vNombre" name="vNombre" value="{{ old('vNombre') }}" 
                               placeholder="Ej: José Cuervo, Patrón, Don Julio" required>
                        @error('vNombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="tDescripcion" class="form-label">Descripción</label>
                        <textarea class="form-control @error('tDescripcion') is-invalid @enderror" 
                                  id="tDescripcion" name="tDescripcion" rows="4" 
                                  placeholder="Describe la marca...">{{ old('tDescripcion') }}</textarea>
                        @error('tDescripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="button" class="btn btn-secondary me-md-2" onclick="limpiarFormulario()">
                            <i class="fas fa-undo me-1"></i> Limpiar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Guardar Marca
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function limpiarFormulario() {
    // Método 1: Resetear el formulario completo
    document.getElementById('marcaForm').reset();
    
    // Método 2: Limpiar manualmente cada campo
    document.getElementById('vNombre').value = '';
    document.getElementById('tDescripcion').value = '';
    
    // Método 3: Remover clases de error y mensajes
    const elementosConError = document.querySelectorAll('.is-invalid');
    elementosConError.forEach(elemento => {
        elemento.classList.remove('is-invalid');
    });
    
    const mensajesError = document.querySelectorAll('.invalid-feedback');
    mensajesError.forEach(mensaje => {
        mensaje.remove();
    });
    
    
}
</script>
@endsection