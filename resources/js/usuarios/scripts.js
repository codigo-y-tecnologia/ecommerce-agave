import Swal from "sweetalert2";

document.addEventListener("DOMContentLoaded", () => {
    console.log("scripts.js cargado correctamente");

    // Diccionario global reutilizable
  const nombresLegibles = {
    vNombre: "Nombre",
    vApaterno: "Apellido Paterno",
    vAmaterno: "Apellido Materno",
    vEmail: "Correo Electrónico",
    vPassword: "Contraseña",
    vPassword_confirmation: "Confirmar Contraseña",
    dFecha_nacimiento: "Fecha de Nacimiento",
    terminos: "Términos y Condiciones",
  };

    const registroForm = document.getElementById("registroForm");
    const passwordInput = document.querySelector("#vPassword");
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
    if (registroForm) {
        // Evitar espacios al inicio (incluye email)
        registroForm.querySelectorAll("input").forEach((campo) => {
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

const soloLetrasCampos = [
  { name: "vNombre", max: 60 },
  { name: "vApaterno", max: 50 },
  { name: "vAmaterno", max: 50 },
];

const limitarLongitud = [
  { id: "vEmail", max: 80 }, 
  { id: "vPassword", max: 80 },
  { id: "vPassword_confirmation", max: 80 },
];

soloLetrasCampos.forEach(({ name, max }) => {
  const input = registroForm.querySelector(`input[name="${name}"]`);
  if (!input) return;

  let composing = false; // bandera para saber si el usuario está escribiendo con acento

  // Detectar inicio y fin de composición (para no interferir con teclas muertas)
  input.addEventListener("compositionstart", () => (composing = true));
  input.addEventListener("compositionend", () => {
    composing = false;
    validarCampo(); // validamos cuando termina la combinación (p.ej. ´ + a)
  });

  input.addEventListener("input", (e) => {
    if (composing) return; // no validar mientras se compone el acento
    validarCampo();
  });

  function validarCampo() {
    const caretPos = input.selectionStart;
    const regex = /^[a-zA-ZÀ-ÖØ-öø-ÿ\u00f1\u00d1\s]*$/u;

    if (!regex.test(input.value)) {
      // Limpia caracteres inválidos (mantiene tildes válidas)
      input.value = input.value
        .normalize("NFD")
        .replace(/[^a-zA-Z\u0300-\u036f\s]/g, "");
    }

    // Quitar espacios iniciales
    if (input.value.startsWith(" ")) {
      input.value = input.value.trimStart();
    }

    // Limitar longitud
    if (input.value.length > max) {
      input.value = input.value.substring(0, max);
      Swal.fire({
        icon: "info",
        title: "Límite alcanzado",
        text: `El campo "${nombresLegibles[name] || name}" solo permite ${max} caracteres.`,
        confirmButtonText: "Entendido",
      });
    }

    input.setSelectionRange(caretPos, caretPos);

    // Si estaba marcado en rojo, quitarlo al escribir bien
    if (input.classList.contains("campo-invalido") && input.value.trim() !== "") {
      input.classList.remove("campo-invalido");
    }
  }

  // Validar texto pegado
  input.addEventListener("paste", (e) => {
    const pasteData = e.clipboardData.getData("text");
    const regex = /^[a-zA-ZÀ-ÖØ-öø-ÿ\u00f1\u00d1\s]+$/u;
    if (!regex.test(pasteData)) {
      e.preventDefault();
      Swal.fire({
        icon: "warning",
        title: "Entrada no válida",
         text: `Solo se permiten letras y acentos en el campo "${nombresLegibles[name] || name}".`,
        confirmButtonText: "Entendido",
      });
    }
  });
});

// Limitar longitud de email y contraseñas
limitarLongitud.forEach(({ id, max }) => {
  const input = registroForm.querySelector(`input#${id}`);
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

        // Restricción para campo de fecha (no permitir fechas futuras)
        const fechaInput = registroForm.querySelector('input[name="dFecha_nacimiento"]');
        if (fechaInput) {
            fechaInput.addEventListener("input", () => {
                const hoy = new Date().toISOString().split("T")[0];
                if (fechaInput.value > hoy) {
                    fechaInput.value = "";
                    fechaInput.classList.add("shake");
                    Swal.fire({
                        icon: "warning",
                        title: "Fecha inválida",
                        text: "La fecha de nacimiento no puede ser futura.",
                        confirmButtonText: "Entendido",
                    });
                    setTimeout(() => fechaInput.classList.remove("shake"), 600);
                } else if (fechaInput.classList.contains("campo-invalido") && fechaInput.value.trim() !== "") {
                    fechaInput.classList.remove("campo-invalido");
                }
            });
        }   

        // =============================
        // Validar al enviar
        // =============================
        registroForm.addEventListener("submit", (e) => {
            const campos = registroForm.querySelectorAll(
                'input[type="text"], input[type="email"], input[type="password"], input[type="date"]'
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
            const confirmInput = document.getElementById("vPassword_confirmation");
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

            // Edad mínima
            const fechaInput = document.getElementById("dFecha_nacimiento");
            const fechaNac = new Date(fechaInput.value);
            const hoy = new Date();
            let edad = hoy.getFullYear() - fechaNac.getFullYear();
            const m = hoy.getMonth() - fechaNac.getMonth();
            if (m < 0 || (m === 0 && hoy.getDate() < fechaNac.getDate())) edad--;

            if (edad < 18) {
                e.preventDefault();
                fechaInput.classList.add("campo-invalido", "shake");
                Swal.fire({
                    icon: "error",
                    title: "Edad no permitida",
                    text: "Debes ser mayor de edad para registrarte.",
                }).then(() => fechaInput.focus());
                setTimeout(() => fechaInput.classList.remove("shake"), 600);
                return;
            }

            // Checkbox
            const terminos = document.getElementById("terminos");
            if (!terminos.checked) {
                e.preventDefault();
                terminos.classList.add("shake");
                Swal.fire({
                    icon: "info",
                    title: "Acepta los términos",
                    text: "Debes aceptar los términos y condiciones antes de continuar.",
                }).then(() => terminos.focus());
                setTimeout(() => terminos.classList.remove("shake"), 600);
                return;
            }
        });
    }
});
