<!-- Modal de selección de usuario para favoritos -->
<div class="modal fade" id="modalSeleccionUsuario" tabindex="-1" aria-labelledby="modalSeleccionUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px 15px 0 0; border: none;">
                <h5 class="modal-title" id="modalSeleccionUsuarioLabel">
                    <i class="fas fa-heart me-2"></i> Agregar a favoritos
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <i class="fas fa-wine-bottle fa-3x text-primary mb-3"></i>
                    <h4>¡Elige cómo continuar!</h4>
                    <p class="text-muted">Para guardar productos en favoritos, selecciona una opción:</p>
                </div>

                <!-- Botón Invitado -->
                <div class="d-grid gap-3 mb-3">
                    <button type="button" class="btn btn-outline-success btn-lg p-3" id="btnContinuarInvitado" style="border-width: 2px;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user fa-2x me-3"></i>
                            <div class="text-start">
                                <strong>Continuar como invitado</strong><br>
                                <small class="text-muted">Sin registro, empieza ahora</small>
                            </div>
                        </div>
                    </button>
                </div>

                <div class="position-relative my-4">
                    <hr>
                    <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted">
                        o
                    </span>
                </div>

                <!-- Opciones para usuarios registrados -->
                <div class="row g-3">
                    <div class="col-md-6">
                        <a href="{{ route('login') }}?from_favoritos=true&redirect={{ urlencode(request()->fullUrl()) }}" class="btn btn-outline-primary w-100 py-3">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            Iniciar Sesión
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('usuarios.create') }}?from_favoritos=true&redirect={{ urlencode(request()->fullUrl()) }}" class="btn btn-outline-success w-100 py-3">
                            <i class="fas fa-user-plus me-2"></i>
                            Crear Cuenta
                        </a>
                    </div>
                </div>

                <!-- Información para invitados -->
                <div class="card bg-light mt-4">
                    <div class="card-body text-start p-3">
                        <h6 class="fw-bold text-success mb-2">
                            <i class="fas fa-check-circle me-2"></i>Como invitado:
                        </h6>
                        <ul class="small mb-0 ps-3">
                            <li>✅ Tus favoritos se guardan en este navegador</li>
                            <li>✅ Puedes agregar productos inmediatamente</li>
                            <li class="text-warning">⚠️ Se perderán si cambias de navegador</li>
                        </ul>
                    </div>
                </div>

                <div class="alert alert-info mt-3 small">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>¿Quieres guardarlos permanentemente?</strong> Regístrate y se migrarán automáticamente.
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let datosPendientes = null;

    function mostrarModalSeleccionUsuario(productoId, tipo, variacionId = null) {
        datosPendientes = {
            productoId: productoId,
            tipo: tipo,
            variacionId: variacionId
        };
        
        const modal = new bootstrap.Modal(document.getElementById('modalSeleccionUsuario'));
        modal.show();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const btnInvitado = document.getElementById('btnContinuarInvitado');
        if (btnInvitado) {
            btnInvitado.addEventListener('click', function() {
                if (datosPendientes) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalSeleccionUsuario'));
                    modal.hide();
                    
                    setTimeout(() => {
                        if (datosPendientes.tipo === 'variacion') {
                            toggleFavoritoInvitadoVariacion(datosPendientes.productoId, datosPendientes.variacionId);
                        } else {
                            toggleFavoritoInvitado(datosPendientes.productoId);
                        }
                        datosPendientes = null;
                    }, 300);
                }
            });
        }
    });

    // Funciones para toggle de favoritos como invitado
    function toggleFavoritoInvitado(productoId) {
        fetch(`/favoritos/invitado/toggle-producto/${productoId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.action === 'added') {
                    showNotification('✅ Producto agregado a favoritos temporales', 'success');
                    actualizarEstadoCorazon(productoId, null, true);
                } else {
                    showNotification('❌ Producto eliminado de favoritos temporales', 'info');
                    actualizarEstadoCorazon(productoId, null, false);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al gestionar favoritos', 'error');
        });
    }

    function toggleFavoritoInvitadoVariacion(productoId, variacionId) {
        fetch(`/favoritos/invitado/toggle-variacion/${variacionId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.action === 'added') {
                    showNotification('✅ Variación agregada a favoritos temporales', 'success');
                    actualizarEstadoCorazon(productoId, variacionId, true);
                } else {
                    showNotification('❌ Variación eliminada de favoritos temporales', 'info');
                    actualizarEstadoCorazon(productoId, variacionId, false);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al gestionar favoritos', 'error');
        });
    }

    function actualizarEstadoCorazon(productoId, variacionId, activo) {
        let selector = '';
        if (variacionId) {
            selector = `[data-producto="${productoId}"][data-variacion="${variacionId}"]`;
        } else {
            selector = `[data-producto="${productoId}"][data-variacion=""]`;
        }
        
        const botones = document.querySelectorAll(selector);
        botones.forEach(button => {
            if (activo) {
                button.classList.remove('inactivo');
                button.classList.add('activo');
                button.innerHTML = '❤️';
                button.title = 'Quitar de favoritos';
            } else {
                button.classList.remove('activo');
                button.classList.add('inactivo');
                button.innerHTML = '🤍';
                button.title = 'Agregar a favoritos';
            }
        });
    }

    function showNotification(message, type = 'success') {
        // Reutilizar la función de notificación que ya existe
        if (typeof window.showSingleNotification === 'function') {
            window.showSingleNotification(message, 3000);
        } else if (typeof window.showNotification === 'function') {
            window.showNotification(message, type);
        } else {
            alert(message);
        }
    }
</script>