import Swal from 'sweetalert2';

// resources/js/scripts.js
document.addEventListener("DOMContentLoaded", () => {
    console.log("scripts.js cargado correctamente!");

    const registroForm = document.getElementById("registroForm");

    if (registroForm) {
        registroForm.addEventListener("submit", function(e) {
            const fechaNac = new Date(
                document.querySelector('input[name="dFecha_nacimiento"]').value
            );
            const hoy = new Date();
            let edad = hoy.getFullYear() - fechaNac.getFullYear();
            const m = hoy.getMonth() - fechaNac.getMonth();
            if (m < 0 || (m === 0 && hoy.getDate() < fechaNac.getDate())) {
                edad--;
            }

            if (edad < 18) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Edad mínima requerida',
                    text: 'Debes ser mayor de edad para registrarte.',
                    confirmButtonText: 'Entendido'
                });
                  return;
            }

            // Validar checkbox
            const terminos = document.getElementById("terminos");
            if (!terminos.checked) {
                e.preventDefault();
                Swal.fire({
                    icon: 'info',
                    title: 'Acepta los términos',
                    text: 'Debes aceptar los términos y condiciones antes de registrarte.',
                    confirmButtonText: 'Ok, entendido'
                });
                return; // Detener ejecución
            }
        });
    }
});
