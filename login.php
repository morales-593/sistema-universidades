<?php
// Este archivo es incluido por AuthController
?>
<link rel="stylesheet" href="assets/css/login.css">

<div class="login-container">
    <div class="floating-shapes">
        <div class="shape shape1"></div>
        <div class="shape shape2"></div>
        <div class="shape shape3"></div>
    </div>

    <div class="card-custom">
        <div class="card-header-custom">
            <i class="bi bi-building header-icon"></i>
            <h1 class="header-title">Sistema Universidades</h1>
            <p class="header-subtitle">Gestión de Catálogo - Ecuador</p>
        </div>

        <div class="card-body-custom">
            <form method="POST" action="index.php?action=login">
                <div class="form-group">
                    <label for="email" class="form-label">
                        <i class="bi bi-envelope-fill"></i> Correo electrónico
                    </label>
                    <div class="input-group-custom">
                        <i class="bi bi-envelope input-icon"></i>
                        <input type="email" class="form-control-custom" id="email" name="email"
                            placeholder="tu@email.com" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="bi bi-lock-fill"></i> Contraseña
                    </label>
                    <div class="input-group-custom">
                        <i class="bi bi-lock input-icon"></i>
                        <input type="password" class="form-control-custom" id="password" name="password"
                            placeholder="••••••••" required>
                        <button type="button" class="toggle-password" id="togglePassword">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                </button>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert-custom">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <span>Credenciales incorrectas. Por favor, intenta de nuevo.</span>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        const password = document.getElementById('password');
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        this.querySelector('i').classList.toggle('bi-eye');
        this.querySelector('i').classList.toggle('bi-eye-slash');
    });

    // Animación adicional para los inputs
    document.querySelectorAll('.form-control-custom').forEach(input => {
        input.addEventListener('focus', function () {
            this.parentElement.classList.add('focused');
        });

        input.addEventListener('blur', function () {
            this.parentElement.classList.remove('focused');
        });
    });
</script>