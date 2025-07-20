<?php
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$base_url = $protocol . $_SERVER['HTTP_HOST'] . '/';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="slider">
    <div class="slider-content">
        <h4>FocoGlobal Admin</h4>
    </div>
    <div class="menu">
        <h3>Principal</h3>
        <ul>
            <li class="<?php echo ($current_page == 'dashboard_admin.php') ? 'active' : ''; ?>">
                <a href="<?php echo $base_url; ?>Admin/dashboard_admin.php" class="nav-link"><i class="fa fa-tachometer-alt"></i> Dashboard</a>
            </li>
            <li class="<?php echo ($current_page == 'inicio.php') ? 'active' : ''; ?>">
                <a href="<?php echo $base_url; ?>Publico/inicio.php" class="nav-link"><i class="fa fa-globe"></i> Página Principal</a>
            </li>
        </ul>

        <h3>Contenido</h3>
        <ul>
            <li class="<?php echo ($current_page == 'Todas_Noticias.php') ? 'active' : ''; ?>">
                <a href="<?php echo $base_url; ?>Admin/Todas_Noticias.php" class="nav-link"><i class="fa fa-newspaper"></i> Todas las Noticias</a>
            </li>
            <li class="<?php echo ($current_page == 'Crear_Noticias.php') ? 'active' : ''; ?>">
                <a href="<?php echo $base_url; ?>Admin/Crear_Noticias.php" class="nav-link"><i class="fa fa-plus-circle"></i> Crear Noticia</a>
            </li>
            <li class="<?php echo ($current_page == 'Programar_Noticias.php') ? 'active' : ''; ?>">
                <a href="<?php echo $base_url; ?>Admin/Programar_Noticias.php" class="nav-link"><i class="fa fa-clock"></i> Programadas</a>
            </li>
            <li class="<?php echo ($current_page == 'Revision_Noticias.php') ? 'active' : ''; ?>">
                <a href="<?php echo $base_url; ?>Admin/Revision_Noticias.php" class="nav-link"><i class="fa fa-clipboard-check"></i> Revisar</a>
            </li>
        </ul>

        <h3>Administración</h3>
        <ul>
            <li class="<?php echo ($current_page == 'Usuarios.php') ? 'active' : ''; ?>">
                <a href="<?php echo $base_url; ?>Admin/Usuarios.php" class="nav-link"><i class="fa fa-users"></i> Usuarios</a>
            </li>
            <li>
                <a href="<?php echo $base_url; ?>bd/logout.php"><i class="fa fa-sign-out-alt"></i> Cerrar Sesión</a>
            </li>
        </ul>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Solo para resaltar el menú activo - navegación tradicional
document.addEventListener('DOMContentLoaded', function() {
    // Obtener la ruta actual
    var currentPath = window.location.pathname.split('/').pop();
    
    // Remover active de todos los items
    var menuItems = document.querySelectorAll('.menu li');
    menuItems.forEach(function(item) {
        item.classList.remove('active');
    });
    
    // Agregar active al item correspondiente
    var activeLink = document.querySelector('.menu li a[href*="' + currentPath + '"]');
    if (activeLink) {
        activeLink.parentElement.classList.add('active');
    }
});
</script>