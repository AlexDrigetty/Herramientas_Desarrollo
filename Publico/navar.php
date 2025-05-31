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
                    
                    <?php if (isset($_SESSION['usuario_id'])): ?>
                    <!-- Mostrar cuando el usuario ESTÁ logueado -->
                        <li><a href="perfil.php" class="profile-link">
                            <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['nombre']); ?>
                        </a></li>
                        <li><a href="logout.php" class="logout-btn">
                            <i class="fas fa-sign-out-alt"></i> Salir
                        </a></li>
                    <?php else: ?>
                        <!-- Mostrar cuando el usuario NO está logueado -->
                        <li><a href="login.php" class="login-btn">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a></li>
                    <?php endif; ?>
                </ul>
                <button class="fas fa-bars"></button>
            </div>
        </div>
    </nav>