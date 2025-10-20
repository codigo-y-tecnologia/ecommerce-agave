document.addEventListener("DOMContentLoaded", () => {
    console.log("login.js cargado correctamente");

    // Diccionario global reutilizable
  const nombresLegibles = {
    vEmail: "Correo Electrónico",
    vPassword: "Contraseña",
  };

    const loginForm = document.getElementById("loginForm");
    const passwordInput = document.querySelector("#vPassword");

    // =============================
    // Validaciones del formulario
    // =============================
    if (loginForm) {
        // Evitar espacios al inicio (incluye email)
        loginForm.querySelectorAll("input").forEach((campo) => {
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

// Configuración de campos a limitar
const limitarLongitud = [
  { id: "vEmail", max: 100 }, 
  { id: "vPassword", max: 150 },
];

// Limitar longitud de email y contraseñas
limitarLongitud.forEach(({ id, max }) => {
  const input = loginForm.querySelector(`input#${id}`);
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
        loginForm.addEventListener("submit", (e) => {
            const campos = loginForm.querySelectorAll(
                'input[type="email"], input[type="password"]');

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
        });
    } 
});
