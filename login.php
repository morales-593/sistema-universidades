<?php
// Versión optimizada manteniendo toda la funcionalidad
?>
<link rel="stylesheet" href="assets/css/login.css">

<div class="login-wrapper">
    <!-- Fondo con imagen única -->
    <div class="floating-shapes" style="background-image: url('assets/img/login.png');"></div>

    <div class="login-card">
        <!-- Lado izquierdo -->
        <div class="login-card-left" style="background-image: url('assets/img/login.png');">
            <div class="left-content">
                <i class="bi bi-building left-icon" aria-hidden="true"></i>
                <h2 class="left-title">Universidades<br><span>de Ecuador</span></h2>
                <p class="left-description">Gestión de Catálogo Académico</p>
                <div class="left-features">
                    <?php 
                    $features = [
                        ['icon' => 'bi-check-circle-fill', 'text' => 'Administración eficiente'],
                        ['icon' => 'bi-check-circle-fill', 'text' => 'Datos actualizados'],
                        ['icon' => 'bi-check-circle-fill', 'text' => 'Plataforma segura']
                    ];
                    foreach ($features as $feature): 
                    ?>
                    <div class="feature">
                        <i class="bi <?= $feature['icon'] ?>" aria-hidden="true"></i>
                        <span><?= $feature['text'] ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Lado derecho -->
        <div class="login-card-right">
            <div class="card-header-custom">
                <i class="bi bi-box-arrow-in-right header-icon" aria-hidden="true"></i>
                <h1 class="header-title">Acceso al Sistema</h1>
                <p class="header-subtitle">Ingresa tus credenciales</p>
            </div>

            <div class="card-body-custom">
                <form method="POST" action="index.php?action=login" novalidate>
                    <div class="form-group">
                        <label for="email" class="form-label">
                            <i class="bi bi-envelope-fill" aria-hidden="true"></i> Correo
                        </label>
                        <div class="input-group-custom">
                            <i class="bi bi-envelope input-icon" aria-hidden="true"></i>
                            <input type="email" class="form-control-custom" id="email" name="email"
                                placeholder="usuario@universidad.edu.ec" required
                                pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                                title="Ingrese un correo válido">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">
                            <i class="bi bi-lock-fill" aria-hidden="true"></i> Contraseña
                        </label>
                        <div class="input-group-custom">
                            <i class="bi bi-lock input-icon" aria-hidden="true"></i>
                            <input type="password" class="form-control-custom" id="password" name="password"
                                placeholder="••••••••" required minlength="6">
                            <button type="button" class="toggle-password" id="togglePassword" aria-label="Mostrar/ocultar contraseña">
                                <i class="bi bi-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-login">
                        <i class="bi bi-box-arrow-in-right" aria-hidden="true"></i>
                        Ingresar
                    </button>

                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert-custom" role="alert">
                            <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
                            <span>Error de autenticación</span>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');
    
    if (togglePassword && password) {
        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            const icon = this.querySelector('i');
            icon.classList.toggle('bi-eye');
            icon.classList.toggle('bi-eye-slash');
        });
    }

    // Validación básica del formulario
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const email = document.getElementById('email');
            const pass = document.getElementById('password');
            
            if (!email.value || !pass.value) {
                e.preventDefault();
                alert('Por favor complete todos los campos');
            }
        });
    }
})(); 
</script>