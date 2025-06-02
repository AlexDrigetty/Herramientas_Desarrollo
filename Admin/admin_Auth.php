<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../Publico/login.php");
    exit;
}

if ($_SESSION['rol'] !== '0') {
    header("Location: ../Publico/inicio.php"); 
    exit;
}
?>