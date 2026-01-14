   <!-- FOOTER -->
    <footer class="footer bg-light py-2 border-top">
        <div class="container text-center text-muted  accordion">
            © 2025 ChinguMarket — diseñado por DigitalKeysTena
        </div>
    </footer>
</div>
<script > const sidebar    = document.getElementById("sidebar");
const toggleMenu = document.getElementById("toggleMenu");
const overlay    = document.getElementById("overlay");

const themeBtn   = document.getElementById("themeToggleBtn");
const themeIcon  = document.getElementById("themeIcon");

/* ============================
   MENÚ LATERAL (PC + MÓVIL)
============================ */
toggleMenu.addEventListener("click", () => {

    // MÓVIL
    if (window.innerWidth < 768) {
        sidebar.classList.toggle("show");
        overlay.classList.toggle("active");
        return;
    }

    // PC
    sidebar.classList.toggle("sidebar-collapsed");
    sidebar.classList.toggle("sidebar-expanded");
});

/* Cerrar al tocar overlay (móvil) */
overlay.addEventListener("click", () => {
    sidebar.classList.remove("show");
    overlay.classList.remove("active");
});

/* Cerrar al tocar fuera (móvil) */
document.addEventListener("click", (e) => {
    if (window.innerWidth >= 768) return;

    const clickDentroSidebar = sidebar.contains(e.target);
    const clickEnBoton = toggleMenu.contains(e.target);

    if (!clickDentroSidebar && !clickEnBoton) {
        sidebar.classList.remove("show");
        overlay.classList.remove("active");
    }
});

// ===============================
//   CAMBIO DE MODO (DARK/LIGHT)
// ===============================
const themeToggle = document.getElementById("themeToggle");
const body = document.body;

// Recuperar modo guardado
if (localStorage.getItem("theme") === "dark") {
    body.classList.add("dark-mode");
    themeToggle.innerHTML = `<i class="bi bi-sun"></i>`;
}

// Escuchar clic del botón
themeToggle.addEventListener("click", () => {

    // Alternar clase
    body.classList.toggle("dark-mode");

    // Guardar en localStorage
    if (body.classList.contains("dark-mode")) {
        localStorage.setItem("theme", "dark");
        themeToggle.innerHTML = `<i class="bi bi-sun"></i>`; // icono modo claro
    } else {
        localStorage.setItem("theme", "light");
        themeToggle.innerHTML = `<i class="bi bi-moon"></i>`; // icono modo oscuro
    }
});
</script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>>

</body>
</html>
