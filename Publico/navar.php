<?php
session_start();
$user_logged = isset($_SESSION['usuario_id']) && isset($_SESSION['rol']) && $_SESSION['rol'] === 'USUARIO';
?>
<nav>
    <div class="container">
        <div class="nav">
            <a href="inicio.php" class="logo">foco<span>Global</span></a>
            <ul class="nav-links">
                <li><a href="../Publico/inicio.php">Inicio</a></li>
                <li><a href="../Publico/Nacional.php">Nacionales</a></li>
                <li><a href="../Publico/Internacional.php">Internacional</a></li>
                <li><a href="../Publico/destacados.php">Destacados</a></li>
                <li><a href="../Publico/Categoria.php">Categoria</a></li>
            </ul>
            
            <?php if($user_logged): ?>
                <div class="login">
                    <a href="../Publico/inicio.php" class="nombre">
                        <i class="fas fa-user"></i>
                        <?php echo htmlspecialchars($_SESSION['nombre']); ?>
                    </a>
                    <ul class="barra_menu">
                        <li ><a href="../Usuario/perfil_usuario.php"><i class="fas fa-user-circle"></i> Ver perfil</a></li>
                        <li ><a href="../bd/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a></li>
                    </ul>
                </div>
            <?php else: ?>
                <a class="login" href="../Publico/login.php">Login</a>
            <?php endif; ?>
            
            <button class="fas fa-bars"></button>
        </div>
    </div>
</nav>
