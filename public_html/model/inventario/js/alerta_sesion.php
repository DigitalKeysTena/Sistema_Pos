<?php if (isset($_SESSION['swal'])): ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    Swal.fire({
        icon: "<?= $_SESSION['swal']['icon'] ?>",
        title: "<?= $_SESSION['swal']['title'] ?>",
        text: "<?= $_SESSION['swal']['text'] ?>",
        timer: <?= $_SESSION['swal']['timer'] ?>,
        showConfirmButton: false
    });
});
</script>
<?php unset($_SESSION['swal']); endif; ?>