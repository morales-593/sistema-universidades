<?php
// Este archivo es incluido por AuthController
?>
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-lg border-0 rounded-4 mt-5">
                <div class="card-header bg-primary text-white text-center py-4 rounded-top-4">
                    <i class="bi bi-building fs-1"></i>
                    <h3 class="mt-2">Sistema Universidades</h3>
                    <p class="mb-0 small">Gestión de Catálogo - Ecuador</p>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="index.php?action=login">
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo electrónico</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
                            <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                        </button>
                        
                        <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle"></i> Credenciales incorrectas
                        </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('togglePassword').addEventListener('click', function() {
    const password = document.getElementById('password');
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);
    this.querySelector('i').classList.toggle('bi-eye');
    this.querySelector('i').classList.toggle('bi-eye-slash');
});
</script>