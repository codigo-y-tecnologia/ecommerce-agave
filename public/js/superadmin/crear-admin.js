import Swal from "sweetalert2";

document.addEventListener("DOMContentLoaded", () => {
    console.log("crear-admin.js cargado correctamente");

    // Diccionario global reutilizable
  const nombresLegibles = {
    vNombre: "Nombre",
    vApaterno: "Apellido Paterno",
    vAmaterno: "Apellido Materno",
    vEmail: "Correo Electrónico",
    dFecha_nacimiento: "Fecha de Nacimiento",
  };

    const createAdminForm = document.getElementById("createAdminForm");

    // =============================
    // Validaciones del formulario
    // =============================
    if (createAdminForm) {
        // Evitar espacios al inicio (incluye email)
        createAdminForm.querySelectorAll("input").forEach((campo) => {
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
  { id: "vEmail", max: 100 }, 
];

soloLetrasCampos.forEach(({ name, max }) => {
  const input = createAdminForm.querySelector(`input[name="${name}"]`);
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

// Limitar longitud de email
limitarLongitud.forEach(({ id, max }) => {
  const input = createAdminForm.querySelector(`input#${id}`);
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
        const fechaInput = createAdminForm.querySelector('input[name="dFecha_nacimiento"]');
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
        createAdminForm.addEventListener("submit", (e) => {
            const campos = createAdminForm.querySelectorAll(
                'input[type="text"], input[type="email"], input[type="date"]'
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
                    text: "El admin debe ser mayor de edad para registrarse.",
                }).then(() => fechaInput.focus());
                setTimeout(() => fechaInput.classList.remove("shake"), 600);
                return;
            }
        });
    }
});
