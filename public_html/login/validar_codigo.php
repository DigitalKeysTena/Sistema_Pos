<?php
session_start();
require_once __DIR__ . '/../../src/security/csrf.php';

$message = $_SESSION['otp_msg'] ?? null;
unset($_SESSION['otp_msg']);

$email = $_SESSION['reset_email'] ?? null;
if (!$email) {
    redirect("recuperar.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Validar Código</title>

<style>
/* 🎨 CSS — el mismo que ya usas, intacto */
body {
    margin: 0;
    padding: 0;
    font-family: Poppins, sans-serif;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: linear-gradient(120deg,#b4c09b,#f4b6b8,#f4a261,#5e7c50);
    background-size:200% 200%;
    animation:bgMove 16s ease infinite;
}
@keyframes bgMove{0%,100%{background-position:0 50%}50%{background-position:100% 50%}}

.verify-card {
    max-width: 360px;
    width: 100%;
    padding: 25px;
    border-radius: 18px;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(12px);
    text-align: center;
    color: white;
}

.otp-wrapper { display:flex; justify-content:center; gap:10px; margin-bottom:25px; }

.otp-input {
  width:48px;height:55px;border-radius:10px;
  border:2px solid rgba(255,255,255,.25);
  background:rgba(255,255,255,.20);text-align:center;
  font-size:1.4rem;font-weight:bold;color:#fff;
  transition:.3s ease;
}

.otp-input:focus {
  border-color:#f4b6b8;transform:scale(1.08);
  background:rgba(255,255,255,.32);
}

.btn{
  width:100%;padding:13px;border:none;border-radius:12px;
  background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.6);
  color:white;font-weight:bold;cursor:pointer;transition:.3s ease;
}
.btn:hover{ transform:translateY(-2px); box-shadow:0 4px 14px rgba(255,255,255,.25); }

.alert{
  background:#f8d7da;color:#842029;border:1px solid #f5c2c7;
  border-radius:8px;padding:12px;margin-bottom:12px;
}
</style>
</head>

<body>

<div class="verify-card">
    <h2>Verificación</h2>
    <p>Ingresa el código enviado a tu correo</p>

    <?php if ($message): ?>
      <div class="alert"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <form id="otpForm" method="POST" action="/api/login/validar_codigo.php"
>

        <!-- ⭐ CSRF TOKEN -->
        <?= csrf_field() ?>

        <input type="hidden" name="otp" id="otp_final">
        <input type="hidden" name="email" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>">

        <div class="otp-wrapper">
            <input maxlength="1" class="otp-input" autocomplete="off">
            <input maxlength="1" class="otp-input" autocomplete="off">
            <input maxlength="1" class="otp-input" autocomplete="off">
            <input maxlength="1" class="otp-input" autocomplete="off">
            <input maxlength="1" class="otp-input" autocomplete="off">
            <input maxlength="1" class="otp-input" autocomplete="off">
        </div>

        <button class="btn" type="submit">Validar Código</button>

    </form>
</div>

<script>
const inputs = document.querySelectorAll(".otp-input");
const otp_final = document.getElementById("otp_final");

function updateOtp() {
    otp_final.value = [...inputs].map(i => i.value).join("");
}

inputs.forEach((input, index) => {

    input.addEventListener("input", () => {
        input.value = input.value.replace(/[^0-9]/g,"");

        if (input.value && index < inputs.length - 1) {
            inputs[index + 1].focus();
        }
        updateOtp();
    });

    input.addEventListener("keydown", e => {
        if (e.key === "Backspace" && !input.value && index > 0) {
            inputs[index - 1].focus();
        }
    });
});
</script>

</body>
</html>