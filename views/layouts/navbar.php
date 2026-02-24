<?php
$userRole = $_SESSION['user_rol_nombre'] ?? '';
$userName = $_SESSION['user_nombre'] ?? '';
$baseUrl = BASE_URL;
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php?action=dashboard">
            <i class="bi bi-building"></i> UniEcuador
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo $_GET['action'] == 'dashboard' ? 'active' : ''; ?>" 
                       href="index.php?action=dashboard">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $_GET['action'] == 'regiones' ? 'active' : ''; ?>" 
                       href="index.php?action=regiones">
                        <i class="bi bi-geo-alt"></i> Regiones
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $_GET['action'] == 'universidades' ? 'active' : ''; ?>" 
                       href="index.php?action=universidades">
                        <i class="bi bi-buildings"></i> Universidades
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $_GET['action'] == 'carreras' ? 'active' : ''; ?>" 
                       href="index.php?action=carreras">
                        <i class="bi bi-book"></i> Carreras
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $_GET['action'] == 'modalidades' ? 'active' : ''; ?>" 
                       href="index.php?action=modalidades">
                        <i class="bi bi-grid-3x3-gap-fill"></i> Modalidades
                    </a>
                </li>
                <?php if ($userRole == 'Administrador'): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $_GET['action'] == 'usuarios' ? 'active' : ''; ?>" 
                       href="index.php?action=usuarios">
                        <i class="bi bi-people"></i> Usuarios
                    </a>
                </li>
                <?php endif; ?>
            </ul>
            <div class="d-flex align-items-center">
                <span class="text-white me-3">
                    <i class="bi bi-person-circle"></i> 
                    <?php echo htmlspecialchars($userName); ?> (<?php echo $userRole; ?>)
                </span>
                <a href="index.php?action=logout" class="btn btn-light btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Salir
                </a>
            </div>
        </div>
    </div>
</nav>

<div class="container-fluid mt-5 pt-4">