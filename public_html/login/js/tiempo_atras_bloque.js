
document.addEventListener("DOMContentLoaded", () => {
    const countdown = document.getElementById("countdown");
    const loginBtn = document.getElementById("loginBtn");
    const inputs = document.querySelectorAll("input");

    // ✅ Si no hay contador, no hacemos nada
    if (!countdown) return;

    // ✅ Bloquear inputs y botón mientras el tiempo corre
    if (loginBtn) loginBtn.disabled = true;
    inputs.forEach(i => i.disabled = true);

    let remaining = parseInt(countdown.dataset.seconds, 10);
    const display = document.getElementById("timer-display");

    const updateTimer = () => {
        if (remaining <= 0) {
            display.textContent = "Puedes volver a intentarlo.";
            countdown.classList.add("done");

            // ✅ Reactivar inputs y botón
            if (loginBtn) loginBtn.disabled = false;
            inputs.forEach(i => i.disabled = false);

            return;
        }

        const mins = Math.floor(remaining / 60);
        const secs = remaining % 60;

        display.textContent = `${mins}m ${secs < 10 ? "0" + secs : secs}s`;

        remaining--;
        setTimeout(updateTimer, 1000);
    };

    updateTimer();
});

