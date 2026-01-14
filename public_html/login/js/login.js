(() => {
  "use strict";

  const toggleBtn = document.getElementById("togglePassword");
  const passwordInput = document.getElementById("password");

  if (!toggleBtn || !passwordInput) return;

  const toggleVisibility = () => {
    const isText = passwordInput.type === "text";
    passwordInput.type = isText ? "password" : "text";

    // 🔒 Actualiza accesibilidad
    toggleBtn.setAttribute("aria-pressed", String(!isText));
    toggleBtn.setAttribute("aria-label", isText ? "Mostrar contraseña" : "Ocultar contraseña");

    // ✅ No cambia tamaño del botón ni CLS
    toggleBtn.innerHTML = isText
      ? `<span aria-hidden="true">👁️</span><span class="sr-only"></span>`
      : `<span aria-hidden="true">🙈</span><span class="sr-only"></span>`;
  };

  // Evento seguro
  toggleBtn.addEventListener("click", toggleVisibility);

  // ⚠️ No bloqueamos copy/paste (Lighthouse lo penaliza)
})();