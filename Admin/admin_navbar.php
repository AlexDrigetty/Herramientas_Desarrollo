<?php
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$base_url = $protocol . $_SERVER['HTTP_HOST'] . '/PROYECTO_HERRAMIENTAS/';
?>
<div class="slider">
    <div class="slider-content">
        <h4>FocoGlobal Admin</h4>
    </div>
    <div class="menu">
        <h3>Principal</h3>
        <ul>
            <li class="active"><a href="<?php echo $base_url; ?>Admin/dashboard_admin.php"><i class="fa fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="<?php echo $base_url; ?>Publico/inicio.php"><i class="fa fa-globe"></i> Página Principal</a></li>
        </ul>

        <h3>Contenido</h3>
        <ul>
            <li><a href="<?php echo $base_url; ?>Admin/Todas_Noticias.php"><i class="fa fa-newspaper"></i> Todas las Noticias</a></li>
            <li><a href="<?php echo $base_url; ?>Admin/Crear_Noticias.php"><i class="fa fa-plus-circle"></i> Crear Noticia</a></li>
            <li><a href="<?php echo $base_url; ?>Admin/Programar_Noticias.php"><i class="fa fa-clock"></i> Programadas</a></li>
        </ul>

        <h3>Administración</h3>
        <ul>
            <li><a href="<?php echo $base_url; ?>Admin/Usuarios.php"><i class="fa fa-users"></i> Usuarios</a></li>
            <li><a href="<?php echo $base_url; ?>bd/logout.php"><i class="fa fa-sign-out-alt"></i> Cerrar Sesión</a></li>
        </ul>
    </div>
</div>