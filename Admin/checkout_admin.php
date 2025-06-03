<?php
// Verifica si la sesión no está activa antes de iniciarla
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$admin_true = (isset($_SESSION['rol']) && $_SESSION['rol'] === 'ADMIN');

if($admin_true) {
    echo '<script>console.log("Sesión admin activa")</script>';
} else {
    echo '<script>console.log("No hay sesión admin")</script>';
}
?>