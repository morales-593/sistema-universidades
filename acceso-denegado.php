<?php
// Este archivo es incluido por index.php cuando action=acceso-denegado
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Denegado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card shadow-lg border-0 rounded-4 mt-5">
                    <div class="card-body text-center p-5">
                        <div class="text-danger mb-4">
                            <i class="bi bi-shield-lock fs-1"></i>
                        </div>
                        <h2 class="mb-3">Acceso Denegado</h2>
                        <p class="text-muted mb-4">
                            No tienes permisos suficientes para acceder a esta página.
                            Si crees que esto es un error, contacta al administrador del sistema.
                        </p>
                        <div class="d-grid gap-2">
                            <a href="javascript:history.back()" class="btn btn-primary">
                                <i class="bi bi-arrow-left"></i> Volver
                            </a>
                            <a href="<?php echo BASE_URL; ?>/index.php?action=logout" class="btn btn-outline-secondary">
                                <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>