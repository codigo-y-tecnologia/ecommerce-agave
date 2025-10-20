document.addEventListener("DOMContentLoaded", () => {
    console.log("reset-password.js cargado correctamente");

    // Diccionario global reutilizable
  const nombresLegibles = {
    vEmail: "Correo Electrónico",
    password: "Contraseña",
    password_confirmation: "Confirmar Contraseña",
  };

    const resetPasswordForm = document.getElementById("resetPasswordForm");
    const passwordInput = document.querySelector("#password");
    const passwordStrengthText = document.getElementById("passwordStrengthText");
    const passwordStrengthBar = document.querySelector("#passwordStrengthBar .progress-bar");

    // =============================
    // Indicador de fortaleza de contraseña
    // =============================
    if (passwordInput) {
        passwordInput.addEventListener("input", () => {
            const val = passwordInput.value;
            let strength = 0;

            if (val.length >= 8) strength++;
            if (/[A-Z]/.test(val)) strength++;
            if (/[0-9]/.test(val)) strength++;
            if (/[^A-Za-z0-9]/.test(val)) strength++; // símbolos

            let color = "";
            let text = "";

            switch (strength) {
                case 0:
                case 1:
                    color = "bg-danger";
                    text = "Débil";
                    break;
                case 2:
                    color = "bg-warning";
                    text = "Media";
                    break;
                case 3:
                    color = "bg-info";
                    text = "Buena";
                    break;
                case 4:
                    color = "bg-success";
                    text = "Fuerte";
                    break;
            }

            passwordStrengthText.textContent = `Seguridad: ${text}`;
            passwordStrengthText.style.color =
                color === "bg-danger"
                    ? "#dc3545"
                    : color === "bg-warning"
                    ? "#ffc107"
                    : color === "bg-info"
                    ? "#0dcaf0"
                    : "#198754";

            passwordStrengthBar.className = `progress-bar ${color}`;
            passwordStrengthBar.style.width = `${(strength / 4) * 100}%`;
        });
    }

    // =============================
    // Validaciones del formulario
    // =============================
    if (resetPasswordForm) {
        // Evitar espacios al inicio (incluye email)
        resetPasswordForm.querySelectorAll("input").forEach((campo) => {
            campo.addEventListener("input", () => {
                if (campo.value.length === 1 && campo.value.startsWith(" ")) {
                    campo.value = "";
                    campo.classList.add("shake");
                    setTimeout(() => campo.classList.remove("shake"), 600);
                    return;
                }

                 // Quitar borde rojo si el usuario empieza a escribir bien
            if (campo.classList.contains("campo-invalido") && campo.value.trim() !== "") {
                campo.classList.remove("campo-invalido");
            }
            });
        });

        // =============================
// Restricciones para campos de texto (solo letras, espacios y acentos válidos)
// con soporte completo para teclas muertas (´ + vocal)
// =============================

const limitarLongitud = [
  { id: "vEmail", max: 80 }, 
  { id: "password", max: 80 },
  { id: "password_confirmation", max: 80 },
];

// Limitar longitud de email y contraseñas
limitarLongitud.forEach(({ id, max }) => {
  const input = resetPasswordForm.querySelector(`input#${id}`);
  if (!input) return;

  input.addEventListener("input", () => {

    if (input.value.length > max) {
      input.value = input.value.substring(0, max);
      Swal.fire({
        icon: "info",
        title: "Límite alcanzado",
        text: `El campo "${nombresLegibles[id] || id}" solo permite ${max} caracteres.`,
        confirmButtonText: "Entendido",
      });
    }

    // Si estaba marcado en rojo, quitarlo al escribir bien
    if (input.classList.contains("campo-invalido") && input.value.trim() !== "") {
      input.classList.remove("campo-invalido");
    }
  });
}); 

        // =============================
        // Validar al enviar
        // =============================
        resetPasswordForm.addEventListener("submit", (e) => {
            const campos = resetPasswordForm.querySelectorAll(
                'input[type="email"], input[type="password"]'
            );
            let camposVacios = [];

            campos.forEach((campo) => campo.classList.remove("campo-invalido", "shake"));

            campos.forEach((campo) => {
                if (campo.value.trim() === "") {
                    camposVacios.push(campo);
                    campo.classList.add("campo-invalido", "shake");
                }
            });

            if (camposVacios.length > 0) {
                e.preventDefault();

                const camposFaltantes = camposVacios
                    .map((c) => nombresLegibles[c.name] || c.name)
                    .join(", ");

                Swal.fire({
                    icon: "warning",
                    title: "Campos incompletos",
                    html: `Por favor completa los siguientes campos:<br><b>${camposFaltantes}</b>`,
                    confirmButtonText: "Entendido",
                }).then(() => camposVacios[0].focus());

                setTimeout(() => camposVacios.forEach((c) => c.classList.remove("shake")), 600);
                return;
            }

            // Validar email
            const emailInput = document.getElementById("vEmail");
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailInput.value)) {
                e.preventDefault();
                emailInput.classList.add("campo-invalido", "shake");
                Swal.fire({
                    icon: "error",
                    title: "Correo inválido",
                    text: "Por favor ingresa un correo electrónico válido.",
                }).then(() => emailInput.focus());
                setTimeout(() => emailInput.classList.remove("shake"), 600);
                return;
            }

            // Contraseña mínima
            if (passwordInput.value.length < 8) {
                e.preventDefault();
                passwordInput.classList.add("campo-invalido", "shake");
                Swal.fire({
                    icon: "error",
                    title: "Contraseña débil",
                    text: "La contraseña debe tener al menos 8 caracteres.",
                }).then(() => passwordInput.focus());
                setTimeout(() => passwordInput.classList.remove("shake"), 600);
                return;
            }

            // Confirmar contraseña
            const confirmInput = document.getElementById("password_confirmation");
            if (passwordInput.value !== confirmInput.value) {
                e.preventDefault();
                passwordInput.classList.add("campo-invalido", "shake");
                confirmInput.classList.add("campo-invalido", "shake");
                Swal.fire({
                    icon: "error",
                    title: "Contraseñas no coinciden",
                    text: "Verifica que ambas contraseñas sean iguales.",
                }).then(() => confirmInput.focus());
                setTimeout(() => {
                    passwordInput.classList.remove("shake");
                    confirmInput.classList.remove("shake");
                }, 600);
                return;
            }
        });
    }
});
