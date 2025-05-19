<?php
session_start(); 
$isInPages = strpos($_SERVER['PHP_SELF'], '/pages/') !== false;
$basePath = $isInPages ? '../' : '';
?>

<nav class="navbar">
    <div class="navbar-brand">TRIPLANING</div>

    <button id="menu-toggle" class="menu-toggle md:hidden focus:outline-none">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>

    <div id="navbar-links" class="navbar-links">
        <a href="<?php echo $basePath; ?>index.php" class="nav-link">Inicio</a>
        <?php if(isset($_SESSION['user_id'])): ?>
            <!-- Mostrar cuando el usuario está logueado -->
            <a href="<?php echo $basePath; ?>pages/perfil.php" class="nav-link">Perfil</a>
            <a href="<?php echo $basePath; ?>pages/logout.php" class="nav-link">Cerrar Sesión</a>
        <?php else: ?>
            <!-- Mostrar cuando no hay sesión -->
            <a href="<?php echo $basePath; ?>pages/registro.php" class="nav-link">Registro</a>
            <a href="<?php echo $basePath; ?>pages/login.php" class="nav-link">Login</a>
        <?php endif; ?>
        <a href="<?php echo $basePath; ?>pages/mis_viajes.php" class="nav-link">Mis Viajes</a>
        <a href="<?php echo $basePath; ?>pages/popular.php" class="nav-link">Popular</a>
        <a href="<?php echo $basePath; ?>pages/contacto.php" class="nav-link">Contacto</a>
    </div>
</nav>

<script src="<?php echo $basePath; ?>assets/js/navbar.js"></script>