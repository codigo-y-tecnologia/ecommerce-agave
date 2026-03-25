@extends('layouts.public')

@section('title', 'Bienvenido')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-lg border-0 rounded-lg mt-5">
            <div class="card-body p-5 text-center">
                <div class="mb-4">
                    <i class="fas fa-wine-bottle fa-4x text-primary"></i>
                </div>
                
                <h1 class="fw-bold mb-3">¡Bienvenido a Ecommerce Agave!</h1>
                <p class="lead text-muted mb-4">Elige cómo quieres continuar</p>

                <!-- Botón principal para invitados -->
                <div class="d-grid gap-3 mb-4">
                    <a href="{{ route('favoritos.index') }}" class="btn btn-success btn-lg py-3">
                        <i class="fas fa-user me-2"></i>
                        <strong>Entrar como Invitado</strong>
                        <br>
                        <small>Sin registro, empieza ahora</small>
                    </a>
                </div>

                <div class="position-relative my-4">
                    <hr>
                    <span class="position-absolute top-50 start-50 translate-middle bg-white px-3">
                        o
                    </span>
                </div>

                <!-- Opciones para usuarios registrados -->
                <div class="row g-3">
                    <div class="col-md-6">
                        <a href="{{ route('login') }}" class="btn btn-outline-primary w-100 py-3">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            Iniciar Sesión
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('usuarios.create') }}" class="btn btn-outline-success w-100 py-3">
                            <i class="fas fa-user-plus me-2"></i>
                            Crear Cuenta
                        </a>
                    </div>
                </div>

                <!-- Información para invitados -->
                <div class="card bg-light mt-4">
                    <div class="card-body text-start">
                        <h6 class="fw-bold text-success">
                            <i class="fas fa-check-circle me-2"></i>Como invitado podrás:
                        </h6>
                        <ul class="small mb-0">
                            <li>✅ Agregar productos a favoritos inmediatamente</li>
                            <li>✅ Tus favoritos se guardan en este navegador</li>
                            <li>✅ Explorar todo el catálogo sin restricciones</li>
                            <li class="text-warning">⚠️ Los favoritos se perderán si cambias de navegador</li>
                        </ul>
                    </div>
                </div>

                <div class="alert alert-info mt-4 small">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>¿Quieres guardar tus favoritos?</strong> Regístrate y se migrarán automáticamente.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Verificar si venimos de una redirección por favoritos
        const urlParams = new URLSearchParams(window.location.search);
        const fromFavoritos = urlParams.get('from_favoritos');
        const producto = urlParams.get('producto');
        const variacion = urlParams.get('variacion');
        const tipo = urlParams.get('tipo');
        
        if (fromFavoritos === 'true' && producto) {
            // Mostrar mensaje informativo
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-info alert-dismissible fade show mt-3';
            alertDiv.setAttribute('role', 'alert');
            alertDiv.innerHTML = `
                <i class="fas fa-info-circle me-2"></i>
                Para agregar a favoritos, elige cómo continuar.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            const cardBody = document.querySelector('.card-body');
            if (cardBody) {
                cardBody.insertBefore(alertDiv, cardBody.firstChild);
            }
            
            // Limpiar parámetros de la URL
            const url = new URL(window.location);
            url.searchParams.delete('from_favoritos');
            url.searchParams.delete('producto');
            url.searchParams.delete('variacion');
            url.searchParams.delete('tipo');
            window.history.replaceState({}, document.title, url.toString());
        }
    });
</script>
@endpush
