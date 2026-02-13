<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nuevo Atributo - Ecommerce Agave</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            max-width: 800px;
            margin-top: 30px;
        }
        .card-header {
            font-weight: 600;
        }
        .form-control:focus {
            border-color: #2E8B57;
            box-shadow: 0 0 0 0.25rem rgba(46, 139, 87, 0.25);
        }
        .btn-success {
            background-color: #2E8B57;
            border-color: #2E8B57;
        }
        .btn-success:hover {
            background-color: #26734A;
            border-color: #26734A;
        }
        .text-muted {
            font-size: 0.85rem;
        }
        h1 {
            color: #333;
            margin-bottom: 25px;
            font-weight: 600;
        }
        .slug-info {
            background-color: #e8f5e9;
            border: 1px solid #c8e6c9;
            border-radius: 4px;
            padding: 8px 12px;
            margin-top: 5px;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #2E8B57;">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-wine-bottle me-2"></i>Ecommerce Agave
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('atributos.index') }}">
                            <i class="fas fa-arrow-left me-1"></i> Volver a Atributos
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-plus-circle me-2"></i>Crear Nuevo Atributo</h1>
        </div>

        <!-- Alert Messages con SweetAlert2 -->
        @if(session('success'))
            <script>
                Swal.fire({
                    title: "¡Registrado!",
                    text: "{{ session('success') }}",
                    icon: "success",
                    draggable: true,
                    confirmButtonText: "Aceptar"
                }).then(() => {
                    window.location.href = "{{ route('atributos.index') }}";
                });
            </script>
        @endif

        @if($errors->any())
            <script>
                let errorMessages = '';
                @foreach($errors->all() as $error)
                    errorMessages += '• {{ addslashes($error) }}\\n';
                @endforeach
                
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "¡Algo salió mal!",
                    html: '<div class="text-start">' +
                          '<p>Se encontraron los siguientes errores:</p>' +
                          '<pre style="white-space: pre-wrap; text-align: left;">' + errorMessages + '</pre>' +
                          '</div>',
                    confirmButtonText: "Entendido"
                });
            </script>
        @endif

        <form action="{{ route('atributos.store') }}" method="POST" id="formAtributo">
            @csrf

            <div class="card mb-4 shadow-sm">
                <div class="card-header text-white" style="background-color: #2E8B57;">
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
                                    Ejemplos para mezcal: Tamaño (750ml, 1L), Tipo (Joven, Reposado), Edad (6 meses, 1 año)
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
                                <div class="slug-info" id="slugPreview">
                                    <i class="fas fa-link me-1"></i>
                                    <span id="slugText">URL generada aparecerá aquí</span>
                                </div>
                                <div class="form-text text-muted mt-1">
                                    <i class="fas fa-info-circle me-1"></i>
                                    El slug se actualiza automáticamente según el nombre. Puedes editarlo manualmente.
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
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
        const slugPreview = document.getElementById('slugText');
        const fullUrl = window.location.origin + '/atributos/' + slug;
        
        if (slug.trim() === '') {
            slugPreview.textContent = 'URL generada aparecerá aquí';
            slugPreview.parentElement.style.backgroundColor = '#e8f5e9';
        } else {
            slugPreview.textContent = fullUrl;
            slugPreview.parentElement.style.backgroundColor = '#e3f2fd';
        }
    }
    
    // Evento para el campo de nombre
    document.getElementById('vNombre').addEventListener('input', function() {
        const nombre = this.value.trim();
        const slugInput = document.getElementById('vSlug');
        
        // Solo actualizar automáticamente si:
        // 1. El slug está vacío, O
        // 2. El slug nunca ha sido editado manualmente, O
        // 3. El slug actual es igual al último generado automáticamente
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
            .replace(/[^a-z0-9-]/g, '-') // caracteres no permitidos por guiones
            .replace(/-+/g, '-') // guiones múltiples por uno solo
            .replace(/^-|-$/g, ''); // quitar guiones al inicio y final
        
        updateSlugPreview(this.value);
    });
    
    // Evento para el campo de slug - detectar cuando el usuario hace focus
    document.getElementById('vSlug').addEventListener('focus', function() {
        // Si el campo está vacío o tiene el mismo valor que el generado automáticamente
        const nombreInput = document.getElementById('vNombre');
        const nombreSlug = generateSlug(nombreInput.value.trim());
        
        if (this.value === '' || this.value === nombreSlug) {
            slugEditedManually = false;
        } else {
            slugEditedManually = true;
        }
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
        const slugContainer = document.querySelector('.form-group:has(#vSlug)');
        const regenerateButton = document.createElement('button');
        regenerateButton.type = 'button';
        regenerateButton.className = 'btn btn-sm btn-outline-secondary mt-2';
        regenerateButton.innerHTML = '<i class="fas fa-redo me-1"></i> Regenerar desde nombre';
        regenerateButton.id = 'regenerateSlugBtn';
        
        slugContainer.appendChild(regenerateButton);
        
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
        e.preventDefault(); // Prevenir envío normal
        
        const nombreInput = document.getElementById('vNombre');
        const slugInput = document.getElementById('vSlug');
        const btnSubmit = document.getElementById('btnSubmit');
        
        // Validar nombre
        if (!nombreInput.value.trim()) {
            Swal.fire({
                icon: "warning",
                title: "Campo requerido",
                text: "El nombre del atributo es obligatorio",
                confirmButtonText: "Entendido"
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
        btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Guardando...';
        btnSubmit.disabled = true;
        
        // Enviar formulario
        this.submit();
    });
    
    // Remover clase de error cuando el usuario escribe
    document.querySelectorAll('input, textarea').forEach(input => {
        input.addEventListener('input', function() {
            this.classList.remove('is-invalid');
        });
    });
    </script>
</body>
</html>
