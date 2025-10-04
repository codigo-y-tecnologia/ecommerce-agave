import Swal from "sweetalert2";

document.addEventListener("DOMContentLoaded", () => {
    console.log("✅ scripts.js cargado correctamente");

    const registroForm = document.getElementById("registroForm");

    if (registroForm) {
        registroForm.addEventListener("submit", function (e) {
            const campos = registroForm.querySelectorAll(
                'input[type="text"], input[type="email"], input[type="password"], input[type="date"]'
            );

            let camposVacios = [];

            // Limpiar estilos previos
            campos.forEach((campo) => {
                campo.classList.remove("campo-invalido", "shake");
            });

            // Detectar campos vacíos
            campos.forEach((campo) => {
                if (campo.value.trim() === "") {
                    camposVacios.push(campo);
                    campo.classList.add("campo-invalido", "shake");
                }
            });

            // Mostrar alerta si hay campos vacíos
            if (camposVacios.length > 0) {
                e.preventDefault();

                const nombresCampos = {
                    vNombre: "Nombre",
                    vApaterno: "Apellido Paterno",
                    vAmaterno: "Apellido Materno",
                    vEmail: "Correo Electrónico",
                    vPassword: "Contraseña",
                    vPassword_confirmation: "Confirmar Contraseña",
                    dFecha_nacimiento: "Fecha de Nacimiento",
                };

                const camposFaltantes = camposVacios
                    .map((c) => nombresCampos[c.name] || c.name)
                    .join(", ");

                Swal.fire({
                    icon: "warning",
                    title: "Campos incompletos",
                    html: `Por favor completa los siguientes campos:<br><b>${camposFaltantes}</b>`,
                    confirmButtonText: "Entendido",
                }).then(() => {
                    camposVacios[0].focus();
                });

                // Quitar animación luego de 500ms para que se pueda reutilizar si vuelve a fallar
                setTimeout(() => {
                    camposVacios.forEach((campo) =>
                        campo.classList.remove("shake")
                    );
                }, 600);

                return;
            }

            // Validar edad mínima
            const fechaInput = document.querySelector('input[name="dFecha_nacimiento"]');
            const fechaNac = new Date(fechaInput.value);
            const hoy = new Date();
            let edad = hoy.getFullYear() - fechaNac.getFullYear();
            const m = hoy.getMonth() - fechaNac.getMonth();
            if (m < 0 || (m === 0 && hoy.getDate() < fechaNac.getDate())) {
                edad--;
            }

            if (edad < 18) {
                e.preventDefault();
                fechaInput.classList.add("campo-invalido", "shake");
                Swal.fire({
                    icon: "error",
                    title: "Edad no permitida",
                    text: "Debes ser mayor de edad para registrarte.",
                    confirmButtonText: "Entendido",
                }).then(() => {
                    fechaInput.focus();
                });

                setTimeout(() => {
                    fechaInput.classList.remove("shake");
                }, 600);

                return;
            }

            // Validar checkbox de términos
            const terminos = document.getElementById("terminos");
            if (!terminos.checked) {
                e.preventDefault();
                terminos.classList.add("shake");
                Swal.fire({
                    icon: "info",
                    title: "Acepta los términos",
                    text: "Debes aceptar los términos y condiciones antes de continuar.",
                    confirmButtonText: "Ok, entendido",
                }).then(() => {
                    terminos.focus();
                });

                setTimeout(() => {
                    terminos.classList.remove("shake");
                }, 600);

                return;
            }
        });
    }
});
